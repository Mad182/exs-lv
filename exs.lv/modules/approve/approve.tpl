<!-- START BLOCK : approve-body-->
<h1>Raksta iesniegšana</h1>

<ul class="tabs">
	<li><a href="/write" class="{new-active}">Jauns raksts</a></li>
	<li><a href="/write/list" class="{edit-active}">Iesniegtie</a></li>
</ul>

<div class="tabMain">
	<!-- START BLOCK : approve-new-->
    
    <!-- START BLOCK : goto-wide-page -->
    <p style="margin-left:20px"><a href="/write?wide=1">Atvērt platā skata režīmu (notiks lapas pārlāde!)</a></p>
    <!-- END BLOCK : goto-wide-page -->
    <!-- START BLOCK : goto-narrow-page -->
    <p style="margin-left:20px"><a href="/write">Atvērt šaurā skata režīmu (notiks lapas pārlāde!)</a></p>
    <!-- END BLOCK : goto-narrow-page -->

	<form action="{page-url}" id="new-article-approve" class="form" method="post" enctype="multipart/form-data">
		<fieldset>
			<legend>Iesniegt jaunu rakstu</legend>
			<div id="autosave-status"></div>
			<p class="notice">
				Lūdzu nepievieno zagtus rakstus! Tas var beigties ar banu!
			</p>
			<p><label for="new-topic-title">Raksta nosaukums:</label><br /><input type="text" name="new-topic-title" id="new-topic-title" class="text" maxlength="64" value="{draft-title}" /></p>
			<p>
				<label for="new-topic-body">Teksts:</label><br />
				<textarea name="new-topic-body" id="new-topic-body" cols="94" rows="40" style="width: 100%;height:500px"></textarea>
			</p>
			<p><label for="edit-avatar">Raksta avatars:</label><br /><input type="file" class="long" name="edit-avatar" id="edit-avatar" /></p>
			<p>
				<label for="new-topic-category">Lapas sadaļa:</label><br />
				<select name="new-topic-category">
					<!-- START BLOCK : select-category-->
					<option value="{category-id}">{category-title}</option>
					<!-- END BLOCK : select-category-->
				</select>
			</p>
			<input type="submit" name="submit" value="Pievienot" class="button" />
		</fieldset>
	</form>
	<!-- END BLOCK : approve-new-->
	<!-- START BLOCK : approve-edit-->
	<h4>{article-showtitle}</h4>
	<p>
		<strong>Autors:</strong> <a href="{aurl}">{article-author-nick}</a><br />
		<strong>Datums:</strong> {article-date}<br />
		<strong>IP:</strong> {article-ip}<br />
		{article-avatar}
	</p>
	<form action="{page-url}" class="form" method="post">
		<fieldset>
			<legend>Raksta pārbaude</legend>
			<p><label for="ap-topic-title">Nosaukums:</label><br /><input type="text" name="ap-topic-title" id="ap-topic-title" class="text" value="{article-title}" maxlength="64" /></p>
			<p>
				<label for="ap-topic-body">Saturs:</label><br />
				<textarea name="ap-topic-body" id="ap-topic-body" cols="94" rows="40" style="width: 100%; height: 400px;">{article-text}</textarea>
			</p>
			<p>
				<label for="ap-topic-category">Sadaļa:</label><br />
				<select name="ap-topic-category">
					<!-- START BLOCK : select-apcategory-->
					<option value="{category-id}"{category-sel}>{category-title}</option>
					<!-- END BLOCK : select-apcategory-->
				</select>
			</p>
			<input type="hidden" name="ap-topic-author" value="{article-author}" />
			<input type="hidden" name="ap-topic-date" value="{article-date}" />
			<input type="hidden" name="ap-topic-ip" value="{article-ip}" />
			<input type="hidden" name="ap-topic-wide" value="{article-wide}" />
			<input type="submit" name="submit" value="Pievienot" class="button" />
			<br />
			<p style="text-align:right">[<a class="red" href="/write/delete/{article-id}">Dzēst</a>]</p>
		</fieldset>
	</form>
	<!-- END BLOCK : approve-edit-->
	<!-- START BLOCK : approve-view-->
	<h4>Tavi iesniegtie raksti:</h4>
	<!-- START BLOCK : approve-list-->
	<ul>
		<!-- START BLOCK : approve-list-node-->
		<li>{approve-list-title}</li>
		<!-- END BLOCK : approve-list-node-->
	</ul>
	<!-- END BLOCK : approve-list-->
	<!-- END BLOCK : approve-view-->
	<!-- START BLOCK : approveadm-view-->
	<h4>Apstiprināšanu gaida:</h4>
	<!-- START BLOCK : approveadm-list-->
	<ul>
		<!-- START BLOCK : approveadm-list-node-->
		<li><a href="/write/edit/{approve-list-id}">{approve-list-title}</a></li>
		<!-- END BLOCK : approveadm-list-node-->
	</ul>
	<!-- END BLOCK : approveadm-list-->
	<!-- END BLOCK : approveadm-view-->
</div>
<!-- END BLOCK : approve-body-->
