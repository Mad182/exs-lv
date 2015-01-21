<?php
/**
 *  Lietotāja profila apakšmodulis
 *
 *  Ļauj pārskatīt lietotāja profilu liegumus un to statusu.
 */
if (!isset($auth)) { // ja .php failu atver pa tiešo
	die('error');
}

if (!isset($_GET['var2']) || $_GET['var2'] != 'block' || !im_mod()) {
	set_flash('Pieeja liegta!');
	redirect();
	exit;
} else if ($inprofile->level == 1 || ($inprofile->level == 2 && $auth->level != 1)) {
	set_flash('Pieeja liegta!');
	redirect();
	exit;
}

// iespējamie bloķēšanas termiņi sekundēs
$ban_lengths = array(
	21600 => '6 stundas',
	86400 => '1 diena',
	259200 => '3 dienas',
	604800 => '1 nedēļa',
	1209600 => '2 nedēļas',
	1814400 => '3 nedēļas',
	2629743 => '1 mēnesis',
	5184000 => '2 mēneši',
	7889231 => '3 mēneši',
	15552000 => '6 mēneši',
	31556926 => '1 gads'
);

// nosaka, vai lietotājam ir aktīvs liegums uz atvērtā profila,
// lai zinātu, vai rādīt tā bloķēšanas formu
$is_banned = false;
if (in_array($inprofile->id, $busers)) {
	$find_ban = $db->get_row("
		SELECT * FROM `banned` 
		WHERE 
			`user_id` = ".$inprofile->id." AND
			`active` = 1 AND
			(`lang` = 0 OR `lang` = '$lang') 
		ORDER BY `time` DESC
		LIMIT 1
	");
	if ($find_ban) {
		$is_banned = true;
	}
}
 
// jāzina brīdinājumu skaits, lai varētu piedāvāt noteiktu skaitu 
// noņemt reizē ar lieguma uzlikšanu atvērtajam profilam
$warn_cnt = $db->get_var("
	SELECT count(*) FROM `warns`
	WHERE
		`warns`.`user_id`   = " . $inprofile->id . " AND
		`warns`.`active`    = 1 AND
		`warns`.`site_id`   = " . $lang
);

// ja lietotājs ir kādā no profilu grupām, 
// jāpiedāvā iespēja bloķēt arī pārējos profilus
$has_profiles = false;
$main_profile = 0; // jāvar id norādīt adresē pie profilu formas
$is_in_group = $db->get_row("
	SELECT 
		`users_groups`.`id`, 
		`users_groups`.`user_id`, 
		`users_groups`.`parent_id`,
		`parent`.`user_id` AS `parent_user_id`
	FROM `users_groups`
		LEFT JOIN `users_groups` AS `parent` ON (
			`users_groups`.`parent_id` = `parent`.`id`
		)
	WHERE 
		`users_groups`.`deleted_by` = 0 AND 
		`users_groups`.`user_id` = ".(int)$inprofile->id."
");
if ($is_in_group) {

	if ($is_in_group->parent_id == 0) { // šis ir grupas main profils
		$sql = '`users_groups`.`parent_id` = '.(int)$is_in_group->id;
		$main_profile = $is_in_group->user_id;
	} else {
		$sql = '(`users_groups`.`parent_id` = '.(int)$is_in_group->parent_id.' OR `users_groups`.`id` = '.(int)$is_in_group->parent_id.')';
		$main_profile = $is_in_group->parent_user_id;
	}

	// katram profilam pie reizes atgriež lieguma termiņu, ja
	// profilam ir aktīvs liegums tabulā
	$other_profiles = $db->get_results("
		SELECT
			`users`.`id`,
			`users`.`nick`,
			`users`.`lastseen`,
			`users`.`lastip`,
			`users`.`level`,
			`users`.`warn_count`
		FROM `users_groups`
			JOIN `users` ON (
				`users_groups`.`user_id` = `users`.`id` AND
				`users`.`deleted` = 0 AND
				`users`.`level` NOT IN(1,2)
			)
		WHERE
			`users_groups`.`deleted_by` = 0 AND
			".$sql." AND
			`users_groups`.`id` != ".$is_in_group->id."
		ORDER BY `users`.`nick` ASC
	");

	if ($other_profiles) {
		$has_profiles = true;
	}
}


/**
 *  Iesniegti atvērtā profila bloķēšanas dati
 */
if (isset($_POST['block-reason'])) {

	$reason = sanitize(h($_POST['block-reason']));
	$length = (isset($_POST['block-length'])) ? (int)$_POST['block-length'] : 0;
	if ($length < 1) {
		$length = 259200; // 3 diennaktis
	} else if ($length > 31556926) {
        $length = 31556926; // 1 gads
    }

	/**
	 * Ja admins nav "globāls", t.i. norādīts sub-exa konfigurācijā, bans attiecas tikai uz to lapu.
	 * Globālie admini var izvēlēties domēnu, vai visus domēnus (0)
	 */
	if (in_array($auth->id, $site_access[1]) || in_array($auth->id, $site_access[2])) {
		$site = $lang;
	} else {
		$site = (isset($_POST['block-domain'])) ? (int)$_POST['block-domain'] : 0;
		if ($site < 0) {
			$site = $lang;
		}
	}

	$values = array(
		'user_id' => $inprofile->id,
		'reason' => $reason,
		'time' => time(),
		'length' => $length,
		'author' => $auth->id,
		'ip' => sanitize($inprofile->lastip),
		'lang' => $site
	);
	$insert = $db->insert('banned', $values);
	if (!$insert) {
		set_flash('Bloķēt lietotāju neizdevās!');
		redirect('/user/'.$inprofile->id.'/block');
	}
	$auth->log('Bloķēja lietotāju', 'users', $inprofile->id);
	get_banlist(true);

	// pārbauda, vai nav nepieciešams noņemt aktīvos brīdinājumus
	if ($warn_cnt > 0 && isset($_POST['warn-removal-reason']) && isset($_POST['warn-removal'])) {

		$removal_reason = post2db($_POST['warn-removal-reason']);
		$remove_count = (int) $_POST['warn-removal'];
		$remove_count = ($remove_count > $warn_cnt || $remove_count < 0) ? 0 : $remove_count;

		if ($remove_count > 0) {

			// atlasa visu noņemamo brīdinājumu ids
			$get_ids = $db->get_results("
				SELECT `id` FROM `warns`
				WHERE `user_id` = ".$inprofile->id." AND `active` = 1 AND `site_id` = ".$lang."
				ORDER BY `created` ASC
				LIMIT ".$remove_count."
			");
			if ($get_ids) {
				foreach ($get_ids as $single_id) {
					$ids[] = $single_id->id;
				}
				// noņem visus norādītos brīdinājumus
				if (!empty($ids)) {
					$db->query("
						UPDATE `warns`
						SET
							`warns`.`active`        = 0,
							`warns`.`removed`       = NOW(),
							`warns`.`removed_by`    = $auth->id,
							`warns`.`remove_reason` = '$removal_reason'
						WHERE `warns`.`id` IN(" . implode(',', $ids) . ")
					");
					$auth->log('Atbrīvoja '.count($ids).' vārnas', 'users', $inprofile->id);
				}
			}
		}
	}

	// piesaistīto profilu bloķēšana redzama tikai galvenajā exs,
	// tāpēc tikai tajā ir vērts atgriezties uz to pašu lapu
	if ($lang == 1) {
		redirect('/user/'.(int)$_GET['var1'].'/block');
	} else {
		redirect('/banned');
	}
}


/**
 *  Iesniegti piesaistīto profilu bloķēšanas dati
 */
if (isset($_GET['var3']) && $_GET['var3'] == 'other' && isset($_POST['reason-2'])) {

	$reason = (isset($_POST['reason-2'])) ? input2db($_POST['reason-2'], 1000) : '-';
	$length = (isset($_POST['length-2'])) ? (int)$_POST['length-2'] : 0;
	if ($length < 21600) { // 6h
		$length = 21600;
	} else if ($length > 31556926) {
        $length = 31556926; // 1 gads
    }
	$domain = (isset($_POST['domain-2'])) ? (int)$_POST['domain-2'] : 0;
	if ($domain < 0 || $domain > count($config_domains)) {
		$domain = 0;
	}

	$cnt_not_banned = 0;
	$cnt_banned = 0;
	
	if ($other_profiles) {
		foreach ($other_profiles as $profile) {
		
			// ja checkbox nav atzīmēts, profila sodu nealterē
			if (isset($_POST['block-'.$profile->id])) {
		
				// atkarībā no tā, vai profilam jau ir aktīvs liegums,
				// tādu vainu izveido, vai arī pamaina tā termiņu un iemeslu
				$ban_data = $db->get_row("
					SELECT
						`banned`.`id`,
						`banned`.`reason`,
						`banned`.`length`, 
						`banned`.`time`
					FROM `banned` 
					WHERE 
						`banned`.`user_id` = ".(int)$profile->id." AND
						`banned`.`active` = 1 AND
						(`banned`.`lang` = 0 OR `banned`.`lang` = ".(int)$lang.") 
					ORDER BY `banned`.`time` DESC
					LIMIT 0, 1
				");
				
				// pielīdzina laiku atvērtā profila liegumam, ja tāds ir
				$ban_time = ($is_banned) ? $find_ban->time : time();
				
				// nav vērts bloķēt, ja laiks jau pagājis
				if ($ban_time + $length > time()) {
				
					// liegums jau ir
					if ($ban_data) {
						
						$db->query("
							UPDATE `banned`
							SET
								`reason` = '".$reason."',
								`time` = '".sanitize($ban_time)."',
								`length` = '".$length."',
								`author` = ".(int)$auth->id.",
								`lang` = ".$domain."
							WHERE `id` = ".(int)$ban_data->id."
						");
					
					// lieguma vēl nav
					} else {
					
						$data = array(
							'user_id' => $profile->id,
							'reason' => $reason,
							'time' => sanitize($ban_time),
							'length' => $length,
							'author' => (int)$auth->id,
							'ip' => sanitize($profile->lastip),
							'lang' => $domain
						);
						$db->insert('banned', $data);
					}
					
					$cnt_banned++;
					
				} else {
					$cnt_not_banned++;
				}
			}
			
			if ($cnt_not_banned > 0) {
				set_flash('Kāds no profiliem netika bloķēts, jo tā lieguma sākuma laiks + izvēlētais termiņš jau pagājis!');
				get_banlist(true);
				redirect('/user/'.$inprofile->id.'/block');
			}
		}
		
		$auth->log('Bloķēja '.$cnt_banned.' piesaistītos profilus', 'users', $inprofile->id);
	}
	
	get_banlist(true);
	
	redirect('/user/'.$inprofile->id.'/block#profiles');
}


/**
 *  Atvērtā profila bloķēšanas forma
 */
$tpl->newBlock('user-profile-block');

if ($is_banned) {

	$usr = get_user($find_ban->author);
	$usr_nick = usercolor($usr->nick, $usr->level, false, $usr->id);

	$tpl->newBlock('has-active-ban');
	$tpl->assign(array(
		'reason' => $find_ban->reason,
		'from' => date('d.m.Y, H:i', $find_ban->time),
		'until' => date('d.m.Y, H:i', $find_ban->time + $find_ban->length),
		'author' => $usr_nick,
		'id' => $usr->id
	));
} else {
	$tpl->newBlock('ban-form');
	
	foreach ($ban_lengths as $key => $value) {
		$tpl->newBlock('ban-length');
		$tpl->assign(array(
			'length' => $key,
			'title' => $value
		));
		if ($key == 259200) {
			$tpl->assign('selected', ' selected="selected"');
		}
	}

	if (!$warn_cnt) {
		$tpl->newBlock('no-active-warns');
	} else {
		$tpl->newBlock('warn-removal');
		for ($i = 1; $i <= $warn_cnt; $i++) {
			$tpl->newBlock('warn-removal-option');
			$tpl->assign('x', $i);
		}
	}
	
	// globālajiem modiem rāda domēnu izvēli
	if (!in_array($auth->id, $site_access[1]) && !in_array($auth->id, $site_access[2])) {
		
		$tpl->newBlock('block-domain');

		foreach ($config_domains as $key => $domain) {
			if ($domain['domain'] !== 'secure.exs.lv' && $domain['domain'] !== 'android.exs.lv') {
				$tpl->newBlock('block-domain-node');
				$tpl->assign(array(
					'id' => $key,
					'domain' => $domain['domain']
				));
			}
		}
	}
}

/**
 *  Piesaistīto profilu bloķēšanas forma
 *
 *  Pagaidām citus piesaistītos profilus rādīsim tikai galvenajā un rs.exs,
 *  izlaižot apakšprojektus un to spec. modus.
 */
if ($lang == 1 || $lang == 9) {
	
	$tpl->newBlock('form-other-profiles');    

	if (!$has_profiles) {
		$tpl->newBlock('no-other-profiles');
	} else {
	
		$tpl->newBlock('has-other-profiles');
		if ($is_banned) {
			$tpl->assign('ban-start-time', date('d.m.Y, H:i:s', $find_ban->time).' (sakrīt ar atvērtā profila lieguma laiku)');
		} else {
			$tpl->assign('ban-start-time', date('d.m.Y, H:i', time()));
		}

		if ($is_banned) {
			$tpl->assign(array(
				'reason' => $find_ban->reason
			));
		}        
		
		// bana termiņu izvēlne
		foreach ($ban_lengths as $key => $value) {
			$tpl->newBlock('ban-length-2');
			$tpl->assign(array(
				'length' => $key,
				'title' => $value
			));
			if ($is_banned && $find_ban->length == $key) {
				$tpl->assign('selected', ' selected="selected"');
			}
		}
		
		// globālajiem modiem rāda domēnu izvēli
		if (!in_array($auth->id, $site_access[1]) && !in_array($auth->id, $site_access[2])) {
			
			$tpl->newBlock('block-domain-2');

			foreach ($config_domains as $key => $domain) {
				if ($domain['domain'] !== 'secure.exs.lv' && $domain['domain'] !== 'android.exs.lv') {
					$tpl->newBlock('block-domain-node-2');
					$tpl->assign(array(
						'id' => $key,
						'domain' => $domain['domain']
					));
					if ($is_banned && $key == $find_ban->lang) {
						$tpl->assign('selected', ' selected="selected"');
					}
				}
			}
		}
		
		// runescape.exs.lv profilu sasaistes sadaļa nav pieejama
		if ($lang == 1) {
			$tpl->newBlock('goto-group');
			$tpl->assign('group-parent', $main_profile);
		}

		$cnt_checked = 0;

		foreach ($other_profiles as $single) {
		
			// nepieciešams aprēķināt atlikušo bana laiku
			$ban_data = $db->get_row("
				SELECT
					`banned`.`length`, 
					`banned`.`time`
				FROM `banned` 
				WHERE 
					`banned`.`user_id` = ".(int)$single->id." AND
					`banned`.`active` = 1 AND
					(`banned`.`lang` = 0 OR `banned`.`lang` = '$lang') 
				ORDER BY `banned`.`time` DESC
				LIMIT 0, 1
			");
			if ($ban_data) {
				$time_left = $ban_data->time + $ban_data->length - time();
				if ($time_left > 0) {
					$single->time_left = strTime($time_left);
				} else {                
					$single->time_left = '-';
				}
				$single->checked = '';
			} else {
				$single->time_left = '-';
				$single->checked = ' checked="checked"';
				$cnt_checked++;
			}
			
			$single->lastseen = date('d.m.y, H:i', strtotime($single->lastseen));
			$single->nick = usercolor($single->nick, $single->level, false, $single->id);
			$single->warns = $single->warn_count;
		
			$tpl->newBlock('other-profile');
			$tpl->assignAll($single);
		}
		
		// augšējais checkbox būs atzīmēts tad, 
		// ja atzīmēts būs vismaz viens no pārējiem
		if ($cnt_checked > 0) {
			$tpl->gotoBlock('has-other-profiles');
			$tpl->assign('top-checked', ' checked="checked"');
		}
	}
}

$page_title = 'Bloķēt lietotāju &quot;' . $inprofile->nick . '&quot;';
