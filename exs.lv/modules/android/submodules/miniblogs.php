<?php
/**
 *  Android miniblogu apakšmodulis.
 *
 *  Apstrādā pieprasījumus saistībā ar darbībām miniblogos, tai skaitā
 *  jauna minibloga pievienošanu vai esoša komentēšanu, vērtēšanu u.c.
 */

// nebūs iespējams skatīt failu pa tiešo
!isset($sub_include) and die('Error loading page!');

require_once(CORE_PATH.'/modules/android/functions.miniblogs.php');

// piegriezies rakstīt isset pārbaudi un neērto $_GET
$var1 = (!empty($_GET['var1'])) ? $_GET['var1'] : '';
$var2 = (!empty($_GET['var2'])) ? $_GET['var2'] : '';
$var3 = (!empty($_GET['var3'])) ? $_GET['var3'] : '';


/**
 *  Atgriezīs jaunāko miniblogu sarakstu.
 */
if ($var1 === 'getlist') {
    a_fetch_miniblogs();

/**
 *  Jauna minibloga pievienošana vai esoša minibloga komentēšana.
 */
} else if ($var1 === 'new' || $var1 === 'comment') {
    
    if (empty($_POST['group_id']) || empty($_POST['parent_id']) ||
        empty($_POST['content']) || empty($_POST['is_private'])) {
        a_error('Kļūdains pieprasījums');
        if ($var1 === 'new') {
            a_log('Netika iesniegti minibloga ieraksta pievienošanas dati');
        } else {
            a_log('Netika iesniegti minibloga komentēšanas dati');
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
 *  Izvēlēts konkrēts miniblogs (der arī tie no grupām).
 *  $var1 - minibloga ID
 */
} else if (!empty($var1)) {

    $mb_id = (int)$var1;

    // atlasīs minibloga informāciju, pie reizes arī pārbaudot,
    // vai tas ir kādā no grupām un vai lietotājam ir tam piekļuve
    $miniblog = $db->get_row("
        SELECT 
            `miniblog`.*,
            IFNULL(`clans`.`id`, 0) AS `clan_id`,
            `clans`.`title` AS `clan_title`,
            `clans`.`avatar` AS `clan_avatar`,
            `clans`.`public` AS `clan_public`,
            `clans`.`owner` AS `clan_owner`,
            IFNULL(`clans_members`.`approve`, 0) AS `is_member`
        FROM `miniblog`
            LEFT JOIN `clans` ON `miniblog`.`groupid` = `clans`.`id`
            LEFT JOIN `clans_members` ON (
                `clans`.`id` = `clans_members`.`clan` AND
                `clans_members`.`user` = ".$auth->id." AND
                `clans_members`.`approve` = 1
            )
        WHERE 
            `miniblog`.`id`        = ".$mb_id." AND
            `miniblog`.`removed`   = 0 AND 
            `miniblog`.`parent`    = 0 AND 
            `miniblog`.`lang`      = ".(int)$android_lang."
        ORDER BY `miniblog`.`bump` DESC
    ");
    
    if (!$miniblog) {
        a_error('Miniblogs nav atrasts');
    } else {
    
        // miniblogs var būt un var nebūt no kādas grupas
        $clan_id = 0;
        $clan_title = $clan_av_url = '';
        if (!empty($miniblog->clan_id)) {
            $clan_id = (int)$miniblog->clan_id;
            $clan_title = $miniblog->clan_title;
            $clan_av_url = $img_server.'/userpic/large/'.$miniblog->clan_avatar;
        }

        // liedz darboties miniblogā, kas ir slēgtā grupā,
        // kurai lietotājam nav piekļuves
        if ($clan_id !== 0 && $miniblog->is_member == '0' &&
            $miniblog->clan_owner != $auth->id) {
            a_error('Nav pieejas');
            a_log('Mēģināja darboties miniblogā, kuram nav pieejas');           
        } else {    
    
            $author = get_user($miniblog->author);
            if ($author->deleted) {
                $author->nick = 'dzēsts';
            }
        
            // minibloga vērtēšana
            if (in_array($var2, array('plus', 'minus'))) {
            
                // miniblogā esoša komentāra vērtēšana
                if (!empty($var3)) {
                    if ($var2 === 'plus') {
                        a_rate_comment((int)$var3, 'miniblog', true);
                    } else {
                        a_rate_comment((int)$var3, 'miniblog', false);
                    }

                // galvenā minibloga vērtēšana
                } else if ($var2 == 'plus') {
                    a_rate_comment($miniblog->id, 'miniblog', true);
                } else if ($var2 == 'minus') {
                    a_rate_comment($miniblog->id, 'miniblog', false);
                }
            
            // atgriezīs minibloga saturu
            } else {
                a_fetch_miniblog($miniblog->id);
            }
        }
    }

} else {
    a_error('Kļūdaini veikts pieprasījums');
}
