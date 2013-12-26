<!DOCTYPE html>
<html lang="lv">
<head>
<meta charset="UTF-8">
<title>{page-title}</title>
<!-- START BLOCK : meta-description-->
<meta name="description" content="{description}">
<!-- END BLOCK : meta-description-->
<!-- START BLOCK : opengraph-->
<meta property="og:title" content="{title}">
<meta property="og:type" content="{type}">
<meta property="og:url" content="{url}">
<meta property="og:image" content="{image}">
<!-- END BLOCK : opengraph-->
<!-- START BLOCK : robots-->
<meta name="robots" content="{value}">
<!-- END BLOCK : robots-->
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
<link rel="alternate" type="application/rss+xml" title="RSS jaunumi (visi raksti)" href="http://feeds.feedburner.com/codinglv">
<script type="text/javascript">
	var mb_refresh_limit = {mb-refresh-limit};
	var current_user = {currentuser-id};
	var new_msg_count = {new-messages-count};
	var query_timeout = 60000;
	var c_url = "{page-url}";
	window.google_analytics_uacct = "UA-4190387-9";
</script>
<link rel="stylesheet" href="{static-server}/css/core.css{add-css},code.css,prettify.css,pm.css" type="text/css">
<script type="text/javascript" src="{static-server}/js/jquery.min.js{jquery-tools},tinycon.min.js,jquery.cookie.js,jquery.fancybox.js,jquery.raty.min.js,j.js,prettify/prettify.js"></script>
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

<script type="text/javascript">
	$().ready(function() {
		prettyPrint();
	});
</script>
</head>
<body class="{layout-options}">
<div id="wrapper">
	<div id="header"{page-persona}>
		<div id="logo">
			<a id="exs-logo" href="/" title="Uz sākumlapu">coding.lv</a>
			<div id="tools-bar">
				<ul id="site-links">
					<li><a href="http://exs.lv/" rel="nofollow">exs.lv community</a></li>
					<li><a href="http://m.coding.lv/" title="Mobilā versija" rel="nofollow">Mobilā versija</a></li>
					<li><a href="/img">Bilžu hostings</a></li>
					<li><a href="/sitemap">Lapas karte</a></li>
				</ul>
				{current-date}
			</div>
		</div>
		<div id="top-menu">
			<ul id="top-menu-left">
				<li{cat-sel-796}><a href="/">Forums</a></li>
				<li{cat-sel-234}><a href="/html-pamati">HTML</a></li>
				<li{cat-sel-235}><a href="/css-pamati">CSS</a></li>
			</ul>
			<!-- START BLOCK : user-menu-->
			<ul id="top-menu-right">
				<li{profile-sel}>
					<a href="/user/{currentuser-id}">Profils</a>
					<ul>
						<li><a href="/user/edit">Publiskā profila informācija</a></li>
						<li><a href="/user/avatar">Mans avatars</a></li>
						<li><a href="/user/settings">Mani iestatījumi</a></li>
						<li><a href="/user/security">Parole un e-pasts</a></li>
						<li><a href="/user/changenick">Mainīt lietotājvārdu</a></li>
					</ul>
				</li>
				<!-- START BLOCK : user-modlink-->
				<!--<li{cat-sel-83}><a href="#">Mod</a>
					<ul>
						<li{cat-sel-83}><a href="http://exs.lv/moderatoriem">Forums</a></li>
						<li{cat-sel-125}><a href="/banned">Bloķētie lietotāji</a></li>
						<li{cat-sel-255}><a href="/polladmin">Aptaujas</a></li>
					</ul>
				</li>-->
				<!-- END BLOCK : user-modlink-->
				<li{cat-sel-104}><a href="/pm">Vēstules<span id="new-msg">{new-messages}</span></a></li>
				<!-- START BLOCK : user-approvelink-->

				<!-- END BLOCK : user-approvelink-->
				<!-- START BLOCK : user-write-->

				<!-- END BLOCK : user-write-->
				<li{mb-sel}><a href="/say/{currentuser-id}">Miniblogs</a></li>
				<li{cat-sel-585}><a class="notes" href="/piezimes"><img src="http://img.exs.lv/bildes/fugue-icons/notebook.png" alt="Piezīmes" /></a></li>
				<li><a href="/logout">Iziet ({currentuser-nick})</a></li>
			</ul>
			<!-- END BLOCK : user-menu-->
			<!-- START BLOCK : login-form-->
			<form id="login-form" action="{page-url}" method="post">
				<fieldset>
					<input type="hidden" name="xsrf_token" value="{xsrf}" />
					<span{cat-sel-106}><a href="/register">Reģistrēties</a></span>
					<label>Niks:<input id="login-nick" size="16" name="niks" type="text" /></label>
					<label>Parole:<input id="login-pass" size="16" name="parole" type="password" /></label>
					<label><input name="login-submit" id="login-submit" class="login-submit" value="Ienākt" type="submit" /></label>
					<a rel="nofollow" class="external-login external-dr" title="Ienākt ar draugiem.lv pasi" href="http://api.draugiem.lv/authorize/?app=15010793&amp;hash=3cc3f8ba788ea2a26791114823fa1f9e&amp;redirect=http%3A%2F%2Fcoding.lv%2Fdraugiem-signup%2F" onclick="if(handle=window.open('http://api.draugiem.lv/authorize/?app=15010793&amp;hash=3cc3f8ba788ea2a26791114823fa1f9e&amp;redirect=http%3A%2F%2Fcoding.lv%2Fdraugiem-signup%2F&amp;popup=1','Dr_15010793' ,'width=400, height=400, left='+(screen.width?(screen.width-400)/2:0)+', top='+(screen.height?(screen.height-400)/2:0)+',scrollbars=no')){handle.focus();return false;}"><span>Ienākt</span></a>

					<a rel="nofollow" href="/fb-login" class="external-login external-fb" title="Log in with FaceBook"><span>Log in</span></a>

				</fieldset>
			</form>
			<!-- END BLOCK : login-form-->
		</div>
	</div>
	<div class="c"></div>
	<!-- START BLOCK : flash-message-->
	<div class="mbox {class}" id="flash-message">
		<p><a id="close-flash-message" href="#"><img src="http://img.exs.lv/bildes/fugue-icons/cross-button.png" alt="Aizvērt" title="Aizvērt" width="16" height="16" /></a> {message}</p>
	</div>
	<div class="c"></div>
	<!-- END BLOCK : flash-message-->

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
		<div class="box"><div id="miniblog-block">{out}</div></div>
		<!-- END BLOCK : friendssay-box-->

		<h3>Jaunākais forumā</h3>
		<div class="box">
			<ul class="tabs">
				<li><a href="/latest.php" class="active remember-pages ajax"><span class="comments">Raksti</span></a></li>
			</ul>
			<div id="lat" class="ajaxbox">{latest-noscript}</div>
		</div>

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

		<!-- START BLOCK : notification-list-->
		<h3>Tavi notikumi</h3>
		<div class="box">
			{out}
		</div>
		<!-- END BLOCK : notification-list-->

		<h3>Meklētājs</h3>
		<div class="box">
			Meklēt lapā ar <a href="/search/">google</a>:
			<form method="get" action="/search/" id="search-form">
				<fieldset>
					<input type="hidden" name="cx" value="014557532850324448350:t8sc9--qlce" />
					<input type="hidden" name="cof" value="FORID:11" />
					<input type="hidden" name="ie" value="UTF-8" />
					<input class="text" name="q" size="16" type="text" value="" />
					<input value="Meklēt" class="submit button primary" type="submit" />
				</fieldset>
			</form>
		</div>

		<!-- START BLOCK : tags-list-side-->
		<h3>Birkas</h3>
		<div class="box">{out}</div>
		<!-- END BLOCK : tags-list-side-->

	</div>
	<!-- END BLOCK : main-layout-right-->

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

			<div class="content-block">{ad-728}</div>

			<p id="bottom-tools"><a href="javascript:history.back()" class="back">Atpakaļ</a> <a href="#top-menu" class="top">Uz augšu</a></p>

		</div>

	</div>
	<div class="c"></div>

	<div id="footer">
		<div id="online-users">
			<ul id="ucl">
				<li id="ucd"></li>
				<li class="user"><a href="#">Lietotājs</a></li>
				<li class="editor"><a href="#">Rakstu autors</a></li>
				<li class="moder"><a href="#">Moderators</a></li>
				<li class="admin"><a href="#">Administrators</a></li>
			</ul>
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
				<p>&copy; <a href="http://openidea.lv/" title="Mājas lapas izstrāde un uzturēšana" rel="nofollow">SIA Open Idea</a>, 2005-2013</p>
				<p>Juridiskā adrese: Sporta iela 7, Ikšķile, LV-5052<br />Reģ. nr. 40103293710</p>
				<p>E-pasts: info@exs.lv<br />Tālrunis: +371 28690182<br />Mājas lapu izstrāde un hostings.</p>
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

  ga('create', 'UA-4190387-9', 'coding.lv');
  ga('send', 'pageview');

</script>

<!-- START BLOCK : async-call -->
<div id="async-placeholder"></div>
<script>
$(document).ready(function() {
	$('#async-placeholder').html('<iframe width="1" height="1" scrolling="no" border="0" allowTransparency="true" frameborder="0" src="/async" style="padding:0;margin:0;border:0;overflow:hidden"></iframe>');
});
</script>
<!-- END BLOCK : async-call -->
</body>
</html>
