<?php

if (!im_mod()) {
	set_flash('Pieeja liegta!');
	redirect();
	exit;
}



// jquery ielādē izvēlētā lietotāja vecos nikus, profilus, kuriem sakrīt parole, bana termiņus un lietotās IP (daļu)
if (isset($_GET['display']) && is_numeric($_GET['display'])) {

	$userid = (int) $_GET['display'];
	$content = '';

	$user = $db->get_row("SELECT `id`,`nick`,`pwd`,`level` FROM `users` WHERE `id` = '$userid' ");
	if ($user) {
	
		// pārbauda, vai lietotājam ir aktīvs bans
		if ($ban = $db->get_row("SELECT `banned`.`reason`,`users`.`nick`,`banned`.`length`,`banned`.`time` FROM `banned` JOIN `users` ON `banned`.`author` = `users`.`id` WHERE `banned`.`user_id` = '$user->id' ORDER BY `banned`.`time` DESC")) {
			if (time() - $ban->time < $ban->length) { 
				$content .= '<p class="infop"><strong>Banots ar iemeslu:</strong> '.$ban->reason.' ('.$ban->nick.')</p>';
			}
		}
		
		// atrod pēdējās 10 lietotās IP
		if ($ips = $db->get_results("SELECT `ip`,`lastseen` FROM `visits` WHERE `user_id` = '$user->id' ORDER BY `lastseen` DESC LIMIT 0,10")) {
			$content .= '<p class="infop"><strong>Izmantotās IP:</strong><br />';
			foreach ($ips as $ip) {
				$content .= '<span style="margin-left:10px">'.$ip->ip.' (pirms '.time_ago(strtotime($ip->lastseen)).')</span><br />';
			}
			$content .= '</p>';
		}

		// veic salīdzināšanu, vai paroles hash nesakrīt ar kādu citu
		if (strlen($user->pwd) > 5 && !in_array($user->pwd, array('', ' '))) {
		
			// moderatori neredzēs, ja viņu paroles sakritīs ar kāda cita profila parolēm
			$pass = $db->get_results("SELECT `id`,`nick`,`lastseen`,`level`,`lastip` FROM `users` WHERE `pwd` LIKE '%" .$user->pwd . "%' AND `id` != '" . $user->id . "' AND `id` != '".$auth->id."' ORDER BY `nick` ASC ");
			
			if ($pass) {
			
				$content .= '<p class="infop"><strong>Parole sakrīt ar šādiem profiliem:</strong><br />';
				$content .= '<table><tr><th>Profils</th><th>Manīts</th><th>Pēdējā IP</th><th>Bans vēl...</th></tr>';
				
				$counter = 0;
				$add_class = '';
				foreach ($pass as $pwd) {
				
					// paslēps rindu, ja to profilu ar tādu pašu paroli ir daudz; varēs ar jQuery apskatīt
					if ($counter > 4) $add_class = ' class="hide-rows"';	
					
					// pārbauda, vai profilam ir aktīvs bans
					$active_ban = $db->get_row("SELECT `time`,`length` FROM `banned` WHERE `user_id` = '".$pwd->id."' ORDER BY `time` DESC ");
					
					if ($active_ban) {
						$len = time() - $active_ban->time;					
						if ($len < $active_ban->length) {
							$banned = '<strong>'.floor(($active_ban->length - $len)/60/60/24).'</strong> dienas';
						} else $banned = ' -- ';
					} else $banned = ' -- ';
					
					$pwd->lastseen = time_ago(strtotime($pwd->lastseen));
					$pwd->nick = usercolor($pwd->nick, $pwd->level, false, $pwd->id);
					$content .= '<tr'.$add_class.'><td><a href="/user/'.$pwd->id.'">'.$pwd->nick.'</a></td><td class="centered-result">pirms ' . $pwd->lastseen . '</td><td class="centered-result">'.$pwd->lastip.'</td><td class="centered-result">'.$banned.'</td></tr>';
					
					$counter++;
				}
				
				if ($counter > 5) {
					$content .= '<tr><td colspan="4" class="toggle-rows"><a class="show-rows" href="javascript:void(0);">rādīt vairāk</a></td></tr>';
				}
				
				$content .= '</table></p>';
			} else
				$content .= '<p class="infop"><strong>Parole ne ar vienu lietotāju nesakrīt.</strong></p>';

		} else {
			$content .= '<p class="infop"><strong>Parole ne ar vienu lietotāju nesakrīt.</strong></p>';
		}		
		
		// atrod vecos lietotājvārdus
		$usernames = $db->get_results("SELECT `user_id` AS `id`,`nick`,`changed` FROM `nick_history` WHERE `user_id` = '" . $user->id . "' ORDER BY `changed` DESC ");		
		if ($usernames) {		
			$content .= '<p class="infop"><strong>Iepriekšējie lietotājvārdi:</strong></p><p>';
			foreach ($usernames as $uname) {
				$uname->changed = date("d.m.Y, H:i", strtotime($uname->changed));
				$content .= '<a href="/user/' . $uname->id . '">' . $uname->nick . '</a> &nbsp;&nbsp;&nbsp;(mainīts: ' . $uname->changed . ')<br />';
			}
			$content .= '</p>';			
		} else {
			$content .= '<p class="infop" style="margin-bottom:10px"><strong>Šis lietotājs savu lietotājvārdu nav mainījis!</strong></p>';
		}

		echo $content;
	}
	exit;
}




$tpl->assignInclude('module-head', CORE_PATH . '/modules/' . $category->module . '/head.tpl');
$tpl->prepare();
$skinid = ($auth->skin == 1) ? 'dark' : 'light';
$tpl->assign('skinid', $skinid);

$tpl->newBlock('mod-cpanel');

if (isset($_POST['submit'])) {

	// meklēšana pēc lietotāja nika
	if (isset($_POST['nick']) && strlen(trim($_POST['nick'])) >= 2) {
		$field = 'nick';
		$criteria = '`nick` LIKE \'%' . sanitize(trim($_POST['nick'])) . '%\'';
		$tpl->assign('nick', trim($_POST['nick']));

		// meklēšana pēc e-pasta
	} else if (isset($_POST['mail']) && strlen(trim($_POST['mail'])) >= 3) {
		$field = 'mail';
		$criteria = '`mail` LIKE \'%' . sanitize(trim($_POST['mail'])) . '%\'';
		$tpl->assign('mail', trim($_POST['mail']));

		// meklēšana pēc pēdējās lietotās IP adreses
	} else if (isset($_POST['ip']) && strlen(trim($_POST['ip'])) >= 3) {
		$field = 'ip';
		// šeit neder % zīme, jo POST['ip'] laukā tā var tikt izmantota
		$criteria = '`lastip` LIKE \'' . sanitize(trim($_POST['ip'])) . '\'';
		$tpl->assign('ip', trim($_POST['ip']));

		// meklēšana pēc IP iekš 'visits'
	} else if (isset($_POST['vip']) && strlen(trim($_POST['vip'])) >= 3) {
		$field = 'vip';
		// šeit neder = zīme, jo POST['vip'] laukā tā var tikt izmantota
		$criteria = '`visits`.`ip` LIKE \'' . sanitize(trim($_POST['vip'])) . '\'';
		$tpl->assign('vip', trim($_POST['vip']));

		// kļūdu gadījumā
	} else {
		$criteria = '1';
		$field = '';
	}

	if ($field == 'vip') {
	
		$results = $db->get_results("SELECT 
			`users`.`id`, `users`.`nick`, `users`.`level`, `users`.`lastip`,
			`users`.`mail`, `users`.`karma`, `users`.`date`,
			`visits`.`ip` 
			FROM `visits` 
			JOIN `users` ON `visits`.`user_id` = `users`.`id` 
			WHERE " . $criteria . " 
			GROUP BY `user_id`
			ORDER BY ABS(`users`.`level`) DESC, `users`.`nick` ASC LIMIT 0,50");
	} else {
		$results = $db->get_results("SELECT `id`,`nick`,`mail`,`lastip`,`karma`,`date`,`level` FROM `users` WHERE " . $criteria . " ORDER BY ABS(`level`) DESC, `nick` ASC LIMIT 0,50");
	}

	if ($results) {

		$tpl->newBlock('search-results');
		if ($field == 'vip') {
			$tpl->assign('ip-type','Lietotā IP');
		} else {
			$tpl->assign('ip-type','Pēdējā IP');
		}
		
		foreach ($results as $res) {

			$res->date = ceil((time() - strtotime($res->date)) / 60 / 60 / 24);
			if ($field == 'vip') {
			
				// izdzēš %-zīmes no formas ievades, citādi nebūtu, ko izcelt.
				// Šeit % izmantošana paliek, citādi, piem., 212.93.100.1 atrastu veselu jūru citu IP, kam beigās ir vēl viens/divi cipari.
				$escaped = str_replace('%','',trim($_POST['vip']));
				$res->lastip = $res->ip;
				$res->lastip = str_replace($escaped, '<strong>' . $escaped . '</strong>', $res->lastip);
			}
			else if ($field == 'mail') {
			
				// izdzēš %-zīmes no formas ievades, citādi nebūtu, ko izcelt
				//$escaped = str_replace('%','',trim($_POST['mail']));
				// laikam nevajag. Parasti modi % nekur neliks
				$escaped = trim($_POST['mail']);
				$res->mail = str_replace($escaped, '<strong>' . $escaped . '</strong>', $res->mail);
			}
			else if ($field == 'ip') {
			
				// izdzēš %-zīmes no formas ievades, citādi nebūtu, ko izcelt.
				// Šeit lai % izmantošana paliek, citādi, piem., 212.93.100.1 atrastu veselu jūru citu IP, kam beigās ir vēl viens/divi cipari.
				$escaped = str_replace('%','',trim($_POST['ip']));
				$res->lastip = str_replace($escaped, '<strong>' . $escaped . '</strong>', $res->lastip);
			}
			$res->nick = usercolor($res->nick, $res->level, false, $res->id);
			$tpl->newBlock('search-result');
			$tpl->assignAll($res);
		}
	}
}	

