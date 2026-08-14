/**
 * EXS.LV Memory Game Engine
 */

$(function() {
	var Memory = {
		grid: '4x4',
		token: null,
		cards: [],
		firstCard: null,
		secondCard: null,
		lockBoard: false,
		moves: 0,
		matchedPairs: 0,
		totalPairs: 8,
		startTime: 0,
		timerInterval: null,
		duration: 0,

		init: function() {
			$('#btn-start').on('click', function() {
				Memory.startNewGame();
			});

			$('#btn-restart').on('click', function() {
				Memory.startNewGame();
			});

			$('#grid-select').on('change', function() {
				Memory.grid = $(this).val();
				Memory.startNewGame();
			});
		},

		startNewGame: function() {
			clearInterval(Memory.timerInterval);
			Memory.duration = 0;
			Memory.moves = 0;
			Memory.matchedPairs = 0;
			Memory.firstCard = null;
			Memory.secondCard = null;
			Memory.lockBoard = true;
			Memory.token = null;

			Memory.grid = $('#grid-select').val() || '4x4';

			$('#stat-moves').text(0);
			$('#stat-pairs').text(0);
			$('#stat-score').text(0);
			$('#stat-time').text('0s');

			var $board = $('#memory-board');
			$board.removeClass('grid-4x4 grid-6x4 grid-6x6').addClass('grid-' + Memory.grid);
			$board.find('.memory-card').remove();
			$('#memory-overlay').fadeIn(300);

			$.getJSON('/memory?action=init_token', { grid: Memory.grid }, function(res) {
				if (!res || !res.success) {
					alert('Kļūda ielādējot spēli!');
					return;
				}

				Memory.token = res.token;
				Memory.totalPairs = res.pairs;
				Memory.cards = res.cards;

				$('#stat-totpairs').text(Memory.totalPairs);

				Memory.renderCards();
				$('#memory-overlay').fadeOut(300, function() {
					Memory.lockBoard = false;
					Memory.startTime = Math.floor(Date.now() / 1000);
					Memory.startTimer();
				});
			});
		},

		renderCards: function() {
			var $board = $('#memory-board');
			$board.find('.memory-card').remove();

			$.each(Memory.cards, function(idx, card) {
				var $card = $(
					'<div class="memory-card" data-pair="' + card.pair_id + '" data-idx="' + idx + '">' +
						'<div class="card-inner">' +
							'<div class="card-front">?</div>' +
							'<div class="card-back">' +
								'<img src="' + card.avatar + '" alt="' + Memory.escapeHtml(card.nick) + '" />' +
								'<span class="card-nick">' + Memory.escapeHtml(card.nick) + '</span>' +
							'</div>' +
						'</div>' +
					'</div>'
				);

				$card.on('click', Memory.onCardClick);
				$board.append($card);
			});
		},

		onCardClick: function() {
			if (Memory.lockBoard) return;
			var $this = $(this);
			if ($this.hasClass('flipped') || $this.hasClass('matched')) return;

			$this.addClass('flipped');

			if (!Memory.firstCard) {
				Memory.firstCard = $this;
				return;
			}

			Memory.secondCard = $this;
			Memory.lockBoard = true;
			Memory.moves++;
			$('#stat-moves').text(Memory.moves);

			Memory.checkMatch();
		},

		checkMatch: function() {
			var isMatch = Memory.firstCard.data('pair') === Memory.secondCard.data('pair');

			if (isMatch) {
				Memory.disableCards();
			} else {
				Memory.unflipCards();
			}
		},

		disableCards: function() {
			Memory.firstCard.addClass('matched');
			Memory.secondCard.addClass('matched');
			Memory.resetTurn();

			Memory.matchedPairs++;
			$('#stat-pairs').text(Memory.matchedPairs);

			if (Memory.matchedPairs === Memory.totalPairs) {
				Memory.onGameComplete();
			}
		},

		unflipCards: function() {
			setTimeout(function() {
				if (Memory.firstCard) Memory.firstCard.removeClass('flipped');
				if (Memory.secondCard) Memory.secondCard.removeClass('flipped');
				Memory.resetTurn();
			}, 700);
		},

		resetTurn: function() {
			Memory.firstCard = null;
			Memory.secondCard = null;
			Memory.lockBoard = false;
		},

		startTimer: function() {
			Memory.timerInterval = setInterval(function() {
				Memory.duration++;
				$('#stat-time').text(Memory.duration + 's');
			}, 1000);
		},

		onGameComplete: function() {
			clearInterval(Memory.timerInterval);
			Memory.lockBoard = true;

			if (!Memory.token) return;
			var tokenToSend = Memory.token;
			Memory.token = null;

			$.ajax({
				url: '/memory?action=push',
				type: 'POST',
				data: {
					token: tokenToSend,
					moves: Memory.moves,
					duration: Memory.duration
				},
				dataType: 'json',
				success: function(res) {
					if (res && res.success) {
						$('#stat-score').text(res.score);
						if (res.highScore !== undefined) {
							$('#stat-highscore').text(res.highScore);
						}
						$('#overlay-title').text('UZVARA!');
						$('#overlay-msg').html('Apsveicam! Visi pāri atrasti!<br>Tavs rezultāts: <strong>' + res.score + '</strong> punkti (' + Memory.moves + ' gājieni, ' + Memory.duration + 's).');
					} else {
						$('#overlay-title').text('MALACIS!');
						$('#overlay-msg').text(res && res.message ? res.message : 'Spēle pabeigta!');
					}
					$('#btn-start').text('Spēlēt vēlreiz');
					$('#memory-overlay').fadeIn(400);
				},
				error: function() {
					$('#overlay-title').text('SPĒLE BEIGUSIES');
					$('#overlay-msg').text('Rezultāts saglabāts.');
					$('#btn-start').text('Spēlēt vēlreiz');
					$('#memory-overlay').fadeIn(400);
				}
			});
		},

		escapeHtml: function(str) {
			return $('<div>').text(str || '').html();
		}
	};

	Memory.init();
});
