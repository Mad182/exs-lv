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
		<textarea class="mb-textarea" rows="5" cols="42" name="newminiblog" id="newminiblog"></textarea>
		<!-- START BLOCK : private-checkbox-->
		<br /><label><input type="checkbox" name="private" value="1" /><small>&nbsp;nerādīt publiski (<abbr title="Ja atzīmēsi šo opciju, tavs minibloga ieraksts būs redzams tikai reģistrētajiem lietotājiem un netiks indeksēts google">?</abbr>)</small></label><br />
		<!-- END BLOCK : private-checkbox-->
		<p>
			<input class="button primary" type="submit" name="submit" id="submit" value="Pievienot" />
		</p>
	</fieldset>
</form>
<!-- END BLOCK : user-miniblog-form-->
<!-- START BLOCK : user-miniblog-list-->
<ul id="miniblog-list">

	<!-- START BLOCK : user-miniblog-list-private-->
	<li>
		<div class="mbox">
			<p>Šī tēma apskatāma tikai reģistrētajiem lietotājiem!</p>
			<p>Ielogojies, vai <a href="/register">Reģistrējies</a>!</p>
		</div>
	</li>
	<!-- END BLOCK : user-miniblog-list-private-->

	<!-- START BLOCK : user-miniblog-list-node-->
	<li>
		<div class="mbox">
			<div class="mb-av">
				<a id="m{id}" href="/user/{author-id}">
					<img class="av" src="{avatar}" alt="{author-nick}" width="45" height="45" />
				</a>
				{add_deco}
			</div>
			<!-- START BLOCK : mb-reply-main-->
			<a href="#" class="mb-reply-main mb-icon">Atbildēt</a>
			<!-- END BLOCK : mb-reply-main-->
			<!-- START BLOCK : mb-edit-main-->
			<a href="/edit/{id}" id="edit-{id}" class="mb-icon mb-edit">
				<img src="//exs.lv/bildes/fugue-icons/balloon--pencil.png" alt="Labot" title="Labot" width="16" height="16" />
			</a>
			<!-- START BLOCK : mb-edit-main-->
			<!-- START BLOCK : mb-edit-close-->
			<a href="{url}/close" id="close-{id}" class="mb-icon mb-close">
				<img src="//exs.lv/bildes/fugue-icons/lock.png" alt="Aizvērt" title="Aizvērt tēmu" width="16" height="16" />
			</a>
			<!-- START BLOCK : mb-edit-close-->
			<!-- START BLOCK : mb-edit-unclose-->
			<a href="{url}/open" id="unclose-{id}" class="mb-icon mb-unclose">
				<img src="//exs.lv/bildes/fugue-icons/lock-unlock.png" alt="Atvērt" title="Atvērt tēmu" width="16" height="16" />
			</a>
			<!-- START BLOCK : mb-edit-unclose-->
			<!-- START BLOCK : mb-delete-->
			<a class="mb-icon delete confirm" title="Dzēst" href="/delete/{id}">
				<img src="//exs.lv/bildes/fugue-icons/cross-octagon-frame.png" alt="Dzēst" title="Dzēst" width="16" height="16" />
			</a>
			<!-- END BLOCK : mb-delete-->
			<div class="mb-rater">{rater}</div>
			<p class="post-info">{author} <span class="date-time" title="{date-title}">{date}</span>
				<!-- START BLOCK : report-mb -->
				<a class="post-button report-user" href="/report/miniblog/{id}" title="Ziņot par pārkāpumu!">ziņot</a>
				<!-- END BLOCK : report-mb -->
			</p>
			{text}
			<div class="c"></div>
			<!-- START BLOCK : mb-tags-wrapper-->
			<div id="mb-tags-wrapper">
				<!-- START BLOCK : mb-tags-->
				<ul id="mb-tags" class="list-tags">
					<!-- START BLOCK : mb-tags-node-->
					<li><a href="/tag/{slug}" rel="tag">{name}</a></li>
					<!-- END BLOCK : mb-tags-node-->
				</ul>
				<div class="c"></div>
				<!-- END BLOCK : mb-tags-->
			</div>
			<!-- START BLOCK : mb-newtags-->
			<div class="c"></div>
			<form action="{page-url}" id="new-tags-mb" class="form" method="post" style="float:right;padding:0;margin:-4px 0 0">
				<input style="width:140px;font-size:10px;padding:3px 1px 2px" type="text" class="text" name="newtags" id="post-tags-input" />
				<input style="padding:1px" class="button" type="submit" value="Tag" />
			</form>
			<div class="c"></div>
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
						<!-- START BLOCK : resp-tools-->
						<label><input type="checkbox" name="no-bump" value="1" /><small>&nbsp;nebumpot</small></label><br />
						<!-- END BLOCK : resp-tools-->
						<textarea class="mb-textarea" tabindex="1" rows="5" cols="42" name="responseminiblog" id="responseminiblog"></textarea>
						<p>
							<input id="mbresponse-submit" tabindex="2" class="button primary" type="submit" name="submit" value="Pievienot" />
							<input id="mbresponse-waiting" class="button disabled" type="submit" style="display:none" value="Pievienot" disabled="disabled" />
						</p>
					</fieldset>
				</form>
			</div>
		</div>
		<!-- END BLOCK : user-miniblog-resp-->
		<!-- START BLOCK : user-miniblog-closed-->
		<div class="miniblog-response"><p class="closed">Šis minibloga ieraksts ir slēgts.{by}</p>{reason}</div>
		<!-- END BLOCK : user-miniblog-closed-->
		<!-- START BLOCK : user-miniblog-login-->
		<div class="miniblog-response"><p class="login">Ielogojies vai <a href="/register">izveido profilu</a>, lai komentētu!</p></div>
		<!-- END BLOCK : user-miniblog-login-->
	</li>
	<!-- END BLOCK : user-miniblog-list-node-->
</ul><div class="c"></div>

<!-- START BLOCK : mb-pager-->
<p class="core-pager">{pager-next} {pager-numeric} {pager-prev}</p>
<!-- END BLOCK : mb-pager-->

<!-- END BLOCK : user-miniblog-list-->
