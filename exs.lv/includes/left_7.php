<?php

$sel = 'pages';
if (!empty($_COOKIE['last-sidebar-tab']) && $_COOKIE['last-sidebar-tab'] == 'gallery') {
	$out = get_latest_images();
	$sel = 'gallery';
} else {
	$out = get_latest_posts();
}

$tpl->newBlock('main-layout-left');
$tpl->assign([
	'latest-noscript' => $out,
	$sel . '-selected' => 'active ',
]);
unset($out);

//top users
$tusers = $db->get_results("SELECT `id`,`nick`,`today`,`level`,`av_alt`,`avatar` FROM `users` WHERE `today` > 0 ORDER BY `today` DESC LIMIT 9");
if ($tusers) {
	$tpl->newBlock('user-top');
	foreach ($tusers as $tuser) {
		$tpl->newBlock('user-top-node');
		$tpl->assign([
			'user' => usercolor($tuser->nick, $tuser->level, false, $tuser->id),
			'url' => '/user/' . $tuser->id,
			'today' => $tuser->today,
			'avatar' => get_avatar($tuser, 's')
		]);
	}
}
unset($tusers);

//lietotāja notifikācijas
if ($auth->ok === true) {
	if ($html = get_notify($auth->id)) {
		$tpl->newBlock('notification-list');
		$tpl->assign('out', $html);
		unset($html);
	}
}

