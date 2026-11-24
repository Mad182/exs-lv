#!/usr/bin/env python3
"""
EXS.LV UT99 WebSocket-to-UDP Proxy Bridge (zero-dependency Python 3 asyncio).

Bridges Emscripten SOCKFS WebSocket clients (RFC 6455 binary frames,
subprotocol "binary") to the local Unreal Tournament 99 Dedicated Server
UDP port 7777.
"""

import asyncio
import base64
import hashlib
import os
import signal
import socket
import struct
import sys

WS_HOST = os.environ.get("WS_HOST", "127.0.0.1")
WS_PORT = int(os.environ.get("WS_PORT", "8777"))
UT_HOST = os.environ.get("UT_HOST", "127.0.0.1")
UT_PORT = int(os.environ.get("UT_PORT", "7777"))

WS_MAGIC = "258EAFA5-E914-47DA-95CA-C5AB0DC85B11"


def encode_ws_frame(payload: bytes, opcode: int = 0x2) -> bytes:
    header = bytearray()
    header.append(0x80 | (opcode & 0x0F))
    length = len(payload)
    if length < 126:
        header.append(length)
    elif length < 65536:
        header.append(126)
        header.extend(struct.pack("!H", length))
    else:
        header.append(127)
        header.extend(struct.pack("!Q", length))
    return bytes(header) + payload


async def read_ws_frame(reader: asyncio.StreamReader):
    head = await reader.readexactly(2)
    b1, b2 = head[0], head[1]
    fin = (b1 & 0x80) != 0
    opcode = b1 & 0x0F
    masked = (b2 & 0x80) != 0
    length = b2 & 0x7F

    if length == 126:
        length = struct.unpack("!H", await reader.readexactly(2))[0]
    elif length == 127:
        length = struct.unpack("!Q", await reader.readexactly(8))[0]

    if length > 1024 * 1024:
        raise ValueError("WebSocket frame too large")

    mask_key = await reader.readexactly(4) if masked else None
    payload = await reader.readexactly(length) if length > 0 else b""

    if masked and length > 0:
        payload = bytes(b ^ mask_key[i & 3] for i, b in enumerate(payload))

    return fin, opcode, payload


class UDPClientProtocol(asyncio.DatagramProtocol):
    def __init__(self, writer: asyncio.StreamWriter, peer_label: str):
        self.writer = writer
        self.peer_label = peer_label
        self.transport = None

    def connection_made(self, transport):
        self.transport = transport

    def datagram_received(self, data: bytes, addr):
        if not self.writer.is_closing():
            try:
                self.writer.write(encode_ws_frame(data, opcode=0x2))
            except Exception:
                pass

    def error_received(self, exc):
        print(f"[UT99-WSProxy] UDP error ({self.peer_label}): {exc}", flush=True)


async def handle_client(reader: asyncio.StreamReader, writer: asyncio.StreamWriter):
    sock = writer.get_extra_info("socket")
    if sock is not None:
        try:
            sock.setsockopt(socket.IPPROTO_TCP, socket.TCP_NODELAY, 1)
        except OSError:
            pass
    peer = writer.get_extra_info("peername")
    peer_label = f"{peer[0]}:{peer[1]}" if peer else "unknown"
    udp_transport = None

    try:
        raw_req = await asyncio.wait_for(reader.readuntil(b"\r\n\r\n"), timeout=10.0)
        lines = raw_req.decode("latin1").split("\r\n")
        headers = {}
        for line in lines[1:]:
            if ":" in line:
                k, v = line.split(":", 1)
                headers[k.strip().lower()] = v.strip()

        ws_key = headers.get("sec-websocket-key")
        if not ws_key:
            writer.write(b"HTTP/1.1 400 Bad Request\r\nContent-Length: 0\r\n\r\n")
            await writer.drain()
            return

        accept_key = base64.b64encode(
            hashlib.sha1((ws_key + WS_MAGIC).encode("ascii")).digest()
        ).decode("ascii")

        resp_lines = [
            "HTTP/1.1 101 Switching Protocols",
            "Upgrade: websocket",
            "Connection: Upgrade",
            f"Sec-WebSocket-Accept: {accept_key}",
        ]
        req_proto = headers.get("sec-websocket-protocol", "")
        if req_proto:
            protos = [p.strip() for p in req_proto.split(",") if p.strip()]
            chosen = "binary" if "binary" in protos else protos[0]
            resp_lines.append(f"Sec-WebSocket-Protocol: {chosen}")

        writer.write(("\r\n".join(resp_lines) + "\r\n\r\n").encode("ascii"))
        await writer.drain()

        loop = asyncio.get_running_loop()
        udp_transport, _ = await loop.create_datagram_endpoint(
            lambda: UDPClientProtocol(writer, peer_label),
            remote_addr=(UT_HOST, UT_PORT),
        )
        print(f"[UT99-WSProxy] Connected {peer_label} -> {UT_HOST}:{UT_PORT}", flush=True)

        frag_buf = bytearray()
        while True:
            fin, opcode, payload = await read_ws_frame(reader)
            if opcode == 0x8:  # Close
                writer.write(encode_ws_frame(b"", opcode=0x8))
                await writer.drain()
                break
            elif opcode == 0x9:  # Ping
                writer.write(encode_ws_frame(payload, opcode=0xA))
                await writer.drain()
                continue
            elif opcode == 0xA:  # Pong
                continue
            elif opcode in (0x1, 0x2, 0x0):
                if opcode != 0x0:
                    frag_buf.clear()
                frag_buf.extend(payload)
                if fin:
                    pkt = bytes(frag_buf)
                    frag_buf.clear()
                    # Drop Emscripten SOCKFS 10-byte control packet (\xff\xff\xff\xffport<u16>)
                    if len(pkt) == 10 and pkt[:8] == b"\xff\xff\xff\xffport":
                        continue
                    udp_transport.sendto(pkt)

    except (asyncio.IncompleteReadError, ConnectionResetError, BrokenPipeError, asyncio.TimeoutError):
        pass
    except Exception as exc:
        print(f"[UT99-WSProxy] Session error ({peer_label}): {exc}", flush=True)
    finally:
        if udp_transport is not None:
            udp_transport.close()
        if not writer.is_closing():
            writer.close()
            try:
                await writer.wait_closed()
            except Exception:
                pass
        print(f"[UT99-WSProxy] Disconnected {peer_label}", flush=True)


async def main():
    server = await asyncio.start_server(handle_client, WS_HOST, WS_PORT)
    print(
        f"[UT99-WSProxy] Listening on ws://{WS_HOST}:{WS_PORT} -> udp://{UT_HOST}:{UT_PORT}",
        flush=True,
    )
    stop_event = asyncio.Event()
    loop = asyncio.get_running_loop()
    for sig in (signal.SIGINT, signal.SIGTERM):
        loop.add_signal_handler(sig, stop_event.set)

    async with server:
        await stop_event.wait()


if __name__ == "__main__":
    asyncio.run(main())

