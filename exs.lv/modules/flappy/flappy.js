/**
 * Lidojošais Eksis - Flappy Bird Canvas Game Engine
 */
$(function() {
	var canvas = document.getElementById('flappy-canvas');
	if (!canvas) return;
	var ctx = canvas.getContext('2d');

	// Game state
	var STATE_START = 0;
	var STATE_PLAYING = 1;
	var STATE_GAMEOVER = 2;
	var gameState = STATE_START;

	var score = 0;
	var highScore = window.FLAPPY_USER_HIGHSCORE || 0;
	var currentToken = null;
	var gameStartTime = 0;
	var soundEnabled = true;

	// Audio Context for synthetic sound effects
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

			if (type === 'flap') {
				osc.type = 'sine';
				osc.frequency.setValueAtTime(400, now);
				osc.frequency.exponentialRampToValueAtTime(750, now + 0.08);
				gain.gain.setValueAtTime(0.2, now);
				gain.gain.linearRampToValueAtTime(0.01, now + 0.08);
				osc.start(now);
				osc.stop(now + 0.08);
			} else if (type === 'score') {
				osc.type = 'triangle';
				osc.frequency.setValueAtTime(523.25, now); // C5
				osc.frequency.setValueAtTime(659.25, now + 0.06); // E5
				gain.gain.setValueAtTime(0.25, now);
				gain.gain.linearRampToValueAtTime(0.01, now + 0.18);
				osc.start(now);
				osc.stop(now + 0.18);
			} else if (type === 'hit') {
				osc.type = 'sawtooth';
				osc.frequency.setValueAtTime(160, now);
				osc.frequency.linearRampToValueAtTime(40, now + 0.15);
				gain.gain.setValueAtTime(0.3, now);
				gain.gain.linearRampToValueAtTime(0.01, now + 0.15);
				osc.start(now);
				osc.stop(now + 0.15);
			}
		} catch (e) {}
	}

	// Avatar image loading
	var avatarImg = new Image();
	var avatarLoaded = false;
	if (window.FLAPPY_USER_AVATAR && window.FLAPPY_USER_AVATAR.length > 0) {
		avatarImg.crossOrigin = "Anonymous";
		avatarImg.onload = function() { avatarLoaded = true; };
		avatarImg.onerror = function() { avatarLoaded = false; };
		avatarImg.src = window.FLAPPY_USER_AVATAR;
	}

	// Bird physics & properties
	var bird = {
		x: 80,
		y: 250,
		radius: 18,
		velocity: 0,
		gravity: 0.42,
		jump: -7.2,
		rotation: 0,
		wingPhase: 0
	};

	// Environment & Pipes
	var groundHeight = 60;
	var pipeWidth = 56;
	var pipeGap = 125;
	var pipeSpeed = 2.3;
	var pipeSpawnInterval = 95; // frames
	var frameCount = 0;
	var pipes = [];
	var clouds = [
		{ x: 30, y: 50, speed: 0.4, r: 24 },
		{ x: 190, y: 90, speed: 0.3, r: 32 },
		{ x: 320, y: 40, speed: 0.5, r: 20 }
	];

	// Fetch security token
	function fetchToken(callback) {
		$.getJSON('/flappy?action=init_token', function(res) {
			if (res && res.success) {
				currentToken = res.token;
			}
			if (callback) callback();
		});
	}

	// Submit score to server
	function submitScore() {
		if (score <= 0) return;
		var scoreToSend = score;
		var tokenToSend = currentToken || '';
		var durationToSend = Math.round((Date.now() - gameStartTime) / 1000);

		currentToken = null;

		$.ajax({
			url: '/flappy?action=push',
			type: 'POST',
			data: {
				token: tokenToSend,
				score: scoreToSend,
				duration: durationToSend
			},
			dataType: 'json',
			success: function(res) {
				if (res && res.success) {
					if (res.highScore !== undefined) {
						highScore = res.highScore;
						$('#stat-highscore').text(highScore);
						$('#flappy-best-score').text(highScore);
					}
					if (res.isNewRecord) {
						$('#flappy-record-alert').show();
					}
				}
			}
		});
	}

	function resetGame() {
		bird.y = 220;
		bird.velocity = 0;
		bird.rotation = 0;
		pipes = [];
		score = 0;
		frameCount = 0;
		gameStartTime = Date.now();
		$('#flappy-record-alert').hide();
		fetchToken();
	}

	function flap() {
		initAudio();
		if (gameState === STATE_START) {
			resetGame();
			gameState = STATE_PLAYING;
			$('#flappy-start-overlay').fadeOut(150);
			bird.velocity = bird.jump;
			playSound('flap');
		} else if (gameState === STATE_PLAYING) {
			bird.velocity = bird.jump;
			playSound('flap');
		} else if (gameState === STATE_GAMEOVER) {
			resetGame();
			gameState = STATE_PLAYING;
			$('#flappy-gameover-overlay').fadeOut(150);
			bird.velocity = bird.jump;
			playSound('flap');
		}
	}

	// Update game state
	function update() {
		// Update clouds
		for (var c = 0; c < clouds.length; c++) {
			clouds[c].x -= clouds[c].speed;
			if (clouds[c].x + clouds[c].r * 2 < 0) {
				clouds[c].x = canvas.width + clouds[c].r;
				clouds[c].y = 30 + Math.random() * 90;
			}
		}

		if (gameState !== STATE_PLAYING) {
			// Idle hovering effect
			bird.wingPhase += 0.08;
			bird.y = 230 + Math.sin(bird.wingPhase) * 6;
			return;
		}

		frameCount++;

		// Apply gravity to bird
		bird.velocity += bird.gravity;
		bird.y += bird.velocity;
		bird.wingPhase += 0.25;

		// Calculate rotation
		if (bird.velocity < 0) {
			bird.rotation = Math.max(-0.45, bird.rotation - 0.08);
		} else {
			bird.rotation = Math.min(1.2, bird.rotation + 0.05);
		}

		// Ground collision
		if (bird.y + bird.radius >= canvas.height - groundHeight) {
			bird.y = canvas.height - groundHeight - bird.radius;
			gameOver();
			return;
		}

		// Ceiling collision
		if (bird.y - bird.radius <= 0) {
			bird.y = bird.radius;
			bird.velocity = 0;
		}

		// Spawn pipes
		if (frameCount % pipeSpawnInterval === 0) {
			var minPipe = 50;
			var maxPipe = canvas.height - groundHeight - pipeGap - minPipe;
			var topPipeHeight = Math.floor(minPipe + Math.random() * (maxPipe - minPipe));

			pipes.push({
				x: canvas.width,
				top: topPipeHeight,
				bottom: canvas.height - groundHeight - (topPipeHeight + pipeGap),
				passed: false
			});
		}

		// Move & update pipes
		for (var i = pipes.length - 1; i >= 0; i--) {
			var p = pipes[i];
			p.x -= pipeSpeed;

			// Check score
			if (!p.passed && p.x + pipeWidth < bird.x - bird.radius) {
				p.passed = true;
				score++;
				playSound('score');
			}

			// Collision detection
			if (checkCollision(bird, p)) {
				gameOver();
				return;
			}

			// Remove offscreen pipes
			if (p.x + pipeWidth < 0) {
				pipes.splice(i, 1);
			}
		}
	}

	function checkCollision(b, p) {
		// Pipe AABB box hitboxes vs Bird circle
		if (b.x + b.radius > p.x && b.x - b.radius < p.x + pipeWidth) {
			// Top pipe
			if (b.y - b.radius < p.top) {
				return true;
			}
			// Bottom pipe
			if (b.y + b.radius > canvas.height - groundHeight - p.bottom) {
				return true;
			}
		}
		return false;
	}

	function gameOver() {
		playSound('hit');
		gameState = STATE_GAMEOVER;
		$('#flappy-final-score').text(score);
		$('#flappy-gameover-overlay').fadeIn(200);
		submitScore();
	}

	// Render canvas
	function render() {
		// Sky gradient
		var skyGrad = ctx.createLinearGradient(0, 0, 0, canvas.height - groundHeight);
		skyGrad.addColorStop(0, '#38bdf8');
		skyGrad.addColorStop(1, '#bae6fd');
		ctx.fillStyle = skyGrad;
		ctx.fillRect(0, 0, canvas.width, canvas.height - groundHeight);

		// Draw clouds
		ctx.fillStyle = 'rgba(255, 255, 255, 0.85)';
		for (var c = 0; c < clouds.length; c++) {
			var cl = clouds[c];
			ctx.beginPath();
			ctx.arc(cl.x, cl.y, cl.r, 0, Math.PI * 2);
			ctx.arc(cl.x + cl.r * 0.7, cl.y - cl.r * 0.3, cl.r * 0.75, 0, Math.PI * 2);
			ctx.arc(cl.x - cl.r * 0.7, cl.y - cl.r * 0.2, cl.r * 0.7, 0, Math.PI * 2);
			ctx.fill();
		}

		// Draw pipes
		for (var i = 0; i < pipes.length; i++) {
			var p = pipes[i];

			// Pipe Gradient
			var pipeGrad = ctx.createLinearGradient(p.x, 0, p.x + pipeWidth, 0);
			pipeGrad.addColorStop(0, '#22c55e');
			pipeGrad.addColorStop(0.5, '#4ade80');
			pipeGrad.addColorStop(1, '#15803d');

			ctx.fillStyle = pipeGrad;
			ctx.strokeStyle = '#14532d';
			ctx.lineWidth = 2;

			// Top pipe body
			ctx.fillRect(p.x, 0, pipeWidth, p.top);
			ctx.strokeRect(p.x, 0, pipeWidth, p.top);
			// Top pipe cap
			ctx.fillRect(p.x - 3, p.top - 18, pipeWidth + 6, 18);
			ctx.strokeRect(p.x - 3, p.top - 18, pipeWidth + 6, 18);

			// Bottom pipe body
			var bottomY = canvas.height - groundHeight - p.bottom;
			ctx.fillRect(p.x, bottomY, pipeWidth, p.bottom);
			ctx.strokeRect(p.x, bottomY, pipeWidth, p.bottom);
			// Bottom pipe cap
			ctx.fillRect(p.x - 3, bottomY, pipeWidth + 6, 18);
			ctx.strokeRect(p.x - 3, bottomY, pipeWidth + 6, 18);
		}

		// Draw Ground
		var groundY = canvas.height - groundHeight;
		ctx.fillStyle = '#eab308';
		ctx.fillRect(0, groundY, canvas.width, groundHeight);

		// Grass top strip
		ctx.fillStyle = '#16a34a';
		ctx.fillRect(0, groundY, canvas.width, 12);
		ctx.fillStyle = '#15803d';
		ctx.fillRect(0, groundY + 12, canvas.width, 3);

		// Draw Bird (Avatar or procedurally styled bird)
		ctx.save();
		ctx.translate(bird.x, bird.y);
		ctx.rotate(bird.rotation);

		if (avatarLoaded) {
			// Round avatar badge
			ctx.beginPath();
			ctx.arc(0, 0, bird.radius, 0, Math.PI * 2);
			ctx.closePath();
			ctx.clip();

			ctx.drawImage(avatarImg, -bird.radius, -bird.radius, bird.radius * 2, bird.radius * 2);

			// Border ring
			ctx.lineWidth = 3;
			ctx.strokeStyle = '#f59e0b';
			ctx.stroke();
		} else {
			// Procedural Bird
			// Body
			ctx.beginPath();
			ctx.arc(0, 0, bird.radius, 0, Math.PI * 2);
			ctx.fillStyle = '#facc15';
			ctx.fill();
			ctx.lineWidth = 2;
			ctx.strokeStyle = '#ca8a04';
			ctx.stroke();

			// Eye
			ctx.beginPath();
			ctx.arc(6, -5, 4, 0, Math.PI * 2);
			ctx.fillStyle = '#ffffff';
			ctx.fill();
			ctx.beginPath();
			ctx.arc(7, -5, 2, 0, Math.PI * 2);
			ctx.fillStyle = '#0f172a';
			ctx.fill();

			// Beak
			ctx.beginPath();
			ctx.moveTo(12, 0);
			ctx.lineTo(22, 4);
			ctx.lineTo(12, 8);
			ctx.closePath();
			ctx.fillStyle = '#f97316';
			ctx.fill();

			// Wing
			var wingY = Math.sin(bird.wingPhase) * 4;
			ctx.beginPath();
			ctx.ellipse(-6, 2 + wingY, 8, 5, -0.3, 0, Math.PI * 2);
			ctx.fillStyle = '#fde047';
			ctx.fill();
			ctx.stroke();
		}

		ctx.restore();

		// Draw Score HUD during play
		if (gameState === STATE_PLAYING) {
			ctx.font = 'bold 36px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
			ctx.textAlign = 'center';
			ctx.fillStyle = '#ffffff';
			ctx.strokeStyle = '#0f172a';
			ctx.lineWidth = 4;
			ctx.strokeText(score, canvas.width / 2, 54);
			ctx.fillText(score, canvas.width / 2, 54);
		}
	}

	// Main Game Loop
	function loop() {
		update();
		render();
		requestAnimationFrame(loop);
	}

	// Controls & Event Listeners
	$(document).on('keydown', function(e) {
		if (e.keyCode === 32 || e.keyCode === 38) { // Spacebar or Up arrow
			e.preventDefault();
			flap();
		}
	});

	$('#flappy-canvas').on('click touchstart', function(e) {
		e.preventDefault();
		flap();
	});

	$('#flappy-start-btn').on('click', function(e) {
		e.preventDefault();
		flap();
	});

	$('#flappy-restart-btn').on('click', function(e) {
		e.preventDefault();
		flap();
	});

	$('#flappy-sound-toggle').on('click', function(e) {
		e.preventDefault();
		soundEnabled = !soundEnabled;
		$(this).html(soundEnabled ? '🔊 Ieslēgta' : '🔇 Izslēgta');
	});

	// Pre-fetch token on load
	fetchToken();

	// Start Loop
	requestAnimationFrame(loop);
});
