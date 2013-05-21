<?php

if (!im_mod()) {
	redirect();
}

// jquery ielādē izvēlētā lietotāja vecos nikus, profilus, kuriem sakrīt parole, un bana termiņus
if (isset($_GET['display']) && is_numeric($_GET['display'])) {

	$userid = (int) $_GET['display'];
	$content = '';

	$user = $db->get_row("SELECT `id`,`nick`,`pwd`,`level` FROM `users` WHERE `id` = '" . $userid . "' ");
	if ($user) {

		// veic salīdzināšanu, vai paroles hash sakrīt ar kādu citu
		if (strlen($user->pwd) > 5 && !in_array($user->pwd, array('', ' '))) {
		
			// moderatori neredzēs, ja viņu paroles sakritīs ar kāda cita profila parolēm
			$pass = $db->get_results("SELECT `id`,`nick`,`lastseen`,`level`,`lastip` FROM `users` WHERE `pwd` LIKE '%" .$user->pwd . "%' AND `id` != '" . $user->id . "' AND `id` != '".$auth->id."' ORDER BY `nick` ASC ");
			
			if ($pass) {
			
				$content .= '<p class="infop"><strong>Parole sakrīt ar šādiem lietotājiem:</strong></p><p>';
				$content .= '<table><tr><th>Profils</th><th>Manīts</th><th>Pēdējā IP</th><th>Bans vēl...</th></tr>';
				
				foreach ($pass as $pwd) {
				
					// pārbauda, vai šim profilam ir aktīvs bans
					$active_ban = $db->get_row("SELECT `time`,`length` FROM `banned` WHERE `user_id` = '".$pwd->id."' ORDER BY `time` DESC ");
					
					if ($active_ban) {
						$len = time() - $active_ban->time;					
						if ($len < $active_ban->length) {
							$banned = '<strong>'.floor(($active_ban->length - $len)/60/60/24).'</strong> dienas';
						} else $banned = ' -- ';
					} else $banned = ' -- ';
					
					$pwd->lastseen = time_ago(strtotime($pwd->lastseen));
					$pwd->nick = usercolor($pwd->nick, $pwd->level, false, $pwd->id);
					$content .= '<tr><td><a href="/user/'.$pwd->id.'">'.$pwd->nick.'</a></td><td class="centered-result">pirms ' . $pwd->lastseen . '</td><td class="centered-result">'.$pwd->lastip.'</td><td class="centered-result">'.$banned.'</td></tr>';
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
			$content .= '<p class="infop" style="font-weight:bold;">Šis lietotājs savu lietotājvārdu pēdējā laikā nav mainījis!</p>';
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
	if (isset($_POST['nick']) && strlen(trim($_POST['nick'])) >= 3) {
		$field = 'nick';
		$criteria = '`nick` LIKE \'%' . sanitize(trim($_POST['nick'])) . '%\'';
		$tpl->assign('niks', trim($_POST['nick']));

		// meklēšana pēc e-pasta
	} else if (isset($_POST['mail']) && strlen(trim($_POST['mail'])) >= 3) {
		$field = 'mail';
		$criteria = '`mail` LIKE \'%' . sanitize(trim($_POST['mail'])) . '%\'';
		$tpl->assign('mails', trim($_POST['mail']));

		// meklēšana pēc pēdējās lietotās IP adreses
	} else if (isset($_POST['ip']) && strlen(trim($_POST['ip'])) >= 3) {
		$field = 'ip';
		$criteria = '`lastip` LIKE \'' . sanitize(trim($_POST['ip'])) . '\'';
		$tpl->assign('aipii', trim($_POST['ip']));

		// kļūdu gadījumā
	} else {
		$criteria = '1';
		$field = '';
	}


	$results = $db->get_results("SELECT `id`,`nick`,`mail`,`lastip`,`karma`,`date`,`level` FROM `users` WHERE " . $criteria . " ORDER BY ABS(`level`) DESC, `nick` ASC LIMIT 0,50");

	if ($results) {

		$tpl->newBlock('search-results');

		foreach ($results as $res) {

			$res->date = ceil((time() - strtotime($res->date)) / 60 / 60 / 24);
			if ($field == 'mail') {
			
				// izdzēš %-zīmes no formas ievades, citādi nebūtu, ko izcelt
				//$escaped = str_replace('%','',trim($_POST['mail']));
				// laikam nevajag. Parasti modi % nekur neliks.
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

