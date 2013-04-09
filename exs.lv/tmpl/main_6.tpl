<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="lv" lang="lv">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="content-language" content="lv" />
<title>{page-title} | lfs.lv</title>
<!-- START BLOCK : meta-description-->
<meta name="description" content="{description}" />
<!-- END BLOCK : meta-description-->
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<!-- START BLOCK : opengraph-->
<meta property="og:title" content="{title}" />
<meta property="og:type" content="{type}" />
<meta property="og:url" content="{url}" />
<meta property="og:image" content="{image}" />
<!-- END BLOCK : opengraph-->
<link rel="stylesheet" href="/css/core.css{add-css},lfs.css" type="text/css" />
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
<script type="text/javascript">
	var mb_refresh_limit = {mb-refresh-limit};
	var current_user = {currentuser-id};
	var new_msg_count = {new-messages-count};
	var query_timeout = 32000;
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
			nonbreaking_force_tab : true,
			file_browser_callback : MadFileBrowser
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
					<a id="exs-logo" href="/" title="Live For Speed: Latvia">lfs.lv</a>
					<div id="tools-bar">
						<ul id="site-links">
							<!--<li><a href="http://exs.lv/">exs.lv community</a></li>
							<li><a href="http://m.rp.exs.lv/" title="Mobilā versija" rel="nofollow">Mobilā versija</a></li>-->
							<li><a href="/img">Bilžu hostings</a></li>
							<li><a href="/sitemap">Lapas karte</a></li>
						</ul>
						{current-date}
					</div>
				</div>
				<div id="top-menu">
					<ul id="top-menu-left">
						<li{cat-sel-1093}><a class="first" href="/">Forums</a></li>
					</ul>
					<!-- START BLOCK : user-menu-->
					<ul id="top-menu-right">
						<li{profile-sel}><a class="first" href="/user/{currentuser-id}">Profils</a></li>
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
							<a class="external-login external-dr" title="Ienākt ar draugiem.lv pasi" href="http://api.draugiem.lv/authorize/?app=15005147&amp;hash=864de6756b2463e0abcca22ff1725c5d&amp;redirect=http%3A%2F%2Frp.exs.lv%2Fdraugiem-signup%2F" onclick="if(handle=window.open('http://api.draugiem.lv/authorize/?app=15005147&amp;hash=864de6756b2463e0abcca22ff1725c5d&amp;redirect=http%3A%2F%2Frp.exs.lv%2Fdraugiem-signup%2F&amp;popup=1','Dr_15005147' ,'width=400, height=400, left='+(screen.width?(screen.width-400)/2:0)+', top='+(screen.height?(screen.height-400)/2:0)+',scrollbars=no')){handle.focus();return false;}"><span>Ienākt</span></a>
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

				<ul class="tabs">
					<li><a href="/user/{user-id}" class="{active-tab-profile}"><span class="profile user-level-{inprofile-level} user-gender-{inprofile-gender}">Profils</span></a></li>
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
				<!-- END BLOCK : ads-google-->
				<!-- START BLOCK : ads-google-wide-->
				<!-- END BLOCK : ads-google-wide-->

				<p id="bottom-tools"><a href="javascript:history.back()" class="back">Atpakaļ</a> <a href="#top-menu" class="top">Uz augšu</a></p>
			</div>
		</div>
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

			<!-- START BLOCK : group-box-->
			<h3>{group-title}</h3>
			<div class="box">
				<a href="/group/{group-id}"><img id="profile-image" src="http://exs.lv/dati/bildes/{av-path}/{group-av}" alt="{group-alt}" /></a>
			</div>
			<!-- END BLOCK : group-box-->

			<h3>Jaunākais forumā</h3>
			<div class="box">
				<ul class="tabs">
					<li><a href="/latest.php" class="active remember-pages ajax"><span class="comments">Tēmas</span></a></li>
				</ul>
				<div id="lat" class="ajaxbox">{latest-noscript}</div>
			</div>

			<!-- START BLOCK : friendssay-box-->
			<h3>Miniblogi{miniblog-add}</h3>
			<div class="box"><div id="miniblog-block">{out}</div></div>
			<!-- END BLOCK : friendssay-box-->

			<!-- START BLOCK : notification-list-->
			<h3>Tavi notikumi</h3>
			<div class="box">
				{out}
			</div>
			<!-- END BLOCK : notification-list-->

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
			Lapu šobrīd skatās {page-onlinetotal} lietotāji, no tiem reģistrētie:<br /><span style="font-size:10px;">{page-onlineusers}</span>
		</div>
		<div class="infoblock">
			Jaunākie raksti: {footer-topics}
		</div>
		<div class="infoblock">
			Jaunākais miniblogos: {footer-mb}
		</div>
		<div class="infoblock">
			<div class="box">
				Meklēt lapā ar <a href="/search/">google</a>:
				<form method="get" action="/search/" id="search-form">
					<fieldset>
						<input type="hidden" name="cx" value="014557532850324448350:o5frztmsfcy" />
						<input type="hidden" name="cof" value="FORID:11" />
						<input type="hidden" name="ie" value="UTF-8" />
						<input class="text" name="q" size="16" type="text" value="" />
						<input value="Meklēt" class="submit button primary" type="submit" />
					</fieldset>
				</form>
			</div>
		</div>
		<div class="infoblock">
			lfs.lv ir neoficeāls Latvijas Live For Speed spēlētāju forums.
		</div>
		<div class="c"></div>
	</div>
</div>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-4190387-12', 'lfs.lv');
  ga('send', 'pageview');

</script>

<div id="async-placeholder"></div>
</body>
</html>
