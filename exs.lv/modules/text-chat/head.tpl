<!-- START BLOCK : chat-head-->
<script type="text/javascript">

	var lastid = {lastid};
	var flaRefreshId = setInterval("update_chat()", 8000);

	function postcomment() {
		clearInterval(flaRefreshId);
		flaRefreshId = '';
		$.ajax({
			type: "POST",
			url: "/{slug}/?ajax=" + lastid,
			data: 'new-c-text=' + encodeURIComponent($("#new-c-text").val()) + '&ajax=true',
			success: function(data) {
				$('#new-c-text').val("");
				update_chat();
			}
		});
		flaRefreshId = setInterval("update_chat()", 8000);
		return false;
	}

	function update_chat() {
		$.getJSON('/{slug}/?ajax=' + lastid + '&' + Math.round(new Date().getTime()), function(data) {
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
<!-- END BLOCK : chat-head-->
