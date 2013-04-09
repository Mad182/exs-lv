<!-- START BLOCK : games-head-->
<script type="text/javascript">
var lastid = {lastid};
var flaRefreshId = setInterval("update_chat()",20000);
function postcomment() {
clearInterval(flaRefreshId);
flaRefreshId = '';
$.ajax({
	type: "POST",
	url: "/{category-url}/{slug}/?ajax="+lastid,
	data: 'new-c-text='+encodeURIComponent($("#new-c-text").val())+'&ajax=true',
	success: function(data) {
	  $('#new-c-text').val("");
	  update_chat();
	}
});
flaRefreshId = setInterval("update_chat()",6000);
return false;
}
function update_chat() {
$.getJSON('/{category-url}/{slug}/?ajax='+lastid+'&'+Math.round(new Date().getTime()), function(data) {
	var items = [];
	$.each(data, function(key, val) {
		if(key == 'id' && val != lastid) {
			lastid = val;
		}
		if(key == 'comment') {
			$.each(val, function(ckey, cval) {
				$('<li>'+cval+'</li>').hide().prependTo("#comments-ajax-list").fadeIn('slow');
			});
			$("#comments-ajax-list .empty").hide();
		}
	});
});
return false;
}
function postrating() {
$.ajax({
	type: "POST",
	url: "/{category-url}/{slug}/?rate=true",
	data: $("#game-rating-form").serialize(),
	success: function(data) {
	  $('#game-rating-form').fadeOut("slow"),
	  $('#game-rating').html(data).fadeIn("slow");
	}
});
return false;
}
</script>
<!-- END BLOCK : games-head-->
