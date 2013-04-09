<?php

if (im_mod()) {
	$tpl->newBlock('polls_admin-body');

	if (isset($_GET['act']) && $_GET['act'] == 'list') {
		$tpl->assign('list-active', ' active');
		$polls = $db->get_results("SELECT * FROM `poll` WHERE `topic` != 0 AND `lang` = '$lang' ORDER BY `id` DESC");
		if ($polls) {
			$tpl->newBlock('polls_admin-list');
			foreach ($polls as $poll) {
				$tpl->newBlock('polls_admin-list-node');
				$tpl->assign(array(
					'question' => $poll->name,
					'topic' => $poll->topic,
				));
			}
		}
	} else {
		$tpl->assign('exist-active', ' active');

		if (isset($_POST['new-poll-q']) && isset($_POST['new-poll-a'])) {
			$new_q = sanitize(trim($_POST['new-poll-q']));
			$title = title2db('[Aptauja] ' . $_POST['new-poll-q']);
			$strid = mkslug_newpage($title);

			$db->query("INSERT INTO `pages` (strid,textid,category,text,title,author,date,bump,ip,lang)
									VALUES ('$strid','" . time() . "','".$polls_cat."','<p>" . $new_q . "</p>','$title','$auth->id',NOW(),NOW(),'$auth->ip','$lang')");

			$poll_page_id = $db->insert_id;

			$db->query("INSERT INTO `poll` (`name`,`topic`,`lang`) VALUES ('$new_q', '$poll_page_id', '$lang')");

			$poll_id = $db->insert_id;

			$html_answers = '';
			foreach ($_POST['new-poll-a'] as $new_a) {
				$new_a = trim($new_a);
				if (!empty($new_a)) {
					$new_a = sanitize($new_a);
					$html_answers .= '<li>' . $new_a . '</li>';
					$db->query("INSERT INTO `questions` (`pid`, `question`) VALUES ('$poll_id','$new_a')");
				}
			}

			$new_text = '<p><strong>' . $new_q . '</strong></p><ul>' . $html_answers . '</ul><p>(Atbildi aptaujā lapas malā)</p>';

			$db->query("UPDATE `pages` SET `text` = '$new_text' WHERE `id` = '$poll_page_id'");

			build_latest();
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
