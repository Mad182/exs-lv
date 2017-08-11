$(document).ready(function($) {
	$('#responsive-menu-button').sidr({
		name: 'sidr-right',
		speed: 50,
		side: 'right',
		source: '#swipe-menu-responsive'
	});

	$('a.sidr-class-close-this-menu').click(function() {
		$('div.sidr').css({
			'right': '-476px'
		});
		$('body').css({
			'right': '0'
		});
	});
});

