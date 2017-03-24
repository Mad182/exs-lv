<?php
/**
 *  iOS grupu apakšmodulis.
 *
 *  Apstrādā pieprasījumus saistībā ar darbībām grupās.
 */

require API_PATH.'/shared/ios.miniblogs.php';


/**
 *  Grupas minibloga vērtēšana ar plusu vai mīnusu.
 *  (/groups/{plus|minus}/{entry_id})
 */
if (!empty($var1) && !empty($var2) &&
	in_array($var1, array('plus', 'minus'))) {

	api_rate_comment($var2, ($var1 === 'plus'));

/**
 *  Atgriezīs sarakstu ar grupas jaunākajiem miniblogiem.
 *  (/groups/{group_id}/getminiblogs)
 */
} else if (!empty($var1) && $var2 === 'getminiblogs') {
	set_action('grupas miniblogus');
	api_fetch_miniblogs($var1);

/**
 *  Atgriezīs grupas minibloga saturu.
 *  (/groups/getcontent/{miniblog_id})
 */
} else if ($var1 === 'getminiblog' && !empty($var2)) {
	set_action('grupas miniblogu');
	api_fetch_miniblog($var2);

/**
 *  Jauna grupas minibloga pievienošana vai esoša minibloga komentēšana.
 *  (/groups/{new|comment})
 */
} else if ($var1 === 'new' || $var1 === 'comment') {
	
	if (!isset($_POST['group_id']) || !isset($_POST['parent_id']) ||
		!isset($_POST['mb_text'])) {
		api_error('Kļūdains pieprasījums.');
		if ($var1 === 'new') {
			api_log('Netika iesniegti grupas minibloga ieraksta pievienošanas dati.');
		} else {
			api_log('Netika iesniegti grupas minibloga komentēšanas dati.');
		}
	} else {
		api_add_miniblog(array(
			'group_id' => $_POST['group_id'],
			'parent_id' => $_POST['parent_id'],
			'is_private' => false,
			'mb_text' => $_POST['mb_text']
		));
	}
    
/**
 *  Atgriezīs sarakstu ar visām grupām, kurām lietotājs ir pieteicies.
 */
} else if ($var1 === 'group_news') {

	set_action('jaunāko grupās');

	// grupas, kurās lietotājs ir admins
	$own_groups = $db->get_results("
		SELECT `id`, `title`, `avatar`, `owner_seenposts`, `posts`, `members`
		FROM `clans`
		WHERE 
			`owner` = ".(int)$auth->id." AND
			`lang` = ".(int)$api_lang." 
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
				`clans`.`lang` = ".(int)$api_lang."
			)
		WHERE 
			`clans_members`.`user` = ".(int)$auth->id." AND
			`clans_members`.`approve` = 1
		ORDER BY 
			`clans_members`.`moderator` DESC, 
			`clans`.`title` ASC
	");
	
	if (!$own_groups && !$member_of) {
		api_error('Neesi pieteicies nevienai grupai.');
	} else {
	
		$groups = array();
		$group_count = 0;
		
		if ($own_groups) {
			foreach ($own_groups as $group) {
				$groups[] = array(
					'id' => (int)$group->id,
					'avatar_url' => 'https://img.exs.lv/userpic/medium/'.$group->avatar,
					'title' => $group->title,
					'member_count' => (int)$group->members,
					'post_count' => (int)$group->posts,
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
					'avatar_url' => 'https://img.exs.lv/userpic/medium/'.$group->avatar,
					'title' => $group->title,
					'member_count' => (int)$group->members,
					'post_count' => (int)$group->posts,
					'is_admin' => false,
					'is_mod' => (bool)($group->moderator ? true : false),
					'unread_msgs' => (int)($group->posts - $group->seenposts)
				);
				$group_count++;
			}
		}

		api_append(array(
			'group_count' => $group_count++,
			'groups' => $groups
		));
	}
    
/**
 *  Atgriezīs sarakstu ar grupu kategorijām.
 */
} else if ($var1 === 'categories') {

	set_action('grupu sarakstu');

	$categories = $db->get_results("
		SELECT 
			`clans_categories`.`id`, 
			`clans_categories`.`title`,
			count(*) AS `clan_count`
		FROM `clans_categories`
			JOIN `clans` ON (
				`clans_categories`.`id` = `clans`.`category_id` AND
				`clans`.`lang` = ".$api_lang."
			)
		GROUP BY `clans`.`category_id`
		ORDER BY 
			`clans_categories`.`title` ASC
	");
	
	if (!$categories) {
		api_error('Nav nevienas grupu kategorijas!');
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
		
		api_append(array(
			'group_count' => (int)$groups_total,
			'group_categories' => $data
		));
	}
    
/**
 *  Atgriezīs norādītajā kategorijā ietilpstošās grupas.
 */
} else if ($var1 === 'cat_groups' && !empty($var2)) {

	set_action('grupu sarakstu');

	$cat_id = (int)$var2;
	
	$get_cat = $db->get_row("
		SELECT `id`, `title` FROM `clans_categories` WHERE `id` = ".$cat_id
	);

	if (!$get_cat) {
		api_error('Kļūdaini norādīta sadaļa.');
	} else {
        
        $group_count = (int) $db->get_var("
			SELECT count(*) FROM `clans`
			WHERE `lang` = ".(int)$api_lang." AND `category_id` = ".(int)$get_cat->id."
		");
        
        // lappušu iestatījumi
		$per_page = 20;
		$current_page = 1;
		$page_count = (int) ceil($group_count / $per_page);
        
        if (isset($_GET['page'])) {
			$_GET['page'] = (int)$_GET['page'];
			if ($_GET['page'] < 1) {
				api_error('Pieprasīta neeksistējoša lappuse');
                api_log('Pieprasīta < 1 lappuse.');
                return;
			} else if ($_GET['page'] > $page_count) {
                api_error('Pārsniegts lappušu skaits');
                api_log('Pieprasīta pārāk liela lappuse.');
                return;
			}
			$current_page = $_GET['page'];
		}
		$limit_start = ($current_page - 1) * $per_page;
	
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
				`lang` = ".(int)$api_lang." AND
				`category_id` = ".(int)$get_cat->id." 
			ORDER BY `title` ASC
			LIMIT ".$limit_start.", ".$per_page."
		");
		
		if (!$groups) {
			api_log('Neizdevās atlasīt kategorijas grupu sarakstu.');
            api_error('Grupu ielāde neizdevās.');
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
					'avatar_url' => 'https://img.exs.lv/userpic/medium/'.$group->avatar,
					'title' => $group->title,
					'member_count' => (int)$group->members,
					'post_count' => (int)$group->posts,
					'in_group' => $in_group,
					'is_admin' => false,
					'is_mod' => $is_moderator,
					'unread_msgs' => $unread_msgs
				);
			}
			
			api_append(array(
                'group_count' => $group_count,
                'page_count' => $page_count,
                'current_page' => $current_page,
                'per_page' => $per_page,
				'category_id' => (int)$get_cat->id,
				'category_title' => $get_cat->title,
				'groups' => $data
			));
		}
	}

/**
 *  Lietotājs piesakās par biedru grupā.
 *  (/groups/{group_id}/apply)
 */
} else if (!empty($var1) && $var2 === 'apply') {

	$group_id = (int)$var1;

	$group = $db->get_row("
		SELECT
			`clans`.*,
			IFNULL(`clans_members`.`approve`, '-') AS `approved`
		FROM `clans`
		LEFT JOIN `clans_members` ON (
			`clans`.`id` = `clans_members`.`clan` AND
			`clans_members`.`user` = ".$auth->id."
		)
		WHERE
			`clans`.`id` = ".$group_id." AND
			`clans`.`lang` = ".$api_lang."
	");

	$credit = $db->get_var("SELECT `credit` FROM `users` WHERE `id` = ".$auth->id);

	if (empty($group)) {
		api_error('Neizdevās pārbaudīt grupas datus.');
		api_log('Norādīja neeksistējošas grupas id.');
	} else if (!api_check_xsrf()) {
		api_error('no hacking, pls');
		api_log('Piesakoties grupai, norādīja nepareizu xsrf atslēgu.');
	} else if ($group->owner == $auth->id) {
		api_error('Tu jau esi šīs grupas administrators.');
		api_log('Grupas administrators centās pievienoties grupai.');
	} else if ($group->approved == '0') {
		api_error('Grupai jau esi pieteicies, gaidi apstiprinājumu.');
		api_log('Centās pieteikties grupai, kurā jau gaida apstiprinājumu.');
	} else if ($group->approved == '1') {
		api_error('Jau esi grupā.');
		api_log('Centās pieteikties grupai, kuras biedrs lietotājs jau ir.');
	} else if ($group->paid == 1 && $credit < 3) {
		api_error('Nepietiek kredīta, lai pieteiktos grupai.');
		api_log('Pieteicās grupai, bet nepietika kredīta.');
	} else if ($group->archived) {
		api_error('Arhivētai grupai pieteikties nav iespējams.');
		api_log('Centās pieteikties arhivētai grupai.');
	} else {
	
		$approved = (int)$group->auto_approve;
		if ($group->paid == 1) {
			$db->query("
				UPDATE `users` SET `credit` = (`credit` - 3) WHERE `id` = ".$auth->id
			);
			$db->insert('clans_paid', array(
				'clan_id' => $group_id,
				'user_id' => $auth->id,
				'time' => time()
			));
			$approved = 1;
		}
	
		$db->insert('clans_members', array(
			'user' => $auth->id,
			'clan' => $group_id,
			'approve' => $approved,
			'date_added' => time()
		));
		
		if (!empty($group->strid)) {
			$group_link = '/'.$group->strid;
		} else {
			$group_link = '/group/'.(int)$group->id;
		}
		
		$db->query("
			UPDATE `clans` SET `members` = (
				SELECT count(*) FROM `clans_members` WHERE `clan` = ".$group_id." AND `approve` = 1
			) WHERE `id` = ".$group_id
		);
		$group->av_alt = 1;
		push('Pieteicās grupā &quot;<a href="'.$group_link.'">'.$group->title.'</a>&quot;', get_avatar($group, 's', true));
		notify($group->owner, 4, $group->id, $group_link.'/members', $group->title);
		
		// piesakoties ar kodēšanu saistītām grupām, lietotājam pie jaunumiem
		// sāks rādīt arī coding.lv ziņas
		if ($group->id == 53 || $group->id == 89) {
			$db->query("UPDATE `users` SET `show_code` = 1 WHERE `id` = ".(int)$auth->id);
		}
		
		api_append(array('joined' => true));
	}

/**
 *  Lietotājs izstājas no grupas.
 *  (/groups/{group_id}/leave)
 */
} else if (!empty($var1) && $var2 === 'leave') {

	$group_id = (int)$var1;

	$group = $db->get_row("
		SELECT
			`clans`.*,
			IFNULL(`clans_members`.`approve`, '-') AS `approved`
		FROM `clans`
		LEFT JOIN `clans_members` ON (
			`clans`.`id` = `clans_members`.`clan` AND
			`clans_members`.`user` = ".$auth->id."
		)
		WHERE
			`clans`.`id` = ".$group_id." AND
			`clans`.`lang` = ".$api_lang."
	");

	if (empty($group)) {
		api_error('Neizdevās pārbaudīt grupas datus.');
		api_log('Norādīja neeksistējošas grupas id.');
	} else if (!api_check_xsrf()) {
		api_error('no hacking, pls');
		api_log('Izstājoties no grupas, norādīja nepareizu xsrf atslēgu.');
	} else if ($group->owner == $auth->id) {
		api_error('Tu esi grupas administrators.');
		api_log('Grupas administrators centās izstāties no grupas.');
	} else if ($group->approved == '-') {
		api_error('Neesi pieteicies grupai.');
		api_log('Centās izstāties no grupas, kurs biedrs nemaz nav.');
	} else if ($group->approved == '0') {
		api_error('Nevar izstāties, ja neesi apstiprināts.');
		api_log('Centās izstāties no grupas, kurā gaida apstiprinājumu.');
	} else {
		
		$db->query("
			DELETE FROM `clans_members` WHERE `clan` = ".$group_id." AND `user` = ".$auth->id
		);
		$db->query("
			UPDATE `clans` SET `members` = (
				SELECT count(*) FROM `clans_members` WHERE `clan` = ".$group_id." AND `approve` = 1
			) WHERE `id` = ".$group_id
		);
		
		if (!empty($group->strid)) {
			$group_link = '/'.$group->strid;
		} else {
			$group_link = '/group/'.(int)$group->id;
		}
		$group->av_alt = 1;
		push('Izstājās no grupas &quot;<a href="'.$group_link.'">'.$group->title.'</a>&quot;', get_avatar($group, 's', true));
		
		api_append(array('left' => true));
	}

/**
 *  Atgriezīs grupas informāciju, kādu rādīt grupas sākumlapā.
 *  (/groups/{group_id}/home)
 */
} else if (!empty($var1) && $var2 === 'home') {

	$group_data = $db->get_row("
		SELECT 
			`clans`.`id` AS `clan_id`,
			`clans`.`title`,
			`clans_categories`.`id` AS `cat_id`,
			`clans_categories`.`title` AS `cat_title`,
			`clans`.`text`,
			`clans`.`avatar`,
			`clans`.`posts`,
			`clans`.`members`,
			`clans`.`archived`,
			`clans`.`owner`,
			`clans`.`owner_seenposts` AS `owner_seen`,        
			IFNULL(`clans_members`.`id`, 0) AS `is_member`,
			`clans_members`.`seenposts` AS `member_seen`
		FROM `clans`
			JOIN `clans_categories` ON (
				`clans`.`category_id` = `clans_categories`.`id`
			)
			LEFT JOIN `clans_members` ON (
				`clans`.`id` = `clans_members`.`clan` AND
				`clans_members`.`user` = ".(int)$auth->id." AND
				`clans_members`.`approve` = 1
			)
		WHERE 
			`clans`.`id` = ".(int)$var1
	);
	if (!$group_data) {
		api_error('Kļūdaini norādīta grupa.');
		api_log('Norādīja neeksistējošas grupas id ('.(int)$var1.').');
	} else {
	
		set_action('grupas informāciju');

		$owner = get_user($group_data->owner);
		if (!empty($owner->deleted)) {
			$owner->nick = 'dzēsts';
		}
		$owner = api_fetch_user($owner->id, $owner->nick, $owner->level, true);

		$is_member = ($group_data->is_member != '0') ? true : false;
		
		// cik ierakstus lietotājs jau ir izlasījis, ja ir grupā?
		$posts_seen = 0;
		if ($group_data->owner == $auth->id) {
			$posts_seen = $group_data->owner_seen;
		} else if ($is_member) {
			$posts_seen = $group_data->member_seen;
		}
		
		$arr_images = api_format_text($group_data->text);

		// atgriežamais masīvs ar datiem
		api_append(array('group_data' => array(
			'category_id' => (int)$group_data->cat_id,
			'category_title' => mb_strtoupper($group_data->cat_title),
			'group_id' => (int)$group_data->clan_id,
			'group_title' => $group_data->title,
			'intro_text' => $group_data->text,
			'image_count' => count($arr_images),
			'image_urls' => $arr_images,
			'avatar_url' => $img_server.'/userpic/large/'.$group_data->avatar,   
			'member_count' => (int)($group_data->members + 1), // + admins
			'post_count' => (int)$group_data->posts,
			'posts_seen' => (int)$posts_seen,
			'is_member' => $is_member,
			'created_by' => $owner,
			'is_archived' => ($group_data->archived ? true : false)
		)));
	}

/**
 *  Atgriezīs sarakstu ar grupā esošajiem lietotājiem.
 *  (/groups/{group_id}/members)
 */
} else if (!empty($var1) && $var2 === 'members') {

	$group_id = (int)$var1;
	
	$group_owner = $db->get_row("
		SELECT `owner` FROM `clans` WHERE `id` = ".$group_id
	);
	
	if (empty($group_id) || !$group_owner) {
		api_error('Neizdevās atlasīt biedru sarakstu.');
		api_log('Kļūdaini norādīta grupa, vai arī neizdevās noteikt tās autoru.');
	} else {
	
		set_action('grupas biedru sarakstu');

		// tā kā biedru grupā var būt pat > 1000,
		// to saraksts tiks ielādēts pa lappusēm
		$total_members = $db->get_var("
			SELECT count(*) FROM `clans_members` 
			WHERE `approve` = 1 AND `clan` = ".$group_id
		) + 1; // pieskaita grupas autoru, kas tabulā nav
		
		// lappušu iestatījumi
		$per_page = 20;
		$current_page = 1;
		$page_count = (int) ceil($total_members / $per_page);

		if (isset($_GET['page'])) {
			$_GET['page'] = (int)$_GET['page'];
			if ($_GET['page'] < 1) {
				api_error('Pieprasīta neeksistējoša lappuse.');
                api_log('Pieprasīta < 1 lappuse (page:'.$_GET['page'].').');
                return;
			} else if ($_GET['page'] > $page_count) {
                api_error('Pārsniegts lappušu skaits.');
                api_log('Pieprasīta pārāk liela grupas biedru lappuse (page:'.$_GET['page'].').');
                return;
			}
			$current_page = $_GET['page'];
		}
		$limit_start = ($current_page - 1) * $per_page;
        
        if ($current_page === 1) {
            // jo sākumā jau papildu pievieno grupas adminu
            $per_page -= 1;
        } else {
            // citās lappusēs sāk par vienu ātrāk, jo pirmajā lappusē
            // gala robeža ir par vienu mazāk
            $limit_start -= 1;
        }
		
		$arr_members = array();
		$member_count = 0;
		
		// jebkurā grupā ir vismaz administrators,
		// tāpēc to var pievienot jau uzreiz (ja skatīta tiek 1. lappuse)
		if ($current_page == 1) {  
			$owner = get_user($group_owner->owner);
			if (!empty($owner->deleted)) {
				$owner->nick = 'dzēsts';
			}
			$arr = array(
				'member_id' => 0,
                'is_admin' => true,
                'is_mod' => false
			);
            $arr += api_fetch_user($owner->id, $owner->nick, $owner->level, true);
            $arr_members[] = $arr;
			$member_count = 1;
		}

		// atlasa un pievieno masīvam visus pārējos grupas biedrus, ja tādi ir
		$all_members = $db->get_results("
			SELECT `id`, `user` AS `user_id`, `moderator` FROM `clans_members`
			WHERE `clan` = ".$group_id." AND `approve` = 1
			ORDER BY `moderator` DESC, `date_added` ASC
			LIMIT ".$limit_start.", ".$per_page."
		");
		
		if ($all_members) {
			foreach ($all_members as $member) {
				$usr = get_user($member->user_id);
				if ($usr) {
					if ($usr->deleted == 1) {
						$usr->nick = 'dzēsts';
					}
					$avatar = api_get_user_avatar($usr, 'l');
					$arr = array(
						'member_id' => (int)$member->id,
                        'is_admin' => false,
                        'is_mod' => (bool)$member->moderator				
					);
                    $arr += api_fetch_user($usr->id, $usr->nick, $usr->level, true);
                    $arr_members[] = $arr;
					$member_count++;
				}
			}
		}
		
		// atgriezīs datus lietotnei
		api_append(array(
            'member_count' => $total_members,
            'page_count' => $page_count,
            'current_page' => $current_page, 
            'per_page' => (($current_page === 1) ? $per_page + 1 : $per_page),
            'members' => $arr_members
        ));
	}

/**
 *  Citas situācijas.
 */
} else {
    api_log('Sasniegts grupu moduļa "else" bloks.');
    api_error('hellou... are thou lost?');
}
