(function() {
	'use strict';

	var canvas, ctx;
	var isRunning = false;
	var isGameOver = false;
	var soundEnabled = true;

	var score = 0;
	var highScore = 0;
	var lives = 3;
	var level = 1;
	var sessionToken = '';
	var startTime = 0;

	var GRID_SIZE = 40;
	var COLS = 11;
	var ROWS = 14;

	var frog = {
		col: 5,
		row: 12,
		x: 5 * 40,
		y: 12 * 40,
		targetX: 5 * 40,
		targetY: 12 * 40,
		isMoving: false,
		dir: 'UP'
	};

	var timerMax = 30; // 30 seconds per frog life
	var timerCurrent = 30;
	var timerInterval = null;

	var homeSlots = [
		{ col: 1, filled: false },
		{ col: 3, filled: false },
		{ col: 5, filled: false },
		{ col: 7, filled: false },
		{ col: 9, filled: false }
	];

	// Audio Synth
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

	function playSound(type) {
		if (!soundEnabled || !audioCtx) return;
		try {
			var now = audioCtx.currentTime;
			var osc = audioCtx.createOscillator();
			var gain = audioCtx.createGain();
			osc.connect(gain);
			gain.connect(audioCtx.destination);

			if (type === 'hop') {
				osc.type = 'sine';
				osc.frequency.setValueAtTime(250, now);
				osc.frequency.exponentialRampToValueAtTime(450, now + 0.08);
				gain.gain.setValueAtTime(0.3, now);
				gain.gain.linearRampToValueAtTime(0.01, now + 0.08);
				osc.start(now);
				osc.stop(now + 0.08);
			} else if (type === 'home') {
				osc.type = 'triangle';
				osc.frequency.setValueAtTime(523.25, now);
				osc.frequency.setValueAtTime(659.25, now + 0.1);
				osc.frequency.setValueAtTime(783.99, now + 0.2);
				gain.gain.setValueAtTime(0.4, now);
				gain.gain.linearRampToValueAtTime(0.01, now + 0.3);
				osc.start(now);
				osc.stop(now + 0.3);
			} else if (type === 'squash' || type === 'splash') {
				osc.type = 'sawtooth';
				osc.frequency.setValueAtTime(160, now);
				osc.frequency.linearRampToValueAtTime(40, now + 0.25);
				gain.gain.setValueAtTime(0.4, now);
				gain.gain.linearRampToValueAtTime(0.01, now + 0.25);
				osc.start(now);
				osc.stop(now + 0.25);
			} else if (type === 'level') {
				osc.type = 'sine';
				osc.frequency.setValueAtTime(440, now);
				osc.frequency.setValueAtTime(554.37, now + 0.12);
				osc.frequency.setValueAtTime(659.25, now + 0.24);
				osc.frequency.setValueAtTime(880, now + 0.36);
				gain.gain.setValueAtTime(0.4, now);
				gain.gain.linearRampToValueAtTime(0.01, now + 0.5);
				osc.start(now);
				osc.stop(now + 0.5);
			} else if (type === 'gameover') {
				osc.type = 'sawtooth';
				osc.frequency.setValueAtTime(260, now);
				osc.frequency.linearRampToValueAtTime(70, now + 0.4);
				gain.gain.setValueAtTime(0.4, now);
				gain.gain.linearRampToValueAtTime(0.01, now + 0.4);
				osc.start(now);
				osc.stop(now + 0.4);
			}
		} catch (e) {}
	}

	// Obstacles & Water Rides
	var rowsConfig = [
		// River Rows (1 to 5) - Smooth & accessible initial speeds
		{ row: 1, type: 'river', isLog: true, speed: 1.0, length: 3, spacing: 65, items: [] },
		{ row: 2, type: 'river', isLog: false, speed: -1.2, length: 3, spacing: 60, items: [] },
		{ row: 3, type: 'river', isLog: true, speed: 1.4, length: 4, spacing: 75, items: [] },
		{ row: 4, type: 'river', isLog: true, speed: 1.1, length: 3, spacing: 65, items: [] },
		{ row: 5, type: 'river', isLog: false, speed: -0.9, length: 3, spacing: 60, items: [] },

		// Road Rows (7 to 11) - Smooth initial traffic speeds
		{ row: 7, type: 'road', carType: 'race', speed: -2.2, length: 1, spacing: 130, items: [] },
		{ row: 8, type: 'road', carType: 'truck', speed: 1.2, length: 2, spacing: 150, items: [] },
		{ row: 9, type: 'road', carType: 'taxi', speed: -1.5, length: 1, spacing: 120, items: [] },
		{ row: 10, type: 'road', carType: 'sports', speed: 1.8, length: 1, spacing: 140, items: [] },
		{ row: 11, type: 'road', carType: 'sedan', speed: -1.0, length: 1, spacing: 110, items: [] }
	];

	function initObstacles() {
		for (var i = 0; i < rowsConfig.length; i++) {
			var r = rowsConfig[i];
			r.items = [];
			var speedMult = 1 + (level - 1) * 0.10;
			var actualSpeed = r.speed * speedMult;
			var itemWidth = r.length * GRID_SIZE;
			var step = itemWidth + r.spacing;

			// Seamless ring buffer: calculate pattern width to cover canvas plus offscreen margins
			var minCoverage = canvas.width + itemWidth + r.spacing;
			var numItems = Math.ceil(minCoverage / step);
			r.patternWidth = numItems * step;

			for (var k = 0; k < numItems; k++) {
				r.items.push({
					x: k * step,
					width: itemWidth,
					speed: actualSpeed
				});
			}
		}
	}

	function resetFrog() {
		frog.col = 5;
		frog.row = 12;
		frog.x = 5 * GRID_SIZE;
		frog.y = 12 * GRID_SIZE;
		frog.targetX = frog.x;
		frog.targetY = frog.y;
		frog.dir = 'UP';
		timerCurrent = timerMax;
	}

	function resetGame() {
		score = 0;
		lives = 3;
		level = 1;
		isGameOver = false;

		for (var i = 0; i < homeSlots.length; i++) {
			homeSlots[i].filled = false;
		}

		initObstacles();
		resetFrog();

		startTime = Date.now();
		fetchInitToken();

		if (timerInterval) clearInterval(timerInterval);
		timerInterval = setInterval(function() {
			if (isRunning && !isGameOver) {
				timerCurrent--;
				if (timerCurrent <= 0) {
					killFrog('time');
				}
			}
		}, 1000);
	}

	function fetchInitToken() {
		$.getJSON('/vardes?action=init_token', function(data) {
			if (data && data.token) {
				sessionToken = data.token;
			}
		});
	}

	function moveFrog(dc, dr, dirName) {
		if (!isRunning || isGameOver) return;

		var newCol = frog.col + dc;
		var newRow = frog.row + dr;

		if (newCol < 0 || newCol >= COLS || newRow < 0 || newRow >= ROWS - 1) return;

		frog.col = newCol;
		frog.row = newRow;
		frog.targetX = newCol * GRID_SIZE;
		frog.targetY = newRow * GRID_SIZE;
		frog.dir = dirName;

		score += 10; // 10 points for forward progress
		playSound('hop');
	}

	function killFrog(reason) {
		lives--;
		playSound(reason === 'splash' ? 'splash' : 'squash');

		if (lives <= 0) {
			gameOver();
		} else {
			resetFrog();
		}
	}

	function checkHomeSlot() {
		var found = false;
		for (var i = 0; i < homeSlots.length; i++) {
			var slot = homeSlots[i];
			if (frog.col === slot.col && !slot.filled) {
				slot.filled = true;
				found = true;
				score += 500 + timerCurrent * 10;
				playSound('home');
				break;
			}
		}

		if (found) {
			// Check if all 5 slots filled -> Level Up
			var allFilled = homeSlots.every(function(s) { return s.filled; });
			if (allFilled) {
				level++;
				score += 1000;
				playSound('level');
				for (var j = 0; j < homeSlots.length; j++) {
					homeSlots[j].filled = false;
				}
				initObstacles();
			}
			resetFrog();
		} else {
			killFrog('splash'); // Missed home slot or hit water
		}
	}

	function update() {
		if (!isRunning || isGameOver) return;

		// Smooth frog interpolation
		frog.x += (frog.targetX - frog.x) * 0.4;
		frog.y += (frog.targetY - frog.y) * 0.4;

		// Update Obstacle positions using seamless ring buffer
		for (var i = 0; i < rowsConfig.length; i++) {
			var r = rowsConfig[i];
			var pw = r.patternWidth || (canvas.width + 120);
			for (var k = 0; k < r.items.length; k++) {
				var item = r.items[k];
				item.x += item.speed;

				if (item.speed > 0) {
					if (item.x >= pw - item.width) {
						item.x -= pw;
					}
				} else {
					if (item.x + item.width <= 0) {
						item.x += pw;
					}
				}
			}
		}

		// Row 0 Collision check (Home Slot Row)
		if (frog.row === 0) {
			checkHomeSlot();
			return;
		}

		// Highway Rows (7 to 11) Collision check
		if (frog.row >= 7 && frog.row <= 11) {
			var roadRow = rowsConfig.find(function(rc) { return rc.row === frog.row; });
			if (roadRow) {
				var frogBox = { x: frog.x + 8, y: frog.y + 8, width: 24, height: 24 };
				for (var m = 0; m < roadRow.items.length; m++) {
					var car = roadRow.items[m];
					if (
						frogBox.x < car.x + car.width &&
						frogBox.x + frogBox.width > car.x &&
						frogBox.y < frog.row * GRID_SIZE + GRID_SIZE &&
						frogBox.y + frogBox.height > frog.row * GRID_SIZE
					) {
						killFrog('squash');
						return;
					}
				}
			}
		}

		// River Rows (1 to 5) Water Ride / Drown check
		if (frog.row >= 1 && frog.row <= 5) {
			var riverRow = rowsConfig.find(function(rc) { return rc.row === frog.row; });
			var ridingItem = null;

			if (riverRow) {
				var fCenterX = frog.x + GRID_SIZE / 2;
				for (var n = 0; n < riverRow.items.length; n++) {
					var log = riverRow.items[n];
					if (fCenterX >= log.x - 12 && fCenterX <= log.x + log.width + 12) {
						ridingItem = log;
						break;
					}
				}

				if (ridingItem) {
					// Hitch ride on log/turtle
					frog.targetX += ridingItem.speed;
					frog.x += ridingItem.speed;

					// Offscreen river drift kill
					if (frog.x < -20 || frog.x > canvas.width - 20) {
						killFrog('splash');
					}
				} else {
					// Landed in water!
					killFrog('splash');
				}
			}
		}
	}

	function draw() {
		ctx.clearRect(0, 0, canvas.width, canvas.height);

		// 1. Draw Environment Zones
		// Row 0: Home Slot Water/Bush Base
		ctx.fillStyle = '#1e3a8a';
		ctx.fillRect(0, 0, canvas.width, GRID_SIZE);

		// Home Slot Lilies (Columns 1, 3, 5, 7, 9)
		for (var s = 0; s < homeSlots.length; s++) {
			var slot = homeSlots[s];
			var sx = slot.col * GRID_SIZE;

			ctx.fillStyle = '#15803d';
			ctx.beginPath();
			ctx.arc(sx + GRID_SIZE / 2, GRID_SIZE / 2, 16, 0, Math.PI * 2);
			ctx.fill();

			if (slot.filled) {
				ctx.font = '22px sans-serif';
				ctx.textAlign = 'center';
				ctx.textBaseline = 'middle';
				ctx.fillText('🐸', sx + GRID_SIZE / 2, GRID_SIZE / 2);
			}
		}

		// Rows 1-5: Blue River
		ctx.fillStyle = '#0284c7';
		ctx.fillRect(0, 1 * GRID_SIZE, canvas.width, 5 * GRID_SIZE);

		// Row 6: Purple Safe Zone
		ctx.fillStyle = '#4c1d95';
		ctx.fillRect(0, 6 * GRID_SIZE, canvas.width, GRID_SIZE);

		// Rows 7-11: Dark Highway
		ctx.fillStyle = '#1e293b';
		ctx.fillRect(0, 7 * GRID_SIZE, canvas.width, 5 * GRID_SIZE);

		// Dashed Lane Dividers
		ctx.strokeStyle = '#64748b';
		ctx.setLineDash([12, 12]);
		for (var lane = 8; lane <= 11; lane++) {
			ctx.beginPath();
			ctx.moveTo(0, lane * GRID_SIZE);
			ctx.lineTo(canvas.width, lane * GRID_SIZE);
			ctx.stroke();
		}
		ctx.setLineDash([]); // Reset dash

		// Row 12: Green Safe Starting Strip
		ctx.fillStyle = '#15803d';
		ctx.fillRect(0, 12 * GRID_SIZE, canvas.width, GRID_SIZE);

		// Row 13: Bottom HUD Strip
		ctx.fillStyle = '#0f172a';
		ctx.fillRect(0, 13 * GRID_SIZE, canvas.width, GRID_SIZE);

		// 2. Draw Logs & Turtles on River
		for (var rIdx = 0; rIdx < 5; rIdx++) {
			var rConf = rowsConfig[rIdx];
			for (var itemIdx = 0; itemIdx < rConf.items.length; itemIdx++) {
				var item = rConf.items[itemIdx];
				var y = rConf.row * GRID_SIZE;

				if (rConf.isLog) {
					// Draw Wood Log
					ctx.fillStyle = '#854d0e';
					ctx.strokeStyle = '#713f12';
					ctx.lineWidth = 2;
					ctx.beginPath();
					ctx.roundRect(item.x, y + 6, item.width, GRID_SIZE - 12, 8);
					ctx.fill();
					ctx.stroke();
				} else {
					// Draw Turtles
					var turtleCount = Math.floor(item.width / GRID_SIZE);
					ctx.fillStyle = '#16a34a';
					for (var t = 0; t < turtleCount; t++) {
						ctx.beginPath();
						ctx.arc(item.x + t * GRID_SIZE + GRID_SIZE / 2, y + GRID_SIZE / 2, 14, 0, Math.PI * 2);
						ctx.fill();
					}
				}
			}
		}

		// 3. Draw Cars & Trucks on Highway
		for (var cIdx = 5; cIdx < rowsConfig.length; cIdx++) {
			var carConf = rowsConfig[cIdx];
			for (var carItemIdx = 0; carItemIdx < carConf.items.length; carItemIdx++) {
				var car = carConf.items[carItemIdx];
				var cy = carConf.row * GRID_SIZE;

				if (carConf.carType === 'race') ctx.fillStyle = '#ef4444';
				else if (carConf.carType === 'truck') ctx.fillStyle = '#3b82f6';
				else if (carConf.carType === 'taxi') ctx.fillStyle = '#eab308';
				else if (carConf.carType === 'sports') ctx.fillStyle = '#ec4899';
				else ctx.fillStyle = '#94a3b8';

				ctx.beginPath();
				ctx.roundRect(car.x, cy + 6, car.width, GRID_SIZE - 12, 6);
				ctx.fill();

				// Headlights
				ctx.fillStyle = '#fef08a';
				if (car.speed > 0) {
					ctx.fillRect(car.x + car.width - 4, cy + 8, 4, 6);
					ctx.fillRect(car.x + car.width - 4, cy + GRID_SIZE - 14, 4, 6);
				} else {
					ctx.fillRect(car.x, cy + 8, 4, 6);
					ctx.fillRect(car.x, cy + GRID_SIZE - 14, 4, 6);
				}
			}
		}

		// 4. Draw Frog Player Character
		ctx.save();
		ctx.font = '28px sans-serif';
		ctx.textAlign = 'center';
		ctx.textBaseline = 'middle';
		ctx.fillText('🐸', frog.x + GRID_SIZE / 2, frog.y + GRID_SIZE / 2);
		ctx.restore();

		// 5. Draw HUD Bar (Row 13)
		ctx.save();
		ctx.font = 'bold 15px sans-serif';
		ctx.fillStyle = '#f8fafc';
		ctx.textAlign = 'left';
		ctx.fillText('Punkti: ' + score, 10, 13 * GRID_SIZE + 24);

		// Lives
		ctx.textAlign = 'center';
		var livesText = '';
		for (var l = 0; l < lives; l++) livesText += '❤️ ';
		ctx.fillText(livesText, canvas.width / 2, 13 * GRID_SIZE + 24);

		// Timer Progress Bar
		ctx.fillStyle = timerCurrent < 8 ? '#ef4444' : '#22c55e';
		var timerW = (timerCurrent / timerMax) * 90;
		ctx.fillRect(canvas.width - 105, 13 * GRID_SIZE + 12, Math.max(0, timerW), 14);
		ctx.strokeStyle = '#ffffff';
		ctx.lineWidth = 1;
		ctx.strokeRect(canvas.width - 105, 13 * GRID_SIZE + 12, 90, 14);
		ctx.restore();
	}

	function gameLoop() {
		update();
		draw();
		if (isRunning) {
			requestAnimationFrame(gameLoop);
		}
	}

	function gameOver() {
		isGameOver = true;
		isRunning = false;
		if (timerInterval) clearInterval(timerInterval);
		playSound('gameover');

		var duration = Math.round((Date.now() - startTime) / 1000);
		var finalScore = score;

		$('#vardes-final-score').text(finalScore);
		$('#vardes-gameover-overlay').fadeIn(200);

		if (finalScore > highScore) {
			highScore = finalScore;
			$('#vardes-best-score').text(highScore);
			$('#stat-highscore').text(highScore);
			$('#vardes-record-alert').show();
		} else {
			$('#vardes-record-alert').hide();
		}

		// Submit Highscore via AJAX
		if (finalScore > 0) {
			$.post('/vardes?action=push', {
				score: finalScore,
				duration: duration,
				token: sessionToken
			}, function(response) {
				if (response && response.success) {
					if (response.highScore) {
						highScore = response.highScore;
						$('#vardes-best-score').text(highScore);
						$('#stat-highscore').text(highScore);
					}
				}
			}, 'json');
		}
	}

	$(document).ready(function() {
		canvas = document.getElementById('vardes-canvas');
		if (!canvas) return;
		ctx = canvas.getContext('2d');

		highScore = window.VARDES_USER_HIGHSCORE || 0;

		// Canvas Polyfill for roundRect
		if (!ctx.roundRect) {
			ctx.roundRect = function(x, y, w, h, r) {
				if (w < 2 * r) r = w / 2;
				if (h < 2 * r) r = h / 2;
				this.beginPath();
				this.moveTo(x + r, y);
				this.arcTo(x + w, y, x + w, y + h, r);
				this.arcTo(x + w, y + h, x, y + h, r);
				this.arcTo(x, y + h, x, y, r);
				this.arcTo(x, y, x + w, y, r);
				this.closePath();
				return this;
			};
		}

		// Controls Event Listeners
		$(window).on('keydown', function(e) {
			initAudio();
			if (e.key === 'ArrowUp' || e.key === 'w' || e.key === 'W') {
				moveFrog(0, -1, 'UP');
				e.preventDefault();
			} else if (e.key === 'ArrowDown' || e.key === 's' || e.key === 'S') {
				moveFrog(0, 1, 'DOWN');
				e.preventDefault();
			} else if (e.key === 'ArrowLeft' || e.key === 'a' || e.key === 'A') {
				moveFrog(-1, 0, 'LEFT');
				e.preventDefault();
			} else if (e.key === 'ArrowRight' || e.key === 'd' || e.key === 'D') {
				moveFrog(1, 0, 'RIGHT');
				e.preventDefault();
			} else if (e.key === ' ' || e.code === 'Space') {
				if (isGameOver || !isRunning) {
					$('#vardes-start-btn').click();
					e.preventDefault();
				}
			}
		});

		// D-Pad Touch Controls
		$('#dpad-up').on('click touchstart', function(e) { e.preventDefault(); initAudio(); moveFrog(0, -1, 'UP'); });
		$('#dpad-down').on('click touchstart', function(e) { e.preventDefault(); initAudio(); moveFrog(0, 1, 'DOWN'); });
		$('#dpad-left').on('click touchstart', function(e) { e.preventDefault(); initAudio(); moveFrog(-1, 0, 'LEFT'); });
		$('#dpad-right').on('click touchstart', function(e) { e.preventDefault(); initAudio(); moveFrog(1, 0, 'RIGHT'); });

		// Start & Restart Buttons
		$('#vardes-start-btn, #vardes-restart-btn').on('click', function() {
			initAudio();
			$('#vardes-start-overlay').hide();
			$('#vardes-gameover-overlay').hide();
			resetGame();
			isRunning = true;
			requestAnimationFrame(gameLoop);
		});

		// Sound Toggle
		$('#vardes-sound-toggle').on('click', function() {
			soundEnabled = !soundEnabled;
			$(this).text(soundEnabled ? '🔊 Ieslēgta' : '🔇 Izslēgta');
		});
	});
})();
