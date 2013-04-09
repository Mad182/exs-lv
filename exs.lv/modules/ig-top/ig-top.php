<?php

$topusers = $db->get_results("SELECT * FROM users WHERE ig_done > '0' AND id != '10136' AND id != '6220' AND id != '928' AND id != '5999' AND id != '5390' ORDER BY ig_points DESC,ig_done ASC LIMIT 100");
if ($topusers) {
	$i = 1;
	foreach ($topusers as $topuser) {
		$special = '';
		if ($auth->id == $topuser->id) {
			$special = ' style="background: #ffffaa;font-weight: bold;"';
		}
		if ($i == 1) {
			$icon = '<img src="/bildes/icons/award_star_gold_3.png" alt="' . $i . '." title="' . $i . '." />';
		} elseif ($i == 2) {
			$icon = '<img src="/bildes/icons/award_star_silver_3.png" alt="' . $i . '." title="' . $i . '." />';
		} elseif ($i == 3) {
			$icon = '<img src="/bildes/icons/award_star_bronze_3.png" alt="' . $i . '." title="' . $i . '." />';
		} else {
			$icon = $i . '.';
		}

		$tpl->newBlock('top-node');
		$tpl->assign(array(
			'user-place' => $icon,
			'user-special' => $special,
			'user-id' => $topuser->id,
			'user-nick' => $topuser->nick,
			'user-ig_points' => $topuser->ig_points,
			'user-ig_done' => $topuser->ig_done,
		));
		$i++;
	}
}
