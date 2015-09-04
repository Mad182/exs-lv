function prompt_why_delete(url) {
	var reply = prompt("Iemesls dzēšanai?", "")
	if (reply) {
		window.location = url + '&message=' + reply;
	} else {
		return false;
	}
}

function msgrr() {
	query_timeout = query_timeout + 2000;
	var query_string = '?query=true';
	if (current_user > 0) {
		query_string = query_string + '&loadpm=true';
	}
	if ($('.remember-pages').hasClass('active')) {
		query_string = query_string + '&loadposts=true';
	}
	if ($('.remember-events').hasClass('active')) {
		query_string = query_string + '&loadevents=true';
	}
	if ($('.remember-gallery').hasClass('active')) {
		query_string = query_string + '&loadgallery=true';
	}
	if ($('.default-minibog-tab').hasClass('selected')) {
		query_string = query_string + '&loadmb=true';
	}
	if ($('.remember-friends').hasClass('active')) {
		query_string = query_string + '&tab=friends';
	}
	if ($('.remember-music').hasClass('active')) {
		query_string = query_string + '&tab=music';
	}

	$.getJSON('/get/updates.json' + query_string, function(data) {
		$.each(data, function(key, val) {
			if (key == 'pm-count') {
				if (val > 0) {
					$('#new-msg').html('&nbsp;(<span class="r" style="display:inline">' + val + '</span>)');
					Tinycon.setBubble(val);
				} else {
					$('#new-msg').html('');
					Tinycon.setBubble(0);
				}
			}
			if (key == 'in-tabs') {
				$('#lat').html(val);
			}
			if (key == 'mb-latest') {
				$('#miniblog-block').html(val);
			}

		});
	});

	setTimeout('msgrr()', query_timeout);
}

var processing = false;

function update_mb() {
	clearInterval(mbRefreshId);
	mbRefreshId = '';
	if (processing == false) {
		processing = true;
		$.getJSON('/json_mb.php?mbid=' + mbid + '&lastid=' + lastid + '&url=' + encodeURIComponent(c_url) + '&type=' + mbtype + '&et=' + edit_time, function(data) {
			var items = [];
			$.each(data, function(key, val) {
				if (key == 'id' && val != lastid) {
					lastid = val;
				}
				if (key == 'et' && val != lastid) {
					edit_time = val;
				}
				if (key == 'comment') {
					refreshlim = mb_refresh_limit;
					$.each(val, function(ckey, cval) {
						$.each(cval, function(rkey, rval) {
							$('<li>' + rval + '</li>').hide().appendTo('ul.responses-' + ckey).fadeIn('slow');
						});
					});
					$('#miniblog-block').load('/?c=300&mbpage=0');
				}
				if (key == 'edits') {
					refreshlim = mb_refresh_limit;
					$.each(val, function(ckey, cval) {
						$('#m' + ckey).siblings('.response-content').children('.post-content').html(cval);
					});
				}
			});
		});
		processing = false;
	} else {
		refreshlim = mb_refresh_limit;
	}
	refreshlim = refreshlim + 1500;
	mbRefreshId = setInterval("update_mb()", refreshlim);
}

Tinycon.setOptions({
	width: 7,
	height: 9,
	font: '9px arial',
	colour: '#ffffff',
	background: '#ef6000',
	fallback: true
});

$(document).ready(function($) {

	$.ajaxSetup({
		cache: false
	});

	/* 	tiek izsaukta, nospiežot uz lietotāja nosūdzēšanas podziņas;
	 atver fancybox ar pārkāpuma aprakstīšanas formu */
	$('.report-user').on('click', function(e) {
		$.ajax({
			dataType: "json",
			url: $(this).attr('href') + '?_=1',
			success: function(data) {
				$.fancybox(data.content);
			}
		});
		e.preventDefault();
	});

	/*	izmanto moderatoru sadaļā, lai apskatītu iesniegtās sūdzības saturu */
	$('.get-report-content').on('click', function(e) {
		$.getJSON(($(this).attr('href') + '?_=1'), function(response) {
			$.fancybox(response.message);
		});
		e.preventDefault();
	});

	/* 	izmanto sūdzību iesūtīšanai; tiek izsaukta,
	 nospiežot submit pogu fancybox logā */
	$('#report-form').on('submit', function() {
		$.ajax({
			type: "POST",
			dataType: "json",
			url: $('#report-form').attr('action') + '?_=1',
			data: $('#report-form').serialize(),
			success: function(response) {

				$('.report-response').html("");

				if (response.state == 'success') {
					$('#outer-form-block').toggle('slow');
					$('.report-response').attr('class', 'report-response-good').html(response.content);
				}
				else {
					$('.report-response').html(response.content);
				}
			}
		});
		return false;
	});

	/* aizver atvērto fancybox, nospiežot uz "Pārdomāju" podziņas */
	$('.fancy-close').on('click', function() {
		$.fancybox.close();
		return false;
	});

	/* sūdzību arhivēšanas poga */
	$('.report-archive').on('click', function(e) {
		var element = $(this);
		$.getJSON($(this).attr('href') + '?_=1', function(response) {
			if (response.state == 'success') {
				$(element).attr('href', '#').text(response.text);
				$(element).removeClass('primary').removeClass('report-archive').addClass('danger');
			}
		});
		e.preventDefault();
	});

	/* ļauj apskatīt dzēsto miniblogu ierakstu bijušo saturu */
	$('.deleted-content').on('click', function(e) {
		$.get(($(this).attr('href')), function(response) {
			$.fancybox(response);
		});
		e.preventDefault();
	});

	/* paslēpj/parāda iesniegto sūdzību paziņojumu pilno saturu */
	$('.report-full').on('click', function(e) {
		$content = $(this).siblings('.report-full-content').html();
		$(this).parent().html($content);
		e.preventDefault();
	});

	/* dzēš minibloga ierakstu bez lapas pārlādes */
	$('.delete-fast').on('click', function(e) {
		if (confirm("Vai tiešām vēlies veikt šo darbību?")) {
			var link_element = $(this);
			var content_element = $(this).parent().siblings('.post-content');
			$.getJSON($(this).attr('href') + '&_=1', function(response) {
				if (response.state == 'success') {
					$(link_element).parent().after(response.message).show('slow');
					$(link_element).siblings('.post-button:not(.comment-permalink)').andSelf().hide();
					$(content_element).hide();
				} else {
					alert('Darbība neizdevās!');
				}
			});
		}
		e.preventDefault();
	});

	/* podziņa ātrai scrollošanai uz augšu */
	var $elem = $('#scroll-up');
	var is_visible = false;
	$(window).scroll(function() {
		if ($(this).scrollTop() > 200 && !is_visible) {
			$elem.stop().animate({bottom: '40px', opacity: 0.6}, 500);
			is_visible = true;
		}
		else if ($(this).scrollTop() <= 200 && is_visible) {
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

	if (current_user > 0 && new_msg_count > 0) {
		Tinycon.setBubble(new_msg_count);
	}

	$('.confirm').on('click', function() {
		return confirm("Vai tiešām vēlies veikt šo darbību?");
	});

	$('#new-tags').on('submit', function(e) {
		$.ajax({
			type: "POST",
			url: $(this).attr('action'),
			data: $(this).serialize(),
			success: function(data) {
				if ($('#article-tags').length) {
					$('#article-tags').append(data);
				} else {
					$('#post-tags-wrapper').html('<ul id="article-tags" class="list-tags">' + data + '</ul>').fadeIn('slow');
				}
				$('#post-tags-input').val("");
			}
		});
		e.preventDefault();
	});

	$('#new-tags-mb').on('submit', function(e) {
		$.ajax({
			type: "POST",
			url: $(this).attr('action'),
			data: $(this).serialize(),
			success: function(data) {
				if ($('#mb-tags').length) {
					$('#mb-tags').append(data);
				} else {
					$('#mb-tags-wrapper').html('<ul id="mb-tags" class="list-tags">' + data + '</ul>').fadeIn('slow');
				}
				$('#post-tags-input').val("");
			}
		});
		e.preventDefault();
	});

	//lapu slēgšana
	$('.ajax-checkbox').on('change', function(e) {
		$.ajax({
			type: "POST",
			url: $(this).parent().parent().parent().attr('action'),
			data: $(this).parent().parent().parent().serialize()
		});
		e.preventDefault();
	});
	
	//spēļu servera monitora pārlādēšana
	$('.gameserver-reload').on('click', function(e) {
		e.preventDefault();

		$(this).parent().parent().parent().fadeTo(250, 0.7);
		$(this).parent().parent().parent().load($(this).attr('href'), function() {
			$(this).fadeTo(150, 1);
		});
		
	});

	//mb komentara forma
	$('body').on('submit', '#addresponse', function(e) {
		clearInterval(mbRefreshId);
		mbRefreshId = '';
		$('#mbresponse-submit').hide();
		$('#mbresponse-waiting').show();
		$.ajax({
			type: "POST",
			url: $(this).attr('action') + '?postcomment=true',
			data: $(this).serialize(),
			success: function(data) {
				$('#responseminiblog').val("");
				update_mb();
				$('#mbresponse-waiting').hide();
				$('#mbresponse-submit').show();
				$('.mb-reply-main').click();
			}
		});
		refreshlim = mb_refresh_limit;
		mbRefreshId = setInterval("update_mb()", refreshlim);
		e.preventDefault();
	});

	//atbildet uz minibloga komentaru
	$('body').on('click', '.mb-reply-to', function(e) {
		e.preventDefault();
		if ($(this).siblings('.reply-ph').html() != '') {
			return false;
		}
		$('.reply-ph').fadeOut(0);
		$(this).siblings('.reply-ph').html($('.reply-ph-current').html());
		$('.reply-ph-current').html('').removeClass('reply-ph-current');
		$(this).siblings('.reply-ph').addClass('reply-ph-current').fadeIn('fast');
		$('#response-to').val($(this).attr('href'));
	});

	//atpakaļ uz atbildēšanu galvenajam mb
	$('.mb-reply-main').on('click', function(e) {
		e.preventDefault();
		if ($('.reply-ph-default').html() != '') {
			return false;
		}
		$('.reply-ph').fadeOut(0);
		$('.reply-ph-default').html($('.reply-ph-current').html());
		$('.reply-ph-current').html('').removeClass('reply-ph-current');
		$('.reply-ph-default').addClass('reply-ph-current').fadeIn('fast');
		$('#response-to').val(mbid);
	});

	$('#random-fact .moar').on('click', function(e) {
		e.preventDefault();
		$('#random-fact').fadeTo(250, 0.5);
		$('#random-fact').load($(this).attr('href'), function() {
			$(this).fadeTo(150, 1);
		});
	});

	$('#addpic').on('click', function(e) {
		e.preventDefault();
		$('#newpic').toggle(200);
	});

	$('.spoiler-title').on('click', function(e) {
		e.preventDefault();
		$(this).siblings('.spoiler-content').toggle(200);
	});

	$('#debug-details-trigger').on('click', function(e) {
		e.preventDefault();
		$('#debug-details').toggle(200);
	});

	$(".rpl").fancybox({
		'overlayShow': true,
		'transitionIn': 'none',
		'transitionOut': 'none'
	});

	$('.lightbox').fancybox({
		'titleShow': false
	});

	$('.c-rate').on('click', '.plus, .minus', function(e) {
		e.preventDefault();

		var elem = $(this).parent();
		elem.fadeTo(250, 0.5);
		elem.load($(this).attr('href'), function() {
			elem.fadeTo(150, 1);
		});
	});

	$('body').on('click', '.ajax-pager a', function(e) {
		e.preventDefault();

		var elem = $(this).parent().parent();
		elem.fadeTo(250, 0.5);
		elem.load($(this).attr('href'), function() {
			elem.fadeTo(150, 1);
		});
	});

	$('.jaunk-queue-ajax').on('click', function(e) {
		e.preventDefault();

		var elem = $(this).parent().parent();
		elem.fadeTo(250, 0.3);
		elem.load($(this).attr('href'), function() {
			elem.fadeTo(250, 0.3);
		});
	});

	$('.mb-rater, .mb-icon').hover(
					function() {
						$(this).fadeTo(150, 1)
					},
					function() {
						$(this).fadeTo(150, 0.45)
					}
	);

	$('.mb-rater, .mb-icon').fadeTo(150, 0.45);

	$('#ucl li a').hover(
					function() {
						$('#ucd').html($(this).html())
					},
					function() {
						$('#ucd').html('')
					}
	);

	/* pārbauda vai lietotājs eksistē, piemēram pie reģistrācijas vai nika maiņas */
	$('.usercheck').on('change', function() {
		$('.usercheck-response').load('/userexists/?user=' + encodeURIComponent($(this).val()));
	});

	/* flash message (brīdinājumu un paziņojumu) aizvēršana */
	$('#close-flash-message').on('click', function(e) {
		$('#flash-message').fadeOut(500);
		e.preventDefault();
	});

	$('.tabs li a.ajax, #tabnav li a.ajax').on('click', function(e) {
		e.preventDefault();

		var clicked = $(this);

		var tabs = {
			'last-sidebar-tab': {tab1: 'events', tab2: 'pages', tab3: 'gallery'},
			'last-facts-tab': {tab1: 'fact-all', tab2: 'fact-rs'},
			'last-mbs-tab': {tab1: 'all', tab2: 'friends', tab3: 'music'},
			'last-rsnews-tab' : {tab1: 'runescape', tab2: 'oldschool'}
		};

		$.each(tabs, function(position, values) {
			$.each(values, function(key, tab) {
				if (clicked.hasClass('remember-' + tab)) {
					$.cookie(position, tab, {
						expires: 7,
						path: '/'
					});
				}
			});
		});

		var elem = clicked.parent().parent().siblings('.ajaxbox');
		elem.fadeTo(250, 0.6);
		elem.load(clicked.attr('href'), function() {
			elem.fadeTo(150, 1);
		});
		clicked.parent().siblings().children('a').removeClass('active');
		clicked.addClass('active');
	});

	$('.movie-liker a').on('click', function(e) {
		e.preventDefault();

		var elem = $(this).parent();
		elem.fadeTo(250, 0.3);
		elem.load($(this).attr('href'), function() {
			elem.fadeTo(250, 0.3);
		});
	});

	/* vertesana */
	$('#star').raty({
		half: true,
		size: 24,
		start: $('.current-rating').html(),
		path: '/bildes/raty/',
		starHalf: 'star-half-big.png',
		starOff: 'star-off-big.png',
		starOn: 'star-on-big.png',
		targetType: 'number',
		click: function(score, evt) {
			$.ajax({
				type: "POST",
				url: c_url,
				data: {
					vote: score
				},
				success: function(data) {
					$('#post-rating').html(data).fadeIn("slow");
				}
			});
		}
	});

	/* pm vesture */
	$('#reply-history').on('click', function(e) {
		e.preventDefault();

		$('#pm-history-container').load($(this).attr('href'));
		$('#reply-history').addClass('disabled');
	});


	/* junk vote */
	$('#junk-vote-wrap a').on('click', function(e) {
		e.preventDefault();

		$('#junk-vote-wrap').load($(this).attr('href'));
	});


	/* auto piemeklētie filmu avatari */
	$('.imgselect').on('click', function(e) {
		e.preventDefault();

		$('.imgselect').removeClass('clicked');
		$(this).addClass('clicked');
		$('#avatar-url').val($(this).attr('href'));
	});


	/* wp admin */
	$('[data-addwp-action="load-external"]').on('click', function(e) {
		e.preventDefault();

		var target = $(this).data('target');
		var resource = $(this).data('resource');

		$.getJSON(resource, function(data) {
			$(data).each(function(i, entity) {
				$(target).append('<li><a href="' + entity['file'] + '" class="lightbox-added"><img src="' + entity['thumb'] + '"></a><br><input type="radio" name="new-image-id" value="' + i + '"></li>')
			});

			$('.lightbox-added').fancybox({
				'titleShow': false
			});
		});

		$(target).show();
		$('#new-image').remove();
		$(this).off('click').remove();
	});
	
	/* universāla funkcija satura parādīšanai iekš fancybox */
	function open_fancy(content) {
		var addr = $(content).attr('href');        
		$.get(addr + '?_=1' , function(data) {
			$.fancybox(data);
		});
	}
	
	/* profili - piesaistīto profilu saraksta atvēršana */
	$('#profile-list').on('click', '.show-children', function(e) {  
		$(this).parent().parent().next().toggle();    
		e.preventDefault();
	});    
	/* profili - profila piesaistīšana */
	$('#profile-list').on('click', '.connect-profile', function(e) {
		open_fancy($(this));
		e.preventDefault();
	});    
	/* profili - profilu grupas dzēšana */
	$('#profile-list').on('click', '.delete-group', function(e) {    
		open_fancy($(this));      
		e.preventDefault();
	});
	/* profili - apraksta rediģēšana */
	$('#profile-list').on('click', '.edit-description', function(e) {    
		open_fancy($(this));      
		e.preventDefault();
	});
	
	/* checkbox pie lietotāja bloķēšanas iespējām */
	$('.check-all').click(function() {
		if ($(this).prop('checked')) {
			$('.js-checkbox').attr('checked', 'checked');
		} else {
			$('.js-checkbox').removeAttr('checked');
		}
	});
	$('.js-checkbox').click(function(){ 
		if($('.js-checkbox').length == $('.js-checkbox:checked').length) {
			$('.check-all').attr('checked', 'checked');
		} else {
			$('.check-all').removeAttr('checked');
		} 
	});
	
	/* grupu toggle bloķēto profilu lapā */
	$('.table-banned').on('click', '.show-banned', function() {
		$('.child-of-' + $(this).attr('data-id')).toggle();
	});
	
	/* paslēptie miniblogu posti */
	$('.toggle-replies').click(function(e){   
		e.preventDefault();
		$(this).hide();
		$(this).siblings('.more-replies').show();
	});

	setTimeout('msgrr()', query_timeout);
	
	

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

});

