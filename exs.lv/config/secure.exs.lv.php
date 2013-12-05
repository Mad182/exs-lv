<?php

$robotstag[] = 'noodp noindex nofollow';

//auto login visos subdomēnos
if($_SERVER['SERVER_NAME'] !== 'localhost' && $_SERVER['SERVER_NAME'] !== 'dev.exs.lv') {
	ini_set("session.cookie_domain", ".exs.lv");
	$secure_login = true;
}

