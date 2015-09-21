<!DOCTYPE html>
<html lang="lv">
	<head>
		<meta charset="UTF-8">
		<title>{page-title}</title>
		<!-- START BLOCK : meta-description-->
		<meta name="description" content="{description}">
		<!-- END BLOCK : meta-description-->
		<!-- START BLOCK : robots-->
		<meta name="robots" content="{value}">
		<!-- END BLOCK : robots-->
		<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
		<script type="text/javascript">
			var mb_refresh_limit = {mb-refresh-limit};
			var current_user = {currentuser-id};
			var new_msg_count = {new-messages-count};
			var query_timeout = 60000;
			var c_url = "{page-url}";
		</script>
		<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Ubuntu&amp;subset=latin,cyrillic,latin-ext" type="text/css">
		<link rel="stylesheet" href="{static-server}/css/core.css,lol.css" type="text/css">
		<!-- START BLOCK : additional-css-->
		<link rel="stylesheet" href="{static-server}/css/{filename}" type="text/css">
		<!-- END BLOCK : additional-css-->
		<script type="text/javascript" src="{static-server}/js/jquery.min.js,tinycon.min.js,jquery.cookie.js,jquery.fancybox.js,jquery.raty.min.js,jquery.cycle.js,mcp.js,j.js"></script>
		<!-- START BLOCK : tinymce-enabled-->
		<script src="//tinymce.cachefly.net/4.0/tinymce.min.js"></script>
		<script type="text/javascript">
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
		<script src="//tinymce.cachefly.net/4.0/tinymce.min.js"></script>
		<script type="text/javascript">
			tinymce.init({
				selector: "textarea",
				plugins: [
					"autolink lists paste image anchor code"
				],
				toolbar1: "bold italic underline strikethrough | undo redo | bullist numlist | link unlink image blockquote code",
				toolbar2: "",
				toolbar3: "",
				relative_urls: false,
				remove_script_host: false,
				menubar: false,
				statusbar: false,
				toolbar_items_size: 'small',
				content_css: "{static-server}/css/style.css"

			});</script>
		<!-- END BLOCK : tinymce-simple-->
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
	</head>

	<body class="{layout-options}">

		<div id="wrapper">
			<div id="header">
				<div id="logo">
					<div id="tools-bar">
						<ul id="site-links">
							<li><a href="https://exs.lv/">exs.lv community</a></li>
							<li><a href="https://mlol.exs.lv/" title="Mobilā versija" rel="nofollow">Mobilā versija</a></li>
							<li><a href="/img">Bilžu hostings</a></li>
							<li><a href="/sitemap">Lapas karte</a></li>
						</ul>
						{current-date}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#aaa">TeamSpeak:</span> ts.exs.lv
					</div>

					<a href="/" title="League of Legends portāls">
						<div id="header-slider" class="cycle-slideshow" data-cycle-speed="3000" data-cycle-timeout="9000" data-cycle-random="true">
							<img src="//img.exs.lv/lol-headers/header-1.jpg" alt="" />
							<img src="//img.exs.lv/lol-headers/header-2.jpg" alt="" />
							<img src="//img.exs.lv/lol-headers/header-3.jpg" alt="" />
							<img src="//img.exs.lv/lol-headers/header-4.jpg" alt="" />
							<img src="//img.exs.lv/lol-headers/header-5.jpg" alt="" />
							<img src="//img.exs.lv/lol-headers/header-6.jpg" alt="" />
							<img src="//img.exs.lv/lol-headers/header-7.jpg" alt="" />
							<img src="//img.exs.lv/lol-headers/header-8.jpg" alt="" />
							<img src="//img.exs.lv/lol-headers/header-9.jpg" alt="" />
							<img src="//img.exs.lv/lol-headers/header-10.jpg" alt="" />
							<img src="//img.exs.lv/lol-headers/header-11.jpg" alt="" />
							<img src="//img.exs.lv/lol-headers/header-12.jpg" alt="" />
						</div>
					</a>

				</div>
				<div id="top-menu">
					<ul id="top-menu-left">
						<li{cat-sel-1755}><a class="first" href="/">Sākumlapa</a></li>
						<li{cat-sel-1122}><a href="/forums">Forums</a></li>
						<li><a href="https://tops.exs.lv/lol" rel="nofollow">Tops</a></li>
					</ul>
					<!-- START BLOCK : user-menu-->
					<ul id="top-menu-right">
						<li{profile-sel}>
							<a class="first" href="/user/{currentuser-id}">Profils</a>
							<ul>
								<li><a href="/user/edit">Publiskā profila informācija</a></li>
								<li><a href="/user/avatar">Mans avatars</a></li>
								<li><a href="/user/settings">Mani iestatījumi</a></li>
								<li><a href="/user/security">Paroles maiņa</a></li>
								<li><a href="/user/email">E-pasta adreses maiņa</a></li>
								<li><a href="/user/changenick">Mainīt lietotājvārdu</a></li>
							</ul>
						</li>
						<li{gal-sel}><a href="/gallery/{currentuser-id}">Galerija</a></li>
						<!-- START BLOCK : user-modlink-->
						<li{cat-sel-83}><a href="#">Mod</a>
							<ul>
								<li{cat-sel-125}><a href="/banned">Liegumi</a></li>
								<li{cat-sel-1827}><a href="/reports">Sūdzības{reports-count}</a></li>
								<li{cat-sel-255}><a href="/polladmin">Aptaujas</a></li>
								<li{cat-sel-199}><a href="/log">Darbību vēsture</a></li>
							</ul>
						</li>
						<!-- END BLOCK : user-modlink-->
						<li{cat-sel-104}><a href="/pm">Vēstules<span id="new-msg">{new-messages}</span></a></li>

						<!-- START BLOCK : user-approvelink-->
						<li{cat-sel-1756}><a href="/write/list">Raksti{new-approve}</a></li>
						<!-- END BLOCK : user-approvelink-->
						<!-- START BLOCK : user-write-->
						<li{cat-sel-1756}><a href="/write">Raksti</a></li>
						<!-- END BLOCK : user-write-->

						<li{mb-sel}><a href="/say/{currentuser-id}">Miniblogs</a></li>
						<li{cat-sel-585}><a class="notes" href="/piezimes">Piezīmes</a></li>
						<li><a href="/logout/{logout-hash}">Iziet ({currentuser-nick})</a></li>
					</ul>
					<!-- END BLOCK : user-menu-->
					<!-- START BLOCK : login-form-->
					<ul id="top-menu-right">
						<li{cat-sel-106}><a class="first" href="/register">Reģistrēties</a></li>
						<li>
							<form id="login-form" action="{page-loginurl}" method="post">
								<fieldset>
									<input type="hidden" name="xsrf_token" value="{xsrf}" />
									<label>Niks:<input id="login-nick" size="16" name="niks" type="text" /></label>
									<label>Parole:<input id="login-pass" size="16" name="parole" type="password" /></label>
									<label><input name="login-submit" id="login-submit" class="login-submit" value="Ienākt" type="submit" /></label>
								</fieldset>
							</form>
						</li>
						<li>
							<a class="external-login external-dr" title="Ienākt ar draugiem.lv pasi" href="https://api.draugiem.lv/authorize/?app=15005147&amp;hash=6f466204e62a4e68806c19e3bd636794&amp;redirect=https%3A%2F%2Flol.exs.lv%2Fdraugiem-signup%2F" onclick="if(handle=window.open('https://api.draugiem.lv/authorize/?app=15005147&amp;hash=6f466204e62a4e68806c19e3bd636794&amp;redirect=https%3A%2F%2Flol.exs.lv%2Fdraugiem-signup%2F&amp;popup=1','Dr_15005147' ,'width=400, height=400, left='+(screen.width?(screen.width-400)/2:0)+', top='+(screen.height?(screen.height-400)/2:0)+',scrollbars=no')){handle.focus();return false;}"><span>Ienākt</span></a>
						</li>
						<li>
							<a href="/fb-login" class="external-login external-fb" title="Log in with FaceBook"><span>Log in</span></a>
						</li>
					</ul>
					<!-- END BLOCK : login-form-->
				</div>
			</div>
			<div class="c"></div>
			<!-- START BLOCK : flash-message-->
			<div class="mbox {class}" id="flash-message">
				<p><a id="close-flash-message" href="#"><img src="//img.exs.lv/bildes/fugue-icons/cross-button.png" alt="Aizvērt" title="Aizvērt" width="16" height="16" /></a> {message}</p>
			</div>
			<div class="c"></div>
			<!-- END BLOCK : flash-message-->

			<div id="content" class="{layout-options}">
				<div id="inner-content">
					<!-- START BLOCK : page-path-->
					<p id="breadcrumbs">{page-path}</p>
					<!-- END BLOCK : page-path-->
					<!-- START BLOCK : profile-menu-->
					<h1>{user-nick}{user-menu-add}</h1>

					<ul class="tabs">
						<li><a href="/user/{user-id}" class="{active-tab-profile}"><span class="profile user-level-{inprofile-level} user-gender-{inprofile-gender}">Profils</span></a></li>
						<li><a href="/gallery/{user-id}" class="{active-tab-gallery}"><span class="gallery">Galerija</span></a></li>
						<li><a href="/awards/{user-id}" class="{active-tab-awards}"><span class="awards">Medaļas</span></a></li>
						<li><a href="/friends/{user-id}" class="{active-tab-friends}"><span class="friends">Draugi</span></a></li>
						<li><a href="/topics/{user-id}" class="{active-tab-usertopics}"><span class="pages">Raksti</span></a></li>
						<li><a href="/say/{user-id}" class="{active-tab-miniblog}"><span class="comments">Miniblogs</span></a></li>
					</ul>

					<!-- END BLOCK : profile-menu-->
					<!-- INCLUDE BLOCK : module-core-error -->
					<div id="current-module">
						<!-- INCLUDE BLOCK : module-currrent -->
					</div>
					<div class="c"></div>

					<p id="bottom-tools"><a href="javascript:history.back()" class="back">Atpakaļ</a> <a href="#top-menu" class="top">Uz augšu</a></p>
				</div>
			</div>


			<!-- START BLOCK : main-layout-left-->
			<div id="left">

				<!-- START BLOCK : menu-list-->
				<h3>{title}</h3>
				<ul class="menu" id="nav-{topid}">
					<!-- START BLOCK : menu-node-->
					<li{sel}><a href="{url}">{title}</a>
						<!-- START BLOCK : menu-list-sub-->
						<ul>
							<!-- START BLOCK : menu-node-sub-->
							<li{sel}><a href="{url}">{title}</a></li>
							<!-- END BLOCK : menu-node-sub-->
						</ul>
						<!-- END BLOCK : menu-list-sub-->
					</li>
					<!-- END BLOCK : menu-node-->
				</ul>
				<!-- END BLOCK : menu-list-->

				<!-- START BLOCK : notification-list-->
				<h3>Tavi notikumi</h3>
				<div class="box">
					{out}
				</div>
				<!-- END BLOCK : notification-list-->

				<h3>Jaunākais portālā</h3>
				<div class="box">
					<ul class="tabs">
						<li><a href="/latest.php" class="{pages-selected}remember-pages ajax"><span class="comments">Raksti</span></a></li>
						<li><a href="/latest.php?type=images" class="{gallery-selected}remember-gallery ajax"><span class="gallery">Bildes</span></a></li>
					</ul>
					<div class="c"></div>
					<div id="lat" class="ajaxbox">{latest-noscript}</div>
				</div>

				<!-- START BLOCK : groups-l-list-->
				<h3>Jaunākās grupas</h3>
				<div class="box">
					<p>
						<!-- START BLOCK : groups-l-node-->
						<a href="/group/{id}">{title}</a><br />
						<!-- END BLOCK : groups-l-node-->
					</p>
					<a href="/grupas">Visas grupas &raquo;</a>
				</div>
				<!-- END BLOCK : groups-l-list-->
				<h3>Meklētājs</h3>
				<div class="box">
					Meklēt lapā ar <a href="/search/">google</a>:
					<form method="get" action="/search/" id="search-form">
						<fieldset>
							<input type="hidden" name="cx" value="014557532850324448350:00ymkcfoxlo" />
							<input type="hidden" name="cof" value="FORID:11" />
							<input type="hidden" name="ie" value="UTF-8" />
							<input class="text" name="q" size="16" type="text" value="" />
							<input value="Meklēt" class="submit button primary" type="submit" />
						</fieldset>
					</form>
				</div>

				<!-- START BLOCK : user-top-->
				<h3>Šodien aktīvākie</h3>
				<div class="box">
					<ul id="today-top">
						<!-- START BLOCK : user-top-node-->
						<li><a href="{url}"><img class="av" src="{avatar}" alt="" />{user}</a><span class="count">({today})</span></li>
						<!-- END BLOCK : user-top-node-->
					</ul>
					<div class="c"></div>
				</div>
				<!-- END BLOCK : user-top-->

			</div>
			<!-- END BLOCK : main-layout-left-->


			<!-- START BLOCK : main-layout-right-->
			<div id="right">

				<!-- START BLOCK : profile-box-->
				<h3>{profile-nick}</h3>
				<div class="box">
					<a href="{url}">
						<img id="profile-image" class="pimg-{profile-id}" src="{avatar}" alt="{profile-nick}" />
					</a>
					{profile-top-awards}
					<!-- START BLOCK : profilebox-pm-link-->
					<a href="/pm/write/?to={profile-id}" id="l-pm">Nosūtīt PM</a><br />
					<!-- END BLOCK : profilebox-pm-link-->
					<!-- START BLOCK : profilebox-warn-->
					<a href="/warns/{profile-id}" id="l-warn"{class}>Brīdinājumi{profile-warns}</a><br />
					<!-- END BLOCK : profilebox-warn-->
					<!-- START BLOCK : profilebox-blog-link-->
					<!--<a href="{url}" id="l-blog">Blogs&nbsp;({count})</a><br />-->
					<!-- END BLOCK : profilebox-blog-link-->
					<!-- START BLOCK : profilebox-twitter-link-->
					<a rel="nofollow" href="https://twitter.com/{twitter}" id="l-twitter">{twitter}</a><br />
					<!-- END BLOCK : profilebox-twitter-link-->
					<!-- START BLOCK : profilebox-yt-link-->
					<!-- END BLOCK : profilebox-yt-link-->
					<div class="c"></div>
				</div>
				<!-- END BLOCK : profile-box-->

				<!-- START BLOCK : mb-box-->
				<h3>Mini blogi{miniblog-add}</h3>

				<div class="box">

					<!-- START BLOCK : mb-tabs-->
					<ul class="tabs">
						<li><a href="/mb-latest?pg=0&amp;tab=all" class="{all-selected}mbs-all ajax"><span class="comments">Visi</span></a></li>
						<li><a href="/mb-latest?pg=0&amp;tab=friends" class="{friends-selected}mbs-friends ajax"><span class="friends">Draugu</span></a></li>
					</ul>
					<div class="c"></div>
					<!-- END BLOCK : mb-tabs-->
					<div id="miniblog-block" class="ajaxbox">{out}</div>
				</div>

				<!-- END BLOCK : mb-box-->

				<!-- START BLOCK : poll-box-->
				<h3>Aptauja</h3>
				<div class="box">
					<p><strong>{poll-title}</strong></p>
					<!-- START BLOCK : poll-answers-->
					<ol class="poll-answers">
						<!-- START BLOCK : poll-answers-node-->
						<li>{poll-answer-question}<div><span>{poll-answer-percentage}%</span><div style="width:{poll-answer-percentage}%"></div></div></li>
						<!-- END BLOCK : poll-answers-node-->
					</ol>
					Balsojuši: {poll-totalvotes}<br />
					<a href="{ppage-id}">Komentāri</a> | <a href="/aptaujas">Aptaujas</a>
					<!-- END BLOCK : poll-answers-->
					<!-- START BLOCK : poll-questions-->
					<form name="poll" method="post" action="">
						<fieldset>
							<!-- START BLOCK : poll-error-->
							<p>{poll-error}</p>
							<!-- END BLOCK : poll-error-->
							<!-- START BLOCK : poll-options-->
							<ol id="poll-questions">
								<!-- START BLOCK : poll-options-node-->
								<li><label><input type="radio" name="questions" value="{poll-options-id}" /> {poll-options-question}</label></li>
								<!-- END BLOCK : poll-options-node-->
							</ol>
							<input type="submit" name="vote" value="Balsot!" class="button primary" />
							<!-- END BLOCK : poll-options-->
						</fieldset>
					</form>
					<!-- END BLOCK : poll-questions-->
				</div>
				<!-- END BLOCK : poll-box-->

				<!-- START BLOCK : lol-top-->
				<h3>LoL tops</h3>
				<div class="box">
					<table id="lol-top">
						<tr>
							<th>LoL niks</th>
							<th class="server">Server</th>
							<th class="lks">LKS</th>
						</tr>
						<!-- START BLOCK : lol-top-node-->
						<tr>
							<td>{lol_nick}</td>
							<td class="server">{server}</td>
							<td class="lks">{lks}</td>
						</tr>
						<!-- END BLOCK : lol-top-node-->
					</table>
				</div>
				<!-- END BLOCK : lol-top-->
				<p style="text-align: right"><a href="/top">Viss tops &raquo;</a></p>

			</div>
			<!-- END BLOCK : main-layout-right-->
			<div class="c"></div>

			<div id="footer">
				<div id="online-users">
					Lapu šobrīd skatās {page-onlinetotal} lietotāji, no tiem reģistrētie:<br /><span style="font-size:10px;">{page-onlineusers}</span>
				</div>
				<div class="infoblock">
					<div class="inner">
						Jaunākie raksti: {footer-topics}
					</div>
				</div>
				<div class="infoblock">
					<div class="inner">
						Jaunākais miniblogos: {footer-mb}
					</div>
				</div>
				<div class="infoblock">
					<div class="inner">
						<p>&copy; <a href="https://openidea.lv/" title="Mājas lapas izstrāde un uzturēšana" rel="nofollow">SIA Open Idea</a>, 2013-{current-year}</p>
						<p>lol.exs.lv ir neoficiāls League of Legends spēlētāju forums</p>
					</div>
				</div>
				<div class="infoblock">
					<div class="inner">
						<ul id="internal-links">
							<li><a href="//exs.lv/read/lietosanas-noteikumi">Lietošanas noteikumi</a></li>
							<li><a href="/sitemap">Lapas karte</a></li>
							<li><a href="//exs.lv/reklama">Reklāma portālā</a></li>
						</ul>
					</div>
				</div>
				<div class="c"></div>
			</div>
		</div>

		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-4190387-2', 'auto');
		  ga('send', 'pageview');

		</script>

	</body>
</html>

