function prompt_why_delete(url) {
	var reply = prompt("Iemesls dzēšanai?", "")
	if (reply) {
		window.location = url + '&message=' + reply;
	} else {
		return false;
	}
}

function autoSaveDraft() {
	$.ajax({
		type: "POST",
		url: "/write/autosave?_=1",
		data: $("#new-article-approve").serialize() + '&content=' + tinyMCE.activeEditor.getContent(),
		success: function (data) {
			$('#autosave-status').hide().html(data).fadeIn("slow");
		}
	});
	return false;
}

function msgrr() {
	query_timeout = query_timeout + 2000;
	var query_string = '?query=true';
	if (current_user > 0) {
		query_string = query_string + '&loadpm=true';
	}
	if ($('.remember-mc').hasClass('active')) {
		query_string = query_string + '&loadmc=true';
	}
	if ($('.remember-mta').hasClass('active')) {
		query_string = query_string + '&loadmta=true';
	}
	if ($('.remember-cs').hasClass('active')) {
		query_string = query_string + '&loadcs=true';
	}
	if ($('.remember-pages').hasClass('active')) {
		query_string = query_string + '&loadposts=true';
	}
	if ($('.remember-gallery').hasClass('active')) {
		query_string = query_string + '&loadgallery=true';
	}
	if ($('.default-minibog-tab').hasClass('selected')) {
		query_string = query_string + '&loadmb=true';
	}
	if ($('#last-action-list').length > 0) {
		query_string = query_string + '&loadindex=true';
	}
	if ($('.mbs-friends').hasClass('active')) {
		query_string = query_string + '&friendmb=true';
	}

	$.getJSON('/get/updates.json' + query_string, function (data) {
		$.each(data, function (key, val) {
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
			if (key == 'cs-content') {
				$('#cs-content').html(val);
			}
			if (key == 'mta-content') {
				$('#mta-content').html(val);
			}
			if (key == 'mc-content') {
				$('#mc-content').html(val);
			}
			if (key == 'index-events') {
				$('#last-action-list').html(val);
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
		$.getJSON('/json_mb.php?mbid=' + mbid + '&lastid=' + lastid + '&url=' + encodeURIComponent(c_url) + '&type=' + mbtype + '&et=' + edit_time, function (data) {
			var items = [];
			$.each(data, function (key, val) {
				if (key == 'id' && val != lastid) {
					lastid = val;
				}
				if (key == 'et' && val != lastid) {
					edit_time = val;
				}
				if (key == 'comment') {
					refreshlim = mb_refresh_limit;
					$.each(val, function (ckey, cval) {
						$.each(cval, function (rkey, rval) {
							$('<li>' + rval + '</li>').hide().appendTo('ul.responses-' + ckey).fadeIn('slow');
						});
					});
					$('#miniblog-block').load('/?c=300&mbpage=0');
				}
				if (key == 'edits') {
					refreshlim = mb_refresh_limit;
					$.each(val, function (ckey, cval) {
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

$(document).ready(function () {

	$.ajaxSetup({
		cache: false
	});

	if (current_user > 0 && new_msg_count > 0) {
		Tinycon.setBubble(new_msg_count);
	}

	$('.confirm').live('click', function () {
		return confirm("Vai tiešām vēlies veikt šo darbību?");
	});

	$('#new-tags').live('submit', function (e) {
		$.ajax({
			type: "POST",
			url: $(this).attr('action'),
			data: $(this).serialize(),
			success: function (data) {
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

	$('#new-tags-mb').live('submit', function (e) {
		$.ajax({
			type: "POST",
			url: $(this).attr('action'),
			data: $(this).serialize(),
			success: function (data) {
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
	$('.ajax-checkbox').live('change', function (e) {
		$.ajax({
			type: "POST",
			url: $(this).parent().parent().parent().attr('action'),
			data: $(this).parent().parent().parent().serialize()
		});
		e.preventDefault();
	});

	//mb komentara forma
	$('#addresponse').live('submit', function (e) {
		clearInterval(mbRefreshId);
		mbRefreshId = '';
		$('#mbresponse-submit').hide();
		$('#mbresponse-waiting').show();
		$.ajax({
			type: "POST",
			url: $(this).attr('action') + '?postcomment=true',
			data: $(this).serialize(),
			success: function (data) {
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
	$('.mb-reply-to').live('click', function (e) {
		if ($(this).siblings('.reply-ph').html() != '') {
			return false;
		}
		$('.reply-ph').fadeOut(0);
		$(this).siblings('.reply-ph').html($('.reply-ph-current').html());
		$('.reply-ph-current').html('').removeClass('reply-ph-current');
		$(this).siblings('.reply-ph').addClass('reply-ph-current').fadeIn('fast');
		$('#response-to').val($(this).attr('href'));
		e.preventDefault();
	});

	//atpakaļ uz atbildēšanu galvenajam mb
	$('.mb-reply-main').live('click', function (e) {
		if ($('.reply-ph-default').html() != '') {
			return false;
		}
		$('.reply-ph').fadeOut(0);
		$('.reply-ph-default').html($('.reply-ph-current').html());
		$('.reply-ph-current').html('').removeClass('reply-ph-current');
		$('.reply-ph-default').addClass('reply-ph-current').fadeIn('fast');
		$('#response-to').val(mbid);
		e.preventDefault();
	});

	$('#random-fact .moar').live('click', function (e) {
		$('#random-fact').fadeTo(250, 0.5);
		$('#random-fact').load($(this).attr('href'), function () {
			$(this).fadeTo(150, 1);
		});
		e.preventDefault();
	});

	$('#addpic').live('click', function (e) {
		$('#newpic').toggle(200);
		e.preventDefault();
	});

	$('.spoiler-title').live('click', function (e) {
		$(this).siblings('.spoiler-content').toggle(200);
		e.preventDefault();
	});

	$('#debug-details-trigger').live('click', function (e) {
		$('#debug-details').toggle(200);
		e.preventDefault();
	});

	$(".rpl").fancybox({
		'overlayShow': true,
		'transitionIn': 'none',
		'transitionOut': 'none'
	});

	$('.lightbox').fancybox({
		'titleShow': false
	});

	$('.plus, .minus').live('click', function (e) {
		var elem = $(this).parent();
		elem.fadeTo(250, 0.5);
		elem.load($(this).attr('href'), function () {
			elem.fadeTo(150, 1);
		});
		e.preventDefault();
	});

	$('.ajax-pager a').live('click', function (e) {
		var elem = $(this).parent().parent();
		elem.fadeTo(250, 0.5);
		elem.load($(this).attr('href'), function () {
			elem.fadeTo(150, 1);
		});
		e.preventDefault();
	});

	$('.jaunk-queue-ajax').live('click', function (e) {
		var elem = $(this).parent().parent();
		elem.fadeTo(250, 0.3);
		elem.load($(this).attr('href'), function () {
			elem.fadeTo(250, 0.3);
		});
		e.preventDefault();
	});



	$('.mb-rater, .mb-icon').hover(
		function () {
			$(this).fadeTo(150, 1)
		},
		function () {
			$(this).fadeTo(150, 0.45)
		}
	);

	$('.mb-rater, .mb-icon').fadeTo(150, 0.45);

	$('#ucl li a').hover(
		function () {
			$('#ucd').html($(this).html())
		},
		function () {
			$('#ucd').html('')
		}
	);

	$('.usercheck').live('change', function () {
		$('.usercheck-response').load('/userexists/?user=' + encodeURIComponent($(this).val()));
	});

	$('#close-flash-message').live('click', function (e) {
		$('#flash-message').fadeOut(500);
		e.preventDefault();
	});

	$('.tabs li a.ajax').live('click', function (e) {

		var clicked = $(this);

		var tabs = {
		    'last-sidebar-tab' : {tab1: 'pages', tab2: 'gallery'},
		    'last-facts-tab' : {tab1: 'fact-all', tab2: 'fact-rs'},
		    'last-mbs-tab' : {tab1: 'all', tab2: 'friends'}
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
		elem.load(clicked.attr('href'), function () {
			elem.fadeTo(150, 1);
		});
		clicked.parent().siblings().children('a').removeClass('active');
		clicked.addClass('active');
		e.preventDefault();
	});

	$('.smiley-list a').live('click', function (e) {
		tinyMCE.execCommand('mceInsertContent', false, ' ' + $(this).attr('href'));
		e.preventDefault();
	});

	$('.movie-liker a').live('click', function (e) {
		var elem = $(this).parent();
		elem.fadeTo(250, 0.3);
		elem.load($(this).attr('href'), function () {
			elem.fadeTo(250, 0.3);
		});
		e.preventDefault();
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
		click: function (score, evt) {
			$.ajax({
				type: "POST",
				url: c_url,
				data: {
					vote: score
				},
				success: function (data) {
					$('#post-rating').html(data).fadeIn("slow");
				}
			});
		}
	});

	$('.ajax-module').live('click', function (e) {

		var href = $(this).attr("href");
		var title = $(this).attr("title");

		if (typeof (title) == 'undefined') {
			title = '';
		}

		$('#current-module').fadeTo(120, 0.7);
		$('#current-module').load(href, function () {
			$('#current-module').fadeTo(150, 1);
			document.title = title;
		});

		history.pushState('', title, href);
		e.preventDefault();
	});


	/* pm vesture */
	$('#reply-history').live('click', function (e) {
		$('#pm-history-container').load($(this).attr('href'));
		$('#reply-history').addClass('disabled');
		e.preventDefault();
	});


	/* junk vote */
	$('#junk-vote-wrap a').live('click', function (e) {
		$('#junk-vote-wrap').load($(this).attr('href'));
		e.preventDefault();
	});

	setTimeout('msgrr()', query_timeout);

});
