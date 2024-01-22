<!-- START BLOCK : add-wp-form-->

<h1>Ekrāntapešu administrācija</h1>
<form id="add-image" class="form" action="{page-url}" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend>Pievienot jaunu attēlu</legend>
		<p>
			<label for="new-image">Bilde:</label><br>
		<ul id="wallpapers-external" style="display:none"></ul>
		<small><a href="#" data-addwp-action="load-external" data-target="#wallpapers-external" data-resource="/wallpaper_admin/catsite.json">Ielādēt ārējo resursu attēlus</a></small>
		<input type="file" name="new-image" id="new-image" />
		</p>
		<p>
			<input type="submit" name="submit" id="submit" value="Pievienot" />
		</p>
	</fieldset>
</form>

<h2>Tuvākajā laikā būs...</h2>

<ul id="wallpapers">
	<!-- START BLOCK : wallpaper-->
	<li><a href="//exs.lv/dati/wallpapers/{image}"><img src="//exs.lv/dati/wallpapers/thb/{image}" alt="dienas ekrāntapete" /><span{style}>{date}</span></a></li>
	<!-- END BLOCK : wallpaper-->
</ul>
<div class="c"></div>
<!-- END BLOCK : add-wp-form-->

