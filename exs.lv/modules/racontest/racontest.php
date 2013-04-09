<?php

if (im_mod() || $auth->level == 3) {
	if (isset($_POST['start']) && isset($_POST['end'])) {
		$start = date('Y-m-d H:i:s', strtotime($_POST['start']));
		$end = date('Y-m-d H:i:s', strtotime($_POST['end']));
	} else {
		$start = date('Y-m-d 00:00:00', time() - 604800);
		$end = date('Y-m-d 23:59:59', time());
	}
	$dont_use = array(2, 12, 13, 77, 78, 244, 6);
	/* 2 - aptauju komentāri, 12 - saites, 13 - kontakti,
	  77 - WAP špikeri, 78 - lekciju pieraksti, 244 - nekur nederīgie raksti, 6 - musars */

	$categorys = $db->get_results("SELECT `id`,`title` FROM `cat` WHERE module IN('list','index','rshelp','movies') AND `isblog` = '0' AND `isforum` = '0' AND id NOT IN(" . implode(',', $dont_use) . ")");
	$tpl->newBlock('ra-contest');
	$tpl->assign(array(
		'start' => $start,
		'end' => $end,
		'articles-catid' => $category->id,
		'articles-title' => $category->title
	));
	if ($categorys) {
		foreach ($categorys as $category_l) {
			$get_topics = $db->get_results("SELECT `strid`,`title`,`author` FROM `pages` WHERE `category` = '" . $category_l->id . "' AND `date` >= '$start' AND `date` <= '$end' ORDER BY `date` DESC LIMIT 50");

			if ($get_topics) {
				$tpl->newBlock('contest-cat');
				$tpl->assign('contest-ctitle', $category_l->title);

				foreach ($get_topics as $topic) {
					if ($user = get_user($topic->author)) {
						$topic->addedby = '<a href="' . mkurl('user', $user->id, $user->nick) . '">' . usercolor($user->nick, $user->level) . '</a>';
					}
					$tpl->newBlock('contest-topic');
					$tpl->assignAll($topic);
				}
			}
		}
	}
}
/*
  4 - prasmes
  5 - padomi
  99 - f2p kvesti
  100 - p2p kvesti
  mini-kvesti - 193
  195 - ceļveži
  194 - ach diary
  160 - minigames
  9 rīki
  288 web padomi
  8 - old flash games
  81 - spēles

  169 farming
  170 melee
  171 thieving
  172 firemaking
  173 fishing
  174 mining
  175 cooking
  176 prayer
  177 slayer
  178 woodcutting
  179 magic
  180 crafting
  181 summoning
  182 construction
  183 ranged
  185 agility
  186 herblore
  187 hunter
  188 fletching
  189 smithing
  190 runecrafting
  191 hitpoints
  287 dungeoneering
*/
