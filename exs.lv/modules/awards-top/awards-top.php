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
	foreach ($awards as $award) {
		$tpl->newBlock('aw-top');
		if ($award->c != $count) {
			$num = $i;
			$count = $award->c;
		}
		$tpl->assign([
			'nick' => $award->nick,
			'c' => $award->c,
			'num' => $num
		]);
		$i++;
	}
}

