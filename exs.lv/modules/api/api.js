$(document).ready(function() {
    /* podziņa ātrai scrollošanai uz augšu */
	var $elem = $('#scroll-up');
	var is_visible = false;
	$(window).scroll(function() {
		if ($(this).scrollTop() > 200 && !is_visible) {
			$elem.stop().animate({bottom: '40px', opacity: 1}, 500);
			is_visible = true;
		} else if ($(this).scrollTop() <= 200 && is_visible) {
			$elem.stop().animate({bottom: '200px', opacity: 0}, 200, function() {
				$(this).css({bottom: '-100px'});
			});
			is_visible = false;
		}
	});
	$elem.click(function() {
		$('html, body').stop().animate({scrollTop: 0}, 500, function() {
			$elem.stop().animate({bottom: '500px', opacity: 0}, 200, function() {
				$(this).css({bottom: '-100px'});
			});
		});
	});
});
