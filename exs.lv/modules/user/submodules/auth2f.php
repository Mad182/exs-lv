<?php

/**
 * 2 faktoru auth
 */
$robotstag[] = 'noindex';

deny_proxies();

if($auth->auth_2fa) {
	$tpl->newBlock('user-profile-2fa-enabled');
} else {
	$tpl->newBlock('user-profile-2fa');
}

$tpl->assign('xsrf', make_token('2fa'));

$ga = new PHPGangsta_GoogleAuthenticator();
if(empty($auth->auth_secret)) {
	$secret = $ga->createSecret();
	$db->query("UPDATE `users` SET `auth_secret` = '".sanitize($secret)."' WHERE `id` = $auth->id LIMIT 1");
	$auth->reset();
} else {
	$secret = $auth->auth_secret;
}

$qrCodeUrl = $ga->getQRCodeGoogleUrl('exs.lv', $secret);
$tpl->assign('qrCodeUrl', $qrCodeUrl);

//$oneCode = $ga->getCode($secret);
//echo "Checking Code '$oneCode' and Secret '$secret':\n";

$tpl->assign('xsrf', make_token('2fa'));

//write changes
if (isset($_POST['submit'])) {

	if (check_token('2fa', $_POST['xsrf_token'])) {
		
		$checkResult = $ga->verifyCode($secret, $_POST['code'], 4);    // 2 = 2*30sec clock tolerance
		if ($checkResult) {
			$db->query("UPDATE `users` SET `auth_2fa` = '1' WHERE `id` = $auth->id LIMIT 1");
			set_flash('Divu Faktoru Autentifikācija ir ieslēgta!', 'success');
			$auth->reset();
			$_SESSION['2fa'] = 1;
		} else {
			set_flash('<strong>Kļūda:</strong> nepareizs kods!', 'error');
		}


	} else {
		set_flash('<strong>Kļūda:</strong> neizdevās saglabāt iestatījumus!', 'error');
	}

	redirect('/user/auth2f');
}

$page_title = 'Tava parole';

