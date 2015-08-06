<?php

$robotstag[] = 'noodp noindex nofollow';

// drošam savienojumam nepieciešamie uzstādījumi, kas pie reizes
// arī autorizēs lietotāju visos subdomēnos
if (!$is_local) {
    ini_set('session.cookie_domain', '.exs.lv');
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.use_only_cookies', 1);
	$secure_login = true;
}
