<?php

/*
  warni by maadinsh
 */

$mod_levels = array(1, 2);

if ($inprofile = get_user(intval($_GET['var1']))) {

	$page_title = $inprofile->nick . ' brīdinājumi';

	if (in_array($inprofile->level, $mod_levels)) {
		$tpl->newBlock('warns-mod');
	} elseif ($auth->ok && (im_mod() || $auth->id == $inprofile->id)) {

		$warns = $db->get_results("SELECT * FROM `warns` WHERE `user_id` = '$inprofile->id' AND `site_id` = '$lang' ORDER BY `active` DESC, `created` DESC");
		//warnu saraksts
		$warn_count = 0;
		if (!empty($warns)) {
			$tpl->newBlock('warns-list');
			foreach ($warns as $warn) {
				if ($warn->active) {
					$tpl->newBlock('warns-active');
					$warn_count++;
				} else {
					$tpl->newBlock('warns-inactive');
				}
				$from = get_user($warn->created_by);
				$edit = '';
				if (im_mod()) {
					$edit = '[<a class="red" href="/' . $category->textid . '/' . $inprofile->id . '/edit/' . $warn->id . '">labot</a>]';
					$remove = '[<a class="red" href="/' . $category->textid . '/' . $inprofile->id . '/remove/' . $warn->id . '">noņemt</a>]';
				}
				$tpl->assign(array(
					'date' => display_time(strtotime($warn->created)),
					'reason' => add_smile($warn->reason),
					'remove_reason' => add_smile($warn->remove_reason),
					'author' => usercolor($from->nick, $from->level, false, $from->id),
					'aurl' => mkurl('user', $from->id, $from->nick),
					'edit' => $edit,
					'remove' => $remove,
				));
			}
			if ($warn_count > 0) {
				$tpl->assignGlobal('total_warns', ' (' . $warn_count . ')');
			}

			//awesome, lietotājam nav warnu
		} else {
			$tpl->newBlock('warns-nowarns');
			if ($auth->id == $inprofile->id) {
				$msg = 'Tev nav neviena brīdinājuma ;)';
			} else {
				$msg = 'Šim lietotājam nav neviena brīdinājuma.';
			}
			$tpl->assign('msg', $msg);
		}

		//pieglabā skaitu lietotāju tabulā, lai nav jātaisa count(*) uz katru ielādi
		if ($warn_count != $inprofile->warn_count) {
			$db->query("UPDATE `users` SET `warn_count` = '$warn_count' WHERE `id` = '$inprofile->id'");
			get_user($inprofile->id, true);
		}

		$bans = $db->get_results("SELECT * FROM `banned` WHERE `user_id` = '$inprofile->id' AND (`lang` = '$lang' OR `lang` = '0') ORDER BY `time` DESC");

		if (!empty($bans)) {
			foreach ($bans as $ban) {
				$from = get_user($ban->author);
				if (($ban->time + $ban->length) > time()) {
					$tpl->newBlock('bans-active');
				} else {
					$tpl->newBlock('bans-inactive');
				}
				$tpl->assign(array(
					'author' => usercolor($from->nick, $from->level, false, $from->id),
					'aurl' => mkurl('user', $from->id, $from->nick),
					'reason' => add_smile($ban->reason),
					'date' => display_time($ban->time),
					'length' => strTime($ban->length),
				));
			}
		} else {
			$tpl->newBlock('bans-no');
		}

		//pielikt warnu
		if (im_mod()) {

			//noņemt warnu
			if (isset($_GET['var2']) && $_GET['var2'] == 'remove') {
				$removable = (int) $_GET['var3'];
				$remove = $db->get_row("SELECT * FROM `warns` WHERE `user_id` = '$inprofile->id' AND `id` = '$removable' AND `site_id` = '$lang'");
				if ($remove) {
					$tpl->newBlock('warns-remove');
					$tpl->assign('reason', add_smile($remove->reason));
					if (isset($_POST['remove_warn']) && !empty($_POST['remove_reason'])) {
						$reason = post2db($_POST['remove_reason']);
						$db->query("UPDATE `warns` SET `removed_by` = '$auth->id', `remove_reason` = '$reason', `modified` = NOW(), `removed` = NOW(), `active` = '0' WHERE `id` = '$remove->id'");
						notify($inprofile->id, 11);
						$auth->log('Noņēma brīdinājumu', 'users', $inprofile->id);
						redirect('/' . $category->textid . '/' . $inprofile->id);
					}
				}
			} else {

				//pielikt & labot
				$tpl->newBlock('warns-edit');

				$edit = false;
				if (isset($_GET['var2']) && $_GET['var2'] == 'edit') {
					$editable = (int) $_GET['var3'];
					$edit = $db->get_row("SELECT * FROM `warns` WHERE `user_id` = '$inprofile->id' AND `id` = '$editable' AND `site_id` = '$lang'");
				}

				//labot esoso iemeslu
				if (!empty($edit)) {
					$tpl->assign('reason', $edit->reason);
					if (isset($_POST['submit_warn'])) {
						$reason = htmlpost2db($_POST['reason']);
						$db->query("UPDATE `warns` SET `edited_by` = '$auth->id', `reason` = '$reason', `modified` = NOW() WHERE `id` = '$edit->id'");
						$auth->log('Laboja brīdinājumu', 'users', $inprofile->id);
						redirect('/' . $category->textid . '/' . $inprofile->id);
					}

					//jauns warns
				} else {
					if (isset($_POST['submit_warn']) && !empty($_POST['reason'])) {
						$reason = post2db($_POST['reason']);
						$db->query("INSERT INTO `warns` (user_id,created_by,created,modified,reason,active,`site_id`) VALUES ('$inprofile->id','$auth->id',NOW(),NOW(),'$reason',1,'$lang')");
						notify($inprofile->id, 10);
						$auth->log('Izteica brīdinājumu', 'users', $inprofile->id);
						redirect('/' . $category->textid . '/' . $inprofile->id);
					}
				}
			}
		}
	} else {
		$tpl->newBlock('warns-noaccess');
	}
} else {
	$tpl->newBlock('error-nouser');
	$page_title = 'Kļūda: profils nav atrasts!';
}
