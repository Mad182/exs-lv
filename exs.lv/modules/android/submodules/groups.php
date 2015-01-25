<?php
/**
 *  Android grupu apakšmodulis.
 *
 *  Apstrādā pieprasījumus saistībā ar darbībām grupās.
 */

// nebūs iespējams skatīt failu pa tiešo
!isset($sub_include) and die('Error loading page!');

// piegriezies rakstīt isset pārbaudi un neērto $_GET
$var1 = (!empty($_GET['var1'])) ? $_GET['var1'] : '';
$var2 = (!empty($_GET['var2'])) ? $_GET['var2'] : '';

// dati par grupu, kurā tiek veikta darbība;
// fiksē arī to, vai aktīvais lietotājs ir šīs grupas biedrs
$group_data = $db->get_row("
    SELECT 
        `clans`.`id`,
        `clans`.`title`,
        `clans_categories`.`title` AS `cat_title`,
        `clans`.`text`,
        `clans`.`avatar`,
        `clans`.`posts`,
        `clans`.`members`,
        `clans`.`archived`,
        `clans`.`owner_seenposts` AS `owner_seen`,
        
        IFNULL(`clans_members`.`id`, 0) AS `is_member`,
        `clans_members`.`seenposts` AS `member_seen`,
        
        `users`.`deleted` AS `owner_deleted`,
        `users`.`id` AS `owner_id`,
        `users`.`nick` AS `owner_nick`,
        `users`.`level` AS `owner_level`
    FROM `clans`
        JOIN `clans_categories` ON (
            `clans`.`category_id` = `clans_categories`.`id`
        )
        JOIN `users` ON (
            `clans`.`owner` = `users`.`id`
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

/**
 *  Atgriezīs grupas informāciju, kādu rādīt grupas sākumlapā.
 *  /groups/{group_id}/home
 */
} else if ($var2 === 'home') {

    if (!empty($group_data->owner_deleted)) {
        $group_data->owner_nick = 'dzēsts';
    }

    $owner_data = a_fetch_user($group_data->owner_id, 
        $group_data->owner_nick, $group_data->owner_level);

    $is_member = ($group_data->is_member != '0') ? true : false;
    
    // cik ierakstus lietotājs jau ir izlasījis, ja ir grupā?
    $posts_seen = 0;
    if ($group_data->owner_id == $auth->id) {
        $posts_seen = $group_data->owner_seen;
    } else if ($is_member) {
        $posts_seen = $group_data->member_seen;
    }

    a_append(array(
        'id' => (int)$group_data->id,
        'cat_title' => mb_strtoupper($group_data->cat_title),
        'title' => mb_strtoupper($group_data->title),
        'text' => $group_data->text,
        'av_url' => $img_server.'/userpic/large/'.$group_data->avatar,   
        'members' => (int)$group_data->members,
        'posts' => (int)$group_data->posts,
        'posts_seen' => (int)$posts_seen,
        'is_member' => $is_member,
        'owner' => $owner_data,
        'is_archived' => ($group_data->archived ? 1 : 0)
    ));
    
/**
 *  Atgriezīs sarakstu ar grupā esošajiem lietotājiem.
 *
 *  var1 - grupas ID
 */
} else if (!empty($var1) && $var2 === 'members') {    
    $json_page = array('info' => 'not implemented, yet');
    
/**
 *  Jauna grupas minibloga pievienošana vai esoša minibloga komentēšana.
 */
} else if ($var2 === 'new' || $var2 === 'comment') {
    
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
 *  Citas situācijas.
 */
} else {
    a_log('Nepareizs pieprasījums uz /groups');
    a_error('Kļūdains pieprasījums');
}
