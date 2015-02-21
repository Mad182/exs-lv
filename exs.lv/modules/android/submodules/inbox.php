<?php
/**
 *  Pieprasījumi darbībām saistībā ar vēstulēm.
 */

// nebūs iespējams skatīt failu pa tiešo
!isset($sub_include) and die('Error loading page!');

$var1 = (!empty($_GET['var1'])) ? $_GET['var1'] : '';
$var2 = (!empty($_GET['var2'])) ? $_GET['var2'] : '';

/**
 *  Pieprasīts saraksts ar saņemtajām vēstulēm.
 *  (/inbox/received)
 */
if ($var1 === 'received') {

    set_action('pastkastīti');
    
    if (isset($_GET['skip'])) {
        $skip = (int) $_GET['skip'];
    } else {
        $skip = 0;
    }

    // atlasīs lietotāja jaunākās vēstules
    $pms = $db->get_results("
        SELECT
            `pm`.*,
            `users`.`nick`,
            `users`.`level`,
            `users`.`deleted` AS `user_deleted`
        FROM `pm`
            JOIN `users` ON (
                `pm`.`from_uid` = `users`.`id`
            )
        WHERE
            `pm`.`to_uid` = ".$auth->id."
        ORDER BY
            `pm`.`date` DESC
        LIMIT
            $skip, 50
    ");
    
    if (!$pms) {
        a_error('Nav saņemtu vēstuļu');
    } else {

        $messages = array();
    
        foreach ($pms as $pm) {
            
            // sūtītāja dati
            $from = '';            
            if (!empty($pm->user_deleted)) {
                $from = '<em>dzēsts</em>';
            } else if (!empty($pm->imap_uid)) {
                if (!stristr($pm->imap_name, '?')) {
                    $from = wordwrap(textlimit(
                        h($pm->imap_name), 48, '...'), 20, '\n', 1);
                } else {
                    $from = wordwrap(textlimit(
                        h($pm->imap_email), 48, '...'), 20, '\n', 1);
                }
            } else {
                $from = a_fetch_user($pm->from_uid, $pm->nick, $pm->level);
            }
            
            $pm_title = wordwrap(textlimit(
                strip_tags($pm->title), 48, '...'), 20, '\n', 1);
            
            $messages[] = array(
                'id' => (int)$pm->id,
                'title' => $pm_title,
                'date' => display_time(strtotime($pm->date)),
                'from' => $from,
                'is_read' => (bool)$pm->is_read
            );
        }
        
        a_append(array('messages' => $messages));
    }

/**
 *  Vēstules (gan saņemtas, gan nosūtītas) lasīšana.
 *  (/inbox/read/{id})
 */
} else if ($var1 === 'read' && !empty($var2)) {

    $read_id = (int)$var2;
    
    $pm = $db->get_row("
        SELECT * FROM `pm` WHERE `id` = ".$read_id
    );
    
    if (!$pm) {
        a_error('Šāda vēstule neeksistē');
    } else if ($pm->to_uid != $auth->id && $pm->from_uid != $auth->id) {
        a_error('Pieeja vēstules saturam liegta');
        a_log('Centās atvērt svešu vēstuli');
    } else {
    
        $type = ($pm->to_uid == $auth->id) ? 'rec' : 'sent';
    
        // saņemtu atzīmēs vēstuli kā lasītu
        if ($type == 'rec' && $pm->is_read == 0) {
            $db->update('pm', $read_id, array(
                'is_read' => 1
            ));
        }
        
        // dati par lietotāju (sūtītāju vai saņēmēju)
        $usr = ($type == 'rec') ? get_user($pm->from_uid) : get_user($pm->to_uid);
        $usr_data = array();        
        if (!$usr || $usr->deleted) {
            $usr->nick = '<em>dzēsts</em>';
        } else {
            $usr_data = a_fetch_user($usr->id, $usr->nick, $usr->level);
        }
        
        $arr_images = a_format_text($pm->text);

        a_append(array('content' => array(
            'id' => (int)$pm->id,
            'title' => $pm->title,
            'text' => $pm->text,
            'text_images' => $arr_images,
            'date' => substr($pm->date, 0, 16),
            'user' => $usr_data,
            'user_avatar' => a_get_user_avatar($usr)
        )));
    }
    
/**
 *  Pieprasīts saraksts ar nosūtītajām vēstulēm.
 *  (/inbox/sent)
 */
} else if ($var1 === 'sent') {

    if (isset($_GET['skip'])) {
        $skip = (int) $_GET['skip'];
    } else {
        $skip = 0;
    }

    // saraksts ar nosūtītajām vēstulēm
    $pms = $db->get_results("
        SELECT
            `pm`.*,
            `users`.`nick`,
            `users`.`level`,
            `users`.`deleted` AS `user_deleted`
        FROM `pm`
            JOIN `users` ON (
                `pm`.`to_uid` = `users`.`id`
            )
        WHERE
            `pm`.`from_uid` = ".$auth->id."
        ORDER BY
            `pm`.`date` DESC
        LIMIT
            $skip, 50
    ");
    
    if (!$pms) {
        a_error('Nav nosūtītu vēstuļu');
    } else {
    
        $messages = array();
    
        foreach ($pms as $pm) {

            // saņēmēja dati
            $to = '';            
            if (!empty($pm->user_deleted)) {
                $to = '<em>dzēsts</em>';
            } else if (!empty($pm->imap_uid)) {
                if (!stristr($pm->imap_name, '?')) {
                    $to = wordwrap(textlimit(
                        h($pm->imap_name), 48, '...'), 20, '\n', 1);
                } else {
                    $to = wordwrap(textlimit(
                        h($pm->imap_email), 48, '...'), 20, '\n', 1);
                }
            } else {
                $to = a_fetch_user($pm->to_uid, $pm->nick, $pm->level);
            }
            
            $pm_title = wordwrap(textlimit(
                strip_tags($pm->title), 48, '...'), 20, '\n', 1);
            
            $messages[] = array(
                'id' => (int)$pm->id,
                'title' => $pm_title,
                'date' => display_time(strtotime($pm->date)),
                'to' => $to,
                'is_read' => (bool)$pm->is_read
            );
        }
    }
    
    a_append(array('messages' => $messages));
}
