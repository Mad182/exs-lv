<?php

if($auth->ok && $auth->auth_2fa && empty($_SESSION['2fa'])) {

	$check_existing = $db->get_results("SELECT `cookie`, `token` FROM `tfa_whitelist` WHERE `user_id` = $auth->id");
	if(!empty($check_existing)) {
		foreach($check_existing as $device) {
			if(!empty($_COOKIE[$device->cookie]) && $_COOKIE[$device->cookie] === $device->token) {
				$_SESSION['2fa'] = 1;
				redirect();
			}
		}
	}

	$tpl->newBlock('auth-2fa');
	$tpl->assign('xsrf', make_token('2falogin'));

	$ga = new PHPGangsta_GoogleAuthenticator();
	$secret = $auth->auth_secret;

	if(isset($_POST['code']) && check_token('2falogin', $_POST['xsrf_token'])) {

		$checkResult = $ga->verifyCode($secret, $_POST['code'], 4);
		if ($checkResult) {
			$_SESSION['2fa'] = 1;

			if(!empty($_POST['remember'])) {

				$cookie = md5(uniqid());
				$token = md5(uniqid() . $auth->xsrf);

				$db->query("INSERT INTO `tfa_whitelist` (`user_id`, `ip`, `cookie`, `token`, `created`, `modified`) VALUES ('$auth->id', '$auth->ip', '$cookie', '$token', NOW(), NOW())");
				if($lang == 3) {
					setcookie($cookie, $token, time()+2592000, "/", ".coding.lv", 1, 1);
				} else {
					setcookie($cookie, $token, time()+2592000, "/", ".exs.lv", 1, 1);
				}

			}

			redirect();
		} else {
			set_flash('<strong>Kļūda:</strong> nepareizs kods!', 'error');
			redirect('/2fa');
		}
	}

} else {
	redirect();
}

