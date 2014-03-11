<?php

/**
 * Ielogošanās un profila izveide ar facebook autorizāciju
 */
//piešķir medaļu
function fb_award($id) {
	global $db, $m;
	$existing_awards = get_awards_list($id);
	if (!in_array('facebook-like', $existing_awards)) {
		$title = sanitize('Facebook.com <a href="https://www.facebook.com/exs.lv">like</a>');
		$db->query("INSERT INTO autoawards (user_id,award,title,created) VALUES ('$id','facebook-like','$title',NOW())");
		$db->update('autoawards', $db->insert_id, array('importance' => $db->insert_id));
		userlog($id, 'Ieguva medaļu &quot;' . stripslashes($title) . '&quot;', '/dati/bildes/awards/facebook-like.png');
		notify($id, 7);
		$m->delete('aw_' . $id);
	}
}

require(LIB_PATH . '/facebook-php-sdk/src/base_facebook.php');
require(LIB_PATH . '/facebook-php-sdk/src/facebook.php');

$facebook = new Facebook(array(
	'appId' => $fb_api_id,
	'secret' => $fb_api_key
		));

$user = $facebook->getUser();
$fb_like = false;

if ($user) {
	try {
		// proceed knowing you have a logged in user who's authenticated
		$me = $facebook->api('/me');

		// noskaidro vai lietotajs ir uzspiedis "like"
		$likes = $facebook->api("/me/likes/160566810630384");
		if (!empty($likes['data'])) {
			$fb_like = true;
		}
	} catch (FacebookApiException $e) {
		error_log($e);
		$user = null;
	}
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

						$gender = 0;
						if ($me['gender'] == 'female') {
							$gender = 1;
						}

						$db->query("UPDATE `users` SET `facebook_id` = '" . sanitize($me['id']) . "', `gender` = '$gender', `user_agent` = '" . sanitize($_SERVER['HTTP_USER_AGENT']) . "' WHERE `id` = '$auth->id'");
						userlog($auth->id, 'Lieto facebook.com', '/bildes/facebook.png');

						//draugiem.lv friends
						if ($friends = $facebook->api('/me/friends')) {
							foreach ($friends['data'] as $friend) {
								$existing = $db->get_row("SELECT * FROM `users` WHERE `facebook_id` = '" . $friend['id'] . "'");
								if ($existing && $friend['id'] > 0) {
									$c1 = $db->get_var("SELECT count(*) FROM friends WHERE friend1 = '$auth->id' AND friend2 = '$existing->id'");
									$c2 = $db->get_var("SELECT count(*) FROM friends WHERE friend2 = '$auth->id' AND friend1 = '$existing->id'");
									if (!$c1 && !$c2) {

										$db->query("INSERT INTO friends (`friend1`,`friend2`,`date`,`date_confirmed`,`confirmed`)
											VALUES ('$auth->id', '$existing->id', NOW(), NOW(), 1)");
										update_karma($existing->id, true);
									}
								}
							}
						}
					}
					if ($fb_like) {
						fb_award($auth->id);
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
						//additional info
						$gender = 0;
						if ($me['gender'] == 'female') {
							$gender = 1;
						}

						//write down
						$db->query("INSERT INTO users (id,nick,mail,date,lastip,skin,facebook_id,source_site,gender, `user_agent`)
							VALUES (NULL,'" . $nick . "','',NOW(),'" . $auth->ip . "','3','" . sanitize($me['id']) . "', '$lang', '$gender', '" . sanitize($_SERVER['HTTP_USER_AGENT']) . "')");
						$newid = $db->insert_id;

						//log registration
						userlog($newid, 'Reģistrējās mājas lapā. Sveicam exiešu pulkā ;)', '/bildes/users-icon.png');
						userlog($newid, 'Lieto facebook.com', '/bildes/facebook.png');

						//facebook friends
						if ($friends = $facebook->api('/me/friends')) {
							foreach ($friends['data'] as $friend) {
								$existing = $db->get_row("SELECT * FROM `users` WHERE `facebook_id` = '" . $friend['id'] . "'");
								if ($existing && $friend['id'] > 0) {
									$db->query("INSERT INTO friends (`friend1`,`friend2`,`date`,`date_confirmed`,`confirmed`)
										VALUES ('$auth->id', '$existing->id', NOW(), NOW(), 1)");
									update_karma($existing->id, true);
								}
							}
						}


						$tmp_image = 'tmp/' . uniqid() . '.jpg';

						require(CORE_PATH . '/includes/class.upload.php');

						file_put_contents($tmp_image, file_get_contents('https://graph.facebook.com/' . $me['id'] . '/picture?type=large'));

						//new avatar image
						$text = time() . '_fb_' . $newid;
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


			if (strlen($me['username']) > 2 && !$db->get_var("SELECT count(*) FROM users WHERE nick = '" . sanitize($me['username']) . "'")) {
				$nick = $me['username'];
			} elseif (strlen($me['first_name']) > 2 && !$db->get_var("SELECT count(*) FROM users WHERE nick = '" . sanitize($me['first_name']) . "'")) {
				$nick = $me['first_name'];
			} elseif (!$db->get_var("SELECT count(*) FROM users WHERE nick = '" . sanitize($me['name']) . "'")) {
				$nick = $me['name'];
			}

			$tpl->assign(array(
				'nick' => htmlspecialchars($nick),
				'avatar' => 'https://graph.facebook.com/' . $me['id'] . '/picture?type=large'
			));
		} else {

			if ($fb_like) {
				fb_award($userinfo->id);
			}

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
			redirect();
		}
	}
} else {
	redirect($facebook->getLoginUrl(array('redirect_uri' => 'http://' . $_SERVER['SERVER_NAME'] . '/fb-login/', 'scope' => 'user_likes')));
	/* $loginUrl = $facebook->getLoginUrl();
	  $tpl->newBlock('fb-login');
	  $tpl->assign('link', $loginUrl); //Show the button */
}
