<!-- START BLOCK : facts_admin-edit-->
<h2>Labot vārdu</h2>

<form id="edit-fact" class="form" action="{page-url}" method="post">
  <fieldset>
<!-- START BLOCK : facts_admin-successupd-->
    <p class="success">Izmaiņas saglabātas!</p>
<!-- END BLOCK : facts_admin-successupd-->
		<p>
			<label for="edit-fact">Vārds:</label><br />
			<textarea class="text" name="edit-word" id="edit-word" style="width: 300px; height: 20px;" cols="10" rows="4">{word}</textarea>
		</p>
		<p>
			<label for="edit-hint">Jautājums:</label><br />
			<textarea class="text" name="edit-hint" id="edit-hint" style="width: 300px; height: 80px;" cols="10" rows="4">{hint}</textarea>
		</p>
		<p>
			<input type="submit" name="submit" id="submit" value="Saglabāt" />
		</p>
  </fieldset>
</form>
<!-- START BLOCK : facts_admin-edit-->

<!-- START BLOCK : facts_admin-add-->
<h2>Jauns vārds</h2>

<form id="new-fact" class="form" action="/?c=331" method="post">
  <fieldset>
<!-- START BLOCK : facts_admin-success-->
    <p class="success">Jautājums pievienots!</p>
<!-- END BLOCK : facts_admin-success-->
		<p>
			<label for="new-word">Vārds:</label><br />
			<textarea class="text" name="new-word" id="new-word" style="width: 300px; height: 20px;" cols="10" rows="4"></textarea>
		</p>
		<p>
			<label for="new-hint">Jautājums:</label><br />
			<textarea class="text" name="new-hint" id="new-hint" style="width: 300px; height: 80px;" cols="10" rows="4"></textarea>
		</p>
		<p>
			<input type="submit" name="submit" id="submit" value="Pievienot" />
		</p>
  </fieldset>
</form>
<!-- START BLOCK : facts_admin-add-->

<!-- START BLOCK : facts_admin-list-->
<h2>Visi vārdi</h2>
<table class="main-table">
  <tr>
    <th>Vārds</th>
    <th>Jautājums</th>
    <th>Labot</th>
  </tr>
<!-- START BLOCK : facts_admin-list-node-->
<tr><td>{word}</td><td>{hint}</td><td><a href="/?c=331&amp;edit={id}">labot</a></td></tr>
<!-- END BLOCK : facts_admin-list-node-->
</table>
<!-- START BLOCK : facts_admin-list-->