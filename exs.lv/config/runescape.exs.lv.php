<?php

$tpl_options = '';
$page_title = 'RuneScape lapele';

//facebook login
$fb_api_id = '382758518536064';
$fb_api_key = 'e91d56558adbab5e25c97d3eb46cf2bb';

//draugiem pase
$dr_api_id = 15005147;
$dr_api_key = 'f38c225b8f65df03c5aaa847b1f052a9';

$robotstag[] = 'noodp';

// aptauju sadaļas id (nav, jo aptaujas ieraksta miniblogos)
$polls_cat = 0;

// bot user id 
$rsbot_id = 33342;

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
 * runescape.exs.lv specific functions
 */
require(CORE_PATH . '/includes/functions.runescape.php');
