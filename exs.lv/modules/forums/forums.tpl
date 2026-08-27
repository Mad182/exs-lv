<!-- START BLOCK : forum-->

	<!-- START BLOCK : forum-new-->
		<a class="add-topic button primary" href="#new">+ izveidot tēmu</a>
	<!-- END BLOCK : forum-new-->

<h1>{title}</h1>

{forum-table-html}

<!-- START BLOCK : forum-addtopic-->
<h2 id="new">Pievienot jaunu tēmu</h2>
<form class="form" action="" method="post">
	<fieldset>
		<legend>Jauna tēma</legend>
		<input type="hidden" name="token" value="{forum-check}" />
		<p>
			<label for="new-topic-title">Tēmas nosaukums:</label><br>
			<input type="text" name="new-topic-title" id="new-topic-title" class="text" maxlength="72" />
		</p>
		<p>
			<label for="new-topic-title">Foruma kategorija:</label><br>
			<select name="new-topic-category">
				<!-- START BLOCK : select-category-->
				<option value="{id}"{sel}>{title}</option>
				<!-- END BLOCK : select-category-->
			</select>
		</p>
		<label for="new-topic-title">Teksts:</label><br>
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

