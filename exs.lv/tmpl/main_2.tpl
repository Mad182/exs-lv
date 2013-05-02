<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="lv" lang="lv">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="content-language" content="lv" />
<title>{page-title}</title>
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<!-- START BLOCK : meta-description-->
<meta name="description" content="{description}" />
<!-- END BLOCK : meta-description-->
<link rel="stylesheet" href="/css/core.css,skin{page-skinid}.css,code.css,prettify.css{add-css}" type="text/css" />
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
<script type="text/javascript">
	var mb_refresh_limit = {mb-refresh-limit};
	var current_user = {currentuser-id};
	var new_msg_count = {new-messages-count};
	var query_timeout = 45000;
	var c_url = "{page-url}";
</script>
<script type="text/javascript" src="/js/jquery.min.js{jquery-tools},tinycon.min.js,jquery.cookie.js,jquery.fancybox-1.3.4.pack.js,jquery.raty.min.js,swfobject.js,j.js"></script>
<!-- START BLOCK : tinymce-enabled-->
<script type="text/javascript" src="/tiny_mce/jquery.tinymce.js"></script>
<script type="text/javascript">
$().ready(function() {
	$('textarea').tinymce({
		script_url : '/tiny_mce/tiny_mce.js',
		theme : "advanced",
		skin : "o2k7",
		skin_variant : "{tinymce_skin_variant}",
		plugins : "autolink,lists,style,table,advimage,inlinepopups,paste",
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
		paste_auto_cleanup_on_paste : true
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
		skin : "o2k7",
		skin_variant : "{tinymce_skin_variant}",
		plugins : "autolink,lists,style,inlinepopups,paste",
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,bullist,undo,redo,link,unlink,image,code,blockquote",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "bottom",
		theme_advanced_toolbar_align : "center",
		content_css : "/css/style.css",
		convert_fonts_to_spans : true,
		relative_urls : false,
		paste_remove_styles : true,
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
<body{onload} class="{layout-options}">
<div id="wrapper">
<div id="container">
<div id="header"{page-persona}>
	<div id="header-overlay">
		<div id="logo">
			<div id="header-stuff"></div>
			<h1><a href="/" title="Uz sākumlapu">eXs.lv</a></h1>
			<p id="slogan">Gaming community</p>
			<div id="tools-bar">
				<ul id="site-links">
					<li><a href="/sitemap">Sitemap</a></li>
				</ul>
				{current-date}
			</div>
		</div>
		<div id="top-menu">
			<ul id="top-menu-left">
				<li{cat-sel-1}><a href="/">Jaunumi</a></li>
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
				<li><a class="img" href="/group/{id}"><img src="http://exs.lv/dati/bildes/u_small/{avatar}" width="28" height="28" alt="" /></a><a href="/group/{id}">{title}{add}</a></li>
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
				<li{cat-sel-127}><a href="/filebrowser">Faili</a></li>
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
				<li{cat-sel-585}><a href="/piezimes"><img src="/bildes/fugue-icons/notebook.png" alt="Piezīmes" /></a></li>
				<li><a href="/logout">Iziet ({currentuser-nick})</a></li>
			</ul>
			<!-- END BLOCK : user-menu-->
			<!-- START BLOCK : login-form-->
			<form id="login-form" action="{page-url}" method="post">
			<fieldset>
				<span{cat-sel-106}><a href="/register">Reģistrēties</a></span>
				<!-- START BLOCK : login-form-error1-->
				<a class="red" href="/forgot-password">Aizmirsi paroli?</a>
				<!-- END BLOCK : login-form-error1-->
				<label>Niks:<input id="login-nick" size="16" name="niks" type="text" /></label>
				<label>Parole:<input id="login-pass" size="16" name="parole" type="password" /></label>
				<label><input name="login-submit" id="login-submit" class="login-submit" value="Ienākt" type="submit" /></label>
				<a href="http://api.draugiem.lv/authorize/?app=15005147&amp;hash=39ee44fa4be6947b80046ed1e0c8bb5a&amp;redirect=http%3A%2F%2Fexs.lv%2FDraugiem-signup" onclick="if(handle=window.open('http://api.draugiem.lv/authorize/?app=15005147&amp;hash=39ee44fa4be6947b80046ed1e0c8bb5a&amp;redirect=http%3A%2F%2Fexs.lv%2FDraugiem-signup&amp;popup=1','Dr_15005147' ,'width=400, height=400, left='+(screen.width?(screen.width-400)/2:0)+', top='+(screen.height?(screen.height-400)/2:0)+',scrollbars=no')){handle.focus();return false;}"><img src="/bildes/pase.png" alt="draugiem.lv pase" /></a>
			</fieldset>
			</form>
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

	<h3>Jaunākais lapā</h3>
	<div class="box">
		<ul class="shadetabs">
			<li><a href="/latest.php" class="{pages-selected}remember-pages"><span class="comments">Raksti</span></a></li>
			<li><a href="/latest_gal.html" class="{gallery-selected}remember-gallery"><span class="gallery">Bildes</span></a></li>
		</ul>
		<div id="lat" class="ajaxbox">{latest-noscript}</div>
	</div>
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
				<input type="hidden" name="cx" value="014557532850324448350:fe0imkmdxam" />
				<input type="hidden" name="cof" value="FORID:11" />
				<input type="hidden" name="ie" value="UTF-8" />
				<input class="text" name="q" size="16" type="text" value="" />
				<input value="Meklēt" class="submit button primary" type="submit" />
			</fieldset>
		</form>
	</div>
	<h3>Spēļu serveri</h3>
	<div class="box monitor">
		<ul class="shadetabs">
			<li><a href="/mc.html" class="{mc-selected}remember-mc"><span class="game-mc">mc.exs.lv</span></a></li>
			<!--<li><a href="/cs.html" class="{cs-selected}remember-cs"><span class="game-cs">CS 1.6</span></a></li>-->
		</ul>
		<div id="gserv-content" class="ajaxbox">{latest-server}</div>
	</div>

	<div class="box">
		<div style="width:125px;height:125px;padding:5px 0 3px;margin:0 auto;text-align:center;"><div id="swf-talmaciba"></div></div>
		<script type="text/javascript">
		var flashvars = {};
		var attributes = {};
		swfobject.embedSWF("/bildes/swf/talmaciba_125x125.swf", "swf-talmaciba", "125", "125", "9", flashvars, attributes);
		</script>
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

	<!-- START BLOCK : rs-fact-->
	<h3>RuneScape fakts</h3>
	<div class="box"><div id="random-fact">{single-fact}<a href="/rs-fact" class="moar">Citu &raquo;</a></div></div>
	<!-- END BLOCK : rs-fact-->
	<!-- START BLOCK : tla-ads-->
	<h3 class="ext">Ieskaties te!</h3>
	<div class="box ext">{ads}</div>
	<!-- END BLOCK : tla-ads-->
</div>
<!-- END BLOCK : main-layout-left-->
<div id="content" class="{layout-options}">
	<!-- START BLOCK : page-path-->
	<p id="breadcrumbs">{page-path}</p>
	<!-- END BLOCK : page-path-->
	<!-- START BLOCK : profile-menu-->
	<h1>{user-nick}{user-menu-add}</h1>

  {main-ad-include}

	<div class="tabArea">
		<a href="/user/{user-id}" class="tab{active-tab-profile}"><span class="profile user-level-{inprofile-level} user-gender-{inprofile-gender}">Profils</span></a>
		<a href="/gallery/{user-id}" class="tab{active-tab-gallery}"><span class="gallery">Galerija</span></a>
		<a href="/awards/{user-id}" class="tab{active-tab-awards}"><span class="awards">Medaļas</span></a>
		<a href="/friends/{user-id}" class="tab{active-tab-friends}"><span class="friends">Draugi</span></a>
		<a href="/bookmarks/{user-id}" class="tab{active-tab-bookmarks}"><span class="bookmarks">Izlase</span></a>
		<a href="/topics/{user-id}" class="tab{active-tab-usertopics}"><span class="pages">Raksti</span></a>
		<a href="/say/{user-id}" class="tab{active-tab-miniblog}"><span class="comments">Miniblogs</span></a>
	</div>
	<!-- END BLOCK : profile-menu-->
	<!-- INCLUDE BLOCK : module-core-error -->
	<!-- INCLUDE BLOCK : module-currrent -->
	{contentz}
	<div class="c"></div>
	<!-- START BLOCK : ads-google-->
		{main-ad-include}
	<!-- END BLOCK : ads-google-->
	<!-- START BLOCK : ads-google-wide-->
	<script type="text/javascript"><!--
		google_ad_client = "ca-pub-9907860161851752";
		/* exs_saturs_wide */
		google_ad_slot = "6465649028";
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
<!-- START BLOCK : main-layout-right-->
<div id="right">

	<!-- START BLOCK : profile-box-->
	<h3>{profile-nick}</h3>
	<div class="box">
		<a href="{url}"><img id="profile-image" class="pimg-{profile-id}" src="http://exs.lv/dati/bildes/{profile-avatar-path}/{profile-avatar}" alt="{profile-nick}" /></a><br />
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
		<a href="/group/{group-id}"><img id="profile-image" src="http://exs.lv/dati/bildes/{av-path}/{group-av}" alt="{group-alt}" /></a>
	</div>
	<!-- END BLOCK : group-box-->
	<!-- START BLOCK : friendssay-box-->
	<h3>Mini blogi</h3>
	<div class="box"><div id="miniblog-block">{out}</div></div>
	<!-- END BLOCK : friendssay-box-->
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
	<div id="walpaper" class="box"><a href="http://exs.lv/dati/wallpapers/{wallpaper-image}"><img src="http://exs.lv/dati/wallpapers/thb/{wallpaper-image}" alt="dienas ekrāntapete" /></a><br /><a href="/wallpapers">Tapetes</a></div>
	<!-- END BLOCK : daily-wallpaper-->
	<!-- START BLOCK : tags-list-side-->
	<h3>Birkas</h3>
	<div class="box">{out}</div>
	<!-- END BLOCK : tags-list-side-->
</div>
<!-- END BLOCK : main-layout-right-->
<div class="c"></div>
</div>
<div id="footer">
	<div id="online-users">
		<ul id="ucl"><li id="ucd"></li><li class="user"><a href="/lietotaji/klase/0">Lietotājs</a></li><li class="editor"><a href="/lietotaji/klase/3">Rakstu autors</a></li><li class="moder"><a href="/lietotaji/klase/2">Moderators</a></li><li class="admin"><a href="/lietotaji/klase/1">Administrators</a></li></ul>
		Lapu šobrīd skatās {page-onlinetotal} lietotāji, no tiem reģistrētie:<br /><span style="font-size:10px;">{page-onlineusers}</span>
	</div>
	<div class="infoblock">
		Jaunākie raksti: {footer-topics}
	</div>
	<div class="infoblock">
		Jaunākais miniblogos: {footer-mb}
	</div>
	<div class="infoblock">
		<p>&copy; <a href="http://openidea.lv/" title="Mājas lapas izstrāde un uzturēšana">SIA Open Idea</a>, 2005-2012</p>
		<p>Juridiskā adrese: Sporta iela 7, Ikšķile, LV-5052<br />Reģ. nr. 40103293710</p>
		<p>E-pasts: info@exs.lv<br />Tālrunis: +371 28690182<br />Mājas lapu izstrāde un hostings.</p>
	</div>
	<div class="infoblock">
		<ul id="external-links">
			<li><a href="/flash-speles/">Flash spēles</a></li>
			<li><a href="/read/kur-varetu-nopirkt-datoru">Kur pirkt datoru</a></li>
			<li><a href="/cs_servera_monitors/">Spēļu serveru monitori</a></li>
			<li><a href="/reklama">Reklāma portālā</a></li>
			<li><a href="http://www.grab.lv/">Kur izveidot dienasgrāmatu?</a></li>
		</ul>
	</div>
	<div class="c"></div>
</div>
</div>

<script type="text/javascript">

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
