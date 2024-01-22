<?php

/**
 * Lietotāja autorizācija un globāls aktīvā lietotāja objekts ($auth)
 *
 * MOBILĀ VERSIJA (atzīmē ka lietotājs ir ielogojies m. versijā)
 *
 * paroles tiek glabātas izmantojot bcrypt
 */
require(CORE_PATH . '/includes/class.authbase.php');

class Auth extends AuthBase {

	/**
	 * Inicializē lietotāja objektu
	 */
	function __construct() {
		global $remote_salt;

		$this->id = 0;
		$this->avatar = 'none.png';
		$this->error = 0;
		$this->level = 0;
		$this->persona = '';
		$this->karma = 0;
		$this->skin = 3;
		$this->vote_today = 0;
		$this->transfer = '';
		$this->showsig = 1;
		$this->nick = "Viesis";
		$this->flood = 8;
		$this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		$this->ok = false;
		if (!empty($_SESSION['xsrf'])) {
			$this->xsrf = $_SESSION['xsrf'];
		} else {
			$this->xsrf = md5($this->ip . $remote_salt . microtime(true));
			$_SESSION['xsrf'] = $this->xsrf;
		}
		$this->check_session();
		$this->logout_hash = substr(md5($this->ip . 'NoKidding' . $this->id), 0, 6);
		$this->mobile = 1;
		$this->via_android = 0;
		return $this->ok;
	}

	function check_session() {
		global $db, $lang;

		if (empty($_SESSION['auth_id'])) {
			return false;
		}
		$userinfo = get_user($_SESSION['auth_id']);

		if($userinfo->auth_2fa && empty($_SESSION['2fa']) && $_GET['viewcat'] !== '2fa' && $_GET['viewcat'] !== 'mb-latest' && $_GET['viewcat'] !== 'mb-latest') {
			redirect('/2fa');
		}

		if ($userinfo->deleted) {
			return $this->logout();
		}

		foreach ($userinfo as $key => $val) {
			$this->$key = $val;
		}
		$this->ok = true;

		if (empty($_SESSION['lastseen']) || $_SESSION['lastseen'] < time() - 360) {
			if (empty($_SESSION['admin_simulate'])) {
				$db->query("UPDATE `users` SET `lastseen` = NOW(), `android` = 0, `mobile` = 1, `mobile_seen` = 1, `seen_today` = 1 WHERE `id` = '$this->id'");
			}
			$_SESSION['lastseen'] = time();
		}

		if (empty($_SESSION['admin_simulate'])) {
			$this->update_visits();
		}

		if ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])) {
			$this->logout();
			redirect();
		}

		if ($ban = $db->get_var("SELECT `id` FROM `banned` WHERE `active` = 1 AND (`user_id` = '$this->id' OR `ip` = '$this->ip') AND (`lang` = 0 OR `lang` = '$lang') LIMIT 1")) {
			$this->logout();
			set_flash('Pieeja lapai ir liegta!', 'error');
			redirect('http://exs.lv/?c=125&bid=' . $ban);
		}

		return true;
	}

	function login($username, $password, $xsrf = null) {
		global $db, $lang;
		
		if($this->is_tor_exit()) {
			$this->logout();
			return false;
		}

		session_regenerate_id(true);

		if (!is_null($xsrf) && $xsrf != $this->xsrf) {
			sleep(1);
			$this->error = 2;
			return false;
		}

		$login = sanitize($username);

		$tmp = $db->get_row("SELECT `id`, `password` FROM `users` WHERE (`nick` = '" . $login . "' OR `mail` = '" . $login . "') AND `deleted` = 0 ORDER BY `karma` DESC LIMIT 1");

		$found = false;
		if (!empty($tmp)) {

			if (!empty($tmp->password) && password_verify($password, $tmp->password)) {
				$found = $tmp->id;
			}

		}

		if ($found) {
			$userinfo = get_user($found, true);
			foreach ($userinfo as $key => $val) {
				$this->$key = $val;
			}
			$this->ok = true;
			$_SESSION['auth_id'] = $userinfo->id;
			$_SESSION['lastseen'] = time();
			$_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);
			$this->error = 0;

			if ($ban = $db->get_var("SELECT `id` FROM `banned` WHERE `active` = 1 AND (`user_id` = '$this->id' OR `ip` = '$this->ip') AND (`lang` = 0 OR `lang` = '$lang') LIMIT 1")) {
				$this->logout();
				set_flash('Pieeja lapai ir liegta!', 'error');
				redirect('http://exs.lv/?c=125&bid=' . $ban);
			}

			$db->query("UPDATE users SET `lastseen` = NOW(), `lastip` = ('" . sanitize($this->ip) . "'), `user_agent` = ('" . sanitize($_SERVER['HTTP_USER_AGENT']) . "'), `android` = 0, `mobile` = 1, `mobile_seen` = 1, `seen_today` = 1 WHERE id = '$userinfo->id'");
			$this->update_visits();

			update_karma($this->id, true);
			return true;
		} else {
			sleep(rand(1, 3));
			$this->error = 1;
			return false;
		}
		return false;
	}

	function logout() {
		global $db, $lang;
		$db->query("UPDATE users SET `lastseen` = '" . date('Y-m-d H:i:s', time() - 360) . "', `android` = 0, `mobile` = 1 WHERE id = '$this->user_id' LIMIT 1");
		$db->query("UPDATE `visits` SET `lastseen` = '" . date('Y-m-d H:i:s', time() - 360) . "' WHERE `user_id` = '$this->id' AND `site_id` = $lang AND `ip` = '$this->ip'");
		$this->user_id = 0;
		$this->username = "Guest";
		$this->access = 0;
		$this->skin = 0;
		$this->vote_today = 0;
		$this->block_cs = 0;
		$this->showsig = 1;
		$this->custom_bg = '';
		$this->custom_others = 0;
		$this->persona = '';
		$this->ok = false;
		$_SESSION['auth_id'] = '';
		session_regenerate_id(true);
		session_destroy();
	}

}

