<?php

$tpl_options = 'no-left';

//facebok login
$fb_api_id = '480869031952190';
$fb_api_key = 'cbf65eee0645935797da97f33b6a77b1';

//draugiem pase
$dr_api_id = 15005147;
$dr_api_key = 'f38c225b8f65df03c5aaa847b1f052a9';

//koementāri (level 1) vienā foruma lapā
$comments_per_page = 25;

//karma no kuras sakot var labot savus postus
$min_post_edit = 0;

//karma no kuras sakot var labot savus rakstus
$min_page_edit = 0;

//cik ilgi var labot savus rakstus (0 = bezgalīgi)
$page_edit_time = 0;

//aptauju sadaļa
$polls_cat = 951;

//radamo profila skatijumu skaits
$profile_views_limit = 20;

$page_title = 'MTA:SA roleplay serveris';

//auto login visos subdomēnos
if ($_SERVER['SERVER_NAME'] !== 'localhost' && substr($_SERVER['SERVER_NAME'], 0, 4) !== 'dev.') {
	ini_set("session.cookie_domain", ".exs.lv");
	$secure_login = true;
}

//redirect https links
if (!empty($_SERVER['HTTPS'])) {
	redirect("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true);
}
