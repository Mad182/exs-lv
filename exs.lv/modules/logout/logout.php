<?php

if (isset($_GET['var1']) && $_GET['var1'] == $auth->logout_hash) {

	$auth->logout();
	$urla = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
	redirect($urla);
} else {

	set_flash('Kļūda izlogojoties!', 'error');
	redirect();
}
