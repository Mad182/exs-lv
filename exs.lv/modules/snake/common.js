/*
 * EXS.LV Snake Game Initializer
 */

$(function() {
	Snake.setup();

	$(document).on('click', 'a#start-game, button#start-game', function(e) {
		e.preventDefault();
		Snake.newGame(true);
	});

	$(window).on('beforeunload unload', function() {
		if (typeof Snake !== 'undefined' && Snake.score > 0) {
			Snake.submitScore();
		}
	});
});
