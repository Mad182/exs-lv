/**
 * EXS.LV - Tornis (Tower Stacker) 3D Isometric Engine
 */

(function () {
	'use strict';

	function initTornis() {
		// Canvas & Context
		var canvas = document.getElementById('tornis-canvas');
		if (!canvas) return;
		var ctx = canvas.getContext('2d');
		var container = document.getElementById('tornis-container');

	// HUD & UI Elements
	var hudEl = document.getElementById('tornis-hud');
	var currentScoreEl = document.getElementById('hud-current-score');
	var comboBadgeEl = document.getElementById('hud-combo-badge');
	var comboTextEl = document.getElementById('hud-combo-text');
	var startOverlay = document.getElementById('tornis-start-overlay');
	var startBtn = document.getElementById('tornis-start-btn');
	var gameoverOverlay = document.getElementById('tornis-gameover-overlay');
	var finalScoreVal = document.getElementById('final-score-val');
	var finalComboVal = document.getElementById('final-combo-val');
	var finalMetersVal = document.getElementById('final-meters-val');
	var finalBestVal = document.getElementById('final-best-val');
	var restartBtn = document.getElementById('tornis-restart-btn');
	var newRecordBanner = document.getElementById('tornis-new-record-banner');
	var rankInfoBox = document.getElementById('tornis-rank-info');
	var rankValEl = document.getElementById('tornis-rank-val');
	var soundBtn = document.getElementById('tornis-sound-btn');
	var soundIcon = document.getElementById('sound-icon');
	var pauseBtn = document.getElementById('tornis-pause-btn');
	var pauseOverlay = document.getElementById('tornis-pause-overlay');
	var resumeBtn = document.getElementById('tornis-resume-btn');
	var bottomBestEl = document.getElementById('bottom-best-score');
	var mobileDropBtn = document.getElementById('tornis-mobile-drop-btn');

	// Game Configuration Constants
	var BLOCK_HEIGHT = 16;
	var INITIAL_SIZE = 140;
	var MIN_SIZE = 8;
	var BASE_SPEED = 140;
	var MAX_SPEED = 320;
	var PERFECT_TOLERANCE = 2.8;
	var ISO_ANGLE = Math.PI / 6; // 30 degrees
	var COS_ISO = Math.cos(ISO_ANGLE);
	var SIN_ISO = Math.sin(ISO_ANGLE);

	// Audio System (Web Audio API)
	var audioCtx = null;
	var soundEnabled = true;
	if (localStorage.getItem('tornis_sound') === '0') {
		soundEnabled = false;
	}

	function updateSoundIcon() {
		if (soundIcon) soundIcon.textContent = soundEnabled ? '🔊' : '🔇';
		if (soundBtn) {
			soundBtn.innerHTML = (soundEnabled ? '🔊' : '🔇') + ' Skaņa: ' + (soundEnabled ? 'Iesl.' : 'Izsl.');
		}
	}
	updateSoundIcon();

	function getAudioContext() {
		if (!audioCtx) {
			var AudioContextClass = window.AudioContext || window.webkitAudioContext;
			if (AudioContextClass) {
				audioCtx = new AudioContextClass();
			}
		}
		if (audioCtx && audioCtx.state === 'suspended') {
			audioCtx.resume();
		}
		return audioCtx;
	}

	// Pentatonic scale notes for ascending combo chimes
	var SCALE_FREQS = [
		261.63, 293.66, 329.63, 392.00, 440.00, // C4, D4, E4, G4, A4
		523.25, 587.33, 659.25, 783.99, 880.00, // C5, D5, E5, G5, A5
		1046.50, 1174.66, 1318.51, 1567.98       // C6, D6, E6, G6
	];

	function playPlaceSound(combo) {
		if (!soundEnabled) return;
		var actx = getAudioContext();
		if (!actx) return;

		var now = actx.currentTime;
		if (combo > 0) {
			// Perfect placement chime
			var noteIdx = Math.min(SCALE_FREQS.length - 1, combo - 1);
			var freq = SCALE_FREQS[noteIdx];

			var osc = actx.createOscillator();
			var gain = actx.createGain();
			osc.type = 'triangle';
			osc.frequency.setValueAtTime(freq, now);

			// Shimmer overtone
			var osc2 = actx.createOscillator();
			var gain2 = actx.createGain();
			osc2.type = 'sine';
			osc2.frequency.setValueAtTime(freq * 2, now);

			gain.gain.setValueAtTime(0.28, now);
			gain.gain.exponentialRampToValueAtTime(0.001, now + 0.45);

			gain2.gain.setValueAtTime(0.12, now);
			gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.35);

			osc.connect(gain);
			gain.connect(actx.destination);
			osc2.connect(gain2);
			gain2.connect(actx.destination);

			osc.start(now);
			osc.stop(now + 0.45);
			osc2.start(now);
			osc2.stop(now + 0.35);
		} else {
			// Normal slice/chop sound
			var osc = actx.createOscillator();
			var gain = actx.createGain();
			osc.type = 'sine';
			osc.frequency.setValueAtTime(140, now);
			osc.frequency.exponentialRampToValueAtTime(40, now + 0.12);

			gain.gain.setValueAtTime(0.3, now);
			gain.gain.exponentialRampToValueAtTime(0.001, now + 0.12);

			osc.connect(gain);
			gain.connect(actx.destination);
			osc.start(now);
			osc.stop(now + 0.12);
		}
	}

	function playGameOverSound() {
		if (!soundEnabled) return;
		var actx = getAudioContext();
		if (!actx) return;

		var now = actx.currentTime;
		var chords = [220, 196, 164.81]; // A3, G3, E3
		chords.forEach(function (freq, i) {
			var osc = actx.createOscillator();
			var gain = actx.createGain();
			osc.type = 'sawtooth';
			osc.frequency.setValueAtTime(freq, now + i * 0.12);

			gain.gain.setValueAtTime(0.2, now + i * 0.12);
			gain.gain.exponentialRampToValueAtTime(0.001, now + i * 0.12 + 0.6);

			osc.connect(gain);
			gain.connect(actx.destination);
			osc.start(now + i * 0.12);
			osc.stop(now + i * 0.12 + 0.6);
		});
	}

	// State Variables
	var gameState = 'ready'; // 'ready', 'playing', 'paused', 'gameover'
	var blocks = [];
	var slicedBlocks = [];
	var particles = [];
	var floatingTexts = [];
	var clouds = [];
	var stars = [];
	var currentBlock = null;
	var score = 0;
	var combo = 0;
	var maxCombo = 0;
	var highScore = window.TORNIS_USER_HIGHSCORE || 0;
	var localGuestBest = parseInt(localStorage.getItem('tornis_guest_best') || '0', 10);
	if (localGuestBest > highScore) {
		highScore = localGuestBest;
	}
	if (bottomBestEl) {
		bottomBestEl.textContent = highScore + ' stāvi';
	}
	var sessionToken = null;
	var gameStartTime = 0;
	var cameraY = 0;
	var targetCameraY = 0;
	var screenShake = 0;
	var dpr = window.devicePixelRatio || 1;
	var logicalW = 440;
	var logicalH = 600;

	// Background Stars Generation
	function initStars() {
		stars = [];
		for (var i = 0; i < 90; i++) {
			stars.push({
				x: Math.random() * logicalW,
				y: Math.random() * logicalH,
				radius: Math.random() * 1.5 + 0.5,
				baseAlpha: Math.random() * 0.7 + 0.3,
				speed: Math.random() * 2 + 1
			});
		}
	}
	initStars();

	// Clouds Generation
	function initClouds() {
		clouds = [];
		for (var i = 0; i < 7; i++) {
			clouds.push({
				x: Math.random() * logicalW,
				y: Math.random() * (logicalH * 2) - 200,
				width: Math.random() * 80 + 70,
				height: Math.random() * 25 + 20,
				speed: (Math.random() * 0.2 + 0.1) * (Math.random() < 0.5 ? 1 : -1),
				alpha: Math.random() * 0.35 + 0.2
			});
		}
	}
	initClouds();

	// High DPI Resize Handling
	function resizeCanvas() {
		dpr = window.devicePixelRatio || 1;
		var rect = canvas.getBoundingClientRect();
		logicalW = rect.width || 440;
		logicalH = rect.height || 600;

		canvas.width = Math.round(logicalW * dpr);
		canvas.height = Math.round(logicalH * dpr);
		ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
	}
	window.addEventListener('resize', resizeCanvas);
	resizeCanvas();

	// Block Color Generator (Smooth Hue Cycling with Height)
	function getBlockColor(index) {
		var hue = (index * 6.5 + 195) % 360;
		return {
			hue: hue,
			top: 'hsl(' + hue + ', 82%, 62%)',
			left: 'hsl(' + hue + ', 75%, 40%)',
			right: 'hsl(' + hue + ', 78%, 50%)',
			highlight: 'hsl(' + hue + ', 88%, 76%)'
		};
	}

	// 3D Isometric Math: World (x, y, z) -> Screen (sx, sy)
	function isoProject(x, y, z) {
		var originX = logicalW / 2;
		var originY = logicalH * 0.74;
		var sx = originX + (x - z) * COS_ISO;
		var sy = originY + (x + z) * SIN_ISO - y + cameraY;
		return { x: sx, y: sy };
	}

	// Draw 3D Isometric Box
	function drawIsometricBox(box, alpha) {
		if (typeof alpha === 'undefined') alpha = 1.0;
		if (alpha <= 0.01) return;

		var x = box.x;
		var y = box.y;
		var z = box.z;
		var w = box.width;
		var d = box.depth;
		var h = box.height;
		var color = box.color;

		ctx.save();
		ctx.globalAlpha = alpha;

		// 8 Vertices of the rectangular prism
		var p0 = isoProject(x, y, z);                 // bottom-front-left
		var p1 = isoProject(x + w, y, z);             // bottom-front-right
		var p2 = isoProject(x + w, y, z + d);         // bottom-back-right
		var p3 = isoProject(x, y, z + d);             // bottom-back-left

		var p4 = isoProject(x, y + h, z);             // top-front-left
		var p5 = isoProject(x + w, y + h, z);         // top-front-right
		var p6 = isoProject(x + w, y + h, z + d);     // top-back-right
		var p7 = isoProject(x, y + h, z + d);         // top-back-left

		// 1. Left Face (p4, p0, p3, p7)
		ctx.fillStyle = color.left;
		ctx.beginPath();
		ctx.moveTo(p4.x, p4.y);
		ctx.lineTo(p0.x, p0.y);
		ctx.lineTo(p3.x, p3.y);
		ctx.lineTo(p7.x, p7.y);
		ctx.closePath();
		ctx.fill();

		// Subtle Left Face Ambient Occlusion Gradient
		var gradLeft = ctx.createLinearGradient(p4.x, p4.y, p0.x, p0.y);
		gradLeft.addColorStop(0, 'rgba(255, 255, 255, 0.08)');
		gradLeft.addColorStop(1, 'rgba(0, 0, 0, 0.28)');
		ctx.fillStyle = gradLeft;
		ctx.fill();

		// 2. Right Face (p4, p5, p1, p0)
		ctx.fillStyle = color.right;
		ctx.beginPath();
		ctx.moveTo(p4.x, p4.y);
		ctx.lineTo(p5.x, p5.y);
		ctx.lineTo(p1.x, p1.y);
		ctx.lineTo(p0.x, p0.y);
		ctx.closePath();
		ctx.fill();

		// Right Face Gradient
		var gradRight = ctx.createLinearGradient(p4.x, p4.y, p1.x, p1.y);
		gradRight.addColorStop(0, 'rgba(255, 255, 255, 0.12)');
		gradRight.addColorStop(1, 'rgba(0, 0, 0, 0.2)');
		ctx.fillStyle = gradRight;
		ctx.fill();

		// 3. Top Face (p4, p5, p6, p7)
		ctx.fillStyle = color.top;
		ctx.beginPath();
		ctx.moveTo(p4.x, p4.y);
		ctx.lineTo(p5.x, p5.y);
		ctx.lineTo(p6.x, p6.y);
		ctx.lineTo(p7.x, p7.y);
		ctx.closePath();
		ctx.fill();

		// Subtle Top Face Inner Highlight
		ctx.strokeStyle = color.highlight;
		ctx.lineWidth = 1;
		ctx.stroke();

		// Floor Number indicator on recent blocks
		if (box.index > 0 && box.width >= 40 && box.depth >= 40) {
			var centerTop = isoProject(x + w / 2, y + h, z + d / 2);
			ctx.fillStyle = 'rgba(255, 255, 255, 0.35)';
			ctx.font = 'bold 9px sans-serif';
			ctx.textAlign = 'center';
			ctx.textBaseline = 'middle';
			ctx.fillText(box.index + '', centerTop.x, centerTop.y);
		}

		ctx.restore();
	}

	// Atmospheric Background Rendering (Altitude-based Transition)
	function drawAtmosphere() {
		var alt = score; // height in floors

		// Background Sky Gradient
		var grad = ctx.createLinearGradient(0, 0, 0, logicalH);
		if (alt < 20) {
			// Day/Morning blue
			grad.addColorStop(0, '#0284c7');
			grad.addColorStop(0.5, '#38bdf8');
			grad.addColorStop(1, '#bae6fd');
		} else if (alt < 50) {
			// Sunset Warm Horizon
			grad.addColorStop(0, '#4338ca');
			grad.addColorStop(0.4, '#8b5cf6');
			grad.addColorStop(0.75, '#f43f5e');
			grad.addColorStop(1, '#fb923c');
		} else if (alt < 85) {
			// Twilight / Stratosphere
			grad.addColorStop(0, '#1e1b4b');
			grad.addColorStop(0.5, '#312e81');
			grad.addColorStop(0.9, '#4c1d95');
			grad.addColorStop(1, '#581c87');
		} else {
			// Deep Cosmic Space with Nebula glow
			grad.addColorStop(0, '#020617');
			grad.addColorStop(0.6, '#0f172a');
			grad.addColorStop(1, '#1e1b4b');
		}
		ctx.fillStyle = grad;
		ctx.fillRect(0, 0, logicalW, logicalH);

		// Draw Twinkling Stars (More visible as tower rises)
		var starAlphaFactor = Math.min(1.0, Math.max(0, (alt - 25) / 50));
		if (starAlphaFactor > 0.05) {
			ctx.save();
			var nowTime = Date.now() * 0.003;
			for (var s = 0; s < stars.length; s++) {
				var star = stars[s];
				var twinkle = Math.sin(nowTime * star.speed + s) * 0.3 + 0.7;
				ctx.fillStyle = '#ffffff';
				ctx.globalAlpha = star.baseAlpha * starAlphaFactor * twinkle;
				var starY = (star.y + cameraY * 0.15) % logicalH;
				if (starY < 0) starY += logicalH;
				ctx.beginPath();
				ctx.arc(star.x, starY, star.radius, 0, Math.PI * 2);
				ctx.fill();
			}
			ctx.restore();
		}

		// Draw Soft Clouds (Drifting and shifting with camera altitude)
		if (alt < 70) {
			var cloudAlphaFactor = Math.max(0, 1.0 - (alt - 20) / 45);
			ctx.save();
			for (var c = 0; c < clouds.length; c++) {
				var cloud = clouds[c];
				cloud.x += cloud.speed;
				if (cloud.x > logicalW + 100) cloud.x = -100;
				if (cloud.x < -100) cloud.x = logicalW + 100;

				var cy = cloud.y + cameraY * 0.35;
				if (cy > -50 && cy < logicalH + 50) {
					ctx.fillStyle = '#ffffff';
					ctx.globalAlpha = cloud.alpha * cloudAlphaFactor;
					ctx.beginPath();
					ctx.ellipse(cloud.x, cy, cloud.width / 2, cloud.height / 2, 0, 0, Math.PI * 2);
					ctx.fill();
				}
			}
			ctx.restore();
		}
	}

	// Sliced Debris Class
	function SlicedPiece(x, y, z, width, depth, height, color, axis, dir) {
		this.box = {
			x: x, y: y, z: z,
			width: width, depth: depth, height: height,
			color: color, index: 0
		};
		this.vy = 0;
		this.vx = (axis === 'x') ? dir * (Math.random() * 40 + 60) : (Math.random() - 0.5) * 20;
		this.vz = (axis === 'z') ? dir * (Math.random() * 40 + 60) : (Math.random() - 0.5) * 20;
		this.alpha = 1.0;
		this.rot = 0;
		this.rotSpeed = (Math.random() - 0.5) * 4;
	}

	SlicedPiece.prototype.update = function (dt) {
		this.vy += 800 * dt; // gravity
		this.box.y -= this.vy * dt;
		this.box.x += this.vx * dt;
		this.box.z += this.vz * dt;
		this.alpha -= 0.65 * dt;
	};

	SlicedPiece.prototype.draw = function () {
		drawIsometricBox(this.box, Math.max(0, this.alpha));
	};

	// Sparkle & Ring Particle System
	function spawnPerfectBurst(x, y, z, w, d) {
		var center = isoProject(x + w / 2, y, z + d / 2);

		// Shockwave ring
		particles.push({
			type: 'ring',
			x: center.x,
			y: center.y,
			radius: 12,
			maxRadius: Math.max(w, d) * 1.3,
			alpha: 0.95,
			color: '#fbbf24'
		});

		// Golden sparkles
		var count = 18;
		for (var i = 0; i < count; i++) {
			var angle = Math.random() * Math.PI * 2;
			var spd = Math.random() * 120 + 60;
			particles.push({
				type: 'sparkle',
				x: center.x,
				y: center.y,
				vx: Math.cos(angle) * spd,
				vy: Math.sin(angle) * spd - 30,
				size: Math.random() * 3 + 2,
				alpha: 1.0,
				color: Math.random() < 0.5 ? '#f59e0b' : '#38bdf8'
			});
		}

		// Floating Combo Text
		var comboStr = (combo >= 2) ? 'COMBO x' + combo + '!' : 'PERFECT!';
		floatingTexts.push({
			text: comboStr,
			x: center.x,
			y: center.y - 20,
			alpha: 1.0,
			vy: -40,
			color: '#fef08a'
		});
	}

	// Initialize New Game
	function startNewGame() {
		blocks = [];
		slicedBlocks = [];
		particles = [];
		floatingTexts = [];
		score = 0;
		combo = 0;
		maxCombo = 0;
		cameraY = 0;
		targetCameraY = 0;
		screenShake = 0;
		gameStartTime = Date.now();

		// Fetch Anti-cheat Session Token via native fetch
		fetch('/tornis?action=init_token')
			.then(function (res) { return res.json(); })
			.then(function (res) {
				if (res && res.token) {
					sessionToken = res.token;
				}
			})
			.catch(function () {});

		// Base Platform Block
		var baseColor = {
			top: '#6366f1',
			left: '#4338ca',
			right: '#4f46e5',
			highlight: '#a5b4fc'
		};

		var baseBlock = {
			x: -INITIAL_SIZE / 2,
			y: 0,
			z: -INITIAL_SIZE / 2,
			width: INITIAL_SIZE,
			depth: INITIAL_SIZE,
			height: BLOCK_HEIGHT * 2.5,
			color: baseColor,
			index: 0
		};
		blocks.push(baseBlock);

		// Spawn First Moving Block
		spawnMovingBlock();

		gameState = 'playing';
		hudEl.style.display = 'flex';
		startOverlay.style.display = 'none';
		gameoverOverlay.style.display = 'none';
		pauseOverlay.style.display = 'none';
		pauseBtn.style.display = 'inline-flex';
		updateHUD();
	}

	// Spawn Next Moving Block
	function spawnMovingBlock() {
		var prev = blocks[blocks.length - 1];
		var newIndex = blocks.length;
		var color = getBlockColor(newIndex);
		var axis = (newIndex % 2 === 1) ? 'x' : 'z'; // Alternate X and Z axes
		var range = 240;

		var startX = prev.x;
		var startZ = prev.z;

		if (axis === 'x') {
			startX = -range;
		} else {
			startZ = -range;
		}

		var speed = Math.min(MAX_SPEED, BASE_SPEED + (newIndex * 3.5));

		currentBlock = {
			x: startX,
			y: prev.y + BLOCK_HEIGHT,
			z: startZ,
			width: prev.width,
			depth: prev.depth,
			height: BLOCK_HEIGHT,
			axis: axis,
			dir: 1,
			speed: speed,
			range: range,
			color: color,
			index: newIndex
		};

		targetCameraY = Math.max(0, (newIndex - 4) * BLOCK_HEIGHT);
	}

	// Drop / Place Block
	function placeBlock() {
		if (gameState !== 'playing' || !currentBlock) return;

		var prev = blocks[blocks.length - 1];
		var cur = currentBlock;
		var axis = cur.axis;

		var diff, overlap, newX, newZ, newW, newD;

		if (axis === 'x') {
			diff = cur.x - prev.x;
			if (Math.abs(diff) <= PERFECT_TOLERANCE) {
				// Perfect Placement
				diff = 0;
				cur.x = prev.x;
				combo++;
				if (combo > maxCombo) maxCombo = combo;
				playPlaceSound(combo);
				spawnPerfectBurst(cur.x, cur.y + cur.height, cur.z, cur.width, cur.depth);

				// Bonus: Every 5 combos expand the block slightly
				if (combo % 5 === 0 && (cur.width < INITIAL_SIZE || cur.depth < INITIAL_SIZE)) {
					cur.width = Math.min(INITIAL_SIZE, cur.width + 8);
					cur.x = Math.max(-INITIAL_SIZE / 2, cur.x - 4);
				}
			} else {
				combo = 0;
				playPlaceSound(0);
			}

			overlap = cur.width - Math.abs(diff);

			if (overlap <= 0) {
				// Missed entirely -> Fall and Game Over
				triggerGameOver(cur, 'x', diff > 0 ? 1 : -1);
				return;
			}

			// Slice off overhang piece
			newW = overlap;
			newD = cur.depth;
			newZ = cur.z;

			if (diff > 0) {
				newX = cur.x;
				var sliceX = cur.x + overlap;
				var sliceW = diff;
				slicedBlocks.push(new SlicedPiece(sliceX, cur.y, newZ, sliceW, newD, cur.height, cur.color, 'x', 1));
			} else if (diff < 0) {
				newX = prev.x;
				var sliceX = cur.x;
				var sliceW = -diff;
				slicedBlocks.push(new SlicedPiece(sliceX, cur.y, newZ, sliceW, newD, cur.height, cur.color, 'x', -1));
			} else {
				newX = cur.x;
			}

			cur.x = newX;
			cur.width = newW;
		} else {
			// Z axis
			diff = cur.z - prev.z;
			if (Math.abs(diff) <= PERFECT_TOLERANCE) {
				// Perfect Placement
				diff = 0;
				cur.z = prev.z;
				combo++;
				if (combo > maxCombo) maxCombo = combo;
				playPlaceSound(combo);
				spawnPerfectBurst(cur.x, cur.y + cur.height, cur.z, cur.width, cur.depth);

				if (combo % 5 === 0 && (cur.width < INITIAL_SIZE || cur.depth < INITIAL_SIZE)) {
					cur.depth = Math.min(INITIAL_SIZE, cur.depth + 8);
					cur.z = Math.max(-INITIAL_SIZE / 2, cur.z - 4);
				}
			} else {
				combo = 0;
				playPlaceSound(0);
			}

			overlap = cur.depth - Math.abs(diff);

			if (overlap <= 0) {
				triggerGameOver(cur, 'z', diff > 0 ? 1 : -1);
				return;
			}

			newD = overlap;
			newW = cur.width;
			newX = cur.x;

			if (diff > 0) {
				newZ = cur.z;
				var sliceZ = cur.z + overlap;
				var sliceD = diff;
				slicedBlocks.push(new SlicedPiece(newX, cur.y, sliceZ, newW, sliceD, cur.height, cur.color, 'z', 1));
			} else if (diff < 0) {
				newZ = prev.z;
				var sliceZ = cur.z;
				var sliceD = -diff;
				slicedBlocks.push(new SlicedPiece(newX, cur.y, sliceZ, newW, sliceD, cur.height, cur.color, 'z', -1));
			} else {
				newZ = cur.z;
			}

			cur.z = newZ;
			cur.depth = newD;
		}

		// Save placed block to tower
		blocks.push(cur);
		score = blocks.length - 1; // Base does not count as placed floor
		screenShake = 3;

		updateHUD();
		spawnMovingBlock();
	}

	// Trigger Game Over
	function triggerGameOver(missedBlock, axis, dir) {
		gameState = 'gameover';
		currentBlock = null;
		pauseBtn.style.display = 'none';

		// Drop the entire missed block
		if (missedBlock) {
			slicedBlocks.push(new SlicedPiece(
				missedBlock.x, missedBlock.y, missedBlock.z,
				missedBlock.width, missedBlock.depth, missedBlock.height,
				missedBlock.color, axis, dir
			));
		}

		playGameOverSound();

		var duration = Math.max(1, Math.round((Date.now() - gameStartTime) / 1000));
		var isNewBest = score > highScore;
		if (isNewBest) {
			highScore = score;
			localStorage.setItem('tornis_guest_best', highScore);
			if (bottomBestEl) bottomBestEl.textContent = highScore + ' stāvi';
		}

		// Submit score via native fetch
		submitScore(score, maxCombo, duration);

		// Populate Game Over Overlay
		if (finalScoreVal) finalScoreVal.textContent = score;
		if (finalComboVal) finalComboVal.textContent = 'x' + maxCombo;
		if (finalMetersVal) finalMetersVal.textContent = (score * 3) + ' m';
		if (finalBestVal) finalBestVal.textContent = highScore;
		if (newRecordBanner) newRecordBanner.style.display = isNewBest ? 'block' : 'none';

		setTimeout(function () {
			if (gameoverOverlay) gameoverOverlay.style.display = 'flex';
		}, 600);
	}

	// Submit score to backend
	function submitScore(finalScore, bestCombo, durationSec) {
		if (finalScore <= 0 || !sessionToken) return;

		var formData = new FormData();
		formData.append('token', sessionToken);
		formData.append('score', finalScore);
		formData.append('combo', bestCombo);
		formData.append('duration', durationSec);

		fetch('/tornis?action=push', {
			method: 'POST',
			body: formData
		})
		.then(function (res) { return res.json(); })
		.then(function (res) {
			if (res && res.success) {
				if (res.rank && rankInfoBox && rankValEl) {
					rankValEl.textContent = '#' + res.rank;
					rankInfoBox.style.display = 'block';
				}
				if (res.highScore) {
					highScore = res.highScore;
					if (finalBestVal) finalBestVal.textContent = highScore;
					if (bottomBestEl) bottomBestEl.textContent = highScore + ' stāvi';
				}
			}
		})
		.catch(function () {});
	}

	// Update Floating HUD Stats
	function updateHUD() {
		if (currentScoreEl) currentScoreEl.textContent = score;
		if (comboBadgeEl) {
			if (combo >= 2) {
				comboBadgeEl.style.display = 'block';
				if (comboTextEl) comboTextEl.textContent = 'COMBO x' + combo;
			} else {
				comboBadgeEl.style.display = 'none';
			}
		}
	}

	// Game Loop (with DeltaTime)
	var lastTime = performance.now();

	function gameLoop(now) {
		var dt = Math.min(0.1, (now - lastTime) / 1000);
		lastTime = now;

		if (gameState === 'playing' || gameState === 'gameover') {
			update(dt);
		}

		render();
		requestAnimationFrame(gameLoop);
	}

	// Update Simulation
	function update(dt) {
		// Update Moving Block
		if (currentBlock && gameState === 'playing') {
			var b = currentBlock;
			var limit = b.range;

			if (b.axis === 'x') {
				b.x += b.dir * b.speed * dt;
				if (b.x > limit) {
					b.x = limit;
					b.dir = -1;
				} else if (b.x < -limit) {
					b.x = -limit;
					b.dir = 1;
				}
			} else {
				b.z += b.dir * b.speed * dt;
				if (b.z > limit) {
					b.z = limit;
					b.dir = -1;
				} else if (b.z < -limit) {
					b.z = -limit;
					b.dir = 1;
				}
			}
		}

		// Smooth Camera Glide (Lerp)
		cameraY += (targetCameraY - cameraY) * 0.08;

		// Update Sliced Pieces
		for (var i = slicedBlocks.length - 1; i >= 0; i--) {
			var piece = slicedBlocks[i];
			piece.update(dt);
			if (piece.alpha <= 0 || piece.box.y < cameraY - 400) {
				slicedBlocks.splice(i, 1);
			}
		}

		// Update Particles
		for (var p = particles.length - 1; p >= 0; p--) {
			var pt = particles[p];
			if (pt.type === 'ring') {
				pt.radius += (pt.maxRadius - pt.radius) * 12 * dt;
				pt.alpha -= 2.2 * dt;
			} else if (pt.type === 'sparkle') {
				pt.x += pt.vx * dt;
				pt.y += pt.vy * dt;
				pt.vy += 220 * dt; // gravity
				pt.alpha -= 1.8 * dt;
			}
			if (pt.alpha <= 0) {
				particles.splice(p, 1);
			}
		}

		// Update Floating Texts
		for (var t = floatingTexts.length - 1; t >= 0; t--) {
			var ft = floatingTexts[t];
			ft.y += ft.vy * dt;
			ft.alpha -= 1.2 * dt;
			if (ft.alpha <= 0) {
				floatingTexts.splice(t, 1);
			}
		}

		// Screen Shake Decay
		if (screenShake > 0) {
			screenShake -= dt * 15;
			if (screenShake < 0) screenShake = 0;
		}
	}

	// Render Everything
	function render() {
		ctx.clearRect(0, 0, logicalW, logicalH);

		ctx.save();
		if (screenShake > 0) {
			var shakeX = (Math.random() - 0.5) * screenShake * 2;
			var shakeY = (Math.random() - 0.5) * screenShake * 2;
			ctx.translate(shakeX, shakeY);
		}

		// 1. Atmosphere (Sky gradient, Stars, Clouds)
		drawAtmosphere();

		// 2. Base Pedestal Ground Shadow
		var baseShadowPt = isoProject(0, -10, 0);
		ctx.save();
		ctx.fillStyle = 'rgba(0, 0, 0, 0.25)';
		ctx.beginPath();
		ctx.ellipse(baseShadowPt.x, baseShadowPt.y, INITIAL_SIZE * 0.9, INITIAL_SIZE * 0.45, 0, 0, Math.PI * 2);
		ctx.fill();
		ctx.restore();

		// 3. Render Static Stacked Blocks (Frustum culled for performance)
		var visibleBottomY = cameraY - 350;
		var visibleTopY = cameraY + 650;

		for (var i = 0; i < blocks.length; i++) {
			var blk = blocks[i];
			if (blk.y >= visibleBottomY && blk.y <= visibleTopY) {
				drawIsometricBox(blk);
			}
		}

		// 4. Render Active Moving Block
		if (currentBlock && gameState === 'playing') {
			drawIsometricBox(currentBlock);
		}

		// 5. Render Sliced Falling Pieces
		for (var s = 0; s < slicedBlocks.length; s++) {
			slicedBlocks[s].draw();
		}

		// 6. Render Particles (Shockwave rings & sparkles)
		for (var p = 0; p < particles.length; p++) {
			var pt = particles[p];
			ctx.save();
			if (pt.type === 'ring') {
				ctx.strokeStyle = pt.color;
				ctx.globalAlpha = Math.max(0, pt.alpha);
				ctx.lineWidth = 2.5;
				ctx.beginPath();
				ctx.ellipse(pt.x, pt.y, pt.radius, pt.radius * 0.5, 0, 0, Math.PI * 2);
				ctx.stroke();
			} else if (pt.type === 'sparkle') {
				ctx.fillStyle = pt.color;
				ctx.globalAlpha = Math.max(0, pt.alpha);
				ctx.beginPath();
				ctx.arc(pt.x, pt.y, pt.size, 0, Math.PI * 2);
				ctx.fill();
			}
			ctx.restore();
		}

		// 7. Render Floating Combo Texts
		ctx.save();
		for (var t = 0; t < floatingTexts.length; t++) {
			var ft = floatingTexts[t];
			ctx.fillStyle = ft.color;
			ctx.globalAlpha = Math.max(0, ft.alpha);
			ctx.font = 'bold 15px sans-serif';
			ctx.textAlign = 'center';
			ctx.shadowColor = 'rgba(0, 0, 0, 0.6)';
			ctx.shadowBlur = 4;
			ctx.fillText(ft.text, ft.x, ft.y);
		}
		ctx.restore();

		ctx.restore();
	}

	// Action Handler (Click / Tap / Spacebar)
	function handleAction() {
		if (gameState === 'ready') {
			startNewGame();
		} else if (gameState === 'playing') {
			placeBlock();
		} else if (gameState === 'gameover') {
			startNewGame();
		} else if (gameState === 'paused') {
			resumeGame();
		}
	}

	function pauseGame() {
		if (gameState === 'playing') {
			gameState = 'paused';
			pauseOverlay.style.display = 'flex';
		}
	}

	function resumeGame() {
		if (gameState === 'paused') {
			gameState = 'playing';
			pauseOverlay.style.display = 'none';
			lastTime = performance.now();
		}
	}

	// Event Listeners
	if (startBtn) {
		startBtn.addEventListener('click', function (e) {
			e.preventDefault();
			startNewGame();
		});
	}

	if (restartBtn) {
		restartBtn.addEventListener('click', function (e) {
			e.preventDefault();
			startNewGame();
		});
	}

	if (resumeBtn) {
		resumeBtn.addEventListener('click', function (e) {
			e.preventDefault();
			resumeGame();
		});
	}

	if (pauseBtn) {
		pauseBtn.addEventListener('click', function (e) {
			e.preventDefault();
			if (gameState === 'playing') pauseGame();
			else if (gameState === 'paused') resumeGame();
		});
	}

	if (soundBtn) {
		soundBtn.addEventListener('click', function (e) {
			e.preventDefault();
			soundEnabled = !soundEnabled;
			localStorage.setItem('tornis_sound', soundEnabled ? '1' : '0');
			updateSoundIcon();
		});
	}

	if (mobileDropBtn) {
		mobileDropBtn.addEventListener('touchstart', function (e) {
			e.preventDefault();
			handleAction();
		});
		mobileDropBtn.addEventListener('click', function (e) {
			e.preventDefault();
			handleAction();
		});
	}

	// Canvas Click / Tap
	canvas.addEventListener('click', function (e) {
		e.preventDefault();
		handleAction();
	});

	canvas.addEventListener('touchstart', function (e) {
		e.preventDefault();
		handleAction();
	}, { passive: false });

	// Keyboard Controls
	window.addEventListener('keydown', function (e) {
		// Prevent spacebar and up arrow from scrolling page
		if (e.code === 'Space' || e.keyCode === 32 || e.code === 'ArrowUp' || e.keyCode === 38) {
			e.preventDefault();
			handleAction();
		} else if (e.code === 'KeyP' || e.keyCode === 80) {
			e.preventDefault();
			if (gameState === 'playing') pauseGame();
			else if (gameState === 'paused') resumeGame();
		}
	});

		// Kick off render loop
		requestAnimationFrame(gameLoop);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initTornis);
	} else {
		initTornis();
	}
})();
