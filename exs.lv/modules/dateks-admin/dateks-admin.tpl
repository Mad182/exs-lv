<h1>Dateks.lv piedāvājumu admin</h1>

<!-- START BLOCK : offer-edit-->
<h2>Labot/pievienot</h2>

<form id="edit-offer" class="form" action="{page-url}" method="post">
	<fieldset>
		<p>
			<label for="offer-title">Nosaukums:</label><br />
			<input value="{title}" class="text" name="offer-title" id="offer-title" />
		</p>
		<p>
			<label for="offer-link">Links:</label><br />
			<input value="{link}" class="text" name="offer-link" id="offer-link" />
		</p>
		<p>
			<label for="offer-img">Attēls:</label><br />
			<input value="{img}" class="text" name="offer-img" id="offer-img" />
		</p>
		<p>
			<label for="offer-price">Cena:</label><br />
			<input value="{price}" class="text" name="offer-price" id="offer-price" />
		</p>
		<p>
			<label for="offer-params">Parametri:</label><br />
			<input value="{params}" class="text" name="offer-params" id="offer-params" />
		</p>
		<p>
			<input type="submit" name="submit" class="button primary" value="Saglabāt">
		</p>
	</fieldset>
</form>
<!-- START BLOCK : offer-edit-->

<!-- START BLOCK : offers-list-->
<h2>Esošie</h2>
<table class="table">
	<tr>
		<th>Nosaukums</th>
		<th>Labot</th>
		<th>Dzēst</th>
	</tr>
	<!-- START BLOCK : offers-list-node-->
	<tr>
		<td>{title}</td>
		<td><a href="/{category-url}{offer-type}?edit={id}">labot</a></td>
		<td><a class="confirm" href="/{category-url}?delete={id}">dzēst</a></td>
	</tr>
	<!-- END BLOCK : offers-list-node-->
</table>
<!-- START BLOCK : offers-list-->

