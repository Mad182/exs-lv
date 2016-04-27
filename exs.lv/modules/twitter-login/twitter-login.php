<?php

/**
 * Ielogošanās un profila izveide ar twitter autorizāciju
 */
$robotstag[] = 'noindex';

require(LIB_PATH . '/twitteroauth/twitteroauth/twitteroauth.php');

if (!isset($_SESSION['twitter_id']) && !isset($_GET['oauth_token']) && !isset($_POST['submit'])) {

	$connection = new TwitterOAuth($CONSUMER_KEY, $CONSUMER_SECRET);
	$request_token = $connection->getRequestToken($OAUTH_CALLBACK); //get Request Token

	if ($request_token) {
		$token = $request_token['oauth_token'];
		$_SESSION['request_token'] = $token;
		$_SESSION['request_token_secret'] = $request_token['oauth_token_secret'];

		switch ($connection->http_code) {
			case 200:
				$url = $connection->getAuthorizeURL($token, true);
				
				//set url where to redirect back after login
				if(!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']))  {
					$_SESSION['redirect_after_login'] = $_SERVER['HTTP_REFERER'];
				}
				
				//redirect to Twitter .
				redirect($url);
				break;
			default:
				echo "Connection with twitter Failed";
				break;
		}
	} else { //error receiving request token
		echo "Error Receiving Request Token";
	}
}

if (isset($_GET['oauth_token'])) {

	$connection = new TwitterOAuth($CONSUMER_KEY, $CONSUMER_SECRET, $_SESSION['request_token'], $_SESSION['request_token_secret']);
	$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
	if ($access_token) {
		$connection = new TwitterOAuth($CONSUMER_KEY, $CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
		$params = array();
		$params['include_entities'] = 'false';
		$content = $connection->get('account/verify_credentials', $params);

		if ($content && isset($content->screen_name) && isset($content->name)) {
			$_SESSION['name'] = $content->name;
			$_SESSION['image'] = $content->profile_image_url;
			$_SESSION['twitter_id'] = $content->screen_name;
			$_SESSION['oauth_token'] = $access_token['oauth_token'];
			$_SESSION['oauth_token_secret'] = $access_token['oauth_token_secret'];

			//redirect to main page.
			header('Location: /twitter-login');
		} else {
			echo "<h4> Login Error </h4>";
		}
	}
}

if (!empty($_SESSION['twitter_id'])) {

	$userinfo = $db->get_row("SELECT * FROM `users` WHERE `twitter_id` = '" . sanitize($_SESSION['twitter_id']) . "'");
	if (!$userinfo) {

		$tpl->newBlock('twitter-signup');
		if (isset($_POST['existing-nick']) && isset($_POST['existing-password'])) {
			$auth->login($_POST['existing-nick'], $_POST['existing-password']);
			if ($auth->ok) {
				$exuser = $db->get_row("SELECT * FROM `users` WHERE `id` = '$auth->id'");
				if (empty($exuser->twitter_id)) {

					$db->query("UPDATE `users` SET `twitter_id` = '" . sanitize($_SESSION['twitter_id']) . "', `user_agent` = '" . sanitize($_SERVER['HTTP_USER_AGENT']) . "' WHERE `id` = '$auth->id'");
					userlog($auth->id, 'Lieto twitter.com autorizāciju', '/bildes/twitter.png');

					/**
					 * Twitter follow exs_lv
					 */
					if (!empty($_POST['follow'])) {
						$connection = new TwitterOAuth($CONSUMER_KEY, $CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
						$connection->post('friendships/create', array('id' => 104146775));
					}
				}

				//award
				$connection = new TwitterOAuth($CONSUMER_KEY, $CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
				$check_followig = $connection->get('friendships/lookup', array('screen_name' => 'exs_lv'));
				if (is_array($check_followig) && ($check_followig[0]->connections[0] === 'following' || $check_followig[0]->connections[1] === 'following')) {
					twitter_award($auth->id);
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
					$db->query("INSERT INTO users (id,nick,mail,date,lastip,skin,twitter_id,source_site, `user_agent`)
					VALUES (NULL,'" . $nick . "','',NOW(),'" . $auth->ip . "','3','" . sanitize($_SESSION['twitter_id']) . "', '$lang', '" . sanitize($_SERVER['HTTP_USER_AGENT']) . "')");
					$newid = $db->insert_id;

					//log registration
					userlog($newid, 'Reģistrējās mājas lapā. Sveicam exiešu pulkā ;)', '/bildes/users-icon.png');
					userlog($newid, 'Lieto twitter.com autorizāciju', '/bildes/twitter.png');

					$tmp_image = 'tmp/' . uniqid() . '.jpg';

					require(CORE_PATH . '/includes/class.upload.php');

					file_put_contents($tmp_image, file_get_contents($_SESSION['image']));

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

					/**
					 * Twitter follow exs_lv
					 */
					if (!empty($_POST['follow'])) {
						$connection = new TwitterOAuth($CONSUMER_KEY, $CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
						$connection->post('friendships/create', array('id' => 104146775));
					}

					redirect('/twitter-login');
				}
			} else {
				$tpl->newBlock('invalid-nick-len');
			}
		}


		if (strlen($_SESSION['twitter_id']) > 2 && !$db->get_var("SELECT count(*) FROM users WHERE nick = '" . sanitize($_SESSION['twitter_id']) . "'")) {
			$nick = $_SESSION['twitter_id'];
		} elseif (strlen($_SESSION['name']) > 2 && !$db->get_var("SELECT count(*) FROM users WHERE nick = '" . sanitize($_SESSION['name']) . "'")) {
			$nick = $_SESSION['name'];
		} elseif (!$db->get_var("SELECT count(*) FROM users WHERE nick = '" . sanitize($_SESSION['name'] . " (" . $_SESSION['twitter_id'] . ")") . "'")) {
			$nick = $_SESSION['name'] . " (" . $_SESSION['twitter_id'] . ")";
		}

		$tpl->assign(array(
			'nick' => h($nick),
			'avatar' => h($_SESSION['image'])
		));
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

		//award
		$connection = new TwitterOAuth($CONSUMER_KEY, $CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		$check_followig = $connection->get('friendships/lookup', array('screen_name' => 'exs_lv'));

		if (is_array($check_followig) && ($check_followig[0]->connections[0] === 'following' || $check_followig[0]->connections[1] === 'following')) {
			twitter_award($userinfo->id);
		}

		update_karma($userinfo->id, true);

		//redirect back to page where login button was clicked
		$to = '/';
		if(!empty($_SESSION['redirect_after_login'])) {
			$to = $_SESSION['redirect_after_login'];
			$_SESSION['redirect_after_login'] = null;
		}

		redirect($to);

	}
}

