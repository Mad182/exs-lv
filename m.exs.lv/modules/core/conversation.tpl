<!-- START BLOCK : close-reason-->
<form id="close-reason" class="form" action="{page-url}" method="post">
<fieldset>
	<legend>Minibloga slēgšana</legend>
	<p>
		<label for="reason">Iemesls</label><br />
		<input type="text" name="reason" id="reason" class="text" />
	</p>
	<input class="button danger" type="submit" name="submit-close" id="submit-close" value="Slēgt" />
</fieldset>
</form>
<!-- END BLOCK : close-reason-->
<!-- START BLOCK : user-miniblog-form-->
<form id="addminiblog" class="form" action="{page-url}" method="post">
<fieldset>
	<legend>Pievienot jaunu ierakstu</legend>
	<input type="hidden" name="token" id="token" value="{token}" />
	<textarea style="width: 80%;" class="mb-textarea" rows="5" cols="42" name="newminiblog" id="newminiblog"></textarea>
	<p>
		<input class="button primary" type="submit" name="submit" id="submit" value="Pievienot" />
	</p>
</fieldset>
</form>
<!-- END BLOCK : user-miniblog-form-->
<!-- START BLOCK : user-miniblog-list-->
<ul id="miniblog-list">
<!-- START BLOCK : user-miniblog-list-node-->
<li>
	<div class="mbox">
		<a id="m{id}" href="/user/{author-id}"><img class="av" src="/av/{author-avatar}" alt="{author-nick}" width="45" height="45" /></a>
		<!-- START BLOCK : mb-reply-main-->
		<a href="#" class="mb-reply-main mb-icon">Atbildēt</a>
		<!-- END BLOCK : mb-reply-main-->
		<!-- START BLOCK : mb-edit-main-->
		<!-- START BLOCK : mb-edit-main-->
		<!-- START BLOCK : mb-delete-->
		<!-- END BLOCK : mb-delete-->
		<p class="post-info"><a href="/user/{author-id}">{author}</a> <span class="date-time" title="{date-title}">{date}</span> teica:</p>
		{text}
		<div class="c"></div>
		<!-- START BLOCK : mb-tags-wrapper-->
			<!-- START BLOCK : mb-tags-->
				<!-- START BLOCK : mb-tags-node-->
				<!-- END BLOCK : mb-tags-node-->
			<!-- END BLOCK : mb-tags-->
		<!-- START BLOCK : mb-newtags-->
		<!-- END BLOCK : mb-newtags-->
		<!-- END BLOCK : mb-tags-wrapper-->
	</div>

	<!-- START BLOCK : miniblog-posts-->
	{mbout}
	<!-- END BLOCK : miniblog-posts-->

	<!-- START BLOCK : miniblog-no-->
	<ul class="responses-0"><li style="display:none">Nav atbilžu</li></ul>
	<!-- END BLOCK : miniblog-no-->

	<!-- START BLOCK : mb-more-->
	<ul class="responses mb-open">
		<li class="more"><a href="{url}">{text}</a></li>
	</ul>
	<!-- END BLOCK : mb-more-->

	<!-- START BLOCK : user-miniblog-resp-->
	<div class="reply-ph-default reply-ph-current">
	<div id="response-{id}" class="miniblog-response">
		<form id="addresponse" class="form" action="{page-url}" method="post">
			<fieldset>
				<legend>Atbilde</legend>
				<input type="hidden" name="response-to" id="response-to" value="{id}" />
				<input type="hidden" name="token" id="token" value="{token}" />
				<textarea class="mb-textarea" style="width: 80%" rows="5" cols="42" name="responseminiblog" id="responseminiblog"></textarea><br />
				<input id="mbresponse-submit" class="button primary" type="submit" name="submit" value="Pievienot" />
				<input id="mbresponse-waiting" class="button disabled" type="submit" name="submit-disabled" style="display:none" value="Pievienot" disabled="disabled" />
			</fieldset>
		</form>
	</div>
	</div>
	<!-- END BLOCK : user-miniblog-resp-->
	<!-- START BLOCK : user-miniblog-closed-->
	<div class="miniblog-response"><p class="closed">Šis minibloga ieraksts ir slēgts.{by}{reason}</p></div>
	<!-- END BLOCK : user-miniblog-closed-->
</li>
<!-- END BLOCK : user-miniblog-list-node-->
</ul><div class="c"></div><p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<!-- END BLOCK : user-miniblog-list-->