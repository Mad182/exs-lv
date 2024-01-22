<?php

$tpl->newBlock('statistics-body');

$spamers = $db->get_results("SELECT `nick`,`level`,`id`,`posts` FROM `users` WHERE `deleted` = 0 ORDER BY `posts` DESC LIMIT 100");
if ($spamers) {
	foreach ($spamers as $spamer) {
		$tpl->newBlock('spamerlist-node');
		$tpl->assign([
			'spamer-nick' => usercolor($spamer->nick, $spamer->level),
			'url' => '/user/' . $spamer->id,
			'spamer-posts' => $spamer->posts
		]);
	}
}

$spamers = $db->get_results("SELECT `nick`,`level`,`id`,`karma` FROM `users` WHERE `deleted` = 0 ORDER BY `karma` DESC LIMIT 100");
if ($spamers) {
	foreach ($spamers as $spamer) {
		$tpl->newBlock('karma-node');
		$tpl->assign([
			'spamer-nick' => usercolor($spamer->nick, $spamer->level),
			'url' => '/user/' . $spamer->id,
			'karma' => $spamer->karma
		]);
	}
}

