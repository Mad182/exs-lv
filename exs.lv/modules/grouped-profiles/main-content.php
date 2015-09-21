<?php
/**
 *  Pamatsaturs, kas rādāms /grouped-profiles sadaļas sākumlapā.
 *
 *  Pārnests uz atsevišķu failu, jo tiek izmantots vairākās vietās -
 *  gan atverot sadaļu normālā veidā, gan nospiežot uz sadaļas cilnes un
 *  ielādējot saturu caur ajax.
 */

// lai izvairītos no faila atvēršanas tiešā veidā
if (!$sub_include) die('error');

// ajax gadījumā var nebūt nepieciešams ārējā template saturs,
// tāpēc šo fragmentu veidosim jaunā objektā
$new_tpl = fetch_tpl();
if (empty($new_tpl)) { die('error'); }

$new_tpl->assignGlobal(array(
    'category-url' => $category->textid,
));
$new_tpl->newBlock('content-info');
$new_tpl->newBlock('new-profile-form');

// atlasīs sarakstu ar saistītajiem profiliem
$profiles = $db->get_results("
    SELECT
        `users_groups`.`id` AS `ug_id`,
        `users_groups`.`description`,
        `users`.`id`        AS `user_id`,
        `users`.`nick`      AS `user_nick`,
        `users`.`level`     AS `user_level`,
        `users`.`lastip`    AS `user_lastip`,
        `users`.`lastseen`  AS `user_seen`,
        IFNULL(`child`.`id`, 0) AS `child_id`,
        `children`.`id`     AS `child_parent`,
        `child`.`nick`      AS `child_nick`,
        `child`.`level`     AS `child_level`,
        `child`.`lastip`    AS `child_lastip`,
        `child`.`lastseen`  AS `child_seen`
    FROM `users_groups`
        JOIN `users` ON (
            `users_groups`.`user_id` = `users`.`id` AND
            `users`.`deleted` = 0
        )
        LEFT JOIN `users_groups` AS `children` ON (
            `users_groups`.`id` = `children`.`parent_id` AND 
            `children`.`deleted_by` = 0
        )
        LEFT JOIN `users` AS `child` ON (
            `children`.`user_id` = `child`.`id` AND
            `child`.`deleted` = 0 AND
            `child`.`level` NOT IN(1,2)
        )
    WHERE 
        `users_groups`.`parent_id` = 0 AND
        `users_groups`.`deleted_by` = 0
    ORDER BY 
        `users`.`nick` ASC,
        `child`.`nick` ASC
");

if (!$profiles) {
    $new_tpl->newBlock('no-profiles');
} else {
    $new_tpl->newBlock('profile-list');

    $tmp_main_id = 0;
    $main_cnt = 0;      // main profilu skaits
    $children_cnt = 0;  // child profilu skaits ciklā ejošajam main
    
    foreach ($profiles as $profile) {

        // mainoties "main" profilam, jāizveido jauni bloki
        if ($profile->user_id != $tmp_main_id) {

            $tmp_main_id = $profile->user_id;
            
            // pirmajā cikla reizē children skaits vēl nav zināms, jo
            // tas tiek skaitīts, ejot pa ciklu, un ir jāpieraksta beigās
            if ($main_cnt > 0) {                    
                if ($children_cnt == 0) {
                    $new_tpl->newBlock('no-children');
                } else {
                    $new_tpl->newBlock('child-table-header');
                }
                $new_tpl->gotoBlock('a-profile');
                $new_tpl->assign('profile_count', $children_cnt + 1);
            }                
            $children_cnt = 0;
            
            $main_cnt++;
            
            // izveido jaunu rindu galvenajam profilam
            $profile->user_nick = usercolor($profile->user_nick, $profile->user_level, false, $profile->user_id);
            $profile->user_seen = date(
                'd.m.y, H:i',
                strtotime($profile->user_seen)
            );

            $new_tpl->newBlock('a-profile');
            $new_tpl->assignAll($profile);
            $new_tpl->assign('counter', $main_cnt);
            
            // tabulas rinda visiem turpmākajiem child profiliem
            $new_tpl->newBlock('all-children');
            $new_tpl->assign(array(
                'user_seen' => $profile->user_seen,
                'user_lastip' => (empty($profile->user_lastip) ? '-' : $profile->user_lastip)
            ));
            if (!empty($profile->description)) {
                $new_tpl->assign(
                    'description',
                    '<p><strong>Komentārs:</strong></p><p>'.$profile->description.'</p>'
                );
            }
        }
        
        // main profilam var nebūt neviena child profila
        if ($profile->child_id != '0') {

            $profile->child_nick = usercolor(
                $profile->child_nick,
                $profile->child_level,
                false,
                $profile->child_id
            );

            $profile->child_seen = date(
                'd.m.y, H:i',
                strtotime($profile->child_seen)
            );
        
            $new_tpl->newBlock('a-child');
            $new_tpl->assignAll($profile);
            
            $children_cnt++;
        }
    }
    
    // ciklā pēdējam ierakstam child skaits vēl netika norādīts
    if ($children_cnt == 0) {
        $new_tpl->newBlock('no-children');
    } else {
        $new_tpl->newBlock('child-table-header');
    }
    
    // atgriezīsies uz parent bloku, lai norādītu saskaitīto skaitu
    $new_tpl->gotoBlock('a-profile');
    $new_tpl->assign('profile_count', $children_cnt + 1);
    
    // iekļaus izvadē javascriptu, kas aizritinās lapu līdz šim profilam
    if (isset($_GET['scroll'])) {
        $scroll_to = (int)$_GET['scroll'];
        if ($scroll_to > 0) {
            $new_tpl->newBlock('scroll-to');
            $new_tpl->assign('main-id', '\'#profile-'.$scroll_to.'\'');
        }
    }
}
