<?php

//mysql master
$hostname = 'localhost';
$username = 'exs';
$password = 'test';
$database = 'exs';

//smtp
$smtp_hostname = 'smtp.gmail.com';
$smtp_port = 465;
$smtp_encryption = 'ssl';
$smtp_account = 'user@gmail.com';
$smtp_password = '***';

//memcached
$mc_host = 'localhost';
$mc_port = 11211;

//formu tokenu salt
$remote_salt = 'BgpgSvz21ku6C2tcEGVLqwWj8fXkeSA9';

//facebook login
$fb_api_id = null;
$fb_api_key = null;

//draugiem pase
$dr_api_id = null;
$dr_api_key = null;

//include folderi
if (!getenv('CORE_PATH')) {
    define('CORE_PATH', '/home/madars/www/exs-lv/exs.lv');
} else {
    define('CORE_PATH', getenv('CORE_PATH'));
}

if (!getenv('LIB_PATH')) {
    define('LIB_PATH', '/home/madars/www/exs-lv/libs');
} else {
    define('LIB_PATH', getenv('LIB_PATH'));
}

//debug konfigurācija
$dev_ips = array(
	'127.0.0.1',
	'87.110.18.3', //m home
	'213.180.98.21', //m work
	//'87.110.104.96',
	'46.109.90.191'	// burvis
);

if(empty($_SERVER['HTTP_CF_RAY'])) {
	$_SERVER['HTTP_X_FORWARDED_FOR'] = $_SERVER['REMOTE_ADDR'];
}

if (in_array($_SERVER['HTTP_X_FORWARDED_FOR'], $dev_ips) && !isset($_GET['_']) && !isset($_POST['newtags']) && substr($_SERVER['REQUEST_URI'], -4) != '.jpg') {
	$start_time = microtime(true);
	$debug = true;
	ini_set('display_errors', 'On');
	error_reporting(E_ALL);
} else {
	error_reporting(0);
	$debug = false;
}

//defaultās vērtības, mainīgo inicializācija
$page_title = 'Spēļu portāls';
$inprofile = false;
$new_msg_string = '';
$pagepath = '';
$new_ap_string = '';
$tpl_options = '';
$cat = 'index';
$skin = 'main';
$idb_count = '';
$add_css = '';
$users_cache = array();
$tinymce_skin_variant = 'o2k7';
$mention_counter = 0;
$hashtag_counter = 0;
$lang = 1;
$locale = 'lv';
$generic_f_icon = 'modules/forums/images/generic.png';
$disable_emotions = 0;
$profile_views_limit = 30;

//multibyte atbalsts
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');

$site_admins = array();
$site_mods = array();

//karma no kuras sakot var labot savus postus
$min_post_edit = 100;

//karma no kuras sakot var labot savus rakstus
$min_page_edit = 0;

//cik ilgi var labot savus rakstus (0 = bezgalīgi)
$page_edit_time = 7200; //2 stundas

//koementāri (level 1) vienā foruma lapā
$comments_per_page = 50;

//sadaļa, kurā parādās aptaujas jautājumu tēmas
$polls_cat = 0;

