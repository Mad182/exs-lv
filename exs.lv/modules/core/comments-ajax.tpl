<!-- START BLOCK : comments-ajax-form -->
<form class="form comments-ajax-add" action="" method="post" onsubmit="return postcomment();">
	<fieldset>
		<legend>Komentāri</legend>
		<p><textarea rows="4" cols="40" name="new-c-text" id="new-c-text"></textarea></p>
		<p><input type="submit" name="submit" class="button" value="Pievienot" /></p>
	</fieldset>
</form>
<!-- END BLOCK : comments-ajax-form -->
<!-- START BLOCK : comments-ajax-list -->
<ul id="comments-ajax-list">
	<!-- END BLOCK : comments-ajax-list -->
	<!-- START BLOCK : comments-ajax-node -->
	<li><img src="{avatar}" class="av" alt="" /><p class="comment-author"><strong>{nick}</strong> {date}</p>{text}</li>
	<!-- END BLOCK : comments-ajax-node -->
	<!-- START BLOCK : comments-ajax-empty -->
	<li class="empty">Pagaidām nav komentāru...</li>
	<!-- END BLOCK : comments-ajax-empty -->
	<!-- START BLOCK : comments-ajax-list-end -->
</ul>
<!-- END BLOCK : comments-ajax-list-end -->
