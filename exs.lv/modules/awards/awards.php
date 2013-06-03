<?php

$robotstag[] = 'noindex';

if (isset($_GET['var1'])) {
	$userid = (int) $_GET['var1'];
	$inprofile = get_user($userid);
} elseif ($auth->ok) {
	$inprofile = get_user($auth->id);
}

if ($inprofile) {

	if ($inprofile->id == $auth->id || $debug) {

		update_awards($auth->id);

		if (isset($_POST['position'])) {
			$i = count($_POST['position']);
			foreach ($_POST['position'] as $pos) {
				$num = intval(str_replace('award-pos-', '', $pos));
				if ($num) {
					$db->query("UPDATE `autoawards` SET `importance` = '$i' WHERE `id` = '$num' AND `user_id` = '$inprofile->id'");
					$i--;
				}
			}
			$m->delete('aw_' . $inprofile->id);
			echo 'ok';
			exit;
		}

		$tpl->assignInclude('module-head', 'modules/' . $category->module . '/head.tpl');
		$tpl->prepare();
	}

	if ($auth->ok) {
		set_action($inprofile->nick . ' medaļas');
	}

	$tpl->newBlock('profile-menu');
	$tpl->assign('user-menu-add', ' medaļas');
	$page_title = $inprofile->nick . ' exs apbalvojumi';
	$tpl->assignGlobal(array(
		'user-id' => $inprofile->id,
		'user-nick' => htmlspecialchars($inprofile->nick),
		'active-tab-awards' => 'active'
	));
	$tpl->newBlock('user-awards');

	$awards = get_awards($inprofile->id);
	$existing_awards = array();

	if (!empty($awards)) {

		$tpl->newBlock('user-awards-list');
		$tpl->assign(array(
			'total' => count($awards)
		));

		foreach ($awards as $award) {
			$tpl->newBlock('user-awards-node');
			$tpl->assign(array(
				'id' => $award->id,
				'award' => $award->award,
				'title' => $award->title,
				'importance' => $award->importance,
				'created' => $award->created,
			));
			if ($auth->id == $inprofile->id || $debug) {
				$tpl->assign('cursor', 'cursor:move;');
			}
			$existing_awards[] = $award->award;
		}
	} else {
		$tpl->newBlock('user-awards-none');
	}

	$awards_list = list_awards();

	$tpl->newBlock('user-awards-free');
	foreach ($awards_list as $key => $val) {
		//ja lietotājam jau ir šāds awards, neko nedaram
		if (!in_array($key, $existing_awards)) {
			$tpl->newBlock('user-awards-free-node');
			$tpl->assign(array(
				'award' => $key,
				'title' => $val['title']
			));
			if ($auth->id == $inprofile->id) {
				$tpl->assign('add', '&nbsp;<a class="clue" href="javascript:void();" rel="/award-info/' . $key . '?_='.time().'">(?)</a>');
			}
		}
	}
	$pagepath = '';
} else {
	redirect();
}
