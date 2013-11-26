<?php

$lang = 3;

$tpl_options = 'no-left';

//facebok login
$fb_api_id = '352399534849590';
$fb_api_key = 'efa5f43f11a7c37a924e7164707579e0';

//draugiem pase
$dr_api_id = 15010793;
$dr_api_key = 'c77481ff03e49feb76cddf54c6ef4929';

//karma no kuras sakot var labot savus postus
$min_post_edit = 10;

//karma no kuras sakot var labot savus rakstus
$min_page_edit = 0;

//cik ilgi var labot savus rakstus (0 = bezgalīgi)
$page_edit_time = 86400; //1 diena

$page_title = 'Web programmēšanas forums';

//radamo profila skatijumu skaits
$profile_views_limit = 20;

//izslēdz smaidiņus foruma tēmās pēc noklusējuma
$disable_emotions = 1;

//aptauju sadaļa
$polls_cat = 803;

//redirect https links
if(!empty($_SERVER['HTTPS'])) {
    redirect("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], true);
}

