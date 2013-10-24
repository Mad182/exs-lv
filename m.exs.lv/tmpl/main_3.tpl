<!DOCTYPE html>
<html lang="lv">
<head>
<meta charset="UTF-8">
<title>{page-title}</title>
<meta name="googlebot" content="noindex">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link rel="stylesheet" href="{static-server}/css/mobile.css" type="text/css" />
<script type="text/javascript" src="{static-server}/js/jquery.min.js,mobile.js"></script>
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
	var mbRefreshId = setInterval("update_mb()",refreshlim);
</script>
<!-- END BLOCK : mb-head-->
<!-- INCLUDE BLOCK : module-head -->
<!-- START BLOCK : tinymce-enabled-->
<!-- END BLOCK : tinymce-enabled-->
<!-- START BLOCK : tinymce-simple-->
<!-- END BLOCK : tinymce-simple-->
</head>

<body>
<div id="outer-wrapper">
<div id="header">
	<div id="user-tools">
	  <a href="/user/{currentuser-id}"><img src="/av/{currentuser-avatar}" alt="" style="float: right;margin: 0 0 0 5px;width:36px;height:36px;" /></a>
		Čau,&nbsp;{currentuser-nick}!<br /><a href="/mevents">Tavi notikumi</a>
	</div>
	<a id="logo" href="/">coding.lv</a>
</div>
<div id="buttons">
<ul>
<li><a href="/">Siena</a></li>
<li><a href="/index">Forums</a></li>
<!-- START BLOCK : user-menu-->
<li><a href="/pm">PM{new-messages}</a></li>
<li><a href="/say/{currentuser-id}">M-blogs</a></li>
<!-- END BLOCK : user-menu-->          
<li><a href="/grupas">Grupas</a></li>
</ul>
</div>
<div id="wrapper">
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
</div>
<div id="footer"><a href="/sitemap">Lapas karte</a> | &copy; coding.lv, 2013 | <a href="/?do=logout">Iziet</a></div>
</div>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-4190387-9']);
  _gaq.push(['_setDomainName', 'coding.lv']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

<!-- START BLOCK : sharethis-->
<!-- END BLOCK : sharethis-->

</body>
</html>
