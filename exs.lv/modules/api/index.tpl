<!DOCTYPE html>
<html lang="lv">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width" />
	<title>exs API dokumentācija &middot; exs.lv</title>
	<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
	<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:400,400italic,500&amp;subset=latin-ext,cyrillic,latin" type="text/css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.4.0/styles/default.min.css">
	<link rel="stylesheet" href="/modules/api/api.css" type="text/css">
    <script src="{static-server}/js/jquery-1.10.2.min.js"></script>
    <script src="/modules/api/api.js"></script>
</head>
<body>
    <div id="scroll-up" title="Uz augšu"></div>
	<div class="upper_line_outer">
        <div id="upper_line">
            <h1 class="site_title"><a href="/api">exs.lv API docs</a></h1>
            <ul class="top_navig">
                <li><a {active-0}href="/api/a" title="Android lietotnes dokumentācija">Android</a></li>
                <li><a {active-1}href="/api/i" title="iOS lietotnes dokumentācija">iOS</a></li>
                <li><a href="//exs.lv" title="Uz exs.lv mājaslapu">exs.lv</a></li>
            </ul>
        </div>
        <div class="c"></div>
    </div>
    <div id="cols_wrapper">
        <div class="col col_left">
            <!-- START BLOCK : android-logo -->
            <p class="side_logo"><img src="/bildes/exsapi/android.png"></p>
            <!-- END BLOCK : android-logo -->
            <!-- START BLOCK : ios-logo -->
            <p class="side_logo"><img src="/bildes/exsapi/ios.png"></p>
            <!-- END BLOCK : ios-logo -->
            <div class="col_divider" style="margin-top:0"></div>
            <ul class="side_navig">
                <!-- START BLOCK : android-navig -->
                <li><a class="{active-intro}" href="/api/a">Ievads</a></li>
                <li><a class="{active-miniblogs}" href="/api/a/miniblogs">Miniblogi</a></li>
                <li><a class="{active-messages}" href="/api/a/messages">Vēstules</a></li>
                <li><a class="{active-groups}" href="/api/a/groups">Domubiedru grupas</a></li>
                <li><a class="{active-other}" href="/api/a/other">Cits</a></li>
                <!-- END BLOCK : android-navig -->
                <!-- START BLOCK : ios-navig -->
                <li><a class="{active-intro}" href="/api/i">Ievads</a></li>
                <li><a class="{active-miniblogs}" href="/api/i/miniblogs">Miniblogi</a></li>
                <li><a class="{active-messages}" href="/api/i/messages">Vēstules</a></li>
                <li><a class="{active-groups}" href="/api/i/groups">Domubiedru grupas</a></li>
                <li><a class="{active-other}" href="/api/i/other">Cits</a></li>
                <!-- END BLOCK : ios-navig -->
            </ul>
            <div class="col_divider"></div>
        </div>
        <div class="col col_right">
            {page_content}
        </div>
    </div>
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.4.0/highlight.min.js"></script>
<script>hljs.initHighlightingOnLoad();</script>
</body>
</html>
