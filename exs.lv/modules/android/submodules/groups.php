<?php
/**
 *  Android grupu apakšmodulis.
 */

// nebūs iespējams skatīt failu pa tiešo
!isset($sub_include) and die('Error loading page!');

/**
 *  Atgriezīs informāciju par grupu, kādu rādīt grupas sākumlapā.
 *
 *  var1 - grupas ID
 */
if (isset($_GET['var1']) &&
    isset($_GET['var2']) && $_GET['var2'] == 'home') {

    $group_id = (int)$_GET['var1'];
    
    $get_data = $db->get_row("
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
            `clans`.`id` = ".$group_id."
    ");
    
    if (!$get_data) {
        a_error('Kļūdains pieprasījums');
        
    } else {
    
        if (!empty($get_data->owner_deleted)) {
            $get_data->owner_nick = 'dzēsts';
        }
    
        $owner_data = a_fetch_user($get_data->owner_id, 
            $get_data->owner_nick, $get_data->owner_level);

        $is_member = ($get_data->is_member != '0') ? true : false;
        
        // cik ierakstus lietotājs jau ir izlasījis, ja ir grupā?
        $posts_seen = 0;
        if ($get_data->owner_id == $auth->id) {
            $posts_seen = $get_data->owner_seen;
        } else if ($is_member) {
            $posts_seen = $get_data->member_seen;
        }
    
        $group_data = array(
            'id' => (int)$get_data->id,
            'cat_title' => mb_strtoupper($get_data->cat_title),
            'title' => mb_strtoupper($get_data->title),
            'text' => $get_data->text,
            'av_url' => 'https://img.exs.lv/userpic/large/'.$get_data->avatar,   
            'members' => (int)$get_data->members,
            'posts' => (int)$get_data->posts,
            'posts_seen' => (int)$posts_seen,
            'is_member' => $is_member,
            'owner' => $owner_data,
            'is_archived' => ($get_data->archived ? 1 : 0)
        );
        
        $json_page = array('info' => $group_data);
    }
    
/**
 *  Atgriezīs sarakstu ar grupā esošajiem lietotājiem.
 *
 *  var1 - grupas ID
 */
} else if (isset($_GET['var1']) &&
    isset($_GET['var2']) && $_GET['var2'] == 'members') {
    
    $json_page = array('info' => 'not implemented, yet');

/**
 *  Citas situācijas.
 */
} else {
    a_log('Nepareizs pieprasījums uz /groups');
    a_error('Kļūdains pieprasījums');
}
