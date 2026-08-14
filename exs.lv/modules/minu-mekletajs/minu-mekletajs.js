/**
 * Minesweeper Engine for EXS.LV
 */

$(document).ready(function () {
	if ($('#ms-board').length === 0) return;

	var CONFIGS = {
		easy: { rows: 9, cols: 9, mines: 10 },
		medium: { rows: 16, cols: 16, mines: 40 },
		hard: { rows: 16, cols: 30, mines: 99 } // 30 cols, 16 rows
	};

	var currentDiff = 'easy';
	var rows = 9;
	var cols = 9;
	var mineCount = 10;

	var grid = []; // 2D array of cell objects: { mine: bool, revealed: bool, flagged: bool, count: int }
	var isFirstClick = true;
	var isGameOver = false;
	var isWon = false;
	var timerInterval = null;
	var timerSeconds = 0;
	var flagsPlaced = 0;
	var revealedCount = 0;
	var mobileMode = 'reveal'; // 'reveal' or 'flag'
	var sessionToken = '';

	function initSession() {
		$.getJSON('/minu-mekletajs/?action=init_token', function (res) {
			if (res && res.success) {
				sessionToken = res.token;
			}
		});
	}

	function startNewGame() {
		currentDiff = $('#ms-difficulty').val() || 'easy';
		var cfg = CONFIGS[currentDiff] || CONFIGS.easy;
		rows = cfg.rows;
		cols = cfg.cols;
		mineCount = cfg.mines;

		isFirstClick = true;
		isGameOver = false;
		isWon = false;
		timerSeconds = 0;
		flagsPlaced = 0;
		revealedCount = 0;

		clearInterval(timerInterval);
		timerInterval = null;
		updateTimerDisplay(0);
		updateMineCounter(mineCount);
		setFace('😊');

		initSession();
		buildGrid();
		renderBoard();
	}

	function buildGrid() {
		grid = [];
		for (var r = 0; r < rows; r++) {
			var row = [];
			for (var c = 0; c < cols; c++) {
				row.push({
					mine: false,
					revealed: false,
					flagged: false,
					count: 0
				});
			}
			grid.push(row);
		}
	}

	function placeMines(safeR, safeC) {
		var placed = 0;
		while (placed < mineCount) {
			var r = Math.floor(Math.random() * rows);
			var c = Math.floor(Math.random() * cols);

			// Don't place mine on first clicked cell or its immediate 3x3 neighbors
			if (Math.abs(r - safeR) <= 1 && Math.abs(c - safeC) <= 1) {
				continue;
			}

			if (!grid[r][c].mine) {
				grid[r][c].mine = true;
				placed++;
			}
		}

		// Compute neighbor counts
		for (var r = 0; r < rows; r++) {
			for (var c = 0; c < cols; c++) {
				if (!grid[r][c].mine) {
					grid[r][c].count = countNeighborMines(r, c);
				}
			}
		}
	}

	function countNeighborMines(r, c) {
		var cnt = 0;
		for (var dr = -1; dr <= 1; dr++) {
			for (var dc = -1; dc <= 1; dc++) {
				if (dr === 0 && dc === 0) continue;
				var nr = r + dr;
				var nc = c + dc;
				if (nr >= 0 && nr < rows && nc >= 0 && nc < cols) {
					if (grid[nr][nc].mine) cnt++;
				}
			}
		}
		return cnt;
	}

	function renderBoard() {
		var $board = $('#ms-board');
		$board.empty().removeClass('ms-easy ms-medium ms-hard').addClass('ms-' + currentDiff);

		var cellSize = '28px';
		if (currentDiff === 'hard') {
			cellSize = '18px';
		} else if (currentDiff === 'medium') {
			cellSize = '24px';
		}

		$board.css({
			'grid-template-columns': 'repeat(' + cols + ', ' + cellSize + ')',
			'grid-template-rows': 'repeat(' + rows + ', ' + cellSize + ')'
		});

		for (var r = 0; r < rows; r++) {
			for (var c = 0; c < cols; c++) {
				var $cell = $('<div></div>')
					.addClass('ms-cell')
					.attr('data-r', r)
					.attr('data-c', c);
				$board.append($cell);
			}
		}
	}

	function updateCellDOM(r, c) {
		var cell = grid[r][c];
		var $cell = $('#ms-board .ms-cell[data-r="' + r + '"][data-c="' + c + '"]');

		$cell.removeClass('revealed flagged mine exploded false-flag num-1 num-2 num-3 num-4 num-5 num-6 num-7 num-8');
		$cell.text('');

		if (cell.flagged) {
			$cell.addClass('flagged').text('🚩');
		} else if (cell.revealed) {
			$cell.addClass('revealed');
			if (cell.mine) {
				$cell.addClass('mine').text('💣');
			} else if (cell.count > 0) {
				$cell.addClass('num-' + cell.count).text(cell.count);
			}
		}
	}

	function startTimer() {
		if (timerInterval) return;
		timerInterval = setInterval(function () {
			timerSeconds++;
			if (timerSeconds > 999) timerSeconds = 999;
			updateTimerDisplay(timerSeconds);
		}, 1000);
	}

	function updateTimerDisplay(sec) {
		var s = String(sec);
		while (s.length < 3) s = '0' + s;
		$('#ms-timer').text(s);
	}

	function updateMineCounter(count) {
		var s = String(count);
		if (count < 0) {
			s = '-' + String(Math.abs(count)).padStart(2, '0');
		} else {
			while (s.length < 3) s = '0' + s;
		}
		$('#ms-mine-counter').text(s);
	}

	function setFace(emoji) {
		$('#ms-btn-face').text(emoji);
	}

	function handleCellClick(r, c) {
		if (isGameOver || isWon) return;

		var cell = grid[r][c];
		if (cell.flagged) return; // Cannot click flagged cell

		if (isFirstClick) {
			isFirstClick = false;
			placeMines(r, c);
			startTimer();
		}

		if (cell.mine) {
			// Game Over!
			cell.revealed = true;
			updateCellDOM(r, c);
			$('#ms-board .ms-cell[data-r="' + r + '"][data-c="' + c + '"]').addClass('exploded');
			triggerGameOver(false);
			return;
		}

		revealCell(r, c);
		checkWin();
	}

	function revealCell(r, c) {
		var cell = grid[r][c];
		if (cell.revealed || cell.flagged) return;

		cell.revealed = true;
		revealedCount++;
		updateCellDOM(r, c);

		if (cell.count === 0 && !cell.mine) {
			// Flood fill
			for (var dr = -1; dr <= 1; dr++) {
				for (var dc = -1; dc <= 1; dc++) {
					if (dr === 0 && dc === 0) continue;
					var nr = r + dr;
					var nc = c + dc;
					if (nr >= 0 && nr < rows && nc >= 0 && nc < cols) {
						if (!grid[nr][nc].revealed && !grid[nr][nc].flagged) {
							revealCell(nr, nc);
						}
					}
				}
			}
		}
	}

	function toggleFlag(r, c) {
		if (isGameOver || isWon) return;
		var cell = grid[r][c];
		if (cell.revealed) return;

		if (cell.flagged) {
			cell.flagged = false;
			flagsPlaced--;
		} else {
			cell.flagged = true;
			flagsPlaced++;
		}

		updateCellDOM(r, c);
		updateMineCounter(mineCount - flagsPlaced);
	}

	function handleChordClick(r, c) {
		if (isGameOver || isWon) return;
		var cell = grid[r][c];
		if (!cell.revealed || cell.count === 0) return;

		// Count flags around cell
		var flagCnt = 0;
		for (var dr = -1; dr <= 1; dr++) {
			for (var dc = -1; dc <= 1; dc++) {
				if (dr === 0 && dc === 0) continue;
				var nr = r + dr;
				var nc = c + dc;
				if (nr >= 0 && nr < rows && nc >= 0 && nc < cols) {
					if (grid[nr][nc].flagged) flagCnt++;
				}
			}
		}

		if (flagCnt === cell.count) {
			// Reveal all unflagged neighbors
			for (var dr = -1; dr <= 1; dr++) {
				for (var dc = -1; dc <= 1; dc++) {
					if (dr === 0 && dc === 0) continue;
					var nr = r + dr;
					var nc = c + dc;
					if (nr >= 0 && nr < rows && nc >= 0 && nc < cols) {
						var nCell = grid[nr][nc];
						if (!nCell.revealed && !nCell.flagged) {
							if (nCell.mine) {
								nCell.revealed = true;
								updateCellDOM(nr, nc);
								$('#ms-board .ms-cell[data-r="' + nr + '"][data-c="' + nc + '"]').addClass('exploded');
								triggerGameOver(false);
								return;
							} else {
								revealCell(nr, nc);
							}
						}
					}
				}
			}
			checkWin();
		}
	}

	function checkWin() {
		var totalSafeCells = (rows * cols) - mineCount;
		if (revealedCount === totalSafeCells && !isGameOver) {
			triggerGameOver(true);
		}
	}

	function triggerGameOver(won) {
		clearInterval(timerInterval);
		if (won) {
			isWon = true;
			setFace('😎');
			// Flag all remaining mines
			for (var r = 0; r < rows; r++) {
				for (var c = 0; c < cols; c++) {
					if (grid[r][c].mine && !grid[r][c].flagged) {
						grid[r][c].flagged = true;
						updateCellDOM(r, c);
					}
				}
			}
			updateMineCounter(0);
			submitScore();
		} else {
			isGameOver = true;
			setFace('😵');
			// Reveal all mines & show false flags
			for (var r = 0; r < rows; r++) {
				for (var c = 0; c < cols; c++) {
					var cell = grid[r][c];
					if (cell.mine && !cell.flagged) {
						cell.revealed = true;
						updateCellDOM(r, c);
					} else if (!cell.mine && cell.flagged) {
						var $cell = $('#ms-board .ms-cell[data-r="' + r + '"][data-c="' + c + '"]');
						$cell.addClass('false-flag').text('❌');
					}
				}
			}
		}
	}

	function submitScore() {
		if (timerSeconds <= 0 || !sessionToken) return;

		$.post('/minu-mekletajs/?action=push', {
			token: sessionToken,
			time_sec: timerSeconds,
			difficulty: currentDiff
		}, function (res) {
			if (res && res.success) {
				alert('Apsveicam! Tu uzvarēji ar laiku ' + timerSeconds + ' sek. ' + (res.message || ''));
			}
		}, 'json');
	}

	// Board Event Listeners
	$('#ms-board').on('mousedown touchstart', '.ms-cell', function (e) {
		if (isGameOver || isWon) return;
		setFace('😮');
	});

	$(document).on('mouseup touchend', function () {
		if (!isGameOver && !isWon) {
			setFace('😊');
		}
	});

	// Left click / Touch
	$('#ms-board').on('click', '.ms-cell', function (e) {
		var r = parseInt($(this).attr('data-r'));
		var c = parseInt($(this).attr('data-c'));

		if (mobileMode === 'flag') {
			toggleFlag(r, c);
		} else {
			handleCellClick(r, c);
		}
	});

	// Right click for flag
	$('#ms-board').on('contextmenu', '.ms-cell', function (e) {
		e.preventDefault();
		var r = parseInt($(this).attr('data-r'));
		var c = parseInt($(this).attr('data-c'));
		toggleFlag(r, c);
	});

	// Double click / Chord click on revealed numbers
	$('#ms-board').on('dblclick', '.ms-cell', function (e) {
		var r = parseInt($(this).attr('data-r'));
		var c = parseInt($(this).attr('data-c'));
		handleChordClick(r, c);
	});

	// Mobile Mode buttons
	$('#ms-mode-reveal').on('click', function () {
		mobileMode = 'reveal';
		$('.ms-mode-btn').removeClass('active');
		$(this).addClass('active');
	});

	$('#ms-mode-flag').on('click', function () {
		mobileMode = 'flag';
		$('.ms-mode-btn').removeClass('active');
		$(this).addClass('active');
	});

	// Difficulty Selector change
	$('#ms-difficulty').on('change', function () {
		startNewGame();
	});

	// Reset button
	$('#ms-btn-face').on('click', function () {
		startNewGame();
	});

	// Initial start
	startNewGame();
});
