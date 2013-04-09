<!-- START BLOCK : comments-block-->
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<ul class="responses">
<!-- START BLOCK : comments-node-->		
<li>
	<a name="c{comment-id}" href="{aurl}">
		<img class="av" src="http://exs.lv/{avatar}" alt="{nick}" />
	</a>
	<div class="response-content">
		<p class="date"><a href="{aurl}">{comment-author}</a> @ {comment-date} atbildēja:
			<!-- START BLOCK : comments-adm-->
			| <a href="/?p={page-id}&amp;delanon={comment-id}" onclick="return confirm_delete();">dzēst</a></a>
			| <a href="/?p={page-id}&amp;editcom={comment-id}">labot</a></a>
			<!-- END BLOCK : comments-adm-->
			<!-- START BLOCK : comments-own-->
			| <a href="/?p={page-id}&amp;editcom={comment-id}">labot</a></a>
			<!-- END BLOCK : comments-own-->
		</p>
		{comment-text}
	</div>
</li>
<!-- END BLOCK : comments-node-->
</ul>
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<!-- END BLOCK : comments-block-->

<!-- START BLOCK : add-comment-->
<form action="" class="form" method="post">
	<fieldset>
	  <legend>Atbildēt</legend>
  	<input type="hidden" name="comment-pid" value="{comment-pid}" />
		<input type="hidden" name="checksrc" value="{comment-pid-check}" />
    <p><textarea rows="5" style="width: 70%; height: 80px;" cols="36" name="commenttext" ></textarea></p>
  	<input class="button" type="submit" value="Pievienot" />
	</fieldset>
</form>
<!-- END BLOCK : add-comment-->