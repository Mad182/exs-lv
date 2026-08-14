/**
 * Wordle (Latvian) Engine for EXS.LV
 */

$(document).ready(function () {
	if ($('#wdl-grid').length === 0) return;

	var solutions = [];
	var validWords = [];

	var targetWord = '';
	var currentMode = 'daily'; // 'daily' or 'practice'
	var currentRow = 0;
	var currentTile = 0;
	var isGameOver = false;
	var isWon = false;
	var gridState = []; // 6 rows of 5 chars
	var keyStates = {}; // char => 'correct', 'present', 'absent'
	var timerInterval = null;
	var timerSeconds = 0;
	var sessionToken = '';

	// 1. Fetch word list
	$.getJSON('/modules/wordle/latvian-5letters.json', function (data) {
		if (data) {
			solutions = data.solutions || [];
			validWords = data.valid || [];
			startNewGame();
		}
	});

	function initSession() {
		$.getJSON('/wordle/?action=init_token', function (res) {
			if (res && res.success) {
				sessionToken = res.token;
			}
		});
	}

	function startNewGame() {
		if (solutions.length === 0) return;

		currentRow = 0;
		currentTile = 0;
		isGameOver = false;
		isWon = false;
		keyStates = {};
		timerSeconds = 0;

		gridState = [
			['', '', '', '', ''],
			['', '', '', '', ''],
			['', '', '', '', ''],
			['', '', '', '', ''],
			['', '', '', '', ''],
			['', '', '', '', '']
		];

		clearInterval(timerInterval);
		timerInterval = null;
		updateTimerDisplay(0);

		if (currentMode === 'daily') {
			targetWord = getDailyWord();
		} else {
			targetWord = solutions[Math.floor(Math.random() * solutions.length)];
		}

		initSession();
		renderGrid();
		renderKeyboard();
		startTimer();
	}

	function getDailyWord() {
		var today = new Date();
		var dateStr = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();
		var hash = 0;
		for (var i = 0; i < dateStr.length; i++) {
			hash = ((hash << 5) - hash) + dateStr.charCodeAt(i);
			hash |= 0;
		}
		var idx = Math.abs(hash) % solutions.length;
		return solutions[idx];
	}

	function renderGrid() {
		var $grid = $('#wdl-grid');
		$grid.empty();

		for (var r = 0; r < 6; r++) {
			var $row = $('<div></div>').addClass('wdl-row');
			for (var c = 0; c < 5; c++) {
				var char = gridState[r][c];
				var $tile = $('<div></div>')
					.addClass('wdl-tile')
					.attr('data-r', r)
					.attr('data-c', c);

				if (char) {
					$tile.text(char).addClass('filled');
				}
				$row.append($tile);
			}
			$grid.append($row);
		}
	}

	function renderKeyboard() {
		$('.wdl-key').removeClass('correct present absent');
		for (var key in keyStates) {
			var state = keyStates[key];
			$('.wdl-key[data-key="' + key + '"]').addClass(state);
		}
	}

	function handleKeyPress(key) {
		if (isGameOver) return;

		key = key.toUpperCase();

		if (key === 'ENTER') {
			submitGuess();
			return;
		}

		if (key === 'BACKSPACE' || key === 'DELETE') {
			deleteLetter();
			return;
		}

		// Single Latvian character
		if (key.length === 1 && currentTile < 5) {
			gridState[currentRow][currentTile] = key;
			var $tile = $('.wdl-tile[data-r="' + currentRow + '"][data-c="' + currentTile + '"]');
			$tile.text(key).addClass('filled pop');
			currentTile++;
		}
	}

	function deleteLetter() {
		if (currentTile > 0) {
			currentTile--;
			gridState[currentRow][currentTile] = '';
			var $tile = $('.wdl-tile[data-r="' + currentRow + '"][data-c="' + currentTile + '"]');
			$tile.text('').removeClass('filled pop');
		}
	}

	function submitGuess() {
		if (currentTile < 5) {
			showToast('Vārdam jābūt 5 burtus garam!');
			shakeRow(currentRow);
			return;
		}

		var guess = gridState[currentRow].join('');

		// Check dictionary validity
		if (validWords.indexOf(guess) === -1 && solutions.indexOf(guess) === -1) {
			showToast('Vārds "' + guess + '" nav vārdnīcā!');
			shakeRow(currentRow);
			return;
		}

		// Evaluate colors accurately with letter counts
		var result = evaluateGuess(guess, targetWord);

		// Apply tile reveal animations
		for (var c = 0; c < 5; c++) {
			(function (colIndex, status) {
				setTimeout(function () {
					var $tile = $('.wdl-tile[data-r="' + currentRow + '"][data-c="' + colIndex + '"]');
					$tile.addClass('flip ' + status);

					var letter = guess[colIndex];
					// Update key states (correct > present > absent)
					if (!keyStates[letter] || keyStates[letter] === 'absent' || (keyStates[letter] === 'present' && status === 'correct')) {
						keyStates[letter] = status;
						$('.wdl-key[data-key="' + letter + '"]').removeClass('present absent').addClass(status);
					}
				}, colIndex * 250);
			})(c, result[c]);
		}

		var delayTotal = 5 * 250 + 200;

		setTimeout(function () {
			if (guess === targetWord) {
				isWon = true;
				isGameOver = true;
				clearInterval(timerInterval);
				showToast('Lieliski! Uzminēji vārdu ' + (currentRow + 1) + '. mēģinājumā! 🎉');
				submitScore(currentRow + 1);
			} else if (currentRow === 5) {
				isGameOver = true;
				clearInterval(timerInterval);
				showToast('Spēle beigusies! Pareizais vārds bija: ' + targetWord);
			} else {
				currentRow++;
				currentTile = 0;
			}
		}, delayTotal);
	}

	function evaluateGuess(guess, target) {
		var res = ['absent', 'absent', 'absent', 'absent', 'absent'];
		var targetChars = target.split('');
		var guessChars = guess.split('');

		// First pass: Green (correct position)
		for (var i = 0; i < 5; i++) {
			if (guessChars[i] === targetChars[i]) {
				res[i] = 'correct';
				targetChars[i] = null; // Consume char
			}
		}

		// Second pass: Yellow (present in wrong position)
		for (var i = 0; i < 5; i++) {
			if (res[i] === 'correct') continue;
			var idx = targetChars.indexOf(guessChars[i]);
			if (idx !== -1) {
				res[i] = 'present';
				targetChars[idx] = null; // Consume char
			}
		}

		return res;
	}

	function shakeRow(rowIdx) {
		var $row = $('.wdl-row').eq(rowIdx);
		$row.addClass('shake');
		setTimeout(function () {
			$row.removeClass('shake');
		}, 600);
	}

	function showToast(msg) {
		var $toast = $('#wdl-toast');
		$toast.text(msg).addClass('show');
		setTimeout(function () {
			$toast.removeClass('show');
		}, 3000);
	}

	function startTimer() {
		if (timerInterval) return;
		timerInterval = setInterval(function () {
			timerSeconds++;
			updateTimerDisplay(timerSeconds);
		}, 1000);
	}

	function updateTimerDisplay(sec) {
		var mins = Math.floor(sec / 60);
		var s = sec % 60;
		$('#wdl-timer').text((mins < 10 ? '0' : '') + mins + ':' + (s < 10 ? '0' : '') + s);
	}

	function submitScore(guessesCount) {
		if (timerSeconds <= 0 || !sessionToken) return;

		$.post('/wordle/?action=push', {
			token: sessionToken,
			time_sec: timerSeconds,
			guesses: guessesCount,
			mode: currentMode
		}, function (res) {
			if (res && res.success) {
				// Score submitted successfully
			}
		}, 'json');
	}

	// Event Listeners
	$('.wdl-keyboard').on('click', '.wdl-key', function () {
		var key = $(this).attr('data-key');
		handleKeyPress(key);
	});

	$(document).on('keydown', function (e) {
		if ($('#wdl-grid').length === 0) return;

		var key = e.key;
		if (key === 'Enter') {
			handleKeyPress('ENTER');
		} else if (key === 'Backspace' || key === 'Delete') {
			handleKeyPress('BACKSPACE');
		} else if (/^[a-zA-ZāčēģīķļņšūžĀČĒĢĪĶĻŅŠŪŽ]$/i.test(key)) {
			handleKeyPress(key.toUpperCase());
		}
	});

	$('#wdl-btn-daily').on('click', function () {
		currentMode = 'daily';
		$('.wdl-mode-btn').removeClass('active');
		$(this).addClass('active');
		startNewGame();
	});

	$('#wdl-btn-practice').on('click', function () {
		currentMode = 'practice';
		$('.wdl-mode-btn').removeClass('active');
		$(this).addClass('active');
		startNewGame();
	});

	$('#wdl-btn-new').on('click', function () {
		startNewGame();
	});
});
