<!DOCTYPE html>
<html lang="lv">
	<head>
		<meta charset="UTF-8">
		<title>{page-title}</title>
		<meta name="googlebot" content="noindex">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" href="{static-server}/css/mobile.css">
		<script src="{static-server}/js/jquery.min.js,jquery.sidr.js,mobile.js"></script>
		<script>
			var mb_refresh_limit = 12000;
			var current_user = {currentuser-id};
			var query_timeout = 80000;
			var c_url = "{page-url}";
		</script>
		<!-- START BLOCK : mb-head-->
		<script>
			var lastid = {lastid};
			var mbid = {mbid};
			var usrid = {usrid};
			var edit_time = {edit_time};
			var mbtype = "{type}";
			var mbRefreshId = setInterval("update_mb()", mb_refresh_limit);
		</script>
		<!-- END BLOCK : mb-head-->
		<!-- INCLUDE BLOCK : module-head -->
		<!-- START BLOCK : tinymce-enabled-->
        <script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
		<script>
			tinymce.init({
				selector: "textarea",
				plugins: [
					"advlist autolink link image lists charmap preview hr anchor",
					"searchreplace wordcount visualblocks visualchars code",
					"table contextmenu paste"
				],
				toolbar1: "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect",
				toolbar2: "cut copy paste | undo redo | bullist numlist | outdent indent blockquote | link unlink anchor image",
				toolbar3: "table | hr removeformat | subscript superscript | charmap | searchreplace | visualchars visualblocks | code",
				relative_urls: false,
				remove_script_host: false,
				menubar: false,
				statusbar: false,
				toolbar_items_size: 'small',
				content_css: "{static-server}/css/style.css",
				style_formats: [
			{title: 'Sarkans', inline: 'span', classes: 'red'},
			{title: 'Admins', inline: 'span', classes: 'admins'},
			{title: 'Rakstu autors', inline: 'span', classes: 'rautors'},
			{title: 'Mods', inline: 'span', classes: 'mods'},
			{title: 'Lejupielāde', inline: 'a', classes: 'download'},
			{title: 'Koda bloks', block: 'pre', classes: 'prettyprint'},
			{title: 'Brīdinājuma teksts', block: 'p', classes: 'text-notice'}
				]
			});</script>
		<!-- END BLOCK : tinymce-enabled-->
		<!-- START BLOCK : tinymce-simple-->
		<!-- END BLOCK : tinymce-simple-->
	</head>

	<body>

		<div id="sidr">
		  <!-- Your content -->
		  <ul>
				<li><a href="/">Siena</a></li>
				<!-- START BLOCK : user-menu-->
				<li><a href="/pm">Vēstules{new-messages}</a></li>
				<li><a href="/say/{currentuser-id}">Miniblogs</a></li>
				<!-- END BLOCK : user-menu-->
				<li><a href="/index">Forums</a></li>
				<li><a href="/user/{currentuser-id}">Mans profils</li>
				<li><a href="/logout/{logout-hash}">Iziet</a></li>
		  </ul>
		</div>

		<div id="outer-wrapper">
			<div id="header">
				<a id="menu" href="#sidr">Toggle menu</a>
				<div id="user-tools">
					<a href="/mevents"><img src="/av/{currentuser-avatar}" alt="" />
					Čau,&nbsp;{currentuser-nick}!<br />Mani notikumi{new-messages}</a>
				</div>
				<a id="logo" href="/">coding.lv</a>
			</div>

			<div id="wrapper">

				<div id="current-module">

					<!-- START BLOCK : flash-message-->
					<div class="c"></div>
					<div class="mbox {class}" id="flash-message">
						<p>
							<a id="close-flash-message" href="#">
								<img src="{img-server}/bildes/fugue-icons/cross-button.png" alt="Aizvērt" title="Aizvērt" width="16" height="16" />
							</a>
							{message}
						</p>
					</div>
					<div class="c"></div>
					<!-- END BLOCK : flash-message-->

					<!-- START BLOCK : profile-menu-->
					<h2>{user-nick}</h2>

					<ul class="tabs">
						<li><a href="/user/{user-id}" class="{active-tab-profile}"><span class="profile">Profils</span></a></li>
						<li><a href="/say/{user-id}" class="{active-tab-miniblog}"><span class="comments">Miniblogs</span></a></li>
					</ul>

					<!-- END BLOCK : profile-menu-->
					<!-- INCLUDE BLOCK : module-core-error -->
					<!-- INCLUDE BLOCK : module-currrent -->
					<div class="c"></div>

				</div>

				<!-- START BLOCK : events-->
				{events-title}
				<div class="box">
					<div id="miniblog-block">
						<ul id="mb-list" class="blockhref">
							<!-- START BLOCK : events-node-->
							<li><a href="{url}"><span class="time-ago">{time}</span> <img class="av" src="{avatar}" alt="" /> <span class="author">{author}{where}</span> {title}&nbsp;[{posts}]<br style="clear:both" /></a></li>
							<!-- END BLOCK : events-node-->
						</ul>
					</div>
				</div>
				<!-- END BLOCK : events-->
				<!-- START BLOCK : mod-box-->
				<h3>Moderatoru forums</h3>
				<div class="box">
					<p>
						<!-- START BLOCK : mod-box-node-->
						<a href="{url}">{title}&nbsp;[{posts}]</a><br />
						<!-- END BLOCK : mod-box-node-->
					</p>
				</div>
				<!-- END BLOCK : mod-box-->
			</div>
			<div id="footer"><a href="/sitemap">Lapas karte</a> | &copy; coding.lv, {current-year}</div>
		</div>

		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-4190387-9', 'auto');
		  ga('send', 'pageview');
		</script>

	</body>
</html>
