<?php
/**
 *	
 *
 *	Moduļa adrese: runescape.exs.lv/
 */
 
if ( !isset($sub_include) ) {
    redirect();
}

/* ----------------------------------------------------------------------- */
//	 Saraksts ar rakstiem, kuriem iekš `pages` ir spec. pamācību sadaļa
/* ---------------------------------------------------------------------- */ 
/*if ($_GET['var1'] == 'inpages' && $auth->id == 115) {
    exit;
	$all_items = $db->get_results("SELECT `strid`,`title`,`author`,`category`,`rsclass` FROM `pages` WHERE `rsclass` != '0' ORDER BY `rsclass` ASC, `title` ASC");
	if ($all_items) {
		$cat = '';
		$tpl->newBlock('rsmod-pagelist');
		foreach ($all_items as $item => $data) {
			if ($cat != $data->rsclass) {
				$tpl->assign('border', ' style="border-bottom:2px solid #bbb;"');
			}
			$cat = $data->rsclass;
			if ($user = get_user($data->author)) {
				$data->author = '<a style="font-size:11px;" href="' . mkurl('user', $user->id, $user->nick) . '">' . usercolor($user->nick, $user->level) . '</a>';
			}
			$tpl->newBlock('pagelist-listitem');
			$tpl->assignAll($data);
		}
	}
}
*/
/* ----------------------------------------------------- */
//	 questlist & rediģēšana
/* ---------------------------------------------------- */

// update
/*else if ($_GET['var1'] == 'quest' && isset($_GET['var2'])) {
    exit;
	$id = (int) $_GET['var2'];
	if ($db->get_var("SELECT count(*) FROM `rs_help` WHERE `page_id` = '$id' ") == 1) {
		$short_date = substr(sanitize($_POST['date']), -2, 2);
		$db->query("UPDATE `rs_help` SET
		  `location` = '" . sanitize($_POST['location']) . "',
		  `skills` = '" . sanitize($_POST['skills']) . "',
		  `quests` = '" . sanitize($_POST['quests']) . "',
		  `extra` = '" . sanitize($_POST['extra']) . "',
		  `date` = '" . sanitize($_POST['date']) . "',
		  `year` = '$short_date',
		  `difficulty` = '" . (int) $_POST['difficulty'] . "',
		  `length` = '" . (int) $_POST['length'] . "',
		  `storyline` = '" . (int) $_POST['storyline'] . "',
		  `ready` = '1',
		  `edit_user` = '" . $auth->id . "',
		  `description` = '" . sanitize($_POST['description']) . "',
		  `old` = '" . (int) $_POST['old'] . "'
		WHERE `page_id` = '$id'");
	}
	header("Location: /" . $_GET['viewcat'] . "/qedit");
} 

else if ($_GET['var1'] == 'qedit') {
    exit;

	// dzēšana
	if (isset($_GET['delete']) && $auth->id == 115) {
		$id = (int) $_GET['delete'];
		$del = $db->query("DELETE FROM `rs_help` WHERE `page_id` = '$id' LIMIT 1");
		header("Location: /" . $_GET['viewcat'] . "/qedit/show");
	}


	// konkrēta kvesta/minikvesta rediģēšana
	if (isset($_GET['var2']) && $_GET['var2'] != 'update' && $_GET['var2'] != 'show') {
		$levels = array(array(1, 'Viegls'), array(2, 'Vidējs'), array(3, 'Grūts'), array(4, 'Master'), array(5, 'Grandmaster'), array(6, 'Special'));
		$length = array(array(1, 'Īss'), array(2, 'Vidējs'), array(3, 'Garš'), array(4, 'Ļoti garš'), array(5, 'Ļoti, ļoti garš'));
		$old = array(array(1, 'Need HD'), array(2, 'Need New'));

		$guide_id = (int) $_GET['var2'];
		if ($guide = $db->get_row("SELECT `id`,`strid`,`title` FROM `pages` WHERE `id` = '$guide_id' LIMIT 1")) {
			// AND `category` IN ('99','100','193')
			$info = $db->get_row("SELECT * FROM `rs_help` WHERE `page_id` = '$guide_id' ORDER BY `id` DESC");
			if ($info) {
				$tpl->newBlock('rsmod-questedit');
				$tpl->assignAll($info); // nemainīt vietām! svarīgi, kuru ID assigno pēdējo
				$tpl->assignAll($guide);
				// izvēlne ar Questu sērijām
				$storylines = $db->get_results("SELECT `id`,`title` FROM `rs_classes` WHERE `cat` = 'series' ORDER BY `title` ASC ");
				foreach ($storylines as $storyline => $data) {
					$tpl->newBlock('rsmod-guide-storyline');
					$tpl->assign(array(
						'nr' => $data->id,
						'storyline' => $data->title
					));
					if ($data->id == $info->storyline) {
						$tpl->assign('selected', ' selected="selected"');
					}
				}
				// izvēlne ar sarežģītības līmeņiem
				foreach ($levels as $level) {
					$tpl->newBlock('rsmod-guide-difficulty');
					$tpl->assign(array(
						'nr' => $level[0],
						'level' => $level[1]
					));
					if ($level[0] == $info->difficulty) {
						$tpl->assign('selected', ' selected="selected"');
					}
				}
				// izvēlne ar kvesta ilgumu
				foreach ($length as $single) {
					$tpl->newBlock('rsmod-guide-length');
					$tpl->assign(array(
						'nr' => $single[0],
						'length' => $single[1]
					));
					if ($single[0] == $info->length) {
						$tpl->assign('selected', ' selected="selected"');
					}
				}
				// kvests novecojis, need HD pics or sth
				foreach ($old as $older) {
					$tpl->newBlock('rsmod-guide-age');
					$tpl->assign(array(
						'nr' => $older[0],
						'old' => $older[1]
					));
					if ($older[0] == $info->old) {
						$tpl->assign('selected', ' selected="selected"');
					}
				}*//*
			} else {
				header("Location: /" . $_GET['viewcat'] . "/qedit");
			}
		} else {
			header("Location: /" . $_GET['viewcat'] . "/qedit");
		}
	}


	else {
		$cats = array(array(100, 'members quests'), array(99, 'free quests'), array(193, 'miniquests'));
		$levels = array(1 => 'easy', 2 => 'medium', 3 => 'hard',
			4 => '<span style="color:#2777aa;text-transform:uppercase;">Master</span>',
			5 => '<span style="color:#e93546;text-transform:uppercase;">Grandmaster</span>',
			6 => '<span style="color:#e453e2;text-transform:uppercase;">Special</span>'
		);
		$diffs = array(1, 2, 3, 4, 5, 6);

		foreach ($cats as $cat) {
			$all_quests = $db->get_results("SELECT * FROM `rs_help` WHERE `cat` = '" . $cat[0] . "' ORDER BY `title` ASC ");
			if ($all_quests) {
				$tpl->newBlock('rsmod-questlist');
				$tpl->assign('cat-title', $cat[1]);
				foreach ($all_quests as $quest) {
					//$quest->ready = ($quest->ready == '1') ? '<img src="/bildes/rs/tick.png" />' : '<img src="/bildes/rs/cross.png" />';
					if ($user = get_user($quest->auth)) {
						$quest->auth = '<a style="font-size:11px;" href="' . mkurl('user', $user->id, $user->nick) . '">' . usercolor($user->nick, $user->level) . '</a>';
					}
					$tpl->newBlock('questlist-quest');
					$tpl->assignAll($quest);
					if (in_array($quest->difficulty, $diffs)) {
						$tpl->assign('level', $levels[$quest->difficulty]);
					}
					if (isset($_GET['var2']) && $_GET['var2'] == 'show' && $auth->id == '115') {
						$check = $db->get_var("SELECT count(*) FROM `pages` WHERE `id` = '$quest->page_id' AND `category` IN ('99','100','193') ");
						if ($check != 1) {
							$tpl->newBlock('quest-delete');
							$tpl->assign('page_id', $quest->page_id);
						}
					}
				}
			}
		}
	}
}*/

	
/* ----------------------------------------------------- */
//	 update
/* ---------------------------------------------------- */ 
/*else if ($_GET['var1'] == 'update' && in_array($auth->id,array(115,140))) {
    exit;
	$pages = $db->get_results("SELECT `page_id`,`strid`,`title`,`auth` FROM `rs_help` ");
	if ($pages) {
		foreach ($pages as $page) {
			$check = $db->get_row("SELECT `id`,`strid`,`title`,`author` FROM `pages` WHERE `id` = '$page->page_id' LIMIT 1");
			if ($check) {
				$string = array();
				if ($check->title != $page->title) {
					$string[] = "`title` = '" . sanitize($check->title) . "'";
				}
				if ($check->strid != $page->strid) {
					$string[] = "`strid` = '" . sanitize($check->strid) . "'";
				}
				if ($check->author != $page->auth) {
					$string[] = "`auth` = '" . (int) $check->author . "'";
				}
				if (!empty($string)) {
					$upd = $db->query("UPDATE `rs_help` SET " . implode(',', $string) . " WHERE `page_id` = '$page->page_id' ");
				}
			}
		}
	}
}
*/