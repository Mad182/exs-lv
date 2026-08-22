<?php

/** 
 *  Aptauju pārvaldība un jaunu aptauju iesniegšana
 */

if ($auth->ok) {

	$tpl->newBlock('polls_admin-body');

	$is_mod = im_mod();
	$pending_count = (int) $db->get_var("SELECT count(*) FROM `poll` WHERE `approved` = 0 AND `lang` = '$lang'");

	if ($is_mod) {
		$tpl->newBlock('polls_admin-tabs-mod');
		$tpl->assign('pending-count', $pending_count);
	} else {
		$tpl->newBlock('polls_admin-tabs-user');
	}

	$act = isset($_GET['act']) ? $_GET['act'] : '';

	// Mod atļautās darbības: approve un delete
	if ($is_mod && $act == 'approve' && isset($_GET['id'])) {
		$poll_id = (int) $_GET['id'];
		$poll = $db->get_row("SELECT * FROM `poll` WHERE `id` = '$poll_id' AND `approved` = 0 LIMIT 1");

		if ($poll) {
			// Izveido rakstu vai miniblogu diskusijai
			if ($lang == 9) {
				$mb_text = sanitize(trim($poll->name));
				$db->query("INSERT INTO `miniblog`
					(author, date, text, lang, bump)
					VALUES (
						'$rsbot_id',
						'".date("Y-m-d H:i:s")."',
						'".sanitize($mb_text)."',
						9,
						'".time()."'
					)
				");
				$poll_page_id = $db->insert_id;
			} else {
				$new_q = sanitize(trim($poll->name));
				$title = title2db('[Aptauja] ' . $poll->name);
				$strid = mkslug_newpage($title);
				$author_id = ($poll->author > 0) ? $poll->author : $auth->id;

				$db->query("INSERT INTO `pages` (strid,textid,category,text,title,author,date,bump,ip,lang)
					VALUES ('$strid','" . time() . "','" . $polls_cat . "','<p>" . $new_q . "</p>','$title','$author_id',NOW(),NOW(),'$auth->ip','$lang')");

				$poll_page_id = $db->insert_id;
			}

			// Formatē atbilžu variantus
			$questions = $db->get_results("SELECT * FROM `questions` WHERE `pid` = '$poll_id'");
			$html_answers = '';
			if ($questions) {
				foreach ($questions as $q) {
					$html_answers .= '<li>' . h($q->question) . '</li>';
				}
			}

			if ($lang == 9) {
				$new_text  = '<p class="mb-poll-question"><strong>Aptauja:</strong> '.h($poll->name).'</p>';
				$new_text .= '<ul class="mb-poll-li">' . $html_answers . '</ul>';
				$new_text .= '<p class="mb-poll-vote">Balsot iespējams lapas sānā redzamajā aptaujā.<br>Ieraksts uzģenerēts automātiski aptaujas rezultātu apspriešanai.</p>';
				$db->query("UPDATE `miniblog` SET `text` = '$new_text' WHERE `id` = '$poll_page_id'");
			} else {
				$new_text = '<p><strong>' . h($poll->name) . '</strong></p><ul>' . $html_answers . '</ul><p>(Atbildi aptaujā lapas malā)</p>';
				$db->query("UPDATE `pages` SET `text` = '$new_text' WHERE `id` = '$poll_page_id'");
			}

			// Atzīmē aptauju kā apstiprinātu
			$db->query("UPDATE `poll` SET `topic` = '$poll_page_id', `approved` = 1 WHERE `id` = '$poll_id'");

			if ($poll->author > 0) {
				update_karma($poll->author);
				if ($lang != 9 && !empty($strid)) {
					push('Aptauja apstiprināta: <a href="/read/' . $strid . '">' . h($poll->name) . '</a>', '/bildes/icons/award_star_gold_3.png', 'poll-' . $poll_id);
				}
			}

			set_flash('Aptauja apstiprināta un publicēta!', 'success');
		} else {
			set_flash('Aptauja nav atrasta vai jau apstiprināta.', 'error');
		}
		redirect('/polladmin?act=pending');
	}

	if ($is_mod && $act == 'delete' && isset($_GET['id'])) {
		$poll_id = (int) $_GET['id'];
		$poll = $db->get_row("SELECT * FROM `poll` WHERE `id` = '$poll_id' LIMIT 1");
		if ($poll) {
			$db->query("DELETE FROM `questions` WHERE `pid` = '$poll_id'");
			$db->query("DELETE FROM `poll` WHERE `id` = '$poll_id'");
			set_flash('Aptauja dzēsta.', 'success');
		}
		if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'act=list') !== false) {
			redirect('/polladmin?act=list');
		} else {
			redirect('/polladmin?act=pending');
		}
	}

	if ($is_mod && $act == 'pending') {

		$tpl->assign('pending-active', ' active');
		$tpl->newBlock('polls_admin-pending');

		$pending_polls = $db->get_results("SELECT * FROM `poll` WHERE `approved` = 0 AND `lang` = '$lang' ORDER BY `id` DESC");

		if ($pending_polls) {
			$tpl->newBlock('polls_admin-pending-list');
			foreach ($pending_polls as $p) {
				$tpl->newBlock('polls_admin-pending-node');

				// Atbildes
				$qs = $db->get_results("SELECT `question` FROM `questions` WHERE `pid` = '{$p->id}'");
				$q_list = [];
				if ($qs) {
					foreach ($qs as $q) {
						$q_list[] = h($q->question);
					}
				}

				// Autors
				$author_html = 'Anonīms';
				if ($p->author > 0) {
					$user = $db->get_row("SELECT `id`, `nick`, `level` FROM `users` WHERE `id` = '{$p->author}' LIMIT 1");
					if ($user) {
						$author_html = usercolor($user->nick, $user->level);
					}
				}

				$tpl->assign([
					'id' => $p->id,
					'question' => h($p->name),
					'answers' => implode(', ', $q_list),
					'author' => $author_html,
					'date' => !empty($p->created) ? date('d.m.Y H:i', strtotime($p->created)) : '-',
				]);
			}
		} else {
			$tpl->newBlock('polls_admin-pending-none');
		}

	} elseif ($is_mod && $act == 'list') {

		$tpl->assign('list-active', ' active');
		$polls = $db->get_results("SELECT * FROM `poll` WHERE `approved` = 1 AND `topic` != 0 AND `lang` = '$lang' ORDER BY `id` DESC");

		if ($polls) {
			$tpl->newBlock('polls_admin-list');
			foreach ($polls as $poll) {
				$tpl->newBlock('polls_admin-list-node');
				$topic_link = $poll->topic;
				if ($lang != 9) {
					$strid = get_page_strid($poll->topic);
					if ($strid) {
						$topic_link = '<a href="/read/' . $strid . '">' . $poll->topic . '</a>';
					}
				}
				$tpl->assign([
					'id' => $poll->id,
					'question' => h($poll->name),
					'topic' => $topic_link,
				]);
			}
		}

	} else {

		$tpl->assign('exist-active', ' active');

		if (isset($_POST['new-poll-q']) && isset($_POST['new-poll-a']) && is_array($_POST['new-poll-a'])) {

			$new_q = sanitize(trim($_POST['new-poll-q']));

			$clean_answers = [];
			foreach ($_POST['new-poll-a'] as $ans) {
				$ans = trim($ans);
				if (!empty($ans)) {
					$clean_answers[] = sanitize($ans);
				}
			}

			if (empty($new_q) || count($clean_answers) < 2) {
				set_flash('Lūdzu ievadiet jautājumu un vismaz divus atbilžu variantus!', 'error');
				redirect('/polladmin');
			}

			if ($is_mod) {
				// Moderators veido aptauju - tiešā publicēšana
				if ($lang == 9) {
					$mb_text = sanitize(trim($new_q));
					$db->query("INSERT INTO `miniblog`
						(author, date, text, lang, bump)
						VALUES (
							'$rsbot_id',
							'".date("Y-m-d H:i:s")."',
							'".sanitize($mb_text)."',
							9,
							'".time()."'
						) 
					");
					$poll_page_id = $db->insert_id;
				} else {
					$title = title2db('[Aptauja] ' . $_POST['new-poll-q']);
					$strid = mkslug_newpage($title);

					$db->query("INSERT INTO `pages` (strid,textid,category,text,title,author,date,bump,ip,lang)
						VALUES ('$strid','" . time() . "','" . $polls_cat . "','<p>" . $new_q . "</p>','$title','$auth->id',NOW(),NOW(),'$auth->ip','$lang')");

					$poll_page_id = $db->insert_id;            
				}

				$db->query("INSERT INTO `poll` (`name`,`topic`,`lang`,`author`,`approved`,`created`) VALUES ('$new_q', '$poll_page_id', '$lang', '$auth->id', 1, NOW())");
				$poll_id = $db->insert_id;

				$html_answers = '';
				foreach ($clean_answers as $new_a) {
					$html_answers .= '<li>' . $new_a . '</li>';
					$db->query("INSERT INTO `questions` (`pid`, `question`) VALUES ('$poll_id','$new_a')");
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
				$tpl->assign('success-message', 'Jautājums izveidots...<br>Komentāru tēma izveidota...<br>Atbilžu varianti izveidoti...');
			} else {
				// Parastais lietotājs - nosūta uz pārbaudi (approved = 0)
				$db->query("INSERT INTO `poll` (`name`,`topic`,`lang`,`author`,`approved`,`created`) VALUES ('$new_q', 0, '$lang', '$auth->id', 0, NOW())");
				$poll_id = $db->insert_id;

				foreach ($clean_answers as $new_a) {
					$db->query("INSERT INTO `questions` (`pid`, `question`) VALUES ('$poll_id','$new_a')");
				}

				$tpl->newBlock('polls_admin-success');
				$tpl->assign('success-message', 'Paldies! Tava aptauja ir veiksmīgi iesniegta un tiks publicēta pēc moderatoru apstiprināšanas.');
			}

		} else {
			$tpl->newBlock('polls_admin-add');
		}

	}

} else {
	set_flash('Lai izveidotu aptauju, lūdzu autorizējies!', 'error');
	redirect();
}
