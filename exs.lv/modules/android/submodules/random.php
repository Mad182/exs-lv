<?php
/**
 *  Apstrādā random Android lietotnes pieprasījumus, 
 *  kurus nav vērts iedalīt kādā specifiskā apakšgrupā.
 */

// nebūs iespējams skatīt failu pa tiešo
!isset($sub_include) and die('Error loading page!');

$var1 = (!empty($_GET['var1'])) ? $_GET['var1'] : '';
$var2 = (!empty($_GET['var2'])) ? $_GET['var2'] : '';
$var3 = (!empty($_GET['var3'])) ? $_GET['var3'] : '';

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
			`lang` = ".$android_lang."
		ORDER BY `bump` DESC 
		LIMIT 0, $notif_limit
	");
	
	if (!$user_notifications) {
		a_error('Nav paziņojumu');
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
				'date' => time_ago(strtotime($notify->bump))
			);
		}
		
		a_append(array('notifications' => $arr_notifs));
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
			`lang` = ".$android_lang." AND
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
	
	a_append(array('numbers' => array(
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
	a_fetch_online();
	
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
		a_error('Šāds profils neeksistē');
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
		$user_pages = $db->get_var("SELECT count(*) FROM pages WHERE `author` = ".$user_id." AND `lang` = ".$android_lang);
		
		// kā citi lietotāji vērtējuši šī lietotāja ierakstus
		$voteval = $db->get_var("SELECT SUM(`vote_value`) FROM `comments` WHERE `author` = ".$user_id) +
				   $db->get_var("SELECT SUM(`vote_value`) FROM `galcom` WHERE `author` = ".$user_id) +
				   $db->get_var("SELECT SUM(`vote_value`) FROM `miniblog` WHERE `author` = ".$user_id);

		// reģistrējās pirms x dienām
		$days = ceil((time() - strtotime($profile->date)) / 60 / 60 / 24);
		
		// pēdējoreiz redzēts pirms...
		$time_ago = time_ago(strtotime($profile->lastseen));
		
		$data = array(
			'formatted' => a_fetch_user($profile->id, $profile->nick, $profile->level),
			'avatar' => 'https://img.exs.lv/userpic/large/'.$profile->avatar,
			'days_online' => $profile->days_in_row.' '.lv_dsk($profile->days_in_row, 'dienu', 'dienas'),
			'days_registered' => $days.' '.lv_dsk($profile->days_in_row, 'dienu', 'dienas'),
			'last_seen' => 'pirms '.$time_ago,
			'usertitle' => $profile->custom_title,
			'gender' => (int)$profile->gender,
			'web' => $profile->web,
			'karma' => (int)$profile->karma,
			'posts' => (int)$posts,
			'pages' => (int)$user_pages,
			'voted_by_self_cnt' => (int)$profile->vote_total,
			'voted_by_self_sum' => (int)$profile->vote_others,
			'voted_by_others' => (int)$voteval
		);
		
		// moderatoriem redzama papildinformācija par lietotāju
		/*if (im_mod()) {
			$data['email'] = $profile->mail;
			$data['last_ip'] = $profile->lastip;
			$data['useragent'] = $profile->user_agent;        
		}*/
		
		a_append(array('userdata' => $data));
		
		// pievienos klāt arī lietotāja pāris jaunākos apbalvojumus
		a_fetch_awards($user_id, 6);
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
					$friends[] = a_fetch_user($info->id, $info->nick, $info->level);
				}
			}
			
			$cnt_friends++;
		}
	}
	
	a_append(array(
		'count' => (int)$cnt_friends,
		'contacts' => $friends
	));
	
/**
 *  Atgriezīs sarakstu ar visām grupām, kurām lietotājs ir pieteicies.
 */
} else if ($var1 === 'mygroups') {

	set_action('jaunāko grupās');

	// grupas, kurās lietotājs ir admins
	$own_groups = $db->get_results("
		SELECT `id`, `title`, `avatar`, `owner_seenposts`, `posts`, `members`
		FROM `clans`
		WHERE 
			`owner` = ".(int)$auth->id." AND
			`lang` = ".(int)$android_lang." 
		ORDER BY `title` ASC
	");
	
	// pārējās grupas, kurām lietotājs ir pieteicies
	$member_of = $db->get_results("
		SELECT
			`clans`.`id`,
			`clans`.`posts`,
			`clans`.`avatar`,
			`clans`.`title`,
			`clans`.`members`,
			`clans_members`.`moderator`,
			`clans_members`.`seenposts`
		FROM `clans_members`
			JOIN `clans` ON (
				`clans_members`.`clan` = `clans`.`id` AND
				`clans`.`lang` = ".(int)$android_lang."
			)
		WHERE 
			`clans_members`.`user` = ".(int)$auth->id." AND
			`clans_members`.`approve` = 1
		ORDER BY 
			`clans_members`.`moderator` DESC, 
			`clans`.`title` ASC
	");
	
	if (!$own_groups && !$member_of) {
		a_error('Neesi pieteicies nevienai grupai');
	} else {
	
		$groups = array();
		$group_count = 0;
		
		if ($own_groups) {
			foreach ($own_groups as $group) {
				$groups[] = array(
					'id' => (int)$group->id,
					'av_url' => 'https://img.exs.lv/userpic/medium/'.$group->avatar,
					'title' => $group->title,
					'members' => (int)$group->members,
					'posts' => (int)$group->posts,
					'in_group' => true,
					'is_admin' => true,
					'is_mod' => false,
					'unread_msgs' => (int)($group->posts - $group->owner_seenposts)
				);
				$group_count++;
			}
		}
		
		if ($member_of) {
			foreach ($member_of as $group) {
				$groups[] = array(
					'id' => (int)$group->id,
					'av_url' => 'https://img.exs.lv/userpic/medium/'.$group->avatar,
					'title' => $group->title,
					'members' => (int)$group->members,
					'posts' => (int)$group->posts,
					'in_group' => true,
					'is_admin' => false,
					'is_mod' => (bool)($group->moderator ? true : false),
					'unread_msgs' => (int)($group->posts - $group->seenposts)
				);
				$group_count++;
			}
		}

		a_append(array(
			'group_count' => $group_count++,
			'groups' => $groups
		));
	}

/**
 *  Atgriezīs sarakstu ar grupu kategorijām.
 */
} else if ($var1 === 'gcategories') {

	set_action('grupu sarakstu');

	$categories = $db->get_results("
		SELECT 
			`clans_categories`.`id`, 
			`clans_categories`.`title`,
			count(*) AS `clan_count`
		FROM `clans_categories`
			JOIN `clans` ON (
				`clans_categories`.`id` = `clans`.`category_id` AND
				`clans`.`lang` = ".$android_lang."
			)
		GROUP BY `clans`.`category_id`
		ORDER BY 
			`clans_categories`.`title` ASC
	");
	
	if (!$categories) {
		a_error('Nav nevienas grupu kategorijas!');
	} else {
	
		$data = array();
		$groups_total = 0;
		
		foreach ($categories as $group_cat) {
			$data[] = array(
				'id' => (int)$group_cat->id,
				'title' => $group_cat->title,
				'group_count' => (int)$group_cat->clan_count
			);
			$groups_total += $group_cat->clan_count;
		}
		
		$json_page = array(
			'group_count' => (int)$groups_total,
			'group_categories' => $data
		);
	}

/**
 *  Atgriezīs norādītajā kategorijā ietilpstošās grupas.
 */
} else if ($var1 === 'groups' && !empty($var2)) {

	set_action('grupu sarakstu');

	$cat_id = (int)$var2;
	
	$get_cat = $db->get_row("
		SELECT `id`, `title` FROM `clans_categories` WHERE `id` = ".$cat_id
	);

	if (!$get_cat) {
		a_error('Kļūdaini norādīta sadaļa');
	} else {
	
		$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;        
		if ($page < 1) {
			$page = 1;
		}

		$amount = 20; // vienā lapā atgriežamo grupu skaits
		$limit = ($page - 1) * $amount;
	
		$groups = $db->get_results("
			SELECT 
				`clans`.`id`, `clans`.`title`, `clans`.`avatar`,
				`clans`.`owner`, `clans`.`members`, `clans`.`posts`,
				
				IFNULL(`clans_members`.`moderator`, '-1') AS `is_moderator`,
				`clans_members`.`seenposts` AS `posts_seen`
			FROM `clans`
				LEFT JOIN `clans_members` ON (
					`clans`.`id` = `clans_members`.`clan` AND
					`clans_members`.`user` = ".(int)$auth->id." AND
					`clans_members`.`approve` = 1
				)
				LEFT JOIN `users` AS `member` ON (
					`clans_members`.`user` = `member`.`id` AND
					`member`.`deleted` = 0
				)
			WHERE 
				`lang` = ".(int)$android_lang." AND
				`category_id` = ".(int)$get_cat->id." 
			ORDER BY `title` ASC
			LIMIT ".$limit.", ".$amount."
		");
		
		if (!$groups) {
			$json_page = array(
				'cat_id' => (int)$get_cat->id,
				'cat_title' => $get_cat->title,
				'groups' => array()
			);           
		} else {
	
			$data = array();

			foreach ($groups as $group) {
				
				// jāpārbauda, vai lietotājs ir šajā grupā, lai lietotnē
				// to varētu izcelt, norādot arī nelasīto ziņu skaitu
				$in_group = false;
				$is_moderator = false;
				$unread_msgs = 0;
				
				if ($group->is_moderator != '-1') {
					$in_group = true;
					$is_moderator = (bool)$group->is_moderator;
					$unread_msgs = (int)($group->posts - $group->posts_seen);
				}
			
				$data[] = array(
					'id' => (int)$group->id,
					'av_url' => 'https://img.exs.lv/userpic/medium/'.$group->avatar,
					'title' => $group->title,
					'members' => (int)$group->members,
					'posts' => (int)$group->posts,
					'in_group' => $in_group,
					'is_admin' => false,
					'is_mod' => $is_moderator,
					'unread_msgs' => $unread_msgs
				);
			}
			
			a_append(array(
				'cat_id' => (int)$get_cat->id,
				'cat_title' => $get_cat->title,
				'groups' => $data
			));
		}
	}

/**
 *  Citas situācijas.
 */
} else {
	a_error('Kļūdains pieprasījums (#3)');
	a_log('Kļūdains pieprasījums random modulī');
}
