<?php

if($auth->ok && $auth->auth_2fa && empty($_SESSION['2fa'])) {
	$tpl->newBlock('auth-2fa');
	$tpl->assign('xsrf', make_token('2falogin'));

	$ga = new PHPGangsta_GoogleAuthenticator();
	$secret = $auth->auth_secret;

	if(isset($_POST['code']) && check_token('2falogin', $_POST['xsrf_token'])) {

		$checkResult = $ga->verifyCode($secret, $_POST['code'], 4);
		if ($checkResult) {
			$_SESSION['2fa'] = 1;
			redirect();
		} else {
			set_flash('<strong>Kļūda:</strong> nepareizs kods!', 'error');
			redirect('/2fa');
		}
	}

} else {
	redirect();
}

