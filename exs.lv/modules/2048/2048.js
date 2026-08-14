/**
 * 2048 Game Engine for EXS.LV
 */

$(document).ready(function () {
	if ($('#twenty48-board').length === 0) return;

	var SIZE = 4;
	var board = [];
	var score = 0;
	var movesCount = 0;
	var previousState = null;
	var isGameOver = false;
	var isWon = false;
	var sessionToken = '';

	// Initialize new session token
	function initSession() {
		$.getJSON('/2048/?action=init_token', function (res) {
			if (res && res.success) {
				sessionToken = res.token;
			}
		});
	}

	function resetGame() {
		board = [
			[0, 0, 0, 0],
			[0, 0, 0, 0],
			[0, 0, 0, 0],
			[0, 0, 0, 0]
		];
		score = 0;
		movesCount = 0;
		previousState = null;
		isGameOver = false;
		isWon = false;

		$('#twenty48-current-score').text(0);
		$('#twenty48-btn-undo').prop('disabled', true);
		$('#twenty48-overlay').removeClass('active win gameover');

		initSession();
		spawnTile();
		spawnTile();
		renderBoard();
	}

	function saveState() {
		previousState = {
			board: JSON.parse(JSON.stringify(board)),
			score: score,
			movesCount: movesCount
		};
		$('#twenty48-btn-undo').prop('disabled', false);
	}

	function undoMove() {
		if (!previousState) return;
		board = JSON.parse(JSON.stringify(previousState.board));
		score = previousState.score;
		movesCount = previousState.movesCount;
		previousState = null;

		$('#twenty48-current-score').text(score);
		$('#twenty48-btn-undo').prop('disabled', true);
		$('#twenty48-overlay').removeClass('active win gameover');
		renderBoard();
	}

	function spawnTile() {
		var emptyCells = [];
		for (var r = 0; r < SIZE; r++) {
			for (var c = 0; c < SIZE; c++) {
				if (board[r][c] === 0) {
					emptyCells.push({ r: r, c: c });
				}
			}
		}
		if (emptyCells.length === 0) return;
		var randCell = emptyCells[Math.floor(Math.random() * emptyCells.length)];
		board[randCell.r][randCell.c] = Math.random() < 0.9 ? 2 : 4;
	}

	function getMaxTile() {
		var max = 0;
		for (var r = 0; r < SIZE; r++) {
			for (var c = 0; c < SIZE; c++) {
				if (board[r][c] > max) max = board[r][c];
			}
		}
		return max;
	}

	function renderBoard() {
		var $container = $('#twenty48-tile-container');
		$container.empty();

		for (var r = 0; r < SIZE; r++) {
			for (var c = 0; c < SIZE; c++) {
				var val = board[r][c];
				if (val > 0) {
					var $tile = $('<div></div>')
						.addClass('twenty48-tile')
						.addClass('twenty48-tile-' + (val <= 2048 ? val : 'super'))
						.addClass('twenty48-pos-' + r + '-' + c)
						.text(val);
					$container.append($tile);
				}
			}
		}
	}

	function move(direction) {
		if (isGameOver) return false;

		var moved = false;
		var stateBeforeMove = JSON.parse(JSON.stringify(board));
		var scoreGained = 0;

		// Rotate board to simplify sliding left
		function rotate(b) {
			var newB = [
				[0, 0, 0, 0],
				[0, 0, 0, 0],
				[0, 0, 0, 0],
				[0, 0, 0, 0]
			];
			for (var r = 0; r < SIZE; r++) {
				for (var c = 0; c < SIZE; c++) {
					newB[c][SIZE - 1 - r] = b[r][c];
				}
			}
			return newB;
		}

		// Apply rotations: 0 for left, 1 for down, 2 for right, 3 for up
		var rotations = 0;
		if (direction === 'down') rotations = 1;
		if (direction === 'right') rotations = 2;
		if (direction === 'up') rotations = 3;

		var tempBoard = JSON.parse(JSON.stringify(board));
		for (var i = 0; i < rotations; i++) {
			tempBoard = rotate(tempBoard);
		}

		// Slide left logic
		for (var r = 0; r < SIZE; r++) {
			var row = tempBoard[r].filter(function (v) { return v !== 0; });
			var newRow = [];

			for (var c = 0; c < row.length; c++) {
				if (c < row.length - 1 && row[c] === row[c + 1]) {
					var mergedVal = row[c] * 2;
					newRow.push(mergedVal);
					scoreGained += mergedVal;
					if (mergedVal === 2048 && !isWon) {
						isWon = true;
					}
					c++; // Skip next element because merged
				} else {
					newRow.push(row[c]);
				}
			}

			while (newRow.length < SIZE) {
				newRow.push(0);
			}

			tempBoard[r] = newRow;
		}

		// Rotate back
		for (var j = 0; j < (4 - rotations) % 4; j++) {
			tempBoard = rotate(tempBoard);
		}

		// Check if board changed
		for (var r = 0; r < SIZE; r++) {
			for (var c = 0; c < SIZE; c++) {
				if (board[r][c] !== tempBoard[r][c]) {
					moved = true;
					break;
				}
			}
			if (moved) break;
		}

		if (moved) {
			saveState();
			board = tempBoard;
			score += scoreGained;
			movesCount++;

			$('#twenty48-current-score').text(score);

			// Update best score display
			var currentBest = parseInt($('#twenty48-best-score').text().replace(/\s/g, '')) || 0;
			if (score > currentBest) {
				$('#twenty48-best-score').text(score);
			}

			spawnTile();
			renderBoard();

			if (isWon && !$('#twenty48-overlay').hasClass('active')) {
				showOverlay('Apsveicam! Sasniegta 2048 flīze!', 'Tev izdevās apvienot skaitļus un sasniegt 2048! Vari turpināt spēlēt tālāk, lai uzstādītu lielāku rekordu.', 'win');
			} else if (checkGameOver()) {
				isGameOver = true;
				showOverlay('Spēle beigusies!', 'Vairs nav brīvu gājienu. Tavs galīgais rezultāts: ' + score + ' punkti.', 'gameover');
				submitScore();
			}

			return true;
		}

		return false;
	}

	function checkGameOver() {
		for (var r = 0; r < SIZE; r++) {
			for (var c = 0; c < SIZE; c++) {
				if (board[r][c] === 0) return false;
				if (c < SIZE - 1 && board[r][c] === board[r][c + 1]) return false;
				if (r < SIZE - 1 && board[r][c] === board[r + 1][c]) return false;
			}
		}
		return true;
	}

	function showOverlay(title, msg, type) {
		$('#twenty48-overlay-title').text(title);
		$('#twenty48-overlay-msg').html(msg);
		$('#twenty48-overlay').addClass('active ' + type);
	}

	function submitScore() {
		if (score <= 0 || !sessionToken) return;

		$.post('/2048/?action=push', {
			token: sessionToken,
			score: score,
			max_tile: getMaxTile(),
			moves: movesCount
		}, function (res) {
			if (res && res.success) {
				if (res.message) {
					$('#twenty48-overlay-msg').append('<br><br><span style="color: #2ec4b6; font-weight: bold;">' + res.message + '</span>');
				}
			}
		}, 'json');
	}

	// Keyboard Controls
	$(document).off('keydown.twenty48').on('keydown.twenty48', function (e) {
		if ($(e.target).is('input, textarea, select')) return;

		var key = e.key ? e.key.toLowerCase() : '';
		var keyCode = e.keyCode;

		var handled = false;
		if (key === 'arrowleft' || key === 'a' || keyCode === 37) {
			handled = move('left');
		} else if (key === 'arrowright' || key === 'd' || keyCode === 39) {
			handled = move('right');
		} else if (key === 'arrowup' || key === 'w' || keyCode === 38) {
			handled = move('up');
		} else if (key === 'arrowdown' || key === 's' || keyCode === 40) {
			handled = move('down');
		}

		if (handled) {
			e.preventDefault();
		}
	});

	// Mobile On-screen Controls
	$('.twenty48-m-btn').on('click', function () {
		var dir = $(this).data('dir');
		if (dir) move(dir);
	});

	// Touch Swipe Controls
	var touchStartX = 0;
	var touchStartY = 0;
	var boardEl = document.getElementById('twenty48-board');

	if (boardEl) {
		boardEl.addEventListener('touchstart', function (e) {
			touchStartX = e.touches[0].clientX;
			touchStartY = e.touches[0].clientY;
		}, { passive: true });

		boardEl.addEventListener('touchend', function (e) {
			if (!touchStartX || !touchStartY) return;
			var touchEndX = e.changedTouches[0].clientX;
			var touchEndY = e.changedTouches[0].clientY;

			var dx = touchEndX - touchStartX;
			var dy = touchEndY - touchStartY;

			if (Math.abs(dx) > 30 || Math.abs(dy) > 30) {
				if (Math.abs(dx) > Math.abs(dy)) {
					if (dx > 0) move('right');
					else move('left');
				} else {
					if (dy > 0) move('down');
					else move('up');
				}
			}

			touchStartX = 0;
			touchStartY = 0;
		}, { passive: true });
	}

	// Action buttons
	$('#twenty48-btn-restart, #twenty48-btn-retry').on('click', function () {
		resetGame();
	});

	$('#twenty48-btn-undo').on('click', function () {
		undoMove();
	});

	// Start game
	resetGame();
});
