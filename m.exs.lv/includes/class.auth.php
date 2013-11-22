<?php

class Auth {

	var $id;
	var $nick;
	var $ok;
	var $level = 0;
	var $avatar = 'none.png';
	var $skin = 3;
	var $custom_bg = '';
	var $custom_others = 0;
	var $showsig = 1;
	var $error = 0;
	var $persona = '';
	var $mobile = 1;

	function Auth() {
		global $remote_salt;

		$this->id = 0;
		$this->avatar = 0;
		$this->error = 0;
		$this->level = 0;
		$this->persona = '';
		$this->custom_bg = '';
		$this->custom_others = 0;
		$this->vote_today = 0;
		$this->block_cs = 0;
		$this->showsig = 1;
		$this->mobile = 1;
		$this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		$this->nick = "Guest";
		$this->ok = false;
		if(!empty($_SESSION['xsrf'])) {
			$this->xsrf = $_SESSION['xsrf'];
		} else {
			$this->xsrf = md5($this->ip . $remote_salt . microtime(true));
			$_SESSION['xsrf'] = $this->xsrf;
		}
		$this->check_session();
		return $this->ok;
	}

	function update_visits() {
		global $db, $lang;
		$exists = $db->get_var("SELECT `id` FROM `visits` WHERE `user_id` = $this->id AND `ip` = '$this->ip' AND `site_id` = $lang");
		if($exists) {
			$db->query("UPDATE `visits` SET `lastseen` = NOW() WHERE `id` = $exists");
		} else {
			$db->query("INSERT INTO `visits` (`user_id`, `site_id`, `ip`, `lastseen`) VALUES ($this->id, $lang, '$this->ip', NOW())");
		}
	}

	function check_session() {
		global $db, $lang;

		if (!empty($_SESSION['auth_id'])) {
			$userinfo = get_user($_SESSION['auth_id']);
			foreach ($userinfo as $key => $val) {
				$this->$key = $val;
			}
			$this->ok = true;

			if (empty($_SESSION['lastseen']) || $_SESSION['lastseen'] < time() - 360) {
				if (empty($_SESSION['admin_simulate'])) {
					$db->query("UPDATE `users` SET `lastseen` = NOW(), `mobile` = 1, `mobile_seen` = 1, `seen_today` = 1 WHERE `id` = '$this->id'");
				}
				$_SESSION['lastseen'] = time();
			}

			if (empty($_SESSION['admin_simulate'])) {
				$this->update_visits();
			}

			if($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])) {
				$this->logout();
				redirect();
			}

			if ($ban = $db->get_var("SELECT `id` FROM `banned` WHERE (`user_id` = '$this->id' OR `ip` = '$this->ip') AND `time`+`length` > '" . time() . "' AND (`lang` = 0 OR `lang` = '$lang') ORDER BY `time` DESC LIMIT 1")) {
				$this->logout();
				set_flash('Pieeja lapai ir liegta!', 'error');
				redirect('http://exs.lv/?c=125&bid=' . $ban);
			}

			return true;
		} else {
			return false;
		}
	}

	function reset() {
		global $db;
		if (!empty($_SESSION['auth_id'])) {
			$userinfo = get_user($_SESSION['auth_id'], true);

			session_regenerate_id(true);

			foreach ($userinfo as $key => $val) {
				$this->$key = $val;
			}
			$_SESSION['auth_id'] = $userinfo->id;
			$this->ok = true;
			return true;
		} else {
			return false;
		}
	}

	function login($username, $password, $xsrf = null) {
		global $db, $lang;

		session_regenerate_id(true);

		if(!is_null($xsrf) && $xsrf != $this->xsrf) {
			sleep(rand(2,4));
			$this->error = 2;
			return false;
		}

		$pwd = pwd($password);
		$login = sanitize($username);

		$found = $db->get_var("SELECT `id` FROM `users` WHERE (`nick` = '".$login."' OR `mail` = '".$login."') AND `pwd` = '$pwd' AND `deleted` = 0 LIMIT 1");

		if ($found) {
			$userinfo = get_user($found, true);
			foreach ($userinfo as $key => $val) {
				$this->$key = $val;
			}
			$this->ok = true;
			$_SESSION['auth_id'] = $userinfo->id;
			$_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);
			$this->error = 0;

			if ($ban = $db->get_var("SELECT `id` FROM `banned` WHERE (`user_id` = '$this->id' OR `ip` = '$this->ip') AND `time`+`length` > '" . time() . "' AND (`lang` = 0 OR `lang` = '$lang') ORDER BY `time` DESC LIMIT 1")) {
				$this->logout();
				set_flash('Pieeja lapai ir liegta!', 'error');
				redirect('http://exs.lv/?c=125&bid=' . $ban);
			}

			$db->query("UPDATE users SET `lastseen` = NOW(), `lastip` = ('" . sanitize($this->ip) . "'), `user_agent` = ('" . sanitize($_SERVER['HTTP_USER_AGENT']) . "'), `mobile` = 1, `mobile_seen` = 1, `seen_today` = 1 WHERE id = '$userinfo->id'");
			$this->update_visits();

			update_karma($this->id, true);
			return true;
		} else {
			sleep(rand(2,4));
			$this->error = 1;
			return false;
		}
		return false;
	}

	function logout() {
		global $db, $lang;
		$db->query("UPDATE users SET `lastseen` = '" . date('Y-m-d H:i:s', time() - 360) . "', `mobile` = 1 WHERE id = '$this->user_id' LIMIT 1");
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

	function update_counter() {
		global $db, $m, $lang;

		if ($db->get_var("SELECT count(*) FROM `counter_ip` WHERE `ip_addr` = '" . $this->ip . "' AND `site_id` = $lang")) {
			$db->query("UPDATE counter_ip SET last_hit = CURRENT_TIMESTAMP WHERE ip_addr = '" . $this->ip . "' AND `site_id` = $lang");
		} else {
			$db->query("INSERT INTO `counter_ip` (`ip_addr`, `last_hit`, `site_id`) VALUES ('" . $this->ip . "', CURRENT_TIMESTAMP, $lang)");
		}

		if (!($this->hosts_online = $m->get('online_count_'.$lang))) {
			$db->query("DELETE FROM `counter_ip` WHERE CURRENT_TIMESTAMP - INTERVAL 300 SECOND > `last_hit`");
			$this->hosts_online = (int) $db->get_var("SELECT count(*) FROM `counter_ip` WHERE `site_id` = $lang");
			$m->set('online_count_'.$lang, "$this->hosts_online", false, 10);
		}
	}

	function log($action, $foreign_table = '', $foreign_key = 0) {
		global $db;
		return $db->query("INSERT INTO `logs` (`user_id`,`action`,`created`,`ip`,`foreign_table`,`foreign_key`) VALUES ('$this->id','".sanitize($action)."',NOW(),'$this->ip','".sanitize($foreign_table)."','".intval($foreign_key)."')");
	}

}
