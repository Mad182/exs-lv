<?php
/**
 *  iOS lietotāju profilu apakšmodulis.
 *
 *  Apstrādā pieprasījumus saistībā ar lietotāju profiliem.
 *
 *  Adrese: ios.exs.lv/profiles/
 */


/**
 *  Atgriezīs ar lietotāja profilu saistītu informāciju.
 *  /profiles/fetch/{user_id}
 */
if ($var1 === 'fetch' && !empty($var2)) {

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
}

/**
 *  Citas situācijas.
 */
else {
    api_log('Sasniegts profilu moduļa "else" bloks.');
    api_error('hellou... are thou lost?');
}
