<!DOCTYPE html>
<html lang="lv">
<head>
<meta charset="UTF-8">
<title>{page-title}</title>
<!-- START BLOCK : meta-description-->
<meta name="description" content="{description}" />
<!-- END BLOCK : meta-description-->
<!-- START BLOCK : opengraph-->
<meta property="og:title" content="{title}" />
<meta property="og:type" content="{type}" />
<meta property="og:url" content="{url}" />
<meta property="og:image" content="{image}" />
<!-- END BLOCK : opengraph-->
<!-- START BLOCK : robots-->
<meta name="robots" content="{value}">
<!-- START BLOCK : robots-->
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
<link rel="alternate" type="application/rss+xml" title="RSS jaunumi" href="http://feeds.feedburner.com/runes" />
<script type="text/javascript">
	var mb_refresh_limit = {mb-refresh-limit};
	var current_user = {currentuser-id};
	var new_msg_count = {new-messages-count};
	var query_timeout = 60000;
	var c_url = "{page-url}";
</script>
<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Ubuntu&amp;subset=latin,cyrillic,latin-ext" type="text/css" />
<link rel="stylesheet" href="{static-server}/css/core.css{add-css},skin{page-skinid}.css" type="text/css" />
<script type="text/javascript" src="{static-server}/js/jquery.min.js{jquery-tools},tinycon.min.js,jquery.cookie.js,jquery.fancybox.js,jquery.raty.min.js,swfobject.js,j.js"></script>
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
		content_css : "{static-server}/css/style.css",

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
			"autolink lists paste image anchor code paste"
		],

		toolbar1: "bold italic underline strikethrough | undo redo | bullist numlist | link unlink image blockquote code",
		toolbar2: "",
		toolbar3: "",
		relative_urls: false,
		remove_script_host: false,
		menubar: false,
		statusbar: false,
		toolbar_items_size: 'small',
		content_css : "{static-server}/css/style.css"

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
	var mbRefreshId = setInterval("update_mb()",refreshlim);
</script>
<!-- END BLOCK : mb-head-->
<!-- INCLUDE BLOCK : module-head -->
</head>

<body{onload} class="{layout-options}">
<div id="wrapper">
<div id="container">
<div id="header"{page-persona}>
	 <div id="header-overlay">
		<div id="logo">
			<div id="header-stuff">{ad-top}</div>
			<a id="exs-logo" href="/" title="Uz sākumlapu">exs.lv</a>
			<div id="tools-bar">
			<ul id="site-links">
				<li><a href="/img">Bilžu hostings</a></li>
				<li><a href="http://m.exs.lv/" title="Mobilā versija" rel="nofollow">Mobilā versija</a></li>
				<li><a href="/junk" title="Bilžu sadaļa">/junk</a></li>
				<li><a href="http://rp.exs.lv/" title="MTA San Andreas Roleplay serveris un forums" rel="nofollow">rp.exs.lv</a></li>
				<li><a href="http://lol.exs.lv/" title="League of Legends forums" rel="nofollow">lol.exs.lv</a></li>
				<li><a href="http://coding.lv/" title="Mājas lapu veidošanas un programmēšanas forums" rel="nofollow">coding.lv</a></li>
				<li><a href="/statistika" title="Statistika">Statistika</a></li>
				<li><a href="/servers" title="Latvijas Counter Strike serveru saraksts">CS Serveri</a></li>
				<li><a href="/flash-speles" title="Online flash spēles">Flash spēles</a></li>
			</ul>
			{current-date}
			</div>
		</div>
		<div id="top-menu">
			<ul id="top-menu-left">
				<li{cat-sel-1}><a href="/" title="Uz sākumlapu">Jaunumi</a></li>
				<li{cat-sel-101}><a href="/forums">Forums</a></li>
				<li{cat-sel-110}><a href="/blogs">Blogi</a></li>
				<li{cat-sel-599}><a href="/runescape" title="RuneScape">RS{idb-count}</a></li>
				<li{cat-sel-81}><a href="/speles">Spēles</a></li>
				<li{cat-sel-80}><a href="/filmas">Filmas</a></li>
				<li{cat-sel-247}><a href="/raksti">Raksti</a></li>
			</ul>
			<!-- START BLOCK : user-menu-->
			<ul id="top-menu-right">
				<li{profile-sel}><a href="/user/{currentuser-id}">Profils</a></li>
				<li{cat-sel-319}><a href="/grupas">Grupas</a>
					<!-- START BLOCK : mygroups-->
					<ul id="user-group-menu">
						<!-- START BLOCK : myg-node-->
						<li><a href="/group/{id}"><img src="http://img.exs.lv/userpic/small/{avatar}" width="28" height="28" alt="" />{title}{add}</a></li>
						<!-- END BLOCK : myg-node-->
					</ul>
					<!-- END BLOCK : mygroups-->
				</li>
				<li{gal-sel}><a href="/gallery/{currentuser-id}">Galerija</a></li>
				<!-- START BLOCK : user-modlink-->
				<li{cat-sel-83}><a href="#">Mod</a>
					<ul>
						<li{cat-sel-83}><a href="/moderatoriem">Forums</a></li>
						<li{cat-sel-125}><a href="/banned">Bloķētie lietotāji</a></li>
						<li{cat-sel-1132}><a href="/checkform">Lietotāju meklēšana</a></li>
						<li{cat-sel-206}><a href="/?c=206">Random fakti</a></li>
						<li{cat-sel-199}><a href="/log">Administrācijas darbības</a></li>
						<li{cat-sel-255}><a href="/polladmin">Aptaujas</a></li>
						<li{cat-sel-229}><a href="/wallpaper_admin">Wallpapers</a></li>
						<li{cat-sel-451}><a href="/smslog">SMS maksājumi</a></li>
						<li{cat-sel-331}><a href="/?c=331">Karātavas</a></li>
						<li{cat-sel-539}><a href="/memcached">Cache</a></li>
						<li{cat-sel-763}><a href="/serverinfo">Servera statuss</a></li>
						<li{cat-sel-586}><a href="/csmaps">CS mapimg</a></li>
						<li{cat-sel-642}><a href="/racontest">RA konkurss</a></li>
						<li{cat-sel-794}><a href="/user_decos">Apbalvojumu ikonas</a></li>
					</ul>
				</li>
				<!-- END BLOCK : user-modlink-->
				<li{cat-sel-104}><a href="/pm">Vēstules<span id="new-msg">{new-messages}</span></a></li>
				<!-- START BLOCK : user-approvelink-->
				<li{cat-sel-116}><a href="/write/list">Raksti{new-approve}</a></li>
				<!-- END BLOCK : user-approvelink-->
				<!-- START BLOCK : user-write-->
				<li{cat-sel-116}><a href="/write">Raksti</a></li>
				<!-- END BLOCK : user-write-->
				<li{cat-sel-111}><a href="/myblog">Blogs</a></li>
				<li{mb-sel}><a href="/say/{currentuser-id}">Miniblogs</a></li>
				<li{cat-sel-585}><a href="/piezimes" title="Piezīmes"><img src="http://img.exs.lv/bildes/fugue-icons/notebook.png" width="16" height="16" alt="Piezīmes" /></a></li>
				<li><a href="/logout">Iziet ({currentuser-nick})</a></li>
			</ul>
			<!-- END BLOCK : user-menu-->
			<!-- START BLOCK : login-form-->
			<form id="login-form" action="{page-loginurl}" method="post">
				<fieldset>
					<input type="hidden" name="xsrf_token" value="{xsrf}" />
					<span{cat-sel-106}><a href="/register">Reģistrēties</a></span>
					<label>Niks:<input id="login-nick" size="16" name="niks" type="text" /></label>
					<label>Parole:<input id="login-pass" size="16" name="parole" type="password" /></label>
					<label><input name="login-submit" id="login-submit" class="login-submit" value="Ienākt" type="submit" /></label>
					<a rel="nofollow" class="external-login external-dr" title="Ienākt ar draugiem.lv pasi" href="http://api.draugiem.lv/authorize/?app=15005147&amp;hash=291e891358c8819a234e6d96b3a0d449&amp;redirect=http%3A%2F%2Fexs.lv%2Fdraugiem-signup%2F" onclick="if(handle=window.open('http://api.draugiem.lv/authorize/?app=15005147&amp;hash=291e891358c8819a234e6d96b3a0d449&amp;redirect=http%3A%2F%2Fexs.lv%2Fdraugiem-signup%2F&amp;popup=1','Dr_15005147' ,'width=400, height=400, left='+(screen.width?(screen.width-400)/2:0)+', top='+(screen.height?(screen.height-400)/2:0)+',scrollbars=no')){handle.focus();return false;}"><span>Ienākt</span></a>

					<a rel="nofollow" href="/fb-login" class="external-login external-fb" title="Log in with FaceBook"><span>Log in</span></a>

				</fieldset>
			</form>
			<!-- END BLOCK : login-form-->
		</div>
	</div>
</div>
<div class="c"></div>
<!-- START BLOCK : flash-message-->
<div class="mbox {class}" id="flash-message">
	<p><a id="close-flash-message" href="#"><img src="http://img.exs.lv/bildes/fugue-icons/cross-button.png" alt="Aizvērt" title="Aizvērt" width="16" height="16" /></a> {message}</p>
</div>
<div class="c"></div>
<!-- END BLOCK : flash-message-->

<div id="content" class="{layout-options}">
	<!-- START BLOCK : page-path-->
	<p id="breadcrumbs">{page-path}</p>
	<!-- END BLOCK : page-path-->

	<!-- START BLOCK : profile-menu-->
	<h1>{user-nick}{user-menu-add}</h1>

	{ad-468}

	<ul class="tabs">
		<li><a href="/user/{user-id}" class="{active-tab-profile}"><span class="profile user-level-{inprofile-level} user-gender-{inprofile-gender}">Profils</span></a></li>
		<li><a href="/gallery/{user-id}" class="{active-tab-gallery}"><span class="gallery">Galerija</span></a></li>
		<li><a href="/awards/{user-id}" class="{active-tab-awards}"><span class="awards">Medaļas</span></a></li>
		<li><a href="/friends/{user-id}" class="{active-tab-friends}"><span class="friends">Draugi</span></a></li>
		<li><a href="/bookmarks/{user-id}" class="{active-tab-bookmarks}"><span class="bookmarks">Izlase</span></a></li>
		<li><a href="/topics/{user-id}" class="{active-tab-usertopics}"><span class="pages">Raksti</span></a></li>
		<li><a href="/say/{user-id}" class="{active-tab-miniblog}"><span class="comments">Miniblogs</span></a></li>
	</ul>

	<!-- END BLOCK : profile-menu-->

	<!-- INCLUDE BLOCK : module-core-error -->
	<div id="current-module">
		<!-- INCLUDE BLOCK : module-currrent -->
	</div>
	{contentz}
	<div class="c"></div>
	<!-- START BLOCK : ads-google-->
	{ad-468}
	<!-- END BLOCK : ads-google-->
	<!-- START BLOCK : ads-google-wide-->
	{ad-728}
	<!-- END BLOCK : ads-google-wide-->
	<p id="bottom-tools"><a href="javascript:history.back()" class="back">Atpakaļ</a> <a href="#top-menu" class="top">Uz augšu</a></p>
</div>

<!-- START BLOCK : main-layout-left-->
<div id="left">

	<!-- START BLOCK : movie-search-->
	<h3>Meklēt filmu</h3>
	<div class="box">
		<form id="movie-search" method="get" action="/filmas/search">
			<!-- START BLOCK : genre-node-->
			<label style="font-size: 10px;line-heigh: 13px;"><input style="width:11px;height:11px;padding: 0;margin:2px;" type="checkbox" name="genres[]" value="{genre}"{checked} />{translated}</label><br />
			<!-- START BLOCK : genre-node-->

			<input type="submit" value="Meklēt" class="button primary small" />
		</form>
	</div>
	<!-- END BLOCK : movie-search-->

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
	<!-- START BLOCK : rs-menu-list-->
	<h3>RuneScape</h3>
	<ul class="menu">
		<li{sel-102}><a href="/{strid-102}">{title-102}</a></li>
		<li{sel-160}><a href="/{strid-160}">{title-160}</a></li>
		<li{sel-4}><a href="/{strid-4}">{title-4}</a></li>
		<li{sel-194}><a href="/{strid-194}">{title-194}</a></li>
		<li{sel-792}><a href="/{strid-792}">{title-792}</a></li>
		<li{sel-195}><a style="margin-top:12px;" href="/{strid-195}">{title-195}</a></li>
		<li{sel-791}><a href="/{strid-791}">{title-791}</a></li>
		<li{sel-789}><a href="/{strid-789}">{title-789}</a></li>
		<li{sel-793}><a href="/{strid-793}">{title-793}</a></li>
		<li{sel-788}><a style="margin-top:12px;" href="/{strid-788}">{title-788}</a></li>
		<li{sel-787}><a href="/{strid-787}">{title-787}</a></li>
		<li{sel-790}><a href="/{strid-790}">{title-790}</a></li>
		<li{sel-5}><a style="margin-top:12px;" href="/{strid-5}">{title-5}</a></li>
		<li{sel-1087}><a href="/{strid-1087}">{title-1087}</a></li>
		<li{sel-346}><a href="/{strid-346}">{title-346}</a></li>
		<li{sel-536}><a href="/{strid-536}">{title-536}</a></li>
	</ul>
	<!-- END BLOCK : rs-menu-list-->

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
			<a href="{link}">{title}</a><br />
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
				<input type="hidden" name="cx" value="014557532850324448350:fe0imkmdxam" />
				<input type="hidden" name="cof" value="FORID:11" />
				<input type="hidden" name="ie" value="UTF-8" />
				<input class="text" name="q" size="16" type="text" value="" />
				<input value="Meklēt" class="submit button primary" type="submit" />
			</fieldset>
		</form>
	</div>
	<!--<h3>Spēļu serveri</h3>
	<div class="box monitor">
		<ul class="tabs">
			<li><a rel="nofollow" href="/cache/cs_monitor.html" class="active remember-cs ajax"><span class="game-cs">Counter Strike</span></a></li>
		</ul>
		<div id="cs-content" class="ajaxbox">{latest-cs}</div>
		<div class="c"></div>
	</div>

	<div class="box monitor">
		<ul class="tabs">
			<li><a rel="nofollow" href="/cache/mta_monitor.html" class="active remember-mta ajax"><span class="game-mta">MTA:RP</span></a></li>
		</ul>
		<div id="mta-content" class="ajaxbox">{latest-mta}</div>
		<div class="c"></div>
	</div>-->

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

	<h3>Nejaušs fakts</h3>
	<div class="box">
		<ul class="tabs">
			<li><a href="/fact" class="{fact-all-selected} remember-fact-all ajax"><span class="fact-all">Spēles</span></a></li>
			<li><a href="/fact/rs" class="{fact-rs-selected} remember-fact-rs ajax"><span class="fact-rs">RS</span></a></li>
		</ul>
		<div class="c"></div>
		<div id="random-fact" class="ajaxbox">{random-fact}</div>
	</div>

</div>
<!-- END BLOCK : main-layout-left-->

<!-- START BLOCK : main-layout-right-->
<div id="right">

	<!-- START BLOCK : junk-info-->
	<p><a href="/adm">Attēlu apstiprināšana{count}</a></p>
	<!-- END BLOCK : junk-info-->

	<!-- START BLOCK : profile-box-->
	<h3>{profile-nick}{custom_title}</h3>
	<div class="box">
		<a href="{url}"><img id="profile-image" class="pimg-{profile-id}" src="{avatar}" alt="{profile-nick}" /></a><br />
		{profile-top-awards}
		<!-- START BLOCK : profilebox-pm-link-->
		<a href="/pm/write/?to={profile-id}" id="l-pm">Nosūtīt PM</a><br />
		<!-- END BLOCK : profilebox-pm-link-->
		<!-- START BLOCK : profilebox-warn-->
		<a href="/warns/{profile-id}" id="l-warn"{class}>Brīdinājumi{profile-warns}</a><br />
		<!-- END BLOCK : profilebox-warn-->
		<!-- START BLOCK : profilebox-blog-link-->
		<a href="{url}" id="l-blog">Blogs&nbsp;({count})</a><br />
		<!-- END BLOCK : profilebox-blog-link-->
		<!-- START BLOCK : profilebox-twitter-link-->
		<a rel="nofollow" href="http://twitter.com/{twitter}" id="l-twitter">{twitter}</a><br />
		<!-- END BLOCK : profilebox-twitter-link-->
		<!-- START BLOCK : profilebox-yt-link-->
		<a href="/youtube/{profile-id}/{yt-slug}" id="l-yt"><span class="yt">{yt-name}</span></a><br />
		<!-- END BLOCK : profilebox-yt-link-->
		<div class="c"></div>
	</div>
	<!-- END BLOCK : profile-box-->
	<!-- START BLOCK : group-box-->
	<h3>{group-title}</h3>
	<div class="box">
		<a href="/group/{group-id}"><img id="profile-image" src="http://img.exs.lv/userpic/large/{group-av}" alt="{group-alt}" /></a>
	</div>
	<!-- END BLOCK : group-box-->

	<!-- START BLOCK : friendssay-box-->
	<h3>Miniblogi{miniblog-add}</h3>

	<div class="box">

		<!-- START BLOCK : friendssay-tabs-->
		<ul class="tabs">
			<li><a href="/mb-latest?pg=0" class="{all-selected}remember-all ajax"><span class="comments">Visi</span></a></li>
			<li><a href="/mb-latest?pg=0&amp;friendmb=true" class="{friends-selected}remember-friends ajax"><span class="friends">Draugu</span></a></li>
		</ul>
		<div class="c"></div>
		<!-- END BLOCK : friendssay-tabs-->
		<div id="miniblog-block" class="ajaxbox">{out}</div>
	</div>

	<!-- END BLOCK : friendssay-box-->

	<!-- START BLOCK : side-junk-->
	<h3>/junk</h3>
	<div class="box">
		<!-- START BLOCK : side-junk-node-->
		<a style="float: left;position:relative;width: 50%;padding:0;margin:0;text-align: center;" href="http://exs.lv/junk/{id}" title="{title}">
			<img src="http://img.exs.lv{thb}" alt="" class="av" style="float: left;width: 90%;" />
			<span style="position: absolute;left: 10px; top: 10px; color: #fff;background: #555;padding:1px 4px">{posts}</span>
		</a>
		<!-- END BLOCK : side-junk-node-->
		<div class="c"></div>
	</div>
	<!-- END BLOCK : side-junk-->

	<!-- START BLOCK : blog-latest-list-->
	{html}
	<!-- END BLOCK : blog-latest-list-->
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

	<!-- START BLOCK : daily-wallpaper-->
	<h3>Dienas tapete</h3>
	<div id="walpaper" class="box">
		<a href="http://img.exs.lv/dati/wallpapers/{wallpaper-image}">
			<img src="http://img.exs.lv/dati/wallpapers/thb/{wallpaper-image}" alt="dienas ekrāntapete" />
		</a><br />
		<a href="/wallpapers">Tapetes</a>
	</div>
	<!-- END BLOCK : daily-wallpaper-->

	<div id="fansBlock" style="width:205px"></div>
	<script type="text/javascript" src="http://www.draugiem.lv/api/api.js" charset="utf-8"></script>
	<script type="text/javascript">
		var fans = new DApi.BizFans({
			name:'exs.lv',
			showFans:1,
			count:9,
			showSay:0,
			saycount:0
		});
		fans.append('fansBlock');
	</script>

</div>
<!-- END BLOCK : main-layout-right-->
<div class="c"></div>
</div>
<div id="footer">
<div id="online-users">
	<ul id="ucl"><li id="ucd"></li><li class="user"><a href="/lietotaji/klase/0">Lietotājs</a></li><li class="editor"><a href="/lietotaji/klase/3">Rakstu autors</a></li><li class="moder"><a href="/lietotaji/klase/2">Moderators</a></li><li class="admin"><a href="/lietotaji/klase/1">Administrators</a></li></ul>
	Lapu šobrīd skatās {page-onlinetotal} lietotāji, no tiem reģistrētie:<br /><span id="online-list">{page-onlineusers}</span>
</div>
<div class="infoblock">
	Jaunākie raksti: {footer-topics}
</div>
<div class="infoblock">
	Jaunākais miniblogos: {footer-mb}
</div>
<div class="infoblock">
	<p>&copy; <a href="http://openidea.lv/" title="Mājas lapas izstrāde un uzturēšana" rel="nofollow">SIA Open Idea</a>, 2005-2013</p>
	<p>Juridiskā adrese: Sporta iela 7, Ikšķile, LV-5052<br />Reģ. nr. 40103293710</p>
	<p>E-pasts: info@exs.lv<br />Tālrunis: +371 28690182<br />Mājas lapu izstrāde un hostings.</p>
</div>
<div class="infoblock">
	<ul id="internal-links">
		<li><a href="/read/lietosanas-noteikumi">Lietošanas noteikumi</a></li>
		<li><a href="/sitemap">Lapas karte</a></li>
		<li><a href="/reklama">Reklāma portālā</a></li>
	</ul>
	<p>Teamspeak 3:<br />ts.exs.lv</p>
</div>
<div class="c"></div>
</div>
</div>

<!-- START BLOCK : async-call -->
<div id="async-placeholder"></div>
<script>
$(document).ready(function() {
	$('#async-placeholder').html('<iframe width="1" height="1" scrolling="no" border="0" allowTransparency="true" frameborder="0" src="/async" style="padding:0;margin:0;border:0;overflow:hidden"></iframe>');
});
</script>
<!-- END BLOCK : async-call -->

<!-- sekomums.lv -->
<script type="text/javascript" src="//sekomums.lv/cb.lv.js" charset="UTF-8"></script>
<script type="text/javascript" src="//www.draugiem.lv/api/api.js"></script>
<script type="text/javascript">function r(f){ /in/.test(document.readyState)?setTimeout('r('+f+')',9):f() }; r(function(){ new ConversionsBox("Tev%20pat%C4%ABk%20%C5%A1%C4%AB%20lapa%3F%20Seko%20mums%20Draugiem.lv!","exs.lv"); });</script>

<!-- smartad.eu -->
<script type='text/javascript'>/* <![CDATA[ */
	var _smartad = _smartad || new Object(); _smartad.page_id=Math.floor(Math.random()*10000001);
	if(!_smartad.prop) { _smartad.prop='screen_width='+(window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth)+unescape('%26screen_height=')+(window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight)+unescape('%26os=')+navigator.platform+unescape('%26refurl=')+encodeURIComponent(document.referrer||'')+unescape('%26pageurl=')+encodeURIComponent(document.URL||'')+unescape('%26rnd=')+ new Date().getTime();}
	(function() {
		if (_smartad.space){
			_smartad.space += ',8149d5ee-611f-4247-9dbd-5a2dcb6e5529';
		}else{
			_smartad.space = '8149d5ee-611f-4247-9dbd-5a2dcb6e5529';
			_smartad.type='onload';
			var f=function(){
				var d = document, b = d.body || d.documentElement || d.getElementsByTagName('BODY')[0],n = b.firstChild, s = d.createElement('SCRIPT');
				s.type = 'text/javascript',s.language = 'javascript',s.async = true,s.charset='UTF-8';
				s.src=location.protocol+'//serving.bepolite.eu/script?space='+_smartad.space+unescape('%26type=')+_smartad.type+unescape('%26page_id=')+_smartad.page_id+unescape('%26')+_smartad.prop;
				n?b.insertBefore(s, n):b.appendChild(s);
			};
			if(document.readyState==='complete'){
				f();
				delete _smartad.space;
			}else{
				if(window.addEventListener){
					window.addEventListener('load',f,false);
				}else if(window.attachEvent){
					window.attachEvent('onload',f);
				}
			}
		}
	})();
/* ]]> */</script>

<!-- google analytics -->
<script>

var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-4190387-2']);
_gaq.push(['_setDomainName', 'exs.lv']);
_gaq.push(['_trackPageview']);

(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();

</script>

</body>
</html>
