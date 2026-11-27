<?php

if (isset($_GET['var1']) && !empty($auth->logout_hash) && hash_equals((string) $auth->logout_hash, (string) $_GET['var1'])) {

	$auth->logout();
	$urla = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
	redirect($urla);
} else {

	set_flash('Kļūda izlogojoties!', 'error');
	redirect();
}
