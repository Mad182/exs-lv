<?php

/**
 * Ielogošanās un profila izveide ar facebook autorizāciju
 */
$robotstag[] = 'noindex';

$tpl->assignGlobal('rules', $db->get_var("SELECT text FROM pages WHERE id = 57753"));

$facebook = new \Facebook\Facebook([
	'app_id' => $fb_api_id,
	'app_secret' => $fb_api_key,
	'default_graph_version' => 'v2.8'
]);

$helper = $facebook->getRedirectLoginHelper();
$permissions = [];

$me = null;

try {
	$accessToken = $helper->getAccessToken();

	$response = $facebook->get('/me', $accessToken);
	$me = $response->getGraphUser();

} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
 // echo 'Graph returned an error: ' . $e->getMessage();
 // exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  //echo 'Facebook SDK returned an error: ' . $e->getMessage();
 // exit;
}


if (!empty($me)) {

	if (!empty($me['id'])) {
		$userinfo = $db->get_row("SELECT * FROM `users` WHERE `facebook_id` = '" . sanitize($me['id']) . "'");
		if (!$userinfo) {

			$tpl->newBlock('fb-signup');
			if (isset($_POST['existing-nick']) && isset($_POST['existing-password'])) {
				$auth->login($_POST['existing-nick'], $_POST['existing-password']);
				if ($auth->ok) {
					$exuser = $db->get_row("SELECT * FROM `users` WHERE `id` = '$auth->id'");
					if (empty($exuser->facebook_id)) {

						$db->query("UPDATE `users` SET `facebook_id` = '" . sanitize($me['id']) . "', `user_agent` = '" . sanitize($_SERVER['HTTP_USER_AGENT']) . "' WHERE `id` = '$auth->id'");
						userlog($auth->id, 'Lieto facebook.com', '/bildes/facebook.png');

					}

					update_karma($auth->id);
					redirect();
				} else {
					$tpl->newBlock('invalid');
				}
			}

			if (isset($_POST['nick'])) {
				//check nick
				if (strlen(trim($_POST['nick'])) > 2 && strlen(trim($_POST['nick'])) <= 24) {
					$nick = sanitize(trim($_POST['nick']));
					if ($db->get_row("SELECT * FROM `users` WHERE nick = '" . $nick . "'")) {
						$tpl->newBlock('invalid-nick-taken');
					} else {

						//process register

						//write down
						$db->query("INSERT INTO users (id,nick,mail,date,lastip,skin,facebook_id,source_site, `user_agent`)
						VALUES (NULL,'" . $nick . "','',NOW(),'" . $auth->ip . "','3','" . sanitize($me['id']) . "', '$lang', '" . sanitize($_SERVER['HTTP_USER_AGENT']) . "')");
						$newid = $db->insert_id;

						//log registration
						userlog($newid, 'Reģistrējās mājas lapā. Sveicam exiešu pulkā ;)', '/bildes/users-icon.png');
						userlog($newid, 'Lieto facebook.com', '/bildes/facebook.png');

						$tmp_image = 'tmp/' . uniqid() . '.jpg';

						require_once(LIB_PATH . '/verot/src/class.upload.php');

						file_put_contents($tmp_image, file_get_contents('https://graph.facebook.com/' . $me['id'] . '/picture?type=large'));

						//new avatar image
						$text = time() . '_fb_' . $newid;
						$foo = new Upload($tmp_image);
						$foo->file_new_name_body = $text;
						$foo->image_resize = true;
						$foo->image_convert = 'jpg';
						$foo->image_x = 90;
						$foo->image_y = 90;
						$foo->allowed = ['image/*'];
						$foo->image_ratio_crop = true;
						$foo->jpeg_quality = 98;
						$foo->file_auto_rename = false;
						$foo->file_overwrite = true;
						$foo->process('dati/bildes/useravatar/');
						if ($foo->processed) {

							$foo = new Upload($tmp_image);
							$foo->file_new_name_body = $text;
							$foo->image_resize = true;
							$foo->image_convert = 'jpg';
							$foo->image_x = 45;
							$foo->image_y = 45;
							$foo->allowed = ['image/*'];
							$foo->image_ratio_crop = true;
							$foo->jpeg_quality = 98;
							$foo->file_auto_rename = false;
							$foo->file_overwrite = true;
							$foo->process('dati/bildes/u_small/');

							$foo = new Upload($tmp_image);
							$foo->file_new_name_body = $text;
							$foo->image_resize = true;
							$foo->image_convert = 'jpg';
							$foo->image_x = 200;
							$foo->image_y = 260;
							$foo->allowed = ['image/*'];
							$foo->image_ratio_crop = false;
							$foo->image_ratio_no_zoom_in = true;
							$foo->jpeg_quality = 98;
							$foo->file_auto_rename = false;
							$foo->file_overwrite = true;
							$foo->process('dati/bildes/u_large/');

							if (file_exists('dati/bildes/useravatar/' . $text . '.jpg')) {
								$avatar = $text . '.jpg';
								$db->query("UPDATE `users` SET `avatar` = '$avatar', `av_alt` = '1' WHERE `id` = '" . $newid . "'");
								unlink($tmp_image);
							}
							$foo->clean();
						}
						redirect('/fb-login');
					}
				} else {
					$tpl->newBlock('invalid-nick-len');
				}
			}

			//create unique username
			if (!empty($me['username']) && strlen($me['username']) > 2 && !$db->get_var("SELECT count(*) FROM users WHERE nick = '" . sanitize($me['username']) . "'")) {
				$nick = $me['username'];
	
			} elseif (!empty($me['first_name']) && !$db->get_var("SELECT count(*) FROM users WHERE nick = '" . sanitize($me['first_name'].substr($me['last_name'], 0, 1)) . "'")) {
				$nick = $me['first_name'].substr($me['last_name'], 0, 1);
				
			} elseif (strlen($me['first_name']) > 2 && !$db->get_var("SELECT count(*) FROM users WHERE nick = '" . sanitize($me['first_name']) . "'")) {
				$nick = $me['first_name'];

			} elseif (!$db->get_var("SELECT count(*) FROM users WHERE nick = '" . sanitize($me['name']) . "'")) {
				$nick = $me['name'];

			} else {
				$nick = $me['first_name'] . rand(100,9999);

			}

			$tpl->assign([
				'nick' => h($nick),
				'avatar' => 'https://graph.facebook.com/' . $me['id'] . '/picture?type=large'
			]);
		} else {

			//perform login
			$_SESSION['auth_id'] = $userinfo->id;
			$_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);

			//update lastseen datetime and user_agent field
			$db->query("UPDATE `users` SET "
					. "`user_agent` = '" . sanitize($_SERVER['HTTP_USER_AGENT']) . "', "
					. "`lastseen` = NOW(), "
					. "`lastip` = '" . $auth->ip . "' "
					. "WHERE `id` = '$userinfo->id'");

			update_karma($userinfo->id, true);

			$to = '/';
			if(!empty($_SESSION['redirect_after_login'])) {
				$to = $_SESSION['redirect_after_login'];
				$_SESSION['redirect_after_login'] = null;
			}

			redirect($to);
		}
	}
} else {

	$protocol = 'https://';
	if (empty($config_domains[$lang]['ssl'])) {
		$protocol = 'http://';
	}

	if(!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']))  {
		$_SESSION['redirect_after_login'] = $_SERVER['HTTP_REFERER'];
	}

	redirect($helper->getLoginUrl($protocol . $_SERVER['HTTP_HOST'] . '/fb-login', $permissions));

}

