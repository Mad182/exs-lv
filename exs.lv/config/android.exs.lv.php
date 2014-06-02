<?php

//facebook login
$fb_api_id = '';
$fb_api_key = '';

//draugiem pase
$dr_api_id = 0;
$dr_api_key = '';

$polls_cat = 0;

//radamo profila skatijumu skaits
$profile_views_limit = 0;

$android_lang  = 1; // nākotnē atbalstīs dažādus apakšprojektus

// (testējot lokāli no telefona)
$android_links = array('192.168.1.116');

//auto login visos subdomēnos
if ($_SERVER['SERVER_NAME'] !== 'localhost' && substr($_SERVER['SERVER_NAME'], 0, 4) !== 'dev.' && !in_array($_SERVER['SERVER_NAME'], $android_links)) {
	ini_set("session.cookie_domain", ".exs.lv");
	$secure_login = true;
}

/*
 * exs.lv specific functions
 */
require(CORE_PATH . '/includes/functions.exs.php');
require(CORE_PATH . '/includes/functions.android.php');
