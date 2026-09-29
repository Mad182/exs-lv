/**
 * EXS.LV - Pac-Man (Exs-Man) Game Engine
 * Authentic Arcade Rules, Level Progression, Ghost AI & Player Avatar Character
 */

(function($) {
	'use strict';

	// Wait for DOM ready
	$(function() {
		var canvas = document.getElementById('pacman-canvas');
		if (!canvas) return;
		var ctx = canvas.getContext('2d');

		// Grid & Resolution Constants
		var COLS = 28;
		var ROWS = 36;
		var TILE = 16; // 28 * 16 = 448px, 36 * 16 = 576px
		canvas.width = COLS * TILE;
		canvas.height = ROWS * TILE;

		// Direction Enums
		var DIR_NONE  = 0;
		var DIR_UP    = 1;
		var DIR_LEFT  = 2;
		var DIR_DOWN  = 3;
		var DIR_RIGHT = 4;

		var DIRS = [
			{ x: 0, y: 0 },
			{ x: 0, y: -1 }, // UP
			{ x: -1, y: 0 }, // LEFT
			{ x: 0, y: 1 },  // DOWN
			{ x: 1, y: 0 }   // RIGHT
		];

		// Game States
		var STATE_START    = 0;
		var STATE_READY    = 1;
		var STATE_PLAYING  = 2;
		var STATE_FRIGHT   = 3;
		var STATE_EATGHOST = 4;
		var STATE_DYING    = 5;
		var STATE_GAMEOVER = 6;
		var STATE_LEVELWIN = 7;
		var STATE_PAUSED   = 8;

		var gameState = STATE_START;
		var prevPlayingState = STATE_PLAYING;

		// Authentic 28x36 Map Layout
		var RAW_MAP = [
			"                            ", // 0
			"                            ", // 1
			"                            ", // 2
			"############################", // 3
			"#[EMAIL ADDRESS]#[EMAIL ADDRESS]#", // 4
			"#.####.#####.##.#####.####.#", // 5
			"#@#  #.#   #.##.#   #.#  #@#", // 6
			"#.####.#####.##.#####.####.#", // 7
			"#..........................#", // 8
			"#.####.##.########.##.####.#", // 9
			"#.####.##.########.##.####.#", // 10
			"#......##....##....##......#", // 11
			"######.##### ## #####.######", // 12
			"     #.##### ## #####.#     ", // 13
			"     #.##          ##.#     ", // 14
			"     #.## ###--### ##.#     ", // 15
			"######.## #------# ##.######", // 16
			"      .   #------#   .      ", // 17 (tunnel)
			"######.## #------# ##.######", // 18
			"     #.## ######## ##.#     ", // 19
			"     #.##          ##.#     ", // 20
			"     #.## ######## ##.#     ", // 21
			"######.## ######## ##.######", // 22
			"#[EMAIL ADDRESS]#[EMAIL ADDRESS]#", // 23
			"#.####.#####.##.#####.####.#", // 24
			"#.####.#####.##.#####.####.#", // 25
			"#@..##.......  .......##..@#", // 26
			"###.##.##.########.##.##.###", // 27
			"###.##.##.########.##.##.###", // 28
			"#......##....##....##......#", // 29
			"#.##########.##.##########.#", // 30
			"#.##########.##.##########.#", // 31
			"#..........................#", // 32
			"############################", // 33
			"                            ", // 34
			"                            "  // 35
		];

		// Parsed grid: 0: empty, 1: wall, 2: dot, 3: energizer, 4: door
		var grid = [];
		var totalDots = 0;
		var dotsRemaining = 0;
		var dotsEatenRound = 0;

		function initGrid() {
			grid = [];
			totalDots = 0;
			for (var r = 0; r < ROWS; r++) {
				var rowStr = RAW_MAP[r] || "                            ";
				var row = [];
				for (var c = 0; c < COLS; c++) {
					var ch = rowStr.charAt(c);
					if (ch === '#') {
						row.push(1); // wall
					} else if (ch === '.') {
						row.push(2); // dot
						totalDots++;
					} else if (ch === '@') {
						row.push(3); // energizer
						totalDots++;
					} else if (ch === '-') {
						row.push(4); // ghost door
					} else {
						row.push(0); // empty
					}
				}
				grid.push(row);
			}
			dotsRemaining = totalDots;
		}

		// Game Scores & Tracking
		var score = 0;
		var highScore = window.PACMAN_USER_HIGHSCORE || 0;
		var level = 1;
		var lives = 3;
		var extraLifeAwarded = false;
		var gameStartTime = 0;
		var currentToken = null;
		var soundEnabled = (localStorage.getItem('exs_pacman_sound') !== 'false');

		// Avatar Image
		var avatarImg = new Image();
		var avatarLoaded = false;
		if (window.PACMAN_USER_AVATAR && window.PACMAN_USER_AVATAR.length > 0) {
			avatarImg.crossOrigin = 'Anonymous';
			avatarImg.onload = function() { avatarLoaded = true; };
			avatarImg.onerror = function() { avatarLoaded = false; };
			avatarImg.src = window.PACMAN_USER_AVATAR;
		}

		// Audio Synthesizer via Web Audio API
		var audioCtx = null;
		function initAudio() {
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

		var lastWakaPitch = false;
		function playSound(type) {
			if (!soundEnabled || !audioCtx) return;
			try {
				var now = audioCtx.currentTime;
				var osc, gain;

				if (type === 'waka') {
					osc = audioCtx.createOscillator();
					gain = audioCtx.createGain();
					osc.connect(gain);
					gain.connect(audioCtx.destination);
					osc.type = 'triangle';
					var freq = lastWakaPitch ? 480 : 320;
					lastWakaPitch = !lastWakaPitch;
					osc.frequency.setValueAtTime(freq, now);
					osc.frequency.exponentialRampToValueAtTime(freq * 0.7, now + 0.08);
					gain.gain.setValueAtTime(0.12, now);
					gain.gain.linearRampToValueAtTime(0.01, now + 0.08);
					osc.start(now);
					osc.stop(now + 0.08);
				} else if (type === 'eat_ghost') {
					osc = audioCtx.createOscillator();
					gain = audioCtx.createGain();
					osc.connect(gain);
					gain.connect(audioCtx.destination);
					osc.type = 'sawtooth';
					osc.frequency.setValueAtTime(200, now);
					osc.frequency.exponentialRampToValueAtTime(800, now + 0.2);
					gain.gain.setValueAtTime(0.25, now);
					gain.gain.linearRampToValueAtTime(0.01, now + 0.25);
					osc.start(now);
					osc.stop(now + 0.25);
				} else if (type === 'fruit') {
					var notes = [523.25, 659.25, 783.99, 1046.50];
					notes.forEach(function(freq, idx) {
						var o = audioCtx.createOscillator();
						var g = audioCtx.createGain();
						o.connect(g);
						g.connect(audioCtx.destination);
						o.type = 'sine';
						o.frequency.setValueAtTime(freq, now + idx * 0.06);
						g.gain.setValueAtTime(0.2, now + idx * 0.06);
						g.gain.linearRampToValueAtTime(0.01, now + idx * 0.06 + 0.12);
						o.start(now + idx * 0.06);
						o.stop(now + idx * 0.06 + 0.12);
					});
				} else if (type === 'death') {
					osc = audioCtx.createOscillator();
					gain = audioCtx.createGain();
					osc.connect(gain);
					gain.connect(audioCtx.destination);
					osc.type = 'sawtooth';
					osc.frequency.setValueAtTime(600, now);
					osc.frequency.exponentialRampToValueAtTime(60, now + 0.65);
					gain.gain.setValueAtTime(0.3, now);
					gain.gain.linearRampToValueAtTime(0.01, now + 0.7);
					osc.start(now);
					osc.stop(now + 0.7);
				} else if (type === 'extra_life') {
					var chime = [587.33, 880, 1174.66];
					chime.forEach(function(f, i) {
						var o = audioCtx.createOscillator();
						var g = audioCtx.createGain();
						o.connect(g);
						g.connect(audioCtx.destination);
						o.type = 'triangle';
						o.frequency.setValueAtTime(f, now + i * 0.1);
						g.gain.setValueAtTime(0.25, now + i * 0.1);
						g.gain.linearRampToValueAtTime(0.01, now + i * 0.1 + 0.25);
						o.start(now + i * 0.1);
						o.stop(now + i * 0.1 + 0.25);
					});
				} else if (type === 'intro') {
					// Classic Pac-Man start arpeggio
					var introNotes = [
						{f: 493.88, d: 0.12}, {f: 987.77, d: 0.12}, {f: 739.99, d: 0.12}, {f: 622.25, d: 0.12},
						{f: 987.77, d: 0.12}, {f: 739.99, d: 0.12}, {f: 622.25, d: 0.18},
						{f: 523.25, d: 0.12}, {f: 1046.50, d: 0.12}, {f: 783.99, d: 0.12}, {f: 659.25, d: 0.12},
						{f: 1046.50, d: 0.12}, {f: 783.99, d: 0.12}, {f: 659.25, d: 0.18}
					];
					var t = now;
					introNotes.forEach(function(item) {
						var o = audioCtx.createOscillator();
						var g = audioCtx.createGain();
						o.connect(g);
						g.connect(audioCtx.destination);
						o.type = 'triangle';
						o.frequency.setValueAtTime(item.f, t);
						g.gain.setValueAtTime(0.2, t);
						g.gain.linearRampToValueAtTime(0.01, t + item.d);
						o.start(t);
						o.stop(t + item.d);
						t += item.d;
					});
				}
			} catch (e) {}
		}

		// Fruit Table & Level Progression
		var FRUIT_TABLE = [
			{ name: 'Cherry', symbol: '🍒', score: 100, frightTime: 6000 },     // L1
			{ name: 'Strawberry', symbol: '🍓', score: 300, frightTime: 5000 }, // L2
			{ name: 'Orange', symbol: '🍊', score: 500, frightTime: 4000 },     // L3
			{ name: 'Orange', symbol: '🍊', score: 500, frightTime: 3000 },     // L4
			{ name: 'Apple', symbol: '🍏', score: 700, frightTime: 2000 },      // L5
			{ name: 'Apple', symbol: '🍏', score: 700, frightTime: 5000 },      // L6
			{ name: 'Melon', symbol: '🍈', score: 1000, frightTime: 2000 },     // L7
			{ name: 'Melon', symbol: '🍈', score: 1000, frightTime: 2000 },     // L8
			{ name: 'Galaxian', symbol: '🛸', score: 2000, frightTime: 1000 },  // L9
			{ name: 'Galaxian', symbol: '🛸', score: 2000, frightTime: 5000 },  // L10
			{ name: 'Bell', symbol: '🔔', score: 3000, frightTime: 2000 },      // L11
			{ name: 'Bell', symbol: '🔔', score: 3000, frightTime: 1000 },      // L12
			{ name: 'Key', symbol: '🗝️', score: 5000, frightTime: 1000 }        // L13+
		];

		function getFruitForLevel(lvl) {
			var idx = Math.min(lvl, FRUIT_TABLE.length) - 1;
			return FRUIT_TABLE[idx];
		}

		// Fruit in play
		var fruitActive = false;
		var fruitTimer = 0;
		var fruitX = 13.5 * TILE;
		var fruitY = 20 * TILE;

		// Pac-Man Player Object
		var pacman = {
			x: 13.5 * TILE,
			y: 26 * TILE,
			dir: DIR_LEFT,
			nextDir: DIR_LEFT,
			speed: 2.1,
			radius: 12,
			mouthAngle: 0.2,
			mouthDelta: 0.04,
			isDying: false,
			deathAngle: 0,
			reset: function() {
				this.x = 13.5 * TILE;
				this.y = 26 * TILE;
				this.dir = DIR_LEFT;
				this.nextDir = DIR_LEFT;
				this.speed = Math.min(2.1 + (level - 1) * 0.05, 2.6);
				this.mouthAngle = 0.2;
				this.mouthDelta = 0.04;
				this.isDying = false;
				this.deathAngle = 0;
			}
		};

		// Ghosts Definitions
		var ghosts = [];
		var GHOST_BLINKY = 0;
		var GHOST_PINKY  = 1;
		var GHOST_INKY   = 2;
		var GHOST_CLYDE  = 3;

		var ghostColors = [
			'#ff3333', // Blinky - Red
			'#ff99cc', // Pinky - Pink
			'#00e5ff', // Inky - Cyan
			'#ffaa00'  // Clyde - Orange
		];

		var ghostsEatenStreak = 0;
		var frightTimer = 0;
		var frightDuration = 6000;
		var flashTimer = 0;
		var globalTimer = 0;

		// Scatter vs Chase waves timing
		var waveTimer = 0;
		var currentWave = 0;
		var isScatter = true;

		function initGhosts() {
			ghosts = [
				{
					id: GHOST_BLINKY,
					name: 'Blinky',
					color: ghostColors[0],
					x: 13.5 * TILE,
					y: 14 * TILE,
					dir: DIR_LEFT,
					targetX: 0,
					targetY: 0,
					mode: 'chase', // 'chase', 'scatter', 'fright', 'eyes', 'house'
					speed: 1.9,
					inHouse: false,
					scatterTarget: { x: 25, y: -3 },
					animTimer: 0
				},
				{
					id: GHOST_PINKY,
					name: 'Pinky',
					color: ghostColors[1],
					x: 13.5 * TILE,
					y: 17 * TILE,
					dir: DIR_UP,
					targetX: 0,
					targetY: 0,
					mode: 'house',
					speed: 1.85,
					inHouse: true,
					exitDelay: 60, // frames until exit
					scatterTarget: { x: 2, y: -3 },
					animTimer: 0
				},
				{
					id: GHOST_INKY,
					name: 'Inky',
					color: ghostColors[2],
					x: 11.5 * TILE,
					y: 17 * TILE,
					dir: DIR_UP,
					targetX: 0,
					targetY: 0,
					mode: 'house',
					speed: 1.85,
					inHouse: true,
					exitDelay: 180, // frames or 30 dots
					scatterTarget: { x: 27, y: 34 },
					animTimer: 0
				},
				{
					id: GHOST_CLYDE,
					name: 'Clyde',
					color: ghostColors[3],
					x: 15.5 * TILE,
					y: 17 * TILE,
					dir: DIR_UP,
					targetX: 0,
					targetY: 0,
					mode: 'house',
					speed: 1.85,
					inHouse: true,
					exitDelay: 320, // frames or 60 dots
					scatterTarget: { x: 0, y: 34 },
					animTimer: 0
				}
			];
		}

		function resetPositions() {
			pacman.reset();
			initGhosts();
			fruitActive = false;
			fruitTimer = 0;
			waveTimer = 0;
			currentWave = 0;
			isScatter = true;
		}

		// Tile Helpers
		function getTileAt(x, y) {
			var col = Math.floor(x / TILE);
			var row = Math.floor(y / TILE);
			if (row < 0 || row >= ROWS || col < 0 || col >= COLS) return 1;
			return grid[row][col];
		}

		function isWalkable(col, row, isGhost, isEyes, isInsideHouse) {
			if (row < 0 || row >= ROWS) return false;
			// Side warp tunnel
			if (row === 17 && (col < 0 || col >= COLS)) return true;
			if (col < 0 || col >= COLS) return false;

			var val = grid[row][col];
			if (val === 1) return false; // Wall
			if (val === 4) {
				// Ghost door: passable by ghosts when exiting/entering, or eyes
				return isGhost && (isEyes || isInsideHouse);
			}
			return true;
		}

		// Security Token Fetching
		function fetchToken(cb) {
			$.getJSON('/pacman?action=init_token', function(res) {
				if (res && res.success) {
					currentToken = res.token;
				}
				if (cb) cb();
			});
		}

		// Score Submission
		function submitScore() {
			if (score <= 0) return;
			var scoreToSend = score;
			var tokenToSend = currentToken || '';
			var durationToSend = Math.round((Date.now() - gameStartTime) / 1000);
			var levelToSend = level;

			currentToken = null;

			$.ajax({
				url: '/pacman?action=push',
				type: 'POST',
				data: {
					token: tokenToSend,
					score: scoreToSend,
					level: levelToSend,
					duration: durationToSend
				},
				dataType: 'json',
				success: function(res) {
					if (res && res.success) {
						if (res.isNewRecord) {
							$('#record-alert').show();
							$('#final-best').text(res.highScore);
							$('#stat-highscore').text(res.highScore);
							highScore = res.highScore;
						}
					}
				}
			});
		}

		// Update HUD DOM
		function updateHUD() {
			$('#stat-score').text(score);
			$('#stat-level').text(level);
			$('#stat-highscore').text(Math.max(score, highScore));

			var livesHtml = '';
			for (var i = 0; i < lives; i++) {
				livesHtml += '<span class="pacman-life-icon"></span>';
			}
			$('#stat-lives').html(livesHtml);
		}

		// Eat Pause score popup
		var popupScore = {
			active: false,
			score: 0,
			x: 0,
			y: 0,
			timer: 0
		};

		// Level Flash effect
		var levelWinTimer = 0;

		// Input Controls
		function handleDirectionInput(dir) {
			initAudio();
			if (gameState === STATE_START) {
				startGame();
				return;
			}
			if (gameState === STATE_GAMEOVER) {
				restartGame();
				return;
			}
			if (gameState === STATE_PLAYING || gameState === STATE_FRIGHT) {
				pacman.nextDir = dir;
				// Immediate reverse if opposite
				if ((dir === DIR_UP && pacman.dir === DIR_DOWN) ||
					(dir === DIR_DOWN && pacman.dir === DIR_UP) ||
					(dir === DIR_LEFT && pacman.dir === DIR_RIGHT) ||
					(dir === DIR_RIGHT && pacman.dir === DIR_LEFT)) {
					pacman.dir = dir;
				}
			}
		}

		// Keyboard Bindings
		$(document).on('keydown', function(e) {
			var code = e.which || e.keyCode;
			if (code === 37 || code === 65) { // Left or A
				e.preventDefault();
				handleDirectionInput(DIR_LEFT);
			} else if (code === 38 || code === 87) { // Up or W
				e.preventDefault();
				handleDirectionInput(DIR_UP);
			} else if (code === 39 || code === 68) { // Right or D
				e.preventDefault();
				handleDirectionInput(DIR_RIGHT);
			} else if (code === 40 || code === 83) { // Down or S
				e.preventDefault();
				handleDirectionInput(DIR_DOWN);
			} else if (code === 32) { // Space
				e.preventDefault();
				if (gameState === STATE_START) startGame();
				else if (gameState === STATE_GAMEOVER) restartGame();
			} else if (code === 80) { // P (Pause)
				e.preventDefault();
				togglePause();
			} else if (code === 77) { // M (Mute)
				e.preventDefault();
				toggleSound();
			}
		});

		// Touch Buttons (D-Pad)
		$('#btn-up').on('touchstart mousedown', function(e) { e.preventDefault(); handleDirectionInput(DIR_UP); });
		$('#btn-left').on('touchstart mousedown', function(e) { e.preventDefault(); handleDirectionInput(DIR_LEFT); });
		$('#btn-right').on('touchstart mousedown', function(e) { e.preventDefault(); handleDirectionInput(DIR_RIGHT); });
		$('#btn-down').on('touchstart mousedown', function(e) { e.preventDefault(); handleDirectionInput(DIR_DOWN); });

		// Swipe Gestures on Canvas
		var touchStartX = 0;
		var touchStartY = 0;
		canvas.addEventListener('touchstart', function(e) {
			if (e.touches && e.touches.length > 0) {
				touchStartX = e.touches[0].clientX;
				touchStartY = e.touches[0].clientY;
			}
		}, { passive: true });

		canvas.addEventListener('touchend', function(e) {
			if (!touchStartX && !touchStartY) return;
			var touchEndX = e.changedTouches[0].clientX;
			var touchEndY = e.changedTouches[0].clientY;
			var dx = touchEndX - touchStartX;
			var dy = touchEndY - touchStartY;
			if (Math.hypot(dx, dy) > 20) {
				if (Math.abs(dx) > Math.abs(dy)) {
					handleDirectionInput(dx > 0 ? DIR_RIGHT : DIR_LEFT);
				} else {
					handleDirectionInput(dy > 0 ? DIR_DOWN : DIR_UP);
				}
			}
			touchStartX = 0;
			touchStartY = 0;
		}, { passive: true });

		// UI Buttons
		$('#pacman-start-btn').on('click', function() {
			initAudio();
			startGame();
		});

		$('#pacman-restart-btn').on('click', function() {
			initAudio();
			restartGame();
		});

		$('#pacman-resume-btn, #pacman-pause-btn').on('click', function() {
			initAudio();
			togglePause();
		});

		$('#pacman-sound-btn').on('click', function() {
			initAudio();
			toggleSound();
		});

		function toggleSound() {
			soundEnabled = !soundEnabled;
			localStorage.setItem('exs_pacman_sound', soundEnabled);
			$('#pacman-sound-btn').text(soundEnabled ? '🔊' : '🔇');
		}

		function togglePause() {
			if (gameState === STATE_PLAYING || gameState === STATE_FRIGHT) {
				prevPlayingState = gameState;
				gameState = STATE_PAUSED;
				$('#pacman-pause-overlay').fadeIn(150);
			} else if (gameState === STATE_PAUSED) {
				$('#pacman-pause-overlay').fadeOut(150);
				gameState = prevPlayingState;
			}
		}

		// Game Lifecycle
		function startGame() {
			initAudio();
			fetchToken();
			gameStartTime = Date.now();
			score = 0;
			level = 1;
			lives = 3;
			extraLifeAwarded = false;
			dotsEatenRound = 0;
			initGrid();
			resetPositions();
			$('#pacman-start-overlay').fadeOut(200);
			$('#pacman-gameover-overlay').hide();
			$('#record-alert').hide();
			readyRound();
		}

		function restartGame() {
			startGame();
		}

		var readyTimer = 0;
		function readyRound() {
			gameState = STATE_READY;
			readyTimer = 110; // frames (~1.8s)
			playSound('intro');
		}

		// Pac-Man Movement & Pre-Turn Cornering
		function updatePacman() {
			if (gameState !== STATE_PLAYING && gameState !== STATE_FRIGHT) return;

			// Cornering: Try to apply nextDir if aligned with tile grid
			var curCol = Math.floor(pacman.x / TILE);
			var curRow = Math.floor(pacman.y / TILE);
			var tileCenterX = curCol * TILE + TILE / 2;
			var tileCenterY = curRow * TILE + TILE / 2;
			var distToCenterX = Math.abs(pacman.x - tileCenterX);
			var distToCenterY = Math.abs(pacman.y - tileCenterY);

			if (pacman.nextDir !== pacman.dir && pacman.nextDir !== DIR_NONE) {
				var nextDirInfo = DIRS[pacman.nextDir];
				var targetCol = curCol + nextDirInfo.x;
				var targetRow = curRow + nextDirInfo.y;

				// Check if target tile in nextDir is walkable
				if (isWalkable(targetCol, targetRow, false, false, false)) {
					// Check alignment tolerance (within 4 pixels of tile center)
					var canTurn = false;
					if ((pacman.dir === DIR_LEFT || pacman.dir === DIR_RIGHT) && (pacman.nextDir === DIR_UP || pacman.nextDir === DIR_DOWN)) {
						if (distToCenterX <= 5) {
							pacman.x = tileCenterX;
							canTurn = true;
						}
					} else if ((pacman.dir === DIR_UP || pacman.dir === DIR_DOWN) && (pacman.nextDir === DIR_LEFT || pacman.nextDir === DIR_RIGHT)) {
						if (distToCenterY <= 5) {
							pacman.y = tileCenterY;
							canTurn = true;
						}
					} else {
						canTurn = true;
					}

					if (canTurn) {
						pacman.dir = pacman.nextDir;
					}
				}
			}

			// Move in current direction if not blocked by wall
			var dirInfo = DIRS[pacman.dir];
			if (pacman.dir !== DIR_NONE) {
				var frontCol = Math.floor((pacman.x + dirInfo.x * (TILE / 2 + 1)) / TILE);
				var frontRow = Math.floor((pacman.y + dirInfo.y * (TILE / 2 + 1)) / TILE);

				// Side warp tunnel
				if (curRow === 17) {
					if (pacman.x < -TILE / 2) {
						pacman.x = (COLS - 0.5) * TILE;
					} else if (pacman.x > (COLS - 0.5) * TILE) {
						pacman.x = -TILE / 2;
					}
				}

				if (isWalkable(frontCol, frontRow, false, false, false) || (curRow === 17 && (frontCol < 0 || frontCol >= COLS))) {
					pacman.x += dirInfo.x * pacman.speed;
					pacman.y += dirInfo.y * pacman.speed;

					// Animate mouth
					pacman.mouthAngle += pacman.mouthDelta;
					if (pacman.mouthAngle > 0.45 || pacman.mouthAngle < 0.05) {
						pacman.mouthDelta = -pacman.mouthDelta;
					}
				} else {
					// Snap to center when hitting wall
					if (dirInfo.x !== 0) pacman.x = tileCenterX;
					if (dirInfo.y !== 0) pacman.y = tileCenterY;
				}
			}

			// Check eating dots
			var centerCol = Math.floor(pacman.x / TILE);
			var centerRow = Math.floor(pacman.y / TILE);
			if (centerRow >= 0 && centerRow < ROWS && centerCol >= 0 && centerCol < COLS) {
				var tileVal = grid[centerRow][centerCol];
				if (tileVal === 2) { // Dot
					grid[centerRow][centerCol] = 0;
					score += 10;
					dotsRemaining--;
					dotsEatenRound++;
					playSound('waka');
					checkTriggers();
				} else if (tileVal === 3) { // Energizer
					grid[centerRow][centerCol] = 0;
					score += 50;
					dotsRemaining--;
					dotsEatenRound++;
					playSound('waka');
					triggerFrightenedMode();
					checkTriggers();
				}
			}

			// Check eating fruit
			if (fruitActive) {
				var distFruit = Math.hypot(pacman.x - fruitX, pacman.y - fruitY);
				if (distFruit < TILE) {
					var fObj = getFruitForLevel(level);
					score += fObj.score;
					fruitActive = false;
					playSound('fruit');
					popupScore = {
						active: true,
						score: fObj.score,
						x: fruitX,
						y: fruitY,
						timer: 50
					};
				}
			}
		}

		function checkTriggers() {
			// Fruit spawn at 70 and 170 dots eaten
			if (dotsEatenRound === 70 || dotsEatenRound === 170) {
				fruitActive = true;
				fruitTimer = 550; // ~9.2 seconds
			}

			// Extra life at 10,000 points
			if (score >= 10000 && !extraLifeAwarded) {
				extraLifeAwarded = true;
				lives++;
				playSound('extra_life');
				updateHUD();
			}

			// Round victory
			if (dotsRemaining <= 0) {
				levelComplete();
			}
		}

		function triggerFrightenedMode() {
			var fObj = getFruitForLevel(level);
			frightDuration = fObj.frightTime;
			if (frightDuration <= 0) {
				// Above level 16: ghosts simply reverse direction
				ghosts.forEach(function(g) {
					if (!g.inHouse && g.mode !== 'eyes') {
						reverseGhost(g);
					}
				});
				return;
			}

			ghostsEatenStreak = 0;
			frightTimer = frightDuration;
			gameState = STATE_FRIGHT;

			ghosts.forEach(function(g) {
				if (!g.inHouse && g.mode !== 'eyes') {
					g.mode = 'fright';
					reverseGhost(g);
				}
			});
		}

		function reverseGhost(g) {
			if (g.dir === DIR_UP) g.dir = DIR_DOWN;
			else if (g.dir === DIR_DOWN) g.dir = DIR_UP;
			else if (g.dir === DIR_LEFT) g.dir = DIR_RIGHT;
			else if (g.dir === DIR_RIGHT) g.dir = DIR_LEFT;
		}

		// Ghost Pathfinding & AI
		function updateGhosts() {
			if (gameState !== STATE_PLAYING && gameState !== STATE_FRIGHT) return;

			// Scatter vs Chase cycle (7s scatter, 20s chase, 7s, 20s, 5s, 20s, 5s, chase)
			waveTimer++;
			if (currentWave === 0 && waveTimer > 420) { isScatter = false; currentWave++; waveTimer = 0; }
			else if (currentWave === 1 && waveTimer > 1200) { isScatter = true; currentWave++; waveTimer = 0; }
			else if (currentWave === 2 && waveTimer > 420) { isScatter = false; currentWave++; waveTimer = 0; }
			else if (currentWave === 3 && waveTimer > 1200) { isScatter = true; currentWave++; waveTimer = 0; }
			else if (currentWave === 4 && waveTimer > 300) { isScatter = false; currentWave++; waveTimer = 0; }

			// Frightened timer
			if (gameState === STATE_FRIGHT) {
				frightTimer -= 16.6;
				if (frightTimer <= 0) {
					gameState = STATE_PLAYING;
					ghosts.forEach(function(g) {
						if (g.mode === 'fright') g.mode = isScatter ? 'scatter' : 'chase';
					});
				}
			}

			ghosts.forEach(function(g) {
				// Handle ghost house releasing
				if (g.inHouse) {
					g.exitDelay--;
					// Pinky exits early, Inky & Clyde after dots or timer
					if (g.id === GHOST_PINKY && g.exitDelay <= 0) releaseGhostFromHouse(g);
					else if (g.id === GHOST_INKY && (dotsEatenRound >= 30 || g.exitDelay <= 0)) releaseGhostFromHouse(g);
					else if (g.id === GHOST_CLYDE && (dotsEatenRound >= 60 || g.exitDelay <= 0)) releaseGhostFromHouse(g);
					else {
						// Bob up and down inside house
						g.y += (g.dir === DIR_UP ? -0.8 : 0.8);
						if (g.y < 16.5 * TILE) g.dir = DIR_DOWN;
						if (g.y > 17.5 * TILE) g.dir = DIR_UP;
						return;
					}
				}

				// Determine speed based on mode & tunnel
				var currentSpeed = g.speed;
				var gCol = Math.floor(g.x / TILE);
				var gRow = Math.floor(g.y / TILE);

				if (g.mode === 'eyes') {
					currentSpeed = 3.6; // Eyes return swiftly
				} else if (gRow === 17 && (gCol < 6 || gCol > 21)) {
					currentSpeed = 1.0; // Tunnel slow down
				} else if (g.mode === 'fright') {
					currentSpeed = 1.15;
				} else if (g.id === GHOST_BLINKY) {
					// Cruise Elroy: speeds up when few dots remain
					if (dotsRemaining <= 10) currentSpeed = 2.25;
					else if (dotsRemaining <= 20) currentSpeed = 2.1;
				}

				// Calculate Target Tile
				calculateGhostTarget(g);

				// Ghost navigation at tile centers
				var tileCenterX = gCol * TILE + TILE / 2;
				var tileCenterY = gRow * TILE + TILE / 2;
				var distCenterX = Math.abs(g.x - tileCenterX);
				var distCenterY = Math.abs(g.y - tileCenterY);

				if (distCenterX <= currentSpeed && distCenterY <= currentSpeed) {
					g.x = tileCenterX;
					g.y = tileCenterY;

					// Check if dead eyes reached ghost house entrance
					if (g.mode === 'eyes') {
						if (gCol === 13 || gCol === 14) {
							if (gRow === 14 || gRow === 15) {
								g.mode = 'house';
								g.inHouse = true;
								g.y = 17 * TILE;
								g.exitDelay = 30;
								return;
							}
						}
					}

					// Choose next direction (no 180 reverse)
					var bestDir = g.dir;
					var bestDist = Infinity;
					var validDirs = [DIR_UP, DIR_LEFT, DIR_DOWN, DIR_RIGHT];

					// Restriction: ghosts cannot turn UP directly above house at tiles (12,14) and (15,14)
					var disableUp = (gRow === 14 && (gCol === 12 || gCol === 15) && g.mode !== 'eyes');

					if (g.mode === 'fright') {
						// Pseudo-random turn
						var available = [];
						validDirs.forEach(function(d) {
							if (isOpposite(d, g.dir)) return;
							if (d === DIR_UP && disableUp) return;
							var nCol = gCol + DIRS[d].x;
							var nRow = gRow + DIRS[d].y;
							if (isWalkable(nCol, nRow, true, false, g.inHouse)) {
								available.push(d);
							}
						});
						if (available.length > 0) {
							bestDir = available[Math.floor(Math.random() * available.length)];
						}
					} else {
						validDirs.forEach(function(d) {
							if (isOpposite(d, g.dir)) return;
							if (d === DIR_UP && disableUp) return;
							var nCol = gCol + DIRS[d].x;
							var nRow = gRow + DIRS[d].y;
							if (isWalkable(nCol, nRow, true, g.mode === 'eyes', g.inHouse)) {
								var dist = Math.hypot(nCol - g.targetX, nRow - g.targetY);
								if (dist < bestDist) {
									bestDist = dist;
									bestDir = d;
								}
							}
						});
					}

					g.dir = bestDir;
				}

				// Move ghost
				var dInfo = DIRS[g.dir];
				g.x += dInfo.x * currentSpeed;
				g.y += dInfo.y * currentSpeed;

				// Tunnel wrap
				if (gRow === 17) {
					if (g.x < -TILE / 2) g.x = (COLS - 0.5) * TILE;
					else if (g.x > (COLS - 0.5) * TILE) g.x = -TILE / 2;
				}

				// Check collision with Pacman
				var distPac = Math.hypot(g.x - pacman.x, g.y - pacman.y);
				if (distPac < 12) {
					if (g.mode === 'fright') {
						// Eat Ghost!
						g.mode = 'eyes';
						ghostsEatenStreak++;
						var ghostPoints = 200 * Math.pow(2, ghostsEatenStreak - 1);
						score += ghostPoints;
						playSound('eat_ghost');

						popupScore = {
							active: true,
							score: ghostPoints,
							x: g.x,
							y: g.y,
							timer: 40
						};

						// Freeze brief moment
						gameState = STATE_EATGHOST;
						setTimeout(function() {
							if (gameState === STATE_EATGHOST) {
								gameState = (frightTimer > 0) ? STATE_FRIGHT : STATE_PLAYING;
							}
						}, 500);

					} else if (g.mode === 'chase' || g.mode === 'scatter') {
						// Pacman Dies!
						pacmanDeath();
					}
				}
			});
		}

		function releaseGhostFromHouse(g) {
			// Center ghost horizontally in front of door, then move up
			if (Math.abs(g.x - 13.5 * TILE) > 2) {
				g.x += (g.x < 13.5 * TILE ? 1 : -1);
			} else {
				g.x = 13.5 * TILE;
				g.y -= 1.2;
				if (g.y <= 14 * TILE) {
					g.inHouse = false;
					g.dir = DIR_LEFT;
					g.mode = isScatter ? 'scatter' : 'chase';
				}
			}
		}

		function calculateGhostTarget(g) {
			if (g.mode === 'eyes') {
				g.targetX = 13.5;
				g.targetY = 14;
				return;
			}

			if (g.mode === 'scatter') {
				g.targetX = g.scatterTarget.x;
				g.targetY = g.scatterTarget.y;
				return;
			}

			var pCol = Math.floor(pacman.x / TILE);
			var pRow = Math.floor(pacman.y / TILE);
			var pDir = DIRS[pacman.dir];

			if (g.id === GHOST_BLINKY) {
				// Blinky directly targets Pacman
				g.targetX = pCol;
				g.targetY = pRow;
			} else if (g.id === GHOST_PINKY) {
				// Pinky ambushes 4 tiles ahead
				g.targetX = pCol + 4 * pDir.x;
				g.targetY = pRow + 4 * pDir.y;
			} else if (g.id === GHOST_INKY) {
				// Inky pincer vector through 2 tiles ahead doubled from Blinky
				var blinky = ghosts[GHOST_BLINKY];
				var bCol = Math.floor(blinky.x / TILE);
				var bRow = Math.floor(blinky.y / TILE);
				var aheadX = pCol + 2 * pDir.x;
				var aheadY = pRow + 2 * pDir.y;
				g.targetX = 2 * aheadX - bCol;
				g.targetY = 2 * aheadY - bRow;
			} else if (g.id === GHOST_CLYDE) {
				// Clyde shy: if dist >= 8 tiles chase, else retreat to corner
				var dist = Math.hypot(Math.floor(g.x / TILE) - pCol, Math.floor(g.y / TILE) - pRow);
				if (dist >= 8) {
					g.targetX = pCol;
					g.targetY = pRow;
				} else {
					g.targetX = g.scatterTarget.x;
					g.targetY = g.scatterTarget.y;
				}
			}
		}

		function isOpposite(d1, d2) {
			return (d1 === DIR_UP && d2 === DIR_DOWN) ||
				   (d1 === DIR_DOWN && d2 === DIR_UP) ||
				   (d1 === DIR_LEFT && d2 === DIR_RIGHT) ||
				   (d1 === DIR_RIGHT && d2 === DIR_LEFT);
		}

		// Death Sequence
		function pacmanDeath() {
			gameState = STATE_DYING;
			playSound('death');
			pacman.isDying = true;
			pacman.deathAngle = 0;
		}

		// Level Complete Sequence
		function levelComplete() {
			gameState = STATE_LEVELWIN;
			levelWinTimer = 140;
		}

		// Rendering Engine
		function render() {
			ctx.clearRect(0, 0, canvas.width, canvas.height);

			// Draw Map Maze Walls & Pellets
			renderMaze();

			// Draw Fruit if active
			if (fruitActive) {
				renderFruit();
			}

			// Draw Popup Score
			if (popupScore.active) {
				ctx.save();
				ctx.font = 'bold 12px "Courier New", monospace';
				ctx.fillStyle = '#00ffff';
				ctx.textAlign = 'center';
				ctx.fillText(popupScore.score, popupScore.x, popupScore.y);
				ctx.restore();
				popupScore.timer--;
				if (popupScore.timer <= 0) popupScore.active = false;
			}

			// Draw Pac-Man Character (Player Avatar)
			if (gameState !== STATE_LEVELWIN) {
				renderPacman();
			}

			// Draw Ghosts
			if (gameState !== STATE_DYING && gameState !== STATE_LEVELWIN) {
				ghosts.forEach(function(g) {
					renderGhost(g);
				});
			}

			// Draw Bottom HUD: Lives & Collected Fruits
			renderBottomHUD();

			// Draw Overlay Prompts ("READY!", "PAUSED", etc.)
			if (gameState === STATE_READY) {
				ctx.save();
				ctx.font = '900 20px "Courier New", monospace';
				ctx.fillStyle = '#fbbf24';
				ctx.textAlign = 'center';
				ctx.fillText('GATAVS!', canvas.width / 2, 20 * TILE + 4);
				ctx.restore();
			}
		}

		function renderMaze() {
			var flashWhite = (gameState === STATE_LEVELWIN && Math.floor(levelWinTimer / 15) % 2 === 0);

			for (var r = 0; r < ROWS; r++) {
				for (var c = 0; c < COLS; c++) {
					var val = grid[r][c];
					var x = c * TILE;
					var y = r * TILE;

					if (val === 1) {
						// Double neon line walls
						ctx.fillStyle = '#000000';
						ctx.fillRect(x, y, TILE, TILE);

						ctx.strokeStyle = flashWhite ? '#ffffff' : '#2121de';
						ctx.lineWidth = 2;
						ctx.strokeRect(x + 1, y + 1, TILE - 2, TILE - 2);
					} else if (val === 2) {
						// Regular Dot (small pellet)
						ctx.fillStyle = '#ffb8ae';
						ctx.fillRect(x + 6, y + 6, 4, 4);
					} else if (val === 3) {
						// Energizer (Power Pellet) - flashes
						if (Math.floor(globalTimer / 14) % 2 === 0) {
							ctx.beginPath();
							ctx.arc(x + TILE / 2, y + TILE / 2, 6, 0, Math.PI * 2);
							ctx.fillStyle = '#ffb8ae';
							ctx.fill();
						}
					} else if (val === 4) {
						// Ghost House Door
						ctx.fillStyle = '#ffb8ff';
						ctx.fillRect(x, y + 6, TILE, 4);
					}
				}
			}
		}

		function renderFruit() {
			var fObj = getFruitForLevel(level);
			ctx.save();
			ctx.font = '16px serif';
			ctx.textAlign = 'center';
			ctx.textBaseline = 'middle';
			ctx.fillText(fObj.symbol, fruitX, fruitY);
			ctx.restore();

			fruitTimer--;
			if (fruitTimer <= 0) fruitActive = false;
		}

		function renderPacman() {
			ctx.save();
			ctx.translate(pacman.x, pacman.y);

			if (pacman.isDying) {
				// Death dissolve animation
				var angle = pacman.deathAngle;
				ctx.beginPath();
				ctx.moveTo(0, 0);
				ctx.arc(0, 0, pacman.radius, angle, Math.PI * 2 - angle);
				ctx.closePath();
				ctx.clip();

				if (avatarLoaded) {
					ctx.drawImage(avatarImg, -pacman.radius, -pacman.radius, pacman.radius * 2, pacman.radius * 2);
				} else {
					ctx.fillStyle = '#facc15';
					ctx.fill();
				}

				pacman.deathAngle += 0.08;
				if (pacman.deathAngle >= Math.PI) {
					pacman.isDying = false;
					lives--;
					updateHUD();
					if (lives <= 0) {
						gameOver();
					} else {
						resetPositions();
						readyRound();
					}
				}
				ctx.restore();
				return;
			}

			// Rotation based on movement direction
			var rot = 0;
			if (pacman.dir === DIR_RIGHT) rot = 0;
			else if (pacman.dir === DIR_DOWN) rot = Math.PI / 2;
			else if (pacman.dir === DIR_LEFT) rot = Math.PI;
			else if (pacman.dir === DIR_UP) rot = -Math.PI / 2;

			ctx.rotate(rot);

			// Clip avatar to circular wedge with chomping mouth
			ctx.beginPath();
			ctx.moveTo(0, 0);
			ctx.arc(0, 0, pacman.radius, pacman.mouthAngle, Math.PI * 2 - pacman.mouthAngle);
			ctx.closePath();
			ctx.clip();

			if (avatarLoaded) {
				ctx.drawImage(avatarImg, -pacman.radius, -pacman.radius, pacman.radius * 2, pacman.radius * 2);
			} else {
				ctx.fillStyle = '#fbbf24';
				ctx.fill();
			}

			// Golden outline ring
			ctx.beginPath();
			ctx.arc(0, 0, pacman.radius, pacman.mouthAngle, Math.PI * 2 - pacman.mouthAngle);
			ctx.strokeStyle = '#f59e0b';
			ctx.lineWidth = 1.5;
			ctx.stroke();

			ctx.restore();
		}

		function renderGhost(g) {
			var x = g.x;
			var y = g.y;
			var r = 11;

			ctx.save();

			if (g.mode === 'eyes') {
				// Only Eyeballs returning to house
				renderEyes(x, y, g.dir);
				ctx.restore();
				return;
			}

			// Ghost Body
			ctx.beginPath();
			ctx.arc(x, y - 2, r, Math.PI, 0, false);
			// Wavy skirt at bottom
			var skirtPhase = Math.floor(globalTimer / 8) % 2;
			var skirtY = y + r - 2;
			ctx.lineTo(x + r, skirtY);
			if (skirtPhase === 0) {
				ctx.lineTo(x + r * 0.6, skirtY - 3);
				ctx.lineTo(x + r * 0.2, skirtY);
				ctx.lineTo(x - r * 0.2, skirtY - 3);
				ctx.lineTo(x - r * 0.6, skirtY);
			} else {
				ctx.lineTo(x + r * 0.6, skirtY);
				ctx.lineTo(x + r * 0.2, skirtY - 3);
				ctx.lineTo(x - r * 0.2, skirtY);
				ctx.lineTo(x - r * 0.6, skirtY - 3);
			}
			ctx.lineTo(x - r, skirtY);
			ctx.closePath();

			if (g.mode === 'fright') {
				// Dark blue with white flashing when expiring
				var isFlashing = (frightTimer < 2000 && Math.floor(frightTimer / 180) % 2 === 0);
				ctx.fillStyle = isFlashing ? '#ffffff' : '#2563eb';
				ctx.fill();

				// Wavy Frightened mouth
				ctx.strokeStyle = isFlashing ? '#ff0000' : '#f97316';
				ctx.lineWidth = 1.5;
				ctx.beginPath();
				ctx.moveTo(x - 6, y + 3);
				ctx.lineTo(x - 3, y + 1);
				ctx.lineTo(x, y + 3);
				ctx.lineTo(x + 3, y + 1);
				ctx.lineTo(x + 6, y + 3);
				ctx.stroke();

				// Simple frightened dots for eyes
				ctx.fillStyle = isFlashing ? '#ff0000' : '#fbbf24';
				ctx.fillRect(x - 5, y - 4, 3, 3);
				ctx.fillRect(x + 2, y - 4, 3, 3);
			} else {
				ctx.fillStyle = g.color;
				ctx.fill();

				// Big arcade eyes
				renderEyes(x, y, g.dir);
			}

			ctx.restore();
		}

		function renderEyes(x, y, dir) {
			var eyeOffsetX = 0;
			var eyeOffsetY = 0;
			if (dir === DIR_LEFT) eyeOffsetX = -2;
			else if (dir === DIR_RIGHT) eyeOffsetX = 2;
			else if (dir === DIR_UP) eyeOffsetY = -2;
			else if (dir === DIR_DOWN) eyeOffsetY = 2;

			// Sclera (White)
			ctx.fillStyle = '#ffffff';
			ctx.beginPath();
			ctx.arc(x - 4, y - 4, 4, 0, Math.PI * 2);
			ctx.arc(x + 4, y - 4, 4, 0, Math.PI * 2);
			ctx.fill();

			// Pupils (Blue)
			ctx.fillStyle = '#1e3a8a';
			ctx.beginPath();
			ctx.arc(x - 4 + eyeOffsetX, y - 4 + eyeOffsetY, 2, 0, Math.PI * 2);
			ctx.arc(x + 4 + eyeOffsetX, y - 4 + eyeOffsetY, 2, 0, Math.PI * 2);
			ctx.fill();
		}

		function renderBottomHUD() {
			// Lives left icons
			var startX = 16;
			var bottomY = (ROWS - 1) * TILE;
			for (var i = 0; i < lives - 1; i++) {
				ctx.save();
				ctx.translate(startX + i * 22, bottomY);
				ctx.beginPath();
				ctx.arc(0, 0, 8, 0.25, Math.PI * 2 - 0.25);
				ctx.lineTo(0, 0);
				ctx.fillStyle = '#fbbf24';
				ctx.fill();
				ctx.restore();
			}

			// Fruits collected icons at bottom right
			var maxFruitToShow = 7;
			var fCount = Math.min(level, maxFruitToShow);
			for (var f = 0; f < fCount; f++) {
				var fObj = getFruitForLevel(level - f);
				ctx.save();
				ctx.font = '14px serif';
				ctx.textAlign = 'center';
				ctx.fillText(fObj.symbol, canvas.width - 20 - f * 20, bottomY + 5);
				ctx.restore();
			}
		}

		function gameOver() {
			gameState = STATE_GAMEOVER;
			$('#final-score').text(score);
			$('#final-level').text(level);
			$('#final-best').text(Math.max(score, highScore));
			$('#pacman-gameover-overlay').fadeIn(200);
			submitScore();
		}

		// Main Animation Loop
		function gameLoop() {
			globalTimer++;

			if (gameState === STATE_READY) {
				readyTimer--;
				if (readyTimer <= 0) {
					gameState = STATE_PLAYING;
				}
			} else if (gameState === STATE_PLAYING || gameState === STATE_FRIGHT) {
				updatePacman();
				updateGhosts();
			} else if (gameState === STATE_LEVELWIN) {
				levelWinTimer--;
				if (levelWinTimer <= 0) {
					level++;
					dotsEatenRound = 0;
					initGrid();
					resetPositions();
					updateHUD();
					readyRound();
				}
			}

			render();
			updateHUD();
			requestAnimationFrame(gameLoop);
		}

		// Initial grid setup and start animation
		initGrid();
		resetPositions();
		updateHUD();
		requestAnimationFrame(gameLoop);
	});
})(jQuery);
