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

    // lappušu iestatījumi
    $msg_per_page = 20;
    $current_page = 1;
    if (isset($_GET['page'])) {
        $current_page = (int)$_GET['page'];
        if ($current_page < 1) {
            $current_page = 1;
        }
    }
    $lim_start = ($current_page - 1) * $msg_per_page;
    
    // intereses pēc pielogosim, ka lietotājs sarakstu
    // lietotnē aizritinājis tik tālu
    if ($current_page > 100) { // 2000 vēstuļu
        a_log('Ielādējis briesmīgi daudz vēstuļu (lpp: '.$current_page.')');
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
            ".$lim_start.", ".$msg_per_page
    );
    
    if (!$pms) {
        a_append(array('endoflist' => true));
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
        
        a_append(array(
            'endoflist' => false,
            'messages' => $messages
        ));
    }

/**
 *  Vēstules nosūtīšana.
 *  (/inbox/send?xsrf={..} + $_POST)
 */
} else if ($var1 === 'send') {

    // kļūdu pārbaudes
    if (!isset($_POST['msg_title']) || !isset($_POST['msg_content']) ||
        !isset($_POST['msg_to'])) {
        a_error('Kļūdaini iesniegti dati');
        a_log('Sūtot vēstuli, nenorādīja pilnīgu informāciju');        
    } else if (!a_check_xsrf()) {
        a_error('no hacking, pls');
        a_log('Sūtot vēstuli, konstatēts XSRF uzbrukums');        
    } else if (isset($_SESSION['antiflood']) && $_SESSION['antiflood'] >= time() - 3) {
        a_error('exā plūdi. :( Brīdi uzgaidi!');        
    } else if ((int)$_POST['msg_to'] == $auth->id) {
        a_error('Tik vientuļi, ka raksti sev? :(');

    // viss šķietami kārtībā un vēstuli var sūtīt
    } else {
    
        $_SESSION['antiflood'] = time();
        
        $send_to = (int)$_POST['msg_to'];
        $send_title = sanitize(trim(stripslashes(h(strip_tags($_POST['msg_title'])))));
        $send_title = (!$send_title) ? '[bez nosaukuma]' : $send_title;
        $send_title = str_replace('Re:Re:', 'Re:', $send_title);
        $send_body = htmlpost2db($_POST['msg_content']);
        
        $receiver = get_user($send_to, true);

        if (!get_user($receiver)) {
            a_error('Norādītais saņēmējs neeksistē');
            a_log('Centās nosūtīt vēstuli neeksistējošam lietotājam (id:'.$send_to.')');
        } else if (empty($send_body)) {
            a_error('Tukšu vēstuli nosūtīt nevar');
        } else {
        
            // vēstules virsraksta apstrāde
            $send_title = sanitize(trim(stripslashes(h(strip_tags($send_title)))));
            if (!$send_title) {
                $send_title = '[bez nosaukuma]';
            }
            $send_title = str_replace('Re:Re:', 'Re:', $send_title);

            // citas vērtības
            $date = date('Y-m-d H:i:s');
            
            $db->insert('pm', array(
                'from_uid' => $auth->id,
                'to_uid' => $receiver->id,
                'date' => $date,
                'ip' => $auth->ip,
                'title' => $send_title,
                'text' => $send_body
            ));
            
            $msg_id = $db->insert_id;

            notify($receiver->id, 9);
            update_karma($auth->id);

            // atbilstoši notifikāciju iestatījumiem,
            // sūtīs e-pastu par saņemtu vēstuli
            if ($receiver->pm_notify_email == 2 ||
                ($receiver->pm_notify_email == 1 && strtotime($receiver->lastseen) < time() - 259200)) {

                $subject = 'Tev pienākusi vēstule portālā ' . $_SERVER['HTTP_HOST'];
                $message = '
                        <h3>Saņemta vēstule portālā ' . $_SERVER['HTTP_HOST'] . '</h3>
                        <p>
                            Čau! Tev pienākusi jauna ziņa no ' . h($auth->nick) . ' - &quot;' . stripslashes($send_title) . '&quot;
                        </p>
                        <p>
                            To vari izlasīt šeit: <a href="https://exs.lv/pm/?act=inbox&read=' . $msg_id . '">https://exs.lv/pm/?act=inbox&read=' . $msg_id . '</a>
                        </p>';

                send_email($receiver->mail, $subject, $message);
            }
            
            a_append(array(
                'sent' => true
            ));
        }
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

    // lappušu iestatījumi
    $msg_per_page = 20;
    $current_page = 1;
    if (isset($_GET['page'])) {
        $current_page = (int)$_GET['page'];
        if ($current_page < 1) {
            $current_page = 1;
        }
    }
    $lim_start = ($current_page - 1) * $msg_per_page;
    
    // intereses pēc pielogosim, ka lietotājs sarakstu
    // lietotnē aizritinājis tik tālu
    if ($current_page > 100) { // 2000 vēstuļu
        a_log('Ielādējis briesmīgi daudz vēstuļu (lpp: '.$current_page.')');
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
            ".$lim_start.", ".$msg_per_page
    );
    
    if (!$pms) {
        a_append(array('endoflist' => true));
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
        
        a_append(array(
            'endoflist' => false,
            'messages' => $messages
        ));
    }
}
