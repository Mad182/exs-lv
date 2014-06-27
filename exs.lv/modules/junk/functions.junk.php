<?php

/**
 * Parāda datumu "Šodien/Vakar/date"
 */
function display_date_simple($time) {
	if (!$time) {
		$out = '';
	} elseif ($time >= strtotime('today')) {
		$out = 'Šodien';
	} elseif ($time >= strtotime('yesterday')) {
		$out = 'Vakar';
	} else {
		$out = date('d.m.Y.', $time);
	}
	return $out;
}

/**
 * Attēla vērtēšana
 */
function junk_vote($pic, $user) {
	global $auth, $db, $remote_salt;
	$out = '<div id="junk-voter">';

	$voted = $db->get_row("SELECT * FROM `junk_votes` WHERE	`junk_id` = $pic AND `user_id` = $user");
	$votes = $db->get_row("SELECT count(*) AS `count`, SUM(`value`) AS `sum` FROM `junk_votes` WHERE `junk_id` = $pic");

	if ($auth->ok) {
		if ($voted) {
			$out .= '<span class="value">' . (int) $votes->sum . '<small>/' . (int) $votes->count . '</small></span>';
			$out .= '<span class="uplink';
			if ($voted->value == 1) {
				$out .= ' active';
			}
			$out .= '"></span>';
			$out .= '<span class="downlink';
			if ($voted->value == -1) {
				$out .= ' active';
			}
			$out .= '"></span>';
		} else {
			$out .= '<span class="value">' . (int) $votes->sum . '<small>/' . (int) $votes->count . '</small></span>';
			$out .= '<a class="uplink" href="/junk/' . $pic . '/upvote/?check=' . substr(md5($remote_salt . '-' . $user . '-' . $pic), 0, 6) . '"></a>';
			$out .= '<a class="downlink" href="/junk/' . $pic . '/downvote/?check=' . substr(md5($remote_salt . '-' . $user . '-' . $pic), 0, 6) . '"></a>';
		}
	} else {
		$out .= '<span class="value">' . (int) $votes->sum . '<small>/' . (int) $votes->count . '</small></span>';
		$out .= '<span class="uplink"></span>';
		$out .= '<span class="downlink"></span>';
	}


	$out .= '</div>';
	return $out;
}
