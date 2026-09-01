/**
 * EXS.LV Unreal Tournament 99 WebAssembly Frontend Controller
 */

(function () {
	'use strict';

	function initUT99() {
		// State
		let sessionToken = '';
		let currentMatchStartTime = 0;
		let isAudioEnabled = true;
		let mouseSensitivity = 3.0;
		let isMatchRunning = false;
		let serverStatusInterval = null;

		// DOM Elements
		const canvas = document.getElementById('canvas');
		const startOverlay = document.getElementById('ut99-start-overlay');
		const gameoverOverlay = document.getElementById('ut99-gameover-overlay');
		const loadingBox = document.getElementById('ut99-loading-box');
		const loadingStatus = document.getElementById('ut99-loading-status');
		const loadingPercent = document.getElementById('ut99-loading-percent');
		const progressBar = document.getElementById('ut99-progress-bar');
		const pointerStatus = document.getElementById('ut99-pointer-status');
		const soundBtn = document.getElementById('ut99-sound-btn');
		const reopenMenuBtn = document.getElementById('ut99-reopen-menu-btn');
		const theaterToggle = document.getElementById('ut99-theater-toggle');
		const fullscreenToggle = document.getElementById('ut99-fullscreen-toggle');
		const stageContainer = document.getElementById('ut99-stage-container');

		if (!canvas) {
			return;
		}

		// Tabs
		const tabButtons = document.querySelectorAll('.ut99-tab-btn');
		const tabPanes = document.querySelectorAll('.ut99-tab-pane');

		tabButtons.forEach((btn) => {
			btn.addEventListener('click', () => {
				tabButtons.forEach((b) => b.classList.remove('active'));
				tabPanes.forEach((p) => p.classList.remove('active'));
				btn.classList.add('active');
				const targetPane = document.getElementById(btn.getAttribute('data-tab'));
				if (targetPane) targetPane.classList.add('active');
			});
		});

		// Settings Handlers
		const sensInput = document.getElementById('ut99-mouse-sens');
		const sensVal = document.getElementById('sens-val');
		if (sensInput) {
			sensInput.addEventListener('input', (e) => {
				mouseSensitivity = parseFloat(e.target.value);
				if (sensVal) sensVal.textContent = mouseSensitivity.toFixed(1);
				localStorage.setItem('ut99_sens', mouseSensitivity);
			});
			const savedSens = localStorage.getItem('ut99_sens');
			if (savedSens) {
				mouseSensitivity = parseFloat(savedSens);
				sensInput.value = mouseSensitivity;
				if (sensVal) sensVal.textContent = mouseSensitivity.toFixed(1);
			}
		}

		const volInput = document.getElementById('ut99-volume-slider');
		const volVal = document.getElementById('vol-val');
		if (volInput) {
			volInput.addEventListener('input', (e) => {
				const vol = parseInt(e.target.value, 10);
				if (volVal) volVal.textContent = vol + '%';
				if (window.AudioContext && window.ut99MasterGain) {
					window.ut99MasterGain.gain.value = vol / 100;
				}
				localStorage.setItem('ut99_volume', vol);
			});
		}

		// Pointer Lock Handling
		function updatePointerLockStatus() {
			if (document.pointerLockElement === canvas) {
				if (pointerStatus) {
					pointerStatus.textContent = '🔒 Nofiksēta (Esc lai atbrīvotu)';
					pointerStatus.style.background = '#16a34a';
				}
			} else {
				if (pointerStatus) {
					pointerStatus.textContent = 'Klikšķini, lai nofiksētu';
					pointerStatus.style.background = '#1e293b';
				}
			}
		}

		document.addEventListener('pointerlockchange', updatePointerLockStatus, false);

		canvas.addEventListener('click', () => {
			if (isMatchRunning && document.pointerLockElement !== canvas) {
				try {
					canvas.requestPointerLock();
				} catch (e) {}
			}
		});

		// Fullscreen & Theater Toggles
		if (theaterToggle) {
			theaterToggle.addEventListener('click', () => {
				const wrapper = document.querySelector('.ut99-wrapper');
				if (wrapper) wrapper.classList.toggle('theater-mode');
			});
		}

		if (fullscreenToggle && stageContainer) {
			fullscreenToggle.addEventListener('click', () => {
				if (!document.fullscreenElement) {
					stageContainer.requestFullscreen().catch((err) => {
						console.warn('Fullscreen error:', err);
					});
				} else {
					document.exitFullscreen();
				}
			});
		}

		// Sound Toggle
		if (soundBtn) {
			soundBtn.addEventListener('click', () => {
				isAudioEnabled = !isAudioEnabled;
				soundBtn.textContent = isAudioEnabled ? '🔊 Ieslēgta' : '🔇 Izslēgta';
				if (window.ut99MasterGain) {
					window.ut99MasterGain.gain.value = isAudioEnabled ? (parseInt(volInput ? volInput.value : 80, 10) / 100) : 0;
				}
			});
		}

		// Re-open Menu
		if (reopenMenuBtn) {
			reopenMenuBtn.addEventListener('click', () => {
				if (document.pointerLockElement === canvas) {
					document.exitPointerLock();
				}
				if (startOverlay) startOverlay.style.display = 'flex';
			});
		}

		// Initialize Anti-Cheat Session Token
		async function initSessionToken() {
			try {
				const res = await fetch('/ut99?action=init_token');
				const data = await res.json();
				if (data && data.token) {
					sessionToken = data.token;
				}
			} catch (e) {
				console.warn('Token init failed:', e);
			}
		}

		// Query Multiplayer Server Status
		async function fetchServerStatus() {
			try {
				const res = await fetch('/ut99?action=server_status');
				const data = await res.json();
				const statusIndicator = document.getElementById('server-status-indicator');
				const statusText = document.getElementById('server-status-text');
				const serverMap = document.getElementById('server-map-name');
				const playerCount = document.getElementById('server-player-count');

				if (data && data.online) {
					if (statusIndicator) {
						statusIndicator.className = 'server-status-pill online';
					}
					if (statusText) statusText.textContent = 'EXS Serveris Tiešsaistē';
					if (serverMap) serverMap.textContent = data.map || 'DM-Deck16][';
					if (playerCount) playerCount.textContent = `${data.players || 0} / ${data.max_players || 16}`;
				} else {
					if (statusIndicator) {
						statusIndicator.className = 'server-status-pill offline';
						statusIndicator.style.color = '#f87171';
						statusIndicator.style.background = 'rgba(239, 68, 68, 0.15)';
						statusIndicator.style.borderColor = 'rgba(239, 68, 68, 0.3)';
					}
					if (statusText) statusText.textContent = 'Serveris Pašlaik Bezsaistē (Lietot botiem)';
				}
			} catch (e) {
				// silent fallback
			}
		}

		// Launch Game Mode
		async function launchGame(commandArgs) {
			await initSessionToken();
			currentMatchStartTime = Math.floor(Date.now() / 1000);

			if (loadingBox) loadingBox.style.display = 'block';
			if (loadingStatus) loadingStatus.textContent = 'Pārbauda spēles dzinēju un resursus...';
			if (progressBar) progressBar.style.width = '5%';
			if (loadingPercent) loadingPercent.textContent = '5%';

			// Configure Emscripten Module
			window.Module = window.Module || {};
			window.Module.noInitialRun = true;
			window.Module.arguments = commandArgs;
			window.Module.canvas = canvas;
			window.Module.wasmBinaryFile = '/games/ut99/index.wasm';
			window.Module.locateFile = function(path) {
				return '/games/ut99/' + path;
			};

			window.Module.preRun = window.Module.preRun || [];
			window.Module.preRun.push(function() {
				if (typeof syncDataFiles === "function") {
					var syncer = syncDataFiles('ut99_exs_v3', 'https://www.icculus.org/ut99-emscripten/flyby/wasm/gamedata/');
					syncer.onerror = function(why) {
						console.error("Syncer error:", why);
						if (loadingStatus) loadingStatus.textContent = why;
					};
					syncer.onprogress = function(str, pct) {
						if (loadingStatus) loadingStatus.textContent = str;
						if (pct > 0) {
							if (progressBar) progressBar.style.width = pct + '%';
							if (loadingPercent) loadingPercent.textContent = pct + '%';
						}
					};
					syncer.onsuccess = function(why) {
						var db = syncer.db;
						var manifest = syncer.manifest;
						var total_requests = 0;
						var num_requests = 0;

						var tx = db.transaction("data", "readonly");
						var store = tx.objectStore("data");
						var dataIndex = store.index("data");

						for (var fname in manifest) {
							total_requests++;
							num_requests++;
							var req = dataIndex.get(fname);
							req.filesize = manifest[fname].filesize;
							req.onsuccess = function(event) {
								if (!event.target.result) {
									num_requests--;
									return;
								}
								var path = "/" + event.target.result.filename;
								var ui8arr = new Uint8Array(event.target.result.chunk);
								var len = event.target.filesize || ui8arr.length;
								var arr = new Array(len);
								for (var j = 0; j < len; ++j) {
									arr[j] = ui8arr[j];
								}

								var basedir = path.substring(0, path.lastIndexOf('/')) || '/';
								var filename = path.substring(path.lastIndexOf('/') + 1);

								try {
									if (typeof FS.mkdirTree === 'function') {
										FS.mkdirTree(basedir);
									} else if (typeof FS.createPath === 'function') {
										FS.createPath('/', basedir, true, true);
									}
								} catch (err) {}

								try {
									FS.createDataFile(basedir, filename, arr, true, true, true);
								} catch (err) {
									console.warn("createDataFile error for " + path, err);
								}

								var completed = total_requests - num_requests;
								var percent = Math.floor((completed / total_requests) * 100);
								if (progressBar) progressBar.style.width = Math.max(5, percent) + '%';
								if (loadingPercent) loadingPercent.textContent = percent + '%';
								if (loadingStatus) loadingStatus.textContent = 'Gatavo spēles failus: ' + percent + '%';

								num_requests--;
								if (num_requests <= 0) {
									console.log("MEMFS is synchronized. Preparing system configs...");

									// Ensure UnrealTournament.ini and User.ini exist in /System
									try {
										var defIni = FS.findObject('/System/Default.ini');
										if (defIni && defIni.contents) {
											FS.createDataFile('/System', 'UnrealTournament.ini', defIni.contents, true, true, true);
										}
									} catch (e) {
										console.warn("Could not clone UnrealTournament.ini:", e);
									}

									try {
										var defUser = FS.findObject('/System/DefUser.ini');
										if (defUser && defUser.contents) {
											FS.createDataFile('/System', 'User.ini', defUser.contents, true, true, true);
										}
									} catch (e) {
										console.warn("Could not clone User.ini:", e);
									}

									console.log("Starting UT99 engine...");
									if (loadingBox) loadingBox.style.display = 'none';
									if (startOverlay) startOverlay.style.display = 'none';
									isMatchRunning = true;
									canvas.focus();
									try {
										canvas.requestPointerLock();
									} catch (e) {}

									// Call main with arguments
									if (typeof Module.callMain === 'function') {
										Module.callMain(Module.arguments || []);
									}
								}
							};
							req.onerror = function() {
								num_requests--;
							};
						}
					};
				}
			});

			// Pre-fetch index.wasm to avoid any 404s
			try {
				const wasmRes = await fetch('/games/ut99/index.wasm');
				if (wasmRes.ok) {
					window.Module.wasmBinary = await wasmRes.arrayBuffer();
				}
			} catch (err) {
				console.warn('WASM binary direct load warning:', err);
			}

			// Load /games/ut99/index.js
			if (!window.UT99_WASM_LOADED) {
				window.UT99_WASM_LOADED = true;
				const script = document.createElement('script');
				script.src = '/games/ut99/index.js';
				script.async = true;
				document.body.appendChild(script);
			} else {
				if (startOverlay) startOverlay.style.display = 'none';
				isMatchRunning = true;
				canvas.focus();
				try {
					canvas.requestPointerLock();
				} catch (e) {}
			}
		}

		// Demo Arena Canvas fallback / HUD preview
		function renderDemoArena() {
			const ctx = canvas.getContext('2d');
			if (!ctx) return;
			let animFrame;
			let frags = 0;
			let deaths = 0;
			let time = 0;

			function draw() {
				time += 0.02;
				ctx.fillStyle = '#090b10';
				ctx.fillRect(0, 0, canvas.width, canvas.height);

				// Grid floor
				ctx.strokeStyle = 'rgba(255, 140, 0, 0.15)';
				ctx.lineWidth = 1;
				for (let x = 0; x < canvas.width; x += 40) {
					ctx.beginPath();
					ctx.moveTo(x, 0);
					ctx.lineTo(x, canvas.height);
					ctx.stroke();
				}
				for (let y = 0; y < canvas.height; y += 40) {
					ctx.beginPath();
					ctx.moveTo(0, y);
					ctx.lineTo(canvas.width, y);
					ctx.stroke();
				}

				// Unreal Centerpiece
				ctx.fillStyle = '#ff9800';
				ctx.font = 'bold 22px monospace';
				ctx.textAlign = 'center';
				ctx.fillText('UNREAL TOURNAMENT 99 - ARENA ACTIVE', canvas.width / 2, canvas.height / 2 - 30);
				ctx.fillStyle = '#94a3b8';
				ctx.font = '14px sans-serif';
				ctx.fillText('Spied ar peli, lai tēmētu un šautu botiem!', canvas.width / 2, canvas.height / 2 + 10);

				// HUD elements
				ctx.fillStyle = 'rgba(15, 23, 42, 0.85)';
				ctx.fillRect(10, canvas.height - 50, 180, 40);
				ctx.strokeStyle = '#ff9800';
				ctx.strokeRect(10, canvas.height - 50, 180, 40);

				ctx.fillStyle = '#22c55e';
				ctx.font = 'bold 20px monospace';
				ctx.textAlign = 'left';
				ctx.fillText('HEALTH: 100', 20, canvas.height - 24);

				ctx.fillStyle = 'rgba(15, 23, 42, 0.85)';
				ctx.fillRect(canvas.width - 190, canvas.height - 50, 180, 40);
				ctx.strokeStyle = '#ff9800';
				ctx.strokeRect(canvas.width - 190, canvas.height - 50, 180, 40);

				ctx.fillStyle = '#f59e0b';
				ctx.textAlign = 'right';
				ctx.fillText(`FRAGS: ${frags}`, canvas.width - 20, canvas.height - 24);

				if (isMatchRunning) {
					animFrame = requestAnimationFrame(draw);
				}
			}

			function onShoot() {
				if (!isMatchRunning) return;
				frags++;
				if (frags >= 15) {
					isMatchRunning = false;
					canvas.removeEventListener('mousedown', onShoot);
					cancelAnimationFrame(animFrame);
					handleMatchEnd(frags, deaths);
				}
			}

			canvas.addEventListener('mousedown', onShoot);

			draw();
		}

		// Match Completion & Score Submission
		async function handleMatchEnd(frags, deaths) {
			const duration = Math.max(1, Math.floor(Date.now() / 1000) - currentMatchStartTime);
			const kd = deaths > 0 ? (frags / deaths).toFixed(1) : frags.toFixed(1);

			const finalFrags = document.getElementById('ut99-final-frags');
			const finalDeaths = document.getElementById('ut99-final-deaths');
			const finalKd = document.getElementById('ut99-final-kd');

			if (finalFrags) finalFrags.textContent = frags;
			if (finalDeaths) finalDeaths.textContent = deaths;
			if (finalKd) finalKd.textContent = kd;

			if (document.pointerLockElement === canvas) {
				document.exitPointerLock();
			}

			if (gameoverOverlay) gameoverOverlay.style.display = 'flex';

			// Submit score to backend
			if (window.UT99_IS_LOGGED && sessionToken) {
				const mapSelect = document.getElementById('ut99-map-select');
				const selectedMap = mapSelect ? mapSelect.value : 'CityIntro';

				try {
					const formData = new FormData();
					formData.append('token', sessionToken);
					formData.append('score', frags);
					formData.append('map', selectedMap);
					formData.append('duration', duration);

					const res = await fetch('/ut99?action=push', {
						method: 'POST',
						body: formData
					});
					const data = await res.json();
					if (data && data.isNewRecord) {
						const recordAlert = document.getElementById('ut99-record-alert');
						if (recordAlert) recordAlert.style.display = 'block';
						const statHigh = document.getElementById('stat-highscore');
						if (statHigh) statHigh.textContent = data.highScore + ' fragi';
					}
				} catch (e) {
					console.warn('Score submit failed:', e);
				}
			}
		}

		// Event Listeners for Game Launch
		const launchBotBtn = document.getElementById('ut99-launch-bot-btn');
		if (launchBotBtn) {
			launchBotBtn.addEventListener('click', () => {
				const mapSelect = document.getElementById('ut99-map-select');
				const botCountSelect = document.getElementById('ut99-bot-count');
				const botSkillSelect = document.getElementById('ut99-bot-skill');
				const fragLimitSelect = document.getElementById('ut99-frag-limit');

				const map = mapSelect ? mapSelect.value : 'CityIntro';
				const botCount = botCountSelect ? botCountSelect.value : '5';
				const botSkill = botSkillSelect ? botSkillSelect.value : '1';
				const fragLimit = fragLimitSelect ? fragLimitSelect.value : '15';

				let args = [];
				if (map === 'CityIntro') {
					args = [`${map}.unr`];
				} else {
					args = [
						`${map}.unr`,
						`?game=Botpack.DeathMatchPlus`,
						`?numplay=${botCount}`,
						`?difficulty=${botSkill}`,
						`?fraglimit=${fragLimit}`
					];
				}
				launchGame(args);
			});
		}

		const joinServerBtn = document.getElementById('ut99-join-server-btn');
		if (joinServerBtn) {
			joinServerBtn.addEventListener('click', () => {
				const args = ['open', 'wss://exs.lv/ut99-ws'];
				launchGame(args);
			});
		}

		const restartBtn = document.getElementById('ut99-restart-btn');
		if (restartBtn) {
			restartBtn.addEventListener('click', () => {
				if (gameoverOverlay) gameoverOverlay.style.display = 'none';
				if (startOverlay) startOverlay.style.display = 'flex';
			});
		}

		// Init
		fetchServerStatus();
		serverStatusInterval = setInterval(fetchServerStatus, 30000);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initUT99);
	} else {
		initUT99();
	}
})();
