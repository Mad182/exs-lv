<h1>Apbalvojumu ikonas</h1>

<p><a href="/deco_list">Ikonu saraksts</a> (right click - copy link location)</p>

<form class="form" method="post" action="">
	<fieldset>
		<legend>Everyone gets an award icon</legend>
		<div class="radio">
			<label><input type="radio" name="give_deco_all" id="give_deco_all">Pievienot katram lietotājam apbalvojuma ikonu</label>
			<label><input type="radio" name="remove_deco_all" id="remove_deco_all">Noņemt visiem apbalvojuma ikonas</label>
		</div>
		<p>
			<input class="button primary" type="submit" name="submit" id="submit" value="Apstiprināt" />
		</p>

	</fieldset>
</form>

<table class="table">
	<tr>
		<th>title</th>
		<th>nick</th>
		<th>icon</th>
		<th>remove</th>
	</tr>
	<!-- START BLOCK : existing-deco-->
	<tr>
		<td>{title}</td>
		<td>{nick}</td>
		<td><img src="{icon}" alt="" width="16" height="16" />&nbsp;{icon}</td>
		<td><a class="button danger confirm" style="font-size: 90%;line-height: 1.1;padding: 3px 5px;" class="confirm" href="?uid={userid}&amp;remove={key}">noņemt</a></td>
	</tr>
	<!-- END BLOCK : existing-deco-->
</table>

<form class="form" method="post" action="">
	<fieldset>
		<legend>Pievienot jaunu</legend>

		<p>
			<label for="userid">Lietotāja #ID</label><br />
			<input type="text" class="text" name="userid" id="userid" />
		</p>

		<p>
			<label for="title">Nosaukums</label><br />
			<input type="text" class="text" name="title" id="title" />
		</p>

		<p>
			<label for="icon">Links uz ikonu (16x16px !!!!!!!!!!!!!)</label><br />
			<input type="text" class="text" name="icon" id="icon" />
		</p>

		<p>
			<input class="button primary" type="submit" name="submit" id="submit" value="Pievienot" />
		</p>

	</fieldset>
</form>


