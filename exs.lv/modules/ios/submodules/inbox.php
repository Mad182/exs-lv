<?php
/**
 *  Pieprasījumi darbībām saistībā ar vēstulēm.
 */

// nebūs iespējams skatīt failu pa tiešo
!isset($sub_include) and die('Error loading page!');

$var1 = (!empty($_GET['var1'])) ? $_GET['var1'] : '';
$var2 = (!empty($_GET['var2'])) ? $_GET['var2'] : '';

/**
 *  Pieprasīts saraksts ar saņemtajām vai nosūtītajām vēstulēm.
 *  /inbox/in vai /inbox/out
 */
if ($var1 === 'in' || $var1 === 'out') {

	set_action('pastkastīti');
    
    $field_prefix = ($var1 === 'in') ? 'to' : 'from';
    $user_key = ($var1 === 'in') ? 'from' : 'to';
    $time_key = ($var1 === 'in') ? 'received_at' : 'sent_at';

    // saņemto/nosūtīto vēstuļu skaits
    $message_count = $db->get_var(
        "SELECT count(*) FROM `pm`
        WHERE `pm`.`".$field_prefix."_uid` = ".$auth->id
    );
	// vēl nelasīto vēstuļu skaits
	$unread = $db->get_var("
		SELECT count(*) FROM `pm`
        WHERE `".$field_prefix."_uid` = ".$auth->id." AND `is_read` = 0
	");
    
	// lappušu iestatījumi
	$per_page = 20;
    $page_count = (int) ceil($message_count / $per_page);    
	$current_page = 1;
    
	if (isset($_GET['page'])) {
        $_GET['page'] = (int)$_GET['page'];
        if ($_GET['page'] < 1) {
			api_error('Pieprasīta neeksistējoša lappuse');
            api_log('Pieprasīta < 1 vēstuļu lappuse.');
			return;
        } else if ($_GET['page'] > $page_count) {
            api_error('Pārsniegts skatāmo lappušu skaits');
            api_log('Pieprasīta pārāk liela vēstuļu lappuse.');
			return;
		}
		$current_page = $_GET['page'];
	}
	$lim_start = ($current_page - 1) * $per_page;    

	// atlasīs lietotāja saņemtās/nosūtītās vēstules
	$pms = $db->get_results("
		SELECT
			`pm`.*,
			`users`.`nick`,
			`users`.`level`,
			`users`.`deleted` AS `user_deleted`
		FROM `pm`
			JOIN `users` ON (
				`pm`.`".$field_prefix."_uid` = `users`.`id`
			)
		WHERE
			`pm`.`".$field_prefix."_uid` = ".$auth->id."
		ORDER BY
			`pm`.`date` DESC
		LIMIT
			".$lim_start.", ".$per_page
	);
	
	if (!$pms) {
        api_error('Neizdevās ielādēt vēstules');
        api_log('Neizdevās ielādēt vēstules.');
	} else {

		$messages = array();
	
		foreach ($pms as $pm) {
			
			// sūtītāja/saņēmēja dati
			$other_user = new arrayObject();            
			if (!empty($pm->user_deleted)) {
                $other_user = array(
                    'nick' => '<em>dzēsts</em>',
                    'params' => '0|0|0|0|0',
                    'avatar_url' => ''
                );
			} else if (!empty($pm->imap_uid)) {
                $other_user = '';
				if (!stristr($pm->imap_name, '?')) {
					$other_user = wordwrap(textlimit(
						h($pm->imap_name), 48, '...'), 20, '\n', 1);
				} else {
					$other_user = wordwrap(textlimit(
						h($pm->imap_email), 48, '...'), 20, '\n', 1);
				}
                $other_user = array(
                    'nick' => $other_user,
                    'params' => '0|0|0|0|0',
                    'avatar_url' => ''
                );
			} else {
				$other_user = api_fetch_user($pm->from_uid, $pm->nick, $pm->level, true);
			}
			
			$pm_title = wordwrap(textlimit(
				strip_tags($pm->title), 48, '...'), 20, ' ', 1);
			
			$messages[] = array(
				'id' => (int)$pm->id,
				'title' => $pm_title,
				$time_key => $pm->date,
				$user_key => $other_user,
				'is_read' => (bool)$pm->is_read
			);
		}
		
		api_append(array(
            'message_count' => (int) $message_count,
            'page_count' => $page_count,
            'current_page' => $current_page,
            'per_page' => $per_page,
			'unread' => (($unread) ? (int)$unread : 0),
			'messages' => $messages
		));
	}    
}

/**
 *  Vēstules nosūtīšana.
 *  (/inbox/send?xsrf={..} + $_POST)
 */
else if ($var1 === 'send') {

	// kļūdu pārbaudes
	if (!isset($_POST['msg_title']) || !isset($_POST['msg_content']) ||
		!isset($_POST['msg_to'])) {
		api_error('Kļūdaini iesniegti dati');
		api_log('Sūtot vēstuli, nenorādīja pilnīgu informāciju');
	} else if (!api_check_xsrf()) {
		api_error('no hacking, pls');
		api_log('Sūtot vēstuli, konstatēts XSRF uzbrukums');
	} else if (isset($_SESSION['antiflood']) && $_SESSION['antiflood'] >= time() - 3) {
		api_error('exā plūdi. :( Brīdi uzgaidi!');
	} else if ((int)$_POST['msg_to'] == $auth->id) {
		api_error('Tik vientuļi, ka raksti sev? :(');

	// viss šķietami kārtībā un vēstuli var sūtīt
	} else {
	
		$_SESSION['antiflood'] = time();
		
		$send_to = (int)$_POST['msg_to'];
		$send_body = htmlpost2db($_POST['msg_content']);        
		$receiver = get_user($send_to, true);

		if (!$receiver) {
			api_error('Norādītais saņēmējs neeksistē');
			api_log('Centās nosūtīt vēstuli neeksistējošam lietotājam (id:'.$send_to.')');
		} else if (empty($send_body)) {
			api_error('Tukšu vēstuli nosūtīt nevar');
		} else {
		
			// vēstules virsraksta apstrāde
			$send_title = sanitize(trim(stripslashes(h(strip_tags($_POST['msg_title'])))));
			if (!$send_title) {
				$send_title = '[bez nosaukuma]';
			}
			$send_title = str_replace(array('Re:Re:', 'Re: Re:'), 'Re:', $send_title);
			
			$db->insert('pm', array(
				'from_uid' => $auth->id,
				'to_uid' => $receiver->id,
				'date' => date('Y-m-d H:i:s'),
				'ip' => $auth->ip,
				'title' => $send_title,
				'text' => $send_body,
				'device' => 3
			));
			
			$msg_id = $db->insert_id;

			notify($receiver->id, 9);
			update_karma($auth->id);

			// atbilstoši notifikāciju iestatījumiem,
			// sūtīs e-pastu par saņemtu vēstuli
			if (!isset($android_local) && ($receiver->pm_notify_email == 2 ||
				($receiver->pm_notify_email == 1 && strtotime($receiver->lastseen) < time() - 259200))) {

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
			
			api_append(array(
				'sent' => true
			));
		}
	}
}

/**
 *  Vēstules (gan saņemtas, gan nosūtītas) lasīšana.
 *  /inbox/read/{id}
 */
else if ($var1 === 'read' && !empty($var2)) {

	$read_id = (int)$var2;
	
	$pm = $db->get_row("
		SELECT * FROM `pm` WHERE `id` = ".$read_id
	);
	
	if (!$pm) {
		api_error('Šāda vēstule neeksistē');
	} else if ($pm->to_uid != $auth->id && $pm->from_uid != $auth->id) {
		api_error('Pieeja vēstules saturam liegta');
		api_log('Centās atvērt svešu vēstuli');
	} else {
	
		$type = ($pm->to_uid == $auth->id) ? 'rec' : 'sent';
        $key_time = ($type === 'rec') ? 'received_at' : 'sent_at';
        $key_user = ($type === 'rec') ? 'from' : 'to';
	
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
            $usr_data = array(
                'nick' => '<em>dzēsts</em>',
                'params' => '0|0|0|0|0',
                'avatar_url' => ''
            );
		} else {
			$usr_data = api_fetch_user($usr->id, $usr->nick, $usr->level, true);
		}
		
		$arr_images = api_format_text($pm->text);

		api_append(array('message' => array(
			'id' => (int)$pm->id,
			'title' => $pm->title,
			'text' => $pm->text,
			'image_count' => count($arr_images),
			'image_urls' => $arr_images,
			$key_time => $pm->date,
			$key_user => $usr_data
		)));
	}	
} else {
    api_log('Sasniegts vēstuļu moduļa "else" bloks.');
    api_error('hellou... are thou lost?');
}
