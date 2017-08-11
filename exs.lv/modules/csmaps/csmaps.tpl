<!-- START BLOCK : image_upload-admin-->
<h1>CS karšu attēlu augšupielāde</h1>
<!-- START BLOCK : image_upload-success-->
<div class="box">
	<pre>{filename}</pre>
	<img src="/bildes/cs/{filename}?reload_me" alt="{filename}" />
</div>
<!-- END BLOCK : image_upload-success-->
<form class="form" action="{page-url}" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend>Pievienot jaunu karti</legend>
		<p class="notice">Faila nosaukumam jābūt vienādam ar kartes nosaukumu. Formāts - jpg.</p>
		<p><label>Attēls no PC:</label><br />
			<input type="file" name="new-image" /></p>
		<p><input type="submit" class="button" value="Upload!"  /></p>
	</fieldset>
</form>
{lost}
<!-- END BLOCK : image_upload-admin-->
