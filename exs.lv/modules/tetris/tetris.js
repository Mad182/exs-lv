/**
 * Tetris JavaScript Engine for exs.lv with Anti-Cheat session validation
 */
document.addEventListener('DOMContentLoaded', function () {
	var mainCanvas = document.getElementById('tetris-canvas');
	if (!mainCanvas) return;

	var ctx = mainCanvas.getContext('2d');
	var nextCanvas = document.getElementById('next-canvas');
	var nextCtx = nextCanvas ? nextCanvas.getContext('2d') : null;
	var holdCanvas = document.getElementById('hold-canvas');
	var holdCtx = holdCanvas ? holdCanvas.getContext('2d') : null;

	var overlay = document.getElementById('tetris-overlay');
	var overlayTitle = document.getElementById('overlay-title');
	var overlayMsg = document.getElementById('overlay-msg');
	var btnStart = document.getElementById('btn-start');
	var btnPause = document.getElementById('btn-pause');
	var btnSound = document.getElementById('btn-sound');

	var elScore = document.getElementById('stat-score');
	var elLevel = document.getElementById('stat-level');
	var elLines = document.getElementById('stat-lines');
	var elHighScore = document.getElementById('stat-highscore');

	// Game Board Configuration
	var COLS = 10;
	var ROWS = 20;
	var BLOCK_SIZE = 32;

	// Tetrominoes definitions (Standard SRS orientation)
	var SHAPES = {
		I: [
			[0, 0, 0, 0],
			[1, 1, 1, 1],
			[0, 0, 0, 0],
			[0, 0, 0, 0]
		],
		J: [
			[1, 0, 0],
			[1, 1, 1],
			[0, 0, 0]
		],
		L: [
			[0, 0, 1],
			[1, 1, 1],
			[0, 0, 0]
		],
		O: [
			[1, 1],
			[1, 1]
		],
		S: [
			[0, 1, 1],
			[1, 1, 0],
			[0, 0, 0]
		],
		T: [
			[0, 1, 0],
			[1, 1, 1],
			[0, 0, 0]
		],
		Z: [
			[1, 1, 0],
			[0, 1, 1],
			[0, 0, 0]
		]
	};

	var COLORS = {
		I: '#00f0f0',
		J: '#0050f0',
		L: '#f0a000',
		O: '#f0f000',
		S: '#00f000',
		T: '#a000f0',
		Z: '#f00000'
	};

	var DARK_COLORS = {
		I: '#008b8b',
		J: '#002b8b',
		L: '#8b5c00',
		O: '#8b8b00',
		S: '#008b00',
		T: '#5c008b',
		Z: '#8b0000'
	};

	// Audio Synth using Web Audio API
	var audioEnabled = true;
	var audioCtx = null;

	function initAudio() {
		if (!audioCtx && (window.AudioContext || window.webkitAudioContext)) {
			audioCtx = new (window.AudioContext || window.webkitAudioContext)();
		}
	}

	function playSound(type) {
		if (!audioEnabled) return;
		try {
			initAudio();
			if (!audioCtx) return;
			if (audioCtx.state === 'suspended') {
				audioCtx.resume();
			}

			var osc = audioCtx.createOscillator();
			var gain = audioCtx.createGain();
			osc.connect(gain);
			gain.connect(audioCtx.destination);

			var now = audioCtx.currentTime;

			if (type === 'move') {
				osc.type = 'triangle';
				osc.frequency.setValueAtTime(220, now);
				osc.frequency.exponentialRampToValueAtTime(110, now + 0.05);
				gain.gain.setValueAtTime(0.15, now);
				gain.gain.exponentialRampToValueAtTime(0.01, now + 0.05);
				osc.start(now);
				osc.stop(now + 0.05);
			} else if (type === 'rotate') {
				osc.type = 'sine';
				osc.frequency.setValueAtTime(440, now);
				osc.frequency.exponentialRampToValueAtTime(880, now + 0.08);
				gain.gain.setValueAtTime(0.15, now);
				gain.gain.exponentialRampToValueAtTime(0.01, now + 0.08);
				osc.start(now);
				osc.stop(now + 0.08);
			} else if (type === 'drop') {
				osc.type = 'square';
				osc.frequency.setValueAtTime(150, now);
				osc.frequency.exponentialRampToValueAtTime(40, now + 0.1);
				gain.gain.setValueAtTime(0.2, now);
				gain.gain.exponentialRampToValueAtTime(0.01, now + 0.1);
				osc.start(now);
				osc.stop(now + 0.1);
			} else if (type === 'clear') {
				osc.type = 'sine';
				osc.frequency.setValueAtTime(523.25, now); // C5
				osc.frequency.setValueAtTime(659.25, now + 0.08); // E5
				osc.frequency.setValueAtTime(783.99, now + 0.16); // G5
				gain.gain.setValueAtTime(0.25, now);
				gain.gain.exponentialRampToValueAtTime(0.01, now + 0.25);
				osc.start(now);
				osc.stop(now + 0.25);
			} else if (type === 'tetris') {
				osc.type = 'triangle';
				osc.frequency.setValueAtTime(523.25, now); // C5
				osc.frequency.setValueAtTime(659.25, now + 0.08); // E5
				osc.frequency.setValueAtTime(783.99, now + 0.16); // G5
				osc.frequency.setValueAtTime(1046.50, now + 0.24); // C6
				gain.gain.setValueAtTime(0.3, now);
				gain.gain.exponentialRampToValueAtTime(0.01, now + 0.4);
				osc.start(now);
				osc.stop(now + 0.4);
			} else if (type === 'gameover') {
				osc.type = 'sawtooth';
				osc.frequency.setValueAtTime(300, now);
				osc.frequency.linearRampToValueAtTime(80, now + 0.5);
				gain.gain.setValueAtTime(0.3, now);
				gain.gain.exponentialRampToValueAtTime(0.01, now + 0.5);
				osc.start(now);
				osc.stop(now + 0.5);
			}
		} catch (e) { }
	}

	// Game state variables
	var board = [];
	var score = 0;
	var level = 1;
	var lines = 0;
	var highScore = parseInt(elHighScore ? elHighScore.textContent : 0) || 0;
	var isGameOver = false;
	var isPaused = false;
	var isPlaying = false;
	var gameToken = '';

	var currentPiece = null;
	var nextPiece = null;
	var holdPiece = null;
	var canHold = true;

	var bag = [];
	var dropCounter = 0;
	var dropInterval = 1000;
	var lastTime = 0;
	var animFrameId = null;

	function createBoard() {
		var b = [];
		for (var r = 0; r < ROWS; r++) {
			b[r] = [];
			for (var c = 0; c < COLS; c++) {
				b[r][c] = 0;
			}
		}
		return b;
	}

	function getRandomPieceType() {
		if (bag.length === 0) {
			bag = ['I', 'J', 'L', 'O', 'S', 'T', 'Z'];
			for (var i = bag.length - 1; i > 0; i--) {
				var j = Math.floor(Math.random() * (i + 1));
				var temp = bag[i];
				bag[i] = bag[j];
				bag[j] = temp;
			}
		}
		return bag.pop();
	}

	function createPiece(type) {
		var shape = SHAPES[type];
		return {
			type: type,
			shape: JSON.parse(JSON.stringify(shape)),
			x: Math.floor((COLS - shape[0].length) / 2),
			y: type === 'I' ? -1 : 0
		};
	}

	function spawnPiece() {
		if (!nextPiece) {
			nextPiece = createPiece(getRandomPieceType());
		}
		currentPiece = nextPiece;
		nextPiece = createPiece(getRandomPieceType());
		canHold = true;

		if (checkCollision(currentPiece, 0, 0)) {
			handleGameOver();
		}

		drawNextPiece();
	}

	function checkCollision(piece, offsetX, offsetY, customShape) {
		var shape = customShape || piece.shape;
		for (var r = 0; r < shape.length; r++) {
			for (var c = 0; c < shape[r].length; c++) {
				if (shape[r][c]) {
					var newX = piece.x + c + offsetX;
					var newY = piece.y + r + offsetY;

					if (newX < 0 || newX >= COLS || newY >= ROWS) {
						return true;
					}
					if (newY >= 0 && board[newY][newX]) {
						return true;
					}
				}
			}
		}
		return false;
	}

	function rotateMatrix(matrix, dir) {
		var N = matrix.length;
		var result = [];
		for (var r = 0; r < N; r++) {
			result[r] = [];
			for (var c = 0; c < N; c++) {
				if (dir > 0) {
					result[r][c] = matrix[N - 1 - c][r];
				} else {
					result[r][c] = matrix[c][N - 1 - r];
				}
			}
		}
		return result;
	}

	function rotateCurrentPiece() {
		if (!currentPiece || isPaused || !isPlaying) return;
		var rotated = rotateMatrix(currentPiece.shape, 1);

		var offsets = [0, 1, -1, 2, -2];
		for (var i = 0; i < offsets.length; i++) {
			if (!checkCollision(currentPiece, offsets[i], 0, rotated)) {
				currentPiece.shape = rotated;
				currentPiece.x += offsets[i];
				playSound('rotate');
				draw();
				return;
			}
		}
	}

	function moveLeft() {
		if (!currentPiece || isPaused || !isPlaying) return;
		if (!checkCollision(currentPiece, -1, 0)) {
			currentPiece.x--;
			playSound('move');
			draw();
		}
	}

	function moveRight() {
		if (!currentPiece || isPaused || !isPlaying) return;
		if (!checkCollision(currentPiece, 1, 0)) {
			currentPiece.x++;
			playSound('move');
			draw();
		}
	}

	function softDrop() {
		if (!currentPiece || isPaused || !isPlaying) return;
		if (!checkCollision(currentPiece, 0, 1)) {
			currentPiece.y++;
			score += 1;
			updateStats();
			draw();
		} else {
			lockPiece();
		}
	}

	function hardDrop() {
		if (!currentPiece || isPaused || !isPlaying) return;
		var dropDistance = 0;
		while (!checkCollision(currentPiece, 0, 1)) {
			currentPiece.y++;
			dropDistance++;
		}
		score += dropDistance * 2;
		playSound('drop');
		updateStats();
		lockPiece();
	}

	function doHold() {
		if (!currentPiece || !canHold || isPaused || !isPlaying) return;

		if (!holdPiece) {
			holdPiece = createPiece(currentPiece.type);
			spawnPiece();
		} else {
			var temp = holdPiece.type;
			holdPiece = createPiece(currentPiece.type);
			currentPiece = createPiece(temp);
		}
		canHold = false;
		playSound('move');
		drawHoldPiece();
		draw();
	}

	function lockPiece() {
		for (var r = 0; r < currentPiece.shape.length; r++) {
			for (var c = 0; c < currentPiece.shape[r].length; c++) {
				if (currentPiece.shape[r][c]) {
					var boardY = currentPiece.y + r;
					var boardX = currentPiece.x + c;
					if (boardY >= 0) {
						board[boardY][boardX] = currentPiece.type;
					}
				}
			}
		}

		clearLines();
		spawnPiece();
		draw();
	}

	function clearLines() {
		var linesCleared = 0;

		for (var r = ROWS - 1; r >= 0; r--) {
			var full = true;
			for (var c = 0; c < COLS; c++) {
				if (!board[r][c]) {
					full = false;
					break;
				}
			}

			if (full) {
				linesCleared++;
				board.splice(r, 1);
				var emptyRow = [];
				for (var c = 0; c < COLS; c++) emptyRow.push(0);
				board.unshift(emptyRow);
				r++;
			}
		}

		if (linesCleared > 0) {
			lines += linesCleared;
			var lineScores = [0, 100, 300, 500, 800];
			score += lineScores[linesCleared] * level;

			if (linesCleared === 4) {
				playSound('tetris');
			} else {
				playSound('clear');
			}

			var newLevel = Math.floor(lines / 10) + 1;
			if (newLevel > level) {
				level = newLevel;
				dropInterval = Math.max(100, 1000 - (level - 1) * 80);
			}

			updateStats();
		}
	}

	function updateStats() {
		if (score > highScore) {
			highScore = score;
		}
		if (elScore) elScore.textContent = score;
		if (elLevel) elLevel.textContent = level;
		if (elLines) elLines.textContent = lines;
		if (elHighScore) elHighScore.textContent = highScore;
	}

	function drawBlock(context, x, y, type, isGhost) {
		var color = COLORS[type];

		if (isGhost) {
			context.strokeStyle = color;
			context.lineWidth = 1.5;
			context.strokeRect(x * BLOCK_SIZE + 2, y * BLOCK_SIZE + 2, BLOCK_SIZE - 4, BLOCK_SIZE - 4);
			return;
		}

		context.fillStyle = color;
		context.fillRect(x * BLOCK_SIZE, y * BLOCK_SIZE, BLOCK_SIZE, BLOCK_SIZE);

		context.fillStyle = 'rgba(255, 255, 255, 0.3)';
		context.fillRect(x * BLOCK_SIZE, y * BLOCK_SIZE, BLOCK_SIZE, 3);
		context.fillRect(x * BLOCK_SIZE, y * BLOCK_SIZE, 3, BLOCK_SIZE);

		context.fillStyle = 'rgba(0, 0, 0, 0.3)';
		context.fillRect(x * BLOCK_SIZE, y * BLOCK_SIZE + BLOCK_SIZE - 3, BLOCK_SIZE, 3);
		context.fillRect(x * BLOCK_SIZE + BLOCK_SIZE - 3, y * BLOCK_SIZE, 3, BLOCK_SIZE);

		context.strokeStyle = '#0d1117';
		context.lineWidth = 1;
		context.strokeRect(x * BLOCK_SIZE, y * BLOCK_SIZE, BLOCK_SIZE, BLOCK_SIZE);
	}

	function draw() {
		ctx.clearRect(0, 0, mainCanvas.width, mainCanvas.height);

		ctx.strokeStyle = '#161b22';
		ctx.lineWidth = 1;
		for (var r = 0; r <= ROWS; r++) {
			ctx.beginPath();
			ctx.moveTo(0, r * BLOCK_SIZE);
			ctx.lineTo(COLS * BLOCK_SIZE, r * BLOCK_SIZE);
			ctx.stroke();
		}
		for (var c = 0; c <= COLS; c++) {
			ctx.beginPath();
			ctx.moveTo(c * BLOCK_SIZE, 0);
			ctx.lineTo(c * BLOCK_SIZE, ROWS * BLOCK_SIZE);
			ctx.stroke();
		}

		for (var r = 0; r < ROWS; r++) {
			for (var c = 0; c < COLS; c++) {
				if (board[r][c]) {
					drawBlock(ctx, c, r, board[r][c], false);
				}
			}
		}

		if (currentPiece && isPlaying && !isPaused) {
			var ghostY = currentPiece.y;
			while (!checkCollision(currentPiece, 0, ghostY - currentPiece.y + 1)) {
				ghostY++;
			}
			for (var r = 0; r < currentPiece.shape.length; r++) {
				for (var c = 0; c < currentPiece.shape[r].length; c++) {
					if (currentPiece.shape[r][c]) {
						var py = ghostY + r;
						var px = currentPiece.x + c;
						if (py >= 0) {
							drawBlock(ctx, px, py, currentPiece.type, true);
						}
					}
				}
			}

			for (var r = 0; r < currentPiece.shape.length; r++) {
				for (var c = 0; c < currentPiece.shape[r].length; c++) {
					if (currentPiece.shape[r][c]) {
						var py = currentPiece.y + r;
						var px = currentPiece.x + c;
						if (py >= 0) {
							drawBlock(ctx, px, py, currentPiece.type, false);
						}
					}
				}
			}
		}
	}

	function drawPreview(context, piece) {
		if (!context) return;
		context.clearRect(0, 0, 100, 100);
		if (!piece) return;

		var shape = piece.shape;
		var size = 20;
		var offsetX = Math.floor((100 - shape[0].length * size) / 2);
		var offsetY = Math.floor((100 - shape.length * size) / 2);

		for (var r = 0; r < shape.length; r++) {
			for (var c = 0; c < shape[r].length; c++) {
				if (shape[r][c]) {
					var x = offsetX + c * size;
					var y = offsetY + r * size;

					context.fillStyle = COLORS[piece.type];
					context.fillRect(x, y, size, size);

					context.fillStyle = 'rgba(255, 255, 255, 0.3)';
					context.fillRect(x, y, size, 2);
					context.fillRect(x, y, 2, size);

					context.strokeStyle = '#0d1117';
					context.lineWidth = 1;
					context.strokeRect(x, y, size, size);
				}
			}
		}
	}

	function drawNextPiece() {
		drawPreview(nextCtx, nextPiece);
	}

	function drawHoldPiece() {
		drawPreview(holdCtx, holdPiece);
	}

	function updateGame(time) {
		if (!isPlaying || isPaused || isGameOver) return;

		var deltaTime = time - lastTime;
		lastTime = time;

		dropCounter += deltaTime;
		if (dropCounter > dropInterval) {
			softDrop();
			dropCounter = 0;
		}

		draw();
		animFrameId = requestAnimationFrame(updateGame);
	}

	function startGame() {
		// Fetch one-time session token from server
		fetch('/tetris?action=init_token')
			.then(function (res) { return res.json(); })
			.then(function (data) {
				if (data.success) {
					gameToken = data.token;
				}
			})
			.catch(function (err) {
				console.error('Failed to init token', err);
			});

		board = createBoard();
		score = 0;
		level = 1;
		lines = 0;
		dropInterval = 1000;
		dropCounter = 0;
		lastTime = performance.now();

		bag = [];
		currentPiece = null;
		nextPiece = null;
		holdPiece = null;
		canHold = true;

		isGameOver = false;
		isPaused = false;
		isPlaying = true;

		updatePauseButtonsUI();
		overlay.style.display = 'none';

		updateStats();
		drawHoldPiece();
		spawnPiece();
		draw();

		if (animFrameId) cancelAnimationFrame(animFrameId);
		animFrameId = requestAnimationFrame(updateGame);
	}

	function updatePauseButtonsUI() {
		var btns = document.querySelectorAll('#btn-pause, #btn-pause-mobile');
		for (var i = 0; i < btns.length; i++) {
			btns[i].disabled = !isPlaying || isGameOver;
			btns[i].textContent = isPaused ? 'Atsākt (P)' : 'Nopauzēt (P)';
		}
	}

	function updateSoundButtonsUI() {
		var btns = document.querySelectorAll('#btn-sound, #btn-sound-mobile');
		for (var i = 0; i < btns.length; i++) {
			btns[i].textContent = audioEnabled ? 'Skaņa: IESL.' : 'Skaņa: IZSL.';
		}
	}

	function togglePause() {
		if (!isPlaying || isGameOver) return;
		isPaused = !isPaused;

		updatePauseButtonsUI();
		if (isPaused) {
			overlayTitle.textContent = 'PAUZE';
			overlayMsg.textContent = 'Spēle ir nopauzēta.';
			btnStart.textContent = 'Turpināt';
			overlay.style.display = 'flex';
		} else {
			overlay.style.display = 'none';
			lastTime = performance.now();
			animFrameId = requestAnimationFrame(updateGame);
		}
	}

	function handleGameOver() {
		isGameOver = true;
		isPlaying = false;
		updatePauseButtonsUI();

		playSound('gameover');

		overlayTitle.textContent = 'SPĒLES BEIGAS!';
		overlayMsg.innerHTML = 'Tavs rezultāts: <strong>' + score + '</strong> punkti!';

		btnStart.textContent = 'Spēlēt vēlreiz';
		overlay.style.display = 'flex';

		if (score > 0) {
			submitScore(score, lines, level);
		}
	}

	function submitScore(finalScore, finalLines, finalLevel) {
		var formData = new FormData();
		formData.append('token', gameToken);
		formData.append('score', finalScore);
		formData.append('lines', finalLines);
		formData.append('level', finalLevel);

		fetch('/tetris?action=push', {
			method: 'POST',
			body: formData
		})
			.then(function (res) { return res.json(); })
			.then(function (data) {
				if (data.success) {
					if (data.isNewRecord) {
						overlayMsg.innerHTML += '<br><span style="color:#2ea44f;font-weight:bold;">Apsveicam! Jauns personīgais rekords!</span>';
					} else {
						overlayMsg.innerHTML += '<br><span style="color:#8b949e;">Rezultāts saglabāts!</span>';
					}
				} else {
					overlayMsg.innerHTML += '<br><span style="color:#d73a49;font-weight:bold;">' + data.message + '</span>';
				}
			})
			.catch(function (err) {
				console.error('Kļūda saglabājot rezultātu:', err);
			});
	}

	// Keyboard Controls
	document.addEventListener('keydown', function (e) {
		if (['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight', ' '].indexOf(e.key) >= 0 && e.target === document.body) {
			e.preventDefault();
		}

		if (!isPlaying) {
			if (e.key === ' ' || e.key === 'Enter') {
				startGame();
			}
			return;
		}

		if (e.key === 'p' || e.key === 'P' || e.key === 'Escape') {
			togglePause();
			return;
		}

		if (isPaused) return;

		switch (e.key) {
			case 'ArrowLeft':
				moveLeft();
				break;
			case 'ArrowRight':
				moveRight();
				break;
			case 'ArrowDown':
				softDrop();
				break;
			case 'ArrowUp':
			case 'x':
			case 'X':
				rotateCurrentPiece();
				break;
			case ' ':
				hardDrop();
				break;
			case 'c':
			case 'C':
			case 'Shift':
				doHold();
				break;
		}
	});

	// Button Listeners
	if (btnStart) {
		btnStart.addEventListener('click', function () {
			if (isPaused) {
				togglePause();
			} else {
				startGame();
			}
		});
	}

	var btnPauses = document.querySelectorAll('#btn-pause, #btn-pause-mobile');
	for (var p = 0; p < btnPauses.length; p++) {
		btnPauses[p].addEventListener('click', function () {
			togglePause();
		});
	}

	var btnSounds = document.querySelectorAll('#btn-sound, #btn-sound-mobile');
	for (var s = 0; s < btnSounds.length; s++) {
		btnSounds[s].addEventListener('click', function () {
			audioEnabled = !audioEnabled;
			updateSoundButtonsUI();
		});
	}

	// Touch Controls Listener Binding
	var btnTouchLeft = document.getElementById('btn-touch-left');
	var btnTouchRight = document.getElementById('btn-touch-right');
	var btnTouchDown = document.getElementById('btn-touch-down');
	var btnTouchRotate = document.getElementById('btn-touch-rotate');
	var btnTouchDrop = document.getElementById('btn-touch-drop');
	var btnTouchHold = document.getElementById('btn-touch-hold');

	function bindTouchBtn(el, action) {
		if (!el) return;
		var handler = function (e) {
			e.preventDefault();
			initAudio();
			action();
		};
		el.addEventListener('touchstart', handler, { passive: false });
		el.addEventListener('click', function (e) {
			if (e.detail === 0 || !('ontouchstart' in window)) {
				initAudio();
				action();
			}
		});
	}

	bindTouchBtn(btnTouchLeft, moveLeft);
	bindTouchBtn(btnTouchRight, moveRight);
	bindTouchBtn(btnTouchDown, softDrop);
	bindTouchBtn(btnTouchRotate, rotateCurrentPiece);
	bindTouchBtn(btnTouchDrop, hardDrop);
	bindTouchBtn(btnTouchHold, doHold);

	// Dynamic Window Scaling Logic
	function autoScaleGame() {
		var scalerWrapper = document.querySelector('.tetris-scaler-wrapper');
		var mainPanel = document.querySelector('.tetris-main-panel');
		if (!scalerWrapper || !mainPanel) return;

		// Clear inline styles to accurately measure unscaled bounding box
		mainPanel.style.transform = 'none';
		scalerWrapper.style.width = '';
		scalerWrapper.style.height = '';

		var unscaledWidth = mainPanel.offsetWidth;
		var unscaledHeight = mainPanel.offsetHeight;

		if (!unscaledWidth || !unscaledHeight) return;

		var container = scalerWrapper.parentElement || document.body;
		var availableWidth = container.clientWidth;
		if (!availableWidth || availableWidth > window.innerWidth) {
			availableWidth = window.innerWidth;
		}
		// 20px padding buffer
		availableWidth = Math.max(280, availableWidth - 20);

		// Fit inside available viewport height (30px margin)
		var availableHeight = Math.max(300, window.innerHeight - 30);

		var scaleX = availableWidth / unscaledWidth;
		var scaleY = availableHeight / unscaledHeight;

		// Scale factor constrained between 0.35 and 1.0
		var scale = Math.min(scaleX, scaleY, 1);
		if (scale < 0.35) scale = 0.35;

		if (scale < 0.99) {
			mainPanel.style.transform = 'scale(' + scale + ')';
			scalerWrapper.style.width = Math.round(unscaledWidth * scale) + 'px';
			scalerWrapper.style.height = Math.round(unscaledHeight * scale) + 'px';
		} else {
			mainPanel.style.transform = 'none';
			scalerWrapper.style.width = '';
			scalerWrapper.style.height = '';
		}
	}

	window.addEventListener('resize', autoScaleGame);
	window.addEventListener('orientationchange', function () {
		setTimeout(autoScaleGame, 100);
	});

	board = createBoard();
	draw();
	autoScaleGame();
});
