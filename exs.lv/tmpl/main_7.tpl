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
<link rel="stylesheet" href="/css/core.css{add-css},lol.css" type="text/css" />
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
<script type="text/javascript">
	var mb_refresh_limit = {mb-refresh-limit};
	var current_user = {currentuser-id};
	var new_msg_count = {new-messages-count};
	var query_timeout = 60000;
	var c_url = "{page-url}";
</script>
<script type="text/javascript" src="/js/jquery.min.js{jquery-tools},tinycon.min.js,jquery.cookie.js,jquery.fancybox.js,jquery.raty.min.js,j.js"></script>
<!-- START BLOCK : tinymce-enabled-->
<script type="text/javascript" src="/tiny_mce/jquery.tinymce.js"></script>
<script type="text/javascript">
	$().ready(function() {
		$('textarea').tinymce({
			script_url : '/tiny_mce/tiny_mce.js',
			theme : "advanced",
			language : "lv",
			skin : "o2k7",
			skin_variant : "{tinymce_skin_variant}",
			plugins : "autolink,lists,style,table,advimage,inlinepopups,paste,nonbreaking",
			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect",
			theme_advanced_buttons2 : "cut,copy,paste,pastetext,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image",
			theme_advanced_buttons3 : "tablecontrols,|,removeformat,|,code,|,cleanup",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "none",
			theme_advanced_resizing : true,
			theme_advanced_blockformats : "p,h2,h3,h4,h5,code,blockquote",
			content_css : "/css/style.css",
			convert_fonts_to_spans : true,
			relative_urls : false,
			paste_remove_styles : true,
			paste_auto_cleanup_on_paste : true,
			nonbreaking_force_tab : true
		});
	});
</script>
<!-- END BLOCK : tinymce-enabled-->
<!-- START BLOCK : tinymce-simple-->
<script type="text/javascript" src="/tiny_mce/jquery.tinymce.js"></script>
<script type="text/javascript">
	$().ready(function() {
		$('textarea').tinymce({
			script_url : '/tiny_mce/tiny_mce.js',
			theme : "advanced",
			language : "lv",
			skin : "o2k7",
			skin_variant : "{tinymce_skin_variant}",
			plugins : "autolink,lists,style,inlinepopups,paste,nonbreaking",
			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,bullist,undo,redo,link,unlink,image,code,blockquote",
			theme_advanced_buttons2 : "",
			theme_advanced_buttons3 : "",
			theme_advanced_toolbar_location : "bottom",
			theme_advanced_toolbar_align : "center",
			content_css : "/css/style.css",
			convert_fonts_to_spans : true,
			relative_urls : false,
			paste_remove_styles : true,
			nonbreaking_force_tab : true,
			paste_auto_cleanup_on_paste : true
		});
	});
</script>
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
<body class="{layout-options}">

<div id="wrapper">
	<div id="container">
		<div id="header">
			<div id="header-overlay">
				<div id="logo">
					<div id="header-stuff-code"></div>
					<a id="exs-logo" href="/" title="League of Legends forums">lol.exs.lv</a>
					<div id="tools-bar">
						<ul id="site-links">
							<li><a href="http://exs.lv/">exs.lv community</a></li>
							<li><a href="http://m.lol.exs.lv/" title="Mobilā versija" rel="nofollow">Mobilā versija</a></li>
							<li><a href="/img">Bilžu hostings</a></li>
							<li><a href="/sitemap">Lapas karte</a></li>
						</ul>
						{current-date}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#aaa">TeamSpeak:</span> ts.exs.lv
					</div>
				</div>
				<div id="top-menu">
					<ul id="top-menu-left">
						<li{cat-sel-1122}><a class="first" href="/">Forums</a></li>
					</ul>
					<!-- START BLOCK : user-menu-->
					<ul id="top-menu-right">
						<li{profile-sel}><a class="first" href="/user/{currentuser-id}">Profils</a></li>
						<li{gal-sel}><a href="/gallery/{currentuser-id}">Galerija</a></li>
						<!-- START BLOCK : user-modlink-->
						<li{cat-sel-83}><a href="#">Mod</a>
							<ul>
								<li{cat-sel-125}><a href="/banned">Bloķētie lietotāji</a></li>
								<li{cat-sel-255}><a href="/polladmin">Aptaujas</a></li>
								<li{cat-sel-199}><a href="/log">Administrācijas darbības</a></li>
							</ul>
						</li>
						<!-- END BLOCK : user-modlink-->
						<li{cat-sel-104}><a href="/pm">Vēstules<span id="new-msg">{new-messages}</span></a></li>
						<!-- START BLOCK : user-approvelink-->

						<!--<li{cat-sel-319}><a href="/grupas">Grupas</a>
							<!-- START BLOCK : mygroups-->
							<ul id="user-group-menu">
								<!-- START BLOCK : myg-node-->
								<li><a href="/group/{id}"><img src="http://exs.lv/dati/bildes/u_small/{avatar}" width="28" height="28" alt="" />{title}{add}</a></li>
								<!-- END BLOCK : myg-node-->
							</ul>
							<!-- END BLOCK : mygroups-->
						</li>-->

						<!-- END BLOCK : user-approvelink-->
						<!-- START BLOCK : user-write-->

						<!-- END BLOCK : user-write-->
						<li{mb-sel}><a href="/say/{currentuser-id}">Miniblogs</a></li>
						<li{cat-sel-585}><a href="/piezimes">Piezīmes</a></li>
						<li><a href="/logout">Iziet ({currentuser-nick})</a></li>
					</ul>
					<!-- END BLOCK : user-menu-->
					<!-- START BLOCK : login-form-->
					<ul id="top-menu-right">
						<li{cat-sel-106}><a class="first" href="/register">Reģistrēties</a></li>
						<!-- START BLOCK : login-form-error1-->
						<li><a class="red" href="/forgot-password">Aizmirsi paroli?</a></li>
						<!-- END BLOCK : login-form-error1-->
						<li>
							<form id="login-form" action="{page-url}" method="post">
								<fieldset>
									<input type="hidden" name="xsrf_token" value="{xsrf}" />
									<label>Niks:<input id="login-nick" size="16" name="niks" type="text" /></label>
									<label>Parole:<input id="login-pass" size="16" name="parole" type="password" /></label>
									<label><input name="login-submit" id="login-submit" class="login-submit" value="Ienākt" type="submit" /></label>
								</fieldset>
							</form>
						</li>
						<li>
							<a class="external-login external-dr" title="Ienākt ar draugiem.lv pasi" href="http://api.draugiem.lv/authorize/?app=15005147&amp;hash=efe004fdc35396fe598032be2213fb34&amp;redirect=http%3A%2F%2Flol.exs.lv%2Fdraugiem-signup%2F" onclick="if(handle=window.open('http://api.draugiem.lv/authorize/?app=15005147&amp;hash=efe004fdc35396fe598032be2213fb34&amp;redirect=http%3A%2F%2Flol.exs.lv%2Fdraugiem-signup%2F&amp;popup=1','Dr_15005147' ,'width=400, height=400, left='+(screen.width?(screen.width-400)/2:0)+', top='+(screen.height?(screen.height-400)/2:0)+',scrollbars=no')){handle.focus();return false;}"><span>Ienākt</span></a>
						</li>
						<li>
							<a href="/fb-login" class="external-login external-fb" title="Log in with FaceBook"><span>Log in</span></a>
						</li>
					</ul>
					<!-- END BLOCK : login-form-->
				</div>
			</div>
		</div>
		<div class="c"></div>
		<!-- START BLOCK : flash-message-->
		<div class="mbox {class}" id="flash-message">
			<p><a id="close-flash-message" href="#"><img src="http://exs.lv/bildes/fugue-icons/cross-button.png" alt="Aizvērt" title="Aizvērt" width="16" height="16" /></a> {message}</p>
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

				<div class="content-block">
					<script type="text/javascript"><!--
					google_ad_client = "ca-pub-9907860161851752";
					/* lol.exs.lv small */
					google_ad_slot = "7997271238";
					google_ad_width = 468;
					google_ad_height = 60;
					//-->
					</script>
					<script type="text/javascript"
					src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
					</script>
				</div>

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
				{contentz}
				<div class="c"></div>
				<!-- START BLOCK : ads-google-->
					<script type="text/javascript"><!--
					google_ad_client = "ca-pub-9907860161851752";
					/* lol.exs.lv small */
					google_ad_slot = "7997271238";
					google_ad_width = 468;
					google_ad_height = 60;
					//-->
					</script>
					<script type="text/javascript"
					src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
					</script>
				<!-- END BLOCK : ads-google-->
				<!-- START BLOCK : ads-google-wide-->
					<script type="text/javascript"><!--
					google_ad_client = "ca-pub-9907860161851752";
					/* lol.exs.lv large */
					google_ad_slot = "5043804832";
					google_ad_width = 728;
					google_ad_height = 90;
					//-->
					</script>
					<script type="text/javascript"
					src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
					</script>
				<!-- END BLOCK : ads-google-wide-->

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

	<h3>Jaunākais lapā</h3>
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
				<a href="{url}"><img id="profile-image" class="pimg-{profile-id}" src="{avatar}" alt="{profile-nick}" /></a><br />
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
				<a rel="nofollow" href="http://twitter.com/{twitter}" id="l-twitter">{twitter}</a><br />
				<!-- END BLOCK : profilebox-twitter-link-->
				<!-- START BLOCK : profilebox-yt-link-->
				<!-- END BLOCK : profilebox-yt-link-->
				<div class="c"></div>
			</div>
			<!-- END BLOCK : profile-box-->

			<!-- START BLOCK : friendssay-box-->
			<h3>Mini blogi{miniblog-add}</h3>

			<div class="box">

				<!-- START BLOCK : friendssay-tabs-->
				<ul class="tabs">
					<li><a href="/mb-latest?pg=0" class="{all-selected}mbs-all ajax"><span class="comments">Visi</span></a></li>
					<li><a href="/mb-latest?pg=0&amp;friendmb=true" class="{friends-selected}mbs-friends ajax"><span class="friends">Draugu</span></a></li>
				</ul>
				<div class="c"></div>
				<!-- END BLOCK : friendssay-tabs-->
				<div id="miniblog-block" class="ajaxbox">{out}</div>
			</div>

			<!-- END BLOCK : friendssay-box-->

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
	</div>
	<div id="footer">
		<div id="online-users">
			Lapu šobrīd skatās {page-onlinetotal} lietotāji, no tiem reģistrētie:<br /><span style="font-size:10px;">{page-onlineusers}</span>
		</div>
		<div class="infoblock">
			Jaunākie raksti: {footer-topics}
		</div>
		<div class="infoblock">
			Jaunākais miniblogos: {footer-mb}
		</div>
		<div class="infoblock">
			<p>&copy; <a href="http://openidea.lv/" title="Mājas lapas izstrāde un uzturēšana" rel="nofollow">SIA Open Idea</a>, 2005-2013</p>
			<p>lol.exs.lv ir neoficiāls League of Legends spēlētāju forums</p>
		</div>
		<div class="infoblock">
			<ul id="internal-links">
				<li><a href="http://exs.lv/read/lietosanas-noteikumi">Lietošanas noteikumi</a></li>
				<li><a href="/sitemap">Lapas karte</a></li>
				<li><a href="http://exs.lv/reklama">Reklāma portālā</a></li>
			</ul>
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

<script>
  (function(i,s,o,g,r,a,m){
  i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments) },i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  } )(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-4190387-13', 'exs.lv');
  ga('send', 'pageview');

</script>

</body>
</html>
