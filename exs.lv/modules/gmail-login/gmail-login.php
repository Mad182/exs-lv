<?php

/**
 * Ielogošanās un profila izveide ar Google / Gmail autorizāciju
 */
$robotstag[] = 'noindex';

$tpl->assignGlobal('rules', $db->get_var("SELECT text FROM pages WHERE id = 57753"));

$redirect_uri = get_protocol($lang) . $_SERVER['HTTP_HOST'] . '/gmail-login';

// If code is not present, initiate OAuth redirect to Google
if (empty($_GET['code'])) {

	if (empty($google_client_id)) {
		set_flash('<strong>Kļūda:</strong> Google autorizācija šobrīd nav konfigurēta!', 'error');
		redirect('/');
		exit;
	}

	$_SESSION['google_auth_state'] = bin2hex(random_bytes(16));

	if (!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])) {
		$_SESSION['redirect_after_login'] = $_SERVER['HTTP_REFERER'];
	}

	$params = [
		'client_id' => $google_client_id,
		'redirect_uri' => $redirect_uri,
		'response_type' => 'code',
		'scope' => 'openid email profile',
		'state' => $_SESSION['google_auth_state'],
		'prompt' => 'select_account'
	];

	$auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
	redirect($auth_url);
	exit;
}

// OAuth Callback handling
if (!empty($_GET['code'])) {

	if (empty($_GET['state']) || empty($_SESSION['google_auth_state']) || !hash_equals($_SESSION['google_auth_state'], $_GET['state'])) {
		set_flash('<strong>Kļūda:</strong> Nederīga drošības atslēga! Mēģini vēlreiz.', 'error');
		redirect('/');
		exit;
	}
	$_SESSION['google_auth_state'] = null;

	// Exchange authorization code for access token
	$ch = curl_init('https://oauth2.googleapis.com/token');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
		'code' => $_GET['code'],
		'client_id' => $google_client_id,
		'client_secret' => $google_client_secret,
		'redirect_uri' => $redirect_uri,
		'grant_type' => 'authorization_code'
	]));
	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
	
	$response = curl_exec($ch);
	curl_close($ch);

	$token_data = json_decode($response, true);

	if (empty($token_data['access_token'])) {
		set_flash('<strong>Kļūda:</strong> Neizdevās saņemt Google autorizācijas tokenu!', 'error');
		redirect('/');
		exit;
	}

	// Fetch user profile info from Google
	$ch = curl_init('https://www.googleapis.com/oauth2/v3/userinfo');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		'Authorization: Bearer ' . $token_data['access_token']
	]);
	$userinfo_response = curl_exec($ch);
	curl_close($ch);

	$guser = json_decode($userinfo_response, true);

	if (empty($guser['sub'])) {
		set_flash('<strong>Kļūda:</strong> Neizdevās saņemt Google lietotāja datus!', 'error');
		redirect('/');
		exit;
	}

	$google_id = sanitize($guser['sub']);
	$google_email = !empty($guser['email']) ? sanitize($guser['email']) : '';

	// Check if user exists by google_id or mail
	$userinfo = $db->get_row("SELECT * FROM `users` WHERE `google_id` = '$google_id'");
	if (!$userinfo && !empty($google_email)) {
		$userinfo = $db->get_row("SELECT * FROM `users` WHERE `mail` = '$google_email'");
	}

	if ($userinfo) {
		// Existing user login
		if (empty($userinfo->google_id)) {
			$db->query("UPDATE `users` SET `google_id` = '$google_id' WHERE `id` = '$userinfo->id'");
		}

		$_SESSION['auth_id'] = $userinfo->id;
		$_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);

		$db->query("UPDATE `users` SET `user_agent` = '" . sanitize($_SERVER['HTTP_USER_AGENT']) . "', `lastseen` = NOW(), `lastip` = '" . $auth->ip . "' WHERE `id` = '$userinfo->id'");
		update_karma($userinfo->id, true);

		$to = '/';
		if (!empty($_SESSION['redirect_after_login'])) {
			$to = $_SESSION['redirect_after_login'];
			$_SESSION['redirect_after_login'] = null;
		}

		redirect($to);
		exit;
	} else {
		// New user registration flow
		$tpl->newBlock('gmail-signup');

		if (isset($_POST['nick'])) {
			$nick = sanitize(trim($_POST['nick']));

			if (strlen($nick) >= 3 && strlen($nick) <= 24) {
				if ($db->get_row("SELECT * FROM `users` WHERE `nick` = '$nick'")) {
					$tpl->newBlock('invalid-nick-taken');
				} else {
					$db->query("INSERT INTO `users` (`id`, `nick`, `mail`, `mail_confirmed`, `date`, `lastip`, `skin`, `google_id`, `source_site`, `user_agent`)
								VALUES (NULL, '$nick', '$google_email', NOW(), NOW(), '" . $auth->ip . "', '3', '$google_id', '$lang', '" . sanitize($_SERVER['HTTP_USER_AGENT']) . "')");
					$newid = $db->insert_id;

					userlog($newid, 'Reģistrējās mājas lapā ar Google kontu. Sveicam exiešu pulkā ;)', '/bildes/users-icon.png');

					// Avatar download if Google picture is available
					if (!empty($guser['picture'])) {
						$tmp_image = 'tmp/' . uniqid() . '.jpg';
						$img_data = @file_get_contents($guser['picture']);
						if ($img_data) {
							file_put_contents($tmp_image, $img_data);
							require_once(LIB_PATH . '/verot/src/class.upload.php');

							$text = time() . '_g_' . $newid;
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
								$foo_sm = new Upload($tmp_image);
								$foo_sm->file_new_name_body = $text;
								$foo_sm->image_resize = true;
								$foo_sm->image_convert = 'jpg';
								$foo_sm->image_x = 45;
								$foo_sm->image_y = 45;
								$foo_sm->allowed = ['image/*'];
								$foo_sm->image_ratio_crop = true;
								$foo_sm->jpeg_quality = 98;
								$foo_sm->file_auto_rename = false;
								$foo_sm->file_overwrite = true;
								$foo_sm->process('dati/bildes/u_small/');

								if (file_exists('dati/bildes/useravatar/' . $text . '.jpg')) {
									$avatar = $text . '.jpg';
									$db->query("UPDATE `users` SET `avatar` = '$avatar', `av_alt` = '1' WHERE `id` = '$newid'");
								}
							}
							@unlink($tmp_image);
						}
					}

					$_SESSION['auth_id'] = $newid;
					$_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);
					update_karma($newid, true);
					redirect('/');
					exit;
				}
			} else {
				$tpl->newBlock('invalid-nick-len');
			}
		}

		// Suggest default username from Google name
		$base_nick = '';
		if (!empty($guser['given_name'])) {
			$base_nick = $guser['given_name'];
		} elseif (!empty($guser['name'])) {
			$parts = explode(' ', $guser['name']);
			$base_nick = $parts[0];
		} else {
			$base_nick = 'GoogleUser';
		}

		$suggested_nick = $base_nick;
		if ($db->get_var("SELECT count(*) FROM `users` WHERE `nick` = '" . sanitize($suggested_nick) . "'")) {
			$suggested_nick = $base_nick . rand(100, 999);
		}

		$tpl->assign([
			'nick' => h($suggested_nick),
			'avatar' => !empty($guser['picture']) ? h($guser['picture']) : '/bildes/avatar.gif',
			'email' => h($google_email)
		]);
	}
}
