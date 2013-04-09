<?php

$end = 400;

if (isset($_GET['skip'])) {
	$skip = (int) $_GET['skip'];
} else {
	$skip = 0;
}

$sfilter = '';
if (isset($_GET['var1']) && $_GET['var1'] == 'klase') {
	$showclass = (int) $_GET['var2'];
	$sfilter = " WHERE level = '" . $showclass . "' ";
}

$users = $db->get_results("SELECT nick,level,id FROM users " . $sfilter . " ORDER BY id ASC LIMIT $skip,$end");
if ($users) {

	$page_title = $page_title . ' | lapa ' . ($skip / $end + 1);

	foreach ($users as $user) {
		$tpl->newBlock('userlist-item');
		$tpl->assign(array(
			'nick' => usercolor($user->nick, $user->level, false, $user->id),
			'id' => $user->id
		));
	}

	$pager = pager($db->get_var("SELECT count(*) FROM users " . $sfilter), $skip, $end, '/lietotaji/?skip=');
	$tpl->assignGlobal(array(
		'pager-next' => $pager['next'],
		'pager-prev' => $pager['prev'],
		'pager-numeric' => $pager['pages']
	));
}
