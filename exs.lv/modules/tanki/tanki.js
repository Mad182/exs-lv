/**
 * EXS.LV - Tanki 1990 (Battle City) Game Engine
 * HTML5 Canvas & Web Audio API Implementation
 */

(function () {
	'use strict';

	// Constants
	var TILE_SIZE = 16;       // Each sub-tile is 16x16 px
	var GRID_COUNT = 26;      // 26x26 sub-tiles = 416x416 px
	var CANVAS_SIZE = 416;

	// Tile Types
	var TILE = {
		EMPTY: 0,
		BRICK: 1,
		STEEL: 2,
		TREES: 3,
		WATER: 4,
		ICE: 5,
		BASE: 9,
		BASE_DEAD: 10
	};

	// Directions: 0=UP, 1=RIGHT, 2=DOWN, 3=LEFT
	var DIR = {
		UP: 0,
		RIGHT: 1,
		DOWN: 2,
		LEFT: 3
	};

	var DIR_VECTORS = [
		{ x: 0, y: -1 },
		{ x: 1, y: 0 },
		{ x: 0, y: 1 },
		{ x: -1, y: 0 }
	];

	// Audio Synthesizer via Web Audio API
	var SoundEngine = (function () {
		var audioCtx = null;
		var soundEnabled = true;

		function init() {
			if (!audioCtx) {
				var AudioContext = window.AudioContext || window.webkitAudioContext;
				if (AudioContext) {
					audioCtx = new AudioContext();
				}
			}
			if (audioCtx && audioCtx.state === 'suspended') {
				audioCtx.resume();
			}
		}

		function playTone(freq, type, duration, vol, slideToFreq) {
			if (!soundEnabled || !audioCtx) return;
			try {
				var osc = audioCtx.createOscillator();
				var gain = audioCtx.createGain();
				osc.type = type || 'square';
				osc.frequency.setValueAtTime(freq, audioCtx.currentTime);
				if (slideToFreq) {
					osc.frequency.exponentialRampToValueAtTime(slideToFreq, audioCtx.currentTime + duration);
				}
				gain.gain.setValueAtTime(vol || 0.15, audioCtx.currentTime);
				gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + duration);
				osc.connect(gain);
				gain.connect(audioCtx.destination);
				osc.start();
				osc.stop(audioCtx.currentTime + duration);
			} catch (e) {}
		}

		function playNoise(duration, vol) {
			if (!soundEnabled || !audioCtx) return;
			try {
				var bufferSize = audioCtx.sampleRate * duration;
				var buffer = audioCtx.createBuffer(1, bufferSize, audioCtx.sampleRate);
				var data = buffer.getChannelData(0);
				for (var i = 0; i < bufferSize; i++) {
					data[i] = Math.random() * 2 - 1;
				}
				var noise = audioCtx.createBufferSource();
				noise.buffer = buffer;
				var filter = audioCtx.createBiquadFilter();
				filter.type = 'lowpass';
				filter.frequency.setValueAtTime(800, audioCtx.currentTime);
				filter.frequency.exponentialRampToValueAtTime(80, audioCtx.currentTime + duration);
				var gain = audioCtx.createGain();
				gain.gain.setValueAtTime(vol || 0.25, audioCtx.currentTime);
				gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + duration);
				noise.connect(filter);
				filter.connect(gain);
				gain.connect(audioCtx.destination);
				noise.start();
			} catch (e) {}
		}

		return {
			init: init,
			toggle: function () {
				soundEnabled = !soundEnabled;
				return soundEnabled;
			},
			isEnabled: function () {
				return soundEnabled;
			},
			shoot: function () {
				playTone(600, 'square', 0.1, 0.15, 120);
			},
			hitBrick: function () {
				playNoise(0.08, 0.18);
			},
			hitSteel: function () {
				playTone(1200, 'triangle', 0.08, 0.2, 400);
			},
			explosion: function () {
				playNoise(0.35, 0.4);
			},
			bigExplosion: function () {
				playNoise(0.65, 0.55);
				playTone(180, 'sawtooth', 0.5, 0.3, 40);
			},
			powerupSpawn: function () {
				[330, 440, 550, 660, 880].forEach(function (f, idx) {
					setTimeout(function () {
						playTone(f, 'sine', 0.08, 0.15);
					}, idx * 60);
				});
			},
			powerupPickup: function () {
				[523, 659, 784, 1046].forEach(function (f, idx) {
					setTimeout(function () {
						playTone(f, 'triangle', 0.1, 0.2);
					}, idx * 70);
				});
			},
			stageStart: function () {
				var notes = [
					{ f: 261, d: 0.1 }, { f: 329, d: 0.1 }, { f: 392, d: 0.1 },
					{ f: 523, d: 0.2 }, { f: 392, d: 0.1 }, { f: 523, d: 0.35 }
				];
				var cur = 0;
				notes.forEach(function (n) {
					setTimeout(function () {
						playTone(n.f, 'square', n.d, 0.18);
					}, cur);
					cur += n.d * 1000 + 40;
				});
			},
			gameOver: function () {
				var notes = [440, 392, 349, 293, 220];
				notes.forEach(function (f, idx) {
					setTimeout(function () {
						playTone(f, 'sawtooth', 0.22, 0.25);
					}, idx * 150);
				});
			}
		};
	})();

	// Authentic Battle City Stage Layouts
	var STAGE_MAPS = [
		// Stage 1
		[
			"..........................",
			"..........................",
			"..##..##..##..##..##..##..",
			"..##..##..##..##..##..##..",
			"..##..##..##..##..##..##..",
			"..##..##..##..##..##..##..",
			"..##..##..==..==..##..##..",
			"..##..##..==..==..##..##..",
			"..##..##..........##..##..",
			"..##..##..........##..##..",
			"..........##..##..........",
			"..........##..##..........",
			"==..##..............##..==",
			"==..##..............##..==",
			"..........##..##..........",
			"..........##..##..........",
			"..##..##..........##..##..",
			"..##..##..........##..##..",
			"..##..##..##..##..##..##..",
			"..##..##..##..##..##..##..",
			"..##..##..........##..##..",
			"..##..##..........##..##..",
			"..##..##...####...##..##..",
			"...........#BB#...........",
			"...........#BB#...........",
			".........................."
		],
		// Stage 2
		[
			"..........................",
			"..........................",
			"..##..##..==..==..##..##..",
			"..##..##..==..==..##..##..",
			"..##..##..##..##..##..##..",
			"..##..##..##..##..##..##..",
			"..~~..~~..........~~..~~..",
			"..~~..~~..........~~..~~..",
			"......==..##..##..==......",
			"......==..##..##..==......",
			"..##..##..##..##..##..##..",
			"..##..##..##..##..##..##..",
			"..##..##..##..##..##..##..",
			"..##..##..##..##..##..##..",
			"..==..........==......==..",
			"..==..........==......==..",
			"......##..##..##..##......",
			"......##..##..##..##......",
			"..##..##..##..##..##..##..",
			"..##..##..##..##..##..##..",
			"..##..##..........##..##..",
			"..##..##..........##..##..",
			"..##..##...####...##..##..",
			"...........#BB#...........",
			"...........#BB#...........",
			".........................."
		],
		// Stage 3
		[
			"..........................",
			"..........................",
			"..%%..%%..##..##..%%..%%..",
			"..%%..%%..##..##..%%..%%..",
			"..##..##..==..==..##..##..",
			"..##..##..==..==..##..##..",
			"..##..##..........##..##..",
			"..##..##..........##..##..",
			"..%%..%%..##..##..%%..%%..",
			"..%%..%%..##..##..%%..%%..",
			"..##..##..##..##..##..##..",
			"..##..##..##..##..##..##..",
			"..........==..==..........",
			"..........==..==..........",
			"..##..##..##..##..##..##..",
			"..##..##..##..##..##..##..",
			"..%%..%%..........%%..%%..",
			"..%%..%%..........%%..%%..",
			"..##..##..##..##..##..##..",
			"..##..##..##..##..##..##..",
			"..##..##..==..==..##..##..",
			"..##..##..==..==..##..##..",
			"...........####...........",
			"...........#BB#...........",
			"...........#BB#...........",
			".........................."
		],
		// Stage 4
		[
			"..........................",
			"..........................",
			"..##..##..##..##..##..##..",
			"..##..##..##..##..##..##..",
			"~~~~~~~~~~~~~~~~~~~~~~~~~~",
			"~~~~~~~~~~~~~~~~~~~~~~~~~~",
			"......##..........##......",
			"......##..........##......",
			"..##..##..==..==..##..##..",
			"..##..##..==..==..##..##..",
			"..##..##..........##..##..",
			"..##..##..........##..##..",
			"~~~~~~~~~~~~~~~~~~~~~~~~~~",
			"~~~~~~~~~~~~~~~~~~~~~~~~~~",
			"..##..##..........##..##..",
			"..##..##..........##..##..",
			"..##..##..##..##..##..##..",
			"..##..##..##..##..##..##..",
			"..==..==..........==..==..",
			"..==..==..........==..==..",
			"..##..##..........##..##..",
			"..##..##..........##..##..",
			"...........####...........",
			"...........#BB#...........",
			"...........#BB#...........",
			".........................."
		],
		// Stage 5
		[
			"..........................",
			"..........................",
			"..==..==..##..##..==..==..",
			"..==..==..##..##..==..==..",
			"..##..##..==..==..##..##..",
			"..##..##..==..==..##..##..",
			"..##..##..##..##..##..##..",
			"..##..##..##..##..##..##..",
			"..%%..%%..~~..~~..%%..%%..",
			"..%%..%%..~~..~~..%%..%%..",
			"..##..##..##..##..##..##..",
			"..##..##..##..##..##..##..",
			"..==..==..........==..==..",
			"..==..==..........==..==..",
			"..##..##..##..##..##..##..",
			"..##..##..##..##..##..##..",
			"..%%..%%..~~..~~..%%..%%..",
			"..%%..%%..~~..~~..%%..%%..",
			"..##..##..##..##..##..##..",
			"..##..##..##..##..##..##..",
			"..==..==..........==..==..",
			"..==..==..........==..==..",
			"...........####...........",
			"...........#BB#...........",
			"...........#BB#...........",
			".........................."
		]
	];

	// Game State
	var canvas, ctx;
	var mapGrid = [];          // 26x26 array
	var player = null;
	var enemies = [];
	var bullets = [];
	var particles = [];
	var powerups = [];
	var baseDestroyed = false;

	var stage = 1;
	var score = 0;
	var playerLives = 3;
	var stageEnemiesRemaining = 20;
	var stageEnemiesSpawned = 0;
	var stageTanksKilled = { basic: 0, fast: 0, power: 0, armor: 0 };
	var totalTanksKilled = 0;

	var isRunning = false;
	var isPaused = false;
	var shovelTimer = 0;
	var freezeTimer = 0;
	var sessionToken = '';
	var gameStartTime = 0;

	// Input Keys
	var keys = {
		up: false,
		down: false,
		left: false,
		right: false,
		fire: false
	};

	// Powerup Types
	var POWERUP_TYPE = {
		STAR: 0,
		BOMB: 1,
		CLOCK: 2,
		HELMET: 3,
		SHOVEL: 4,
		TANK: 5
	};

	// Initialize Grid Map
	function loadStageMap(stageNum) {
		var mapIndex = (stageNum - 1) % STAGE_MAPS.length;
		var template = STAGE_MAPS[mapIndex];
		mapGrid = [];

		for (var y = 0; y < GRID_COUNT; y++) {
			mapGrid[y] = [];
			for (var x = 0; x < GRID_COUNT; x++) {
				var ch = template[y][x];
				var tileType = TILE.EMPTY;
				if (ch === '#') tileType = TILE.BRICK;
				else if (ch === '=') tileType = TILE.STEEL;
				else if (ch === '%') tileType = TILE.TREES;
				else if (ch === '~') tileType = TILE.WATER;
				else if (ch === '-') tileType = TILE.ICE;
				else if (ch === 'B') tileType = TILE.BASE;

				mapGrid[y][x] = tileType;
			}
		}

		baseDestroyed = false;
	}

	// Shovel Base Fortification
	function setBaseWall(tileType) {
		var coords = [
			{ x: 11, y: 22 }, { x: 12, y: 22 }, { x: 13, y: 22 }, { x: 14, y: 22 },
			{ x: 11, y: 23 },                                     { x: 14, y: 23 },
			{ x: 11, y: 24 },                                     { x: 14, y: 24 }
		];
		coords.forEach(function (c) {
			if (mapGrid[c.y] && !baseDestroyed) {
				mapGrid[c.y][c.x] = tileType;
			}
		});
	}

	function destroyBase() {
		baseDestroyed = true;
		for (var by = 23; by <= 24; by++) {
			for (var bx = 12; bx <= 13; bx++) {
				if (mapGrid[by]) mapGrid[by][bx] = TILE.BASE_DEAD;
			}
		}
	}

	// Player Tank Constructor
	function PlayerTank() {
		this.w = 26;
		this.h = 26;
		this.x = 9 * TILE_SIZE + 3;
		this.y = 24 * TILE_SIZE + 3;
		this.dir = DIR.UP;
		this.tier = 1;         // 1=Standard, 2=Fast Shot, 3=Twin Shot, 4=Heavy Buster
		this.speed = 2.0;
		this.shieldTimer = 180; // 3 seconds invulnerability on spawn
		this.lastShotTime = 0;
		this.isMoving = false;
		this.trackFrame = 0;
	}

	PlayerTank.prototype.upgrade = function () {
		if (this.tier < 4) {
			this.tier++;
		}
		if (this.tier >= 2) this.speed = 2.4;
		updateHUD();
	};

	PlayerTank.prototype.update = function () {
		var dx = 0;
		var dy = 0;
		var wantedDir = null;

		if (keys.up) { wantedDir = DIR.UP; dy = -this.speed; }
		else if (keys.down) { wantedDir = DIR.DOWN; dy = this.speed; }
		else if (keys.left) { wantedDir = DIR.LEFT; dx = -this.speed; }
		else if (keys.right) { wantedDir = DIR.RIGHT; dx = this.speed; }

		this.isMoving = (wantedDir !== null);

		if (wantedDir !== null) {
			// Turn snap assist: align with nearest open lane on perpendicular axis
			if (wantedDir !== this.dir) {
				if (wantedDir === DIR.UP || wantedDir === DIR.DOWN) {
					var targetX = Math.round((this.x - 3) / 16) * 16 + 3;
					if (Math.abs(this.x - targetX) <= 8 && !checkObstacleCollision(targetX, this.y, this.w, this.h, false)) {
						this.x = targetX;
					}
				} else {
					var targetY = Math.round((this.y - 3) / 16) * 16 + 3;
					if (Math.abs(this.y - targetY) <= 8 && !checkObstacleCollision(this.x, targetY, this.w, this.h, false)) {
						this.y = targetY;
					}
				}
				this.dir = wantedDir;
			}

			// Move with sub-step collision check
			this.move(dx, dy);
			this.trackFrame = (this.trackFrame + 1) % 8;
		}

		if (this.shieldTimer > 0) {
			this.shieldTimer--;
		}

		// Fire handling
		if (keys.fire) {
			var now = Date.now();
			var maxBullets = (this.tier >= 3) ? 2 : 1;
			var playerBulletsCount = bullets.filter(function (b) { return b.isPlayer; }).length;

			if (playerBulletsCount < maxBullets && now - this.lastShotTime > 180) {
				this.shoot();
				this.lastShotTime = now;
			}
		}
	};

	PlayerTank.prototype.shoot = function () {
		var bSpeed = (this.tier >= 2) ? 5.2 : 3.8;
		var canBreakSteel = (this.tier >= 4);

		var bx = this.x + this.w / 2;
		var by = this.y + this.h / 2;

		if (this.dir === DIR.UP) by = this.y - 2;
		else if (this.dir === DIR.DOWN) by = this.y + this.h + 2;
		else if (this.dir === DIR.LEFT) bx = this.x - 2;
		else if (this.dir === DIR.RIGHT) bx = this.x + this.w + 2;

		bullets.push(new Bullet(bx, by, this.dir, bSpeed, true, canBreakSteel));
		SoundEngine.shoot();
	};

	PlayerTank.prototype.move = function (dx, dy) {
		// Unstick safety check: if currently inside any obstacle, push out immediately
		if (checkObstacleCollision(this.x, this.y, this.w, this.h, false)) {
			var escapes = [
				{ x: 1, y: 0 }, { x: -1, y: 0 }, { x: 0, y: 1 }, { x: 0, y: -1 },
				{ x: 2, y: 0 }, { x: -2, y: 0 }, { x: 0, y: 2 }, { x: 0, y: -2 },
				{ x: 4, y: 0 }, { x: -4, y: 0 }, { x: 0, y: 4 }, { x: 0, y: -4 }
			];
			for (var ev = 0; ev < escapes.length; ev++) {
				var ex = this.x + escapes[ev].x;
				var ey = this.y + escapes[ev].y;
				if (!checkObstacleCollision(ex, ey, this.w, this.h, false)) {
					this.x = ex;
					this.y = ey;
					break;
				}
			}
		}

		// Move in micro-steps so player reaches the exact boundary of the wall without sticking
		var stepX = dx !== 0 ? Math.sign(dx) * 0.5 : 0;
		var stepY = dy !== 0 ? Math.sign(dy) * 0.5 : 0;
		var remainingDist = Math.max(Math.abs(dx), Math.abs(dy));

		while (remainingDist >= 0.4) {
			var nextX = this.x + stepX;
			var nextY = this.y + stepY;

			if (nextX < 0 || nextX + this.w > CANVAS_SIZE || nextY < 0 || nextY + this.h > CANVAS_SIZE) {
				break;
			}

			if (checkObstacleCollision(nextX, nextY, this.w, this.h, false)) {
				break;
			}

			// Check collision with other tanks
			var collidesTank = false;
			for (var i = 0; i < enemies.length; i++) {
				if (checkRectOverlap(nextX, nextY, this.w, this.h, enemies[i].x, enemies[i].y, enemies[i].w, enemies[i].h)) {
					collidesTank = true;
					break;
				}
			}
			if (collidesTank) break;

			this.x = nextX;
			this.y = nextY;
			remainingDist -= 0.5;
		}
	};

	// Enemy Tank Constructor
	function EnemyTank(type, isFlashing) {
		this.type = type; // 1=Basic, 2=Fast, 3=Power, 4=Heavy Armor
		this.isFlashing = !!isFlashing;
		this.w = 26;
		this.h = 26;
		this.dir = DIR.DOWN;
		this.changeDirTimer = 30 + Math.floor(Math.random() * 60);
		this.shootTimer = 40 + Math.floor(Math.random() * 60);
		this.trackFrame = 0;

		// Spawn Positions (3 classic spawn points)
		var spawnPoints = [
			{ x: 3, y: 3 },
			{ x: 12 * TILE_SIZE + 3, y: 3 },
			{ x: 24 * TILE_SIZE + 3, y: 3 }
		];
		var sp = spawnPoints[Math.floor(Math.random() * spawnPoints.length)];
		this.x = sp.x;
		this.y = sp.y;

		// Stats per type
		if (type === 1) { // Basic
			this.speed = 1.2;
			this.hp = 1;
			this.pts = 100;
			this.bulletSpeed = 3.2;
		} else if (type === 2) { // Fast
			this.speed = 2.4;
			this.hp = 1;
			this.pts = 200;
			this.bulletSpeed = 3.8;
		} else if (type === 3) { // Power
			this.speed = 1.4;
			this.hp = 1;
			this.pts = 300;
			this.bulletSpeed = 5.2;
		} else if (type === 4) { // Heavy Armor
			this.speed = 1.1;
			this.hp = 4;
			this.pts = 400;
			this.bulletSpeed = 3.6;
		}
	}

	EnemyTank.prototype.update = function () {
		if (freezeTimer > 0) return; // Frozen by clock powerup

		this.changeDirTimer--;
		if (this.changeDirTimer <= 0) {
			this.chooseDirection();
			this.changeDirTimer = 45 + Math.floor(Math.random() * 80);
		}

		var vec = DIR_VECTORS[this.dir];
		var newX = this.x + vec.x * this.speed;
		var newY = this.y + vec.y * this.speed;

		if (newX < 0 || newX + this.w > CANVAS_SIZE || newY < 0 || newY + this.h > CANVAS_SIZE) {
			this.chooseDirection();
		} else if (checkObstacleCollision(newX, newY, this.w, this.h, false)) {
			this.chooseDirection();
		} else {
			// Check collision with other enemies and player
			var collides = false;
			if (player && checkRectOverlap(newX, newY, this.w, this.h, player.x, player.y, player.w, player.h)) {
				collides = true;
			}
			for (var i = 0; i < enemies.length; i++) {
				if (enemies[i] !== this && checkRectOverlap(newX, newY, this.w, this.h, enemies[i].x, enemies[i].y, enemies[i].w, enemies[i].h)) {
					collides = true;
					break;
				}
			}

			if (collides) {
				this.chooseDirection();
			} else {
				this.x = newX;
				this.y = newY;
				this.trackFrame = (this.trackFrame + 1) % 8;
			}
		}

		// Shooting
		this.shootTimer--;
		if (this.shootTimer <= 0) {
			this.shoot();
			this.shootTimer = 50 + Math.floor(Math.random() * 80);
		}
	};

	EnemyTank.prototype.chooseDirection = function () {
		// 50% chance to steer towards base or player, 50% random
		var targetX = 12 * TILE_SIZE;
		var targetY = 24 * TILE_SIZE;

		if (Math.random() < 0.6) {
			if (Math.abs(this.x - targetX) > Math.abs(this.y - targetY)) {
				this.dir = (this.x < targetX) ? DIR.RIGHT : DIR.LEFT;
			} else {
				this.dir = (this.y < targetY) ? DIR.DOWN : DIR.UP;
			}
		} else {
			this.dir = Math.floor(Math.random() * 4);
		}
	};

	EnemyTank.prototype.shoot = function () {
		var bx = this.x + this.w / 2;
		var by = this.y + this.h / 2;
		if (this.dir === DIR.UP) by = this.y - 2;
		else if (this.dir === DIR.DOWN) by = this.y + this.h + 2;
		else if (this.dir === DIR.LEFT) bx = this.x - 2;
		else if (this.dir === DIR.RIGHT) bx = this.x + this.w + 2;

		bullets.push(new Bullet(bx, by, this.dir, this.bulletSpeed, false, false));
	};

	// Bullet Constructor
	function Bullet(x, y, dir, speed, isPlayer, canBreakSteel) {
		this.x = x;
		this.y = y;
		this.dir = dir;
		this.speed = speed;
		this.isPlayer = isPlayer;
		this.canBreakSteel = !!canBreakSteel;
		this.alive = true;
		this.size = 4;
	}

	Bullet.prototype.update = function () {
		var vec = DIR_VECTORS[this.dir];
		this.x += vec.x * this.speed;
		this.y += vec.y * this.speed;

		// Screen boundary check
		if (this.x < 0 || this.x > CANVAS_SIZE || this.y < 0 || this.y > CANVAS_SIZE) {
			this.alive = false;
			createSparks(this.x, this.y, '#fff');
			return;
		}

		// Bullet-to-bullet collision (counter-fire)
		for (var i = 0; i < bullets.length; i++) {
			var other = bullets[i];
			if (other !== this && other.alive && this.isPlayer !== other.isPlayer) {
				if (Math.abs(this.x - other.x) < 6 && Math.abs(this.y - other.y) < 6) {
					this.alive = false;
					other.alive = false;
					createSparks(this.x, this.y, '#ffcc00');
					return;
				}
			}
		}

		// Tile Map collision
		var tileX = Math.floor(this.x / TILE_SIZE);
		var tileY = Math.floor(this.y / TILE_SIZE);

		if (tileY >= 0 && tileY < GRID_COUNT && tileX >= 0 && tileX < GRID_COUNT) {
			var t = mapGrid[tileY][tileX];

			if (t === TILE.BRICK) {
				mapGrid[tileY][tileX] = TILE.EMPTY;
				this.alive = false;
				createSparks(this.x, this.y, '#b3391b');
				SoundEngine.hitBrick();
				return;
			} else if (t === TILE.STEEL) {
				if (this.canBreakSteel) {
					mapGrid[tileY][tileX] = TILE.EMPTY;
					createSparks(this.x, this.y, '#fff');
					SoundEngine.hitBrick();
				} else {
					createSparks(this.x, this.y, '#90a4ae');
					SoundEngine.hitSteel();
				}
				this.alive = false;
				return;
			} else if (t === TILE.BASE) {
				// Destroy EXS Base!
				destroyBase();
				this.alive = false;
				createExplosion(12 * TILE_SIZE + 16, 23 * TILE_SIZE + 16, true);
				SoundEngine.bigExplosion();
				gameOver('Ienaidnieks iznīcināja EXS zelta bāzi!');
				return;
			}
		}

		// Hit Tanks
		if (this.isPlayer) {
			// Hit Enemy
			for (var e = 0; e < enemies.length; e++) {
				var enemy = enemies[e];
				if (checkRectOverlap(this.x - 2, this.y - 2, 4, 4, enemy.x, enemy.y, enemy.w, enemy.h)) {
					this.alive = false;
					enemy.hp--;

					if (enemy.hp <= 0) {
						// Enemy destroyed
						createExplosion(enemy.x + enemy.w / 2, enemy.y + enemy.h / 2, false);
						SoundEngine.explosion();
						score += enemy.pts;

						// Record tally
						if (enemy.type === 1) stageTanksKilled.basic++;
						else if (enemy.type === 2) stageTanksKilled.fast++;
						else if (enemy.type === 3) stageTanksKilled.power++;
						else if (enemy.type === 4) stageTanksKilled.armor++;
						totalTanksKilled++;

						// Spawn power-up if flashing
						if (enemy.isFlashing) {
							spawnPowerup(enemy.x, enemy.y);
						}

						enemies.splice(e, 1);
						stageEnemiesRemaining--;
						updateQueueHUD();
						updateHUD();

						// Check stage clear
						if (stageEnemiesRemaining <= 0 && enemies.length === 0) {
							stageCleared();
						}
					} else {
						// Damage blink
						createSparks(this.x, this.y, '#ff4444');
						SoundEngine.hitSteel();
					}
					break;
				}
			}
		} else {
			// Hit Player
			if (player && player.shieldTimer <= 0) {
				if (checkRectOverlap(this.x - 2, this.y - 2, 4, 4, player.x, player.y, player.w, player.h)) {
					this.alive = false;
					if (player.tier === 4) {
						// Downgrade tier instead of dying
						player.tier = 3;
						player.shieldTimer = 90;
						createSparks(player.x + 14, player.y + 14, '#ffcc00');
						SoundEngine.hitSteel();
						updateHUD();
					} else {
						// Player dies
						createExplosion(player.x + 14, player.y + 14, true);
						SoundEngine.bigExplosion();
						playerLives--;
						updateHUD();

						if (playerLives <= 0) {
							player = null;
							gameOver('Tavi tanki tika iznīcināti!');
						} else {
							player = new PlayerTank();
						}
					}
				}
			}
		}
	};

	// Powerup Constructor
	function Powerup(x, y, type) {
		this.w = 28;
		this.h = 28;
		this.x = Math.max(16, Math.min(416 - 16 - this.w, x));
		this.y = Math.max(16, Math.min(416 - 16 - this.h, y));
		this.type = type;
		this.timer = 720; // 12 seconds before expiring
	}

	function spawnPowerup(x, y) {
		var types = [
			POWERUP_TYPE.STAR,
			POWERUP_TYPE.BOMB,
			POWERUP_TYPE.CLOCK,
			POWERUP_TYPE.HELMET,
			POWERUP_TYPE.SHOVEL,
			POWERUP_TYPE.TANK
		];
		var selected = types[Math.floor(Math.random() * types.length)];
		powerups.push(new Powerup(x, y, selected));
		SoundEngine.powerupSpawn();
	}

	function applyPowerup(type) {
		SoundEngine.powerupPickup();
		score += 500;
		updateHUD();

		var badge = document.getElementById('stat-powerup');
		if (badge) badge.className = 'hud-powerup-badge';

		if (type === POWERUP_TYPE.STAR) {
			if (player) player.upgrade();
			if (badge) { badge.classList.add('star'); badge.textContent = '⭐ ZVAIGZNE'; }
		} else if (type === POWERUP_TYPE.BOMB) {
			// Destroy all active enemies
			for (var i = enemies.length - 1; i >= 0; i--) {
				var en = enemies[i];
				createExplosion(en.x + 14, en.y + 14, false);
				score += en.pts;
				if (en.type === 1) stageTanksKilled.basic++;
				else if (en.type === 2) stageTanksKilled.fast++;
				else if (en.type === 3) stageTanksKilled.power++;
				else if (en.type === 4) stageTanksKilled.armor++;
				totalTanksKilled++;
				stageEnemiesRemaining--;
			}
			enemies = [];
			SoundEngine.bigExplosion();
			updateQueueHUD();
			updateHUD();
			if (stageEnemiesRemaining <= 0) stageCleared();
			if (badge) { badge.classList.add('bomb'); badge.textContent = '💣 GRANĀTA'; }
		} else if (type === POWERUP_TYPE.CLOCK) {
			freezeTimer = 600; // 10 seconds freeze
			if (badge) { badge.classList.add('clock'); badge.textContent = '⏰ SALDĒTS'; }
		} else if (type === POWERUP_TYPE.HELMET) {
			if (player) player.shieldTimer = 720; // 12 seconds shield
			if (badge) { badge.classList.add('shield'); badge.textContent = '🛡️ VAIROGS'; }
		} else if (type === POWERUP_TYPE.SHOVEL) {
			setBaseWall(TILE.STEEL);
			shovelTimer = 900; // 15 seconds
			if (badge) { badge.classList.add('shovel'); badge.textContent = '⛏️ LĀPSTA'; }
		} else if (type === POWERUP_TYPE.TANK) {
			playerLives++;
			updateHUD();
			if (badge) { badge.classList.add('star'); badge.textContent = '🎖️ +1 DZĪVĪBA'; }
		}
	}

	// Particle Effects
	function createSparks(x, y, color) {
		for (var i = 0; i < 6; i++) {
			var angle = Math.random() * Math.PI * 2;
			var speed = 1 + Math.random() * 2.5;
			particles.push({
				x: x,
				y: y,
				vx: Math.cos(angle) * speed,
				vy: Math.sin(angle) * speed,
				life: 12 + Math.floor(Math.random() * 8),
				maxLife: 20,
				color: color,
				size: 2
			});
		}
	}

	function createExplosion(x, y, isBig) {
		var count = isBig ? 32 : 18;
		for (var i = 0; i < count; i++) {
			var angle = Math.random() * Math.PI * 2;
			var speed = (isBig ? 1.5 : 1) + Math.random() * 3.5;
			var colors = ['#ffffff', '#ffeb3b', '#ff9800', '#f44336'];
			particles.push({
				x: x,
				y: y,
				vx: Math.cos(angle) * speed,
				vy: Math.sin(angle) * speed,
				life: 20 + Math.floor(Math.random() * 15),
				maxLife: 35,
				color: colors[Math.floor(Math.random() * colors.length)],
				size: isBig ? 4 : 3
			});
		}
	}

	// Collision Utilities
	function checkRectOverlap(x1, y1, w1, h1, x2, y2, w2, h2) {
		return !(x1 + w1 <= x2 || x1 >= x2 + w2 || y1 + h1 <= y2 || y1 >= y2 + h2);
	}

	function checkObstacleCollision(x, y, w, h, isBullet) {
		var leftTile = Math.floor(x / TILE_SIZE);
		var rightTile = Math.floor((x + w - 0.05) / TILE_SIZE);
		var topTile = Math.floor(y / TILE_SIZE);
		var bottomTile = Math.floor((y + h - 0.05) / TILE_SIZE);

		for (var ty = topTile; ty <= bottomTile; ty++) {
			for (var tx = leftTile; tx <= rightTile; tx++) {
				if (ty >= 0 && ty < GRID_COUNT && tx >= 0 && tx < GRID_COUNT) {
					var t = mapGrid[ty][tx];
					if (t === TILE.BRICK || t === TILE.STEEL || t === TILE.BASE || t === TILE.BASE_DEAD) {
						return true;
					}
					if (!isBullet && t === TILE.WATER) {
						return true; // Tanks cannot cross water
					}
				}
			}
		}
		return false;
	}

	// Main Game Loop
	var lastTime = 0;
	var enemySpawnTimer = 0;

	function gameLoop(timestamp) {
		if (!isRunning) return;

		if (!isPaused) {
			update();
			render();
		}

		requestAnimationFrame(gameLoop);
	}

	function update() {
		// Update Shovel Timer
		if (shovelTimer > 0) {
			shovelTimer--;
			if (shovelTimer <= 180 && shovelTimer % 30 === 0) {
				// Blinking before reverting
				var blinkType = (Math.floor(shovelTimer / 15) % 2 === 0) ? TILE.BRICK : TILE.STEEL;
				setBaseWall(blinkType);
			}
			if (shovelTimer === 0) {
				setBaseWall(TILE.BRICK);
			}
		}

		// Update Freeze Timer
		if (freezeTimer > 0) {
			freezeTimer--;
		}

		// Spawn Enemy Tanks (up to 4 on board at once)
		if (stageEnemiesSpawned < 20 && enemies.length < 4) {
			enemySpawnTimer++;
			if (enemySpawnTimer >= 100) {
				spawnEnemy();
				enemySpawnTimer = 0;
			}
		}

		// Update Player
		if (player) {
			player.update();
		}

		// Update Enemies
		for (var e = 0; e < enemies.length; e++) {
			enemies[e].update();
		}

		// Update Bullets
		for (var b = bullets.length - 1; b >= 0; b--) {
			bullets[b].update();
			if (!bullets[b].alive) {
				bullets.splice(b, 1);
			}
		}

		// Update Particles
		for (var p = particles.length - 1; p >= 0; p--) {
			var pt = particles[p];
			pt.x += pt.vx;
			pt.y += pt.vy;
			pt.life--;
			if (pt.life <= 0) {
				particles.splice(p, 1);
			}
		}

		// Update Powerups
		for (var pu = powerups.length - 1; pu >= 0; pu--) {
			var pow = powerups[pu];
			pow.timer--;
			if (player && checkRectOverlap(player.x, player.y, player.w, player.h, pow.x, pow.y, pow.w, pow.h)) {
				applyPowerup(pow.type);
				powerups.splice(pu, 1);
			} else if (pow.timer <= 0) {
				powerups.splice(pu, 1);
			}
		}
	}

	function spawnEnemy() {
		// Determine enemy type based on stage progression
		var rand = Math.random();
		var type = 1;
		if (stage >= 3 && rand < 0.25) type = 4; // Heavy armor
		else if (stage >= 2 && rand < 0.5) type = 3; // Power
		else if (rand < 0.75) type = 2; // Fast
		else type = 1;

		// 20% of enemies are flashing bonus carriers
		var isFlashing = (stageEnemiesSpawned === 3 || stageEnemiesSpawned === 9 || stageEnemiesSpawned === 16);
		enemies.push(new EnemyTank(type, isFlashing));
		stageEnemiesSpawned++;
	}

	// Rendering
	function render() {
		ctx.clearRect(0, 0, CANVAS_SIZE, CANVAS_SIZE);

		// 1. Draw Ground & Lower Layer (Empty, Ice, Water)
		ctx.fillStyle = '#000000';
		ctx.fillRect(0, 0, CANVAS_SIZE, CANVAS_SIZE);

		for (var y = 0; y < GRID_COUNT; y++) {
			for (var x = 0; x < GRID_COUNT; x++) {
				var t = mapGrid[y][x];
				var px = x * TILE_SIZE;
				var py = y * TILE_SIZE;

				if (t === TILE.WATER) {
					// Animated water ripple
					ctx.fillStyle = '#0055aa';
					ctx.fillRect(px, py, TILE_SIZE, TILE_SIZE);
					ctx.fillStyle = '#3399ff';
					var offset = (Math.floor(Date.now() / 300) + x + y) % 4;
					ctx.fillRect(px + offset * 2, py + 4, 8, 3);
				} else if (t === TILE.ICE) {
					ctx.fillStyle = '#b0bec5';
					ctx.fillRect(px, py, TILE_SIZE, TILE_SIZE);
					ctx.fillStyle = '#eceff1';
					ctx.fillRect(px + 2, py + 2, 4, 4);
				}
			}
		}

		// 2. Draw Walls & Base
		for (var y = 0; y < GRID_COUNT; y++) {
			for (var x = 0; x < GRID_COUNT; x++) {
				var t = mapGrid[y][x];
				var px = x * TILE_SIZE;
				var py = y * TILE_SIZE;

				if (t === TILE.BRICK) {
					// Classic Brick pattern
					ctx.fillStyle = '#b3391b';
					ctx.fillRect(px, py, TILE_SIZE, TILE_SIZE);
					ctx.fillStyle = '#d35400';
					ctx.fillRect(px + 1, py + 1, TILE_SIZE - 2, 6);
					ctx.fillRect(px + 1, py + 9, TILE_SIZE - 2, 6);
					ctx.fillStyle = '#000';
					ctx.fillRect(px, py + 7, TILE_SIZE, 2);
					ctx.fillRect(px + 7, py, 2, 7);
					ctx.fillRect(px + 11, py + 9, 2, 7);
				} else if (t === TILE.STEEL) {
					// Classic Steel Block
					ctx.fillStyle = '#78909c';
					ctx.fillRect(px, py, TILE_SIZE, TILE_SIZE);
					ctx.fillStyle = '#cfd8dc';
					ctx.fillRect(px + 2, py + 2, TILE_SIZE - 4, TILE_SIZE - 4);
					ctx.fillStyle = '#ffffff';
					ctx.fillRect(px + 4, py + 4, 4, 4);
				}
			}
		}

		// Draw EXS Base (single, perfectly aligned 32x32 entity)
		drawBase();

		// 3. Draw Powerups
		for (var pu = 0; pu < powerups.length; pu++) {
			drawPowerup(powerups[pu]);
		}

		// 4. Draw Enemies
		for (var e = 0; e < enemies.length; e++) {
			drawTank(enemies[e], false);
		}

		// 5. Draw Player
		if (player) {
			drawTank(player, true);
		}

		// 6. Draw Upper Layer (Trees / Foliage hides tanks beneath!)
		for (var y = 0; y < GRID_COUNT; y++) {
			for (var x = 0; x < GRID_COUNT; x++) {
				if (mapGrid[y][x] === TILE.TREES) {
					var px = x * TILE_SIZE;
					var py = y * TILE_SIZE;
					ctx.fillStyle = 'rgba(46, 125, 50, 0.9)';
					ctx.fillRect(px, py, TILE_SIZE, TILE_SIZE);
					ctx.fillStyle = '#4caf50';
					ctx.fillRect(px + 2, py + 2, 4, 4);
					ctx.fillRect(px + 10, py + 8, 4, 4);
				}
			}
		}

		// 7. Draw Bullets
		ctx.fillStyle = '#ffffff';
		for (var b = 0; b < bullets.length; b++) {
			var bl = bullets[b];
			ctx.beginPath();
			ctx.arc(bl.x, bl.y, bl.size / 2, 0, Math.PI * 2);
			ctx.fill();
		}

		// 8. Draw Particles (Sparks, Explosions)
		for (var p = 0; p < particles.length; p++) {
			var pt = particles[p];
			ctx.fillStyle = pt.color;
			ctx.beginPath();
			ctx.arc(pt.x, pt.y, pt.size, 0, Math.PI * 2);
			ctx.fill();
		}
	}

	// Tank Drawing with Rotations & Sprites
	function drawTank(t, isPlayer) {
		ctx.save();
		ctx.translate(t.x + t.w / 2, t.y + t.h / 2);

		// Rotate according to direction
		var angle = 0;
		if (t.dir === DIR.RIGHT) angle = Math.PI / 2;
		else if (t.dir === DIR.DOWN) angle = Math.PI;
		else if (t.dir === DIR.LEFT) angle = -Math.PI / 2;
		ctx.rotate(angle);

		// Determine colors
		var bodyColor = '#f5b300';
		var treadColor = '#424242';
		var barrelColor = '#fff';

		if (isPlayer) {
			if (t.tier === 1) bodyColor = '#f5b300';
			else if (t.tier === 2) bodyColor = '#ff9800';
			else if (t.tier === 3) bodyColor = '#e65100';
			else if (t.tier === 4) bodyColor = '#c2185b';
		} else {
			if (t.isFlashing && Math.floor(Date.now() / 150) % 2 === 0) {
				bodyColor = '#ff1744';
			} else if (t.type === 1) bodyColor = '#9e9e9e'; // Basic
			else if (t.type === 2) bodyColor = '#cfd8dc'; // Fast
			else if (t.type === 3) bodyColor = '#00bcd4'; // Power
			else if (t.type === 4) {                      // Heavy Armor
				var healthColors = ['#e53935', '#fbc02d', '#4caf50', '#2e7d32'];
				bodyColor = healthColors[t.hp - 1] || '#4caf50';
			}
		}

		var hw = t.w / 2;
		var hh = t.h / 2;

		// Treads (Left & Right)
		ctx.fillStyle = treadColor;
		ctx.fillRect(-hw, -hh, 6, t.h);
		ctx.fillRect(hw - 6, -hh, 6, t.h);

		// Tread treads animation
		ctx.fillStyle = '#212121';
		var step = (t.trackFrame % 4) * 3;
		for (var k = -hh + step; k < hh; k += 6) {
			ctx.fillRect(-hw, k, 6, 2);
			ctx.fillRect(hw - 6, k, 6, 2);
		}

		// Tank Body
		ctx.fillStyle = bodyColor;
		ctx.fillRect(-hw + 6, -hh + 4, t.w - 12, t.h - 8);

		// Turret Center
		ctx.fillStyle = '#ffffff';
		ctx.beginPath();
		ctx.arc(0, 0, 5, 0, Math.PI * 2);
		ctx.fill();

		// Cannon Barrel
		ctx.fillStyle = barrelColor;
		ctx.fillRect(-2, -hh - 4, 4, 10);

		// Tier 3/4 Double Barrel
		if (isPlayer && t.tier >= 3) {
			ctx.fillRect(-5, -hh - 4, 3, 10);
			ctx.fillRect(2, -hh - 4, 3, 10);
		}

		ctx.restore();

		// Draw Shield Ring
		if (isPlayer && t.shieldTimer > 0) {
			ctx.strokeStyle = (Math.floor(Date.now() / 80) % 2 === 0) ? '#40c4ff' : '#ffffff';
			ctx.lineWidth = 2;
			ctx.beginPath();
			ctx.arc(t.x + t.w / 2, t.y + t.h / 2, t.w / 2 + 5, 0, Math.PI * 2);
			ctx.stroke();
		}
	}

	// Draw Base (Single 32x32 Composite Entity at columns 12..13, rows 23..24)
	function drawBase() {
		var bx = 12 * TILE_SIZE;
		var by = 23 * TILE_SIZE;
		var bw = TILE_SIZE * 2; // 32px
		var bh = TILE_SIZE * 2; // 32px

		if (!baseDestroyed) {
			// Alive Golden EXS Base
			// Outer beveled frame
			ctx.fillStyle = '#8c6b00';
			ctx.fillRect(bx, by, bw, bh);

			ctx.fillStyle = '#f5b300';
			ctx.fillRect(bx + 1, by + 1, bw - 2, bh - 2);

			ctx.fillStyle = '#d49600';
			ctx.fillRect(bx + 3, by + 3, bw - 6, bh - 6);

			// Inner Shield / Crest Plate
			ctx.fillStyle = '#0f141c';
			ctx.fillRect(bx + 4, by + 4, bw - 8, bh - 8);

			// Eagle Wings / Golden Crest
			ctx.fillStyle = '#ffd54f';
			ctx.beginPath();
			ctx.moveTo(bx + 6, by + 7);
			ctx.lineTo(bx + bw / 2, by + 12);
			ctx.lineTo(bx + bw - 6, by + 7);
			ctx.lineTo(bx + bw - 8, by + 13);
			ctx.lineTo(bx + bw / 2, by + 15);
			ctx.lineTo(bx + 8, by + 13);
			ctx.closePath();
			ctx.fill();

			// "EXS" Text in crisp monospace font
			ctx.fillStyle = '#ffffff';
			ctx.font = 'bold 9px monospace';
			ctx.textAlign = 'center';
			ctx.textBaseline = 'middle';
			ctx.fillText('EXS', bx + bw / 2, by + 21);

			// Star at top
			drawStar(bx + bw / 2, by + 8, 5, 3, 1.5, '#ffffff', null);
		} else {
			// Ruined / Destroyed Base (Skull on Rubble)
			ctx.fillStyle = '#1c2024';
			ctx.fillRect(bx, by, bw, bh);

			// Scorched debris
			ctx.fillStyle = '#37474f';
			ctx.fillRect(bx + 2, by + 2, bw - 4, bh - 4);
			ctx.fillStyle = '#263238';
			ctx.fillRect(bx + 4, by + 8, 10, 8);
			ctx.fillRect(bx + 16, by + 14, 12, 6);

			// Red/orange glowing embers
			ctx.fillStyle = '#ff5722';
			ctx.fillRect(bx + 6, by + 22, 3, 3);
			ctx.fillRect(bx + 22, by + 10, 2, 2);

			// Skull icon
			ctx.fillStyle = '#e53935';
			ctx.font = 'bold 16px sans-serif';
			ctx.textAlign = 'center';
			ctx.textBaseline = 'middle';
			ctx.fillText('☠', bx + bw / 2, by + bh / 2);
		}
	}

	// Star Helper Function
	function drawStar(cx, cy, spikes, outerRadius, innerRadius, fillCol, strokeCol) {
		var rot = (Math.PI / 2) * 3;
		var step = Math.PI / spikes;

		ctx.beginPath();
		ctx.moveTo(cx, cy - outerRadius);
		for (var i = 0; i < spikes; i++) {
			var x = cx + Math.cos(rot) * outerRadius;
			var y = cy + Math.sin(rot) * outerRadius;
			ctx.lineTo(x, y);
			rot += step;

			x = cx + Math.cos(rot) * innerRadius;
			y = cy + Math.sin(rot) * innerRadius;
			ctx.lineTo(x, y);
			rot += step;
		}
		ctx.lineTo(cx, cy - outerRadius);
		ctx.closePath();
		ctx.fillStyle = fillCol;
		ctx.fill();
		if (strokeCol) {
			ctx.strokeStyle = strokeCol;
			ctx.lineWidth = 1;
			ctx.stroke();
		}
	}

	// Powerup Drawing (Authentic Canvas Vector Graphics)
	function drawPowerup(pow) {
		// Blink on/off during last 3 seconds (timer < 180)
		if (pow.timer < 180 && Math.floor(pow.timer / 12) % 2 === 0) return;

		var px = pow.x;
		var py = pow.y;
		var pw = pow.w || 28;
		var ph = pow.h || 28;
		var cx = px + pw / 2;
		var cy = py + ph / 2;

		// Arcade badge background: dark box with animated retro border
		var borderBlink = Math.floor(Date.now() / 200) % 2 === 0;
		ctx.fillStyle = '#10141d';
		ctx.fillRect(px, py, pw, ph);

		ctx.lineWidth = 2;
		ctx.strokeStyle = borderBlink ? '#f5b300' : '#ffffff';
		ctx.strokeRect(px + 1, py + 1, pw - 2, ph - 2);

		ctx.save();

		if (pow.type === POWERUP_TYPE.STAR) {
			// Star: Golden 5-point star with bright core
			drawStar(cx, cy, 5, 9, 4, '#ffd54f', '#ff8f00');
		} else if (pow.type === POWERUP_TYPE.BOMB) {
			// Bomb: Spherical bomb with fuse and spark
			ctx.fillStyle = '#212121';
			ctx.beginPath();
			ctx.arc(cx, cy + 2, 7, 0, Math.PI * 2);
			ctx.fill();
			ctx.strokeStyle = '#eeeeee';
			ctx.lineWidth = 1;
			ctx.stroke();
			// Specular highlight
			ctx.fillStyle = '#ffffff';
			ctx.fillRect(cx - 3, cy - 1, 2, 2);
			// Fuse cap
			ctx.fillStyle = '#78909c';
			ctx.fillRect(cx - 2, cy - 7, 4, 3);
			// Burning fuse spark
			ctx.fillStyle = '#ff5722';
			ctx.beginPath();
			ctx.arc(cx + 2, cy - 8, 2.5, 0, Math.PI * 2);
			ctx.fill();
			ctx.fillStyle = '#ffeb3b';
			ctx.fillRect(cx + 2, cy - 8, 2, 2);
		} else if (pow.type === POWERUP_TYPE.CLOCK) {
			// Clock: White circle with clock hands and top bells
			ctx.fillStyle = '#eceff1';
			ctx.beginPath();
			ctx.arc(cx, cy + 1, 8, 0, Math.PI * 2);
			ctx.fill();
			ctx.strokeStyle = '#0288d1';
			ctx.lineWidth = 1.5;
			ctx.stroke();
			// Top bell pegs
			ctx.fillStyle = '#0288d1';
			ctx.fillRect(cx - 6, cy - 8, 2, 2);
			ctx.fillRect(cx + 4, cy - 8, 2, 2);
			// Clock hands (pointing to 10:10)
			ctx.strokeStyle = '#c62828';
			ctx.lineWidth = 1.5;
			ctx.beginPath();
			ctx.moveTo(cx, cy + 1);
			ctx.lineTo(cx, cy - 4);
			ctx.moveTo(cx, cy + 1);
			ctx.lineTo(cx + 4, cy + 1);
			ctx.stroke();
		} else if (pow.type === POWERUP_TYPE.HELMET) {
			// Helmet / Shield: Blue glowing knight shield
			ctx.fillStyle = '#1e88e5';
			ctx.beginPath();
			ctx.moveTo(cx, cy - 8);
			ctx.lineTo(cx + 8, cy - 4);
			ctx.lineTo(cx + 6, cy + 5);
			ctx.lineTo(cx, cy + 9);
			ctx.lineTo(cx - 6, cy + 5);
			ctx.lineTo(cx - 8, cy - 4);
			ctx.closePath();
			ctx.fill();
			ctx.strokeStyle = '#e3f2fd';
			ctx.lineWidth = 1.5;
			ctx.stroke();
			// Inner emblem
			ctx.fillStyle = '#ffffff';
			ctx.fillRect(cx - 1, cy - 4, 2, 7);
			ctx.fillRect(cx - 3, cy - 2, 6, 2);
		} else if (pow.type === POWERUP_TYPE.SHOVEL) {
			// Shovel: Metallic spade head with wooden shaft
			ctx.strokeStyle = '#8d6e63';
			ctx.lineWidth = 2.5;
			ctx.beginPath();
			ctx.moveTo(cx + 6, cy - 7);
			ctx.lineTo(cx - 2, cy + 2);
			ctx.stroke();
			// Handle grip
			ctx.fillStyle = '#5d4037';
			ctx.fillRect(cx + 4, cy - 8, 4, 3);
			// Spade blade
			ctx.fillStyle = '#cfd8dc';
			ctx.beginPath();
			ctx.moveTo(cx - 1, cy);
			ctx.lineTo(cx - 7, cy + 6);
			ctx.lineTo(cx - 4, cy + 9);
			ctx.lineTo(cx + 2, cy + 3);
			ctx.closePath();
			ctx.fill();
			ctx.strokeStyle = '#90a4ae';
			ctx.lineWidth = 1;
			ctx.stroke();
		} else if (pow.type === POWERUP_TYPE.TANK) {
			// Extra Tank (1UP): Green mini tank with turret
			ctx.fillStyle = '#43a047';
			// Tracks
			ctx.fillRect(cx - 8, cy - 6, 4, 12);
			ctx.fillRect(cx + 4, cy - 6, 4, 12);
			// Body
			ctx.fillStyle = '#66bb6a';
			ctx.fillRect(cx - 4, cy - 4, 8, 9);
			// Turret
			ctx.fillStyle = '#2e7d32';
			ctx.beginPath();
			ctx.arc(cx, cy, 3, 0, Math.PI * 2);
			ctx.fill();
			// Cannon barrel pointing up
			ctx.fillStyle = '#1b5e20';
			ctx.fillRect(cx - 1, cy - 8, 2, 5);
		}

		ctx.restore();
	}

	// Stage Cleared Event
	function stageCleared() {
		isRunning = false;
		SoundEngine.stageStart();

		var overlay = document.getElementById('tanki-stage-overlay');
		var title = document.getElementById('stage-cleared-title');
		if (title) title.textContent = 'LĪMENIS ' + stage + ' PABEIGTS';

		// Tally counts
		document.getElementById('tally-basic-count').textContent = stageTanksKilled.basic;
		document.getElementById('tally-basic-pts').textContent = stageTanksKilled.basic * 100;

		document.getElementById('tally-fast-count').textContent = stageTanksKilled.fast;
		document.getElementById('tally-fast-pts').textContent = stageTanksKilled.fast * 200;

		document.getElementById('tally-power-count').textContent = stageTanksKilled.power;
		document.getElementById('tally-power-pts').textContent = stageTanksKilled.power * 300;

		document.getElementById('tally-armor-count').textContent = stageTanksKilled.armor;
		document.getElementById('tally-armor-pts').textContent = stageTanksKilled.armor * 400;

		var stageTotalKills = stageTanksKilled.basic + stageTanksKilled.fast + stageTanksKilled.power + stageTanksKilled.armor;
		document.getElementById('tally-total-tanks').textContent = stageTotalKills;
		document.getElementById('tally-total-score').textContent = score;

		if (overlay) overlay.style.display = 'flex';
	}

	function nextStage() {
		var overlay = document.getElementById('tanki-stage-overlay');
		if (overlay) overlay.style.display = 'none';

		stage++;
		stageEnemiesRemaining = 20;
		stageEnemiesSpawned = 0;
		stageTanksKilled = { basic: 0, fast: 0, power: 0, armor: 0 };
		enemies = [];
		bullets = [];
		powerups = [];
		particles = [];

		loadStageMap(stage);
		if (player) {
			player.x = 9 * TILE_SIZE + 3;
			player.y = 24 * TILE_SIZE + 3;
			player.dir = DIR.UP;
			player.shieldTimer = 180;
		} else {
			player = new PlayerTank();
		}

		isRunning = true;
		updateHUD();
		updateQueueHUD();
		requestAnimationFrame(gameLoop);
	}

	// Game Over Event
	function gameOver(reason) {
		isRunning = false;
		SoundEngine.gameOver();

		var duration = Math.floor((Date.now() - gameStartTime) / 1000);

		var overlay = document.getElementById('tanki-gameover-overlay');
		var reasonEl = document.getElementById('gameover-reason');
		var finalScore = document.getElementById('tanki-final-score');
		var finalStage = document.getElementById('tanki-final-stage');
		var finalKills = document.getElementById('tanki-final-kills');

		if (reasonEl) reasonEl.textContent = reason || 'Spēle beigusies!';
		if (finalScore) finalScore.textContent = score;
		if (finalStage) finalStage.textContent = stage;
		if (finalKills) finalKills.textContent = totalTanksKilled;

		if (overlay) overlay.style.display = 'flex';

		// Submit score to backend
		submitScore(score, stage, duration);
	}

	// Score Submission
	function submitScore(finalScore, finalStage, duration) {
		if (finalScore <= 0 || !sessionToken) return;

		var formData = new FormData();
		formData.append('score', finalScore);
		formData.append('stage', finalStage);
		formData.append('duration', duration);
		formData.append('token', sessionToken);

		fetch('/tanki?action=push', {
			method: 'POST',
			body: formData
		})
		.then(function (res) { return res.json(); })
		.then(function (data) {
			if (data.success) {
				if (data.isNewRecord) {
					var recAlert = document.getElementById('tanki-record-alert');
					if (recAlert) recAlert.style.display = 'block';
				}
				var bestEl = document.getElementById('tanki-best-score');
				if (bestEl && data.highScore) bestEl.textContent = data.highScore;
			}
		})
		.catch(function () {});
	}

	// Token Fetching
	function initSessionToken(callback) {
		fetch('/tanki?action=init_token')
			.then(function (res) { return res.json(); })
			.then(function (data) {
				if (data.success) {
					sessionToken = data.token;
					if (callback) callback();
				}
			})
			.catch(function () {
				if (callback) callback();
			});
	}

	// HUD Updates
	function updateHUD() {
		var elScore = document.getElementById('stat-score');
		var elStage = document.getElementById('stat-stage');
		var elLives = document.getElementById('stat-lives');
		var elSideLives = document.getElementById('side-lives-count');
		var elSideStage = document.getElementById('side-stage-count');
		var elSideTier = document.getElementById('side-tier-count');

		if (elScore) elScore.textContent = score;
		if (elStage) elStage.textContent = stage;
		if (elSideStage) elSideStage.textContent = stage;
		if (elSideLives) elSideLives.textContent = playerLives;
		if (elSideTier && player) elSideTier.textContent = player.tier;

		if (elLives) {
			var html = '';
			for (var i = 0; i < playerLives; i++) {
				html += '<span class="tank-life-icon"></span>';
			}
			elLives.innerHTML = html;
		}
	}

	function updateQueueHUD() {
		var container = document.getElementById('enemy-queue-container');
		if (!container) return;

		var html = '';
		for (var i = 0; i < 20; i++) {
			var isDead = (i >= stageEnemiesRemaining);
			html += '<div class="enemy-pip' + (isDead ? ' dead' : '') + '"></div>';
		}
		container.innerHTML = html;
	}

	// Start / Restart Game
	function startGame() {
		SoundEngine.init();
		SoundEngine.stageStart();

		var startOverlay = document.getElementById('tanki-start-overlay');
		var overOverlay = document.getElementById('tanki-gameover-overlay');
		var stageOverlay = document.getElementById('tanki-stage-overlay');

		if (startOverlay) startOverlay.style.display = 'none';
		if (overOverlay) overOverlay.style.display = 'none';
		if (stageOverlay) stageOverlay.style.display = 'none';

		stage = 1;
		score = 0;
		playerLives = 3;
		totalTanksKilled = 0;
		stageTanksKilled = { basic: 0, fast: 0, power: 0, armor: 0 };
		stageEnemiesRemaining = 20;
		stageEnemiesSpawned = 0;
		enemies = [];
		bullets = [];
		powerups = [];
		particles = [];
		gameStartTime = Date.now();

		loadStageMap(stage);
		player = new PlayerTank();

		updateHUD();
		updateQueueHUD();

		initSessionToken(function () {
			isRunning = true;
			isPaused = false;
			requestAnimationFrame(gameLoop);
		});
	}

	// Pause Toggle
	function togglePause() {
		if (!isRunning) return;
		isPaused = !isPaused;

		var pauseOverlay = document.getElementById('tanki-pause-overlay');
		var pauseBtn = document.getElementById('tanki-pause-btn');

		if (pauseOverlay) {
			pauseOverlay.style.display = isPaused ? 'flex' : 'none';
		}
		if (pauseBtn) {
			pauseBtn.textContent = isPaused ? '▶' : '⏸';
		}
	}

	// Event Handlers Setup
	function setupEvents() {
		// Keyboard Controls
		window.addEventListener('keydown', function (e) {
			SoundEngine.init();

			if (e.key === 'ArrowUp' || e.key === 'w' || e.key === 'W') {
				keys.up = true;
				e.preventDefault();
			} else if (e.key === 'ArrowDown' || e.key === 's' || e.key === 'S') {
				keys.down = true;
				e.preventDefault();
			} else if (e.key === 'ArrowLeft' || e.key === 'a' || e.key === 'A') {
				keys.left = true;
				e.preventDefault();
			} else if (e.key === 'ArrowRight' || e.key === 'd' || e.key === 'D') {
				keys.right = true;
				e.preventDefault();
			} else if (e.key === ' ' || e.key === 'j' || e.key === 'J' || e.key === 'Enter') {
				keys.fire = true;
				e.preventDefault();

				// Overlay shortcuts
				if (!isRunning) {
					var stageOverlay = document.getElementById('tanki-stage-overlay');
					if (stageOverlay && stageOverlay.style.display === 'flex') {
						nextStage();
					} else {
						startGame();
					}
				}
			} else if (e.key === 'p' || e.key === 'P') {
				togglePause();
				e.preventDefault();
			} else if (e.key === 'm' || e.key === 'M') {
				var on = SoundEngine.toggle();
				var btn = document.getElementById('tanki-sound-btn');
				if (btn) btn.textContent = on ? '🔊' : '🔇';
				e.preventDefault();
			}
		});

		window.addEventListener('keyup', function (e) {
			if (e.key === 'ArrowUp' || e.key === 'w' || e.key === 'W') keys.up = false;
			else if (e.key === 'ArrowDown' || e.key === 's' || e.key === 'S') keys.down = false;
			else if (e.key === 'ArrowLeft' || e.key === 'a' || e.key === 'A') keys.left = false;
			else if (e.key === 'ArrowRight' || e.key === 'd' || e.key === 'D') keys.right = false;
			else if (e.key === ' ' || e.key === 'j' || e.key === 'J' || e.key === 'Enter') keys.fire = false;
		});

		// UI Button Listeners
		var startBtn = document.getElementById('tanki-start-btn');
		if (startBtn) startBtn.addEventListener('click', startGame);

		var restartBtn = document.getElementById('tanki-restart-btn');
		if (restartBtn) restartBtn.addEventListener('click', startGame);

		var nextStageBtn = document.getElementById('tanki-next-stage-btn');
		if (nextStageBtn) nextStageBtn.addEventListener('click', nextStage);

		var resumeBtn = document.getElementById('tanki-resume-btn');
		if (resumeBtn) resumeBtn.addEventListener('click', togglePause);

		var pauseBtn = document.getElementById('tanki-pause-btn');
		if (pauseBtn) pauseBtn.addEventListener('click', togglePause);

		var soundBtn = document.getElementById('tanki-sound-btn');
		if (soundBtn) {
			soundBtn.addEventListener('click', function () {
				var on = SoundEngine.toggle();
				soundBtn.textContent = on ? '🔊' : '🔇';
			});
		}

		// Virtual D-Pad for Mobile
		function bindTouch(id, keyName) {
			var btn = document.getElementById(id);
			if (!btn) return;
			btn.addEventListener('touchstart', function (e) {
				SoundEngine.init();
				keys[keyName] = true;
				e.preventDefault();
			});
			btn.addEventListener('touchend', function (e) {
				keys[keyName] = false;
				e.preventDefault();
			});
			btn.addEventListener('touchcancel', function (e) {
				keys[keyName] = false;
			});
		}

		bindTouch('touch-up', 'up');
		bindTouch('touch-down', 'down');
		bindTouch('touch-left', 'left');
		bindTouch('touch-right', 'right');
		bindTouch('touch-fire', 'fire');

		// Leaderboard tabs
		var tabToday = document.getElementById('tab-today');
		var tabAlltime = document.getElementById('tab-alltime');
		var contentToday = document.getElementById('content-today');
		var contentAlltime = document.getElementById('content-alltime');

		if (tabToday && tabAlltime && contentToday && contentAlltime) {
			tabToday.addEventListener('click', function () {
				tabToday.classList.add('active');
				tabAlltime.classList.remove('active');
				contentToday.classList.add('active');
				contentAlltime.classList.remove('active');
			});
			tabAlltime.addEventListener('click', function () {
				tabAlltime.classList.add('active');
				tabToday.classList.remove('active');
				contentAlltime.classList.add('active');
				contentToday.classList.remove('active');
			});
		}
	}

	// Initialization on DOMContentLoaded
	document.addEventListener('DOMContentLoaded', function () {
		canvas = document.getElementById('tanki-canvas');
		if (!canvas) return;
		ctx = canvas.getContext('2d');

		setupEvents();
		updateQueueHUD();
		loadStageMap(1);
		render();
	});

})();
