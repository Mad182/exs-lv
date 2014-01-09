<h1>Pievienot</h1>

<form id="edit-profile" class="form" action="{page-url}" method="post" enctype="multipart/form-data">
	<fieldset>
		<p>
			<label for="title">Nosaukums:</label><br />
			<input type="text" class="text" name="title" id="title" maxlength="128" />
		</p>
		<p>
			<label for="description">Apraksts:</label><br />
			<input type="text" class="text" name="description" id="description" />
		</p>
		<p>
			<label for="flash_file">URL:</label><br />
			<input type="text" class="text" name="flash_file" id="flash_file" maxlength="512" />
		</p>
		<p>
			<label for="width">Width:</label><br />
			<input type="text" class="text" name="width" id="width" value="640" />
		</p>
		<p>
			<label for="height">Height:</label><br />
			<input type="text" class="text" name="height" id="height" value="480" />
		</p>
		<p>
			<label for="thb_local">Attēls:</label><br />
			<input type="file" class="text" name="thb_local" id="thb_local" />
		</p>
		<p><input type="submit" name="submit" id="submit" value="OK" /></p>
	</fieldset>
</form>