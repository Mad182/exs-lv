<!DOCTYPE html>
<html lang="lv" prefix="fb: http://www.facebook.com/2008/fbml">
<head>
	<meta charset="UTF-8">
	<title>{page-title}</title>
	<!-- START BLOCK : meta-description-->
	<meta name="description" content="{description}">
	<!-- END BLOCK : meta-description-->
	<!-- START BLOCK : og-meta-->
	<meta property="og:{key}" content="{val}">
	<!-- END BLOCK : og-meta-->
	<!-- START BLOCK : twitter-meta-->
	<meta name="twitter:{key}" content="{val}">
	<!-- END BLOCK : twitter-meta-->
	<!-- START BLOCK : robots-->
	<meta name="robots" content="{value}">
	<!-- END BLOCK : robots-->
	<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
	<meta name="viewport" content="width=device-width">
	<!-- START BLOCK : canonical-->
	<link rel="canonical" href="{url}">
	<!-- END BLOCK : canonical-->
	<link rel="alternate" type="application/rss+xml" title="RSS jaunumi" href="https://feeds.feedburner.com/runes">
	<link rel="stylesheet" href="{static-server}/css/responsive.css,bootstrap.css" media="all">
	<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:400,400italic,500&amp;subset=latin-ext,cyrillic,latin">
	<!-- START BLOCK : additional-css-->
	<link rel="stylesheet" href="{static-server}/css/{filename}">
	<!-- END BLOCK : additional-css-->
	<script>var mb_refresh_limit={mb-refresh-limit},current_user={currentuser-id},new_msg_count={new-messages-count},c_url="{page-url}";</script>
	<script src="{static-server}/js/jquery-1.10.2.min.js,html5.js,bootstrap.min.js,fluidvids.min.js,jquery.sidr.min.js,jquery.touchSwipe.min.js,jquery.swipemenu.init.js,swfobject.js,tinycon.min.js,jquery.cookie.js,jquery.fancybox.js,jquery.raty.min.js,mcp.js,j.js,exs.js"></script>
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
	<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
	<script>
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
	<script>var lastid={lastid},mbid={mbid},usrid={usrid},edit_time={edit_time},mbtype="{type}",mbRefreshId=setInterval("update_mb()", mb_refresh_limit);</script>
	<!-- END BLOCK : mb-head-->
	<!-- INCLUDE BLOCK : module-head -->
	{plevel}
</head>

<body{onload} class="{layout-options}">
	<div id="scroll-up" title="Uz augšu"></div>

	<div id="page">

		<header id="header" class="container">
			<div id="mast-head">
				<div id="logo">
					<a href="/" title="exs.lv spēļu portāls" rel="home"><img src="{img-server}/bildes/logos/logo_exs_small.png" alt="logo" /></a>
				</div>
			</div>

			<nav class="navbar clearfix nobot">

				<a id="responsive-menu-button" href="#swipe-menu">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>		
				</a>	

				<div class="nav-collapse" id="swipe-menu-responsive">

					<ul class="nav">

						<li>
							<span id="close-menu">
								<a href="#" class="close-this-menu">Close</a>
							</span>
						</li>

						<li><a href="/forums">Forums</a></li>
						<li class="dropdown"><a href="/raksti">Raksti</a>
							<ul class="dropdown-menu">
								<li><a href="/filmas">Filmas</a></li>
								<li><a href="/muzika">Mūzika</a></li>
								<li><a href="/speles">Spēles</a></li>
							</ul>
						</li>
						<li><a href="/blogs">Blogi</a></li>
						<li><a href="/steam-online">Steam</a></li>
						<li><a href="/img">Bilžu hostings</a></li>
						<li><a href="/junk" title="Bilžu sadaļa">/junk</a></li>
						<!-- START BLOCK : junk-info-->
						<li><a href="/adm">Junk admin{count}</a></li>
						<!-- END BLOCK : junk-info-->
						<li class="dropdown"><a href="/">exs.lv</a>
							<ul class="dropdown-menu">
								<li><a href="https://runescape.exs.lv/" title="RuneScape forums" rel="nofollow">rs.exs.lv</a></li>
								<li><a href="https://lol.exs.lv/" title="League of Legends forums" rel="nofollow">lol.exs.lv</a></li>
								<li><a href="https://coding.lv/" title="Mājas lapu veidošanas un programmēšanas forums" rel="nofollow">coding.lv</a></li>
							</ul>
						</li>

					</ul>

				</div>

			</nav>

		</header>

		<div id="headline" class="container"{page-persona}>
			<!-- START BLOCK : header-ad-->
			<div id="tad">
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-9907860161851752"
     data-ad-slot="7904722018"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
			</div>
			<!-- END BLOCK : header-ad-->
		</div>

		<div id="intr" class="container">
			<div class="row-fluid">
				<div class="span9">
					<!-- START BLOCK : user-menu-->
					<ul id="user-menu" class="nav nav-pills">
						<li><a href="/"><img src="{img-server}/bildes/home.png" alt="Sākumlapa"></a></li>
						<li class="dropdown">
							<a href="/user/{currentuser-id}">Profils</a>
							<ul class="dropdown-menu">
								<li{gal-sel}><a href="/gallery/{currentuser-id}">Mana galerija</a></li>
								<li><a href="/user/edit">Publiskā profila informācija</a></li>
								<li><a href="/user/avatar">Mans avatars</a></li>
								<li><a href="/user/settings">Mani iestatījumi</a></li>
								<li><a href="/user/security">Paroles maiņa</a></li>
								<li><a href="/user/email">E-pasta adreses maiņa</a></li>
								<li><a href="/user/changenick">Mainīt lietotājvārdu</a></li>
							</ul>
						</li>
						<li class="dropdown"><a href="/grupas">Grupas</a>
							<!-- START BLOCK : mygroups-->
							<ul class="dropdown-menu" id="user-group-menu">
								<!-- START BLOCK : myg-node-->
								<li><a{unread-class} href="/group/{id}" title="{title}"><span style="background-image:url('//img.exs.lv/userpic/small/{avatar}')">{unread}</span>{title}</a></li>
								<!-- END BLOCK : myg-node-->
							</ul>
							<!-- END BLOCK : mygroups-->
						</li>
						<!-- START BLOCK : user-modlink-->
						<li class="dropdown"><a href="#">Mod</a>
							<ul class="dropdown-menu">
								<li{cat-sel-83}><a href="/moderatoriem">Moderatoru forums</a></li>
								<li{cat-sel-125}><a href="/banned">Liegumi</a></li>
								<li{cat-sel-1822}><a href="/crows">Atbrīvotās vārnas</a></li>
								<li{cat-sel-1827}><a href="/reports">Sūdzības{reports-count}</a></li>
								<li{cat-sel-1132}><a href="/findby">Profilu pārvaldība</a></li>
								<li{cat-sel-199}><a href="/log">Darbību vēsture</a></li>
								<li{cat-sel-206}><a href="/?c=206">Random fakti</a></li>
								<li{cat-sel-255}><a href="/polladmin">Aptaujas</a></li>
								<li{cat-sel-229}><a href="/wallpaper_admin">Wallpapers</a></li>
								<li{cat-sel-451}><a href="/smslog">SMS maksājumi</a></li>
								<li{cat-sel-331}><a href="/?c=331">Karātavas</a></li>
								<li{cat-sel-586}><a href="/csmaps">CS mapimg</a></li>
								<li{cat-sel-642}><a href="/racontest">RA konkurss</a></li>
								<li{cat-sel-794}><a href="/user_decos">Apbalvojumu ikonas</a></li>
								<!-- START BLOCK : user-modlink-adm -->
								<li{cat-sel-2387}><a href="/custom_awards">Profila apbalvojumi</a></li>
								<!-- END BLOCK : user-modlink-adm -->
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
						<li{cat-sel-585}><a href="/piezimes">Piezīmes</a></li>
						<li><a href="/logout/{logout-hash}">Iziet ({currentuser-nick})</a></li>
					</ul>
					<!-- END BLOCK : user-menu-->
					<!-- START BLOCK : login-form-->
					<ul id="user-menu" class="nav nav-pills">
						<li><a href="/"><img src="/bildes/home.png" alt="Sākumlapa"></a></li>
						<li{cat-sel-106}><a href="/register">Reģistrēties</a></li>
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
						<li><a rel="nofollow" class="external-login external-dr" title="Ienākt ar draugiem.lv pasi" href="https://api.draugiem.lv/authorize/?app=15005147&amp;hash=eaef1fd32cb572a292467e05f26cf774&amp;redirect=http%3A%2F%2Fexs.lv%2Fdraugiem-signup%2F" onclick="if (handle=window.open('https://api.draugiem.lv/authorize/?app=15005147&amp;hash=eaef1fd32cb572a292467e05f26cf774&amp;redirect=https%3A%2F%2Fexs.lv%2Fdraugiem-signup%2F&amp;popup=1', 'Dr_15005147', 'width=400, height=400, left=' + (screen.width ? (screen.width - 400) / 2 : 0) + ', top=' + (screen.height ? (screen.height - 400) / 2 : 0) + ',scrollbars=no')) {
								handle.focus();
								return false;
							}"><span>Ienākt</span></a></li>

						<li><a rel="nofollow" href="/fb-login" class="external-login external-fb" title="Log in with FaceBook"><span>Log in</span></a></li>

						<li><a rel="nofollow" href="/twitter-login" class="external-login external-twitter" title="Log in with twitter"><span>Log in</span></a></li>
					
					</ul>
					<!-- END BLOCK : login-form-->
				</div>

				<div class="search span3">
					<form method="get" id="searchform" action="/search/">
						<input type="hidden" name="cx" value="014557532850324448350:fe0imkmdxam" />
						<input type="hidden" name="cof" value="FORID:11" />
						<input type="hidden" name="ie" value="UTF-8" />
						<p>
							<input type="text" value="" placeholder="Es meklēju..." name="q" id="s" />
							<input type="submit" id="searchsubmit" value="Meklēt" />
						</p>
					</form>
				</div>
			</div>
		</div>

		<div id="content" class="container">

			<div id="main" class="row-fluid">

				<div id="sidebar-left" class="span2">

					<!-- START BLOCK : notification-list-->
					<div class="widget">
						<h3 class="title"><span>Tavi jaunumi</span></h3>
						<div>
							{html}
						</div>
					</div>
					<!-- END BLOCK : notification-list-->

					<!-- START BLOCK : menu-list-->
					<h3 class="title"><span>{title}</span></h3>
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

					<div class="widget">
						<h3 class="title"><span>Jaunākais portālā</span></h3>
						<div class="tabwidget widget tab-container box"> 
							<ul class="tabnav clearfix">
								<li><a href="/latest.php" class="{pages-selected}remember-pages ajax"><span class="comments">Raksti</span></a></li>
								<li><a href="/latest.php?type=images" class="{gallery-selected}remember-gallery ajax"><span class="gallery">Bildes</span></a></li>
							</ul>
							<div class="c"></div>
							<div id="lat" class="ajaxbox">{latest-noscript}</div>
						</div>
					</div>

					<!-- START BLOCK : groups-l-list-->
					<div class="widget" id="latest-groups">
						<h3 class="title"><span>Jaunākās grupas</span></h3>
						<div class="box">
							<p>
								<!-- START BLOCK : groups-l-node-->
								<a href="{link}">{title}</a><br />
								<!-- END BLOCK : groups-l-node-->
							</p>
							<a href="/grupas">Visas grupas &raquo;</a>
						</div>
					</div>
					<!-- END BLOCK : groups-l-list-->

					<div class="widget" id="todays-active">
						<h3 class="title"><span>Šodien aktīvākie</span></h3>
						<div class="box">
							<ul class="tabs">
								<li><a rel="nofollow" href="/dailytop/users" class="active ajax"><span class="profile">Lietotāji</span></a></li>
								<li><a rel="nofollow" href="/dailytop/groups" class="ajax"><span class="users">Grupas</span></a></li>
							</ul>
							<div class="c"></div>
							<div id="daily-top" class="ajaxbox">{user-top}</div>
						</div>
					</div>


				</div>


				<div id="main-left" class="span7">
					<!-- START BLOCK : flash-message-->
					<div class="mbox {class}" id="flash-message">
						<p><a id="close-flash-message" href="#"><img src="{img-server}/bildes/fugue-icons/cross-button.png" alt="Aizvērt" title="Aizvērt" width="16" height="16" /></a> {message}</p>
					</div>
					<div class="c"></div>
					<!-- END BLOCK : flash-message-->
					<!-- START BLOCK : page-path-->
					<p id="breadcrumbs">{page-path}</p>
					<!-- END BLOCK : page-path-->
					<!-- START BLOCK : profile-menu-->
					<h1>{user-nick}{user-menu-add}</h1>
					<ul class="tabs">
						<li><a href="/user/{user-id}" class="{active-tab-profile}"><span class="profile user-level-{inprofile-level} user-gender-{inprofile-gender}">Profils</span></a></li>
						<li><a href="/gallery/{user-id}" class="{active-tab-gallery}"><span class="gallery">Galerija</span></a></li>
						<li><a href="/awards/{user-id}" class="{active-tab-awards}" rel="nofollow"><span class="awards">Medaļas</span></a></li>
						<li><a href="/friends/{user-id}" class="{active-tab-friends}" rel="nofollow"><span class="friends">Draugi</span></a></li>
						<li><a href="/bookmarks/{user-id}" class="{active-tab-bookmarks}"><span class="bookmarks">Izlase</span></a></li>
						<li><a href="/topics/{user-id}" class="{active-tab-usertopics}"><span class="pages">Raksti</span></a></li>
						<li><a href="/say/{user-id}" class="{active-tab-miniblog}"><span class="comments">Miniblogs</span></a></li>
					</ul>
					<!-- END BLOCK : profile-menu-->
					<!-- START BLOCK : profile-menu-deleted-->
					<h1>Dzēsts lietotājs</h1>
					<ul class="tabs">
						<li><a href="/say/{user-id}" class="active"><span class="comments">Miniblogs</span></a></li>
					</ul>
					<!-- END BLOCK : profile-menu-deleted-->
					<!-- INCLUDE BLOCK : module-core-error -->
					<div id="current-module">
						<!-- INCLUDE BLOCK : module-currrent -->
					</div>
					<div class="c"></div>
				</div>

				<div id="sidebar" class="span3">

					<!-- START BLOCK : movie-search-->
					<h3 class="title"><span>Meklēt filmu</span></h3>
					<div class="box">
						<form id="movie-search" method="get" action="/filmas/search">
							<!-- START BLOCK : genre-node-->
							<label style="font-size: 10px;line-height: 13px;"><input style="width:11px;height:11px;padding: 0;margin:2px;" type="checkbox" name="genres[]" value="{genre}"{checked} />{translated}</label>
							<!-- START BLOCK : genre-node-->

							<input type="submit" value="Meklēt" class="button primary small" />
						</form>
					</div>
					<!-- END BLOCK : movie-search-->
					<!-- START BLOCK : profile-box-->
					<div class="widget">
						<h3 class="title"><span>{profile-nick}{custom_title}</span></h3>
						<a href="{url}">
							<img id="profile-image" class="pimg-{profile-id}" src="{avatar}" alt="{profile-nick}" />
						</a>
						<!-- START BLOCK : profilebox-updateavatar-->
						<div class="form">
							<p class="notice">
								Tavam profilam nav attēla. <a href="/user/avatar">Pievienot?</a>
							</p>
						</div>
						<!-- END BLOCK : profilebox-updateavatar-->
						{profile-top-awards}
						<div id="pb-links">
							<!-- START BLOCK : profilebox-pm-link-->
							<a href="/pm/write/?to={profile-id}" id="l-pm">Nosūtīt ziņu</a>
							<!-- END BLOCK : profilebox-pm-link-->
							<!-- START BLOCK : profilebox-warn-->
							<a href="/warns/{profile-id}" id="l-warn"{class}>Brīdinājumi{profile-warns}</a>
							<!-- END BLOCK : profilebox-warn-->

							<!-- START BLOCK : profilebox-blog-link-->
							<a href="{url}" id="l-blog">Blogs&nbsp;({count})</a>
							<!-- END BLOCK : profilebox-blog-link-->

							<!-- START BLOCK : profilebox-twitter-link-->
							<a rel="nofollow" target="_blank" href="https://twitter.com/{twitter}" id="l-twitter">{twitter}</a>
							<!-- END BLOCK : profilebox-twitter-link-->

							<!-- START BLOCK : profilebox-yt-link-->
							<a rel="nofollow" target="_blank" href="https://www.youtube.com/{yt-slug}" id="l-yt"><span class="yt">{yt-name}</span></a>
							<!-- END BLOCK : profilebox-yt-link-->

							<!-- START BLOCK : profilebox-lastfm-link-->
							<a rel="nofollow" target="_blank" href="http://www.last.fm/user/{name}" id="l-lastfm">{name}</a><br />
							<!-- END BLOCK : profilebox-lastfm-link-->
						</div>
						<div class="c"></div>
					</div>
					<!-- END BLOCK : profile-box-->
					<!-- START BLOCK : mb-box-->
					<div class="widget">
						<h3 class="title"><span>Miniblogi{miniblog-add}</span></h3>
						<div class="tabwidget widget tab-container box"> 
							<ul class="tabnav clearfix"> 
								<li><a href="/mb-latest?pg=0&amp;tab=all" class="{all-selected}remember-all ajax"><span class="comments">Visi</span></a></li>
								<!-- START BLOCK : mb-tabs-->
								<li><a href="/mb-latest?pg=0&amp;tab=friends" class="{friends-selected}remember-friends ajax"><span class="friends">Draugu</span></a></li>
								<!-- END BLOCK : mb-tabs-->
								<li><a href="/mb-latest?pg=0&amp;tab=music" class="{music-selected}remember-music ajax"><span class="music">last.fm</span></a></li>
							</ul> 

							<div id="miniblog-block" class="ajaxbox">
								{out}
							</div>
						</div>
					</div>
					<!-- END BLOCK : mb-box-->

					<!-- START BLOCK : poll-box-->
					<div class="widget">
						<h3 class="title"><a href="/aptaujas"><span>Aptauja: {poll-title}</span></a></h3>
						<div class="box">
							<!-- START BLOCK : poll-answers-->
							<ol class="poll-answers">
								<!-- START BLOCK : poll-answers-node-->
								<li>{poll-answer-question}<div><span>{poll-answer-percentage}%</span><div style="width:{poll-answer-percentage}%"></div></div></li>
								<!-- END BLOCK : poll-answers-node-->
							</ol>
							Balsojuši: {poll-totalvotes}<br />
							<a href="{ppage-id}">Komentāri</a>
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
					</div>
					<!-- END BLOCK : poll-box-->

					<!-- START BLOCK : daily-best-->

					<div class="widget">
						<h3 class="title"><span>Dienas komentārs</span></h3>
						<div class="mbox">
							<ul class="blockhref mb-col">
								<li>
									<a href="{best-link}">
										<img class="av" src="{best-avatar}" width="45" height="45" alt=""/>
										<span class="author">{best-nick}</span>
										<span class="post-rating">+{best-rating}</span><br />
										{best-comment}
									</a>
								</li>
							</ul>
						</div>
					</div>
					<!-- END BLOCK : daily-best-->

					<!-- START BLOCK : side-junk-->
					<div class="widget">
						<h3 class="title"><span>/junk attēli</span></h3>
						<div class="junk-box">
							<!-- START BLOCK : side-junk-node-->
							<a href="/junk/{id}" title="{title}">
								<img src="//img.exs.lv{thb}" alt="" class="av" />
								<span style="">{posts}</span>
							</a>
							<!-- END BLOCK : side-junk-node-->
							<div class="c"></div>
						</div>
					</div>
					<!-- END BLOCK : side-junk-->

				</div>
				<div class="c"></div>

			</div>

		</div>

		<footer id="footer" class="row-fluid">
			<div id="footer-widgets" class="container">

				<div class="footer-widget span2 block5">

					<div id="socialmedia-2" class="widget widget_social">
						<br />
						<div class="socmed textwidget clearfix">		
							<ul>
								<li>
									<a href="https://play.google.com/store/apps/details?id=lv.exs.android" rel="nofollow" target="_blank" class="social android">Android</a>
									<h4>Android</h4>
									<p>Aplikācija</p>
								</li>
								<li>
									<a href="https://twitter.com/exs_lv" rel="nofollow" target="_blank" class="social twitter">Twitter</a>
									<h4>360</h4>
									<p>Sekotāji</p>
								</li>
							</ul>
						</div>

					</div>

					<div class="c"></div>
				</div>

				<div class="footer-widget widget span2 block6">

					<div class="kontak">
						<h3 class="title"><span>Kontakti</span></h3>
						<ul>
							<li class="email">E-pasts: info@exs.lv</li>
							<li class="phone">Telefons: <span id="noindex-phone"></span></li>
						</ul>
					</div>
					<div class="c"></div>
				</div>

				<div class="footer-widget span8 block6">
					<div class="widget onlusers-widget">
						<h3 class="title"><span>Lietotāji tiešsaitē</span></h3>
						<div class="onlusers clearfix">		
							<div id="online-users">
								<ul id="ucl">
									<li id="ucd"></li>
									<li class="user"><a href="/lietotaji/klase/0">Lietotājs</a></li>
									<li class="editor"><a href="/lietotaji/klase/3">Rakstu autors</a></li>
									<li class="moder"><a href="/lietotaji/klase/2">Moderators</a></li>
									<li class="admin"><a href="/lietotaji/klase/1">Administrators</a>
									</li>
								</ul>
								<div class="c"></div>
								Tiešsaitē {page-onlinetotal} lietotāji, no tiem reģistrētie:<br />
								<span id="online-list">{page-onlineusers}</span>
							</div>
						</div>
					</div>
				</div>

			</div>

			<div id="site-info" class="container">
				<div id="footer-nav" class="fr">
					<ul class="menu">
						<li><a href="/statistika">Statistika</a></li>
						<li><a href="/read/lietosanas-noteikumi">Lietošanas noteikumi</a></li>
					</ul>
				</div>
				<div id="credit" class="fl">
					<p>&copy; 2005-{current-year}, {openidea} - mājas lapas izstrāde un uzturēšana.</p>
				</div>
			</div>
		</footer>
	</div>

	<!-- START BLOCK : popup-ads-->
	<!-- END BLOCK : popup-ads-->

	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	  ga('create', 'UA-4190387-2', 'auto');ga('send', 'pageview');
	</script>

</body>
</html>
