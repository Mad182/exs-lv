<?php

/**
 * Saņemto medaļu tops -
 * lietotāji ar visvairāk medaļām
 */
$awards = $db->get_results("SELECT COUNT(user_id) AS c, user_id FROM autoawards GROUP BY user_id ORDER BY c DESC LIMIT 200");

$i = 1;
$num = 1;
$count = 0;

foreach ($awards as $award) {
	$tpl->newBlock('aw-top');
	if ($award->c != $count) {
		$num = $i;
		$count = $award->c;
	}
	$user = get_user($award->user_id);
	$tpl->assign(array(
		'nick' => $user->nick,
		'c' => $award->c,
		'num' => $num
	));
	$i++;
}
