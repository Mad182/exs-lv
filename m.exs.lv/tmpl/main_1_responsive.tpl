<!DOCTYPE html>
<html lang="lv">
	<head>
		<meta charset="UTF-8">
		<title>{page-title}</title>
		<meta name="googlebot" content="noindex">
		<script src="/js/jquery.min.js"></script>
		<script src="/js/config.js"></script>
		<script src="/js/skel.min.js"></script>
		<script src="/js/skel-panels.min.js"></script>
		<noscript>
		<link rel="stylesheet" href="/css/skel-noscript.css" />
		<link rel="stylesheet" href="/css/style.css" />
		<link rel="stylesheet" href="/css/style-desktop.css" />
		</noscript>
		<!--[if lte IE 9]><link rel="stylesheet" href="/css/ie9.css" /><![endif]-->
		<!--[if lte IE 8]><script src="/js/html5shiv.js"></script><![endif]-->

		<script type="text/javascript" src="{static-server}/js/mobile.js"></script>
		<script type="text/javascript">
			var mb_refresh_limit = 12000;
			var current_user = {currentuser-id};
			var query_timeout = 80000;
			var c_url = "{page-url}";
		</script>
		<!-- START BLOCK : mb-head-->
		<script type="text/javascript">
			var lastid = {lastid};
			var mbid = {mbid};
			var usrid = {usrid};
			var edit_time = {edit_time};
			var refreshlim = mb_refresh_limit;
			var mbtype = "{type}";
			var mbRefreshId = setInterval("update_mb()", refreshlim);
		</script>
		<!-- END BLOCK : mb-head-->
		<!-- INCLUDE BLOCK : module-head -->
		<!-- START BLOCK : tinymce-enabled-->
		<!-- END BLOCK : tinymce-enabled-->
		<!-- START BLOCK : tinymce-simple-->
		<!-- END BLOCK : tinymce-simple-->
	</head>

	<body>

		<div id="header-wrapper">
			<div class="container">
					<div class="row">
						<div class="12u">

							<header id="header">
								<h1><a href="/" id="logo">exs.lv</a></h1>
								<nav id="nav">
									<a href="/">Siena</a>
									<a href="/forums">Forums</a>
									<a href="/raksti">Raksti</a>
									<!-- START BLOCK : user-menu-->
									<a href="/pm">Vēstules{new-messages}</a>
									<a href="/say/{currentuser-id}">Miniblogs</a>
									<!-- END BLOCK : user-menu-->
									<a href="/grupas">Grupas</a>
									<a href="/mevents">Notikumi</a>
									<a href="/logout/{logout-hash}">Iziet</a>
								</nav>
							</header>

						</div>
					</div>
				</div>
			</div>

			<div id="main">
				<div class="container">
					<div class="row main-row">
						<div class="12u">

							<section>

								<div id="current-module">

									<!-- START BLOCK : flash-message-->
									<div class="c"></div>
									<div class="mbox {class}" id="flash-message">
										<p><a id="close-flash-message" href="#"><img src="http://img.exs.lv/bildes/fugue-icons/cross-button.png" alt="Aizvērt" title="Aizvērt" width="16" height="16" /></a> {message}</p>
									</div>
									<div class="c"></div>
									<!-- END BLOCK : flash-message-->

									<!-- START BLOCK : profile-menu-->
									<h2>{user-nick}</h2>

									<ul class="tabs">
										<li><a href="/user/{user-id}" class="{active-tab-profile}"><span class="profile">Profils</span></a></li>
										<li><a href="/say/{user-id}" class="{active-tab-miniblog}"><span class="comments">Miniblogs</span></a></li>
										<!-- START BLOCK : profilebox-blog-link-->
										<li><a href="/?c={profile-blogid}" id="l-blog"><span class="pages">Blogs</span></a></li>
										<!-- END BLOCK : profilebox-blog-link-->
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
										<ul id="friendssay-list" class="blockhref">
											<!-- START BLOCK : events-node-->
											<li><a href="{url}"><span class="time-ago">{time}</span> <img class="av" src="{avatar}" alt="" /> <span class="author">{author}{where}</span> {title}&nbsp;[{posts}]<br style="clear: both;" /></a></li>
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


							</section>
						</div>
					</div>
				</div>
			</div>

			<div id="footer-wrapper">
				<div class="container">
					<div class="row">
						<div class="8u">

							<section>
								<a href="/sitemap">Lapas karte</a> | &copy; exs.lv, {current-year}
							</section>
						</div>
					</div>
				</div>
			</div>

			<script type="text/javascript">

				var _gaq = _gaq || [];
				_gaq.push(['_setAccount', 'UA-4190387-2']);
				_gaq.push(['_setDomainName', 'exs.lv']);
				_gaq.push(['_trackPageview']);

				(function() {
					var ga = document.createElement('script');
					ga.type = 'text/javascript';
					ga.async = true;
					ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
					var s = document.getElementsByTagName('script')[0];
					s.parentNode.insertBefore(ga, s);
				})();

			</script>

			<!-- START BLOCK : sharethis-->
			<!-- END BLOCK : sharethis-->

		</body>
	</html>
