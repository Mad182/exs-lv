<?php

$robotstag[] = 'noodp noindex nofollow';

//auto login visos subdomēnos
if($_SERVER['SERVER_NAME'] !== 'localhost' && substr($_SERVER['SERVER_NAME'], 0, 4) !== 'dev.') {
	ini_set("session.cookie_domain", ".exs.lv");
	$secure_login = true;
}
