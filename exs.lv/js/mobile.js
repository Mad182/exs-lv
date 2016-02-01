var processing = false;

function update_mb() {
	clearInterval(mbRefreshId);
	mbRefreshId = '';
	if (processing == false) {
		processing = true;
		$.getJSON('/json_mb.php?mbid=' + mbid + '&lastid=' + lastid + '&url=' + encodeURIComponent(c_url) + '&et=' + edit_time, function(data) {
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

$(document).ready(function() {

	$.ajaxSetup({
		cache: false
	});

	$("body").on("click", ".confirm", function() {
		return confirm("Vai tiešām vēlies veikt šo darbību?");
	});

	$("body").on("click", ".spoiler-title", function() {
		$(this).siblings('.spoiler-content').toggle(200);
		return false;
	});

	//lapu slēgšana
	$("body").on("change", ".ajax-checkbox", function() {
		$.ajax({
			type: "POST",
			url: $(this).parent().parent().parent().attr('action'),
			data: $(this).parent().parent().parent().serialize()
		});
		return false;
	});

	//mb komentara forma
	$("body").on("submit", "#addresponse", function() {

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
		return false;
	});

	//atbildet uz minibloga komentaru
	$("body").on("click", ".mb-reply-to", function() {
		if ($(this).siblings('.reply-ph').html() != '') {
			return false;
		}
		$('.reply-ph').fadeOut(0);
		$(this).siblings('.reply-ph').html($('.reply-ph-current').html());
		$('.reply-ph-current').html('').removeClass('reply-ph-current');
		$(this).siblings('.reply-ph').addClass('reply-ph-current').fadeIn('fast');
		$('#response-to').val($(this).attr('href'));
		return false;
	});

	//atpakaļ uz atbildēšanu galvenajam mb
	$("body").on("click", ".mb-reply-main", function() {
		if ($('.reply-ph-default').html() != '') {
			return false;
		}
		$('.reply-ph').fadeOut(0);
		$('.reply-ph-default').html($('.reply-ph-current').html());
		$('.reply-ph-current').html('').removeClass('reply-ph-current');
		$('.reply-ph-default').addClass('reply-ph-current').fadeIn('fast');
		$('#response-to').val(mbid);
		return false;
	});

	$("body").on("click", "#addpic", function() {
		$('#newpic').toggle(200);
		return false;
	});

	$("body").on("click", ".ajax-pager a", function() {
		var elem = $(this).parent().parent();
		elem.fadeTo(250, 0.5);
		elem.load($(this).attr('href'), function() {
			elem.fadeTo(250, 1);
		});
		return false;
	});

	$('.mb-icon').hover(
					function() {
						$(this).fadeTo(150, 1)
					},
					function() {
						$(this).fadeTo(150, 0.45)
					}
	);

	$('.mb-icon').fadeTo(150, 0.45);

	/* flash message (brīdinājum un paziņojumu) aizvēršana */
	$("body").on("click", "#close-flash-message", function(e) {
		$('#flash-message').fadeOut(500);
		e.preventDefault();
	});

	/* load page contents with ajax */
	$("body").on("click", ".ajax-module, .ajax-module-mobile", function(e) {

		var href = $(this).attr("href");
		var title = $(this).attr("title");

		if (typeof (title) == 'undefined') {
			title = '';
		}

		$('#current-module').fadeTo(120, 0.7);
		$('#current-module').load(href, function() {
			$('#current-module').fadeTo(150, 1);
			document.title = title;
		});

		history.pushState('', title, href);
		e.preventDefault();
	});

	/* comment rate */
	$("body").on("click", '.plus, .minus', function(e) {
		var elem = $(this).parent();
		elem.fadeTo(250, 0.5);
		elem.load($(this).attr('href'), function() {
			elem.fadeTo(150, 1);
		});
		e.preventDefault();
	});

	/* paslēptie miniblogu posti */
	$('.toggle-replies').click(function(e){   
		e.preventDefault();
		$(this).hide();
		$(this).siblings('.more-replies').show();
		$(this).siblings('.post-content').show();
	});

	/* sidr menu */
	 $('#menu').sidr();

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

});
