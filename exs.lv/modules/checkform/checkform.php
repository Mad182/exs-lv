<?php
/**	
 *	Ievades formas lietotāju profilu meklēšanai pēc atšķirīgiem kritērijiem.
 *
 *	Moduļa adrese: 		exs.lv/checkform
 *	Pēdējās izmaiņas: 	02.10.2013 ( Edgars )
 */

if ( !im_mod() ) {
	set_flash('Pieeja liegta!');
	redirect();
	exit;
}

/**
 *	globālie mainīgie
 */
 $limit_total_ips 		= 50;		// maksimālais skaits, cik pēdējās IP var apskatīt vienā stabiņā,
									// nospiežot "rādīt vairāk" pogu
									
 $limit_shown_ips 		= 10;		// IP skaits, cik parādīt pirms "rādīt vairāk" pogas
 $limit_shown_profiles 	= 10;		// profilu skaits, cik parādīt pirms "rādīt vairāk" pogas;
									// dažiem kadriem ir desmitiem fake profilu!


if ( isset($_GET['email']) && is_numeric($_GET['email']) ) {
	
	$content = 'Nav norādīts!';
	
	$user = $db->get_row("SELECT `mail` FROM `users` WHERE `id` = '".(int)$_GET['email']."' ");
	if ( $user ) {
		$content = $user->mail;
	}
	
	echo $content;
	exit;
}

/**
 *	jQuery pieprasījums, kas ielādē šādus datus:
 *
 *		- lietotāja vecos lietotājvārdus
 *		- profilus, ar kuriem sakrīt paroles hash
 *		- banu termiņus
 *		- iepriekš izmantotās IP (noteiktu skaitu)
 *
 */
if ( isset($_GET['display']) && is_numeric($_GET['display']) ) {

	$userid 	= (int) $_GET['display'];
	$content 	= '';

	$user = $db->get_row("SELECT `id`,`nick`,`pwd`,`level` FROM `users` WHERE `id` = '$userid' ");
	if ( !$user ) {
		echo '';
		exit;
	}

	
	//$content = '<div><a class="clue" href="javascript:void()" rel="/checkform/?email=115" title="">115</a><div class="c"></div></div><div class="c"></div>';
	
	
	// pārbauda, vai lietotājam ir aktīvs bans
	$ban = $db->get_row("
		SELECT 
			`banned`.`reason`,				
			`banned`.`length`,
			`banned`.`time`,
			`users`.`nick`
		FROM `banned` 
			JOIN `users` ON `banned`.`author` = `users`.`id` 
		WHERE 
			`banned`.`user_id` = '$user->id' 
		ORDER BY 
			`banned`.`time` DESC
	");
	if ( $ban && (time() - $ban->time < $ban->length) ) {
		$content .= '<p class="infop"><strong>Bloķēts ar iemeslu:</strong> '.$ban->reason.' ('.$ban->nick.')</p>';
	}
	
	
	
	// atrod pēdējās x lietotās IP
	$all_ips = $db->get_results("
		SELECT `ip`, `lastseen` FROM `visits` 
		WHERE 
			`user_id` = '$user->id' 
		ORDER BY 
			`lastseen` DESC 
		LIMIT 0, $limit_total_ips
	");
	
	$unique_ips = $db->get_results("
		SELECT `visits`.`ip` FROM `visits`
		WHERE 
			`visits`.`user_id` = '$user->id'
		GROUP BY `visits`.`ip`
		LIMIT 0, $limit_total_ips
	");
	
	if ( $all_ips || $unique_ips ) {
	
		$counter = 1;
	
		$content .= '<div id="ip_block">';
		$content .= '<p class="infop"><strong>Izmantotās IP:</strong></p>';
		
		// visu atrasto IP stabiņš ar laiku, kad šī IP izmantota
		if ( $all_ips ) {
			$content .= '<table id="all_ips" class="ip-table">';
			$content .= '<tr><td><strong>Pēdējās IP</strong></td></tr>';
			foreach ($all_ips as $ip) {
				$row_class = ( $counter > $limit_shown_ips ) ? ' class="hidden-row"' : '';
				$content .= '<tr'.$row_class.'><td>'.$ip->ip.' (pirms '.time_ago(strtotime($ip->lastseen)).')</td></tr>';
				$counter++;
			}
			if ( $counter-1 > $limit_shown_ips ) {
				$content .= '<tr><td>
					<a id="show_more_all" href="javascript:void();">Rādīt <span class="toggle-text">vairāk</span></a>
				</td></tr>';
			}
			$content .= '</table>';
		}
		
		$row_class 	= '';
		$counter 	= 1;
		
		// unikālo izmantoto IP stabiņš
		if ( $unique_ips ) {
			$content .= '<table id="unique_ips" class="ip-table">';
			$content .= '<tr><td><strong>Unikālās IP</strong></td></tr>';
			foreach ($unique_ips as $ip) {
				$row_class = ( $counter > $limit_shown_ips ) ? ' class="hidden-row"' : '';
				$content .= '<tr'.$row_class.'><td>'.$ip->ip.'</td></tr>';
				$counter++;
			}
			if ( $counter-1 > $limit_shown_ips ) {
				$content .= '<tr><td>
					<a id="show_more_unique" href="javascript:void();">Rādīt <span class="toggle-text">vairāk</span></a>
				</td></tr>';
			}
			$content .= '</table>';
		}

		$content .= '</div>';
	} 
	else {
		$content .= '<p class="infop"><strong>Izmantotās IP:</strong><br>Nav fiksētas!</p>';
	}

	
	
	// veic salīdzināšanu, vai paroles hash nesakrīt ar kāda cita profila hash
	if (strlen($user->pwd) > 5 && !in_array($user->pwd, array('', ' '))) {
	
		// moderatori neredzēs, ja viņu paroles sakritīs ar kāda cita profila parolēm
		$pass = $db->get_results("
			SELECT 
				`users`.`id`,
				`users`.`nick`,
				`users`.`lastseen`,
				`users`.`level`,
				`users`.`lastip`,
				`users`.`mail`
			FROM `users`				
			WHERE 
				`users`.`pwd` LIKE '%" .$user->pwd . "%' AND 
				`users`.`id` != '" . $user->id . "' AND 
				`users`.`id` != '" . $auth->id . "' 
			ORDER BY `users`.`nick` ASC 
		");
		
		if ( !$pass ) {
			$content .= '<p class="infop"><strong>Parole ne ar vienu lietotāju nesakrīt.</strong></p>';
		}
		else {
			$content .= '<p class="infop"><strong>Parole sakrīt ar šādiem profiliem:</strong><br />';
			$content .= '<table><tr><th>Profils</th><th>Manīts</th><th>Pēdējā IP</th><th>Bans vēl...</th></tr>';
			
			$counter 	= 0;
			$add_class 	= '';
			
			foreach ($pass as $pwd) {
			
				// 	paslēps rindu, ja to profilu ar tādu pašu paroli ir daudz; 
				//	varēs apskatīt ar jQuery
				if ($counter > $limit_shown_profiles) $add_class = ' class="hide-rows"';	
				
				// 	pārbauda, vai profilam ir aktīvs bans
				$active_ban = $db->get_row("SELECT `time`,`length` FROM `banned` WHERE `user_id` = '".$pwd->id."' ORDER BY `time` DESC ");
				
				if ( $active_ban ) {
					$len = time() - $active_ban->time;					
					if ($len < $active_ban->length) {
						$banned = '<strong>'.floor(($active_ban->length - $len)/60/60/24).'</strong> dienas';
					} else $banned = ' -- ';
				} else $banned = ' -- ';
				
				$pwd->lastseen 	= time_ago(strtotime($pwd->lastseen));
				$pwd->nick 		= usercolor($pwd->nick, $pwd->level, false, $pwd->id);
				//$pwd->mail		= textlimit(substr($pwd->mail,0, strpos($pwd->mail, '@')),20);
				
				$content .= '<tr'.$add_class.'>
								<td><a href="/user/'.$pwd->id.'" title="E-pasts: '.$pwd->mail.'">'.$pwd->nick.'</a></td>
								<td class="centered-result">pirms ' . $pwd->lastseen . '</td>
								<td class="centered-result">'.$pwd->lastip.'</td>
								<td class="centered-result">'.$banned.'</td>
							</tr>';
				
				$counter++;
			}
			
			if ($counter > $limit_shown_profiles + 1) {
				$content .= '<tr><td colspan="4" class="toggle-rows"><a class="show-rows" href="javascript:void(0);">rādīt vairāk</a></td></tr>';
			}
			
			$content .= '</table></p>';
		}
			

	} else {
		$content .= '<p class="infop"><strong>Parole ne ar vienu lietotāju nesakrīt.</strong></p>';
	}		
	
	
	
	// atrod vecos lietotājvārdus
	$usernames = $db->get_results("
		SELECT 
			`user_id` AS `id`,
			`nick`,
			`changed` 
		FROM `nick_history` 
		WHERE 
			`user_id` = '" . $user->id . "' 
		ORDER BY 
			`changed` DESC
	");	
		
	if ( $usernames ) {		
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
	exit;
}





/**
 *	Ievades formas un meklēšanas rezultāti
 */

$tpl->assignInclude('module-head', CORE_PATH . '/modules/' . $category->module . '/head.tpl');
$tpl->prepare();
$skinid = ($auth->skin == 1) ? 'dark' : 'light';
$tpl->assign('skinid', $skinid);

$tpl->newBlock('mod-cpanel');

if (isset($_POST['submit'])) {

	// meklēšana pēc lietotāja nika
	if (isset($_POST['nick']) && strlen(trim($_POST['nick'])) >= 2) {
		$field 		= 'nick';
		$criteria 	= '`nick` LIKE \'%' . sanitize(trim($_POST['nick'])) . '%\'';
		$tpl->assign('nick', trim($_POST['nick']));

		// meklēšana pēc e-pasta
	} else if (isset($_POST['mail']) && strlen(trim($_POST['mail'])) >= 3) {
		$field 		= 'mail';
		$criteria 	= '`mail` LIKE \'%' . sanitize(trim($_POST['mail'])) . '%\'';
		$tpl->assign('mail', trim($_POST['mail']));

		// meklēšana pēc pēdējās lietotās IP adreses
	} else if (isset($_POST['ip']) && strlen(trim($_POST['ip'])) >= 3) {
		$field 		= 'ip';
		// šeit neder % zīme, jo POST['ip'] laukā to ir ļauts pielietot
		$criteria 	= '`lastip` LIKE \'' . sanitize(trim($_POST['ip'])) . '\'';
		$tpl->assign('ip', trim($_POST['ip']));

		// meklēšana pēc IP iekš 'visits'
	} else if (isset($_POST['vip']) && strlen(trim($_POST['vip'])) >= 3) {
		$field 		= 'vip';
		// šeit neder = zīme, jo POST['vip'] laukā tā var tikt izmantota
		$criteria 	= '`visits`.`ip` LIKE \'' . sanitize(trim($_POST['vip'])) . '\'';
		$tpl->assign('vip', trim($_POST['vip']));

		// kļūdu gadījumā
	} else {
		$criteria 	= '1';
		$field 		= '';
	}

	if ($field == 'vip') {
	
		$results = $db->get_results("
			SELECT 
				`users`.`id`, 
				`users`.`nick`, 
				`users`.`level`, 
				`users`.`lastip`,
				`users`.`mail`, 
				`users`.`karma`, 
				`users`.`date`,
				`visits`.`ip` 
			FROM `visits` 
				JOIN `users` ON `visits`.`user_id` = `users`.`id` 
			WHERE " . $criteria . " 
			GROUP BY 
				`user_id`
			ORDER BY 
				ABS(`users`.`level`) DESC, 
				`users`.`nick` ASC 
			LIMIT 0,50
	");
	} else {
		$results = $db->get_results("
			SELECT 
				`id`,`nick`,`mail`,`lastip`,`karma`,`date`,`level` 
			FROM `users` 
			WHERE " . $criteria . " 
			ORDER BY 
				ABS(`level`) DESC, 
				`nick` ASC 
			LIMIT 0,50
		");
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
				// šeit % izmantošana paliek, citādi, piem., 212.93.100.1 atrastu veselu jūru citu IP, 
				// kam beigās ir vēl viens/divi cipari.
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
				// Šeit lai % izmantošana paliek, citādi, piem., 212.93.100.1 atrastu veselu jūru citu IP, 
				// kam beigās ir vēl viens/divi cipari.
				$escaped = str_replace('%','',trim($_POST['ip']));
				$res->lastip = str_replace($escaped, '<strong>' . $escaped . '</strong>', $res->lastip);
			}
			$res->nick = usercolor($res->nick, $res->level, false, $res->id);
			$tpl->newBlock('search-result');
			$tpl->assignAll($res);
		}
	}
}	

