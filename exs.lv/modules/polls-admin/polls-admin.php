<?php

/** 
 *  Aptauju pārvaldība
 */

if (im_mod()) {

	$tpl->newBlock('polls_admin-body');

	if (isset($_GET['act']) && $_GET['act'] == 'list') {
    
		$tpl->assign('list-active', ' active');
		$polls = $db->get_results("SELECT * FROM `poll` WHERE `topic` != 0 AND `lang` = '$lang' ORDER BY `id` DESC");
        
		if ($polls) {
			$tpl->newBlock('polls_admin-list');
			foreach ($polls as $poll) {
				$tpl->newBlock('polls_admin-list-node');
				$tpl->assign([
					'question' => $poll->name,
					'topic' => $poll->topic,
				]);
			}
		}
        
	} else {
    
		$tpl->assign('exist-active', ' active');

		if (isset($_POST['new-poll-q']) && isset($_POST['new-poll-a'])) {
        
            // runescape apakšprojektā veidots tiek miniblogs
            if ($lang == 9) {
            
                $new_q = sanitize(trim($_POST['new-poll-q']));
                $mb_text = sanitize(trim($_POST['new-poll-q']));
            
                $insert = $db->query("INSERT INTO `miniblog`
                    (author, date, text, lang, bump)
                    VALUES (
                        '$rsbot_id',
                        '".date("Y-m-d H:i:s", time())."',
                        '".sanitize($mb_text)."',
                        9,
                        '".time()."'
                    ) 
                ");
                
                $poll_page_id = $db->insert_id;
            }
            // citos projektos - raksts
            else {
                $new_q = sanitize(trim($_POST['new-poll-q']));
                $title = title2db('[Aptauja] ' . $_POST['new-poll-q']);
                $strid = mkslug_newpage($title);

                $db->query("INSERT INTO `pages` (strid,textid,category,text,title,author,date,bump,ip,lang)
                                        VALUES ('$strid','" . time() . "','" . $polls_cat . "','<p>" . $new_q . "</p>','$title','$auth->id',NOW(),NOW(),'$auth->ip','$lang')");

                $poll_page_id = $db->insert_id;            
            }

            
            // ieraksta datubāzē pašu aptauju
			$db->query("INSERT INTO `poll` (`name`,`topic`,`lang`) VALUES ('$new_q', '$poll_page_id', '$lang')");
            $poll_id = $db->insert_id;
            
            // ieraksta datubāzē katru aptaujas atbilžu variantu            
			$html_answers = '';
            
			foreach ($_POST['new-poll-a'] as $new_a) {
				$new_a = trim($new_a);
				if (!empty($new_a)) {
					$new_a = sanitize($new_a);
					$html_answers .= '<li>' . $new_a . '</li>';
					$db->query("INSERT INTO `questions` (`pid`, `question`) VALUES ('$poll_id','$new_a')");
				}
			}
            
            if ($lang == 9) {
            
                $new_text  = '<p class="mb-poll-question"><strong>Aptauja:</strong> '.$new_q.'</p>';
                $new_text .= '<ul class="mb-poll-li">' . $html_answers . '</ul>';
                $new_text .= '<p class="mb-poll-vote">Balsot iespējams lapas sānā redzamajā aptaujā.<br>Ieraksts uzģenerēts automātiski aptaujas rezultātu apspriešanai.</p>';
            
                $db->query("UPDATE `miniblog` SET `text` = '$new_text' WHERE `id` = '$poll_page_id'");
            } else {
            
                $new_text = '<p><strong>' . $new_q . '</strong></p><ul>' . $html_answers . '</ul><p>(Atbildi aptaujā lapas malā)</p>';
            
                $db->query("UPDATE `pages` SET `text` = '$new_text' WHERE `id` = '$poll_page_id'");
            }

			update_karma($auth->id);

			$tpl->newBlock('polls_admin-success');
		} else {
			$tpl->newBlock('polls_admin-add');
		}
	}
} else {
	set_flash('Tev nav pieejas šai sadaļai!', 'error');
	redirect();
}
