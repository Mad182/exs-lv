/*
 * jquery.snake.js - a nibbles clone with EXS.LV anti-cheat integration
 * Copyright (c) 2008 Richard Willis
 * Refactored for EXS.LV 2026
 */

var Snake = {
	$map: {}, $cherry: {}, $overlay: {}, seg: {}, wallseg: {}, cache: {},
	cacheimages: [],
	animateTimer: 0, score: 0, grid: 0, level: 1, lives: 3, speed: 0, cherriesEaten: 0,
	wall: 0,
	currentToken: null,

	setup: function() {
		// build map
		Snake.$map = $("#map1");
		if (!Snake.$map.length) return;

		// Pre-fetch security token
		$.getJSON('/snake?action=init_token', function(res) {
			if (res && res.success) {
				Snake.currentToken = res.token;
			}
		});

		Snake.$map.width = Snake.$map.innerWidth();
		Snake.$map.height = Snake.$map.innerHeight();

		// build and prepend overlay to map
		Snake.$overlay = $('#overlay');
		if (!Snake.$overlay.length) {
			Snake.$overlay = $('<div id="overlay"></div>').hide().prependTo(Snake.$map);
		}

		// build and append cherry to map
		Snake.$cherry = $('#cherry');
		if (!Snake.$cherry.length) {
			Snake.$cherry = $('<div id="cherry"></div>').appendTo(Snake.$map);
		}

		// listen for key press
		Snake.cache.keyCode = [0, 0];
		document.onkeydown = function(event) {
			var keycode = (!!event && !!event.keyCode) ? event.keyCode : event.which;
			switch (keycode) {
				case 71 : // G key - toggle grid
					event.preventDefault();
					Snake.toggleGrid();
					break;
				case 80 : // P key - pause
					event.preventDefault();
					Snake.pause();
					break;
				case 78 : // N key - new game
					event.preventDefault();
					Snake.newGame(true);
					break;
				case 37 : // Left
				case 38 : // Up
				case 39 : // Right
				case 40 : // Down
					event.preventDefault();
					Snake.cache.keyCode[0] = Snake.cache.keyCode[1];
					Snake.cache.keyCode[1] = keycode;
					break;
				default :
					break;
			}
		};
	},
	start: function() {
		// set initial speed
		Snake.speed = Level[Snake.level][0].speed;
		// show the cherry, and start the animation
		Snake.$cherry.fadeIn(function() {
			Snake.animateTimer = setInterval(Snake.animate, Snake.speed);
		});
	},
	newGame: function(reset) {
		Snake.cherriesEaten = 0;

		// reset animation timer
		clearInterval(Snake.animateTimer);
		Snake.animateTimer = 0;

		if (reset) {
			Snake.currentToken = null;
			$.getJSON('/snake?action=init_token', function(res) {
				if (res && res.success) {
					Snake.currentToken = res.token;
				}
			});
		}

		// reset score
		Snake.score = reset ? 0 : Snake.score;
		$("#stats-score").html(Snake.score + "");

		// reset level
		Snake.level = reset ? 1 : Snake.level;
		$("#stats-level").html(Snake.level);

		// reset lives
		Snake.lives = reset ? 3 : Snake.lives;
		$("#stats-lives").html(Snake.lives);

		// reset level cherries eaten and total
		$("#stats-eaten").html(Snake.cherriesEaten + "");
		$("#stats-totcherries").html(Level[Snake.level][0].cherries);

		// remove any wall & snake segments
		$(".wall, .snake").remove();

		// hide the cherry
		Snake.$cherry.hide();

		// update map message
		$("#map-msg").hide().html('Līmenis ' + Snake.level + ' <br/>Apēd <strong>' + Level[Snake.level][0].cherries + '</strong> ķiršus<br/><small style="font-size:80%">Atlikušās dzīvības: <strong>' + Snake.lives + '</strong></small>'
						).fadeIn(500, function() {

			setTimeout(function() {
				// hide map message
				$("#map-msg").fadeOut(500, function() {
					// hide overlay
					Snake.$overlay.hide();

					// reset and generate wall
					Snake.wallseg = {};
					Snake.Wall.generate();

					// generate cherry
					Snake.Cherry.generate(false);

					// reset, remove and re-append snake segments to map
					Snake.seg = {length: Level[Snake.level][0].length};
					for (var i = 0; i < Snake.seg.length; i++) {
						Snake.seg[i] = $('<span class="snake ' + i + '"></span>').appendTo(Snake.$map);
						Snake.seg[i].top = Snake.seg[i].left = 0;
					}

					// start animation
					setTimeout(function() {
						// reset direction to right
						Snake.cache.keyCode[0] = 0;
						Snake.cache.keyCode[1] = 39;
						Snake.start();
					}, 800);
				});
			}, 1800);
		});
	},
	animate: function() {
		// adjust segment position list
		for (var i = 1; i < Snake.seg.length; i++) {
			Snake.seg[i].top = Snake.seg[(i == Snake.seg.length - 1 ? 0 : i + 1)].top;
			Snake.seg[i].left = Snake.seg[(i == Snake.seg.length - 1 ? 0 : i + 1)].left;
		}

		var keycode = Snake.cache.keyCode;
		if (
			keycode[0] == 37 && keycode[1] == 39 ||
			keycode[0] == 39 && keycode[1] == 37 ||
			keycode[0] == 38 && keycode[1] == 40 ||
			keycode[0] == 40 && keycode[1] == 38
		) {
			Snake.cache.keyCode[1] = Snake.cache.keyCode[0];
		}

		keycode = Snake.cache.keyCode[1];
		// adjust leading segment properties
		if (keycode == 39) { // right
			Snake.seg[0].left += 10;
			if (Snake.seg[0].left > Snake.$map.width - 10) {
				Snake.wall && Snake.gameOver();
				Snake.seg[0].left = 0;
			}
		} else if (keycode == 40) { // down
			Snake.seg[0].top += 10;
			if (Snake.seg[0].top > Snake.$map.height - 10) {
				Snake.wall && Snake.gameOver();
				Snake.seg[0].top = 0;
			}
		} else if (keycode == 38) { // up
			Snake.seg[0].top -= 10;
			if (Snake.seg[0].top < 0) {
				Snake.wall && Snake.gameOver();
				Snake.seg[0].top = Snake.$map.height - 10;
			}
		} else if (keycode == 37) { // left
			Snake.seg[0].left -= 10;
			if (Snake.seg[0].left < 0) {
				Snake.wall && Snake.gameOver();
				Snake.seg[0].left = Snake.$map.width - 10;
			}
		}

		// check if snake has eaten a cherry
		(Snake.seg[0].left == Snake.Cherry.left && Snake.seg[0].top == Snake.Cherry.top) &&
			Snake.advance();

		var seg = {};
		for (var i = 1; i < Snake.seg.length; i++) {
			seg[i - 1] = Snake.seg[i];
		}

		// check if snake has slithered into itself
		(Snake.in_obj(Snake.seg[0], seg)) &&
			Snake.gameOver();

		// check if snake has slithered into a wall obstacle
		(Snake.in_obj(Snake.seg[0], Snake.wallseg)) &&
			Snake.gameOver();

		// check if cherries eaten match total for level
		(Snake.cherriesEaten == Level[Snake.level][0].cherries) &&
			Snake.advanceLevel();

		// reposition snake segments on map
		for (var i = 0; i < Snake.seg.length; i++) {
			Snake.seg[i].css({top: Snake.seg[i].top + "px", left: Snake.seg[i].left + "px", display: "block"});
		}
	},
	advance: function() {
		// increase snake segments
		Snake.seg.length++;

		var x = Snake.seg.length - 1;
		Snake.seg[x] =
			$('<span class="snake ' + x + '"></span>')
			.css({left: Snake.seg[1].left + "px", top: Snake.seg[1].top + "px", display: "block"})
			.appendTo(Snake.$map);

		// position new snake segment
		Snake.seg[x].top = Snake.seg[x - 1].top;
		Snake.seg[x].left = Snake.seg[x - 1].left;

		// reposition cherry
		Snake.Cherry.generate();

		// adjust score
		Snake.score += 10;
		$("#stats-score").html(Snake.score);

		// update cherries eaten
		Snake.cherriesEaten++;
		$("#stats-eaten").html(Snake.cherriesEaten);

		// adjust speed
		Snake.speed = Math.max(30, Snake.speed - 1);

		clearInterval(Snake.animateTimer);
		Snake.animateTimer = setInterval(Snake.animate, Snake.speed);
		return false;
	},
	advanceLevel: function() {
		if (Snake.level == Level.length - 1) {
			Snake.finishedGame();
		} else {
			Snake.level++;
			Snake.speed = Level[Snake.level][0].speed;
			Snake.newGame(false);
		}
	},
	toggleGrid: function() {
		var background;
		if (!Snake.grid) {
			background = "transparent url(/modules/snake/images/grid_bg.gif)";
			Snake.grid = 1;
		} else {
			background = "transparent";
			Snake.grid = 0;
		}
		Snake.$map.css({background: background});
	},
	pause: function() {
		if (Snake.animateTimer == 0) {
			Snake.start();
			Snake.$overlay.hide();
			$("#map-msg").fadeOut();
		} else {
			clearInterval(Snake.animateTimer);
			Snake.animateTimer = 0;
			Snake.$overlay.show();
			$("#map-msg").html("Pauze").fadeIn();
		}
	},
	gameOver: function() {
		if (Snake.lives > 1) {
			Snake.lives--;
			Snake.newGame(false);
		} else {
			Snake.lives = 0;
			$("#stats-lives").html(0);
			clearInterval(Snake.animateTimer);
			Snake.animateTimer = 0;
			Snake.$overlay.show();
			Snake.submitScore();
			$("#map-msg").html('Spēle beigusies!<br/><small><a href="javascript:;" onclick="Snake.newGame(true)" class="btn btn-primary btn-snake-start" style="margin-top:10px;">Spēlēt vēlreiz</a></small>').fadeIn();
		}
	},
	finishedGame: function() {
		clearInterval(Snake.animateTimer);
		Snake.animateTimer = 0;
		Snake.$overlay.show();
		Snake.submitScore();
		$("#map-msg").html('Apsveicam! Tu pabeidzi spēli!<br/><small><a href="javascript:;" onclick="Snake.newGame(true)" class="btn btn-primary btn-snake-start" style="margin-top:10px;">Spēlēt vēlreiz</a></small>').fadeIn();
	},
	submitScore: function() {
		if (Snake.score <= 0) return;
		var tokenToSend = Snake.currentToken || '';
		Snake.currentToken = null;

		$.ajax({
			url: '/snake?action=push',
			type: 'POST',
			data: {
				token: tokenToSend,
				score: Snake.score,
				cherries: Snake.cherriesEaten,
				level: Snake.level
			},
			dataType: 'json',
			success: function(res) {
				if (res && res.success) {
					if (res.highScore !== undefined) {
						$('#stat-highscore').text(res.highScore);
					}
				}
			}
		});
	},
	Cherry: {
		left: 0,
		top: 0,
		generate: function(show) {
			do {
				Snake.Cherry.left = Math.round((Math.random() * (Snake.$map.width - 10)) / 10) * 10;
				Snake.Cherry.top = Math.round((Math.random() * (Snake.$map.height - 10)) / 10) * 10;
			} while (Snake.in_obj(Snake.Cherry, Snake.wallseg) || Snake.in_obj(Snake.Cherry, Snake.seg));

			Snake.$cherry.css({
				left: Snake.Cherry.left + "px",
				top: Snake.Cherry.top + "px"
			});
			show == undefined && Snake.$cherry.hide().fadeIn();
		}
	},
	Wall: {
		generate: function() {
			var walls = Level[Snake.level],
				c = 0, t, l, i, n;

			for (i = 1; i < walls.length; i++) {
				t = walls[i].top;
				l = walls[i].left;
				for (n = 0; n < walls[i].seg; n++) {
					Snake.wallseg[c] = $('<span class="wall ' + c + '"></span>').css({top: t + "px", left: l + "px"}).appendTo(Snake.$map);
					Snake.wallseg[c].left = l;
					Snake.wallseg[c].top = t;
					c++;
					t += 10;
				}
			}
		}
	},
	in_obj: function(obj_needle, obj_haystack) {
		for (var i in obj_haystack) {
			if (obj_haystack[i].left === obj_needle.left && obj_haystack[i].top === obj_needle.top) {
				return true;
			}
		}
		return false;
	}
};

var Level = [
	,
	[
		{cherries: 5, length: 10, speed: 100},
		{seg: 30, top: 50, left: 200}
	],
	[
		{cherries: 6, length: 15, speed: 95},
		{seg: 30, top: 50, left: 100},
		{seg: 30, top: 50, left: 300}
	],
	[
		{cherries: 7, length: 20, speed: 90},
		{seg: 12, top: 10, left: 50},
		{seg: 12, top: 0, left: 350},
		{seg: 12, top: 270, left: 350},
		{seg: 12, top: 280, left: 50},
		{seg: 24, top: 80, left: 200}
	],
	[
		{cherries: 8, length: 25, speed: 85},
		{seg: 19, top: 0, left: 200},
		{seg: 20, top: 200, left: 200},
		{seg: 40, top: 0, left: 0},
		{seg: 40, top: 0, left: 390}
	],
	[
		{cherries: 9, length: 30, speed: 80},
		{seg: 20, top: 10, left: 50},
		{seg: 20, top: 0, left: 150},
		{seg: 20, top: 10, left: 250},
		{seg: 20, top: 0, left: 350},
		{seg: 20, top: 200, left: 20},
		{seg: 20, top: 200, left: 120},
		{seg: 20, top: 200, left: 220},
		{seg: 20, top: 200, left: 320}
	],
	[
		{cherries: 10, length: 39, speed: 75},
		{seg: 2, top: 10, left: 200},
		{seg: 2, top: 40, left: 200},
		{seg: 2, top: 70, left: 200},
		{seg: 2, top: 100, left: 200},
		{seg: 2, top: 130, left: 200},
		{seg: 2, top: 160, left: 200},
		{seg: 2, top: 190, left: 200},
		{seg: 2, top: 220, left: 200},
		{seg: 2, top: 250, left: 200},
		{seg: 2, top: 280, left: 200},
		{seg: 2, top: 310, left: 200},
		{seg: 2, top: 340, left: 200},
		{seg: 2, top: 370, left: 200},
		{seg: 20, top: 100, left: 280},
		{seg: 20, top: 100, left: 120},
		{seg: 30, top: 50, left: 60},
		{seg: 30, top: 50, left: 340},
		{seg: 40, top: 0, left: 0},
		{seg: 40, top: 0, left: 390}
	]
];
