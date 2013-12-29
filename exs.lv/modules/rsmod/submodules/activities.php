<?php
/**
 *	RuneScape aktivitāšu pārvaldība.
 *
 *  
 *
 *	Moduļa adrese: runescape.exs.lv/series
 */
 
if ( !isset($sub_include) ) {
    set_flash('No hacking, pls.');
    redirect();
}

if ($_GET['var1'] == 'aedit' && $auth->id == 115) {

    exit;
	// update
	if (isset($_GET['update'])) {
		$id = (int) $_GET['update'];
		if ($db->get_var("SELECT count(*) FROM `rs_help` WHERE `page_id` = '$id' ") == 1) {
			$db->query("UPDATE `rs_help` SET
				`location` = '" . sanitize($_POST['location']) . "',
				`p2p` = '" . (int) $_POST['members'] . "',
				`ready` = '1',
				`edit_user` = '$auth->id',
				`description` = '" . sanitize($_POST['description']) . "',
				`old` = '" . (int) $_POST['old'] . "'
			  WHERE `page_id` = '$id'");
		}
		header("Location: /" . $_GET['viewcat'] . "/aedit");
	}
	// dzēšana
	if (isset($_GET['delete']) && $auth->id == '115') {
		$id = (int) $_GET['delete'];
		$del = $db->query("DELETE FROM `rs_help` WHERE `page_id` = '$id' LIMIT 1");
		header("Location: /" . $_GET['viewcat'] . "/aedit/show");
	}
	// rediģēšanas forma
	if (isset($_GET['var2']) && $_GET['var2'] != 'update' && $_GET['var2'] != 'show') {
		$old = array(array(1, 'Need HD'), array(2, 'Need New'));
		$guide_id = (int) $_GET['var2'];
		if ($guide = $db->get_row("SELECT `id`,`strid`,`title` FROM `pages` WHERE `id` = '$guide_id' LIMIT 1")) {
			$info = $db->get_row("SELECT * FROM `rs_help` WHERE `page_id` = '$guide_id' ");
			if ($info) {
				$tpl->newBlock('rsmod-activities-edit');
				$tpl->assignAll($info); // nemainīt vietām! svarīgi, kuru ID assigno pēdējo
				$tpl->assignAll($guide);
				if ($info->p2p == '1') {
					$tpl->assign('selected-members', ' selected="selected"');
				}
				foreach ($old as $older) {
					$tpl->newBlock('activity-age');
					$tpl->assign(array(
						'nr' => $older[0],
						'old' => $older[1]
					));
					if ($older[0] == $info->old) {
						$tpl->assign('selected', ' selected="selected"');
					}
				}
				/* -- */
			} else {
				header("Location: /" . $_GET['viewcat'] . "/aedit");
			}
		} else {
			header("Location: /" . $_GET['viewcat'] . "/aedit");
		}
	}
	// saraksts
	else {
		$cats = array(array(792, 'Distractions & Diversions'), array(160, 'minigames'));
		foreach ($cats as $cat) {
			$activities = $db->get_results("SELECT * FROM `rs_help` WHERE `cat` = '" . $cat[0] . "' ORDER BY `title` ASC ");
			if ($activities) {
				$tpl->newBlock('rsmod-activities');
				$tpl->assign('cat-title', $cat[1]);
				foreach ($activities as $activity) {
					if ($user = get_user($activity->auth)) {
						$activity->auth = '<a style="font-size:11px;" href="' . mkurl('user', $user->id, $user->nick) . '">' . usercolor($user->nick, $user->level) . '</a>';
					}
					$tpl->newBlock('activity');
					$tpl->assignAll($activity);
					if (isset($_GET['var2']) && $_GET['var2'] == 'show' && $auth->id == '115') {
						$check = $db->get_var("SELECT count(*) FROM `pages` WHERE `id` = '$activity->page_id' AND `category` IN ('160','792') ");
						if ($check == 1) {
							$tpl->newBlock('activity-delete');
							$tpl->assign('page_id', $activity->page_id);
						}
					}
				}
			}
		}
	}
}