<?php

$robotstag[] = 'noodp noindex nofollow';

//auto login visos subdomēnos
if ($_SERVER['SERVER_NAME'] !== 'localhost' && substr($_SERVER['SERVER_NAME'], 0, 4) !== 'dev.') {

	//secure cookies
	ini_set('session.cookie_domain', '.exs.lv');
	ini_set('session.cookie_httponly', 1);
	ini_set('session.cookie_secure', 1);
	ini_set('session.use_only_cookies', 1);
	$secure_login = true;
}

