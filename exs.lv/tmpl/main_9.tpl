<!DOCTYPE html>
<html lang="lv" prefix="fb:http://www.facebook.com/2008/fbml">
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
    <script>var mb_refresh_limit={mb-refresh-limit},current_user={currentuser-id},new_msg_count={new-messages-count},c_url="{page-url}";</script>
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Ubuntu&amp;subset=latin,cyrillic,latin-ext" type="text/css">
    <link rel="stylesheet" href="{static-server}/css/core.css,runescape.css,jquery.cluetip.css" type="text/css">
    <!-- START BLOCK : additional-css-->
    <link rel="stylesheet" href="{static-server}/css/{filename}" type="text/css">
    <!-- END BLOCK : additional-css-->
    <script src="{static-server}/js/jquery.min.js,tinycon.min.js,jquery.cookie.js,jquery.cluetip.js,jquery.fancybox.js,jquery.raty.min.js,runescape.js,mcp.js,j.js"></script>
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
                {title: 'Brīdinājuma teksts (oranžs)', block: 'p', classes: 'text-notice'},
                {title: 'Brīdinājuma teksts (sarkans)', block: 'p', classes: 'text-notice-red'},
                {title: 'Attēls (pa labi)', block: 'span', classes: 'rs-image-right'},
                {title: 'Attēls (pa kreisi)', block: 'span', classes: 'rs-image-left'}
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
    <script>
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
    <!-- START BLOCK : rs-background-css -->
    <style type="text/css">
        body {
            background:url('{img-server}/bildes/runescape/wallpapers/{background-title}') no-repeat fixed 50% 0;
        }
        @media only screen and (min-width:1920px) {
            body { background-size:cover; }
        }
    </style>
    <!-- END BLOCK : rs-background-css -->
</head>
<body class="{layout-options}">
    <div id="scroll-up" title="Uz augšu"></div>
    <div class="top-navig">
        <nav>
            <ul class="droplist">
                <li class="rs-logo"><a style="padding:0" href="/"><img src="{img-server}/bildes/runescape/rs-logo-sm.png" alt=""></a></li>
                <li><a href="/" class="dropdown">runescape.exs.lv</a>
                    <span class="arrow arrow-down"></span>
                    <ul>
                        <li><a href="//exs.lv">exs.lv</a></li>
                        <li><a href="//lol.exs.lv">lol.exs.lv</a></li>
                        <li><a href="//rp.exs.lv">rp.exs.lv</a></li>
                        <li><a rel="nofollow" href="https://coding.lv">coding.lv</a></li>
                        <li>&nbsp;</li>
                    </ul>
                </li>
                <li><a href="javascript:void(0)" class="dropdown">Hiscores</a>
                    <span class="arrow arrow-down"></span>
                    <ul>
                        <li><a href="http://scores.kopideja.lv/Invention.htm" rel="nofollow" target="_blank">Invention HS (LV)</a></li>
                        <li><a href="http://services.runescape.com/m=hiscore/a=1/ranking?category_type=0&table=27&page=1" rel="nofollow" target="_blank">Invention HS</a></li>
                        <li class="as-last">&nbsp;</li>
                        <li><a href="//tops.exs.lv/runescape" rel="nofollow" target="_blank">tops.exs.lv</a></li>
                        <li><a href="http://www.kopideja.lv/scores" rel="nofollow" target="_blank">LV hiscores</a></li>
                        <li><a href="http://www.kopideja.lv/oldscores" rel="nofollow" target="_blank">LV OSRS hiscores</a></li>
                        <li>&nbsp;</li>
                    </ul>
                </li>
                <li><a href="javascript:void(0)" class="dropdown">Citas saites{is-new}</a>
                    <span class="arrow arrow-down"></span>
                    <ul>
                        <li><a class="dropdown" href="javascript:void(0)">Ekrāntapetes</a>
                            <span class="arrow arrow-down"></span>
                            <ul class="flip-side" style="width:194px">
                                <li class="as-last">&nbsp;</li>
                                <li><a class="paint" href="https://imgur.com/a/m9pUt/all" rel="nofollow" target="_blank">imgur #1</a></li>
                                <li><a class="paint" href="https://imgur.com/gallery/b1NsF" rel="nofollow" target="_blank">imgur #2</a></li>
                                <li><a class="paint" href="https://www.flickr.com/photos/126721369@N06/albums/" rel="nofollow" target="_blank">RuneScape Flickr</a></li>
                                <li><a class="paint" href="http://runescape.wikia.com/wiki/Wallpaper" rel="nofollow" target="_blank">runescape.wikia</a></li>
                                <li>&nbsp;</li>
                            </ul>
                        </li>
                        <li><a href="http://runescape.wikia.com/wiki/RuneScape_Wiki" rel="nofollow" target="_blank">runescape.wikia</a></li>
                        <li><a href="http://warbandtracker.com/schedule.php" rel="nofollow" target="_blank">Warbands taimeris</a></li>
                        <li><a href="http://forums.zybez.net/runescape-2007-prices" rel="nofollow" target="_blank">Zybez OSRS market</a></li>
                        <li class="as-last">&nbsp;</li>
                        <li><a href="http://z8.invisionfree.com/lrc" rel="nofollow" target="_blank">LRC forums</a></li>
                        <li><a href="http://z13.invisionfree.com/Latvian_Archers" rel="nofollow" target="_blank">Latvian Archers</a></li>
                        <li><a href="http://z10.invisionfree.com/Janis_Vimba/" rel="nofollow" target="_blank">JV forums</a></li>
                        <li>&nbsp;</li>
                    </ul>
                </li>
            </ul>
            <ul class="droplist nav-right">
                <!-- START BLOCK : rsmod-nav -->
                <li{active-rsmod}><a href="#" class="dropdown">RS Mod</a>
                    <span class="arrow arrow-down"></span>
                    <ul>
                        <li><a href="/rsfacts">RuneScape fakti</a></li>
                        <li><a href="/modules/runescape/1000-rs-facts.txt">1000 faktu saraksts</a></li>
                        <li><a href="/all-quests">Pamācību saraksti</a></li>
                        <li><a href="/skills">Prasmju prasības</a></li>
                        <li><a href="/series">Kvestu sērijas</a></li>
                        <li><a href="/atkritne">Dzēstie raksti</a></li>
                        <li>&nbsp;</li>
                    </ul>
                </li>
                <!-- END BLOCK : rsmod-nav -->
                <!-- START BLOCK : mod-nav -->
                <li{active-mod}><a href="#" class="dropdown">Mod</a>
                    <span class="arrow arrow-down"></span>
                    <ul>
                        <li><a href="/banned">Liegumi</a></li>
                        <li><a href="/crows">Atbrīvotās vārnas</a></li>
                        <li><a href="/reports">Sūdzības {reports-count}</a></li>
                        <li><a href="/log">Darbību vēsture</a></li>
                        <li><a href="/polladmin">Aptaujas</a></li>
                        <li>&nbsp;</li>
                    </ul>
                </li>
                <!-- END BLOCK : mod-nav -->
                <!-- START BLOCK : auth-nav -->
                <li><a href="/img">Bilžu hosts</a></li>
                <li{cat-sel-104}><a href="/pm">Vēstules<span id="new-msg">{new-messages}</span></a></li>
                <li{cat-sel-646}><a href="/user/{currentuser-id}" class="dropdown">Profils{paint-is-new}</a>
                    <span class="arrow arrow-down"></span>
                    <ul>
                        <li><a class="dropdown" href="javascript:void(0)">Noformējums</a>
                            <span class="arrow arrow-down"></span>
                            <ul class="flip-side" style="width:194px">
                                <li class="as-last">&nbsp;</li>
                                <li><a class="paint" href="/?bg=elves">fons: Prifddinas</a></li>
                                <li><a class="paint" href="/?bg=goats">fons: Goats</a></li>
                                <li><a class="paint" href="/?bg=map">fons: RuneScape karte</a></li>
                                <li><a class="paint" href="/?layout=sticky">kolonnas blakus</a></li>
                                <li><a class="paint" href="/?layout=not-sticky">kolonnas sānos</a></li>
                                <li>&nbsp;</li>
                            </ul>
                        </li>
                        <li class="as-last">&nbsp;</li>
                        <li><a href="/user/edit">Profila informācija</a></li>
                        <li><a href="/user/avatar">Mans avatars</a></li>
                        <li><a href="/user/settings">Mani iestatījumi</a></li>
                        <li><a href="/user/security">Paroles maiņa</a></li>
                        <li><a href="/user/email">E-pasta adreses maiņa</a></li>
                        <li><a href="/user/changenick">Mainīt lietotājvārdu</a></li>
                        <li>&nbsp;</li>
                    </ul>
                </li>
                <li><a href="/logout/{logout-hash}">Iziet ({currentuser-nick})</a></li>
                <!-- END BLOCK : auth-nav -->
            </ul>
            <!-- START BLOCK : login-form-->
            <ul id="login-block" class="nav-right">
                <li{cat-sel-106}><a href="/register">Reģistrēties</a></li>
                <li>
                    <form id="login-form" action="{page-loginurl}" method="post">
                        <fieldset>
                            <input type="hidden" name="xsrf_token" value="{xsrf}" />
                            <label><input id="login-nick" size="16" name="niks" type="text" placeholder="Lietotājvārds"></label>
                            <label><input id="login-pass" size="16" name="parole" type="password" placeholder="Parole"></label>
                            <label><input name="login-submit" id="login-submit" class="login-submit" value="Ienākt" type="submit"></label>
                        </fieldset>
                    </form>
                </li>
                <li class="less-padding">
                    <a class="external-login external-dr" title="Ienākt ar draugiem.lv pasi" href="https://api.draugiem.lv/authorize/?app=15005147&amp;hash=f7deb7618bdd54f1dabedac5d92943b2&amp;redirect=https%3A%2F%2Frunescape.exs.lv%2Fdraugiem-signup%2F" onclick="if(handle=window.open('https://api.draugiem.lv/authorize/?app=15005147&amp;hash=f7deb7618bdd54f1dabedac5d92943b2&amp;redirect=https%3A%2F%2Frunescape.exs.lv%2Fdraugiem-signup%2F&amp;popup=1','Dr_15005147' ,'width=400, height=400, left='+(screen.width?(screen.width-400)/2:0)+', top='+(screen.height?(screen.height-400)/2:0)+',scrollbars=no')){handle.focus();return false;}"><span>Ienākt</span></a>
                </li>
                <li class="less-padding">
                    <a href="/fb-login" class="external-login external-fb" title="Log in with FaceBook"><span>Log in</span></a>
                </li>
            </ul>
            <!-- END BLOCK : login-form-->
        </nav>
    </div>
    <div id="wrapper">
        <div id="top-menu">
            <ul id="top-menu-left">
                <li{cat_sel_1863}><a class="first" href="/">Lobby</a></li>
                <li{cat-sel-102}><a href="/kvestu-pamacibas">Kvesti</a>
                    <ul>
                        <li><a href="/kvestu-pamacibas">Sērijas</a></li>
                        <li><a href="/p2p-kvesti">P2P kvesti</a></li>
                        <li><a href="/f2p-kvesti">F2P kvesti</a></li>
                        <li><a href="/mini-kvesti">Minikvesti</a></li>
                        <li><a href="/kvestu-pamacibas/stats">Statistika</a></li>
                        <li><a href="/kvestu-pamacibas/facts">Fakti</a></li>
                        <li><a href="/kvestu-pamacibas/skill-reqs">Prasmju prasības</a></li>
                    </ul>
                </li>
                <li{cat-sel-160}><a href="/minispeles">Minispēles</a></li>
                <li{cat-sel-4}><a href="/prasmes">Prasmes</a>
                    <ul>
                        <li><a href="/prasmes">Prasmju saraksts</a></li>
                        <li><a href="/prasmes/xp-table">XP tabula</a></li>
                        <li><a href="/prasmes/facts">Fakti</a></li>
                    </ul>
                </li>
                <li{cat-sel-791}><a href="/gildes">Ģildes</a></li>
                <li{cat-sel-1903}><a class="dropdown" href="#">Arhīvs</a>
                    <span class="arrow">&nbsp;</span>
                    <ul>
                        <li{cat-sel-599}><a href="/runescape">Ziņas</a></li>
                        <li{cat-sel-195}><a href="/celvezi">Ceļveži</a></li>
                        <li{cat-sel-792}><a href="/distractions-diversions">D&amp;D</a></li>
                        <li{cat-sel-2046}><a href="/achievements">Achievements</a></li>
                        <li{cat-sel-1087}><a href="/oss-guides">Oldschool RS</a></li>
                        <li{cat-sel-5}><a href="/padomi">Dažādi raksti</a></li>
                        <li{cat-sel-789}><a href="/stasti-un-vesture">RS stāsti &amp; vēsture</a></li>
                    </ul>
                </li>
            </ul>
            <!-- START BLOCK : user-menu-->
            <ul id="top-menu-right">
                <li{cat-sel-319}><a href="/grupas">Grupas</a>
                    <!-- START BLOCK : mygroups-->
                    <ul id="user-group-menu">
                        <!-- START BLOCK : myg-node-->
                        <li><a href="/group/{id}"><img src="{img-server}/userpic/small/{avatar}" width="28" height="28" alt="">{title}&nbsp;&nbsp;{unread}</a></li>
                        <!-- END BLOCK : myg-node-->
                    </ul>
                    <!-- END BLOCK : mygroups-->
                </li>
                <!-- START BLOCK : user-modlink-->
                <!-- END BLOCK : user-modlink-->
                <!-- START BLOCK : user-approvelink-->
                <li{cat-sel-116}><a href="/write/list">Raksti{new-approve}</a></li>
                <!-- END BLOCK : user-approvelink-->
                <li{cat-sel-1867}><a href="/gallery/{currentuser-id}">Galerija</a></li>
                <li{mb-sel}><a href="/say/{currentuser-id}">Miniblogs</a></li>
                <!-- START BLOCK : user-write-->
                <li{cat-sel-116}><a href="/write">Raksti</a></li>
                <!-- END BLOCK : user-write-->
                <li{cat-sel-1905}><a href="/myblog">Blogs</a></li>
                <li{cat-sel-661}><a href="/rs">Forums</a></li>
            </ul>
            <!-- END BLOCK : user-menu-->
        </div>
        <div id="space" class="c"></div>

        <div id="outer_content">
        
            <!-- START BLOCK : flash-message-->
            <div id="flash-message" class="mbox">
                <p>
                    <a id="close-flash-message" href="#">
                        <img src="{img-server}/bildes/fugue-icons/cross-button.png" alt="Aizvērt" title="Aizvērt" width="16" height="16">
                    </a>&nbsp;{message}
                </p>
            </div>
            <div class="c"></div>
            <!-- END BLOCK : flash-message-->
            
            <!-- START BLOCK : main-layout-right -->
            <div id="right" class="{layout-right-class}">

                <!-- START BLOCK : notification-list -->
                <h3>Tavi notikumi</h3>
                <div class="box">{out}</div>
                <!-- END BLOCK : notification-list -->
                
                <!-- START BLOCK : latest-images-right -->
                <h3>Jaunākie attēli</h3>
                <div class="box box-images">
                    <div class="ajaxbox">{latest-images}</div>
                </div>
                <!-- END BLOCK : latest-images-right -->

                <!-- START BLOCK : latest-pages -->
                <h3>Jaunākie raksti</h3>
                <div class="box">
                    <div id="lat" class="ajaxbox">{latest-pages}</div>
                </div>
                <!-- END BLOCK : latest-pages -->
                
                <!-- START BLOCK : communication-box -->
                <h3>Saziņas kanāli</h3>
                <div class="box box-communication">
                    <div>
                        <img src="{img-server}/bildes/runescape/ico-discord-app-small.png" title="exs Discord kanāls" alt="">
                        <a href="/say/20867/4609657-discord-kanals-ir-izveidots-cik">Discord kanāls</a>
                    </div>
                </div>
                <!-- END BLOCK : communication-box -->

                <!-- START BLOCK : poll-box-->
                <h3><img class="box-icon" src="{img-server}/bildes/fugue-icons/chart_1.png" alt="Aptauja">Jaunākā aptauja</h3>
                <div class="box poll-box">
                    <p><strong>{poll-title}</strong></p>
                    <!-- START BLOCK : poll-answers-->
                    <ol class="poll-answers">
                        <!-- START BLOCK : poll-answers-node-->
                        <li>{poll-answer-question}<div><span>{poll-answer-percentage}%</span><div style="width:{poll-answer-percentage}%"></div></div></li>
                        <!-- END BLOCK : poll-answers-node-->
                    </ol>
                    <span class="poll-text">
                        Balsojuši: {poll-totalvotes}<br />
                        <a href="{ppage-id}">Komentāri</a> &middot; <a href="/aptaujas">Senākas aptaujas</a>
                    </span>
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

            </div>
            <!-- END BLOCK : main-layout-right -->

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
            </div>          
                
            <!-- START BLOCK : main-layout-left -->
            <div id="left">

                <!-- START BLOCK : profile-box-->
                <h3>{profile-nick}</h3>
                <div class="box">
                    <a href="{url}">
                        <img id="profile-image" class="pimg-{profile-id}" src="{avatar}" alt="{profile-nick}" />
                    </a>
                    {profile-top-awards}
                    <!-- START BLOCK : profilebox-pm-link-->
                    <a href="/pm/write/?to={profile-id}" id="l-pm">Nosūtīt PM</a><br>
                    <!-- END BLOCK : profilebox-pm-link-->
                    <!-- START BLOCK : profilebox-warn-->
                    <a href="/warns/{profile-id}" id="l-warn"{class}>Brīdinājumi{profile-warns}</a><br>
                    <!-- END BLOCK : profilebox-warn-->
                    <!-- START BLOCK : profilebox-blog-link-->
                    <a href="{url}" id="l-blog">Blogs&nbsp;({count})</a><br>
                    <!-- END BLOCK : profilebox-blog-link-->
                    <!-- START BLOCK : profilebox-twitter-link-->
                    <a rel="nofollow" href="https://twitter.com/{twitter}" id="l-twitter">{twitter}</a><br>
                    <!-- END BLOCK : profilebox-twitter-link-->
                    <!-- START BLOCK : profilebox-yt-link-->
                    <!-- END BLOCK : profilebox-yt-link-->
                    <div class="c"></div>
                </div>
                <!-- END BLOCK : profile-box-->
                
                <!-- START BLOCK : latest-images -->
                <h3>Jaunākie attēli</h3>
                <div class="box box-images">
                    <div class="ajaxbox">{latest-images}</div>
                </div>
                <!-- END BLOCK : latest-images -->

                <!-- START BLOCK : mb-box-->
                <h3>Miniblogi{miniblog-add}</h3>
                <div class="box">
                    <!-- START BLOCK : mb-tabs-->
                    <ul class="tabs">
                        <li><a href="/mb-latest?pg=0&amp;tab=all" class="{all-selected}remember-all ajax"><span class="comments">Ārpusē</span></a></li>
                        <li><a href="/mb-latest?pg=0&amp;tab=friends" class="{friends-selected}remember-friends ajax"><span class="friends">Grupās</span></a></li>
                    </ul>
                    <div class="c"></div>
                    <!-- END BLOCK : mb-tabs-->
                    <div id="miniblog-block" class="ajaxbox">{out}</div>
                </div>
                <!-- END BLOCK : mb-box-->

                <!-- START BLOCK : groups-l-list-->
                <h3><img class="box-icon" src="{img-server}/bildes/fugue-icons/xfn-colleague.png" alt="Aptauja">Jaunākās grupas</h3>
                <div class="box new-groups">
                    <!-- START BLOCK : groups-l-node-->
                    <p>
                        <img style="" src="{img-server}/userpic/medium/{avatar}" alt="">
                        <a class="has-ellipsis" href="{link}">{title}</a>
                    </p>
                    <!-- END BLOCK : groups-l-node-->
                    <div class="see-other-groups">
                        <a href="/grupas">Viss saraksts &raquo;</a>
                    </div>
                </div>
                <!-- END BLOCK : groups-l-list-->

                <!-- START BLOCK : runescape-facts-box -->
                <h3>RuneScape fakts <a class="fetch-new-fact" href="#" title="Atlasīt jaunu faktu"></a></h3>
                <div class="box facts-box">{random-fact}</div>
                <!-- END BLOCK : runescape-facts-box -->

            </div>
            <!-- END BLOCK : main-layout-left -->
            
            <div class="c"></div>

            <div id="footer">
                <div id="online-users">
                    Lapu šobrīd skatās {page-onlinetotal} lietotāji, no tiem reģistrētie:<br /><span style="font-size:12px">{page-onlineusers}</span>
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
                        <p>&copy; {openidea}, 2005-{current-year}</p>
                        <p>runescape.exs.lv ir neoficiāls RuneScape spēlētāju sarunu un pamācību forums</p>
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
    </div>

<script>!function(d,s,id){ var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){ js=d.createElement(s);js.id=id;js.async=true;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs); } }(document,"script","twitter-wjs");</script>

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
