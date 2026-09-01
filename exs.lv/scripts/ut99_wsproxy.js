/**
 * EXS.LV UT99 WebSocket-to-UDP Proxy Bridge
 * 
 * Bridges WebAssembly browser clients (via WebSockets) to the native
 * Unreal Tournament 99 Dedicated Server UDP port 7777.
 *
 * Usage:
 *   node ut99_wsproxy.js [--port 8080] [--ut-host 127.0.0.1] [--ut-port 7777]
 */

const WebSocket = require('ws');
const dgram = require('dgram');

const WS_PORT = parseInt(process.env.WS_PORT || process.argv[2] || '8080', 10);
const UT_HOST = process.env.UT_HOST || '127.0.0.1';
const UT_PORT = parseInt(process.env.UT_PORT || '7777', 10);

const wss = new WebSocket.Server({ port: WS_PORT }, () => {
	console.log(`[UT99-WSProxy] WebSocket bridge listening on port ${WS_PORT}`);
	console.log(`[UT99-WSProxy] Forwarding to UT99 Dedicated Server at ${UT_HOST}:${UT_PORT}`);
});

wss.on('connection', (ws, req) => {
	const clientIp = req.socket.remoteAddress;
	console.log(`[UT99-WSProxy] New client connected from ${clientIp}`);

	// Create a UDP socket for this specific client session
	const udpClient = dgram.createSocket('udp4');

	udpClient.on('message', (msg) => {
		if (ws.readyState === WebSocket.OPEN) {
			ws.send(msg);
		}
	});

	udpClient.on('error', (err) => {
		console.error(`[UT99-WSProxy] UDP error for client ${clientIp}:`, err.message);
	});

	ws.on('message', (data, isBinary) => {
		const buffer = isBinary ? data : Buffer.from(data);
		udpClient.send(buffer, 0, buffer.length, UT_PORT, UT_HOST, (err) => {
			if (err) {
				console.error(`[UT99-WSProxy] Send error:`, err.message);
			}
		});
	});

	ws.on('close', () => {
		console.log(`[UT99-WSProxy] Client ${clientIp} disconnected`);
		udpClient.close();
	});

	ws.on('error', (err) => {
		console.error(`[UT99-WSProxy] WebSocket error for ${clientIp}:`, err.message);
		udpClient.close();
	});
});

process.on('SIGINT', () => {
	console.log('[UT99-WSProxy] Shutting down...');
	wss.close(() => process.exit(0));
});
