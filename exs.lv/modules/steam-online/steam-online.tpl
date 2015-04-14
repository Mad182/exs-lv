<!-- START BLOCK : steam-->
<h1>Šobrīd spēlē spēles Steam</h1>

<!-- START BLOCK : steam-login-->
<p>
	<a href="/steam-login">
		Vai vēlies, lai arī citi redz tavu aktivitāti?
	</a>
</p>
<!-- END BLOCK : steam-login-->

<!-- START BLOCK : steam-game-wrapper-->
<div id="steam-online">
	<!-- START BLOCK : steam-game-->
	<div class="game">
		<div class="hero-image">
			<a href="http://store.steampowered.com/app/{game-id}/" title="{game-name}">
				<img src="https://steamcdn-a.akamaihd.net/steam/apps/{game-id}/header.jpg" title="{game-name}"/>
			</a>
		</div>
		<div class="player-list">
			<ul>
				<!-- START BLOCK : steam-player-->
				<li>
					<a href="{profile-url}" class="steam-link">
						<img src="{img-server}/bildes/steam-ico.png" alt="steam profils">
					</a>
					<a href="/user/{id}">
						{nick}
					</a>
				</li>
				<!-- END BLOCK : steam-player-->
			</ul>
		</div>
	</div>

	<script type="text/javascript" src="{static-server}/js/masonry.pkgd.min.js"></script>
	<script type="text/javascript" >
		$(window).load(function() {
			/* masonry priekš steam lapas, uztaisa glītu layoutu  */
			var $container = $('#steam-online');
			// initialize
			$container.masonry({
				gutter: 10,
				itemSelector: '.game'
			});
		});
	</script>
	<!-- END BLOCK : steam-game-->
</div>
<!-- END BLOCK : steam-game-wrapper-->
<!-- END BLOCK : steam-->

