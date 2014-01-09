<?php

if (isset($_GET['var1']) && $_GET['var1'] == $auth->logout_hash) {

	$auth->logout();
	if ($_SERVER['HTTP_REFERER'] == "") {
		$urla = "/";
	} else {
		$urla = $_SERVER['HTTP_REFERER'];
	}
	redirect($urla);
} else {

	set_flash('Kļūda izlogojoties!', 'error');
	redirect();
}
