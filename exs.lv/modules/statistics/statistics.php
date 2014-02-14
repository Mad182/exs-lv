<?php

$tpl->newBlock('statistics-body');

$tpl->assign(array(
	'statistics-users' => $db->get_var("SELECT count(*) FROM users"),
	'statistics-pages' => $db->get_var("SELECT count(*) FROM pages"),
	'statistics-images' => $db->get_var("SELECT count(*) FROM images"),
	'statistics-miniblog' => $db->get_var("SELECT max(id) FROM miniblog"),
	'statistics-comments' => $db->get_var("SELECT count(*) FROM comments") + $db->get_var("SELECT count(*) FROM galcom"),
	'statistics-pms' => $db->get_var("SELECT max(id) FROM pm")
));

$spamers = $db->get_results("SELECT nick,level,id,posts FROM users ORDER BY posts DESC LIMIT 100");
if ($spamers) {
	foreach ($spamers as $spamer) {
		$tpl->newBlock('spamerlist-node');
		$tpl->assign(array(
			'spamer-nick' => usercolor($spamer->nick, $spamer->level),
			'url' => '/user/' . $spamer->id,
			'spamer-posts' => $spamer->posts
		));
	}
}

$spamers = $db->get_results("SELECT nick,level,id,karma FROM users ORDER BY karma DESC LIMIT 100");
if ($spamers) {
	foreach ($spamers as $spamer) {
		$tpl->newBlock('karma-node');
		$tpl->assign(array(
			'spamer-nick' => usercolor($spamer->nick, $spamer->level),
			'url' => '/user/' . $spamer->id,
			'karma' => $spamer->karma
		));
	}
}

$tusers = $db->get_results("SELECT id,nick,today,level FROM users WHERE today > 0 ORDER BY today DESC, lastseen DESC LIMIT 10");
if ($tusers) {
	$self = false;
	$tpl->newBlock('usertop');
	foreach ($tusers as $tuser) {
		$tpl->newBlock('usertop-node');
		$tpl->assign(array(
			'user' => usercolor($tuser->nick, $tuser->level, false, $tuser->id),
			'url' => '/user/' . $tuser->id,
			'today' => $tuser->today
		));
		if ($tuser->id == $auth->id) {
			$self = true;
		}
	}

	if ($auth->ok && !$self) {
		$tpl->newBlock('usertop-self');
		$tpl->assign(array(
			'user' => $auth->nick,
			'today' => $db->get_var("SELECT today FROM users WHERE id = '$auth->id'")
		));
	}
}
