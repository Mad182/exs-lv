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
	
	/* 	tiek izsaukta, nospiežot uz lietotāja nosūdzēšanas podziņas;
		atver fancybox ar pārkāpuma aprakstīšanas formu */
	$('.report-user').live('click', function(e) {		
		$.ajax({
			dataType: "json",
			url: $(this).attr('href') + '?_=1',
			success: function (data) {
				$.fancybox( data.content );				
			}
		});
		e.preventDefault();		
	});
	
	/*	izmanto moderatoru sadaļā, lai apskatītu iesniegtās sūdzības saturu */
	$('.get-report-content').live('click', function(e) {
		$.getJSON( ($(this).attr('href') + '?_=1'), function(response) {
		  $.fancybox( response.message );
		});
		e.preventDefault();	
	});
	
	/* 	izmanto sūdzību iesūtīšanai; tiek izsaukta, 
		nospiežot submit pogu fancybox logā */
	$('#report-form').live('submit', function() {
		$.ajax({
			type: "POST",
			dataType: "json",
			url: $('#report-form').attr('action') + '?_=1',
			data: $('#report-form').serialize(),
			success: function (response) {
			
				$('.report-response').html("");
				
				if ( response.state == 'success' ) {
					$('#outer-form-block').toggle('slow');
					$('.report-response').attr('class', 'report-response-good').html( response.content );
				}
				else {
					$('.report-response').html( response.content );
				}
			}
		});
		return false;
	});
	
	/* aizver atvērto fancybox, nospiežot uz "Pārdomāju" podziņas */
	$('.fancy-close').live('click',function() {
		$.fancybox.close();
		return false;
	});
	
	/* sūdzību arhivēšanas poga */
	$('.report-archive').on('click', function(e) {		
		var element = $(this);
		$.getJSON( $(this).attr('href') + '?_=1', function(response) {
			if (response.state == 'success') {				
				$(element).attr('href', '#').text(response.text);
                $(element).removeClass('primary').removeClass('report-archive').addClass('danger');
			}
		});	
		e.preventDefault();
	});
	
	/* ļauj apskatīt dzēsto miniblogu ierakstu bijušo saturu */
	$('.deleted-content').live('click', function(e) {
		$.get( ($(this).attr('href') ), function(response) {
		  $.fancybox( response );
		});
		e.preventDefault();	
	});
	
	/* paslēpj/parāda iesniegto sūdzību paziņojumu pilno saturu */
	$('.report-full').live('click', function(e) {
		$content = $(this).siblings('.report-full-content').html();
		$(this).parent().html( $content );
		e.preventDefault();
	});
    
    /* dzēš minibloga ierakstu bez lapas pārlādes */
    $('.delete-fast').live('click', function(e) {
        if ( confirm("Vai tiešām vēlies veikt šo darbību?") ) {
            var link_element    = $(this);
            var content_element = $(this).parent().siblings('.post-content');
            $.getJSON( $(this).attr('href') + '?_=1', function(response) {
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
        if( $(this).scrollTop() > 200 && !is_visible ) {
            $elem.stop().animate({bottom: '40px', opacity: 0.6}, 500);
            is_visible = true;
        }
        else if ( $(this).scrollTop() <= 200 && is_visible ) {
            $elem.stop().animate({bottom: '200px', opacity: 0}, 200, function() {
                $(this).css({bottom:'-100px'});
            });
            is_visible = false;
        }
    });
    $elem.click(function() {
        $('html, body').stop().animate({scrollTop: 0}, 500, function() {
            $elem.stop().animate({bottom: '500px', opacity: 0}, 200, function() {
                $(this).css({bottom:'-100px'});
            });
        });        
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

	/* pārbauda vai lietotājs eksistē, piemēram pie reģistrācijas vai nika maiņas */
	$('.usercheck').live('change', function () {
		$('.usercheck-response').load('/userexists/?user=' + encodeURIComponent($(this).val()));
	});

	/* flash message (brīdinājumu un paziņojumu) aizvēršana */
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


	/* wp admin */
	$('[data-addwp-action="load-external"]').on('click', function (e) {
		var target = $(this).data('target');
		var resource = $(this).data('resource');

		$.getJSON(resource, function (data) {
			$(data).each(function (i, entity) {
				$(target).append('<li><a href="' + entity['file'] + '" class="lightbox-added"><img src="' + entity['thumb'] + '"></a><br><input type="radio" name="new-image-id" value="' + i + '"></li>')
			});

			$('.lightbox-added').fancybox({
				'titleShow': false
			});
		});

		$(target).show();
		$('#new-image').remove();
		$(this).off('click').remove();
		e.preventDefault();
	});

	setTimeout('msgrr()', query_timeout);

});
