<?php
/**
 *  Apstrādā random Android lietotnes pieprasījumus, 
 *  kurus nav vērts iedalīt kādā specifiskā apakšgrupā.
 */

// nebūs iespējams skatīt failu pa tiešo
!isset($sub_include) and die('Error loading page!');

$var1 = (!empty($_GET['var1'])) ? $_GET['var1'] : '';
$var2 = (!empty($_GET['var2'])) ? $_GET['var2'] : '';

/**
 *  Pieprasītas lietotāja jaunākās notifikācijas.
 */
if ($var1 === 'notifications') {

    $arr_notifs = array(); // atgriežamais notifikāciju masīvs
    $notif_limit = 25; // cik pēdējos jaunumus atgriezt

	$texts = array(
		0 => 'atbilde komentāram',
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
		15 => 'tevi pieminēja',
		16 => 'tevi pieminēja galerijā'
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

        foreach ($user_notifications as $notify) {    
            $arr_notifs[] = array(
                'type' => (int)$notify->type,
                'text' => textlimit(trim($notify->info), 45, ''),
                'date' => 'pirms ' . time_ago(strtotime($notify->bump))
            );
        }
        
        a_append(array('notifications' => $arr_notifs));
    }

/**
 *  Atgriezīs sarakstu ar tiešsaistē esošiem lietotājiem.
 */
} else if ($var1 === 'online') {
    $json_page = a_fetch_online();
    
/**
 *  Atgriezīs ar lietotāja profilu saistītu informāciju.
 *  /random/profile/{user_id}
 */
} else if ($var1 === 'profile' && !empty($var2)) {

    $user_id = (int)$var2;
    
    $profile_data = $db->get_row("
        SELECT * FROM `users` WHERE `id` = ".$user_id
    );
    if (!$profile_data) {
        a_error('Šāds profils neeksistē');
    } else {
    
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
        $days = ceil((time() - strtotime($profile_data->date)) / 60 / 60 / 24);
        
        // pēdējoreiz redzēts pirms...
        $time_ago = time_ago(strtotime($profile_data->lastseen));
        
        $data = array(
            'id' => (int)$auth->id,
            'nick' => $auth->nick,
            'level' => (int)$auth->level,
            'avatar' => 'https://img.exs.lv/userpic/large/'.$auth->avatar,
            'days_online' => (int)$auth->days_in_row,
            'days_registered' => (int)$days,
            'last_seen' => 'pirms '.$time_ago,
            'usertitle' => $auth->custom_title,
            'web' => $auth->web,
            'karma' => (int)$auth->karma,
            'posts' => (int)$posts,
            'pages' => (int)$user_pages,
            'voted_by_self_cnt' => (int)$profile_data->vote_total,
            'voted_by_self_sum' => (int)$profile_data->vote_others,
            'voted_by_others' => (int)$voteval
        );
        
        // moderatoriem redzama papildinformācija par lietotāju
        if (im_mod()) {
            $data['email'] = $profile_data->mail;
            $data['last_ip'] = $profile_data->lastip;
            $data['useragent'] = $profile_data->useragent;        
        }
        
        a_append(array('userdata' => $data));
        
        // pievienos klāt arī lietotāja pāris jaunākos apbalvojumus
        a_fetch_awards($user_id);
    }
    
/**
 *  Atgriezīs sarakstu ar visām grupām, kurām lietotājs ir pieteicies.
 */
} else if ($var1 === 'mygroups') {

    // grupas, kurās lietotājs ir admins
    $own_groups = $db->get_results("
        SELECT `id`, `title`, `avatar`, `owner_seenposts`, `posts`, `members`
        FROM `clans`
        WHERE 
            `owner = ".(int)$auth->id." AND
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
