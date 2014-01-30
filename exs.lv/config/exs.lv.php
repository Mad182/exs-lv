<?php

//facebook login
$fb_api_id = '353222841436117';
$fb_api_key = 'f6ac0e495e8b5a09ff2ea463383dc57c';

//draugiem pase
$dr_api_id = 15005147;
$dr_api_key = 'f38c225b8f65df03c5aaa847b1f052a9';

$polls_cat = 2;

//radamo profila skatijumu skaits
$profile_views_limit = 27;

$robotstag[] = 'noodp';

//auto login visos subdomēnos
if ($_SERVER['SERVER_NAME'] !== 'localhost' && substr($_SERVER['SERVER_NAME'], 0, 4) !== 'dev.') {
	ini_set("session.cookie_domain", ".exs.lv");
	$secure_login = true;
}

//redirect https links
if (!empty($_SERVER['HTTPS'])) {
	redirect("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true);
}

/*
 * exs.lv specific functions
 */
require(CORE_PATH . '/includes/functions.exs.php');
