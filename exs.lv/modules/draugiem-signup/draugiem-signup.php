<?php

/**
 * Ielogoties/reģistrēties ar draugiem.lv pasi
 */
if ($auth->ok) {
	redirect();
}

require_once(CORE_PATH . '/includes/DraugiemApi.php');

$draugiem = new DraugiemApi($dr_api_id, $dr_api_key);

$session = $draugiem->getSession(); //Try to authenticate user

if ($session && !empty($_GET['dr_auth_code'])) {//New session, check if we are not redirected from popup
	if (!empty($_GET['dr_popup'])) {//Redirected from popup, refresh parent window and close the popup with Javascript
		?>
		<script type="text/javascript">
			window.opener.location.href = 'http://<?php echo htmlspecialchars($_SERVER['SERVER_NAME']); ?>/draugiem-signup';
			window.opener.focus();
			if (window.opener != window) {
				window.close();
			}
		</script>
		<?php
	} else {//No popup, simply reload current window
		redirect('/draugiem-signup');
	}
	exit;
}

if ($session) {//Authentication successful
	$user = $draugiem->getUserData(); //Get user info

	if (intval($user['uid']) != 0) {
		$userinfo = $db->get_row("SELECT * FROM `users` WHERE `draugiem_id` = '" . intval($user['uid']) . "'");
		if (!$userinfo) {

			$tpl->newBlock('draugiem-signup');
			if (isset($_POST['existing-nick']) && isset($_POST['existing-password'])) {
				$auth->login($_POST['existing-nick'], $_POST['existing-password']);
				if ($auth->ok) {
					$exuser = $db->get_row("SELECT * FROM `users` WHERE `id` = '$auth->id'");
					if ($exuser->draugiem_id == 0) {

						$gender = 0;
						if ($user['sex'] == 'F') {
							$gender = 1;
						}

						$db->query("UPDATE `users` SET `draugiem_id` = '" . intval($user['uid']) . "', `gender` = '$gender', `user_agent` = '" . sanitize($_SERVER['HTTP_USER_AGENT']) . "' WHERE `id` = '$auth->id'");
						userlog($auth->id, 'Lieto draugiem.lv pasi', '/bildes/draugiem_badge_small.png');

						//draugiem.lv friends
						if ($users = $draugiem->getUserFriends(1, 100)) {
							foreach ($users as $friend) {
								$existing = $db->get_row("SELECT * FROM users WHERE draugiem_id = '" . $friend['uid'] . "'");
								if ($existing) {
									$c1 = $db->get_var("SELECT count(*) FROM friends WHERE friend1 = '$auth->id' AND friend2 = '$existing->id'");
									$c2 = $db->get_var("SELECT count(*) FROM friends WHERE friend2 = '$auth->id' AND friend1 = '$existing->id'");
									if (!$c1 && !$c2) {

										$db->query("INSERT INTO friends (`friend1`,`friend2`,`date`,`date_confirmed`,`confirmed`)
											VALUES ('$auth->id', '$existing->id', NOW(), NOW(), 1)");
										update_karma($existing->id, true);
										notify($existing->id, 6);
										notify($auth->id, 6);
									}
								}
							}
						}
					}
					update_karma($auth->id, true);
					redirect();
				} else {
					$tpl->newBlock('invalid');
				}
			}

			if (isset($_POST['nick'])) {
				//check nick
				if (strlen(trim($_POST['nick'])) > 2 && strlen(trim($_POST['nick'])) <= 24) {
					$nick = sanitize(trim($_POST['nick']));
					if ($db->get_row("SELECT * FROM users WHERE nick = ('" . $nick . "')")) {
						$tpl->newBlock('invalid-nick-taken');
					} else {

						//process register
						//additional info

						$gender = 0;
						if ($user['sex'] == 'F') {
							$gender = 1;
						}

						//write down
						$db->query("INSERT INTO users (id,nick,mail,date,lastip,skin,draugiem_id,source_site,gender, `user_agent`)
							VALUES (NULL,'" . $nick . "','',NOW(),'" . $auth->ip . "','3','" . intval($user['uid']) . "', '$lang', '$gender', '" . sanitize($_SERVER['HTTP_USER_AGENT']) . "')");
						$newid = $db->insert_id;

						//log registration
						userlog($newid, 'Reģistrējās mājas lapā. Sveicam exiešu pulkā ;)', '/bildes/users-icon.png');
						userlog($newid, 'Lieto draugiem.lv pasi', '/bildes/draugiem_badge_small.png');
						$tmp_image = 'tmp/' . uniqid() . '.jpg';


						//draugiem.lv friends
						if ($users = $draugiem->getUserFriends(1, 100)) {
							foreach ($users as $friend) {
								$existing = $db->get_row("SELECT * FROM users WHERE draugiem_id = '" . $friend['uid'] . "'");
								if ($existing) {
									$db->query("INSERT INTO `friends` (`friend1`,`friend2`,`date`,`date_confirmed`,`confirmed`)
										VALUES ('$newid', '$existing->id', NOW(), NOW(), 1)");

									notify($existing->id, 6);
									notify($newid, 6);
								}
							}
						}

						require(CORE_PATH . '/includes/class.upload.php');

						file_put_contents($tmp_image, curl_get($draugiem->imageForSize($user['img'], 'large')));

						//new avatar image
						$text = time() . '_dr_' . $newid;
						$foo = new Upload($tmp_image);
						$foo->file_new_name_body = $text;
						$foo->image_resize = true;
						$foo->image_convert = 'jpg';
						$foo->image_x = 90;
						$foo->image_y = 90;
						$foo->allowed = array('image/*');
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
							$foo->allowed = array('image/*');
							$foo->image_ratio_crop = true;
							$foo->jpeg_quality = 98;
							$foo->file_auto_rename = false;
							$foo->file_overwrite = true;
							$foo->process('dati/bildes/u_small/');

							$foo = new Upload($tmp_image);
							$foo->file_new_name_body = $text;
							$foo->image_resize = true;
							$foo->image_convert = 'jpg';
							$foo->image_x = 170;
							$foo->image_y = 220;
							$foo->allowed = array('image/*');
							$foo->image_ratio_crop = false;
							$foo->image_ratio_no_zoom_in = true;
							$foo->jpeg_quality = 98;
							$foo->file_auto_rename = false;
							$foo->file_overwrite = true;
							$foo->process('dati/bildes/u_large/');

							if (file_exists('dati/bildes/useravatar/' . $text . '.jpg')) {
								$avatar = $text . '.jpg';
								$db->query("UPDATE users SET avatar = ('$avatar'), av_alt = '1' WHERE id = ('" . $newid . "')");
								unlink($tmp_image);
							}
							$foo->clean();
						}
						redirect('/draugiem-signup');
					}
				} else {
					$tpl->newBlock('invalid-nick-len');
				}
			}


			if (strlen($user['nick']) > 2 && !$db->get_var("SELECT count(*) FROM `users` WHERE `nick` = '" . sanitize($user['nick']) . "'")) {
				$nick = $user['nick'];
			} elseif (strlen($user['name']) > 2 && !$db->get_var("SELECT count(*) FROM `users` WHERE `nick` = '" . sanitize($user['name']) . "'")) {
				$nick = $user['name'];
			} elseif (!$db->get_var("SELECT count(*) FROM `users` WHERE `nick` = '" . sanitize($user['name'] . " " . $user['surname']) . "'")) {
				$nick = $user['name'] . ' ' . $user['surname'];
			}

			$tpl->assign(array(
				'nick' => htmlspecialchars($nick),
				'avatar' => $draugiem->imageForSize($user['img'], 'small')
			));

			if ($users = $draugiem->getUserFriends(1, 100)) {

				$tpl->newBlock('dr-friends');
				foreach ($users as $friend) {
					$tpl->newBlock('dr-friends-node');
					$tpl->assign($friend);
				}
			}
		} else {
			$_SESSION['auth_id'] = $userinfo->id;
			$_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);
			update_karma($userinfo->id, true);
			redirect();
		}
	}
} else { //User not logged in, show login button
	$tpl->newBlock('draugiem-login');
	$redirect = 'http://' . $_SERVER['SERVER_NAME'] . '/draugiem-signup/'; //Where to redirect after authorization
	$tpl->assign('button', $draugiem->getLoginButton($redirect)); //Show the button
}
