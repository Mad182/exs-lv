<?php
/**
 *  Apstrādā random Android lietotnes pieprasījumus, 
 *  kurus nav vērts iedalīt kādā specifiskā apakšgrupā.
 *
 *  Realizētās iespējas:
 *
 *      /notifications
 *      /online
 *      /profile-data
 *      /mygroups
 */

// nebūs iespējams skatīt failu pa tiešo
!isset($sub_include) and die('Error loading page!');
 

/**
 *  Pieprasītas lietotāja jaunākās notifikācijas.
 */
if (isset($_GET['var1']) && $_GET['var1'] == 'notifications') {

    $arr_notifs = array(); // atgriežamais notifikāciju masīvs
    $notif_limit = 15; // cik pēdējos jaunumus atgriezt

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
            `user_id` = ".(int)$auth->id." AND
            `lang` = ".(int)$android_lang."
        ORDER BY `bump` DESC 
        LIMIT 0, $notif_limit
    ");
    
    if (!$user_notifications) {
        a_error('Notikumu nav!');
    } else {
    
        $inner_counter = 0;
        foreach ($user_notifications as $notify) {    
            $arr_notifs[] = [
                'type' => $texts[$notify->type],
                'text' => textlimit(trim($notify->info), 45, ''),
                'date' => 'pirms ' . time_ago(strtotime($notify->bump)),
                'project' => $config_domains[$notify->lang]['domain']
            ];
        }
        
        $json_page = ['notifications' => $arr_notifs];
    }

/**
 *  Atgriezīs sarakstu ar tiešsaistē esošiem lietotājiem.
 */
} else if (isset($_GET['var1']) && $_GET['var1'] == 'online') {

    $json_page = a_fetch_online();
    
/**
 *  Atgriezīs ar lietotāja profilu saistītu informāciju, no kuras daļa
 *  tiek rādīta arī iekš NavigationDrawer.
 */
} else if (isset($_GET['var1']) && $_GET['var1'] == 'profile-data') {
    
    $data = [
        'id' => (int)$auth->id,
        'nick' => $auth->nick,
        'level' => (int)$auth->level,
        'avatar' => 'https://img.exs.lv/userpic/large/'.$auth->avatar,
        'usertitle' => $auth->custom_title,
        'karma' => (int)$auth->karma,
        'days_online' => (int)$auth->days_in_row
    ];
    
    $json_page = ['userdata' => $data];
    
/**
 *  Atgriezīs sarakstu ar visām grupām, kurām lietotājs ir pieteicies.
 */
} else if (isset($_GET['var1']) && $_GET['var1'] == 'mygroups') {

    // grupas, kurās lietotājs ir admins
    $own_groups = $db->get_results("
        SELECT 
            `id`,
            `title`,
            `avatar`,
            `owner_seenposts`,
            `posts`
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
            `clans_members`.`date_added` ASC
    ");
    
    if (!$own_groups && !$member_of) {
        a_error('Neesi pieteicies nevienai grupai');
    } else {
    
        $groups = array();
        $group_count = 0;
        
        if ($own_groups) {
            foreach ($own_groups as $group) {
                $groups[] = [
                    'id' => (int)$group->id,
                    'title' => $group->title,
                    'is_admin' => true,
                    'is_mod' => false,
                    'avatar_m' => 'https://img.exs.lv/userpic/medium/'.$group->avatar,
                    'posts' => (int)$group->posts,
                    'posts_seen' => (int)$group->owner_seenposts
                ];
                $group_count++;
            }
        }
        
        if ($member_of) {
            foreach ($member_of as $group) {
                $groups[] = [
                    'id' => (int)$group->id,
                    'title' => $group->title,
                    'is_admin' => false,
                    'is_mod' => (bool)($group->moderator ? true : false),
                    'avatar_m' => 'https://img.exs.lv/userpic/medium/'.$group->avatar,
                    'posts' => $group->posts,
                    'posts_seen' => $group->seenposts
                ];
                $group_count++;
            }
        }

        $json_page = [
            'group_count' => $group_count++,
            'groups' => $groups
        ];
    }
    
/**
 *  Atgriezīs sarakstu ar visām grupām, grupējot tās pa kategorijām.
 */
} else if (isset($_GET['var1']) && $_GET['var1'] == 'allgroups') {

    $categories = $db->get_results("
        SELECT `id`, `title` FROM `clans_categories`
        ORDER BY `importance` DESC
    ");
    
    if (!$categories) {
        a_error('Nav izveidota neviena grupa');
    } else {
    
        $data = [];

        foreach ($categories as $group_cat) {
        
            $cat_data = [
                'id' => (int)$group_cat->id,
                'title' => $group_cat->title
            ];
            
            $group_cnt = 0;

            $groups = $db->get_results("
                SELECT 
                    `id`, `title`, `avatar`,
                    `owner`, `members`, `posts`
                FROM `clans` 
                WHERE 
                    `lang` = ".(int)$android_lang." AND
                    `category_id` = ".(int)$group_cat->id." 
                ORDER BY `title` ASC
            ");            
        
            if ($groups) {
                foreach ($groups as $group) {                
                    $cat_data['groups'][] = [
                        'id' => (int)$group->id,
                        'title' => $group->title,
                        'avatar_m' => 'https://img.exs.lv/userpic/medium/'.$group->avatar,
                        'members' => (int)$group->members,
                        'posts' => (int)$group->posts,
                        
                    ];
                    $group_cnt++;
                }
            }

            $cat_data['group_cnt'] = $group_cnt;
            $data[] = $cat_data;
        }
        
        $json_page = ['all_groups' => $data];
    }

/**
 *  Citas situācijas.
 */
} else {
    a_error('Kļūdains pieprasījums');
}
