(function() {
	'use strict';

	// Game Engine & State
	var canvas, ctx;
	var isRunning = false;
	var isGameOver = false;
	var soundEnabled = true;
	var score = 0;
	var highScore = 0;
	var sessionToken = '';
	var startTime = 0;

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

			if (type === 'bounce') {
				osc.type = 'sine';
				osc.frequency.setValueAtTime(300, now);
				osc.frequency.exponentialRampToValueAtTime(600, now + 0.12);
				gain.gain.setValueAtTime(0.3, now);
				gain.gain.linearRampToValueAtTime(0.01, now + 0.12);
				osc.start(now);
				osc.stop(now + 0.12);
			} else if (type === 'spring') {
				osc.type = 'triangle';
				osc.frequency.setValueAtTime(400, now);
				osc.frequency.exponentialRampToValueAtTime(900, now + 0.25);
				gain.gain.setValueAtTime(0.4, now);
				gain.gain.linearRampToValueAtTime(0.01, now + 0.25);
				osc.start(now);
				osc.stop(now + 0.25);
			} else if (type === 'break') {
				osc.type = 'sawtooth';
				osc.frequency.setValueAtTime(180, now);
				osc.frequency.linearRampToValueAtTime(60, now + 0.15);
				gain.gain.setValueAtTime(0.3, now);
				gain.gain.linearRampToValueAtTime(0.01, now + 0.15);
				osc.start(now);
				osc.stop(now + 0.15);
			} else if (type === 'star') {
				osc.type = 'sine';
				osc.frequency.setValueAtTime(523.25, now);
				osc.frequency.setValueAtTime(659.25, now + 0.08);
				osc.frequency.setValueAtTime(783.99, now + 0.16);
				gain.gain.setValueAtTime(0.3, now);
				gain.gain.linearRampToValueAtTime(0.01, now + 0.25);
				osc.start(now);
				osc.stop(now + 0.25);
			} else if (type === 'gameover') {
				osc.type = 'sawtooth';
				osc.frequency.setValueAtTime(300, now);
				osc.frequency.linearRampToValueAtTime(90, now + 0.4);
				gain.gain.setValueAtTime(0.4, now);
				gain.gain.linearRampToValueAtTime(0.01, now + 0.4);
				osc.start(now);
				osc.stop(now + 0.4);
			}
		} catch (e) {}
	}

	// Player Avatar Image
	var avatarImg = new Image();
	avatarImg.src = window.AUGSUP_USER_AVATAR || '/dati/bildes/u_small/none.png';

	// Game Constants
	var GRAVITY = 0.42;
	var JUMP_FORCE = -11.5;
	var SPRING_FORCE = -18.0;
	var PLAYER_SIZE = 40;
	var PLATFORM_WIDTH = 68;
	var PLATFORM_HEIGHT = 14;

	// Entities
	var player = {
		x: 180,
		y: 400,
		vx: 0,
		vy: 0,
		facingLeft: false
	};

	var platforms = [];
	var stars = [];

	// Input State
	var keys = {
		left: false,
		right: false
	};

	function createPlatform(y, forceType) {
		var x = Math.random() * (canvas.width - PLATFORM_WIDTH);
		var type = forceType || 'normal';

		if (!forceType) {
			var lastPlat = platforms.length > 0 ? platforms[platforms.length - 1] : null;
			var r = Math.random();

			// Never spawn two broken platforms consecutively
			if (lastPlat && lastPlat.type === 'broken') {
				type = r < 0.65 ? 'normal' : (r < 0.85 ? 'moving' : 'spring');
			} else {
				if (r < 0.60) {
					type = 'normal'; // 60% Green static
				} else if (r < 0.78) {
					type = 'moving'; // 18% Blue moving
				} else if (r < 0.88) {
					type = 'broken'; // 10% Brown breaking
				} else {
					type = 'spring'; // 12% Spring platform
				}
			}
		}

		var plat = {
			x: x,
			y: y,
			width: PLATFORM_WIDTH,
			height: PLATFORM_HEIGHT,
			type: type,
			vx: type === 'moving' ? (Math.random() > 0.5 ? 1.8 : -1.8) : 0,
			broken: false,
			hasSpring: type === 'spring',
			springCompressed: false
		};

		// 15% chance to spawn a star on static/moving platform
		if ((type === 'normal' || type === 'moving') && Math.random() < 0.15) {
			stars.push({
				x: x + PLATFORM_WIDTH / 2 - 10,
				y: y - 22,
				width: 20,
				height: 20,
				collected: false
			});
		}

		return plat;
	}

	function resetGame() {
		score = 0;
		isGameOver = false;
		player.x = canvas.width / 2 - PLAYER_SIZE / 2;
		player.y = canvas.height - 120;
		player.vx = 0;
		player.vy = JUMP_FORCE;

		platforms = [];
		stars = [];

		// Base starting platform under player
		platforms.push({
			x: canvas.width / 2 - PLATFORM_WIDTH / 2,
			y: canvas.height - 60,
			width: PLATFORM_WIDTH,
			height: PLATFORM_HEIGHT,
			type: 'normal',
			vx: 0,
			broken: false,
			hasSpring: false
		});

		// Initial platforms stack with safe 45-70px vertical spacing
		var currentY = canvas.height - 130;
		while (currentY > 0) {
			platforms.push(createPlatform(currentY));
			currentY -= Math.floor(Math.random() * 25 + 45);
		}

		startTime = Date.now();
		fetchInitToken();
	}

	function fetchInitToken() {
		$.getJSON('/augsup?action=init_token', function(data) {
			if (data && data.token) {
				sessionToken = data.token;
			}
		});
	}

	function handleInput() {
		if (keys.left) {
			player.vx = -6.5;
			player.facingLeft = true;
		} else if (keys.right) {
			player.vx = 6.5;
			player.facingLeft = false;
		} else {
			player.vx *= 0.82; // Friction
		}
	}

	function update() {
		if (!isRunning || isGameOver) return;

		handleInput();

		// Apply Physics
		player.vy += GRAVITY;
		player.x += player.vx;
		player.y += player.vy;

		// Screen Wrap Around
		if (player.x < -PLAYER_SIZE / 2) {
			player.x = canvas.width - PLAYER_SIZE / 2;
		} else if (player.x > canvas.width - PLAYER_SIZE / 2) {
			player.x = -PLAYER_SIZE / 2;
		}

		// Platform Collisions (only when falling downward)
		if (player.vy > 0) {
			for (var i = 0; i < platforms.length; i++) {
				var p = platforms[i];
				if (p.broken) continue;

				// Feet collision check
				var footY = player.y + PLAYER_SIZE;
				var prevFootY = footY - player.vy;

				if (
					player.x + PLAYER_SIZE * 0.7 > p.x &&
					player.x + PLAYER_SIZE * 0.3 < p.x + p.width &&
					prevFootY <= p.y + 4 &&
					footY >= p.y
				) {
					if (p.type === 'broken') {
						p.broken = true;
						playSound('break');
					} else if (p.hasSpring) {
						player.vy = SPRING_FORCE;
						p.springCompressed = true;
						playSound('spring');
					} else {
						player.vy = JUMP_FORCE;
						playSound('bounce');
					}
					break;
				}
			}
		}

		// Update Moving Platforms
		for (var j = 0; j < platforms.length; j++) {
			var plat = platforms[j];
			if (plat.type === 'moving') {
				plat.x += plat.vx;
				if (plat.x <= 0 || plat.x + plat.width >= canvas.width) {
					plat.vx *= -1;
				}
			}
		}

		// Star Item Collisions
		for (var k = 0; k < stars.length; k++) {
			var st = stars[k];
			if (!st.collected &&
				player.x < st.x + st.width &&
				player.x + PLAYER_SIZE > st.x &&
				player.y < st.y + st.height &&
				player.y + PLAYER_SIZE > st.y) {
				st.collected = true;
				score += 100;
				playSound('star');
			}
		}

		// Camera Scroll Upwards
		if (player.y < 240) {
			var dy = 240 - player.y;
			player.y = 240;
			score += Math.round(dy);

			// Shift all platforms & stars down by dy
			for (var m = 0; m < platforms.length; m++) {
				platforms[m].y += dy;
			}
			for (var n = 0; n < stars.length; n++) {
				stars[n].y += dy;
			}
		}

		// Remove platforms out of bottom view & spawn new top platforms
		platforms = platforms.filter(function(p) { return p.y < canvas.height + 20; });
		stars = stars.filter(function(s) { return s.y < canvas.height + 20; });

		var highestY = canvas.height;
		for (var pIdx = 0; pIdx < platforms.length; pIdx++) {
			if (platforms[pIdx].y < highestY) {
				highestY = platforms[pIdx].y;
			}
		}

		if (highestY > 50) {
			var newY = highestY - Math.floor(Math.random() * 25 + 45);
			platforms.push(createPlatform(newY));
		}

		// Game Over Condition (Falling below screen)
		if (player.y > canvas.height + 60) {
			gameOver();
		}
	}

	function draw() {
		ctx.clearRect(0, 0, canvas.width, canvas.height);

		// Background Sky Gradient
		var bgGradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
		bgGradient.addColorStop(0, '#7dd3fc');
		bgGradient.addColorStop(1, '#bae6fd');
		ctx.fillStyle = bgGradient;
		ctx.fillRect(0, 0, canvas.width, canvas.height);

		// Background Decorative Clouds
		ctx.fillStyle = 'rgba(255, 255, 255, 0.6)';
		ctx.beginPath();
		ctx.arc(60, (score * 0.1) % (canvas.height + 100) - 50, 40, 0, Math.PI * 2);
		ctx.arc(320, (score * 0.15 + 200) % (canvas.height + 100) - 50, 50, 0, Math.PI * 2);
		ctx.fill();

		// Draw Platforms
		for (var i = 0; i < platforms.length; i++) {
			var p = platforms[i];
			if (p.broken) continue;

			ctx.save();
			if (p.type === 'normal') {
				ctx.fillStyle = '#22c55e';
				ctx.strokeStyle = '#15803d';
			} else if (p.type === 'moving') {
				ctx.fillStyle = '#0284c7';
				ctx.strokeStyle = '#0369a1';
			} else if (p.type === 'broken') {
				ctx.fillStyle = '#a16207';
				ctx.strokeStyle = '#713f12';
			} else if (p.type === 'spring') {
				ctx.fillStyle = '#16a34a';
				ctx.strokeStyle = '#15803d';
			}

			// Rounded platform rect
			ctx.lineWidth = 2;
			ctx.beginPath();
			ctx.roundRect(p.x, p.y, p.width, p.height, 6);
			ctx.fill();
			ctx.stroke();

			// Draw Spring if applicable
			if (p.hasSpring) {
				ctx.fillStyle = '#94a3b8';
				var springH = p.springCompressed ? 4 : 10;
				ctx.fillRect(p.x + p.width / 2 - 6, p.y - springH, 12, springH);
			}

			ctx.restore();
		}

		// Draw Stars
		for (var j = 0; j < stars.length; j++) {
			var s = stars[j];
			if (s.collected) continue;

			ctx.save();
			ctx.font = '18px sans-serif';
			ctx.textAlign = 'center';
			ctx.textBaseline = 'middle';
			ctx.fillText('⭐', s.x + s.width / 2, s.y + s.height / 2);
			ctx.restore();
		}

		// Draw Player Avatar
		ctx.save();
		ctx.translate(player.x + PLAYER_SIZE / 2, player.y + PLAYER_SIZE / 2);
		if (player.facingLeft) {
			ctx.scale(-1, 1);
		}

		// Circular Avatar Frame
		ctx.beginPath();
		ctx.arc(0, 0, PLAYER_SIZE / 2, 0, Math.PI * 2);
		ctx.clip();

		if (avatarImg.complete && avatarImg.naturalWidth !== 0) {
			ctx.drawImage(avatarImg, -PLAYER_SIZE / 2, -PLAYER_SIZE / 2, PLAYER_SIZE, PLAYER_SIZE);
		} else {
			ctx.fillStyle = '#0284c7';
			ctx.fillRect(-PLAYER_SIZE / 2, -PLAYER_SIZE / 2, PLAYER_SIZE, PLAYER_SIZE);
		}
		ctx.restore();

		// Draw Player Border Ring
		ctx.save();
		ctx.lineWidth = 3;
		ctx.strokeStyle = '#ffffff';
		ctx.beginPath();
		ctx.arc(player.x + PLAYER_SIZE / 2, player.y + PLAYER_SIZE / 2, PLAYER_SIZE / 2, 0, Math.PI * 2);
		ctx.stroke();
		ctx.restore();

		// Draw Current Score (HUD)
		ctx.save();
		ctx.font = 'bold 22px sans-serif';
		ctx.fillStyle = '#0f172a';
		ctx.textAlign = 'left';
		ctx.shadowColor = 'rgba(255,255,255,0.8)';
		ctx.shadowBlur = 4;
		ctx.fillText(score + ' m', 15, 35);
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
		playSound('gameover');

		var duration = Math.round((Date.now() - startTime) / 1000);
		var finalScore = score;

		$('#augsup-final-score').text(finalScore + ' m');
		$('#augsup-gameover-overlay').fadeIn(200);

		if (finalScore > highScore) {
			highScore = finalScore;
			$('#augsup-best-score').text(highScore + ' m');
			$('#stat-highscore').text(highScore + ' m');
			$('#augsup-record-alert').show();
		} else {
			$('#augsup-record-alert').hide();
		}

		// Submit Highscore via AJAX
		if (finalScore > 0) {
			$.post('/augsup?action=push', {
				score: finalScore,
				duration: duration,
				token: sessionToken
			}, function(response) {
				if (response && response.success) {
					if (response.highScore) {
						highScore = response.highScore;
						$('#augsup-best-score').text(highScore + ' m');
						$('#stat-highscore').text(highScore + ' m');
					}
				}
			}, 'json');
		}
	}

	$(document).ready(function() {
		canvas = document.getElementById('augsup-canvas');
		if (!canvas) return;
		ctx = canvas.getContext('2d');

		if (window.AUGSUP_USER_AVATAR) {
			avatarImg.src = window.AUGSUP_USER_AVATAR;
		}

		highScore = window.AUGSUP_USER_HIGHSCORE || 0;

		// Canvas Polyfill for roundRect if needed
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
			if (e.key === 'ArrowLeft' || e.key === 'a' || e.key === 'A') {
				keys.left = true;
			} else if (e.key === 'ArrowRight' || e.key === 'd' || e.key === 'D') {
				keys.right = true;
			} else if (e.key === ' ' || e.code === 'Space') {
				if (isGameOver || !isRunning) {
					$('#augsup-start-btn').click();
					e.preventDefault();
				}
			}
		});

		$(window).on('keyup', function(e) {
			if (e.key === 'ArrowLeft' || e.key === 'a' || e.key === 'A') {
				keys.left = false;
			} else if (e.key === 'ArrowRight' || e.key === 'd' || e.key === 'D') {
				keys.right = false;
			}
		});

		// Mobile Touch Controls
		$('#touch-left').on('touchstart mousedown', function(e) {
			e.preventDefault();
			initAudio();
			keys.left = true;
		}).on('touchend mouseup touchcancel', function(e) {
			e.preventDefault();
			keys.left = false;
		});

		$('#touch-right').on('touchstart mousedown', function(e) {
			e.preventDefault();
			initAudio();
			keys.right = true;
		}).on('touchend mouseup touchcancel', function(e) {
			e.preventDefault();
			keys.right = false;
		});

		// Start & Restart Buttons
		$('#augsup-start-btn, #augsup-restart-btn').on('click', function() {
			initAudio();
			$('#augsup-start-overlay').hide();
			$('#augsup-gameover-overlay').hide();
			resetGame();
			isRunning = true;
			requestAnimationFrame(gameLoop);
		});

		// Sound Toggle
		$('#augsup-sound-toggle').on('click', function() {
			soundEnabled = !soundEnabled;
			$(this).text(soundEnabled ? '🔊 Ieslēgta' : '🔇 Izslēgta');
		});
	});
})();
