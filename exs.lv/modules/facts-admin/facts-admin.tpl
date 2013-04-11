<!-- START BLOCK : facts_admin-tabs-->
<ul class="tabs">
	<li><a href="/{category-url}"><span class="pages">Gaming fakti</span></a></li>
	<li><a href="/{category-url}/?type=rs"><span class="bookmarks">RuneScape fakti</span></a></li>
</ul>
<!-- END BLOCK : facts_admin-tabs-->

<!-- START BLOCK : facts_admin-edit-->
<h2>Labot faktu</h2>

<form id="edit-fact" class="form" action="{page-url}" method="post">
	<fieldset>
		<!-- START BLOCK : facts_admin-successupd-->
		<p class="success">Izmaiņas saglabātas!</p>
		<!-- END BLOCK : facts_admin-successupd-->
		<p>
			<label for="edit-fact">Fakts:</label><br />
			<textarea class="text" name="edit-fact" id="edit-fact" style="width: 300px; height: 100px;" cols="10" rows="4">{text}</textarea>
		</p>
		<p>
			<input type="submit" name="submit" id="submit" value="Saglabāt" />
		</p>
	</fieldset>
</form>
<!-- START BLOCK : facts_admin-edit-->

<!-- START BLOCK : facts_admin-list-->
<h2>{facts-title}</h2>
<table class="main-table">
	<tr>
		<th>Teksts</th>
		<th>Labot</th>
		<th>Dzēst</th>
	</tr>
	<!-- START BLOCK : facts_admin-list-node-->
	<tr>
		<td>{text}</td>
		<td><a href="/?c=206{fact-type}&amp;edit={id}">labot</a></td>
		<td><a class="confirm" href="/?c=206{fact-type}&amp;delete={id}">dzēst</a></td>
	</tr>
	<!-- END BLOCK : facts_admin-list-node-->
</table>
<!-- START BLOCK : facts_admin-list-->

<!-- START BLOCK : facts_admin-add-->
<h2>Jauns fakts</h2>

<form id="new-fact" class="form" action="/?c=206{fact-type}" method="post">
	<fieldset>
		<!-- START BLOCK : facts_admin-success-->
		<p class="success">Fakts pievienots!</p>
		<!-- END BLOCK : facts_admin-success-->
		<p>
			<label for="new-fact">Fakts:</label><br />
			<textarea class="text" name="new-fact" id="new-fact" style="width: 300px; height: 100px;" cols="10" rows="4"></textarea>
		</p>
		<p>
			<input type="submit" name="submit" id="submit" value="Pievienot" />
		</p>
	</fieldset>
</form>
<!-- START BLOCK : facts_admin-add-->
