<?php

class Auth {

	var $id;
	var $nick;
	var $password;
	var $ok;
	var $level = 0;
	var $rte = 0;
	var $avatar = 'none.png';
	var $skin = 3;
	var $showsig = 1;
	var $block_cs = 0;
	var $error = 0;
	var $persona = '';
	var $mobile = 0;

	function Auth() {
		global $remote_salt;

		$this->id = 0;
		$this->avatar = 0;
		$this->error = 0;
		$this->level = 0;
		$this->persona = '';
		$this->karma = 0;
		$this->vote_today = 0;
		$this->block_cs = 0;
		$this->rte = 0;
		$this->transfer = '';
		$this->showsig = 1;
		$this->mobile = 0;
		$this->nick = "Viesis";
		$this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		$this->ok = false;
		if(!empty($_SESSION['xsrf'])) {
			$this->xsrf = $_SESSION['xsrf'];
		} else {
			$this->xsrf = md5($this->ip . $remote_salt . microtime(true));
			$_SESSION['xsrf'] = $this->xsrf;
		}
		$this->check_session();
		$this->update_counter();
		return $this->ok;
	}

	function setcookie($title, $data = null) {
		if (defined('LOCAL_DEV')) {
			setcookie($title, $data, time() + 3600, "/");
		} else {
			setcookie($title, $data, time() + 3600, "/", ".exs.lv", 0, false);
		}
	}

	function update_visits() {
		global $db, $lang;
		$exists = $db->get_var("SELECT `id` FROM `visits` WHERE `user_id` = $this->id AND `ip` = '$this->ip' AND `site_id` = $lang");
		if ($exists) {
			$db->query("UPDATE `visits` SET `lastseen` = NOW() WHERE `id` = $exists");
		} else {
			$db->query("INSERT INTO `visits` (`user_id`, `site_id`, `ip`, `lastseen`) VALUES ($this->id, $lang, '$this->ip', NOW())");
		}
	}

	function check_session() {
		global $db, $site_admins, $site_mods, $lang;

		if (!empty($_SESSION['auth_id'])) {
			$userinfo = get_user($_SESSION['auth_id']);
			foreach ($userinfo as $key => $val) {
				$this->$key = $val;
			}

			$this->interests = $db->get_col("SELECT `interest_id` FROM `user_interests` WHERE `user_id` = '$this->id'");

			if (in_array($this->id, $site_admins)) {
				$this->level = 1;
			}

			if (in_array($this->id, $site_mods)) {
				$this->level = 2;
			}

			$this->ok = true;

			/*
			 * ieseto cepumus
			 * šie nav paredzēti lai autentificētu lietotāju exs.lv,
			 * bet lai varētu piekļūt nikam/id no flash spēlēm un party.exs.lv
			 */
			$this->setcookie("ex_nick", $this->nick);
			$this->setcookie("ex_id", $this->id);
			$this->setcookie("ex_check", md5($this->nick . '-' . $this->id . '-aargh'));

			if (empty($_SESSION['lastseen']) || $_SESSION['lastseen'] < time() - 480) {
				if (empty($_SESSION['admin_simulate'])) {
					$db->query("UPDATE `users` SET `lastseen` = NOW(), `mobile` = 0, `seen_today` = 1 WHERE `id` = '$this->id'");
				}
				$_SESSION['lastseen'] = time();
			}

			if (empty($_SESSION['admin_simulate']) && (empty($_SESSION['updvisits']) || $_SESSION['updvisits'] < time() - 30)) {
				$this->update_visits();
				$_SESSION['updvisits'] = time();
			}

			$this->transfer = '?transfer=' . $this->token;

			if (!isset($_GET['_']) && $ban = $db->get_var("SELECT `id` FROM `banned` WHERE (`user_id` = '$this->id' OR `ip` = '$this->ip') AND `time`+`length` > '" . time() . "' AND (`lang` = 0 OR `lang` = '$lang') ORDER BY `time` DESC LIMIT 1")) {
				$this->logout();
				set_flash('Pieeja lapai ir liegta!', 'error');
				redirect('http://exs.lv/?c=125&bid=' . $ban);
			}

			return true;
		} elseif (isset($_GET['transfer']) && !empty($_GET['transfer'])) {
			$transfer = sanitize($_GET['transfer']);

			$userinfo = $db->get_row("SELECT * FROM `users` WHERE `token` = '$transfer' AND `lastip` = '$this->ip' AND `lastseen` > '" . date('Y-m-d H:i:s', time() - 3600) . "' AND `user_agent` = '" . sanitize($_SERVER['HTTP_USER_AGENT']) . "'");

			if (!empty($userinfo)) {

				foreach ($userinfo as $key => $val) {
					$this->$key = $val;
				}

				$this->interests = $db->get_col("SELECT `interest_id` FROM `user_interests` WHERE `user_id` = '$this->id'");

				if (in_array($this->id, $site_admins)) {
					$this->level = 1;
				}

				if (in_array($this->id, $site_mods)) {
					$this->level = 2;
				}

				$this->ok = true;
				$_SESSION['auth_id'] = $this->id;
				$db->query("UPDATE `users` SET `lastseen` = NOW(), `mobile` = 0, `seen_today` = 1 WHERE `id` = '$this->id'");
				$this->update_visits();

				$_SESSION['lastseen'] = time();

				if (!isset($_GET['_']) && $ban = $db->get_var("SELECT `id` FROM `banned` WHERE (`user_id` = '$this->id' OR `ip` = '$this->ip') AND `time`+`length` > '" . time() . "' AND (`lang` = 0 OR `lang` = '$lang') ORDER BY `time` DESC LIMIT 1")) {
					$this->logout();
					set_flash('Pieeja lapai ir liegta!', 'error');
					redirect('http://exs.lv/?c=125&bid=' . $ban);
				}
			}
		} else {
			return false;
		}
	}

	function reset() {
		if (!empty($_SESSION['auth_id'])) {
			$userinfo = get_user($_SESSION['auth_id'], true);
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
		global $db, $site_admins, $site_mods, $lang;

		if(!is_null($xsrf) && $xsrf != $this->xsrf) {
			sleep(rand(2,4));
			$this->error = 2;
			return false;
		}

		$pwd = pwd($password);
		$login = sanitize($username);

		$found = $db->get_var("SELECT `id` FROM `users` WHERE (`nick` = '" . $login . "' OR `mail` = '" . $login . "') AND `pwd` = '$pwd' LIMIT 1");

		if (!$found) {
			$pwd_old = md5(md5($password));
			$found = $db->get_var("SELECT `id` FROM `users` WHERE (`nick` = '" . $login . "' OR `mail` = '" . $login . "') AND `password` = '$pwd_old' LIMIT 1");
			if ($found) {
				$db->query("UPDATE `users` SET `pwd` = '$pwd', `password` = '' WHERE `id` = '$found'");
			}
		}

		if ($found) {
			$userinfo = get_user($found, true);
			foreach ($userinfo as $key => $val) {
				$this->$key = $val;
			}

			$this->interests = $db->get_col("SELECT `interest_id` FROM `user_interests` WHERE `user_id` = '$this->id'");

			if (in_array($this->id, $site_admins)) {
				$this->level = 1;
			}

			if (in_array($this->id, $site_mods)) {
				$this->level = 2;
			}

			$this->ok = true;
			$_SESSION['auth_id'] = $userinfo->id;
			$_SESSION['lastseen'] = time();
			$this->error = 0;

			$this->setcookie("ex_nick", $this->nick);
			$this->setcookie("ex_id", $this->id);
			$this->setcookie("ex_check", md5($this->nick . '-' . $this->id . '-aargh'));

			if ($ban = $db->get_var("SELECT `id` FROM `banned` WHERE (`user_id` = '$this->id' OR `ip` = '$this->ip') AND `time`+`length` > '" . time() . "' AND (`lang` = 0 OR `lang` = '$lang') ORDER BY `time` DESC LIMIT 1")) {
				$this->logout();
				set_flash('Pieeja lapai ir liegta!', 'error');
				redirect('http://exs.lv/?c=125&bid=' . $ban);
			}

			$db->query("UPDATE `users` SET `lastseen` = NOW(), `lastip` = '" . $this->ip . "', `user_agent` = '" . sanitize($_SERVER['HTTP_USER_AGENT']) . "', `mobile` = 0, `seen_today` = 1, `token` = '" . md5(uniqid() . $this->ip . $this->nick) . "' WHERE `id` = '$this->id'");
			$userinfo = get_user($found, true);
			$this->transfer = '?transfer=' . $userinfo->token;

			$this->update_visits();
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
		if (empty($_SESSION['admin_simulate'])) {
			$db->query("UPDATE `users` SET `lastseen` = '" . date('Y-m-d H:i:s', time() - 360) . "', `mobile` = 0 WHERE `id` = '$this->id' LIMIT 1");
			$db->query("UPDATE `visits` SET `lastseen` = '" . date('Y-m-d H:i:s', time() - 360) . "' WHERE `user_id` = '$this->id' AND `site_id` = $lang AND `ip` = '$this->ip'");
			$this->id = 0;
			$this->nick = "Guest";
			$this->level = 0;
			$this->skin = 0;
			$this->rte = 0;
			$this->vote_today = 0;
			$this->block_cs = 0;
			$this->showsig = 1;
			$this->karma = 0;
			$this->persona = '';
			$this->transfer = '';
			$this->ok = false;
			$_SESSION['auth_id'] = '';
			session_destroy();

			$domain = '.exs.lv';

			if (defined('LOCAL_DEV')) {
				$domain = '';
			}

			setcookie("ex_nick", "", 1, "/", $domain, 0, true);
			setcookie("ex_id", "", 1, "/", $domain, 0, true);
			setcookie("ex_check", "", 1, "/", $domain, 0, true);
		} else {
			$_SESSION['auth_id'] = $_SESSION['admin_simulate'];
			$_SESSION['admin_simulate'] = '';
		}
	}

	function update_counter() {
		global $db, $m, $lang;

		if ($db->get_var("SELECT count(*) FROM `counter_ip` WHERE `ip_addr` = '" . $this->ip . "' AND `site_id` = $lang")) {
			$db->query("UPDATE counter_ip SET last_hit = NOW() WHERE ip_addr = '" . $this->ip . "' AND `site_id` = $lang");
		} else {
			$db->query("INSERT INTO `counter_ip` (`ip_addr`, `last_hit`, `site_id`) VALUES ('" . $this->ip . "', CURRENT_TIMESTAMP, $lang)");
		}

		if (!($this->hosts_online = $m->get('online_count_' . $lang))) {
			$db->query("DELETE FROM `counter_ip` WHERE CURRENT_TIMESTAMP - INTERVAL 300 SECOND > `last_hit`");
			$this->hosts_online = (int) $db->get_var("SELECT count(*) FROM `counter_ip` WHERE `site_id` = $lang");
			$m->set('online_count_' . $lang, "$this->hosts_online", false, 10);
		}
	}

	function log($action, $foreign_table = '', $foreign_key = 0) {
		global $db;
		return $db->query("INSERT INTO `logs` (`user_id`,`action`,`created`,`ip`,`foreign_table`,`foreign_key`) VALUES ('$this->id','" . sanitize($action) . "',NOW(),'$this->ip','" . sanitize($foreign_table) . "','" . intval($foreign_key) . "')");
	}

}
