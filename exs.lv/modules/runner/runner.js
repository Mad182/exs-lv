/**
 * Bezgalīgais Bēdzējs (Endless Runner) Game Engine for EXS.LV
 */

$(document).ready(function () {
	var $canvas = $('#runner-canvas');
	if ($canvas.length === 0) return;

	var canvas = $canvas[0];
	var ctx = canvas.getContext('2d');

	// Game States
	var STATE_START = 0;
	var STATE_PLAYING = 1;
	var STATE_GAMEOVER = 2;
	var gameState = STATE_START;

	// Session & Anti-Cheat
	var sessionToken = '';

	// Audio Context for Web Audio API Synth
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

	function playSynthSound(type) {
		if (!audioCtx) return;
		try {
			var now = audioCtx.currentTime;
			if (type === 'jump') {
				var osc = audioCtx.createOscillator();
				var gain = audioCtx.createGain();
				osc.type = 'sine';
				osc.frequency.setValueAtTime(160, now);
				osc.frequency.exponentialRampToValueAtTime(480, now + 0.15);
				gain.gain.setValueAtTime(0.3, now);
				gain.gain.exponentialRampToValueAtTime(0.01, now + 0.15);
				osc.connect(gain);
				gain.connect(audioCtx.destination);
				osc.start(now);
				osc.stop(now + 0.15);
			} else if (type === 'duck') {
				var osc = audioCtx.createOscillator();
				var gain = audioCtx.createGain();
				osc.type = 'triangle';
				osc.frequency.setValueAtTime(320, now);
				osc.frequency.exponentialRampToValueAtTime(120, now + 0.12);
				gain.gain.setValueAtTime(0.25, now);
				gain.gain.exponentialRampToValueAtTime(0.01, now + 0.12);
				osc.connect(gain);
				gain.connect(audioCtx.destination);
				osc.start(now);
				osc.stop(now + 0.12);
			} else if (type === 'coin') {
				var osc = audioCtx.createOscillator();
				var gain = audioCtx.createGain();
				osc.type = 'sine';
				osc.frequency.setValueAtTime(987.77, now); // B5
				osc.frequency.setValueAtTime(1318.51, now + 0.08); // E6
				gain.gain.setValueAtTime(0.3, now);
				gain.gain.exponentialRampToValueAtTime(0.01, now + 0.25);
				osc.connect(gain);
				gain.connect(audioCtx.destination);
				osc.start(now);
				osc.stop(now + 0.25);
			} else if (type === 'crash') {
				var osc = audioCtx.createOscillator();
				var gain = audioCtx.createGain();
				osc.type = 'sawtooth';
				osc.frequency.setValueAtTime(180, now);
				osc.frequency.exponentialRampToValueAtTime(40, now + 0.35);
				gain.gain.setValueAtTime(0.4, now);
				gain.gain.exponentialRampToValueAtTime(0.01, now + 0.35);
				osc.connect(gain);
				gain.connect(audioCtx.destination);
				osc.start(now);
				osc.stop(now + 0.35);
			} else if (type === 'big_star') {
				var osc1 = audioCtx.createOscillator();
				var osc2 = audioCtx.createOscillator();
				var gain = audioCtx.createGain();
				osc1.type = 'sine';
				osc2.type = 'triangle';
				osc1.frequency.setValueAtTime(880, now);
				osc1.frequency.setValueAtTime(1318.51, now + 0.08);
				osc1.frequency.setValueAtTime(1760, now + 0.16);
				osc2.frequency.setValueAtTime(440, now);
				osc2.frequency.setValueAtTime(659.25, now + 0.08);
				osc2.frequency.setValueAtTime(880, now + 0.16);
				gain.gain.setValueAtTime(0.35, now);
				gain.gain.exponentialRampToValueAtTime(0.01, now + 0.35);
				osc1.connect(gain);
				osc2.connect(gain);
				gain.connect(audioCtx.destination);
				osc1.start(now);
				osc2.start(now);
				osc1.stop(now + 0.35);
				osc2.stop(now + 0.35);
			} else if (type === 'tortoise') {
				var osc = audioCtx.createOscillator();
				var gain = audioCtx.createGain();
				osc.type = 'sine';
				osc.frequency.setValueAtTime(600, now);
				osc.frequency.exponentialRampToValueAtTime(220, now + 0.3);
				gain.gain.setValueAtTime(0.4, now);
				gain.gain.exponentialRampToValueAtTime(0.01, now + 0.3);
				osc.connect(gain);
				gain.connect(audioCtx.destination);
				osc.start(now);
				osc.stop(now + 0.3);
			}
		} catch (e) { }
	}

	// Preloaded Avatars
	var playerAvatarImg = new Image();
	playerAvatarImg.src = $canvas.data('avatar') || '/dati/bildes/u_small/none.png';

	var obstacleAvatars = [];
	function loadAvatars(avatarPaths) {
		obstacleAvatars = [];
		if (!avatarPaths || avatarPaths.length === 0) {
			avatarPaths = ['/dati/bildes/u_small/none.png'];
		}
		for (var i = 0; i < avatarPaths.length; i++) {
			var img = new Image();
			img.src = avatarPaths[i];
			obstacleAvatars.push(img);
		}
	}

	// Fetch Session Token and Community Avatars
	function initSessionToken() {
		$.getJSON('/runner/?action=init_token', function (res) {
			if (res && res.success) {
				sessionToken = res.token;
				if (res.avatars) {
					loadAvatars(res.avatars);
				}
			}
		});
	}
	initSessionToken();

	// World Constants
	var GROUND_Y = 320;
	var GRAVITY = 0.85;

	// Player Object
	var player = {
		x: 90,
		y: GROUND_Y - 44,
		width: 44,
		height: 44,
		velocityY: 0,
		isGrounded: true,
		isDucking: false,
		runFrame: 0,

		reset: function () {
			this.x = 90;
			this.y = GROUND_Y - 44;
			this.width = 44;
			this.height = 44;
			this.velocityY = 0;
			this.isGrounded = true;
			this.isDucking = false;
			this.runFrame = 0;
		},

		jump: function () {
			if (this.isGrounded) {
				this.velocityY = -15.2;
				this.isGrounded = false;
				this.isDucking = false;
				this.height = 44;
				playSynthSound('jump');
			}
		},

		duck: function (ducking) {
			if (this.isGrounded) {
				if (ducking && !this.isDucking) {
					this.isDucking = true;
					this.height = 24;
					this.y = GROUND_Y - 24;
					playSynthSound('duck');
				} else if (!ducking && this.isDucking) {
					this.isDucking = false;
					this.height = 44;
					this.y = GROUND_Y - 44;
				}
			}
		},

		update: function () {
			if (!this.isGrounded) {
				this.velocityY += GRAVITY;
				this.y += this.velocityY;

				if (this.y >= GROUND_Y - this.height) {
					this.y = GROUND_Y - this.height;
					this.velocityY = 0;
					this.isGrounded = true;
				}
			} else {
				if (this.isDucking) {
					this.height = 24;
					this.y = GROUND_Y - 24;
				} else {
					this.height = 44;
					this.y = GROUND_Y - 44;
				}
				this.runFrame += 0.2;
			}
		},

		draw: function () {
			ctx.save();

			var renderY = this.y;
			var renderH = this.height;

			// Draw Avatar inside circular clip
			ctx.beginPath();
			ctx.arc(this.x + this.width / 2, renderY + renderH / 2, Math.min(this.width, renderH) / 2, 0, Math.PI * 2);
			ctx.closePath();
			ctx.clip();

			if (playerAvatarImg.complete && playerAvatarImg.naturalWidth !== 0) {
				ctx.drawImage(playerAvatarImg, this.x, renderY, this.width, renderH);
			} else {
				ctx.fillStyle = '#3b82f6';
				ctx.fillRect(this.x, renderY, this.width, renderH);
			}

			ctx.restore();

			// Avatar Glowing Ring Border
			ctx.strokeStyle = '#3b82f6';
			ctx.lineWidth = 3;
			ctx.beginPath();
			ctx.arc(this.x + this.width / 2, renderY + renderH / 2, Math.min(this.width, renderH) / 2, 0, Math.PI * 2);
			ctx.stroke();

			// Running dust effect if grounded
			if (this.isGrounded) {
				ctx.fillStyle = 'rgba(203, 213, 225, 0.4)';
				var dustX = this.x - 6 - (Math.sin(this.runFrame) * 4);
				ctx.beginPath();
				ctx.arc(dustX, GROUND_Y - 4, 4, 0, Math.PI * 2);
				ctx.fill();
			}
		}
	};

	// Obstacle, Item & Bonus Arrays
	var obstacles = [];
	var coins = [];
	var bonuses = [];
	var particles = [];
	var floatingTexts = [];

	// Game Engine Stats
	var gameSpeed = 6.0;
	var distanceRun = 0;
	var coinsCollected = 0;
	var spawnTimer = 0;
	var targetSpawnInterval = 90;
	var startTime = 0;

	function getNextSpawnInterval(lastType) {
		// Base interval shrinks as game speed increases (from ~95 frames down to ~38 frames)
		var baseInterval = Math.max(38, 95 - (gameSpeed - 6.0) * 4.0);

		// Add random variance: 70% to 140% of base interval
		var randomFactor = 0.70 + (Math.random() * 0.70);
		var interval = Math.floor(baseInterval * randomFactor);

		// If previous obstacle was a wide double/triple obstacle, add extra clearance frames
		if (lastType === 'double_flying') {
			interval += Math.floor(18 + Math.random() * 14);
		} else if (lastType === 'triple_flying') {
			interval += Math.floor(28 + Math.random() * 20);
		}

		return interval;
	}

	function resetGame() {
		player.reset();
		obstacles = [];
		coins = [];
		bonuses = [];
		particles = [];
		floatingTexts = [];
		gameSpeed = 6.0;
		distanceRun = 0;
		coinsCollected = 0;
		spawnTimer = 0;
		targetSpawnInterval = getNextSpawnInterval();
		startTime = Date.now();

		updateHUD();
	}

	function spawnObstacle() {
		var typeRand = Math.random();
		var avatarImg = null;
		if (obstacleAvatars.length > 0) {
			avatarImg = obstacleAvatars[Math.floor(Math.random() * obstacleAvatars.length)];
		}

		var spawnedType = '';

		if (typeRand < 0.52) {
			// 1. Single Ground User Avatar Obstacle (52% - Jump over)
			// Height/Size Variation: size ranges randomly from 34 to 52 px
			spawnedType = 'ground_avatar';
			var size = Math.floor(34 + Math.random() * 19);
			obstacles.push({
				type: 'ground_avatar',
				x: canvas.width + 40,
				y: GROUND_Y - size,
				width: size,
				height: size,
				img: avatarImg
			});
		} else if (typeRand < 0.82) {
			// 2. Single Flying Drone Avatar (30% - Duck under)
			// Height/Elevation Variation: Y position varies between GROUND_Y - 73 and GROUND_Y - 61
			spawnedType = 'flying_avatar';
			var size = Math.floor(34 + Math.random() * 10);
			var yElev = Math.floor(61 + Math.random() * 13);
			obstacles.push({
				type: 'flying_avatar',
				x: canvas.width + 40,
				y: GROUND_Y - yElev,
				width: size,
				height: size,
				img: avatarImg,
				hoverOffset: Math.random() * Math.PI * 2
			});
		} else if (typeRand < 0.92) {
			// 3. Double Flying Drone Obstacle (10% - Double Width Hazard)
			spawnedType = 'double_flying';
			var subSize = 36;
			var gap = 8;
			var totalWidth = (subSize * 2) + gap; // 80px wide

			var imgs = [];
			for (var i = 0; i < 2; i++) {
				if (obstacleAvatars.length > 0) {
					imgs.push(obstacleAvatars[Math.floor(Math.random() * obstacleAvatars.length)]);
				} else {
					imgs.push(null);
				}
			}

			obstacles.push({
				type: 'double_flying',
				x: canvas.width + 40,
				y: GROUND_Y - 65,
				width: totalWidth,
				height: subSize,
				subSize: subSize,
				gap: gap,
				imgs: imgs,
				hoverOffset: Math.random() * Math.PI * 2
			});
		} else {
			// 4. Triple Flying Drone Obstacle (8% - Triple Width Hazard)
			spawnedType = 'triple_flying';
			var subSize = 36;
			var gap = 8;
			var totalWidth = (subSize * 3) + (gap * 2); // 124px wide!

			var imgs = [];
			for (var i = 0; i < 3; i++) {
				if (obstacleAvatars.length > 0) {
					imgs.push(obstacleAvatars[Math.floor(Math.random() * obstacleAvatars.length)]);
				} else {
					imgs.push(null);
				}
			}

			obstacles.push({
				type: 'triple_flying',
				x: canvas.width + 40,
				y: GROUND_Y - 65,
				width: totalWidth,
				height: subSize,
				subSize: subSize,
				gap: gap,
				imgs: imgs,
				hoverOffset: Math.random() * Math.PI * 2
			});
		}

		// Spawn Golden Star Coins in air or underneath double/triple obstacles
		if (spawnedType === 'double_flying') {
			// Reward ducking under double obstacle with 2 coins
			for (var c = 0; c < 2; c++) {
				coins.push({
					x: canvas.width + 58 + (c * 36),
					y: GROUND_Y - 15,
					radius: 9,
					collected: false
				});
			}
		} else if (spawnedType === 'triple_flying') {
			// Reward ducking under triple obstacle with 3 coins
			for (var c = 0; c < 3; c++) {
				coins.push({
					x: canvas.width + 58 + (c * 36),
					y: GROUND_Y - 15,
					radius: 9,
					collected: false
				});
			}
		} else if (Math.random() < 0.6) {
			var coinY = (Math.random() < 0.5) ? GROUND_Y - 35 : GROUND_Y - 85;
			for (var c = 0; c < 3; c++) {
				coins.push({
					x: canvas.width + 120 + (c * 30),
					y: coinY,
					radius: 9,
					collected: false
				});
			}
		}

		// Spawn Rare Bonuses
		if (gameSpeed >= 10.0 && Math.random() < 0.2) {
			var bonusType = 'big_star';
			if (gameSpeed >= 15.0 && Math.random() < 0.2) {
				bonusType = 'tortoise';
			}
			var bonusY = GROUND_Y - 45 - (Math.random() * 65);
			bonuses.push({
				type: bonusType,
				x: canvas.width + 160,
				y: bonusY,
				radius: (bonusType === 'big_star') ? 16 : 14,
				animFrame: 0,
				collected: false
			});
		}

		return spawnedType;
	}

	function addFloatingText(text, x, y, color) {
		floatingTexts.push({
			text: text,
			x: x,
			y: y,
			color: color || '#ffffff',
			alpha: 1.0
		});
	}

	function updateGame() {
		if (gameState !== STATE_PLAYING) return;

		// Increase distance & speed over time
		distanceRun += gameSpeed * 0.15;
		gameSpeed += 0.0012; // Gradually ramp difficulty
		if (gameSpeed > 24.0) gameSpeed = 24.0;

		player.update();

		// Spawn Obstacles with dynamic variable spacing
		spawnTimer++;
		if (spawnTimer >= targetSpawnInterval) {
			var spawnedType = spawnObstacle();
			spawnTimer = 0;
			targetSpawnInterval = getNextSpawnInterval(spawnedType);
		}

		// Update Obstacles
		for (var i = obstacles.length - 1; i >= 0; i--) {
			var obs = obstacles[i];
			obs.x -= gameSpeed;

			if (obs.type === 'flying_avatar' || obs.type === 'double_flying' || obs.type === 'triple_flying') {
				obs.hoverOffset += 0.08;
			}

			// Collision detection
			if (checkCollision(player, obs)) {
				triggerGameOver();
				return;
			}

			if (obs.x + obs.width < -50) {
				obstacles.splice(i, 1);
			}
		}

		// Update Coins
		for (var i = coins.length - 1; i >= 0; i--) {
			var coin = coins[i];
			coin.x -= gameSpeed;

			// Collision check with player
			if (!coin.collected && checkCircleBoxCollision(coin, player)) {
				coin.collected = true;
				coinsCollected++;
				distanceRun += 25; // Bonus distance
				playSynthSound('coin');
				createSparkles(coin.x, coin.y);
			}

			if (coin.x < -30) {
				coins.splice(i, 1);
			}
		}

		// Update Bonuses
		for (var i = bonuses.length - 1; i >= 0; i--) {
			var b = bonuses[i];
			b.x -= gameSpeed;

			// Collision check with player
			if (!b.collected && checkCircleBoxCollision(b, player)) {
				b.collected = true;
				if (b.type === 'big_star') {
					coinsCollected += 5;
					distanceRun += 50;
					playSynthSound('big_star');
					createSparkles(b.x, b.y, '#fbbf24', 16);
					addFloatingText('+5 ⭐', b.x, b.y, '#f59e0b');
				} else if (b.type === 'tortoise') {
					// Reduces speed by 0.5x (3.0 units in gameSpeed = 0.5x multiplier)
					gameSpeed = Math.max(6.0, gameSpeed - 3.0);
					playSynthSound('tortoise');
					createSparkles(b.x, b.y, '#4ade80', 16);
					addFloatingText('-0.5x ĀTRUMS! 🐢', b.x, b.y, '#22c55e');
				}
			}

			if (b.x < -40) {
				bonuses.splice(i, 1);
			}
		}

		// Update Floating Texts
		for (var i = floatingTexts.length - 1; i >= 0; i--) {
			var ft = floatingTexts[i];
			ft.y -= 0.8;
			ft.alpha -= 0.02;
			if (ft.alpha <= 0) {
				floatingTexts.splice(i, 1);
			}
		}

		// Update Particles
		for (var i = particles.length - 1; i >= 0; i--) {
			var p = particles[i];
			p.x += p.vx;
			p.y += p.vy;
			p.alpha -= 0.03;
			if (p.alpha <= 0) {
				particles.splice(i, 1);
			}
		}

		updateHUD();
	}

	function getObstacleY(obs) {
		if (obs.type === 'flying_avatar' || obs.type === 'double_flying' || obs.type === 'triple_flying') {
			return obs.y + Math.sin(obs.hoverOffset) * 4;
		}
		return obs.y;
	}

	function checkCollision(p, o) {
		var pMargin = 4;
		var oY = getObstacleY(o);
		return (
			p.x + pMargin < o.x + o.width - pMargin &&
			p.x + p.width - pMargin > o.x + pMargin &&
			p.y + pMargin < oY + o.height - pMargin &&
			p.y + p.height - pMargin > oY + pMargin
		);
	}

	function checkCircleBoxCollision(circle, box) {
		var closestX = Math.max(box.x, Math.min(circle.x, box.x + box.width));
		var closestY = Math.max(box.y, Math.min(circle.y, box.y + box.height));
		var distanceX = circle.x - closestX;
		var distanceY = circle.y - closestY;
		return (distanceX * distanceX + distanceY * distanceY) < (circle.radius * circle.radius);
	}

	function createSparkles(x, y, customColor, count) {
		var pColor = customColor || '#f59e0b';
		var pCount = count || 8;
		for (var i = 0; i < pCount; i++) {
			particles.push({
				x: x,
				y: y,
				vx: (Math.random() - 0.5) * 6,
				vy: (Math.random() - 0.5) * 6,
				alpha: 1.0,
				color: pColor
			});
		}
	}

	var backgroundOffset = 0;

	function render() {
		ctx.clearRect(0, 0, canvas.width, canvas.height);

		// Background Sky Gradient
		var skyGrad = ctx.createLinearGradient(0, 0, 0, GROUND_Y);
		skyGrad.addColorStop(0, '#0f172a');
		skyGrad.addColorStop(1, '#1e293b');
		ctx.fillStyle = skyGrad;
		ctx.fillRect(0, 0, canvas.width, canvas.height);

		// Parallax Mountains
		backgroundOffset += gameSpeed * 0.2;
		ctx.fillStyle = '#1e2430';
		ctx.beginPath();
		ctx.moveTo(0, GROUND_Y);
		for (var m = 0; m <= canvas.width + 100; m += 80) {
			var peakY = GROUND_Y - 70 - (Math.sin((m + backgroundOffset) * 0.01) * 35);
			ctx.lineTo(m, peakY);
		}
		ctx.lineTo(canvas.width, GROUND_Y);
		ctx.closePath();
		ctx.fill();

		// Parallax Hills
		ctx.fillStyle = '#252d3a';
		ctx.beginPath();
		ctx.moveTo(0, GROUND_Y);
		for (var h = 0; h <= canvas.width + 100; h += 50) {
			var hillY = GROUND_Y - 35 - (Math.sin((h + backgroundOffset * 2) * 0.02) * 20);
			ctx.lineTo(h, hillY);
		}
		ctx.lineTo(canvas.width, GROUND_Y);
		ctx.closePath();
		ctx.fill();

		// Ground Line & Grid
		ctx.fillStyle = '#334155';
		ctx.fillRect(0, GROUND_Y, canvas.width, canvas.height - GROUND_Y);

		ctx.strokeStyle = '#475569';
		ctx.lineWidth = 3;
		ctx.beginPath();
		ctx.moveTo(0, GROUND_Y);
		ctx.lineTo(canvas.width, GROUND_Y);
		ctx.stroke();

		// Moving Ground Details
		ctx.fillStyle = '#64748b';
		for (var g = -(backgroundOffset * 3 % 40); g < canvas.width; g += 40) {
			ctx.fillRect(g, GROUND_Y + 10, 20, 3);
		}

		// Draw Coins
		for (var i = 0; i < coins.length; i++) {
			var c = coins[i];
			if (c.collected) continue;

			ctx.save();
			ctx.fillStyle = '#f59e0b';
			ctx.strokeStyle = '#fbbf24';
			ctx.lineWidth = 2;
			ctx.beginPath();
			ctx.arc(c.x, c.y, c.radius, 0, Math.PI * 2);
			ctx.fill();
			ctx.stroke();

			// Star emblem in center
			ctx.fillStyle = '#ffffff';
			ctx.font = 'bold 10px sans-serif';
			ctx.textAlign = 'center';
			ctx.textBaseline = 'middle';
			ctx.fillText('★', c.x, c.y + 1);
			ctx.restore();
		}

		// Draw Bonuses (Large Star and Tortoise)
		for (var i = 0; i < bonuses.length; i++) {
			var b = bonuses[i];
			if (b.collected) continue;
			b.animFrame++;

			ctx.save();
			if (b.type === 'big_star') {
				var pulse = Math.sin(b.animFrame * 0.12) * 3;
				var r = b.radius + pulse;

				// Outer radial glow
				var grad = ctx.createRadialGradient(b.x, b.y, 2, b.x, b.y, r + 8);
				grad.addColorStop(0, 'rgba(251, 191, 36, 0.9)');
				grad.addColorStop(0.6, 'rgba(245, 158, 11, 0.4)');
				grad.addColorStop(1, 'rgba(245, 158, 11, 0)');
				ctx.fillStyle = grad;
				ctx.beginPath();
				ctx.arc(b.x, b.y, r + 8, 0, Math.PI * 2);
				ctx.fill();

				// Star body
				ctx.fillStyle = '#fbbf24';
				ctx.strokeStyle = '#ffffff';
				ctx.lineWidth = 2;

				ctx.beginPath();
				for (var s = 0; s < 5; s++) {
					var outerAngle = (s * 4 * Math.PI / 5) - Math.PI / 2;
					var innerAngle = outerAngle + (2 * Math.PI / 10);
					var xOuter = b.x + Math.cos(outerAngle) * r;
					var yOuter = b.y + Math.sin(outerAngle) * r;
					var xInner = b.x + Math.cos(innerAngle) * (r * 0.45);
					var yInner = b.y + Math.sin(innerAngle) * (r * 0.45);

					if (s === 0) ctx.moveTo(xOuter, yOuter);
					else ctx.lineTo(xOuter, yOuter);
					ctx.lineTo(xInner, yInner);
				}
				ctx.closePath();
				ctx.fill();
				ctx.stroke();

				// Inner "+5" text emblem
				ctx.fillStyle = '#78350f';
				ctx.font = '900 11px sans-serif';
				ctx.textAlign = 'center';
				ctx.textBaseline = 'middle';
				ctx.fillText('+5', b.x, b.y + 1);
			} else if (b.type === 'tortoise') {
				var pulse = Math.sin(b.animFrame * 0.12) * 2;
				var r = b.radius + pulse;

				// Green slow-down aura
				var grad = ctx.createRadialGradient(b.x, b.y, 2, b.x, b.y, r + 8);
				grad.addColorStop(0, 'rgba(74, 222, 128, 0.9)');
				grad.addColorStop(0.6, 'rgba(34, 197, 94, 0.4)');
				grad.addColorStop(1, 'rgba(34, 197, 94, 0)');
				ctx.fillStyle = grad;
				ctx.beginPath();
				ctx.arc(b.x, b.y, r + 8, 0, Math.PI * 2);
				ctx.fill();

				// Shell
				ctx.fillStyle = '#166534';
				ctx.beginPath();
				ctx.ellipse(b.x + 2, b.y, 13, 9, 0, 0, Math.PI * 2);
				ctx.fill();

				ctx.fillStyle = '#22c55e';
				ctx.beginPath();
				ctx.ellipse(b.x + 2, b.y - 2, 11, 7, 0, 0, Math.PI * 2);
				ctx.fill();

				// Shell patterns
				ctx.strokeStyle = '#15803d';
				ctx.lineWidth = 1.5;
				ctx.beginPath();
				ctx.moveTo(b.x - 3, b.y - 2); ctx.lineTo(b.x + 7, b.y - 2);
				ctx.moveTo(b.x - 1, b.y + 2); ctx.lineTo(b.x + 5, b.y + 2);
				ctx.moveTo(b.x + 2, b.y - 7); ctx.lineTo(b.x + 2, b.y + 4);
				ctx.stroke();

				// Tortoise Head (facing left)
				ctx.fillStyle = '#4ade80';
				ctx.beginPath();
				ctx.arc(b.x - 12, b.y + 1, 4.5, 0, Math.PI * 2);
				ctx.fill();

				// Eye
				ctx.fillStyle = '#0f172a';
				ctx.beginPath();
				ctx.arc(b.x - 13.5, b.y, 1, 0, Math.PI * 2);
				ctx.fill();

				// Feet
				ctx.fillStyle = '#15803d';
				ctx.fillRect(b.x - 6, b.y + 5, 3, 4);
				ctx.fillRect(b.x + 2, b.y + 5, 3, 4);
				ctx.fillRect(b.x + 8, b.y + 5, 3, 4);

				// Speed slow emblem "-0.5x"
				ctx.fillStyle = '#86efac';
				ctx.font = 'bold 10px sans-serif';
				ctx.textAlign = 'center';
				ctx.fillText('-0.5x', b.x, b.y - 12);
			}
			ctx.restore();
		}

		// Draw Obstacles (All obstacles are Community User Avatars)
		for (var i = 0; i < obstacles.length; i++) {
			var obs = obstacles[i];
			var drawY = getObstacleY(obs);

			ctx.save();
			if (obs.type === 'double_flying' || obs.type === 'triple_flying') {
				var count = (obs.type === 'double_flying') ? 2 : 3;

				// Connected Hazard Beam / Frame for Multi-Drone Obstacle
				var beamGrad = ctx.createLinearGradient(obs.x, drawY, obs.x + obs.width, drawY);
				beamGrad.addColorStop(0, 'rgba(239, 68, 68, 0.4)');
				beamGrad.addColorStop(0.5, 'rgba(220, 38, 38, 0.8)');
				beamGrad.addColorStop(1, 'rgba(239, 68, 68, 0.4)');
				ctx.fillStyle = beamGrad;
				ctx.fillRect(obs.x, drawY + 4, obs.width, obs.height - 8);

				ctx.strokeStyle = '#f87171';
				ctx.lineWidth = 2;
				ctx.strokeRect(obs.x, drawY + 4, obs.width, obs.height - 8);

				// Draw Individual Drones
				for (var k = 0; k < count; k++) {
					var subX = obs.x + k * (obs.subSize + obs.gap);
					var img = obs.imgs ? obs.imgs[k] : null;

					ctx.save();
					// Hazard Glow Outer Ring
					ctx.strokeStyle = '#ef4444';
					ctx.lineWidth = 3;
					ctx.beginPath();
					ctx.arc(subX + obs.subSize / 2, drawY + obs.subSize / 2, obs.subSize / 2 + 2, 0, Math.PI * 2);
					ctx.stroke();

					// Circular Clip for User Avatar
					ctx.beginPath();
					ctx.arc(subX + obs.subSize / 2, drawY + obs.subSize / 2, obs.subSize / 2, 0, Math.PI * 2);
					ctx.closePath();
					ctx.clip();

					if (img && img.complete && img.naturalWidth !== 0) {
						ctx.drawImage(img, subX, drawY, obs.subSize, obs.subSize);
					} else {
						ctx.fillStyle = '#ef4444';
						ctx.fillRect(subX, drawY, obs.subSize, obs.subSize);
					}
					ctx.restore();

					// Thruster jet flame for each drone
					ctx.fillStyle = '#f97316';
					ctx.beginPath();
					ctx.arc(subX + obs.subSize / 2, drawY + obs.subSize + 4, 5, 0, Math.PI * 2);
					ctx.fill();
				}
			} else {
				// Single Ground or Flying Avatar Obstacle
				var radius = Math.min(obs.width, obs.height) / 2;

				ctx.strokeStyle = '#ef4444';
				ctx.lineWidth = 3;
				ctx.beginPath();
				ctx.arc(obs.x + obs.width / 2, drawY + obs.height / 2, radius + 2, 0, Math.PI * 2);
				ctx.stroke();

				// Circular Clip for User Avatar
				ctx.beginPath();
				ctx.arc(obs.x + obs.width / 2, drawY + obs.height / 2, radius, 0, Math.PI * 2);
				ctx.closePath();
				ctx.clip();

				if (obs.img && obs.img.complete && obs.img.naturalWidth !== 0) {
					ctx.drawImage(obs.img, obs.x, drawY, obs.width, obs.height);
				} else {
					ctx.fillStyle = '#ef4444';
					ctx.fillRect(obs.x, drawY, obs.width, obs.height);
				}
				ctx.restore();

				if (obs.type === 'flying_avatar') {
					// Drone Thruster Jet Flame
					ctx.fillStyle = '#f97316';
					ctx.beginPath();
					ctx.arc(obs.x + obs.width / 2, drawY + obs.height + 4, 5, 0, Math.PI * 2);
					ctx.fill();
				}
			}
			ctx.restore();
		}

		// Draw Particles
		for (var i = 0; i < particles.length; i++) {
			var p = particles[i];
			ctx.save();
			ctx.globalAlpha = p.alpha;
			ctx.fillStyle = p.color;
			ctx.beginPath();
			ctx.arc(p.x, p.y, 3, 0, Math.PI * 2);
			ctx.fill();
			ctx.restore();
		}

		// Draw Player
		player.draw();

		// Draw Floating Texts
		for (var i = 0; i < floatingTexts.length; i++) {
			var ft = floatingTexts[i];
			ctx.save();
			ctx.globalAlpha = ft.alpha;
			ctx.fillStyle = ft.color;
			ctx.font = '900 15px sans-serif';
			ctx.textAlign = 'center';
			ctx.shadowColor = 'rgba(0, 0, 0, 0.7)';
			ctx.shadowBlur = 4;
			ctx.fillText(ft.text, ft.x, ft.y);
			ctx.restore();
		}
	}

	function gameLoop() {
		updateGame();
		render();
		requestAnimationFrame(gameLoop);
	}
	requestAnimationFrame(gameLoop);

	function updateHUD() {
		$('#runner-score').text(Math.floor(distanceRun) + ' m');
		$('#runner-coins').text('⭐ ' + coinsCollected);
		$('#runner-speed').text((gameSpeed / 6.0).toFixed(1) + 'x');
	}

	function triggerGameOver() {
		gameState = STATE_GAMEOVER;
		playSynthSound('crash');

		var finalScore = Math.floor(distanceRun);
		$('#final-score').text(finalScore + ' m');
		$('#final-coins').text(coinsCollected);
		$('#runner-gameover-overlay').fadeIn(200);

		// Submit score via AJAX
		var durationSec = Math.max(1, Math.floor((Date.now() - startTime) / 1000));
		if (sessionToken && finalScore > 0) {
			$.post('/runner/?action=push', {
				token: sessionToken,
				score: finalScore,
				coins: coinsCollected,
				duration: durationSec
			}, function (res) {
				if (res && res.success) {
					if (res.isNewRecord) {
						$('#runner-record-badge').show();
					} else {
						$('#runner-record-badge').hide();
					}
				}
			}, 'json');
		}
	}

	// User Input Handlers
	function handleJumpStart() {
		initAudio();
		if (gameState === STATE_PLAYING) {
			player.jump();
		}
	}

	function handleDuckStart() {
		initAudio();
		if (gameState === STATE_PLAYING) {
			player.duck(true);
		}
	}

	function handleDuckEnd() {
		if (gameState === STATE_PLAYING) {
			player.duck(false);
		}
	}

	// Keyboard Controls
	$(document).on('keydown', function (e) {
		if ($('#runner-container').length === 0) return;

		var key = e.key;
		if (key === 'Enter' || key === 'N' || key === 'n') {
			if (gameState === STATE_GAMEOVER) {
				e.preventDefault();
				$('#runner-restart-btn').trigger('click');
				return;
			} else if (gameState === STATE_START) {
				e.preventDefault();
				$('#runner-start-btn').trigger('click');
				return;
			}
		}

		if (key === 'ArrowUp' || key === 'w' || key === 'W' || key === ' ') {
			e.preventDefault();
			if (gameState === STATE_START) {
				$('#runner-start-btn').trigger('click');
			} else if (gameState === STATE_GAMEOVER) {
				$('#runner-restart-btn').trigger('click');
			} else {
				handleJumpStart();
			}
		} else if (key === 'ArrowDown' || key === 's' || key === 'S') {
			e.preventDefault();
			handleDuckStart();
		}
	});

	$(document).on('keyup', function (e) {
		var key = e.key;
		if (key === 'ArrowDown' || key === 's' || key === 'S') {
			handleDuckEnd();
		}
	});

	// Mobile & On-Screen Touch Buttons
	$('#btn-jump').on('touchstart mousedown', function (e) {
		e.preventDefault();
		handleJumpStart();
	});

	$('#btn-duck').on('touchstart mousedown', function (e) {
		e.preventDefault();
		handleDuckStart();
	});

	$('#btn-duck').on('touchend mouseup mouseleave', function (e) {
		e.preventDefault();
		handleDuckEnd();
	});

	// Overlay Buttons
	$('#runner-start-btn').on('click', function () {
		initAudio();
		$('#runner-start-overlay').fadeOut(200);
		resetGame();
		gameState = STATE_PLAYING;
	});

	$('#runner-restart-btn').on('click', function () {
		initAudio();
		$('#runner-gameover-overlay').fadeOut(200);
		resetGame();
		gameState = STATE_PLAYING;
	});
});
