<?php

/**
 * Grupu saraksts
 */
$end = 50;

if (isset($_GET['skip'])) {
	$skip = (int) $_GET['skip'];
} else {
	$skip = 0;
}

$total = $db->get_var("SELECT count(*) FROM clans WHERE `archived` = 0 AND `members` > 4 AND `posts` > 49 AND (`members` > 19 OR `posts` > 99) AND `list` = 1 AND `lang` = '$lang'");

$groups = $db->get_results("SELECT id,title,avatar,posts,members,owner FROM clans WHERE `archived` = 0 AND `members` > 4 AND `posts` > 49 AND (`members` > 19 OR `posts` > 99) AND `list` = 1 AND `lang` = '$lang' ORDER BY members DESC, posts DESC");

$i = 0;
foreach ($groups as $group) {

	$i++;

	if ($i < $skip || $i > $skip + $end) {
		continue;
	}

	$user = $db->get_row("SELECT id,seenposts FROM clans_members WHERE clan = '$group->id' AND user = '$auth->id' AND approve = '1'");
	if (!empty($user)) {
		$istyle = ' style="background:green;padding:3px" ';
		if (($group->posts - $user->seenposts) > 0) {
			$add = '&nbsp;(<a style="font-size: 16px;" href="/group/' . $group->id . '/forum/"><span class="red">' . ($group->posts - $user->seenposts) . '</span></a>)';
		} else {
			$add = '';
		}
		if ($group->avatar == '') {
			$group->avatar = 'none.png';
		}
		$tpl->newBlock('allgroups');
		$tpl->assign([
			'title' => $group->title,
			'id' => $group->id,
			'avatar' => $group->avatar,
			'posts' => $group->posts,
			'members' => $group->members + 1,
			'style' => $istyle,
			'add' => $add,
		]);
	} else {
		if ($group->avatar == '') {
			$group->avatar = 'none.png';
		}
		$tpl->newBlock('allgroups2');
		$tpl->assign([
			'title' => $group->title,
			'id' => $group->id,
			'avatar' => $group->avatar,
			'posts' => $group->posts,
			'members' => $group->members + 1,
			'style' => '',
			'add' => '',
		]);
	}
}

$pager = pager($total, $skip, $end, '/grupas/?skip=');
$tpl->assignGlobal([
	'pager-next' => $pager['next'],
	'pager-prev' => $pager['prev'],
	'pager-numeric' => $pager['pages']
]);
