/* desas */
var desasRefreshId;
function desas_message(text) {
	$('<div class="alert">' + text + '</div>').hide().appendTo("#desas").fadeIn('slow').delay(3500).fadeOut('slow');
}
function desas_overlay(text) {
	$('<div class="overlay">' + text + '</div>').hide().appendTo("#desas").fadeIn('slow').delay(4200).fadeOut('slow');
}
function desas_timeout() {
	$('<div class="overlay">Spēle pārtraukta, jo Tu 25 sekundes neveici gājienu!</div>').hide().appendTo("#desas").fadeIn('slow').delay(999999).fadeOut('slow');
}
function load_desas(url) {
	clearInterval(desasRefreshId);
	desasRefreshId = '';
	var timeout = false;
	$.getJSON(url, function(data) {
		$.each(data, function(key, val) {
			if (key == 'fields') {
				$('#desas').html('');
				$.each(val, function(line_key, line_val) {
					$.each(line_val, function(fkey, fval) {
						if (fval == data.me) {
							$('<div class="field mine">X</div>').appendTo("#desas");
						}
						if (fval == 0) {
							$('<div class="field"><a href="/desas_server/?mark=' + line_key + fkey + '">#</a></div>').appendTo("#desas");
						}
						if (fval != data.me && fval != 0) {
							$('<div class="field other">O</div>').appendTo("#desas");
						}
					});
				});
			}
		});
		$('#desas-info').show();
		$('#desas-opponent').html(data.opponent);
		$('#desas-my-win').html(data.mytotal.wins);
		$('#desas-my-lose').html(data.mytotal.loses);
		$('#desas-op-win').html(data.optotal.wins);
		$('#desas-op-lose').html(data.optotal.loses);
		if (data.alert != 0) {
			desas_message(data.alert);
		}
		if (data.overlay != 0) {
			desas_overlay(data.overlay);
		}
		if (data.other == 0) {
			desas_message('Meklē pretinieku');
		}
		if (data.timeout == 1) {
			timeout = true;
		}
	});
	if (timeout != true) {
		desasRefreshId = setInterval("load_desas('/desas_server?xmlhttprequest=true')", 1500);
	} else {
		desas_timeout();
	}
}

$(document).ready(function() {

	$('#desas a, #desas-drop').on('click', function() {
		load_desas($(this).attr('href'));
		return false;
	});

});
