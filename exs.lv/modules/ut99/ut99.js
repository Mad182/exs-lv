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
			return; // Not on the ut99 game stage
		}

		// Tabs
		const tabButtons = document.querySelectorAll('.ut99-tab-btn');
		const tabPanes = document.querySelectorAll('.ut99-tab-pane');

		// Tab switching
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

		// Pointer Lock API Handling
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
				canvas.requestPointerLock();
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

			// Show loading progress
			if (loadingBox) loadingBox.style.display = 'block';
			if (loadingStatus) loadingStatus.textContent = 'Pārbauda un ielādē resursus...';

			if (window.UT99AssetCache) {
				await window.UT99AssetCache.init();
				window.UT99AssetCache.onProgress = (file, loaded, total) => {
					const pct = Math.min(100, Math.round((loaded / total) * 100));
					if (progressBar) progressBar.style.width = pct + '%';
					if (loadingPercent) loadingPercent.textContent = pct + '%';
					if (loadingStatus) loadingStatus.textContent = `Ielādē ${file}...`;
				};
			}

			// Configure Emscripten Module runtime
			window.Module = window.Module || {};
			window.Module.canvas = canvas;
			window.Module.arguments = commandArgs;

			window.Module.onRuntimeInitialized = function () {
				if (loadingBox) loadingBox.style.display = 'none';
				if (startOverlay) startOverlay.style.display = 'none';
				isMatchRunning = true;
				canvas.focus();
				canvas.requestPointerLock();
			};

			// Load WebAssembly runtime if not already loaded
			if (!window.UT99_WASM_LOADED) {
				window.UT99_WASM_LOADED = true;
				const script = document.createElement('script');
				script.src = '/games/ut99/index.js';
				script.onerror = () => {
					// Fallback demonstration mode if static assets not yet populated on host
					setTimeout(() => {
						if (loadingBox) loadingBox.style.display = 'none';
						if (startOverlay) startOverlay.style.display = 'none';
						isMatchRunning = true;
						renderDemoArena();
					}, 600);
				};
				document.body.appendChild(script);
			} else {
				if (startOverlay) startOverlay.style.display = 'none';
				isMatchRunning = true;
				canvas.focus();
				canvas.requestPointerLock();
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
				const selectedMap = mapSelect ? mapSelect.value : 'DM-Deck16][';

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

				const map = mapSelect ? mapSelect.value : 'DM-Deck16][';
				const botCount = botCountSelect ? botCountSelect.value : '5';
				const botSkill = botSkillSelect ? botSkillSelect.value : '1';
				const fragLimit = fragLimitSelect ? fragLimitSelect.value : '15';

				const args = [
					`${map}.unr`,
					`?game=Botpack.DeathMatchPlus`,
					`?numplay=${botCount}`,
					`?difficulty=${botSkill}`,
					`?fraglimit=${fragLimit}`
				];
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
