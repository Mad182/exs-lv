<!-- START BLOCK : forum-->

	<!-- START BLOCK : forum-new-->
		<a class="add-topic button primary" href="#new">+ izveidot tēmu</a>
	<!-- END BLOCK : forum-new-->

<h1>{title}</h1>

<table id="forum">
	<!-- START BLOCK : forum-list-->
	<tr>
		<th class="first" colspan="{columns}">
			<a href="/{textid}">{title}</a>
			<!-- START BLOCK : forum-list-add-->
			<span style="float:right;font-size:9px;"><a href="/forum-add/{id}">+pievienot</a></span>
			<!-- END BLOCK : forum-list-add-->

		</th>
	</tr>
	<!-- START BLOCK : forum-item-->
	<tr>
		<!-- START BLOCK : forum-item-avatar-->
		<td class="forum-avatar">
			<a href="/{textid}"><img width="48" height="48" src="/{icon}" alt="" /></a>
		</td>
		<!-- END BLOCK : forum-item-avatar-->
		<td>
			<h3><a href="/{textid}">{title}</a></h3>
			<p>{content}{addlink}{editlink}{uplink}{downlink}</p>

			<!-- START BLOCK : subcats-->
			<ul class="subcat-list">
				<!-- START BLOCK : subcats-node-->
				<li><a href="/{textid}">{title}</a></li>
				<!-- END BLOCK : subcats-node-->
			</ul>
			<!-- END BLOCK : subcats-->

		</td>
		<!-- START BLOCK : forum-item-stats-->
		<td class="stat">
			{topics}&nbsp;{txt-topics}<br />
			{posts}&nbsp;{txt-posts}
		</td>
		<!-- END BLOCK : forum-item-stats-->
		<td class="last">
			{topic}<br />
			{date}<br />
			no: {author}
		</td>
	</tr>
	<!-- END BLOCK : forum-item-->
	<!-- END BLOCK : forum-list-->
</table>

<!-- START BLOCK : forum-addtopic-->
<h2 id="new">Pievienot jaunu tēmu</h2>
<form class="form" action="" method="post">
	<fieldset>
		<legend>Jauna tēma</legend>
		<input type="hidden" name="token" value="{forum-check}" />
		<p>
			<label for="new-topic-title">Tēmas nosaukums:</label><br />
			<input type="text" name="new-topic-title" id="new-topic-title" class="text" maxlength="72" />
		</p>
		<p>
			<label for="new-topic-title">Foruma kategorija:</label><br />
			<select name="new-topic-category">
				<!-- START BLOCK : select-category-->
				<option value="{id}"{sel}>{title}</option>
				<!-- END BLOCK : select-category-->
			</select>
		</p>
		<label for="new-topic-title">Teksts:</label><br />
		<textarea name="new-topic-body" id="new-topic-body" style="width:100%;height:300px" rows="5" cols="50">{forum-content}</textarea>
		<p><input type="submit" name="submit" value="Pievienot" class="button primary" /></p>
	</fieldset>
</form>
<!-- END BLOCK : forum-addtopic-->
<!-- START BLOCK : forum-addtopic-no-->
<h2 id="new">Pievienot jaunu tēmu</h2>
<p>Ielogojies vai <a href="/register">reģistrējies</a>, lai pievienotu tēmu!</p>
<!-- END BLOCK : forum-addtopic-no-->

<!-- END BLOCK : forum-->

