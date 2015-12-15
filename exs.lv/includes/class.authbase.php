<?php

/**
 * Lietotāja autorizācija un globāls aktīvā lietotāja objekts ($auth)
 *
 * paroles tiek glabātas izmantojot bcrypt
 */
require(LIB_PATH . '/bcrypt/lib/password.php');

class AuthBase {

	var $error = 0;

	/**
	 * Inicializē lietotāja objektu
	 */
	function __construct() {
		global $remote_salt, $lang;

		$this->id = 0;
		$this->avatar = 'none.png';
		$this->error = 0;
		$this->level = 0;
		$this->persona = '';
		$this->karma = 0;
		$this->skin = 3;
		$this->vote_today = 0;
		$this->showsig = 1;
		$this->nick = "Viesis";
		$this->flood = 8;
		$this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		$this->ok = false;

		// lai vēlāk iekš `visits` (un varbūt kur citur) varētu fiksēt tos,
		// kas saturu ielādē caur Android appu
		$this->via_android = (($lang === 2) ? 1 : 0);

		if (!empty($_SESSION['xsrf'])) {
			$this->xsrf = $_SESSION['xsrf'];
		} else {
			$this->xsrf = md5($this->ip . $remote_salt . microtime(true));
			$_SESSION['xsrf'] = $this->xsrf;
		}
		$this->check_session();
		$this->update_counter();
		$this->logout_hash = substr(md5($this->ip . 'NoKidding' . $this->id), 0, 6);

		// vai lapa ielādēta mobilā versijā, ar to saprotot visus
		// "m." apakšprojektus, nevis Android vai citas OS lietotni
		$this->mobile = 0;

		return $this->ok;
	}

	function update_visits() {
		global $db;
		$lang = get_lang();
		$exists = $db->get_var("SELECT `id` FROM `visits` WHERE `user_id` = $this->id AND `ip` = '$this->ip' AND `site_id` = $lang");
		if ($exists) {
			$db->query("UPDATE `visits` SET `lastseen` = NOW() WHERE `id` = $exists");
		} else {
			$db->query("INSERT INTO `visits` (`user_id`, `site_id`, `ip`, `lastseen`) VALUES ($this->id, $lang, '$this->ip', NOW())");
		}
	}

	function check_session() {
		global $db, $site_access, $lang;

		if (empty($_SESSION['auth_id'])) {
			return false;
		}

		$userinfo = get_user($_SESSION['auth_id']);

		if ($userinfo->deleted) {
			return $this->logout();
		}

		foreach ($userinfo as $key => $val) {
			$this->$key = $val;
		}

		$this->interests = $db->get_col("SELECT `interest_id` FROM `user_interests` WHERE `user_id` = '$this->id'");

		if (in_array($this->id, $site_access[1])) {
			$this->level = 1;
		}

		if (in_array($this->id, $site_access[2])) {
			$this->level = 2;
		}

		if ($this->level == 1 || $this->level == 2) {
			$this->flood = 3;
		}

		$this->ok = true;

		if (empty($_SESSION['lastseen']) || $_SESSION['lastseen'] < time() - 480) {
			$db->query("UPDATE `users` SET `lastseen` = NOW(), `mobile` = 0, `android` = ".$this->via_android.", `seen_today` = 1 WHERE `id` = '$this->id'");
			$_SESSION['lastseen'] = time();
		}

		if (empty($_SESSION['updvisits']) || $_SESSION['updvisits'] < time() - 30) {
			$this->update_visits();
			$_SESSION['updvisits'] = time();
		}

		// android.exs.lv redirekti neder
		if ($this->via_android === 0 && $_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])) {
			$this->logout();
			redirect();
		}

		// android.exs.lv pats prot apstrādāt bloķētos profilus
		if ($this->via_android === 0 && !isset($_GET['_']) &&
				$ban = $db->get_var("SELECT `id` FROM `banned` WHERE `active` = 1 AND (`user_id` = '$this->id' OR `ip` = '$this->ip') AND (`lang` = 0 OR `lang` = '$lang') LIMIT 1")) {
			$this->logout();
			set_flash('Pieeja lapai ir liegta!', 'error');
			redirect('http://exs.lv/?c=125&bid=' . $ban);
		}

		return true;
	}

	function reset() {
		if (!empty($_SESSION['auth_id'])) {
			$userinfo = get_user($_SESSION['auth_id'], true);

			session_regenerate_id(true);

			foreach ($userinfo as $key => $val) {
				$this->$key = $val;
			}
			$this->ok = true;
			$_SESSION['auth_id'] = $userinfo->id;
			return true;
		} else {
			return false;
		}
	}

	function login($username, $password, $xsrf = null) {
		global $db, $site_access, $lang;

		if (!is_null($xsrf) && $xsrf != $this->xsrf) {
			$this->log('Neizdevies login mēģinājums (CSRF check failed, user: ' . h($username) . ', ref: ' . h($_SERVER['HTTP_REFERER']) . ')');
			set_flash('Ielogoties neizdevās! Mēģini vēlreiz!', 'error');
			sleep(1);
			$this->error = 2;
			return false;
		}

		session_regenerate_id(true);

		$login = sanitize($username);

		$tmp = $db->get_row("SELECT `id`, `password`, `pwd` FROM `users` WHERE (`nick` = '" . $login . "' OR `mail` = '" . $login . "') AND `deleted` = 0 ORDER BY `karma` DESC LIMIT 1");

		$found = false;
		if (!empty($tmp)) {

			//log in using old SHA password
			if (empty($tmp->password) && !empty($tmp->pwd)) {

				if ($tmp->pwd === pwd($password)) {
					$found = $tmp->id;

					//create new bcrypt hash and delete old one
					$hash = password_hash($password, PASSWORD_BCRYPT, array("cost" => 14));
					$db->query("UPDATE `users` SET `password` = '$hash', `pwd` = '' WHERE `id` = '$tmp->id' LIMIT 1");
				}

				//using bcrypt
			} elseif (!empty($tmp->password)) {

				if (password_verify($password, $tmp->password)) {
					$found = $tmp->id;
				}
			}
		}

		if ($found) {
			$userinfo = get_user($found, true);

			if($this->is_tor_exit()) {
				$this->log('Neizdevies login mēģinājums (IS_TOR_EXIT = true)', 'users', $userinfo->id);
				$this->logout();
				return false;
			}

			foreach ($userinfo as $key => $val) {
				$this->$key = $val;
			}

			$this->interests = $db->get_col("SELECT `interest_id` FROM `user_interests` WHERE `user_id` = '$this->id'");

			if (in_array($this->id, $site_access[1])) {
				$this->level = 1;
			}

			if (in_array($this->id, $site_access[2])) {
				$this->level = 2;
			}

			$this->ok = true;
			$_SESSION['auth_id'] = $userinfo->id;
			$_SESSION['lastseen'] = time();
			$_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);
			$this->error = 0;

			// android.exs.lv pats prot apstrādāt bloķētos profilus un
			// redirekts kā tāds tam vispār neder
			if ($this->via_android === 0 && $ban = $db->get_var("SELECT `id` FROM `banned` WHERE `active` = 1 AND (`user_id` = '$this->id' OR `ip` = '$this->ip') AND (`lang` = 0 OR `lang` = '$lang') LIMIT 1")) {
				$this->logout();
				$this->error = 3;
				set_flash('Pieeja lapai ir liegta!', 'error');
				redirect('http://exs.lv/?c=125&bid=' . $ban);
			}

			$db->query("UPDATE `users` SET `lastseen` = NOW(), `lastip` = '" . $this->ip . "', `user_agent` = '" . sanitize($_SERVER['HTTP_USER_AGENT']) . "', `mobile` = 0, `android` = ".$this->via_android.", `seen_today` = 1, `token` = '" . md5(uniqid() . $this->ip . $this->nick) . "' WHERE `id` = '$this->id'");
			$userinfo = get_user($found, true);

			$this->update_visits();

			$this->logout_hash = substr(md5($this->ip . 'NoKidding' . $this->id), 0, 6);

			return true;
		} else {
			$db->query("INSERT INTO `failed_logins` (`date`, `username`, `ip`) VALUES (NOW(), '$login', '$this->ip')");
			sleep(rand(1, 3));
			$this->error = 1;
			return false;
		}
		return false;
	}

	function logout() {
		global $db;
		$lang = get_lang();
		$db->query("UPDATE `users` SET `lastseen` = '" . date('Y-m-d H:i:s', time() - 360) . "', `mobile` = 0, `android` = 0 WHERE `id` = '$this->id' LIMIT 1");
		$db->query("UPDATE `visits` SET `lastseen` = '" . date('Y-m-d H:i:s', time() - 360) . "' WHERE `user_id` = '$this->id' AND `site_id` = $lang AND `ip` = '$this->ip'");
		$this->id = 0;
		$this->nick = "Guest";
		$this->level = 0;
		$this->skin = 0;
		$this->vote_today = 0;
		$this->showsig = 1;
		$this->karma = 0;
		$this->persona = '';
		$this->ok = false;
		$_SESSION['auth_id'] = '';
		session_regenerate_id(true);
		session_destroy();
	}

	function update_counter() {
		global $db, $m;
		$lang = get_lang();

		if ($db->get_var("SELECT count(*) FROM `counter_ip` WHERE `ip_addr` = '" . $this->ip . "' AND `site_id` = ".$lang)) {
			$db->query("UPDATE `counter_ip` SET `last_hit` = CURRENT_TIMESTAMP WHERE `ip_addr` = '" . $this->ip . "' AND `site_id` = ".$lang);
		} else {
			$db->query("INSERT INTO `counter_ip` (`ip_addr`, `last_hit`, `site_id`) VALUES ('" . $this->ip . "', CURRENT_TIMESTAMP, ".$lang.")");
		}

		if (!($this->hosts_online = $m->get('online_count_' . $lang))) {
			$db->query("DELETE FROM `counter_ip` WHERE CURRENT_TIMESTAMP - INTERVAL 300 SECOND > `last_hit`");
			$this->hosts_online = (int) $db->get_var("SELECT count(*) FROM `counter_ip` WHERE `site_id` = ".$lang);
			$m->set('online_count_' . $lang, "$this->hosts_online", false, 10);
		}
	}

	function log($action, $foreign_table = '', $foreign_key = 0) {
		global $db;
		return $db->query("INSERT INTO `logs` (`user_id`,`action`,`created`,`ip`,`foreign_table`,`foreign_key`) VALUES ('$this->id','" . sanitize($action) . "',NOW(),'$this->ip','" . sanitize($foreign_table) . "','" . intval($foreign_key) . "')");
	}

	/**
	 * Pārbauda, vai IP nāk no tor
	 */
	function is_tor_exit() {
		global $m;

		$is_tor = 'n';

		$wl = array(
			'77.93.29.110',
			'83.99.202.248',
			'85.254.5.40',
			'85.254.17.118',
			'85.254.34.183',
			'85.254.36.48',
			'86.63.168.151',
			'86.63.172.49',
			'86.63.183.96',
			'87.246.137.132',
			'89.248.83.3',
			'109.73.109.194',
			'136.169.15.221',
			'159.148.15.150',
			'159.148.36.129',
			'176.67.34.96',
			'176.67.40.27',
			'212.93.100.40',
			'212.142.121.225',
		);

		if(in_array($this->ip, $wl)) {
			return false;
		}

		if (($is_tor = $m->get('t-'.md5($this->ip))) === false) {
		
			$value = curl_get('http://check.getipintel.net/check.php?ip=' . $this->ip . '&contact=mad182@gmail.com');

			if ($value == "1") {
				$is_tor = 'y';
			} else {
				$is_tor = 'n';
			}
			$m->set('t-'.md5($this->ip), $is_tor, false, 9000);

		}

		if($is_tor === 'y') {
			return true;
		}
		return false;
	}

}

