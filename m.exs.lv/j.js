var processing = false;

function update_mb() {
	clearInterval(mbRefreshId);
	mbRefreshId = '';
	if(processing == false) {
		processing = true;
		$.getJSON('/json_mb.php?mbid='+mbid+'&lastid='+lastid+'&url='+encodeURIComponent(c_url)+'&et='+edit_time, function(data) {
			var items = [];
			$.each(data, function(key, val) {
				if(key == 'id' && val != lastid) {
					lastid = val;
				}
				if(key == 'et' && val != lastid) {
					edit_time = val;
				}
				if(key == 'comment') {
					refreshlim = mb_refresh_limit;
					$.each(val, function(ckey, cval) {
						$.each(cval, function(rkey, rval) {
							$('<li>'+rval+'</li>').hide().appendTo('ul.responses-'+ckey).fadeIn('slow');
						});
					});
				}
				if(key == 'edits') {
					refreshlim = mb_refresh_limit;
					$.each(val, function(ckey, cval) {
						$('#m'+ckey).siblings('.response-content').children('.post-content').html(cval);
					});
				}
			});
		});
		processing = false;
	} else {
		refreshlim = mb_refresh_limit;
	}
	refreshlim = refreshlim+1500;
	mbRefreshId = setInterval("update_mb()",refreshlim);
}

$(document).ready(function() {

	$.ajaxSetup ({
		cache: false
	});

	$('.confirm').live('click', function(){
		return confirm("Vai tiešām vēlies veikt šo darbību?");
	});
	
	$('.spoiler-title').live('click', function() {
		$(this).siblings('.spoiler-content').toggle(200);
		return false;
	});

	//lapu slēgšana
	$('.ajax-checkbox').live('change', function() {
		$.ajax({
			type: "POST",
			url: $(this).parent().parent().parent().attr('action'),
			data: $(this).parent().parent().parent().serialize()
		});
		return false;
	});

	//mb komentara forma
	$('#addresponse').live('submit', function() {
		clearInterval(mbRefreshId);
		mbRefreshId = '';
		$('#mbresponse-submit').hide();
		$('#mbresponse-waiting').show();
		$.ajax({
			type: "POST",
			url: $(this).attr('action')+'?postcomment=true',
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
		mbRefreshId = setInterval("update_mb()",refreshlim);
		return false;
	});

	//atbildet uz minibloga komentaru
	$('.mb-reply-to').live('click', function() {
		if($(this).siblings('.reply-ph').html() != '') {
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
	$('.mb-reply-main').live('click', function() {
		if($('.reply-ph-default').html() != '') {
			return false;
		}
		$('.reply-ph').fadeOut(0);
		$('.reply-ph-default').html($('.reply-ph-current').html());
		$('.reply-ph-current').html('').removeClass('reply-ph-current');
		$('.reply-ph-default').addClass('reply-ph-current').fadeIn('fast');
		$('#response-to').val(mbid);
		return false;
	});

	$('#addpic').live('click', function() {
		$('#newpic').toggle(200);
		return false;
	});

	$('.ajax-pager a').live('click', function() {
		var elem = $(this).parent().parent();
		elem.fadeTo(250, 0.5);
		elem.load($(this).attr('href'), function() {
			elem.fadeTo(250, 1);
		});
		return false;
	});

	$('.mb-icon').hover(
		function () {
			$(this).fadeTo(150, 1)
		},
		function () {
			$(this).fadeTo(150, 0.45)
		}
	);

	$('.mb-icon').fadeTo(150, 0.45);

});
