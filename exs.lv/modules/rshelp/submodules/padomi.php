<?php

/**
 * 	RuneScape padomi
 */
!isset($sub_include) and die('No hacking, pls.');

$tpl_options = '';


$get_blocks = $db->get_results("SELECT `id`,`title`,`klase` FROM `rs_classes` WHERE `category` = 'other' ORDER BY `ordered` ASC");

if ($get_blocks) {

	$tpl->newBlock('rshelp-blocklist-outer');

	foreach ($get_blocks as $data) {

		$get_guides = $db->get_results("SELECT `id`,`strid`,`title`,`author` FROM `pages` WHERE `category` = 5 AND `rsclass` = '" . $data->id . "' ORDER BY `title` ASC");

		if ($get_guides) {

			$class = ($data->klase != '') ? ' class="rshelp-' . $data->klase . '"' : '';

			$tpl->newBlock('rshelp-blocklist');
			$tpl->assign('blocklist-title', $data->title);

			foreach ($get_guides as $guidedata) {

				// $db->query("UPDATE `pages` SET `rsclass` = '14' WHERE `id` = '".$guidedata->id."' AND `title` like '%naudas peln%' ");

				if ($user = get_user($guidedata->author)) {
					$addedby = '<a style="font-size:11px;" href="' . mkurl('user', $user->id, $user->nick) . '">' . usercolor($user->nick, $user->level) . '</a>';
				}

				$tpl->newBlock('rshelp-blocklistitem');
				$tpl->assign(array(
					'guide-strid' => $guidedata->strid,
					'guide-title' => $guidedata->title,
					'guide-author' => $addedby,
					'rshelp-class' => $class
				));
			}
		}
	}
}

$all_items = $db->get_results("SELECT `strid`,`title`,`author` FROM `pages` WHERE `category` = 5 AND `rsclass` = 0 ORDER BY `title` ASC");

if ($all_items) {

	$tpl->newBlock('rshelp-list');
	$tpl->assign(array(
		'articles-catid' => '5',
		'articles-title' => 'Padomi'
	));

	foreach ($all_items as $item => $data) {

		// $db->query("UPDATE `pages` SET `rsclass` = '18' WHERE `strid` = '".$data->strid."' AND `title` like '%RuneScape legend%' ");

		if ($user = get_user($data->author)) {
			$data->author = '<a style="font-size:11px;" href="' . mkurl('user', $user->id, $user->nick) . '">' . usercolor($user->nick, $user->level) . '</a>';
		}

		$tpl->newBlock('rshelp-listitem');
		$tpl->assignAll($data);
	}
}