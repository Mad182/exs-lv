<?php

/**
 * Saņemto medaļu tops -
 * lietotāji ar visvairāk medaļām
 */
global $db, $m, $tpl;

$awards = $m->get('awards_top_200');
if ($awards === false) {
	$awards = $db->get_results("SELECT COUNT(a.user_id) AS c, a.user_id, u.nick FROM autoawards a JOIN users u ON u.id = a.user_id WHERE u.deleted = 0 GROUP BY a.user_id ORDER BY c DESC LIMIT 200");
	if ($awards) {
		$m->set('awards_top_200', $awards, 3600);
	}
}

$i = 1;
$num = 1;
$count = 0;

if (!empty($awards)) {
	$half = (int) ceil(count($awards) / 2);
	foreach ($awards as $award) {
		$block = ($i <= $half) ? 'aw-top-left' : 'aw-top-right';
		$tpl->newBlock($block);
		if ($award->c != $count) {
			$num = $i;
			$count = $award->c;
		}
		$tpl->assign([
			'url' => '/user/' . $award->user_id,
			'nick' => usercolor($award->nick, $award->level),
			'c' => $award->c,
			'num' => $num
		]);
		$i++;
	}
}

