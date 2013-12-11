<!-- START BLOCK : link-manager-->
<h1>{title}</h1>

<ul class="tabs">
	<li><a href="/{category-url}" class="{active-dofollow}"><span class="friends">Whitelist</span></a></li>
	<li><a href="/{category-url}/blacklisted" class="{active-blacklisted}"><span class="pages">Blacklist</span></a></li>
</ul>

<form class="form" method="post" action="">
	<fieldset>
		<legend>Pievienot jaunu domēnu</legend>
		<p>
			<label for="domain">Domēns:</label><br/ >
			<input type="text" class="text" name="domain" id="domain" />
		</p>
		<p>
			<input type="submit" class="button primary" name="submit-domain" value="Pievienot" />
		</p>
	</fieldset>
</form>

<ul style="padding:0;margin:0;list-style:none">
	<!-- START BLOCK : link-manager-item-->
	<li>{domain} <a class="confirm button danger small" href="/{category-url}/{type}/delete/{id}">dzēst</a></li>
	<!-- END BLOCK : link-manager-item-->
</ul>

<!-- END BLOCK : link-manager-->

