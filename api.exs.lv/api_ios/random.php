<?php
/**
 *  Apstrādā random iOS lietotnes pieprasījumus, 
 *  kurus nav vērts iedalīt kādā specifiskā apakšgrupā.
 */

/**
 *  Pieprasītas lietotāja jaunākās notifikācijas.
 */
if ($var1 === 'notifications') {

	$arr_notifs = array(); // atgriežamais notifikāciju masīvs
	$notif_limit = 25; // cik pēdējos jaunumus atgriezt
	
	set_action('jaunākās notifikācijas');

	/*$texts = array(
		0 => 'atbilde komentāram', // rakstā
		1 => 'komentārs galerijā',
		2 => 'komentārs rakstam',
		3 => 'atbilde mb',
		4 => 'jauns biedrs tavā grupā',
		5 => 'tevi aicina draudzēties',
		6 => 'tev ir jauns draugs',
		7 => 'tu saņēmi medaļu',
		8 => 'atbilde grupā',
		9 => 'saņemta vēstule',
		10 => 'brīdinājums!',
		11 => 'noņemts brīdinājums',
		12 => 'jaunumi no exs.lv',
		13 => 'tevi pieminēja grupā',
		14 => 'tevi pieminēja mb',
		15 => 'tevi pieminēja rakstā',
		16 => 'tevi pieminēja galerijā'
	);*/
	
	// id tām notifikācijām, kas saistītas ar grupām, lai pēc tam varētu
	// atsevišķi atlasīt grupu id
	$group_notifs = array(
		3, 8, 13, 14
	);
	
	$user_notifications = $db->get_results("
		SELECT * FROM `notify` 
		WHERE 
			`user_id` = ".$auth->id." AND
			`lang` = ".$api_lang."
		ORDER BY `bump` DESC 
		LIMIT 0, $notif_limit
	");
	
	if (!$user_notifications) {
		api_error('Nav paziņojumu.');
	} else {
	
		// atlasīs miniblogu id no tām notifikācijām, kas ir ierakstiem grupās
		$mb_ids = array();
		foreach ($user_notifications as $notify) {
			if (in_array($notify->type, $group_notifs)) {
				$mb_ids[] = (int)$notify->foreign_key;
			}
		}
		
		// šiem miniblogiem noteiks grupu ID, kas jānodod tālāk atbildē
		$group_ids = array();
		if (!empty($mb_ids)) {
		
			$ids = $db->get_results("
				SELECT `id`, `groupid` FROM `miniblog`
				WHERE `id` IN(".implode(',', $mb_ids).")
			");
			if ($ids) {
				foreach ($ids as $entry) {
					$group_ids[$entry->id] = $entry->groupid;
				}
			}
		}

		// sagatavos atbildi lietotnei
		foreach ($user_notifications as $notify) {
		
			// noteiks pareizu notifikācijas grupas id
			$group_id = 0;
			if (in_array($notify->type, $group_notifs) &&
					!empty($group_ids[$notify->foreign_key])) {
				$group_id = $group_ids[$notify->foreign_key];
			}
		
			$arr_notifs[] = array(
				'id' => (int)$notify->id,
				'type' => (int)$notify->type,
				'group_id' => (int)$group_id,
				'foreign_key' => (int)$notify->foreign_key,
				'text' => textlimit(trim($notify->info), 45, ''),
				'date' => 'pirms ' . time_ago(strtotime($notify->bump))
			);
		}
		
		api_append(array('notifications' => $arr_notifs));
	}

/**
 *  Šo informāciju lietotne fonā pieprasīs samērā bieži, lai varētu izziņot
 *  jaunākās notifikācijas, parādīt nelasīto vēstuļu skaitu utt.
 *
 *  /status/{last_bump}
 */
} else if ($var1 == 'status') {

	// pēdējās redzētās notifikācijas laiks sekundēs
	$last_bump = 0;
	if (!empty($var2)) {
		$last_bump = (int)$var2;
	}

	// noteiks vēl neredzēto notifikāciju skaitu
	$user_notifications = $db->get_results("
		SELECT `bump` FROM `notify` 
		WHERE 
			`user_id` = ".$auth->id." AND
			`lang` = ".$api_lang." AND
			`bump` > '".date('Y-m-d H:i:s', $last_bump)."'
		ORDER BY `bump` DESC 
		LIMIT 0, 25
	");
	
	$unseen_notifs = 0;
	$latest_bump = 0;
	
	if ($user_notifications) {
		foreach ($user_notifications as $notif) {
			if ($latest_bump == 0) {
				$latest_bump = strtotime($notif->bump);
			}
			$unseen_notifs++;
		}
	}
	
	if ($latest_bump == 0) {
		$latest_bump = $last_bump;
	}
	
	// nelasīto vēstuļu skaits
	$inbox = $db->get_var("
		SELECT count(*) FROM `pm` WHERE `to_uid` = ".$auth->id." AND `is_read` = 0
	");
	
	api_append(array('numbers' => array(
		'users_online' => (int)$auth->hosts_online,
		'inbox_unread' => (int)$inbox,
		'notifs_new' => (int)$unseen_notifs,
		'bump_time' => $latest_bump
	)));
 
/**
 *  Atgriezīs sarakstu ar tiešsaistē esošiem lietotājiem.
 */
} else if ($var1 === 'online') {
	set_action('tiešsaistē esošo lietotāju sarakstu');
	api_fetch_online();
	
/**
 *  Atgriezīs ar lietotāja profilu saistītu informāciju.
 *  /random/profile/{user_id}
 */
} else if ($var1 === 'profile' && !empty($var2)) {

	$user_id = (int)$var2;
	
	$profile = $db->get_row("
		SELECT * FROM `users` WHERE `id` = ".$user_id
	);
	if (!$profile) {
		api_error('Šāds profils neeksistē.');
	} else {
	
		// skatot cita lietotāja profilu, skatījums jāatzīmē
		if ($auth->id != $profile->id && $auth->level != 5) {

			$date = time();
			$viewed = $db->get_var("
				SELECT `id` FROM `viewprofile`
				WHERE 
					`profile` = ".$profile->id." AND
					`viewer` = ".$auth->id." AND
					`time` > '".($date - 3600)."'"
			);
			if (!$viewed) {
				$db->insert('viewprofile', array(
					'profile' => $profile->id,
					'viewer' => $auth->id,
					'time' => sanitize($date)
				));
			} else {
				$db->update('viewprofile', $viewed, array('time' => $date));
			}
		}
		
		$user_nick = (empty($profile->nick)) ? '<i>dzēsts</i>' : $profile->nick;
		set_action($user_nick.' profilu');
	
		// komentāru kopskaits dažādās tabulās
		$posts = ($db->get_var("SELECT count(*) FROM `comments` WHERE `author` = ".$user_id." AND `removed` = 0") +
				  $db->get_var("SELECT count(*) FROM `galcom` WHERE `author` = ".$user_id." AND `removed` = 0") +
				  $db->get_var("SELECT count(*) FROM `miniblog` WHERE `author` = ".$user_id." AND removed = 0"));

		// lietotāja rakstu skaits atvērtajā apakšprojektā
		$user_pages = $db->get_var("SELECT count(*) FROM pages WHERE `author` = ".$user_id." AND `lang` = ".$api_lang);
		
		// kā citi lietotāji vērtējuši šī lietotāja ierakstus
		$voteval = $db->get_var("SELECT SUM(`vote_value`) FROM `comments` WHERE `author` = ".$user_id) +
				   $db->get_var("SELECT SUM(`vote_value`) FROM `galcom` WHERE `author` = ".$user_id) +
				   $db->get_var("SELECT SUM(`vote_value`) FROM `miniblog` WHERE `author` = ".$user_id);

		// reģistrējās pirms x dienām
		$days = ceil((time() - strtotime($profile->date)) / 60 / 60 / 24);
		
		// pēdējoreiz redzēts pirms...
		$time_ago = time_ago(strtotime($profile->lastseen));
		
        $data = api_fetch_user($profile->id, $profile->nick, $profile->level, true);
		$data += array(
			'days_online' => (int)$profile->days_in_row,
			'days_registered' => (int)$days,
			'last_seen' => 'pirms '.$time_ago,
			'usertitle' => $profile->custom_title,
			'gender' => (int)$profile->gender,
			'web' => $profile->web,
			'karma' => (int)$profile->karma,
			'post_count' => (int)$posts,
			'page_count' => (int)$user_pages,
			'self_votes_count' => (int)$profile->vote_total,
			'self_votes_sum' => (int)$profile->vote_others,
			'other_votes_sum' => (int)$voteval
		);
		
		// moderatoriem redzama papildinformācija par lietotāju
		/*if (im_mod()) {
			$data['email'] = $profile->mail;
			$data['last_ip'] = $profile->lastip;
			$data['useragent'] = $profile->user_agent;        
		}*/
		
		api_append(array('profile' => $data));
		
		// pievienos klāt arī lietotāja pāris jaunākos apbalvojumus
		api_fetch_awards($user_id, 6);
	}

/**
 *  Atgriezīs sarakstu ar lietotāja "draugiem".
 */
} else if ($var1 === 'friends') {

	$contacts = $db->get_results("
		SELECT 
			`friend1`, `friend2`
		FROM `friends`
			JOIN `users` ON (
				`users`.`id` = CASE WHEN `friend1` = ".$auth->id." THEN `friend2` ELSE `friend1` END AND
				`users`.`deleted` = 0
			)
		WHERE 
			(`friend1` = (".$auth->id.") OR `friend2` = (".$auth->id.")) AND
			`confirmed` = 1
		ORDER BY `users`.`nick` ASC
	");
	
	$friends = array();
	$cnt_friends = 0;
	
	if ($contacts) {    
		foreach ($contacts as $contact) {
		
			if ($contact->friend1 == $auth->id) {
				$the_other = $contact->friend2;
			} else {
				$the_other = $contact->friend1;
			}
			
			$info = get_user($the_other);
			if ($info) {
				if ($info->deleted) {
					$info->nick = '<em>dzēsts</em>';
				}
				
				// lietotnē ir dropdowns, kuros lietotājvārdus neizkrāsos ar stiliem
				if ($var2 === 'simple') {
					$friends[] = array(
						'id' => (int)$info->id,
						'nick' => $info->nick
					);
				} else {
					$friends[] = api_fetch_user($info->id, $info->nick, $info->level);
				}
			}
			
			$cnt_friends++;
		}
	}
	
	api_append(array(
		'count' => (int)$cnt_friends,
		'contacts' => $friends
	));

/**
 *  Citas situācijas.
 */
} else {
    api_log('Sasniegts random moduļa "else" bloks.');
    api_error('hellou... are thou lost?');
}
