<!DOCTYPE html>
<html lang="lv">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width" />
    <meta name="robots" content="noindex">
	<title>exs API dokumentācija &middot; exs.lv</title>
	<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
	<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:400,400italic,500&amp;subset=latin-ext,cyrillic,latin" type="text/css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.4.0/styles/default.min.css">
	<link rel="stylesheet" href="/assets/api.css" type="text/css">
    <script src="{static-server}/js/jquery-1.10.2.min.js"></script>
    <script src="/assets/api.js"></script>
</head>
<body>
    <div id="scroll-up" title="Uz augšu"></div>
	<div class="upper_line_outer">
        <div id="upper_line">
            <h1 class="site_title"><a href="/i">EXS.LV iOS API</a></h1>
            <ul class="top_navig" style="float:right">
                <!--<li><a href="/a" title="Android lietotnes dokumentācija">Android</a></li>
                <li><a href="/i" title="iOS lietotnes dokumentācija">iOS</a></li>//-->
                <li><a style="padding-right:0" href="//exs.lv" title="Uz exs.lv mājaslapu">uz exs.lv</a></li>
            </ul>
        </div>
        <div class="c"></div>
    </div>
    <div id="cols_wrapper">
        <div class="col col_left">
            <!-- START BLOCK : android-logo -->
            <p class="side_logo"><img src="/assets/android.png"></p>
            <!-- END BLOCK : android-logo -->
            <!-- START BLOCK : ios-logo -->
            <p class="side_logo"><img src="/assets/ios.png"></p>
            <!-- END BLOCK : ios-logo -->
            <div class="col_divider" style="margin-top:0"></div>
            <ul class="side_navig">
                <!-- START BLOCK : android-navig -->
                <li><a class="{active-changeset}" href="/a/changeset">Izmaiņu vēsture</a></li>
                <li><a class="{active-intro} inactive" href="/a/intro">Ievads</a></li>
                <li><a class="{active-miniblogs} inactive" href="/a/miniblogs">Miniblogi</a></li>
                <li><a class="{active-groups} inactive" href="/a/groups">Grupas</a></li>
                <li><a class="{active-inbox} inactive" href="/a/inbox">Vēstules</a></li>
                <li><a class="{active-other} inactive" href="/a/other">Dažādi</a></li>
                <li><a class="{active-collections}" href="/a/collections">Kolekcijas</a></li>
                <!-- END BLOCK : android-navig -->
                <!-- START BLOCK : ios-navig -->
                <li><a class="{active-changeset}" href="/i/changeset">Izmaiņu vēsture</a></li>
                <li><a class="{active-intro}" href="/i/intro">Ievads</a></li>
                <li><a class="{active-profiles}" href="/i/profiles">Profili</a></li>
                <li><a class="{active-inbox}" href="/i/inbox">Vēstules</a></li>
                <li><a class="{active-miniblogs}" href="/i/miniblogs">Miniblogi</a></li>
                <li><a class="{active-groups}" href="/i/groups">Grupas</a></li>
                <li><a class="{active-other}" href="/i/other">Dažādi</a></li>
                <li><a class="{active-collections}" href="/i/collections">Kolekcijas</a></li>
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
