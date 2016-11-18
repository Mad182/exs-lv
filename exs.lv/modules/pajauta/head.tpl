<link rel="stylesheet" href="/modules/flash-games/flash-games.css" />
<script>

	function postanswer(answer) {
		$.ajax({
			type: "POST",
			url: "/Pajauta/{qslug}/?rate=true",
			data: 'answer=' + answer,
			success: function(data) {
				$('#answer-ask').fadeOut("slow"),
								$('#answer-results').html(data).fadeIn("slow");
			}
		});
		return false;
	}

	var lastid = {lastid};
	var flaRefreshId = setInterval("update_chat()", 10000);

	function postcomment() {
		clearInterval(flaRefreshId);
		flaRefreshId = '';
		$.ajax({
			type: "POST",
			url: "/Pajauta/{qslug}/?ajax=" + lastid,
			data: 'new-c-text=' + encodeURIComponent($("#new-c-text").val()) + '&ajax=true',
			success: function(data) {
				$('#new-c-text').val("");
				update_chat();
			}
		});
		flaRefreshId = setInterval("update_chat()", 4000);
		return false;
	}

	function update_chat() {
		$.getJSON('/Pajauta/{qslug}/?ajax=' + lastid + '&' + Math.round(new Date().getTime()), function(data) {
			var items = [];
			$.each(data, function(key, val) {
				if (key == 'id' && val != lastid) {
					lastid = val;
				}
				if (key == 'comment') {
					$.each(val, function(ckey, cval) {
						$("#comments-ajax-list").prepend('<li>' + cval + '</li>').fadeIn('slow');
					});
				}
			});
		});
		return false;
	}

</script>
