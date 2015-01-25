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
 *  (/groups/{group_id}/{plus|minus}/{entry_id})
 */
if (!empty($var1) && !empty($var2) && !empty($var3) &&
           in_array($var2, array('plus', 'minus'))) {

    // miniblogā esoša komentāra vērtēšana
    if (!empty($var3)) {
        a_rate_comment($var3, ($var2 === 'plus'));
    } else { // paša minibloga vērtēšana
        a_rate_comment($var1, ($var2 === 'plus'));
    }

/**
 *  Atgriezīs sarakstu ar grupas jaunākajiem miniblogiem.
 *  /groups/{group_id}/getlist
 */
} else if (!empty($var1) && $var2 === 'getlist') {
    a_fetch_miniblogs($var1);

/**
 *  Atgriezīs grupas minibloga saturu.
 *  /groups/{group_id}/getcontent/{miniblog_id}
 */
} else if (!empty($var1) && $var2 === 'getcontent' && !empty($var3)) {
    a_fetch_miniblog($var3);

/**
 *  Jauna grupas minibloga pievienošana vai esoša minibloga komentēšana.
 */
} else if (!empty($var1) && ($var2 === 'new' || $var2 === 'comment')) {
    
    if (empty($_POST['group_id']) || empty($_POST['parent_id']) ||
        empty($_POST['content']) || empty($_POST['is_private'])) {
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
 *  Atgriezīs grupas informāciju, kādu rādīt grupas sākumlapā.
 *  /groups/{group_id}/home
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

        $owner = get_user($group_data->owner);
        if (!empty($owner->deleted)) {
            $owner->nick = 'dzēsts';
        }
        $owner = a_fetch_user($owner->id, $owner->nick, $owner->level);

        $is_member = ($group_data->is_member != '0') ? true : false;
        
        // cik ierakstus lietotājs jau ir izlasījis, ja ir grupā?
        $posts_seen = 0;
        if ($group_data->owner_id == $auth->id) {
            $posts_seen = $group_data->owner_seen;
        } else if ($is_member) {
            $posts_seen = $group_data->member_seen;
        }

        // atgriežamais masīvs ar datiem
        a_append(array(
            'id' => (int)$group_data->clan_id,
            'cat_title' => mb_strtoupper($group_data->cat_title),
            'title' => mb_strtoupper($group_data->title),
            'text' => $group_data->text,
            'av_url' => $img_server.'/userpic/large/'.$group_data->avatar,   
            'members' => (int)$group_data->members,
            'posts' => (int)$group_data->posts,
            'posts_seen' => (int)$posts_seen,
            'is_member' => $is_member,
            'owner' => $owner,
            'is_archived' => ($group_data->archived ? 1 : 0)
        ));
    }

/**
 *  Atgriezīs sarakstu ar grupā esošajiem lietotājiem.
 *  /groups/{group_id}/members
 */
} else if (!empty($var1) && $var2 === 'members') {

    $group_id = (int)$var1;
    
    $group_owner = $db->get_row("
        SELECT `owner` FROM `clans` WHERE `id` = ".$group_id
    );
    
    if (empty($group_id) || !$group_owner) {
        a_error('Neizdevās atlasīt biedru sarakstu');
        a_log('Kļūdaini norādīta grupa, vai arī neizdevās noteikt tās autoru');
    // lietotājam var nebūt piekļuves šai grupai
    } else if (!a_member_of($group_id)) {
        // funkcija jau pievienos atbildei kļūdu paziņojumus
    } else {

        // tā kā biedru grupā var būt pat > 1000,
        // to saraksts tiks ielādēts pa lappusēm
        $total_members = $db->get_var("
            SELECT count(*) FROM `clans_members` 
            WHERE `approve` = 1 AND `clan` = ".$group_id
        );
        
        // lappušu iestatījumi
        $max_per_page = 30;
        $current_page = 1;
        $page_count = ceil($total_members / $max_per_page);
        
        if (isset($_GET['page'])) {
            $_GET['page'] = (int)$_GET['page'];
            if ($_GET['page'] < 0 || $_GET['page'] > $page_count) {
                $_GET['page'] = 1;
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
            $arr_members[] = array(
                'member_id' => 0,
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
                if ($usr->deleted == 1) {
                    $usr->nick = 'dzēsts';
                }
                $arr_members[] = array(
                    'member_id' => (int)$member->id,
                    'user' => a_fetch_user($usr->id, $usr->nick, $usr->level),
                    'is_mod' => (bool)$member->moderator
                );
                $member_count++;
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
            a_error('Neizdevās atlasīt biedru sarakstu');
            a_log('Datu apstrādes kļūda, atlasot grupas biedru sarakstu');
        }
    }

/**
 *  Citas situācijas.
 */
} else {
    a_log('Nepareizs pieprasījums uz /groups');
    a_error('Kļūdains pieprasījums');
}
