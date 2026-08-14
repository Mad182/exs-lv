/**
 * Space Invaders - Client Engine (Vanilla JS + Canvas + Web Audio API)
 */

document.addEventListener('DOMContentLoaded', function () {
	const canvas = document.getElementById('invaders-canvas');
	if (!canvas) return;
	const ctx = canvas.getContext('2d');

	// UI Overlays & Buttons
	const startOverlay = document.getElementById('invaders-start-overlay');
	const gameoverOverlay = document.getElementById('invaders-gameover-overlay');
	const pauseOverlay = document.getElementById('invaders-pause-overlay');
	const startBtn = document.getElementById('invaders-start-btn');
	const restartBtn = document.getElementById('invaders-restart-btn');
	const resumeBtn = document.getElementById('invaders-resume-btn');
	const pauseToggleBtn = document.getElementById('invaders-pause-toggle-btn');
	const soundToggleBtn = document.getElementById('invaders-sound-toggle');

	const finalScoreEl = document.getElementById('invaders-final-score');
	const finalWaveEl = document.getElementById('invaders-final-wave');
	const bestScoreEl = document.getElementById('invaders-best-score');
	const statHighscoreEl = document.getElementById('stat-highscore');
	const recordAlertEl = document.getElementById('invaders-record-alert');

	// Touch Buttons
	const touchLeftBtn = document.getElementById('touch-left-btn');
	const touchRightBtn = document.getElementById('touch-right-btn');
	const touchFireBtn = document.getElementById('touch-fire-btn');

	// Audio Synthesizer State
	let soundEnabled = localStorage.getItem('invaders_sound_enabled') !== 'false';
	let audioCtx = null;

	function initAudio() {
		if (!audioCtx) {
			const AudioContext = window.AudioContext || window.webkitAudioContext;
			if (AudioContext) audioCtx = new AudioContext();
		}
		if (audioCtx && audioCtx.state === 'suspended') {
			audioCtx.resume();
		}
	}

	function playSynthSound(type, freq, duration, sweepFreq = null) {
		if (!soundEnabled || !audioCtx) return;
		try {
			const osc = audioCtx.createOscillator();
			const gain = audioCtx.createGain();
			osc.type = type;
			osc.frequency.setValueAtTime(freq, audioCtx.currentTime);
			if (sweepFreq) {
				osc.frequency.exponentialRampToValueAtTime(sweepFreq, audioCtx.currentTime + duration);
			}
			gain.gain.setValueAtTime(0.15, audioCtx.currentTime);
			gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + duration);

			osc.connect(gain);
			gain.connect(audioCtx.destination);

			osc.start();
			osc.stop(audioCtx.currentTime + duration);
		} catch (e) {}
	}

	function playShootSound() {
		playSynthSound('square', 800, 0.1, 150);
	}

	function playExplosionSound() {
		playSynthSound('sawtooth', 180, 0.25, 30);
	}

	function playUFOSound() {
		playSynthSound('sine', 500, 0.15, 800);
	}

	function playWaveClearSound() {
		if (!soundEnabled || !audioCtx) return;
		const notes = [440, 554, 659, 880];
		notes.forEach((freq, i) => {
			setTimeout(() => playSynthSound('triangle', freq, 0.12), i * 90);
		});
	}

	// Game Engine Parameters
	const WIDTH = 500;
	const HEIGHT = 600;

	let gameState = 'START'; // START, PLAYING, PAUSED, GAMEOVER
	let score = 0;
	let wave = 1;
	let lives = 3;
	let highScore = window.INVADERS_USER_HIGHSCORE || 0;
	let sessionToken = '';
	let startTime = 0;

	let keys = { left: false, right: false, fire: false };

	// Entities
	let player = {
		x: WIDTH / 2 - 15,
		y: 540,
		width: 30,
		height: 18,
		speed: 4.5,
		cooldown: 0,
		rapidFireTimer: 0
	};

	let playerShots = [];
	let invaderShots = [];
	let invaders = [];
	let ufo = null;
	let powerups = [];
	let floatingTexts = [];
	let bunkers = [];

	let invaderDirection = 1; // 1 = right, -1 = left
	let invaderStepTimer = 0;
	let invaderStepInterval = 800; // ms
	let invaderAnimFrame = 0;
	let ufoTimer = 0;

	let waveBannerTimer = 0;
	let waveBannerText = '';

	// Sprite Data (8x8 pixel matrices for classic Invader art)
	const SPRITES = {
		squid: [
			[0,0,0,1,1,0,0,0],
			[0,0,1,1,1,1,0,0],
			[0,1,1,1,1,1,1,0],
			[1,1,0,1,1,0,1,1],
			[1,1,1,1,1,1,1,1],
			[0,0,1,0,0,1,0,0],
			[0,1,0,1,1,0,1,0],
			[1,0,1,0,0,1,0,1]
		],
		squidFrame2: [
			[0,0,0,1,1,0,0,0],
			[0,0,1,1,1,1,0,0],
			[0,1,1,1,1,1,1,0],
			[1,1,0,1,1,0,1,1],
			[1,1,1,1,1,1,1,1],
			[0,1,0,1,1,0,1,0],
			[1,0,0,0,0,0,0,1],
			[0,1,0,0,0,0,1,0]
		],
		crab: [
			[0,0,1,0,0,1,0,0],
			[0,0,0,1,1,0,0,0],
			[0,0,1,1,1,1,0,0],
			[0,1,1,0,0,1,1,0],
			[1,1,1,1,1,1,1,1],
			[1,0,1,1,1,1,0,1],
			[1,0,1,0,0,1,0,1],
			[0,0,0,1,1,0,0,0]
		],
		crabFrame2: [
			[0,0,1,0,0,1,0,0],
			[1,0,0,1,1,0,0,1],
			[1,0,1,1,1,1,0,1],
			[1,1,1,0,0,1,1,1],
			[1,1,1,1,1,1,1,1],
			[0,1,1,1,1,1,1,0],
			[0,0,1,0,0,1,0,0],
			[1,0,0,0,0,0,0,1]
		],
		octopus: [
			[0,0,0,1,1,0,0,0],
			[0,1,1,1,1,1,1,0],
			[1,1,1,1,1,1,1,1],
			[1,1,0,1,1,0,1,1],
			[1,1,1,1,1,1,1,1],
			[0,0,1,0,0,1,0,0],
			[0,1,0,1,1,0,1,0],
			[0,1,0,0,0,0,1,0]
		],
		octopusFrame2: [
			[0,0,0,1,1,0,0,0],
			[0,1,1,1,1,1,1,0],
			[1,1,1,1,1,1,1,1],
			[1,1,0,1,1,0,1,1],
			[1,1,1,1,1,1,1,1],
			[0,0,1,0,0,1,0,0],
			[1,0,0,1,1,0,0,1],
			[0,1,0,0,0,0,1,0]
		]
	};

	function drawSprite(sprite, posX, posY, pixelSize, color) {
		ctx.fillStyle = color;
		for (let r = 0; r < sprite.length; r++) {
			for (let c = 0; c < sprite[r].length; c++) {
				if (sprite[r][c]) {
					ctx.fillRect(posX + c * pixelSize, posY + r * pixelSize, pixelSize, pixelSize);
				}
			}
		}
	}

	// Initialize Bunkers
	function createBunkers() {
		bunkers = [];
		const bunkerCount = 4;
		const spacing = (WIDTH - 4 * 60) / 5;
		for (let b = 0; b < bunkerCount; b++) {
			const bx = spacing + b * (60 + spacing);
			const by = 460;

			// Create 15x10 grid of 4x4 blocks
			let blocks = [];
			for (let r = 0; r < 10; r++) {
				for (let c = 0; c < 15; c++) {
					// Arch cutout logic at bottom center
					if (r >= 6 && c >= 4 && c <= 10) continue;
					// Top rounded corners cutout
					if (r === 0 && (c === 0 || c === 14)) continue;

					blocks.push({
						x: bx + c * 4,
						y: by + r * 4,
						width: 4,
						height: 4,
						active: true
					});
				}
			}
			bunkers.push(blocks);
		}
	}

	// Spawn Invaders Wave
	function spawnWave() {
		invaders = [];
		const cols = 11;
		const rows = 5;
		const startX = 40;
		const startY = 80;
		const spacingX = 38;
		const spacingY = 32;

		for (let r = 0; r < rows; r++) {
			let type = 'octopus';
			let points = 10;
			let color = '#00ff66';

			if (r === 0) {
				type = 'squid';
				points = 30;
				color = '#00e5ff';
			} else if (r === 1 || r === 2) {
				type = 'crab';
				points = 20;
				color = '#ff007f';
			}

			for (let c = 0; c < cols; c++) {
				invaders.push({
					x: startX + c * spacingX,
					y: startY + r * spacingY,
					width: 24,
					height: 20,
					type: type,
					points: points,
					color: color,
					alive: true
				});
			}
		}

		invaderDirection = 1;
		invaderAnimFrame = 0;
		invaderStepInterval = Math.max(120, 800 - (wave - 1) * 60);
		invaderStepTimer = 0;

		waveBannerText = 'VILNIS ' + wave;
		waveBannerTimer = 100; // frames
	}

	// Fetch Session Token
	function fetchToken() {
		fetch('/invaders?action=init_token')
			.then(res => res.json())
			.then(data => {
				if (data.success) {
					sessionToken = data.token;
				}
			})
			.catch(() => {});
	}

	// Submit High Score
	function submitScore() {
		if (score <= 0 || !window.INVADERS_IS_LOGGED) return;

		const duration = Math.max(1, Math.floor((Date.now() - startTime) / 1000));
		const formData = new FormData();
		formData.append('token', sessionToken);
		formData.append('score', score);
		formData.append('wave', wave);
		formData.append('duration', duration);

		fetch('/invaders?action=push', {
			method: 'POST',
			body: formData
		})
			.then(res => res.json())
			.then(data => {
				if (data.success) {
					if (data.isNewRecord) {
						highScore = data.highScore;
						statHighscoreEl.innerText = highScore;
						bestScoreEl.innerText = highScore;
						recordAlertEl.style.display = 'block';
					}
				}
			})
			.catch(() => {});
	}

	// Start New Game
	function startNewGame() {
		initAudio();
		score = 0;
		wave = 1;
		lives = 3;
		player.x = WIDTH / 2 - 15;
		player.rapidFireTimer = 0;
		playerShots = [];
		invaderShots = [];
		ufo = null;
		powerups = [];
		floatingTexts = [];
		ufoTimer = Math.floor(Math.random() * 600) + 600;

		createBunkers();
		spawnWave();
		fetchToken();

		startTime = Date.now();
		gameState = 'PLAYING';

		startOverlay.style.display = 'none';
		gameoverOverlay.style.display = 'none';
		pauseOverlay.style.display = 'none';
		recordAlertEl.style.display = 'none';
	}

	// Controls Listeners
	window.addEventListener('keydown', function (e) {
		if (e.code === 'ArrowLeft' || e.code === 'KeyA') keys.left = true;
		if (e.code === 'ArrowRight' || e.code === 'KeyD') keys.right = true;
		if (e.code === 'Space' || e.code === 'ArrowUp' || e.code === 'KeyW') {
			keys.fire = true;
			e.preventDefault();
		}
		if (e.code === 'KeyP') {
			togglePause();
		}
	});

	window.addEventListener('keyup', function (e) {
		if (e.code === 'ArrowLeft' || e.code === 'KeyA') keys.left = false;
		if (e.code === 'ArrowRight' || e.code === 'KeyD') keys.right = false;
		if (e.code === 'Space' || e.code === 'ArrowUp' || e.code === 'KeyW') keys.fire = false;
	});

	// Touch Controls
	if (touchLeftBtn) {
		touchLeftBtn.addEventListener('touchstart', (e) => { e.preventDefault(); keys.left = true; });
		touchLeftBtn.addEventListener('touchend', (e) => { e.preventDefault(); keys.left = false; });
		touchRightBtn.addEventListener('touchstart', (e) => { e.preventDefault(); keys.right = true; });
		touchRightBtn.addEventListener('touchend', (e) => { e.preventDefault(); keys.right = false; });
		touchFireBtn.addEventListener('touchstart', (e) => { e.preventDefault(); keys.fire = true; });
		touchFireBtn.addEventListener('touchend', (e) => { e.preventDefault(); keys.fire = false; });
	}

	// Buttons
	startBtn.addEventListener('click', startNewGame);
	restartBtn.addEventListener('click', startNewGame);
	resumeBtn.addEventListener('click', function () {
		if (gameState === 'PAUSED') {
			gameState = 'PLAYING';
			pauseOverlay.style.display = 'none';
		}
	});

	function togglePause() {
		if (gameState === 'PLAYING') {
			gameState = 'PAUSED';
			pauseOverlay.style.display = 'flex';
		} else if (gameState === 'PAUSED') {
			gameState = 'PLAYING';
			pauseOverlay.style.display = 'none';
		}
	}

	pauseToggleBtn.addEventListener('click', togglePause);

	soundToggleBtn.addEventListener('click', function () {
		soundEnabled = !soundEnabled;
		localStorage.setItem('invaders_sound_enabled', soundEnabled);
		soundToggleBtn.innerHTML = soundEnabled ? '🔊 Ieslēgta' : '🔇 Izslēgta';
	});
	soundToggleBtn.innerHTML = soundEnabled ? '🔊 Ieslēgta' : '🔇 Izslēgta';

	// Game Loop Update
	let lastTime = performance.now();

	function update(dt) {
		if (gameState !== 'PLAYING') return;

		// Player Movement
		if (keys.left && player.x > 15) {
			player.x -= player.speed;
		}
		if (keys.right && player.x < WIDTH - player.width - 15) {
			player.x += player.speed;
		}

		// Player Firing
		if (player.cooldown > 0) player.cooldown--;
		if (player.rapidFireTimer > 0) player.rapidFireTimer--;

		const maxShots = player.rapidFireTimer > 0 ? 4 : 2;
		const coolRate = player.rapidFireTimer > 0 ? 8 : 16;

		if (keys.fire && player.cooldown <= 0 && playerShots.length < maxShots) {
			playerShots.push({
				x: player.x + player.width / 2 - 2,
				y: player.y - 6,
				width: 4,
				height: 10,
				speed: 7
			});
			playShootSound();
			player.cooldown = coolRate;
		}

		// Update Player Shots
		for (let i = playerShots.length - 1; i >= 0; i--) {
			let s = playerShots[i];
			s.y -= s.speed;
			if (s.y < -10) {
				playerShots.splice(i, 1);
			}
		}

		// Invader Step Logic
		invaderStepTimer += dt;
		const aliveInvaders = invaders.filter(inv => inv.alive);

		if (aliveInvaders.length === 0) {
			// Wave Cleared!
			playWaveClearSound();
			score += wave * 200;
			wave++;
			spawnWave();
			return;
		}

		// Adjust step interval dynamically as invaders are killed
		const currentInterval = Math.max(50, (aliveInvaders.length / 55) * invaderStepInterval);

		if (invaderStepTimer >= currentInterval) {
			invaderStepTimer = 0;
			invaderAnimFrame = 1 - invaderAnimFrame;

			let moveDown = false;
			// Check edge collision
			for (let inv of aliveInvaders) {
				if ((invaderDirection === 1 && inv.x + inv.width >= WIDTH - 20) ||
					(invaderDirection === -1 && inv.x <= 20)) {
					moveDown = true;
					break;
				}
			}

			if (moveDown) {
				invaderDirection *= -1;
				for (let inv of aliveInvaders) {
					inv.y += 14;
					// Check if reached player level
					if (inv.y + inv.height >= player.y) {
						triggerGameOver();
						return;
					}
				}
			} else {
				for (let inv of aliveInvaders) {
					inv.x += invaderDirection * 10;
				}
			}

			// Random Invader Bomb Firing
			if (Math.random() < 0.35 + (wave * 0.05)) {
				const shooter = aliveInvaders[Math.floor(Math.random() * aliveInvaders.length)];
				invaderShots.push({
					x: shooter.x + shooter.width / 2 - 2,
					y: shooter.y + shooter.height,
					width: 4,
					height: 10,
					speed: 3.5 + Math.min(2.5, wave * 0.3)
				});
			}
		}

		// Update Invader Shots
		for (let i = invaderShots.length - 1; i >= 0; i--) {
			let b = invaderShots[i];
			b.y += b.speed;

			// Collision with Player
			if (b.x < player.x + player.width &&
				b.x + b.width > player.x &&
				b.y < player.y + player.height &&
				b.y + b.height > player.y) {

				invaderShots.splice(i, 1);
				playExplosionSound();
				lives--;

				if (lives <= 0) {
					triggerGameOver();
					return;
				}
				continue;
			}

			if (b.y > HEIGHT + 10) {
				invaderShots.splice(i, 1);
			}
		}

		// Collision: Player Shots vs Invaders
		for (let i = playerShots.length - 1; i >= 0; i--) {
			let s = playerShots[i];
			let hit = false;

			for (let inv of aliveInvaders) {
				if (s.x < inv.x + inv.width &&
					s.x + s.width > inv.x &&
					s.y < inv.y + inv.height &&
					s.y + s.height > inv.y) {

					inv.alive = false;
					score += inv.points;
					hit = true;
					playExplosionSound();

					floatingTexts.push({
						x: inv.x + inv.width / 2,
						y: inv.y,
						text: '+' + inv.points,
						color: inv.color,
						life: 30
					});
					break;
				}
			}

			if (hit) {
				playerShots.splice(i, 1);
				continue;
			}

			// Collision: Player Shots vs UFO
			if (ufo && s.x < ufo.x + ufo.width &&
				s.x + s.width > ufo.x &&
				s.y < ufo.y + ufo.height &&
				s.y + s.height > ufo.y) {

				const ufoPoints = [100, 150, 200, 300][Math.floor(Math.random() * 4)];
				score += ufoPoints;

				floatingTexts.push({
					x: ufo.x + ufo.width / 2,
					y: ufo.y,
					text: '+' + ufoPoints + ' UFO!',
					color: '#ffe600',
					life: 45
				});

				// Power-up chance
				if (Math.random() < 0.5) {
					powerups.push({
						x: ufo.x + ufo.width / 2 - 8,
						y: ufo.y,
						type: 'rapid',
						speed: 2
					});
				}

				ufo = null;
				hit = true;
				playerShots.splice(i, 1);
				playExplosionSound();
			}
		}

		// Collision: Shots vs Bunkers
		function checkBunkerCollision(shot, isPlayer) {
			for (let b = 0; b < bunkers.length; b++) {
				let blocks = bunkers[b];
				for (let bl of blocks) {
					if (!bl.active) continue;
					if (shot.x < bl.x + bl.width &&
						shot.x + shot.width > bl.x &&
						shot.y < bl.y + bl.height &&
						shot.y + shot.height > bl.y) {

						bl.active = false;
						return true;
					}
				}
			}
			return false;
		}

		for (let i = playerShots.length - 1; i >= 0; i--) {
			if (checkBunkerCollision(playerShots[i], true)) {
				playerShots.splice(i, 1);
			}
		}

		for (let i = invaderShots.length - 1; i >= 0; i--) {
			if (checkBunkerCollision(invaderShots[i], false)) {
				invaderShots.splice(i, 1);
			}
		}

		// UFO Spawn & Movement
		ufoTimer--;
		if (ufoTimer <= 0 && !ufo) {
			const dir = Math.random() < 0.5 ? 1 : -1;
			ufo = {
				x: dir === 1 ? -40 : WIDTH + 40,
				y: 45,
				width: 36,
				height: 16,
				dir: dir,
				speed: 2.2
			};
			playUFOSound();
			ufoTimer = Math.floor(Math.random() * 800) + 700;
		}

		if (ufo) {
			ufo.x += ufo.dir * ufo.speed;
			if ((ufo.dir === 1 && ufo.x > WIDTH + 50) || (ufo.dir === -1 && ufo.x < -50)) {
				ufo = null;
			}
		}

		// Power-ups Movement & Pickup
		for (let i = powerups.length - 1; i >= 0; i--) {
			let p = powerups[i];
			p.y += p.speed;

			if (p.x < player.x + player.width &&
				p.x + 16 > player.x &&
				p.y < player.y + player.height &&
				p.y + 16 > player.y) {

				player.rapidFireTimer = 360; // 6 seconds rapid fire
				floatingTexts.push({
					x: player.x + 15,
					y: player.y - 15,
					text: '⚡ RAPID FIRE!',
					color: '#00e5ff',
					life: 40
				});
				powerups.splice(i, 1);
				playWaveClearSound();
				continue;
			}

			if (p.y > HEIGHT + 10) {
				powerups.splice(i, 1);
			}
		}

		// Floating text update
		for (let i = floatingTexts.length - 1; i >= 0; i--) {
			let ft = floatingTexts[i];
			ft.y -= 0.8;
			ft.life--;
			if (ft.life <= 0) {
				floatingTexts.splice(i, 1);
			}
		}
	}

	function triggerGameOver() {
		gameState = 'GAMEOVER';
		finalScoreEl.innerText = score;
		finalWaveEl.innerText = wave;
		bestScoreEl.innerText = Math.max(score, highScore);
		gameoverOverlay.style.display = 'flex';
		submitScore();
	}

	// Render Loop
	function render() {
		// Clear Canvas
		ctx.clearRect(0, 0, WIDTH, HEIGHT);

		// Starfield Background
		ctx.fillStyle = 'rgba(255, 255, 255, 0.4)';
		for (let i = 0; i < 30; i++) {
			let sx = (i * 37) % WIDTH;
			let sy = (i * 59) % HEIGHT;
			ctx.fillRect(sx, sy, 1.5, 1.5);
		}

		// Draw Bunkers
		ctx.fillStyle = '#00ff66';
		for (let b = 0; b < bunkers.length; b++) {
			for (let bl of bunkers[b]) {
				if (bl.active) {
					ctx.fillRect(bl.x, bl.y, bl.width, bl.height);
				}
			}
		}

		// Draw Invaders
		for (let inv of invaders) {
			if (!inv.alive) continue;
			let spriteName = inv.type + (invaderAnimFrame === 1 ? 'Frame2' : '');
			let sprite = SPRITES[spriteName] || SPRITES[inv.type];
			drawSprite(sprite, inv.x, inv.y, 3, inv.color);
		}

		// Draw UFO
		if (ufo) {
			ctx.fillStyle = '#ff0055';
			ctx.fillRect(ufo.x + 8, ufo.y, 20, 4);
			ctx.fillRect(ufo.x + 4, ufo.y + 4, 28, 6);
			ctx.fillRect(ufo.x, ufo.y + 10, 36, 4);
			ctx.fillStyle = '#ffe600';
			ctx.fillRect(ufo.x + 10, ufo.y + 6, 4, 4);
			ctx.fillRect(ufo.x + 22, ufo.y + 6, 4, 4);
		}

		// Draw Power-ups
		for (let p of powerups) {
			ctx.fillStyle = '#00e5ff';
			ctx.beginPath();
			ctx.arc(p.x + 8, p.y + 8, 8, 0, Math.PI * 2);
			ctx.fill();
			ctx.fillStyle = '#030611';
			ctx.font = 'bold 10px monospace';
			ctx.fillText('⚡', p.x + 4, p.y + 12);
		}

		// Draw Player Ship
		ctx.fillStyle = player.rapidFireTimer > 0 ? '#00e5ff' : '#00ff66';
		// Tank Base
		ctx.fillRect(player.x, player.y + 8, player.width, 10);
		ctx.fillRect(player.x + 4, player.y + 4, player.width - 8, 4);
		// Cannon
		ctx.fillRect(player.x + player.width / 2 - 2, player.y, 4, 4);

		// Draw Player Shots
		ctx.fillStyle = player.rapidFireTimer > 0 ? '#00e5ff' : '#00ff66';
		for (let s of playerShots) {
			ctx.fillRect(s.x, s.y, s.width, s.height);
		}

		// Draw Invader Shots
		ctx.fillStyle = '#ff007f';
		for (let b of invaderShots) {
			ctx.fillRect(b.x, b.y, b.width, b.height);
		}

		// Draw Floating Texts
		for (let ft of floatingTexts) {
			ctx.fillStyle = ft.color;
			ctx.font = 'bold 13px monospace';
			ctx.fillText(ft.text, ft.x - 12, ft.y);
		}

		// HUD Overlay
		ctx.fillStyle = '#ffffff';
		ctx.font = 'bold 15px monospace';
		ctx.fillText('PUNKTI: ' + score, 15, 25);
		ctx.fillText('VILNIS: ' + wave, WIDTH / 2 - 35, 25);

		// Draw Lives Icons
		ctx.fillText('DZĪVĪBAS:', WIDTH - 150, 25);
		ctx.fillStyle = '#00ff66';
		for (let l = 0; l < lives; l++) {
			ctx.fillRect(WIDTH - 65 + l * 18, 14, 12, 10);
			ctx.fillRect(WIDTH - 61 + l * 18, 10, 4, 4);
		}

		// Wave Banner Announcement
		if (waveBannerTimer > 0) {
			waveBannerTimer--;
			ctx.fillStyle = 'rgba(0, 229, 255, 0.85)';
			ctx.font = 'bold 26px monospace';
			ctx.textAlign = 'center';
			ctx.fillText(waveBannerText, WIDTH / 2, HEIGHT / 2 - 20);
			ctx.textAlign = 'left';
		}
	}

	// Main Loop
	function gameLoop(now) {
		const dt = now - lastTime;
		lastTime = now;

		update(dt);
		render();

		requestAnimationFrame(gameLoop);
	}

	requestAnimationFrame(gameLoop);
});
