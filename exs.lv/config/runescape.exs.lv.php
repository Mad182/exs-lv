<?php

$tpl_options    = '';
$page_title     = 'RuneScape lapele';

//facebook login
$fb_api_id = '382758518536064';
$fb_api_key = 'e91d56558adbab5e25c97d3eb46cf2bb';

//draugiem pase
$dr_api_id = '';
$dr_api_key = '';

$robotstag[] = 'noodp';

//auto login visos subdomēnos
if($_SERVER['SERVER_NAME'] !== 'localhost' && $_SERVER['SERVER_NAME'] !== 'dev.runescape.exs.lv') {
	ini_set("session.cookie_domain", ".exs.lv");
	$secure_login = true;
}

//redirect https links
if(!empty($_SERVER['HTTPS'])) {
    redirect("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], true);
}

