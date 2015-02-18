<?php
/**
 *  Android grupu apakšmodulis.
 *
 *  Apstrādā pieprasījumus saistībā ar darbībām grupās.
 */

// nebūs iespējams skatīt failu pa tiešo
!isset($sub_include) and die('Error loading page!');

require_once(CORE_PATH.'/modules/android/functions.miniblogs.php');

// piegriezies rakstīt isset pārbaudi un neērto $_GET
$var1 = (!empty($_GET['var1'])) ? $_GET['var1'] : '';
$var2 = (!empty($_GET['var2'])) ? $_GET['var2'] : '';
$var3 = (!empty($_GET['var3'])) ? $_GET['var3'] : '';


/**
 *  Grupas minibloga vērtēšana ar plusu vai mīnusu.
 *  (/groups/{plus|minus}/{entry_id})
 */
if (!empty($var1) && !empty($var2) &&
    in_array($var1, array('plus', 'minus'))) {

    a_rate_comment($var2, ($var1 === 'plus'));

/**
 *  Atgriezīs sarakstu ar grupas jaunākajiem miniblogiem.
 *  (/groups/{group_id}/getlist)
 */
} else if (!empty($var1) && $var2 === 'getlist') {
    set_action('grupas miniblogus');
    a_fetch_miniblogs($var1);

/**
 *  Atgriezīs grupas minibloga saturu.
 *  (/groups/getcontent/{miniblog_id})
 */
} else if ($var1 === 'getcontent' && !empty($var2)) {
    set_action('grupas miniblogu');
    a_fetch_miniblog($var2);

/**
 *  Jauna grupas minibloga pievienošana vai esoša minibloga komentēšana.
 *  (/groups/{new|comment})
 */
} else if ($var1 === 'new' || $var1 === 'comment') {
    
    if (!isset($_POST['group_id']) || !isset($_POST['parent_id']) ||
        !isset($_POST['content']) || !isset($_POST['is_private'])) {
        a_error('Kļūdains pieprasījums');
        if ($var1 === 'new') {
            a_log('Netika iesniegti grupas minibloga ieraksta pievienošanas dati');
        } else {
            a_log('Netika iesniegti grupas minibloga komentēšanas dati');
        }
    } else {
        a_add_miniblog(array(
            'group_id' => $_POST['group_id'],
            'parent_id' => $_POST['parent_id'],
            'is_private' => $_POST['is_private'],
            'content' => $_POST['content']
        ));
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
            `clans`.`lang` = ".$android_lang."
    ");

    $credit = $db->get_var("SELECT `credit` FROM `users` WHERE `id` = ".$auth->id);

    if (empty($group)) {
        a_error('Neizdevās pārbaudīt grupas datus');
        a_log('Norādīja neeksistējošas grupas id');
    } else if (!a_check_xsrf()) {
        a_error('no hacking, pls');
        a_log('Piesakoties grupai, norādīja nepareizu xsrf atslēgu');
    } else if ($group->owner == $auth->id) {
        a_error('Tu jau esi šīs grupas administrators');
        a_log('Grupas administrators centās pievienoties grupai');
    } else if ($group->approved == '0') {
        a_error('Grupai jau esi pieteicies, gaidi apstiprinājumu');
        a_log('Centās pieteikties grupai, kurā jau gaida apstiprinājumu');
    } else if ($group->approved == '1') {
        a_error('Jau esi grupā');
        a_log('Centās pieteikties grupai, kuras biedrs lietotājs jau ir');
    } else if ($group->paid == 1 && $credit < 3) {
        a_error('Nepietiek kredīta, lai pieteiktos grupai');
        a_log('Pieteicās grupai, bet nepietika kredīta');
    } else if ($group->archived) {
        a_error('Arhivētai grupai pieteikties nav iespējams');
        a_log('Centās pieteikties arhivētai grupai');
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
        
        a_append(array('approved' => $approved));
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
            `clans`.`lang` = ".$android_lang."
    ");

    if (empty($group)) {
        a_error('Neizdevās pārbaudīt grupas datus');
        a_log('Norādīja neeksistējošas grupas id');
    } else if (!a_check_xsrf()) {
        a_error('no hacking, pls');
        a_log('Izstājoties no grupas, norādīja nepareizu xsrf atslēgu');
    } else if ($group->owner == $auth->id) {
        a_error('Tu esi grupas administrators');
        a_log('Grupas administrators centās izstāties no grupas');
    } else if ($group->approved == '-') {
        a_error('Neesi pieteicies grupai');
        a_log('Centās izstāties no grupas, kurs biedrs nemaz nav');
    } else if ($group->approved == '0') {
        a_error('Nevar izstāties, ja neesi apstiprināts');
        a_log('Centās izstāties no grupas, kurā gaida apstiprinājumu');
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
        
        a_append(array('left' => 1));
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
        a_error('Kļūdaini norādīta grupa');
        a_log('Norādīja neeksistējošas grupas id ('.(int)$var1.')');
    } else {
    
        set_action('grupas informāciju');

        $owner = get_user($group_data->owner);
        if (!empty($owner->deleted)) {
            $owner->nick = 'dzēsts';
        }
        $owner = a_fetch_user($owner->id, $owner->nick, $owner->level);

        $is_member = ($group_data->is_member != '0') ? true : false;
        
        // cik ierakstus lietotājs jau ir izlasījis, ja ir grupā?
        $posts_seen = 0;
        if ($group_data->owner == $auth->id) {
            $posts_seen = $group_data->owner_seen;
        } else if ($is_member) {
            $posts_seen = $group_data->member_seen;
        }

        // atgriežamais masīvs ar datiem
        a_append(array('content' => array(
            'id' => (int)$group_data->clan_id,
            'cat_title' => mb_strtoupper($group_data->cat_title),
            'title' => $group_data->title,
            'text' => $group_data->text,
            'av_url' => $img_server.'/userpic/large/'.$group_data->avatar,   
            'members' => (int)($group_data->members + 1), // + admins
            'posts' => (int)$group_data->posts,
            'posts_seen' => (int)$posts_seen,
            'is_member' => $is_member,
            'owner' => $owner,
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
        a_error('Neizdevās atlasīt biedru sarakstu');
        a_log('Kļūdaini norādīta grupa, vai arī neizdevās noteikt tās autoru');
    } else {
    
        set_action('grupas biedru sarakstu');

        // tā kā biedru grupā var būt pat > 1000,
        // to saraksts tiks ielādēts pa lappusēm
        $total_members = $db->get_var("
            SELECT count(*) FROM `clans_members` 
            WHERE `approve` = 1 AND `clan` = ".$group_id
        );
        
        // lappušu iestatījumi
        $max_per_page = 21;
        $current_page = 1;
        $page_count = ceil($total_members / $max_per_page);
        
        if (isset($_GET['page'])) {
            $_GET['page'] = (int)$_GET['page'];
            if ($_GET['page'] < 0) {
                $_GET['page'] = 1;
            } else if ($_GET['page'] > $page_count) {
                a_append(array(
                    'group_members' => array(),
                    'endoflist' => true
                ));
                return;
            }
            $current_page = $_GET['page'];
        }
        $limit_start = ($current_page - 1) * $max_per_page;
        
        $arr_members = array();
        $member_count = 0;
        
        // jebkurā grupā ir vismaz administrators,
        // tāpēc to var pievienot jau uzreiz (ja skatīta tiek 1. lappuse)
        if ($current_page == 1) {  
            $owner = get_user($group_owner->owner);
            if (!empty($owner->deleted)) {
                $owner->nick = 'dzēsts';
            }
            $avatar = a_get_user_avatar($owner, 'l');
            $arr_members[] = array(
                'member_id' => 0,
                'av_url' => $avatar,
                'user' => a_fetch_user($owner->id, $owner->nick, $owner->level),
                'is_mod' => 0
            );
            $member_count = 1;
        }

        // atlasīs un pievienos masīvam visus pārējos grupas biedrus, ja eksistē
        $all_members = $db->get_results("
            SELECT `id`, `user` AS `user_id`, `moderator` FROM `clans_members`
            WHERE `clan` = ".$group_id." AND `approve` = 1
            ORDER BY `moderator` DESC, `date_added` ASC
            LIMIT ".$limit_start.", ".$max_per_page."
        ");
        
        if ($all_members) {
            foreach ($all_members as $member) {
                $usr = get_user($member->user_id);
                if ($usr) {
                    if ($usr->deleted == 1) {
                        $usr->nick = 'dzēsts';
                    }
                    $avatar = a_get_user_avatar($usr, 'l');
                    $arr_members[] = array(
                        'member_id' => (int)$member->id,
                        'av_url' => $avatar,
                        'user' => a_fetch_user($usr->id, $usr->nick, $usr->level),
                        'is_mod' => (bool)$member->moderator
                    );
                    $member_count++;
                }
            }
        }
        
        // atgriezīs datus lietotnei
        if (!empty($arr_members)) {
        
            $endoflist = false;
        
            // pēdējā lappusē ziņos par saraksta beigām,
            // lai lietotne neturpinātu nākamo lappušu pieprasījumus
            if ($member_count < $max_per_page) {
                $endoflist = true;
            }
        
            a_append(array(
                'group_members' => $arr_members,
                'endoflist' => $endoflist
            ));
            
        } else {
            a_append(array(
                'group_members' => array(),
                'endoflist' => true
            ));
        }
    }

/**
 *  Citas situācijas.
 */
} else {
    a_error('Kļūdains pieprasījums (#5)');
    a_log('Kļūdains pieprasījums grupu modulī');
}
