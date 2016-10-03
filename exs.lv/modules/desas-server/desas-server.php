<?php

/**
 * "tic tac toe" json serveris
 * (c) madars anziķis, 2011
 */
if (!$auth->ok) {
	die('auth required');
}

$fields = [
	0 => [0 => 0, 1 => 0, 2 => 0],
	1 => [0 => 0, 1 => 0, 2 => 0],
	2 => [0 => 0, 1 => 0, 2 => 0],
];

$out = [];
$out['status'] = 2;
$out['me'] = 0;
$out['other'] = 0;
$out['alert'] = 0;
$out['winner'] = 0;
$out['finished'] = 0;
$out['timeout'] = 0;
$out['opponent'] = 'Nav';
$out['fields'] = $fields;
$out['overlay'] = 0;

if (!$game = $db->get_row("SELECT * FROM desas WHERE (user_1 = '$auth->id' OR user_2 = '$auth->id') AND ((status = 0 OR status = 1) OR loser_seen	= 0)")) {
	$game = $db->get_row("SELECT * FROM desas WHERE status = '0' AND user_1 != '$auth->id'");
	if ($game) {
		$db->query("INSERT INTO desas_moves (`user_id`,`timestamp`) VALUES ('$auth->id', '" . time() . "') ON DUPLICATE KEY UPDATE `timestamp` = '" . time() . "'");
		$db->query("UPDATE desas SET status = '1', user_2 = '$auth->id', waiting_for = '1', modified = NOW() WHERE id = '$game->id'");
		$out['status'] = 2;
		$out['me'] = 2;
	} else {
		$db->query("INSERT INTO desas_moves (`user_id`,`timestamp`) VALUES ('$auth->id', '" . time() . "') ON DUPLICATE KEY UPDATE `timestamp` = '" . time() . "'");
		$db->query("INSERT INTO desas (user_1,data,created) VALUES ('$auth->id', '" . sanitize(serialize($fields)) . "', NOW())");
		$out['status'] = 2;
		$out['me'] = 1;
	}
} else {

	if (isset($_GET['var1']) && $_GET['var1'] == 'drop') {
		$db->query("DELETE FROM desas WHERE id = '$game->id' LIMIT 1");
		$game = $db->get_row("SELECT * FROM desas WHERE status = '0' AND user_1 != '$auth->id'");
		if ($game) {
			$db->query("UPDATE desas SET status = '1', user_2 = '$auth->id', waiting_for = '1', modified = NOW() WHERE id = '$game->id'");
			$out['status'] = 2;
		} else {
			$db->query("INSERT INTO desas (user_1,data,created) VALUES ('$auth->id', '" . sanitize(serialize($fields)) . "', NOW())");
			$out['status'] = 2;
		}
	}

	$lastmove = $db->get_var("SELECT `timestamp` FROM `desas_moves` WHERE `user_id` = '$auth->id'");
	if ($lastmove < time() - 25) {
		$out['timeout'] = 1;
		$db->query("DELETE FROM desas WHERE id = '$game->id' LIMIT 1");
	}

	$fields = unserialize($game->data);

	if ($game->user_1 == $auth->id && $game->user_2 != 0) {
		$me = 1;
		$other = 2;
		if ($game->waiting_for == 1) {
			$out['status'] = 1;
			$out['me'] = $me;
		} else {
			$out['status'] = 2;
			$out['me'] = $me;
		}
		$out['other'] = $game->user_2;
	} elseif ($game->user_2 == $auth->id) {
		$me = 2;
		$other = 1;
		if ($game->waiting_for == 2) {
			$out['status'] = 1;
			$out['me'] = $me;
		} else {
			$out['status'] = 2;
			$out['me'] = $me;
		}
		$out['other'] = $game->user_1;
	} else {
		$out['status'] = 2;
		$out['me'] = 1;
	}

	$updated = 0;
	if (isset($_GET['mark']) && $out['status'] == 1) {
		if (strlen($_GET['mark']) == 2) {
			$coord_x = substr($_GET['mark'], 0, 1);
			$coord_y = substr($_GET['mark'], 1, 1);

			if ($coord_x < 0 or $coord_x > 2) {
				die('wrong_x_coord');
			}
			if ($coord_y < 0 or $coord_y > 2) {
				die('wrong_y_coord');
			}

			if ($fields[$coord_x][$coord_y] == 0) {
				$fields[$coord_x][$coord_y] = $me;
				$updated = 1;
				$db->query("UPDATE desas SET data = '" . sanitize(serialize($fields)) . "', waiting_for	= '$other', modified = NOW() WHERE id = '$game->id'");
				$db->query("INSERT INTO desas_moves (`user_id`,`timestamp`) VALUES ('$auth->id', '" . time() . "') ON DUPLICATE KEY UPDATE `timestamp` = '" . time() . "'");
				$out['status'] = 2;
			} else {
				$out['alert'] = 'Lauks jau ir atzīmēts';
			}
		} else {
			$out['alert'] = 'Nepareizs pieprasījums';
		}
	}

	if (!empty($out['other'])) {
		$out['opponent'] = $db->get_var("SELECT nick FROM users WHERE id = '" . intval($out['other']) . "'");
	}

	$winner = 0;
	for ($i = 0; $i <= 2; $i++) {
		if ($fields[$i][0] != 0 && $fields[$i][0] == $fields[$i][1] && $fields[$i][1] == $fields[$i][2]) {
			$winner = $fields[$i][0];
			break;
		}
		if ($fields[0][$i] != 0 && $fields[0][$i] == $fields[1][$i] && $fields[1][$i] == $fields[2][$i]) {
			$winner = $fields[0][$i];
			break;
		}
	}
	if ($fields[0][0] != 0 && $fields[0][0] == $fields[1][1] && $fields[1][1] == $fields[2][2]) {
		$winner = $fields[0][0];
	}
	if ($fields[2][0] != 0 && $fields[2][0] == $fields[1][1] && $fields[1][1] == $fields[0][2]) {
		$winner = $fields[2][0];
	}

	if ($winner) {
		$out['finished'] = 1;
		$out['winner'] = $winner;
		$db->query("UPDATE desas SET winner = '$winner', waiting_for	= '0', status = '2', modified = NOW() WHERE id = '$game->id'");
		$out['alert'] = '';
		if ($me == $winner) {
			update_awards($auth->id);
			$out['overlay'] = 'Tu uzvarēji ;)';
		} else {
			$out['overlay'] = 'Tu zaudēji :(';
			$db->query("UPDATE desas SET loser_seen = 1 WHERE id = '$game->id'");
		}
	} elseif (
			$fields[0][0] != 0 &&
			$fields[0][1] != 0 &&
			$fields[0][2] != 0 &&
			$fields[1][0] != 0 &&
			$fields[1][1] != 0 &&
			$fields[1][2] != 0 &&
			$fields[2][0] != 0 &&
			$fields[2][1] != 0 &&
			$fields[2][2] != 0
	) {

		$db->query("UPDATE desas SET winner = '0', waiting_for	= '0', status = '2', modified = NOW() WHERE id = '$game->id'");
		$out['overlay'] = 'Neizšķirts';
		if (!$updated) {
			$db->query("UPDATE desas SET loser_seen = 1 WHERE id = '$game->id'");
		}
	} else {
		if ($out['status'] == 2) {
			$out['alert'] = 'Gaida pretinieku';
		}
		if ($out['status'] == 1) {
			$out['alert'] = 'Tavs gājiens';
		}
	}

	$out['fields'] = $fields;
}


$out['mytotal']['wins'] = (int) $db->get_var("SELECT count(*) FROM desas WHERE (user_1 = '$auth->id' AND winner = '1') OR (user_2 = '$auth->id' AND winner = '2')");
$out['mytotal']['loses'] = (int) $db->get_var("SELECT count(*) FROM desas WHERE ((user_1 = '$auth->id' AND winner = '2') OR (user_2 = '$auth->id' AND winner = '1')) AND status = '2'");
$out['optotal']['wins'] = (int) $db->get_var("SELECT count(*) FROM desas WHERE (user_1 = '" . intval($out['other']) . "' AND winner = '1') OR (user_2 = '" . intval($out['other']) . "' AND winner = '2')");
$out['optotal']['loses'] = (int) $db->get_var("SELECT count(*) FROM desas WHERE ((user_1 = '" . intval($out['other']) . "' AND winner = '2') OR (user_2 = '" . intval($out['other']) . "' AND winner = '1')) AND status = '2'");


$json = json_encode($out);
header('Content-Type: application/json; charset=utf-8');
die($json);
