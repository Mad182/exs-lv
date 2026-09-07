/**
 * EXS.LV - Classic Arkanoid Game Engine
 * Features: Authentic maps, full capsule powerups (S, C, E, D, L, B, P),
 * Silver/Gold bricks, floating enemies, Web Audio sound synth, touch controls, highscores.
 */

(function () {
	'use strict';

	// ==========================================
	// 1. CONSTANTS & CONFIGURATION
	// ==========================================
	const CANVAS_WIDTH = 480;
	const CANVAS_HEIGHT = 600;

	const BRICK_COLS = 11;
	const BRICK_WIDTH = 38;
	const BRICK_HEIGHT = 16;
	const BRICK_PADDING = 4;
	const BRICK_OFFSET_TOP = 50;
	const BRICK_OFFSET_LEFT = 11; // (480 - (11 * 38 + 10 * 4)) / 2 = (480 - 458) / 2 = 11

	const VAUS_NORMAL_WIDTH = 76;
	const VAUS_EXPANDED_WIDTH = 114;
	const VAUS_HEIGHT = 14;
	const VAUS_Y = CANVAS_HEIGHT - 40;
	const VAUS_SPEED = 8;

	const BALL_RADIUS = 5;
	const BALL_INITIAL_SPEED = 5.2;
	const BALL_MAX_SPEED = 9.5;

	// Classic Arkanoid Brick Colors & Points
	const BRICK_TYPES = {
		'W': { name: 'White', points: 50, color: '#f8fafc', border: '#cbd5e1', hits: 1 },
		'O': { name: 'Orange', points: 60, color: '#f97316', border: '#ea580c', hits: 1 },
		'C': { name: 'Cyan', points: 70, color: '#06b6d4', border: '#0891b2', hits: 1 },
		'G': { name: 'Green', points: 80, color: '#22c55e', border: '#16a34a', hits: 1 },
		'R': { name: 'Red', points: 90, color: '#ef4444', border: '#dc2626', hits: 1 },
		'B': { name: 'Blue', points: 100, color: '#3b82f6', border: '#2563eb', hits: 1 },
		'P': { name: 'Pink', points: 110, color: '#ec4899', border: '#db2777', hits: 1 },
		'Y': { name: 'Yellow', points: 120, color: '#eab308', border: '#ca8a04', hits: 1 },
		'S': { name: 'Silver', points: 50, color: '#94a3b8', border: '#e2e8f0', hits: 2, silver: true },
		'X': { name: 'Gold', points: 0, color: '#d97706', border: '#fbbf24', hits: Infinity, gold: true }
	};

	// Power-up Types
	const POWERUP_TYPES = [
		{ type: 'S', name: 'Slow', color: '#f97316', text: 'S', weight: 18 },
		{ type: 'C', name: 'Catch', color: '#22c55e', text: 'C', weight: 16 },
		{ type: 'E', name: 'Expand', color: '#3b82f6', text: 'E', weight: 18 },
		{ type: 'D', name: 'Disruption', color: '#06b6d4', text: 'D', weight: 15 },
		{ type: 'L', name: 'Laser', color: '#ef4444', text: 'L', weight: 17 },
		{ type: 'B', name: 'Break', color: '#ec4899', text: 'B', weight: 8 },
		{ type: 'P', name: 'Player', color: '#94a3b8', text: 'P', weight: 8 }
	];

	// ==========================================
	// 2. 10 CLASSIC HANDCRAFTED MAPS
	// ==========================================
	const CLASSIC_MAPS = [
		// Round 1: Classic horizontal color tiers with silver top
		[
			"SSSSSSSSSSS",
			"RRRRRRRRRRR",
			"YYYYYYYYYYY",
			"BBBBBBBBBBB",
			"PPPPPPPPPPP",
			"GGGGGGGGGGG"
		],
		// Round 2: Stepped pyramids & gates
		[
			"...SS.SS...",
			"..RRR.RRR..",
			".YYYY.YYYY.",
			"BBBBB.BBBBB",
			".GGG...GGG.",
			"..OO...OO..",
			"...C...C..."
		],
		// Round 3: Twin fortress pillars with gold & silver
		[
			"XX.......XX",
			"SS..YYY..SS",
			"RR..YPY..RR",
			"GG..YYY..GG",
			"BB.......BB",
			"CC..SSS..CC",
			"XX..RRR..XX"
		],
		// Round 4: Concentric Diamond Formation
		[
			".....S.....",
			"....R.R....",
			"...Y...Y...",
			"..B..P..B..",
			".G..PCP..G.",
			"..B..P..B..",
			"...Y...Y...",
			"....R.R....",
			".....S....."
		],
		// Round 5: Space Invader Pixel Art Tribute!
		[
			"..G.....G..",
			"...G...G...",
			"..GGGGGGG..",
			".GG.GGG.GG.",
			"GGGGGGGGGGG",
			"G.GGGGGGG.G",
			"G.G.....G.G",
			"...GG.GG..."
		],
		// Round 6: Fortress Honeycomb / Maze
		[
			"R.R.R.R.R.R",
			"BXBXBXBXBXB",
			"Y.Y.Y.Y.Y.Y",
			"GSGSGSGSGSG",
			"P.P.P.P.P.P",
			"C.C.C.C.C.C"
		],
		// Round 7: Dual Hourglass Challenge
		[
			"YYYYY.YYYYY",
			".RRR...RRR.",
			"..G.....G..",
			".SSS...SSS.",
			"BBBBB.BBBBB",
			"..X.....X..",
			".PP.....PP."
		],
		// Round 8: Bunker with Narrow 1-Brick Entrance
		[
			"XXXXXXXXXXX",
			"XSSSSSSSSSX",
			"XS.......SX",
			"XS.PPPPP.SX",
			"XS.YYYYY.SX",
			"XS...R...SX",
			"XXXX.X.XXXX"
		],
		// Round 9: St. Andrew's Cross with Silver Anchors
		[
			"S.........S",
			".R.......R.",
			"..Y.....Y..",
			"...G...G...",
			"....B.B....",
			".....P.....",
			"....B.B....",
			"...G...G...",
			"..Y.....Y..",
			".R.......R.",
			"S.........S"
		],
		// Round 10: The Citadel / Grand Finale
		[
			"XX..SSS..XX",
			"XR..RRR..RX",
			"XG..GGG..GX",
			"XB..BBB..BX",
			"SS.YYYYY.SS",
			"..PPPPPPP..",
			"...CCCCC...",
			"....OOO...."
		]
	];

	// ==========================================
	// 3. WEB AUDIO SYNTHESIZER
	// ==========================================
	class SoundEngine {
		constructor() {
			this.ctx = null;
			this.enabled = true;
			try {
				const saved = localStorage.getItem('arkanoid_sound');
				if (saved !== null) {
					this.enabled = saved === '1';
				}
			} catch (e) { }
		}

		init() {
			if (!this.ctx) {
				const AudioCtx = window.AudioContext || window.webkitAudioContext;
				if (AudioCtx) {
					this.ctx = new AudioCtx();
				}
			}
			if (this.ctx && this.ctx.state === 'suspended') {
				this.ctx.resume();
			}
		}

		playTone(freq, type, duration, vol = 0.2, pitchShift = 0) {
			if (!this.enabled || !this.ctx) return;
			try {
				const osc = this.ctx.createOscillator();
				const gain = this.ctx.createGain();
				osc.type = type;
				osc.frequency.setValueAtTime(freq, this.ctx.currentTime);
				if (pitchShift !== 0) {
					osc.frequency.linearRampToValueAtTime(freq + pitchShift, this.ctx.currentTime + duration);
				}
				gain.gain.setValueAtTime(vol, this.ctx.currentTime);
				gain.gain.exponentialRampToValueAtTime(0.0001, this.ctx.currentTime + duration);
				osc.connect(gain);
				gain.connect(this.ctx.destination);
				osc.start();
				osc.stop(this.ctx.currentTime + duration);
			} catch (e) { }
		}

		paddleHit() {
			this.playTone(320, 'sine', 0.08, 0.25, 60);
		}

		wallHit() {
			this.playTone(240, 'sine', 0.05, 0.15, 20);
		}

		brickHit(colorKey) {
			const freqs = {
				'W': 440, 'O': 480, 'C': 520, 'G': 580,
				'R': 640, 'B': 700, 'P': 780, 'Y': 880,
				'S': 550, 'X': 300
			};
			const freq = freqs[colorKey] || 500;
			this.playTone(freq, 'square', 0.09, 0.18);
		}

		metalClink() {
			this.playTone(850, 'triangle', 0.06, 0.3, -200);
		}

		capsuleDrop() {
			this.playTone(700, 'sine', 0.12, 0.15, -300);
		}

		capsuleCollect() {
			if (!this.enabled || !this.ctx) return;
			const notes = [440, 554, 659, 880];
			notes.forEach((n, i) => {
				setTimeout(() => this.playTone(n, 'sine', 0.1, 0.2), i * 50);
			});
		}

		laserShoot() {
			this.playTone(880, 'sawtooth', 0.1, 0.2, -600);
		}

		lifeLost() {
			if (!this.enabled || !this.ctx) return;
			const notes = [400, 320, 260, 180, 100];
			notes.forEach((n, i) => {
				setTimeout(() => this.playTone(n, 'sawtooth', 0.12, 0.25, -40), i * 70);
			});
		}

		roundClear() {
			if (!this.enabled || !this.ctx) return;
			const fanfare = [523.25, 659.25, 783.99, 1046.5];
			fanfare.forEach((n, i) => {
				setTimeout(() => this.playTone(n, 'triangle', 0.2, 0.25), i * 90);
			});
		}

		gameOver() {
			if (!this.enabled || !this.ctx) return;
			const notes = [330, 293, 261, 220];
			notes.forEach((n, i) => {
				setTimeout(() => this.playTone(n, 'sine', 0.25, 0.25, -20), i * 160);
			});
		}

		toggle() {
			this.enabled = !this.enabled;
			try {
				localStorage.setItem('arkanoid_sound', this.enabled ? '1' : '0');
			} catch (e) { }
			return this.enabled;
		}
	}

	// ==========================================
	// 4. MAIN GAME CLASS
	// ==========================================
	class ArkanoidGame {
		constructor() {
			this.canvas = document.getElementById('arkanoid-canvas');
			if (!this.canvas) return;
			this.ctx = this.canvas.getContext('2d');

			this.sound = new SoundEngine();

			// Game State
			this.state = 'START'; // START, PLAYING, SERVING, PAUSED, GAMEOVER, VICTORY, ROUND_CLEAR
			this.score = 0;
			this.round = 1;
			this.lives = 3;
			this.totalBricksDestroyed = 0;
			this.sessionToken = null;
			this.startTime = 0;
			this.userBestScore = parseInt(document.getElementById('arkanoid-best-score')?.textContent) || 0;

			// Paddle (Vaus)
			this.vaus = {
				x: (CANVAS_WIDTH - VAUS_NORMAL_WIDTH) / 2,
				y: VAUS_Y,
				width: VAUS_NORMAL_WIDTH,
				height: VAUS_HEIGHT,
				targetX: (CANVAS_WIDTH - VAUS_NORMAL_WIDTH) / 2,
				color: '#e2e8f0',
				powerup: null, // null, 'L', 'C', 'E', 'S', 'D'
				isExpanded: false,
				hasLaser: false,
				hasCatch: false,
				lasersCooldown: 0
			};

			// Balls array
			this.balls = [];

			// Game entities
			this.bricks = [];
			this.capsules = [];
			this.lasers = [];
			this.particles = [];
			this.enemies = [];
			this.warpPortal = null;

			// Timers & counters
			this.enemySpawnTimer = 0;
			this.roundTransitionTimer = 0;

			// Input handling
			this.keys = { left: false, right: false, space: false };
			this.mouseControl = false;

			this.initDOM();
			this.initInput();
			this.initAntiCheatToken();
			this.loadRound(1);

			// Start Game Loop
			this.lastTimestamp = performance.now();
			requestAnimationFrame((t) => this.loop(t));
		}

		// ------------------------------------------
		// Anti-Cheat Session Token
		// ------------------------------------------
		initAntiCheatToken() {
			fetch('/arkanoid?action=init_token')
				.then(res => res.json())
				.then(data => {
					if (data && data.token) {
						this.sessionToken = data.token;
					}
				})
				.catch(err => {
					console.warn('Could not init arkanoid session token', err);
				});
		}

		// ------------------------------------------
		// DOM Elements & Event Listeners
		// ------------------------------------------
		initDOM() {
			this.dom = {
				score: document.getElementById('stat-score'),
				round: document.getElementById('stat-round'),
				lives: document.getElementById('stat-lives'),
				powerup: document.getElementById('stat-powerup'),
				soundBtn: document.getElementById('arkanoid-sound-btn'),
				pauseBtn: document.getElementById('arkanoid-pause-btn'),
				startOverlay: document.getElementById('arkanoid-start-overlay'),
				gameoverOverlay: document.getElementById('arkanoid-gameover-overlay'),
				victoryOverlay: document.getElementById('arkanoid-victory-overlay'),
				pauseOverlay: document.getElementById('arkanoid-pause-overlay'),
				startBtn: document.getElementById('arkanoid-start-btn'),
				restartBtn: document.getElementById('arkanoid-restart-btn'),
				continueBtn: document.getElementById('arkanoid-continue-btn'),
				resumeBtn: document.getElementById('arkanoid-resume-btn'),
				finalScore: document.getElementById('arkanoid-final-score'),
				finalRound: document.getElementById('arkanoid-final-round'),
				finalBricks: document.getElementById('arkanoid-final-bricks'),
				victoryScore: document.getElementById('arkanoid-victory-score'),
				recordAlert: document.getElementById('arkanoid-record-alert'),
				touchLeft: document.getElementById('touch-left'),
				touchRight: document.getElementById('touch-right'),
				touchFire: document.getElementById('touch-fire')
			};

			if (this.dom.soundBtn) {
				this.dom.soundBtn.textContent = this.sound.enabled ? '🔊' : '🔇';
				this.dom.soundBtn.addEventListener('click', () => {
					const on = this.sound.toggle();
					this.dom.soundBtn.textContent = on ? '🔊' : '🔇';
				});
			}

			if (this.dom.pauseBtn) {
				this.dom.pauseBtn.addEventListener('click', () => this.togglePause());
			}

			if (this.dom.startBtn) {
				this.dom.startBtn.addEventListener('click', () => this.startGame());
			}

			if (this.dom.restartBtn) {
				this.dom.restartBtn.addEventListener('click', () => this.restartGame());
			}

			if (this.dom.continueBtn) {
				this.dom.continueBtn.addEventListener('click', () => this.continueInfiniteMode());
			}

			if (this.dom.resumeBtn) {
				this.dom.resumeBtn.addEventListener('click', () => this.togglePause());
			}
		}

		// ------------------------------------------
		// Input & Touch Handling
		// ------------------------------------------
		initInput() {
			window.addEventListener('keydown', (e) => {
				if (e.code === 'ArrowLeft' || e.code === 'KeyA') {
					this.keys.left = true;
					this.mouseControl = false;
				} else if (e.code === 'ArrowRight' || e.code === 'KeyD') {
					this.keys.right = true;
					this.mouseControl = false;
				} else if (e.code === 'Space') {
					e.preventDefault();
					this.handleActionInput();
				} else if (e.code === 'KeyP') {
					this.togglePause();
				} else if (e.code === 'KeyM') {
					if (this.dom.soundBtn) this.dom.soundBtn.click();
				}
			});

			window.addEventListener('keyup', (e) => {
				if (e.code === 'ArrowLeft' || e.code === 'KeyA') this.keys.left = false;
				if (e.code === 'ArrowRight' || e.code === 'KeyD') this.keys.right = false;
				if (e.code === 'Space') this.keys.space = false;
			});

			// Canvas Mouse Control
			this.canvas.addEventListener('mousemove', (e) => {
				const rect = this.canvas.getBoundingClientRect();
				const scaleX = CANVAS_WIDTH / rect.width;
				const mouseX = (e.clientX - rect.left) * scaleX;
				this.vaus.targetX = mouseX - this.vaus.width / 2;
				this.mouseControl = true;
			});

			let lastActionTime = 0;
			const triggerAction = () => {
				const now = performance.now();
				if (now - lastActionTime < 120) return;
				lastActionTime = now;

				this.sound.init();
				if (this.state === 'START') {
					this.startGame();
				} else if (this.state === 'GAMEOVER') {
					this.restartGame();
				} else {
					this.handleActionInput();
				}
			};

			this.canvas.addEventListener('mousedown', (e) => {
				if (e.button === 0) {
					triggerAction();
				}
			});

			this.canvas.addEventListener('click', () => {
				triggerAction();
			});

			// Touch drag on canvas
			const handleTouchMove = (e) => {
				if (e.touches && e.touches[0]) {
					const rect = this.canvas.getBoundingClientRect();
					const scaleX = CANVAS_WIDTH / rect.width;
					const touchX = (e.touches[0].clientX - rect.left) * scaleX;
					this.vaus.targetX = touchX - this.vaus.width / 2;
					this.mouseControl = true;
					e.preventDefault();
				}
			};

			this.canvas.addEventListener('touchstart', (e) => {
				this.sound.init();
				handleTouchMove(e);
			}, { passive: false });

			this.canvas.addEventListener('touchmove', handleTouchMove, { passive: false });

			// Mobile On-Screen Buttons
			if (this.dom.touchLeft) {
				const startLeft = (e) => { e.preventDefault(); this.keys.left = true; this.mouseControl = false; };
				const endLeft = (e) => { e.preventDefault(); this.keys.left = false; };
				this.dom.touchLeft.addEventListener('mousedown', startLeft);
				this.dom.touchLeft.addEventListener('mouseup', endLeft);
				this.dom.touchLeft.addEventListener('touchstart', startLeft);
				this.dom.touchLeft.addEventListener('touchend', endLeft);
			}

			if (this.dom.touchRight) {
				const startRight = (e) => { e.preventDefault(); this.keys.right = true; this.mouseControl = false; };
				const endRight = (e) => { e.preventDefault(); this.keys.right = false; };
				this.dom.touchRight.addEventListener('mousedown', startRight);
				this.dom.touchRight.addEventListener('mouseup', endRight);
				this.dom.touchRight.addEventListener('touchstart', startRight);
				this.dom.touchRight.addEventListener('touchend', endRight);
			}

			if (this.dom.touchFire) {
				const fireAction = (e) => {
					e.preventDefault();
					this.sound.init();
					this.handleActionInput();
				};
				this.dom.touchFire.addEventListener('mousedown', fireAction);
				this.dom.touchFire.addEventListener('touchstart', fireAction);
			}
		}

		handleActionInput() {
			if (this.state === 'SERVING') {
				this.launchBall();
			} else if (this.state === 'PLAYING') {
				// Launch caught balls if any
				let caughtBall = false;
				for (const ball of this.balls) {
					if (ball.stuckToVaus) {
						this.releaseCaughtBall(ball);
						caughtBall = true;
					}
				}
				// Fire Lasers if armed
				if (!caughtBall && this.vaus.hasLaser && this.vaus.lasersCooldown <= 0) {
					this.fireLasers();
				}
			}
		}

		// ------------------------------------------
		// Round Loading
		// ------------------------------------------
		loadRound(roundNum) {
			this.round = roundNum;
			this.bricks = [];
			this.capsules = [];
			this.lasers = [];
			this.enemies = [];
			this.warpPortal = null;
			this.enemySpawnTimer = 8; // First enemy after 8s

			// Select map pattern
			const mapIndex = (roundNum - 1) % CLASSIC_MAPS.length;
			const mapData = CLASSIC_MAPS[mapIndex];

			// Silver brick durability scales with higher loops
			const silverHits = 2 + Math.floor((roundNum - 1) / 3);

			for (let r = 0; r < mapData.length; r++) {
				const rowStr = mapData[r];
				for (let c = 0; c < rowStr.length; c++) {
					const ch = rowStr[c];
					if (ch && ch !== '.') {
						const typeDef = BRICK_TYPES[ch];
						if (typeDef) {
							const hits = typeDef.silver ? silverHits : typeDef.hits;
							this.bricks.push({
								x: BRICK_OFFSET_LEFT + c * (BRICK_WIDTH + BRICK_PADDING),
								y: BRICK_OFFSET_TOP + r * (BRICK_HEIGHT + BRICK_PADDING),
								width: BRICK_WIDTH,
								height: BRICK_HEIGHT,
								type: ch,
								color: typeDef.color,
								border: typeDef.border,
								points: typeDef.points * (typeDef.silver ? roundNum : 1),
								maxHits: hits,
								hitsLeft: hits,
								silver: !!typeDef.silver,
								gold: !!typeDef.gold,
								flashTimer: 0
							});
						}
					}
				}
			}

			this.resetVausAndBall();
			this.updateHUD();
		}

		resetVausAndBall() {
			this.vaus.x = (CANVAS_WIDTH - this.vaus.width) / 2;
			this.vaus.targetX = this.vaus.x;
			this.vaus.lasersCooldown = 0;

			// Create primary ball stuck to paddle
			this.balls = [{
				x: this.vaus.x + this.vaus.width / 2,
				y: this.vaus.y - BALL_RADIUS - 1,
				radius: BALL_RADIUS,
				vx: 0,
				vy: 0,
				speed: BALL_INITIAL_SPEED + Math.min(2.5, (this.round - 1) * 0.3),
				stuckToVaus: true,
				stuckOffset: this.vaus.width / 2
			}];

			this.state = 'SERVING';
		}

		launchBall() {
			for (const ball of this.balls) {
				if (ball.stuckToVaus) {
					ball.stuckToVaus = false;
					ball.stuckTimer = 0;
					// Initial angle: slight random slant
					const angle = -Math.PI / 2 + (Math.random() * 0.4 - 0.2);
					ball.vx = Math.cos(angle) * ball.speed;
					ball.vy = Math.sin(angle) * ball.speed;
				}
			}
			this.state = 'PLAYING';
			this.sound.paddleHit();
		}

		releaseCaughtBall(ball) {
			if (!ball.stuckToVaus) return;
			ball.stuckToVaus = false;
			ball.stuckTimer = 0;

			// Angular deflection based on paddle position where ball was caught
			const hitOffset = (ball.stuckOffset - this.vaus.width / 2) / (this.vaus.width / 2);
			const clampedOffset = Math.max(-0.85, Math.min(0.85, hitOffset));
			let bounceAngle = clampedOffset * (Math.PI / 3); // Max 60 deg

			// Avoid purely vertical trajectory to prevent dead loops
			if (Math.abs(bounceAngle) < 0.1) {
				bounceAngle = (Math.random() > 0.5 ? 1 : -1) * 0.15;
			}

			const speed = ball.speed || BALL_INITIAL_SPEED;
			ball.vx = speed * Math.sin(bounceAngle);
			ball.vy = -speed * Math.cos(bounceAngle);
			ball.y = this.vaus.y - ball.radius - 2;
			this.sound.paddleHit();
		}

		// ------------------------------------------
		// Game Control
		// ------------------------------------------
		startGame() {
			this.sound.init();
			this.score = 0;
			this.round = 1;
			this.lives = 3;
			this.totalBricksDestroyed = 0;
			this.startTime = Date.now();
			this.resetPowerup();
			this.loadRound(1);
			this.hideOverlays();
			this.updateHUD();
		}

		restartGame() {
			this.initAntiCheatToken();
			this.startGame();
		}

		continueInfiniteMode() {
			this.hideOverlays();
			this.loadRound(this.round + 1);
		}

		togglePause() {
			if (this.state === 'PLAYING' || this.state === 'SERVING') {
				this.prevState = this.state;
				this.state = 'PAUSED';
				if (this.dom.pauseOverlay) this.dom.pauseOverlay.style.display = 'flex';
			} else if (this.state === 'PAUSED') {
				this.state = this.prevState || 'PLAYING';
				if (this.dom.pauseOverlay) this.dom.pauseOverlay.style.display = 'none';
			}
		}

		hideOverlays() {
			if (this.dom.startOverlay) this.dom.startOverlay.style.display = 'none';
			if (this.dom.gameoverOverlay) this.dom.gameoverOverlay.style.display = 'none';
			if (this.dom.victoryOverlay) this.dom.victoryOverlay.style.display = 'none';
			if (this.dom.pauseOverlay) this.dom.pauseOverlay.style.display = 'none';
		}

		// ------------------------------------------
		// Power-up Management
		// ------------------------------------------
		spawnCapsule(x, y) {
			// ~18% chance to drop a capsule
			if (Math.random() > 0.18) return;

			// Weighted selection
			const totalWeight = POWERUP_TYPES.reduce((acc, p) => acc + p.weight, 0);
			let rand = Math.random() * totalWeight;
			let chosen = POWERUP_TYPES[0];
			for (const p of POWERUP_TYPES) {
				if (rand < p.weight) {
					chosen = p;
					break;
				}
				rand -= p.weight;
			}

			this.capsules.push({
				x: x,
				y: y,
				width: 32,
				height: 14,
				vy: 2.2,
				type: chosen.type,
				color: chosen.color,
				text: chosen.text,
				angle: 0
			});

			this.sound.capsuleDrop();
		}

		applyPowerup(type) {
			this.sound.capsuleCollect();
			this.addScore(1000);

			// Cancel mutually exclusive capabilities
			this.resetPowerup();
			this.vaus.powerup = type;

			switch (type) {
				case 'S': // Slow
					for (const b of this.balls) {
						b.speed = BALL_INITIAL_SPEED;
						const curMag = Math.hypot(b.vx, b.vy);
						if (curMag > 0) {
							b.vx = (b.vx / curMag) * b.speed;
							b.vy = (b.vy / curMag) * b.speed;
						}
					}
					break;

				case 'C': // Catch
					this.vaus.hasCatch = true;
					break;

				case 'E': // Expand
					this.vaus.isExpanded = true;
					this.vaus.width = VAUS_EXPANDED_WIDTH;
					break;

				case 'D': // Disruption (Multi-ball)
					const newBalls = [];
					for (const b of this.balls) {
						// Spawn 2 new balls from this ball at +/- 30 degrees
						const angle = Math.atan2(b.vy, b.vx);
						const speed = b.speed;
						newBalls.push({
							x: b.x,
							y: b.y,
							radius: BALL_RADIUS,
							vx: Math.cos(angle + 0.5) * speed,
							vy: Math.sin(angle + 0.5) * speed,
							speed: speed,
							stuckToVaus: false,
							stuckOffset: 0
						});
						newBalls.push({
							x: b.x,
							y: b.y,
							radius: BALL_RADIUS,
							vx: Math.cos(angle - 0.5) * speed,
							vy: Math.sin(angle - 0.5) * speed,
							speed: speed,
							stuckToVaus: false,
							stuckOffset: 0
						});
					}
					this.balls.push(...newBalls);
					break;

				case 'L': // Laser
					this.vaus.hasLaser = true;
					break;

				case 'B': // Break / Warp
					this.openWarpPortal();
					break;

				case 'P': // Player (+1 Life)
					this.lives++;
					this.sound.playTone(880, 'sine', 0.25, 0.3);
					break;
			}

			this.updateHUD();
		}

		resetPowerup() {
			this.vaus.powerup = null;
			this.vaus.hasLaser = false;
			this.vaus.hasCatch = false;
			this.vaus.isExpanded = false;
			this.vaus.width = VAUS_NORMAL_WIDTH;
			this.warpPortal = null;
			if (this.state === 'PLAYING' && this.balls) {
				for (const ball of this.balls) {
					if (ball.stuckToVaus) {
						this.releaseCaughtBall(ball);
					}
				}
			}
			this.updateHUD();
		}

		openWarpPortal() {
			this.warpPortal = {
				x: CANVAS_WIDTH - 12,
				y: VAUS_Y - 20,
				width: 12,
				height: 50,
				animPulse: 0
			};
		}

		fireLasers() {
			this.vaus.lasersCooldown = 0.25; // seconds
			this.lasers.push({
				x: this.vaus.x + 8,
				y: this.vaus.y - 4,
				width: 3,
				height: 12,
				vy: -11
			});
			this.lasers.push({
				x: this.vaus.x + this.vaus.width - 11,
				y: this.vaus.y - 4,
				width: 3,
				height: 12,
				vy: -11
			});
			this.sound.laserShoot();
		}

		// ------------------------------------------
		// Enemies Spawner
		// ------------------------------------------
		spawnEnemy() {
			if (this.enemies.length >= 3) return;
			const side = Math.random() < 0.5 ? 'left' : 'right';
			const startX = side === 'left' ? 40 : CANVAS_WIDTH - 40;
			this.enemies.push({
				x: startX,
				y: 20,
				radius: 12,
				baseX: startX,
				time: 0,
				vy: 0.8 + Math.random() * 0.5,
				type: Math.floor(Math.random() * 3), // 0: sphere, 1: cube, 2: pyramid
				hp: 1
			});
		}

		// ------------------------------------------
		// Score & Life Handling
		// ------------------------------------------
		addScore(pts) {
			this.score += pts;
			if (this.dom.score) {
				this.dom.score.textContent = this.score.toLocaleString('lv-LV');
			}
		}

		loseLife() {
			this.lives--;
			this.sound.lifeLost();
			this.resetPowerup();

			if (this.lives <= 0) {
				this.gameOver();
			} else {
				this.resetVausAndBall();
				this.updateHUD();
			}
		}

		checkRoundClear() {
			// Round is clear if all destructible bricks (not Gold) are destroyed
			const remainingDestructible = this.bricks.filter(b => !b.gold);
			if (remainingDestructible.length === 0) {
				this.clearRound();
			}
		}

		clearRound(warpBonus = 0) {
			this.state = 'ROUND_CLEAR';
			this.sound.roundClear();
			if (warpBonus > 0) {
				this.addScore(warpBonus);
			}

			// End of classic 10 maps?
			if (this.round === 10) {
				setTimeout(() => {
					this.state = 'VICTORY';
					if (this.dom.victoryScore) this.dom.victoryScore.textContent = this.score.toLocaleString('lv-LV');
					if (this.dom.victoryOverlay) this.dom.victoryOverlay.style.display = 'flex';
				}, 1000);
			} else {
				setTimeout(() => {
					this.loadRound(this.round + 1);
				}, 1200);
			}
		}

		gameOver() {
			this.state = 'GAMEOVER';
			this.sound.gameOver();

			const duration = Math.max(1, Math.floor((Date.now() - this.startTime) / 1000));

			if (this.dom.finalScore) this.dom.finalScore.textContent = this.score.toLocaleString('lv-LV');
			if (this.dom.finalRound) this.dom.finalRound.textContent = this.round;
			if (this.dom.finalBricks) this.dom.finalBricks.textContent = this.totalBricksDestroyed;

			if (this.dom.gameoverOverlay) this.dom.gameoverOverlay.style.display = 'flex';

			// Submit score to backend
			this.submitScore(this.score, this.round, duration);
		}

		submitScore(score, round, duration) {
			if (!this.sessionToken) return;

			const params = new URLSearchParams();
			params.append('token', this.sessionToken);
			params.append('score', score);
			params.append('round', round);
			params.append('duration', duration);

			fetch('/arkanoid?action=push', {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: params.toString()
			})
				.then(res => res.json())
				.then(data => {
					if (data && data.success) {
						if (data.isNewRecord && this.dom.recordAlert) {
							this.dom.recordAlert.style.display = 'block';
						}
						if (data.highScore && this.dom.bestScore) {
							this.dom.bestScore.textContent = data.highScore.toLocaleString('lv-LV');
						}
					}
				})
				.catch(err => console.warn('Score submission error', err));
		}

		updateHUD() {
			if (this.dom.score) this.dom.score.textContent = this.score.toLocaleString('lv-LV');
			if (this.dom.round) this.dom.round.textContent = this.round;

			if (this.dom.lives) {
				let html = '';
				for (let i = 0; i < this.lives; i++) {
					html += '<span class="vaus-life-icon"></span>';
				}
				this.dom.lives.innerHTML = html;
			}

			if (this.dom.powerup) {
				const mapClass = {
					'L': { cls: 'active-laser', txt: 'LĀZERS' },
					'C': { cls: 'active-catch', txt: 'PIELIPT' },
					'E': { cls: 'active-expand', txt: 'PLATĀKS' },
					'S': { cls: 'active-slow', txt: 'LĒNĀK' },
					'D': { cls: 'active-disrupt', txt: '3 BUMBAS' },
					'B': { cls: 'active-laser', txt: 'PORTĀLS' },
					'P': { cls: 'active-catch', txt: '+1 DZĪVĪBA' }
				};
				const cur = mapClass[this.vaus.powerup];
				if (cur) {
					this.dom.powerup.className = 'hud-powerup-badge ' + cur.cls;
					this.dom.powerup.textContent = cur.txt;
				} else {
					this.dom.powerup.className = 'hud-powerup-badge none';
					this.dom.powerup.textContent = 'NAV';
				}
			}
		}

		// ------------------------------------------
		// Particles
		// ------------------------------------------
		createBrickParticles(x, y, color) {
			for (let i = 0; i < 8; i++) {
				const angle = Math.random() * Math.PI * 2;
				const speed = 1 + Math.random() * 3;
				this.particles.push({
					x: x + BRICK_WIDTH / 2,
					y: y + BRICK_HEIGHT / 2,
					vx: Math.cos(angle) * speed,
					vy: Math.sin(angle) * speed,
					size: 2 + Math.random() * 3,
					color: color,
					alpha: 1,
					decay: 0.03 + Math.random() * 0.02
				});
			}
		}

		// ------------------------------------------
		// Main Game Loop
		// ------------------------------------------
		loop(timestamp) {
			const dt = Math.min(0.05, (timestamp - this.lastTimestamp) / 1000);
			this.lastTimestamp = timestamp;

			this.update(dt);
			this.render();

			requestAnimationFrame((t) => this.loop(t));
		}

		// ------------------------------------------
		// Update Logic
		// ------------------------------------------
		update(dt) {
			if (this.state !== 'PLAYING' && this.state !== 'SERVING') return;

			// Vaus Movement
			if (this.mouseControl) {
				// Smooth interpolation to targetX
				this.vaus.x += (this.vaus.targetX - this.vaus.x) * 0.4;
			} else {
				if (this.keys.left) this.vaus.x -= VAUS_SPEED;
				if (this.keys.right) this.vaus.x += VAUS_SPEED;
			}
			// Clamp Vaus to screen bounds
			const maxX = this.warpPortal ? CANVAS_WIDTH : CANVAS_WIDTH - this.vaus.width;
			this.vaus.x = Math.max(0, Math.min(maxX, this.vaus.x));

			// Laser cooldown
			if (this.vaus.lasersCooldown > 0) {
				this.vaus.lasersCooldown -= dt;
			}

			// Check Warp Portal entry
			if (this.warpPortal && (this.vaus.x + this.vaus.width >= CANVAS_WIDTH - 4)) {
				this.clearRound(10000);
				return;
			}

			// Update Balls
			for (let i = this.balls.length - 1; i >= 0; i--) {
				const ball = this.balls[i];

				if (ball.stuckToVaus) {
					ball.x = this.vaus.x + ball.stuckOffset;
					ball.y = this.vaus.y - ball.radius - 1;
					ball.stuckTimer = (ball.stuckTimer || 0) + dt;
					// Auto release after 4.5 seconds if player hasn't launched it
					if (this.state === 'PLAYING' && ball.stuckTimer >= 4.5) {
						this.releaseCaughtBall(ball);
					}
					continue;
				}

				// Move ball
				ball.x += ball.vx;
				ball.y += ball.vy;

				// Wall collisions
				if (ball.x - ball.radius <= 0) {
					ball.x = ball.radius;
					ball.vx = Math.abs(ball.vx);
					this.sound.wallHit();
				} else if (ball.x + ball.radius >= CANVAS_WIDTH) {
					ball.x = CANVAS_WIDTH - ball.radius;
					ball.vx = -Math.abs(ball.vx);
					this.sound.wallHit();
				}

				if (ball.y - ball.radius <= 0) {
					ball.y = ball.radius;
					ball.vy = Math.abs(ball.vy);
					this.sound.wallHit();
				}

				// Paddle Collision
				if (ball.vy > 0 &&
					ball.y + ball.radius >= this.vaus.y &&
					ball.y - ball.radius <= this.vaus.y + this.vaus.height &&
					ball.x >= this.vaus.x - ball.radius &&
					ball.x <= this.vaus.x + this.vaus.width + ball.radius) {

					if (this.vaus.hasCatch) {
						ball.stuckToVaus = true;
						ball.stuckOffset = Math.max(ball.radius + 2, Math.min(this.vaus.width - ball.radius - 2, ball.x - this.vaus.x));
						ball.stuckTimer = 0;
						ball.speed = Math.min(BALL_MAX_SPEED, (ball.speed || BALL_INITIAL_SPEED) + 0.05);
						ball.vy = 0;
						ball.vx = 0;
						this.sound.paddleHit();
					} else {
						// Angular deflection based on hit position
						const hitOffset = (ball.x - (this.vaus.x + this.vaus.width / 2)) / (this.vaus.width / 2);
						const clampedOffset = Math.max(-0.9, Math.min(0.9, hitOffset));
						const bounceAngle = clampedOffset * (Math.PI / 3); // Max 60 deg

						ball.speed = Math.min(BALL_MAX_SPEED, ball.speed + 0.05);
						ball.vx = ball.speed * Math.sin(bounceAngle);
						ball.vy = -ball.speed * Math.cos(bounceAngle);
						ball.y = this.vaus.y - ball.radius - 1;
						this.sound.paddleHit();
					}
				}

				// Brick Collisions
				for (let j = 0; j < this.bricks.length; j++) {
					const brick = this.bricks[j];

					// Check circle-AABB collision
					const nearestX = Math.max(brick.x, Math.min(ball.x, brick.x + brick.width));
					const nearestY = Math.max(brick.y, Math.min(ball.y, brick.y + brick.height));
					const distX = ball.x - nearestX;
					const distY = ball.y - nearestY;

					if ((distX * distX + distY * distY) <= (ball.radius * ball.radius)) {
						// Determine collision axis
						const prevX = ball.x - ball.vx;
						const prevY = ball.y - ball.vy;

						if (prevX + ball.radius <= brick.x || prevX - ball.radius >= brick.x + brick.width) {
							ball.vx = -ball.vx;
						} else {
							ball.vy = -ball.vy;
						}

						// Hit brick
						if (brick.gold) {
							this.sound.metalClink();
							brick.flashTimer = 0.1;
						} else {
							brick.hitsLeft--;
							brick.flashTimer = 0.1;

							if (brick.hitsLeft <= 0) {
								this.sound.brickHit(brick.type);
								this.addScore(brick.points);
								this.totalBricksDestroyed++;
								this.createBrickParticles(brick.x, brick.y, brick.color);
								this.spawnCapsule(brick.x + brick.width / 2, brick.y + brick.height / 2);
								this.bricks.splice(j, 1);
							} else {
								this.sound.metalClink();
								this.addScore(50);
							}
						}
						break;
					}
				}

				// Enemy Collisions with Ball
				for (let eIdx = this.enemies.length - 1; eIdx >= 0; eIdx--) {
					const enemy = this.enemies[eIdx];
					const dx = ball.x - enemy.x;
					const dy = ball.y - enemy.y;
					if (Math.hypot(dx, dy) < ball.radius + enemy.radius) {
						ball.vy = -ball.vy;
						this.addScore(100);
						this.createBrickParticles(enemy.x - 10, enemy.y - 10, '#ec4899');
						this.sound.brickHit('P');
						this.enemies.splice(eIdx, 1);
					}
				}

				// Ball fell below screen
				if (ball.y - ball.radius > CANVAS_HEIGHT) {
					this.balls.splice(i, 1);
				}
			}

			// If no balls remaining, lose a life
			if (this.balls.length === 0 && this.state === 'PLAYING') {
				this.loseLife();
			}

			// Update Capsules
			for (let i = this.capsules.length - 1; i >= 0; i--) {
				const cap = this.capsules[i];
				cap.y += cap.vy;
				cap.angle += 0.08;

				// Paddle collection check
				if (cap.y + cap.height >= this.vaus.y &&
					cap.y <= this.vaus.y + this.vaus.height &&
					cap.x + cap.width >= this.vaus.x &&
					cap.x <= this.vaus.x + this.vaus.width) {
					this.applyPowerup(cap.type);
					this.capsules.splice(i, 1);
					continue;
				}

				// Off bottom
				if (cap.y > CANVAS_HEIGHT) {
					this.capsules.splice(i, 1);
				}
			}

			// Update Lasers
			for (let i = this.lasers.length - 1; i >= 0; i--) {
				const lz = this.lasers[i];
				lz.y += lz.vy;

				let hit = false;
				for (let j = this.bricks.length - 1; j >= 0; j--) {
					const brick = this.bricks[j];
					if (lz.x >= brick.x && lz.x <= brick.x + brick.width &&
						lz.y >= brick.y && lz.y <= brick.y + brick.height) {

						hit = true;
						if (!brick.gold) {
							brick.hitsLeft--;
							brick.flashTimer = 0.1;
							if (brick.hitsLeft <= 0) {
								this.sound.brickHit(brick.type);
								this.addScore(brick.points);
								this.totalBricksDestroyed++;
								this.createBrickParticles(brick.x, brick.y, brick.color);
								this.spawnCapsule(brick.x + brick.width / 2, brick.y + brick.height / 2);
								this.bricks.splice(j, 1);
							} else {
								this.sound.metalClink();
							}
						} else {
							this.sound.metalClink();
						}
						break;
					}
				}

				// Laser hits enemy
				if (!hit) {
					for (let eIdx = this.enemies.length - 1; eIdx >= 0; eIdx--) {
						const enemy = this.enemies[eIdx];
						if (Math.hypot(lz.x - enemy.x, lz.y - enemy.y) < enemy.radius + 2) {
							hit = true;
							this.addScore(100);
							this.createBrickParticles(enemy.x - 10, enemy.y - 10, '#ec4899');
							this.sound.brickHit('P');
							this.enemies.splice(eIdx, 1);
							break;
						}
					}
				}

				if (hit || lz.y < 0) {
					this.lasers.splice(i, 1);
				}
			}

			// Update Enemies
			this.enemySpawnTimer -= dt;
			if (this.enemySpawnTimer <= 0) {
				this.spawnEnemy();
				this.enemySpawnTimer = 10 + Math.random() * 8;
			}

			for (let i = this.enemies.length - 1; i >= 0; i--) {
				const en = this.enemies[i];
				en.time += dt;
				en.y += en.vy;
				en.x = en.baseX + Math.sin(en.time * 2.5) * 35;

				// Touch paddle check
				if (en.y + en.radius >= this.vaus.y &&
					en.y - en.radius <= this.vaus.y + this.vaus.height &&
					en.x + en.radius >= this.vaus.x &&
					en.x - en.radius <= this.vaus.x + this.vaus.width) {
					this.createBrickParticles(en.x, en.y, '#ef4444');
					this.enemies.splice(i, 1);
					this.loseLife();
					continue;
				}

				if (en.y - en.radius > CANVAS_HEIGHT) {
					this.enemies.splice(i, 1);
				}
			}

			// Update Particles
			for (let i = this.particles.length - 1; i >= 0; i--) {
				const p = this.particles[i];
				p.x += p.vx;
				p.y += p.vy;
				p.alpha -= p.decay;
				if (p.alpha <= 0) {
					this.particles.splice(i, 1);
				}
			}

			// Flash timers for bricks
			for (const b of this.bricks) {
				if (b.flashTimer > 0) b.flashTimer -= dt;
			}

			// Check Level Clear condition
			this.checkRoundClear();
		}

		// ------------------------------------------
		// Render Logic
		// ------------------------------------------
		render() {
			const ctx = this.ctx;
			ctx.clearRect(0, 0, CANVAS_WIDTH, CANVAS_HEIGHT);

			// Background subtle grid
			ctx.strokeStyle = 'rgba(30, 41, 59, 0.4)';
			ctx.lineWidth = 1;
			for (let x = 0; x < CANVAS_WIDTH; x += 40) {
				ctx.beginPath();
				ctx.moveTo(x, 0);
				ctx.lineTo(x, CANVAS_HEIGHT);
				ctx.stroke();
			}

			// Render Bricks
			for (const brick of this.bricks) {
				this.renderBrick(brick);
			}

			// Render Warp Portal
			if (this.warpPortal) {
				this.renderWarpPortal();
			}

			// Render Vaus Paddle
			this.renderVaus();

			// Render Lasers
			for (const lz of this.lasers) {
				ctx.fillStyle = '#ef4444';
				ctx.shadowColor = '#ef4444';
				ctx.shadowBlur = 8;
				ctx.fillRect(lz.x, lz.y, lz.width, lz.height);
				ctx.shadowBlur = 0;
			}

			// Render Capsules
			for (const cap of this.capsules) {
				this.renderCapsule(cap);
			}

			// Render Enemies
			for (const en of this.enemies) {
				this.renderEnemy(en);
			}

			// Render Balls
			for (const ball of this.balls) {
				ctx.save();
				ctx.beginPath();
				ctx.arc(ball.x, ball.y, ball.radius, 0, Math.PI * 2);
				ctx.fillStyle = '#ffffff';
				ctx.shadowColor = '#38bdf8';
				ctx.shadowBlur = 10;
				ctx.fill();

				// Inner soft cyan core
				ctx.beginPath();
				ctx.arc(ball.x - 1, ball.y - 1, ball.radius * 0.4, 0, Math.PI * 2);
				ctx.fillStyle = '#e0f2fe';
				ctx.fill();
				ctx.restore();
			}

			// Render Particles
			for (const p of this.particles) {
				ctx.save();
				ctx.globalAlpha = p.alpha;
				ctx.fillStyle = p.color;
				ctx.fillRect(p.x, p.y, p.size, p.size);
				ctx.restore();
			}
		}

		renderBrick(brick) {
			const ctx = this.ctx;
			const isFlashing = brick.flashTimer > 0;

			ctx.save();
			// Base shape
			ctx.fillStyle = isFlashing ? '#ffffff' : brick.color;
			ctx.fillRect(brick.x, brick.y, brick.width, brick.height);

			// Metallic / Bevel borders
			if (!isFlashing) {
				// Top & Left Highlight
				ctx.fillStyle = brick.gold ? 'rgba(255, 255, 255, 0.6)' : 'rgba(255, 255, 255, 0.45)';
				ctx.beginPath();
				ctx.moveTo(brick.x, brick.y + brick.height);
				ctx.lineTo(brick.x, brick.y);
				ctx.lineTo(brick.x + brick.width, brick.y);
				ctx.lineTo(brick.x + brick.width - 2, brick.y + 2);
				ctx.lineTo(brick.x + 2, brick.y + 2);
				ctx.lineTo(brick.x + 2, brick.y + brick.height - 2);
				ctx.closePath();
				ctx.fill();

				// Bottom & Right Shadow
				ctx.fillStyle = 'rgba(0, 0, 0, 0.4)';
				ctx.beginPath();
				ctx.moveTo(brick.x + brick.width, brick.y);
				ctx.lineTo(brick.x + brick.width, brick.y + brick.height);
				ctx.lineTo(brick.x, brick.y + brick.height);
				ctx.lineTo(brick.x + 2, brick.y + brick.height - 2);
				ctx.lineTo(brick.x + brick.width - 2, brick.y + brick.height - 2);
				ctx.lineTo(brick.x + brick.width - 2, brick.y + 2);
				ctx.closePath();
				ctx.fill();

				// Silver/Gold metallic center pattern
				if (brick.silver || brick.gold) {
					ctx.fillStyle = brick.gold ? 'rgba(254, 240, 138, 0.5)' : 'rgba(255, 255, 255, 0.4)';
					ctx.fillRect(brick.x + 6, brick.y + 4, brick.width - 12, brick.height - 8);
				}
			}
			ctx.restore();
		}

		renderVaus() {
			const ctx = this.ctx;
			const v = this.vaus;

			ctx.save();
			// Main Vaus body - Metallic Chrome with rounded ends
			const radius = v.height / 2;
			ctx.beginPath();
			ctx.moveTo(v.x + radius, v.y);
			ctx.lineTo(v.x + v.width - radius, v.y);
			ctx.arc(v.x + v.width - radius, v.y + radius, radius, -Math.PI / 2, Math.PI / 2);
			ctx.lineTo(v.x + radius, v.y + v.height);
			ctx.arc(v.x + radius, v.y + radius, radius, Math.PI / 2, -Math.PI / 2);
			ctx.closePath();

			const grad = ctx.createLinearGradient(v.x, v.y, v.x, v.y + v.height);
			grad.addColorStop(0, '#f8fafc');
			grad.addColorStop(0.3, '#cbd5e1');
			grad.addColorStop(0.7, '#64748b');
			grad.addColorStop(1, '#334155');
			ctx.fillStyle = grad;
			ctx.fill();

			// Red energy capsule in center
			const coreW = v.width * 0.5;
			const coreX = v.x + (v.width - coreW) / 2;
			ctx.fillStyle = v.hasLaser ? '#ef4444' : (v.hasCatch ? '#22c55e' : '#e11d48');
			ctx.fillRect(coreX, v.y + 3, coreW, v.height - 6);

			// Laser Cannons if equipped
			if (v.hasLaser) {
				ctx.fillStyle = '#ef4444';
				ctx.fillRect(v.x + 4, v.y - 5, 4, 6);
				ctx.fillRect(v.x + v.width - 8, v.y - 5, 4, 6);
			}

			// Thruster glow on sides
			ctx.fillStyle = '#38bdf8';
			ctx.beginPath();
			ctx.arc(v.x + 3, v.y + radius, 2, 0, Math.PI * 2);
			ctx.arc(v.x + v.width - 3, v.y + radius, 2, 0, Math.PI * 2);
			ctx.fill();

			ctx.restore();
		}

		renderCapsule(cap) {
			const ctx = this.ctx;
			ctx.save();
			ctx.translate(cap.x, cap.y);

			// Capsule pill appearance (rounded rectangle)
			const r = cap.height / 2;
			ctx.beginPath();
			ctx.moveTo(r, 0);
			ctx.lineTo(cap.width - r, 0);
			ctx.arc(cap.width - r, r, r, -Math.PI / 2, Math.PI / 2);
			ctx.lineTo(r, cap.height);
			ctx.arc(r, r, r, Math.PI / 2, -Math.PI / 2);
			ctx.closePath();

			ctx.fillStyle = cap.color;
			ctx.shadowColor = cap.color;
			ctx.shadowBlur = 6;
			ctx.fill();

			// Pill Letter
			ctx.shadowBlur = 0;
			ctx.fillStyle = '#ffffff';
			ctx.font = 'bold 10px monospace';
			ctx.textAlign = 'center';
			ctx.textBaseline = 'middle';
			ctx.fillText(cap.text, cap.width / 2, cap.height / 2);

			ctx.restore();
		}

		renderEnemy(en) {
			const ctx = this.ctx;
			ctx.save();
			ctx.translate(en.x, en.y);

			ctx.fillStyle = '#f43f5e';
			ctx.strokeStyle = '#fda4af';
			ctx.lineWidth = 1.5;

			if (en.type === 0) {
				// Floating sphere with concentric geometric rings
				ctx.beginPath();
				ctx.arc(0, 0, en.radius, 0, Math.PI * 2);
				ctx.fill();
				ctx.stroke();

				ctx.fillStyle = '#ffe4e6';
				ctx.beginPath();
				ctx.arc(0, 0, en.radius * 0.4, 0, Math.PI * 2);
				ctx.fill();
			} else if (en.type === 1) {
				// Floating geometric cube / diamond
				ctx.rotate(en.time * 2);
				ctx.beginPath();
				ctx.rect(-en.radius, -en.radius, en.radius * 2, en.radius * 2);
				ctx.fill();
				ctx.stroke();
			} else {
				// Floating pyramid
				ctx.rotate(Math.sin(en.time * 2) * 0.4);
				ctx.beginPath();
				ctx.moveTo(0, -en.radius);
				ctx.lineTo(en.radius, en.radius);
				ctx.lineTo(-en.radius, en.radius);
				ctx.closePath();
				ctx.fill();
				ctx.stroke();
			}

			ctx.restore();
		}

		renderWarpPortal() {
			const ctx = this.ctx;
			const wp = this.warpPortal;
			wp.animPulse += 0.05;

			ctx.save();
			const grad = ctx.createLinearGradient(wp.x, wp.y, wp.x + wp.width, wp.y + wp.height);
			grad.addColorStop(0, '#ec4899');
			grad.addColorStop(0.5, '#38bdf8');
			grad.addColorStop(1, '#a855f7');

			ctx.fillStyle = grad;
			ctx.shadowColor = '#ec4899';
			ctx.shadowBlur = 12 + Math.sin(wp.animPulse) * 4;
			ctx.fillRect(wp.x, wp.y, wp.width, wp.height);

			// Portal arrow pointing right
			ctx.fillStyle = '#ffffff';
			ctx.beginPath();
			ctx.moveTo(wp.x + 2, wp.y + wp.height / 2 - 6);
			ctx.lineTo(wp.x + wp.width - 2, wp.y + wp.height / 2);
			ctx.lineTo(wp.x + 2, wp.y + wp.height / 2 + 6);
			ctx.fill();
			ctx.restore();
		}
	}

	// ==========================================
	// 5. INITIALIZE ON DOM READY
	// ==========================================
	document.addEventListener('DOMContentLoaded', () => {
		new ArkanoidGame();
	});

})();
