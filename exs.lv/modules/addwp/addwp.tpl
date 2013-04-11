<!-- START BLOCK : add-wp-form-->

<h1>Ekrāntapešu administrācija</h1>
<form id="add-image" class="form" action="{page-url}" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend>Pievienot jaunu attēlu</legend>
		<p>
			<label for="new-image">Bilde:</label><br />
			<input type="file" name="new-image" id="new-image" />
		</p>
		<p>
			<input type="submit" name="submit" id="submit" value="Pievienot" />
		</p>
	</fieldset>
</form>

<h2>Upcoming</h2>

<ul id="wallpapers">
	<!-- START BLOCK : wallpaper-->
	<li><a href="http://exs.lv/dati/wallpapers/{wallpaper-image}"><img src="http://exs.lv/dati/wallpapers/thb/{wallpaper-image}" alt="dienas ekrāntapete" /><span{style}>{wallpaper-date}</span></a></li>
	<!-- END BLOCK : wallpaper-->
</ul>
<div class="c"></div>
<!-- START BLOCK : add-wp-form-->
