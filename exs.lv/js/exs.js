$(document).ready(function($) {

	/*
	* Fix dropdown menu bootstrap error 
	* ------------------------------------------------- */

	$('.nav').find('li:has(ul)').addClass('dropdown');
	$('.dropdown > a').addClass('dropdown-toggle disabled');
	$('li.dropdown').children('ul.sub-menu').addClass('dropdown-menu');

	/*
	* Fix dropdown menu bootstrap error ends
	* --------------------------------------------------------- */	

	$('.dropdown .sub-menu').addClass('dropdown-menu');	


	$('.dropdown > a').append('<b class="caret"></b>').dropdown();
	$('.dropdown .sub-menu').addClass('dropdown-menu');


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

