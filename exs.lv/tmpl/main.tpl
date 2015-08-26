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
		<link rel="alternate" type="application/rss+xml" title="RSS jaunumi" href="http://feeds.feedburner.com/runes">
		<script type="text/javascript">
			var mb_refresh_limit = {mb-refresh-limit};
			var current_user = {currentuser-id};
			var new_msg_count = {new-messages-count};
			var query_timeout = 60000;
			var c_url = "{page-url}";
		</script>
		
		<meta name="viewport" content="width=device-width" />

		<link rel='stylesheet' id='magz-style-css'  href='{static-server}/responsive/style.css' type='text/css' media='all' />
		<link rel='stylesheet' id='swipemenu-css'  href='{static-server}/responsive/css/swipemenu.css' type='text/css' media='all' />
		<link rel='stylesheet' id='bootstrap-css'  href='{static-server}/responsive/css/bootstrap.css' type='text/css' media='all' />
		<link rel='stylesheet' id='bootstrap-responsive-css'  href='{static-server}/responsive/css/bootstrap-responsive.css' type='text/css' media='all' />
		<link rel='stylesheet' id='simplyscroll-css'  href='{static-server}/responsive/css/jquery.simplyscroll.css' type='text/css' media='all' />
		<link rel='stylesheet' id='jPages-css'  href='{static-server}/responsive/css/jPages.css' type='text/css' media='all' />
		<link rel='stylesheet' id='rating-css'  href='{static-server}/responsive/css/jquery.rating.css' type='text/css' media='all' />
		<link rel='stylesheet' id='ie-styles-css'  href='{static-server}/responsive/css/ie.css' type='text/css' media='all' />
		<link rel='stylesheet' id='Roboto-css'  href='http://fonts.googleapis.com/css?family=Roboto' type='text/css' media='all' />

		<script type='text/javascript' src="{static-server}/responsive/js/jquery-1.10.2.min.js"></script>
		<script type='text/javascript' src='{static-server}/responsive/js/html5.js'></script>
		<script type='text/javascript' src='{static-server}/responsive/js/bootstrap.min.js'></script>
		<script type='text/javascript' src='{static-server}/responsive/js/jquery.rating.js'></script>
		<script type='text/javascript' src='{static-server}/responsive/js/jquery.idTabs.min.js'></script>
		<script type='text/javascript' src='{static-server}/responsive/js/jquery.simplyscroll.js'></script>
		<script type='text/javascript' src='{static-server}/responsive/js/fluidvids.min.js'></script>
		<script type='text/javascript' src='{static-server}/responsive/js/jPages.js'></script>
		<script type='text/javascript' src='{static-server}/responsive/js/jquery.sidr.min.js'></script>
		<script type='text/javascript' src='{static-server}/responsive/js/jquery.touchSwipe.min.js'></script>
		<script type='text/javascript' src='{static-server}/responsive/js/jquery.swipemenu.init.js'></script>
		<script type='text/javascript' src='{static-server}/responsive/js/custom.js'></script>

		<!-- START BLOCK : additional-css-->
		<link rel="stylesheet" href="{static-server}/css/{filename}" type="text/css">
		<!-- END BLOCK : additional-css-->
		<script type="text/javascript" src="{static-server}/js/jquery.raty.min.js{jquery-tools},tinycon.min.js,jquery.cookie.js,jquery.fancybox.js,swfobject.js,j.js"></script>
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

	<body{onload} class="{layout-options}">

<div id="page">

	<header id="header" class="container">
		<div id="mast-head">
			<div id="logo">
				<a href="/" title="Magazine" rel="home"><img src="/responsive/images/logo.png" alt="Magazine" /></a>
			</div>
		</div>

				
        <nav class="navbar navbar-inverse clearfix nobot">
						
			<a id="responsive-menu-button" href="#swipe-menu">
			  <span class="icon-bar"></span>
			  <span class="icon-bar"></span>
			  <span class="icon-bar"></span>		
			</a>	

            <!-- Responsive Navbar Part 2: Place all navbar contents you want collapsed withing .navbar-collapse.collapse. -->
            <div class="nav-collapse" id="swipe-menu-responsive">

			<ul class="nav">
				
				<li>
				<span id="close-menu">
					<a href="#" class="close-this-menu">Close</a>
						<script type="text/javascript">
							jQuery('a.sidr-class-close-this-menu').click(function(){
								jQuery('div.sidr').css({
									'right': '-476px'
								});
								jQuery('body').css({
								'right': '0'
								});							
							});
						</script>
					
				</span>
				</li>
						
				<li><a href="/"><img src="/responsive/images/home.png" alt="Sākumlapa"></a></li>
				<li><a href="/">Jaunumi</a></li>
				<li><a href="/forums">Forums</a></li>
				<li class="dropdown"><a href="/raksti">Raksti</a>
					<ul class="sub-menu">
						<li><a href="/filmas">Filmas</a></li>
						<li><a href="/muzika">Mūzika</a></li>
						<li><a href="/speles">Spēles</a></li>
					</ul>
				</li>
				<li><a href="/steam-online">Steam</a></li>
			</ul>

            </div><!--/.nav-collapse -->
			
        </nav><!-- /.navbar -->
			
	</header><!-- #masthead -->

	<div id="headline" class="container">
	<div class="row-fluid">
		
		<div class="span3">
			<article class="post">
				<a href="#" title="Permalink to Donec consectetuer ligula vulputate sem tristique cursus" rel="bookmark">
				<img width="225" height="136" src="http://placehold.it/225x136" class="thumb" alt="" />
				</a>
				<div class="entry">
					<h3><a href="#" title="Permalink to Donec consectetuer ligula vulputate sem tristique cursus" rel="bookmark">Donec consectetuer ligula vulputate...</a></h3>
					<p>5 months ago </p>
				</div>
				<div class="clearfix"></div>
			</article>
		</div>
		
		<div class="span3">
			<article class="post">
				<a href="#" title="Permalink to Nam nibh arcu tristique eget pretium vitae libero ac risus" rel="bookmark">
				<img width="225" height="136" src="http://placehold.it/225x136" class="thumb" alt="" />
				</a>
				<div class="entry">
					<h3><a href="#" title="Permalink to Nam nibh arcu tristique eget pretium vitae libero ac risus" rel="bookmark">Nam nibh arcu tristique eget pretiu...</a></h3>
					<p>5 months ago </p>
				</div>
				<div class="clearfix"></div>
			</article>
		</div>

		<div class="span3">
			<article class="post">
				<a href="#" title="Permalink to Aliquam quam lectus pulvinar urna leo dignissim lorem" rel="bookmark">
				<img width="225" height="136" src="http://placehold.it/225x136" class="thumb" alt="" />
				</a>
				<div class="entry">
					<h3><a href="#" title="Permalink to Aliquam quam lectus pulvinar urna leo dignissim lorem" rel="bookmark">Aliquam quam lectus pulvinar urna l...</a></h3>
					<p>6 months ago </p>
				</div>
				<div class="clearfix"></div>
			</article>
		</div>

		<div class="span3">
			<article class="post">
				<a href="#" title="Permalink to Phasellus scelerisque massa molestie iaculis lectus pulvinar" rel="bookmark">
				<img width="225" height="136" src="http://placehold.it/225x136" class="thumb" alt="" />
				</a>
				<div class="entry">
					<h3><a href="#" title="Permalink to Phasellus scelerisque massa molestie iaculis lectus pulvinar" rel="bookmark">Phasellus scelerisque massa molesti...</a></h3>
					<p>6 months ago </p>
				</div>
				<div class="clearfix"></div>
			</article>
		</div>	
		
	</div>
	</div>

	<div id="intr" class="container">
		<div class="row-fluid">
			<div class="brnews span9">
					<!-- START BLOCK : user-menu-->
					<ul id="top-menu-right">
						<li{profile-sel}>
							<a href="/user/{currentuser-id}">Profils</a>
							<ul>
								<li><a href="/user/edit">Publiskā profila informācija</a></li>
								<li><a href="/user/avatar">Mans avatars</a></li>
								<li><a href="/user/settings">Mani iestatījumi</a></li>
								<li><a href="/user/security">Paroles maiņa</a></li>
								<li><a href="/user/email">E-pasta adreses maiņa</a></li>
								<li><a href="/user/changenick">Mainīt lietotājvārdu</a></li>
							</ul>
						</li>
						<li{cat-sel-319}><a href="/grupas">Grupas</a>
							<!-- START BLOCK : mygroups-->
							<ul id="user-group-menu">
								<!-- START BLOCK : myg-node-->
								<li><a href="/group/{id}"><img src="{img-server}/userpic/small/{avatar}" width="28" height="28" alt="" />{title}{add}</a></li>
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
								<li{cat-sel-2082}><a href="/grouped-profiles">Profilu sasaiste</a></li>
								<li{cat-sel-1822}><a href="/crows">Atbrīvotās vārnas</a></li>
								<li{cat-sel-1827}><a href="/reports">Iesniegtās sūdzības{reports-count}</a></li>
								<li{cat-sel-1132}><a href="/findby">Profilu meklētājs</a></li>
								<li{cat-sel-206}><a href="/?c=206">Random fakti</a></li>
								<li{cat-sel-199}><a href="/log">Administrācijas darbības</a></li>
								<li{cat-sel-255}><a href="/polladmin">Aptaujas</a></li>
								<li{cat-sel-229}><a href="/wallpaper_admin">Wallpapers</a></li>
								<li{cat-sel-451}><a href="/smslog">SMS maksājumi</a></li>
								<li{cat-sel-331}><a href="/?c=331">Karātavas</a></li>
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
						<li{cat-sel-585}><a class="notes" href="/piezimes" title="Piezīmes"><img src="{img-server}/bildes/fugue-icons/notebook.png" width="16" height="16" alt="Piezīmes" /></a></li>
						<li><a href="/logout/{logout-hash}">Iziet ({currentuser-nick})</a></li>
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
							<a rel="nofollow" class="external-login external-dr" title="Ienākt ar draugiem.lv pasi" href="https://api.draugiem.lv/authorize/?app=15005147&amp;hash=eaef1fd32cb572a292467e05f26cf774&amp;redirect=http%3A%2F%2Fexs.lv%2Fdraugiem-signup%2F" onclick="if (handle = window.open('https://api.draugiem.lv/authorize/?app=15005147&amp;hash=eaef1fd32cb572a292467e05f26cf774&amp;redirect=https%3A%2F%2Fexs.lv%2Fdraugiem-signup%2F&amp;popup=1', 'Dr_15005147', 'width=400, height=400, left=' + (screen.width ? (screen.width - 400) / 2 : 0) + ', top=' + (screen.height ? (screen.height - 400) / 2 : 0) + ',scrollbars=no')){handle.focus();return false;}"><span>Ienākt</span></a>

							<a rel="nofollow" href="/fb-login" class="external-login external-fb" title="Log in with FaceBook"><span>Log in</span></a>

							<a rel="nofollow" href="/twitter-login" class="external-login external-twitter" title="Log in with twitter"><span>Log in</span></a>

						</fieldset>
					</form>
					<!-- END BLOCK : login-form-->
			</div>
		
		
		<div class="search span3"><div class="offset1">
			<form method="get" id="searchform" action="/search/">
				<input type="hidden" name="cx" value="014557532850324448350:fe0imkmdxam" />
				<input type="hidden" name="cof" value="FORID:11" />
				<input type="hidden" name="ie" value="UTF-8" />
				<p><input type="text" value="Es meklēju..." onfocus="if ( this.value == 'Es meklēju...' ) { this.value = ''; }" onblur="if ( this.value == '' ) { this.value = 'Es meklēju...'; }" name="q" id="s" />
				<input type="submit" id="searchsubmit" value="Meklēt" /></p>
			</form>
		</div></div>
		</div>
	</div>

	<div id="content" class="container">

		<div id="main" class="row-fluid">
			<div id="main-left" class="span8">


				<!-- START BLOCK : page-path-->
				<p id="breadcrumbs">{page-path}</p>
				<!-- END BLOCK : page-path-->

				<!-- START BLOCK : profile-menu-->
				<h1>{user-nick}{user-menu-add}</h1>
				
				<div style="width:468px;height:60px;margin:8px auto">
					<script type="text/javascript" id="position_2918">
					  var ads_positions = ads_positions || [];
					  ads_positions.push(["2918", "", "document"]);
					  (function() {
					    if (!document.getElementById("ads_loader")) {
					      var script = document.createElement("script"); script.type = "text/javascript"; script.id = "ads_loader"; script.async = true;
					      script.src = ("https:" == document.location.protocol ? "https://" : "http://") + "static.adclick.lv/ads_loader__min.js?rand=" + (new Date()).getTime();
					      (document.getElementsByTagName("head")[0] || document.getElementsByTagName("body")[0]).appendChild(script);
					    }
					  })();
					</script>
				</div>

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

			</div><!-- #main-left -->

		<div id="sidebar" class="span4">




					<!-- START BLOCK : mb-box-->

					<h3 class="title"><span>Miniblogi{miniblog-add}</h3>
			<div id="tabwidget" class="widget tab-container box"> 
				<ul id="tabnav" class="clearfix"> 
					<li><h3><a href="/mb-latest?pg=0&amp;tab=all" class="{all-selected}remember-all ajax"><img src="/responsive/images/view-white-bg.png" alt="Visi">Visi</a></h3></li>
					<!-- START BLOCK : mb-tabs-->
					<li><h3><a href="/mb-latest?pg=0&amp;tab=friends" class="{friends-selected}remember-friends ajax"><img src="/responsive/images/time-white.png" alt="Draugu">Draugu</a></h3></li>
					<!-- END BLOCK : mb-tabs-->
					<li><h3><a href="/mb-latest?pg=0&amp;tab=music" class="{music-selected}remember-music ajax"><img src="/responsive/images/komen-putih.png" alt="Klausās">Klausās</a></h3></li>
				</ul> 

			<div id="tab-content miniblog-block" class="ajaxbox">
	 			{out}
			</div>
					<!-- END BLOCK : mb-box-->

			<div class="widget widget_latestpost"><h3 class="title"><span>Technology News</span></h3>
				<div class="latest-posts">
					<article class="post">
						<a class="image_thumb_zoom" href="#" title="Permalink to Porta lorem ipsum dolor sit amet, consectetur adipiscing risus" rel="bookmark">
						<img width="371" height="177" src="http://placehold.it/371x177" alt="" />
						</a>
						<h4 class="post-title">
						<a href="#" title="Permalink to Porta lorem ipsum dolor sit amet, consectetur adipiscing risus" rel="bookmark">Porta lorem ipsum dolor sit amet, c...</a>
						<span class="date">August 2, 2013</span>
						</h4>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer placerat id augue non dapibus. Morbi ut ipsum cond...</p>
					</article>
				
					<article class="post">
						<div class="entry clearfix">
							<a href="#" title="Permalink to Donec consectetuer ligula vulputate sem tristique cursus" rel="bookmark">
							<img width="225" height="136" src="http://placehold.it/225x136" class="thumb" alt="" />
							<h4 class="post-title">Donec consectetuer ligula vulputate...</h4>
							</a>
							<p>Nam nibh arcu, tristique eget pretium se...</p>
							<div class="meta">
								<span class="date">July 11, 2013</span>
							</div>
						</div>
					</article>

					<article class="post">
						<div class="entry clearfix">
							<a href="#" title="Permalink to Quisque sodales viverra ornare vitae libero ac risus" rel="bookmark">
							<img width="225" height="136" src="http://placehold.it/225x136" class="thumb" alt="" />
							<h4 class="post-title">Quisque sodales viverra ornare vita...</h4></a>
							<p>Quisque sodales viverra ornare. Aenean p...</p>
							<div class="meta">
								<span class="date">July 2, 2013</span>
							</div>
						</div>
					</article>
				</div>
			</div>
	
        				
		</div><!-- sidebar -->
		
		<div class="clearfix"></div>

		</div><!-- #main -->

		</div><!-- #content -->

	<footer id="footer" class="row-fluid">
		<div id="footer-widgets" class="container">
			<div class="footer-widget span3 block1">
				<div class="widget widget_latestpost">
					<h3 class="title"><span>Latest News</span></h3>
					<div class="latest-posts widget">
						<div class="latest-post clearfix">
							<a href="#"><img width="225" height="136" src="http://placehold.it/225x136" class="thumb fl" alt="" title="" /></a>
							<h4><a href="#" rel="bookmark" title="Lectus non rutrum pulvinar urna leo dignissim lorem">Lectus non rutrum pulvinar urna leo...</a></h4>
							<div class="post-time">August 12, 2013</div>
							<div class="ratings" style="float: none">
								<input class="star" type="radio" name="footer-latest-post-1" value="1" disabled="disabled"/>
								<input class="star" type="radio" name="footer-latest-post-1" value="2" disabled="disabled"/>
								<input class="star" type="radio" name="footer-latest-post-1" value="3" disabled="disabled"/>
								<input class="star" type="radio" name="footer-latest-post-1" value="4" disabled="disabled" checked="checked"/>
								<input class="star" type="radio" name="footer-latest-post-1" value="5" disabled="disabled"/>
							</div>
						</div>

						<div class="latest-post clearfix">
						<a href="#"><img width="225" height="136" src="http://placehold.it/225x136" class="thumb fl" alt="" title="" /></a>
						<h4><a href="#" rel="bookmark" title="Suspen disse auctor dapibus neque pulvinar urna leo">Suspen disse auctor dapibus neque p...</a></h4>
						<div class="post-time">August 11, 2013</div>
							<div class="ratings" style="float: none">
								<input class="star" type="radio" name="footer-latest-post-2" value="1" disabled="disabled"/>
								<input class="star" type="radio" name="footer-latest-post-2" value="2" disabled="disabled"/>
								<input class="star" type="radio" name="footer-latest-post-2" value="3" disabled="disabled"/>
								<input class="star" type="radio" name="footer-latest-post-2" value="4" disabled="disabled" checked="checked"/>
								<input class="star" type="radio" name="footer-latest-post-2" value="5" disabled="disabled"/>
							</div>
						</div>

						<div class="latest-post clearfix">
						<a href="#"><img width="225" height="136" src="http://placehold.it/225x136" class="thumb fl" alt="" title="" /></a>
						<h4><a href="#" rel="bookmark" title="Porta lorem ipsum dolor sit amet, consectetur adipiscing risus">Porta lorem ipsum dolor sit amet, c...</a></h4>
						<div class="post-time">August 2, 2013</div>
							<div class="ratings" style="float: none">
								<input class="star" type="radio" name="footer-latest-post-3" value="1" disabled="disabled"/>
								<input class="star" type="radio" name="footer-latest-post-3" value="2" disabled="disabled"/>
								<input class="star" type="radio" name="footer-latest-post-3" value="3" disabled="disabled"/>
								<input class="star" type="radio" name="footer-latest-post-3" value="4" disabled="disabled" checked="checked"/>
								<input class="star" type="radio" name="footer-latest-post-3" value="5" disabled="disabled"/>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="footer-widget span3 block2">
				<div class="widget">
					<h3 class="title"><span>Latest Tweets</span></h3>
					<a class="twitter-timeline" href="https://twitter.com/exs_lv" data-widget-id="404553406976516097" data-tweet-limit="2"{twitter-theme}>Tweets by @exs_lv</a>
										<script>!function(d, s, id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){ js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs); }}(document, "script", "twitter-wjs")
													;</script>
				
					<div id="magz-twitter-follow-link"><a target="_blank" href="http://twitter.com/exs_lv">Seko mums Twitter</a></div>
				</div>
			</div>
			
			<div class="footer-widget span3 block3">
				<div class="widget">
					<h3 class="title"><span>Tag Cloud</span></h3>
					<div class="tagcloud">
						<a href='#'>Blog</a>
						<a href='#'>Framework</a>
						<a href='#'>Grid</a>
						<a href='#'>Magazine</a>
						<a href='#'>Mobile</a>
						<a href='#'>Responsive</a>
						<a href='#'>Sidebar</a>
						<a href='#'>Themes</a>
						<a href='#'>WordPress</a>
					</div>
				</div>
			</div>
			
			<div class="footer-widget span3 block4">
				<div class="widget">
					<h3 class="title"><span>Social Media</span></h3>
						<div class="socmed clearfix">		
							<ul>
								<li>
									<a href="#"><img src="/responsive/images/rss-icon.png" alt=""></a>
									<h4>RSS</h4>
									<p>Subscribe</p>
								</li>
								<li>
									<a href="#"><img src="/responsive/images/twitter-icon.png" alt=""></a>
									<h4>37005</h4>
									<p>Followers</p>
								</li>
								<li>
									<a href="#"><img src="/responsive/images/fb-icon.png" alt=""></a>
									<h4>109</h4>
									<p>Fans</p>
								</li>
							</ul>
						</div>
				</div>
			</div>
			
			<div class="footer-widget span6 block5">
				<img class="footer-logo" src="/responsive/images/footer-logo.png" alt="Magazine">
					<div class="footer-text">
						<h4>About Magazine</h4>
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eius mod tempor incididu... </p>
					</div><div class="clearfix"></div>
			</div>

		</div><!-- footer-widgets -->

	
		<div id="site-info" class="container">
		
			<div id="footer-nav" class="fr">
				<ul class="menu">
					<li><a href="index.html">Home</a></li>
					<li><a href="about.html">About</a></li>
					<li><a href="blog.html">Blog</a></li>
					<li><a href="contact.html">Contact</a></li>
				</ul>
			</div>

			<div id="credit" class="fl">
				<p>All Right Reserved by minimalthemes</p>
			</div>

		</div><!-- .site-info -->
		
	</footer>

</div><!-- #wrapper -->

</body>
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
		<div id="scroll-up" title="Uz augšu"></div>
		<div id="wrapper">
			<div id="header"{page-persona}>
				<div style="position:absolute;right:20px;top:27px;width:728px;height:90px;z-index:4">
					<script type="text/javascript" id="position_2919">
					  var ads_positions = ads_positions || [];
					  ads_positions.push(["2919", "", "document"]);
					  (function() {
					    if (!document.getElementById("ads_loader")) {
					      var script = document.createElement("script"); script.type = "text/javascript"; script.id = "ads_loader"; script.async = true;
					      script.src = ("https:" == document.location.protocol ? "https://" : "http://") + "static.adclick.lv/ads_loader__min.js?rand=" + (new Date()).getTime();
					      (document.getElementsByTagName("head")[0] || document.getElementsByTagName("body")[0]).appendChild(script);
					    }
					  })();
					</script>
				</div>
				<div id="logo">
					<a id="exs-logo" href="/" title="Uz sākumlapu">exs.lv</a>
					<div id="tools-bar">
						<ul id="site-links">
							<li><a href="/blogs">Blogi</a></li>
							<li><a href="/img">Bilžu hostings</a></li>
							<li><a href="https://m.exs.lv/" title="Mobilā versija" rel="nofollow">Mobilā versija</a></li>
							<li><a href="/junk" title="Bilžu sadaļa">/junk</a></li>
							<li><a href="https://runescape.exs.lv/" title="RuneScape forums" rel="nofollow">rs.exs.lv</a></li>
							<li><a href="https://rp.exs.lv/" title="MTA San Andreas Roleplay serveris un forums" rel="nofollow">rp.exs.lv</a></li>
							<li><a href="https://lol.exs.lv/" title="League of Legends forums" rel="nofollow">lol.exs.lv</a></li>
							<li><a href="https://coding.lv/" title="Mājas lapu veidošanas un programmēšanas forums">coding.lv</a></li>
							<li><a href="/statistika" title="Statistika">Statistika</a></li>
							<li><a href="/flash-speles" title="Online flash spēles">Flash spēles</a></li>
						</ul>
						{current-date}
					</div>
				</div>
				<div id="top-menu">
					<ul id="top-menu-left">

					</ul>

				</div>
			</div>
			<div class="c"></div>
			<!-- START BLOCK : flash-message-->
			<div class="mbox {class}" id="flash-message">
				<p><a id="close-flash-message" href="#"><img src="{img-server}/bildes/fugue-icons/cross-button.png" alt="Aizvērt" title="Aizvērt" width="16" height="16" /></a> {message}</p>
			</div>
			<div class="c"></div>
			<!-- END BLOCK : flash-message-->

			<!-- START BLOCK : main-layout-left-->
			<div id="left">
				<div class="inner">

					<!-- START BLOCK : movie-search-->
					<h3>Meklēt filmu</h3>
					<div class="box">
						<form id="movie-search" method="get" action="/filmas/search">
							<!-- START BLOCK : genre-node-->
							<label style="font-size: 10px;line-height: 13px;"><input style="width:11px;height:11px;padding: 0;margin:2px;" type="checkbox" name="genres[]" value="{genre}"{checked} />{translated}</label><br />
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

					<h3><strong>UT 2004</strong> ut.exs.lv</h3>
					<div class="box">
						{ut-monitor}
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


					<h3>Šodien aktīvākie</h3>
					<div class="box">
						<ul class="tabs">
							<li><a rel="nofollow" href="/dailytop/users" class="active ajax"><span class="profile">Lietotāji</span></a></li>
							<li><a rel="nofollow" href="/dailytop/groups" class="ajax"><span class="users">Grupas</span></a></li>
						</ul>
						<div class="c"></div>
						<div id="daily-top" class="ajaxbox">{user-top}</div>
					</div>

					<h3>Nejaušs fakts</h3>
					<div class="box">
						<div id="random-fact" class="ajaxbox">{random-fact}</div>
					</div>

				</div>
			</div>
			<!-- END BLOCK : main-layout-left-->

			<div id="content" class="{layout-options}">
				<div class="inner">

				</div>
			</div>

			<!-- START BLOCK : main-layout-right-->
			<div id="right">
				<div class="inner">

					<!-- START BLOCK : junk-info-->
					<p><a href="/adm">Attēlu apstiprināšana{count}</a></p>
					<!-- END BLOCK : junk-info-->

					<!-- START BLOCK : profile-box-->
					<h3>{profile-nick}{custom_title}</h3>
					<div class="box">
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
						<div style="padding:0 0 0 15px;">
							<!-- START BLOCK : profilebox-pm-link-->
							<a href="/pm/write/?to={profile-id}" id="l-pm">Nosūtīt ziņu</a><br />
							<!-- END BLOCK : profilebox-pm-link-->
							<!-- START BLOCK : profilebox-warn-->
							<a href="/warns/{profile-id}" id="l-warn"{class}>Brīdinājumi{profile-warns}</a><br />
							<!-- END BLOCK : profilebox-warn-->

							<!-- START BLOCK : profilebox-blog-link-->
							<a href="{url}" id="l-blog">Blogs&nbsp;({count})</a><br />
							<!-- END BLOCK : profilebox-blog-link-->

							<!-- START BLOCK : profilebox-twitter-link-->
							<a rel="nofollow" href="https://twitter.com/{twitter}" id="l-twitter">{twitter}</a><br />
							<!-- END BLOCK : profilebox-twitter-link-->

							<!-- START BLOCK : profilebox-yt-link-->
							<a href="/youtube/{profile-id}/{yt-slug}" id="l-yt"><span class="yt">{yt-name}</span></a><br />
							<!-- END BLOCK : profilebox-yt-link-->

							<!-- START BLOCK : profilebox-lastfm-link-->
							<a rel="nofollow" href="http://www.last.fm/user/{name}" id="l-lastfm">{name}</a><br />
							<!-- END BLOCK : profilebox-lastfm-link-->
						</div>
						<div class="c"></div>
					</div>
					<!-- END BLOCK : profile-box-->
					<!-- START BLOCK : group-box-->
					<h3>{group-title}</h3>
					<div class="box">
						<a href="/group/{group-id}"><img id="profile-image" src="{group-av}" alt="{group-alt}" /></a>
					</div>
					<!-- END BLOCK : group-box-->



					<!-- START BLOCK : daily-best-->
					<h3>Dienas komentārs</h3>

					<div class="box">
						<ul class="blockhref mb-col">
							<li>
								<a href="{best-link}">
									<img class="av" src="{best-avatar}" width="45" height="45" alt=""/>
									<span class="author">{best-nick}</span>
									<span class="post-rating">+{best-rating}</span>
									{best-comment}
								</a>
							</li>
						</ul>
					</div>
					<!-- END BLOCK : daily-best-->

					<!-- START BLOCK : side-junk-->
					<h3>/junk</h3>
					<div class="box junk-box">
						<!-- START BLOCK : side-junk-node-->
						<a href="/junk/{id}" title="{title}">
							<img src="//img.exs.lv{thb}" alt="" class="av" />
							<span style="">{posts}</span>
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
						<a href="//img.exs.lv/dati/wallpapers/{wallpaper-image}">
							<img src="//img.exs.lv/dati/wallpapers/thb/{wallpaper-image}" alt="dienas ekrāntapete" />
						</a><br />
						<a href="/wallpapers">Tapetes</a>
					</div>
					<!-- END BLOCK : daily-wallpaper-->

					

				</div>
			</div>
			<!-- END BLOCK : main-layout-right-->
			<div class="c"></div>

			<div id="footer">
				<div id="online-users">
					<ul id="ucl">
						<li id="ucd"></li>
						<li class="user"><a href="/lietotaji/klase/0">Lietotājs</a></li>
						<li class="editor"><a href="/lietotaji/klase/3">Rakstu autors</a></li>
						<li class="moder"><a href="/lietotaji/klase/2">Moderators</a></li>
						<li class="admin"><a href="/lietotaji/klase/1">Administrators</a>
						</li>
					</ul>
					Lapu šobrīd skatās {page-onlinetotal} lietotāji, no tiem reģistrētie:<br />
					<span id="online-list">{page-onlineusers}</span>
				</div>
				<div class="infoblock">
					<div class="inner">
						Jaunākie raksti: {footer-topics}
					</div>
				</div>
				<div class="infoblock">
					<div class="inner">
						Pēdējie miniblogi: {footer-mb}
					</div>
				</div>
				<div class="infoblock">
					<div class="inner">
						<p>&copy; <a href="https://openidea.lv/" title="Mājas lapas izstrāde un uzturēšana" rel="nofollow">SIA Open Idea</a>, 2005-{current-year}</p>
						<p>
							E-pasts: info@exs.lv<br />
							Tālrunis: <span id="noindex-phone"></span><br />
							Mājas lapu izstrāde un hostings.
						</p>
					</div>
				</div>
				<div class="infoblock">
					<div class="inner">
						<ul id="internal-links">
							<li><a href="/read/lietosanas-noteikumi">Lietošanas noteikumi</a></li>
							<li><a href="/sitemap">Lapas karte</a></li>
							<li><a href="/reklama">Reklāma portālā</a></li>
						</ul>
						<p>Teamspeak 3:<br />ts.exs.lv</p>
					</div>
				</div>
				<div class="c"></div>
			</div>
		</div>
		
		<!-- START BLOCK : smartad-eu-->
		<!-- smartad.eu -->
		<script type='text/javascript'>/* <![CDATA[ */
			var _smartad = _smartad || new Object();
			_smartad.page_id = Math.floor(Math.random() * 10000001);
			if (!_smartad.prop) {
				_smartad.prop = 'screen_width=' + (window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth) + unescape('%26screen_height=') + (window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight) + unescape('%26os=') + navigator.platform + unescape('%26refurl=') + encodeURIComponent(document.referrer || '') + unescape('%26pageurl=') + encodeURIComponent(document.URL || '') + unescape('%26rnd=') + new Date().getTime();
			}
			(function() {
				if (_smartad.space) {
					_smartad.space += ',8149d5ee-611f-4247-9dbd-5a2dcb6e5529';
				} else {
					_smartad.space = '8149d5ee-611f-4247-9dbd-5a2dcb6e5529';
					_smartad.type = 'onload';
					var f = function() {
						var d = document, b = d.body || d.documentElement || d.getElementsByTagName('BODY')[0], n = b.firstChild, s = d.createElement('SCRIPT');
						s.type = 'text/javascript', s.language = 'javascript', s.async = true, s.charset = 'UTF-8';
						s.src = location.protocol + '//serving.bepolite.eu/script?space=' + _smartad.space + unescape('%26type=') + _smartad.type + unescape('%26page_id=') + _smartad.page_id + unescape('%26') + _smartad.prop;
						n ? b.insertBefore(s, n) : b.appendChild(s);
					};
					if (document.readyState === 'complete') {
						f();
						delete _smartad.space;
					} else {
						if (window.addEventListener) {
							window.addEventListener('load', f, false);
						} else if (window.attachEvent) {
							window.attachEvent('onload', f);
						}
					}
				}
			})();
			/* ]]> */</script>
		<!-- END BLOCK : smartad-eu-->

		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-4190387-2', 'auto');
		  ga('send', 'pageview');

		</script>

		<script type='text/javascript' src="//keytarget.adnet.lt/js/init-for-BBEposCodes-withExtra.js?coCo=lv"></script>
		<!-- Position: go.eu.bbelements.com exs.lv(22484) / Pixel_Visas_Lapas_LV(1) / Pixel_Visas_Lapas_LV(10) / Pixel(21) -->
		<script type='text/javascript' charset='utf-8' src='https://go.eu.bbelements.com/please/code?j-22484.1.10.21.0.0._blank'></script>
		<noscript>
		<a href="https://go.eu.bbelements.com/please/redirect/22484/1/10/21/" target="_blank"><img src="https://go.eu.bbelements.com/please/showit/22484/1/10/21/?typkodu=img" border='0' alt='' /></a>
		</noscript>

	</body>
</html>

