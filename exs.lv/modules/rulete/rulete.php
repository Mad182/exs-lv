<?php

/**
 * EXS.LV Kazino Rulete
 */

$tpl->assignInclude('module-head', 'modules/' . $category->module . '/head.tpl');
$tpl->prepare();

// Red and Black numbers on European Roulette
$red_numbers = [1, 3, 5, 7, 9, 12, 14, 16, 18, 19, 21, 23, 25, 27, 30, 32, 34, 36];
$black_numbers = [2, 4, 6, 8, 10, 11, 13, 15, 17, 20, 22, 24, 26, 28, 29, 31, 33, 35];

// Ensure tables exist
$db->query("
CREATE TABLE IF NOT EXISTS `roulette_balance` (
  `user_id` int(11) NOT NULL,
  `gold` int(11) NOT NULL DEFAULT 100,
  `max_gold` int(11) NOT NULL DEFAULT 100,
  `last_reset_date` date NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `gold` (`gold`),
  KEY `max_gold` (`max_gold`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Helper function to get & check user chip balance with daily reset
function get_user_roulette_balance($user_id) {
	global $db;
	if (!$user_id || $user_id <= 0) {
		return ['gold' => 100, 'max_gold' => 100];
	}

	$today = date('Y-m-d');
	$row = $db->get_row("SELECT * FROM `roulette_balance` WHERE `user_id` = " . intval($user_id));

	if (!$row) {
		$db->query("INSERT INTO `roulette_balance` (`user_id`, `gold`, `max_gold`, `last_reset_date`) VALUES (" . intval($user_id) . ", 100, 100, '" . $today . "')");

		// Sync with gamescore table
		$existing_score = $db->get_row("SELECT * FROM `gamescore` WHERE `game` = 'rulete' AND `user_id` = " . intval($user_id));
		if (!$existing_score) {
			$db->query("INSERT INTO `gamescore` (`user_id`, `game`, `score`, `time`) VALUES (" . intval($user_id) . ", 'rulete', 100, " . time() . ")");
		}

		return ['gold' => 100, 'max_gold' => 100];
	}

	// Check daily reset
	if ($row->last_reset_date < $today) {
		$new_gold = $row->gold;
		if ($new_gold < 100) {
			$new_gold = 100;
		}
		$max_gold = max($row->max_gold, $new_gold);
		$db->query("UPDATE `roulette_balance` SET `gold` = " . intval($new_gold) . ", `max_gold` = " . intval($max_gold) . ", `last_reset_date` = '" . $today . "' WHERE `user_id` = " . intval($user_id));

		// Sync with gamescore table
		$existing_score = $db->get_row("SELECT * FROM `gamescore` WHERE `game` = 'rulete' AND `user_id` = " . intval($user_id));
		if (!$existing_score) {
			$db->query("INSERT INTO `gamescore` (`user_id`, `game`, `score`, `time`) VALUES (" . intval($user_id) . ", 'rulete', " . intval($max_gold) . ", " . time() . ")");
		} else if ($max_gold > $existing_score->score) {
			$db->query("UPDATE `gamescore` SET `score` = " . intval($max_gold) . ", `time` = " . time() . " WHERE `id` = " . intval($existing_score->id));
		}

		return ['gold' => $new_gold, 'max_gold' => $max_gold];
	}

	return ['gold' => intval($row->gold), 'max_gold' => intval($row->max_gold)];
}

// Handle AJAX Spin Request
if (isset($_GET['action']) && $_GET['action'] === 'spin') {
	header('Content-Type: application/json');

	$raw_input = file_get_contents('php://input');
	$data = json_decode($raw_input, true);
	$bets = isset($data['bets']) && is_array($data['bets']) ? $data['bets'] : [];

	if (empty($bets)) {
		echo json_encode(['error' => 'Nav izvēlēta neviena likme!']);
		exit;
	}

	// Calculate total bet amount
	$total_bet = 0;
	foreach ($bets as $bet) {
		$amt = isset($bet['amount']) ? intval($bet['amount']) : 0;
		if ($amt > 0) {
			$total_bet += $amt;
		}
	}

	if ($total_bet <= 0) {
		echo json_encode(['error' => 'Likmes summai jābūt lielākai par 0!']);
		exit;
	}

	$is_logged_in = ($auth->ok && $auth->id > 0);
	$current_gold = 100;

	if ($is_logged_in) {
		$bal_data = get_user_roulette_balance($auth->id);
		$current_gold = $bal_data['gold'];

		if ($total_bet > $current_gold) {
			echo json_encode(['error' => 'Nepietiekams žetonu daudzums! Tev ir ' . $current_gold . ' žetoni.']);
			exit;
		}
	} else {
		if (isset($data['guest_gold']) && is_numeric($data['guest_gold'])) {
			$current_gold = max(0, intval($data['guest_gold']));
		}
		if ($total_bet > $current_gold) {
			echo json_encode(['error' => 'Nepietiekams žetonu daudzums! Tev ir ' . $current_gold . ' žetoni.']);
			exit;
		}
	}

	// Perform spin
	$winning_number = rand(0, 36);

	// Determine winning color
	$winning_color = 'green';
	if (in_array($winning_number, $red_numbers)) {
		$winning_color = 'red';
	} elseif (in_array($winning_number, $black_numbers)) {
		$winning_color = 'black';
	}

	// Calculate payouts
	$total_payout = 0;

	foreach ($bets as $bet) {
		$type = isset($bet['type']) ? $bet['type'] : '';
		$val = isset($bet['val']) ? intval($bet['val']) : null;
		$amt = isset($bet['amount']) ? intval($bet['amount']) : 0;

		if ($amt <= 0) continue;

		$win = false;
		$multiplier = 0;

		if ($type === 'num' && $val === $winning_number) {
			$win = true;
			$multiplier = 36; // 35:1 payout + 1 original bet
		} elseif ($type === 'red' && $winning_color === 'red') {
			$win = true;
			$multiplier = 2; // 1:1 payout + 1 original bet
		} elseif ($type === 'black' && $winning_color === 'black') {
			$win = true;
			$multiplier = 2;
		} elseif ($type === 'even' && $winning_number > 0 && $winning_number % 2 === 0) {
			$win = true;
			$multiplier = 2;
		} elseif ($type === 'odd' && $winning_number % 2 === 1) {
			$win = true;
			$multiplier = 2;
		} elseif ($type === '1-18' && $winning_number >= 1 && $winning_number <= 18) {
			$win = true;
			$multiplier = 2;
		} elseif ($type === '19-36' && $winning_number >= 19 && $winning_number <= 36) {
			$win = true;
			$multiplier = 2;
		} elseif ($type === '1st12' && $winning_number >= 1 && $winning_number <= 12) {
			$win = true;
			$multiplier = 3; // 2:1 payout + 1 original bet
		} elseif ($type === '2nd12' && $winning_number >= 13 && $winning_number <= 24) {
			$win = true;
			$multiplier = 3;
		} elseif ($type === '3rd12' && $winning_number >= 25 && $winning_number <= 36) {
			$win = true;
			$multiplier = 3;
		} elseif ($type === 'col1' && $winning_number > 0 && $winning_number % 3 === 1) {
			$win = true;
			$multiplier = 3;
		} elseif ($type === 'col2' && $winning_number > 0 && $winning_number % 3 === 2) {
			$win = true;
			$multiplier = 3;
		} elseif ($type === 'col3' && $winning_number > 0 && $winning_number % 3 === 0) {
			$win = true;
			$multiplier = 3;
		}

		if ($win) {
			$total_payout += ($amt * $multiplier);
		}
	}

	$net_gain = $total_payout - $total_bet;
	$new_gold = $current_gold + $net_gain;

	if ($is_logged_in) {
		$bal_data = get_user_roulette_balance($auth->id);
		$max_gold = max($bal_data['max_gold'], $new_gold);

		$today = date('Y-m-d');
		$db->query("UPDATE `roulette_balance` SET `gold` = " . intval($new_gold) . ", `max_gold` = " . intval($max_gold) . ", `last_reset_date` = '" . $today . "' WHERE `user_id` = " . intval($auth->id));

		// Sync with gamescore table for platform leaderboards
		$existing_score = $db->get_row("SELECT * FROM `gamescore` WHERE `game` = 'rulete' AND `user_id` = " . intval($auth->id));
		if (!$existing_score) {
			$db->query("INSERT INTO `gamescore` (`user_id`, `game`, `score`, `time`) VALUES (" . intval($auth->id) . ", 'rulete', " . intval($max_gold) . ", " . time() . ")");
			push('Uzstādīja jaunu rekordu spēlē <a href="/rulete">Rulete</a> (' . number_format($max_gold, 0, '', ' ') . ' zelta)', '/bildes/icons/games/rulete.png', 'game-rulete-' . $auth->id);
		} else if ($max_gold > $existing_score->score) {
			$db->query("UPDATE `gamescore` SET `score` = " . intval($max_gold) . ", `time` = " . time() . " WHERE `id` = " . intval($existing_score->id));
			push('Uzstādīja jaunu rekordu spēlē <a href="/rulete">Rulete</a> (' . number_format($max_gold, 0, '', ' ') . ' zelta)', '/bildes/icons/games/rulete.png', 'game-rulete-' . $auth->id);
		}
	} else {
		$max_gold = $new_gold;
	}

	echo json_encode([
		'success' => true,
		'winning_number' => $winning_number,
		'color' => $winning_color,
		'total_bet' => $total_bet,
		'payout' => $total_payout,
		'net_gain' => $net_gain,
		'new_gold' => $new_gold,
		'max_gold' => $max_gold,
		'is_guest' => !$is_logged_in
	]);
	exit;
}

// Leaderboard Action OR Game View
$act = isset($_GET['act']) ? $_GET['act'] : '';

if ($act === 'top' || $act === 'today' || $act === 'all') {
	// Leaderboards View
	$is_all_time = ($act === 'all');

	if ($is_all_time) {
		$tpl->assign(['active-tab-all' => ' active']);
		$scores = $db->get_results("
			SELECT r.user_id, r.max_gold as score, u.nick, u.level
			FROM `roulette_balance` r
			JOIN `users` u ON u.id = r.user_id
			WHERE r.max_gold > 0
			ORDER BY r.max_gold DESC, r.user_id ASC
			LIMIT 50
		");
	} else {
		$tpl->assign(['active-tab-today' => ' active']);
		$scores = $db->get_results("
			SELECT r.user_id, r.gold as score, u.nick, u.level
			FROM `roulette_balance` r
			JOIN `users` u ON u.id = r.user_id
			WHERE r.gold > 0
			ORDER BY r.gold DESC, r.user_id ASC
			LIMIT 50
		");
	}

	$tpl->newBlock('rulete-top');

	if ($scores) {
		$i = 1;
		foreach ($scores as $topuser) {
			$usr = (object) ['nick' => $topuser->nick, 'level' => $topuser->level];
			$tpl->newBlock('top-row');

			$special = '';
			if ($i === 1) $icon = '🥇 1.';
			elseif ($i === 2) $icon = '🥈 2.';
			elseif ($i === 3) $icon = '🥉 3.';
			else $icon = $i . '.';

			$score_str = number_format($topuser->score) . ' 🎰 žetoni';

			$tpl->assign([
				'user-place' => $icon,
				'user-special' => $special,
				'user-url' => mkurl('user', $topuser->user_id, $usr->nick),
				'user-nick' => usercolor($usr->nick, $usr->level),
				'user-score' => $score_str
			]);
			$i++;
		}
	} else {
		$tpl->newBlock('no-scores');
	}
} else {
	// Game View
	$tpl->assign(['active-tab-game' => ' active']);

	if (!$auth->ok) {
		$tpl->newBlock('game-login');
		$tpl->newBlock('seo-text');
		$user_gold = 100;
	} else {
		$bal_data = get_user_roulette_balance($auth->id);
		$user_gold = $bal_data['gold'];
	}

	$tpl->newBlock('game-play');
	$tpl->assign([
		'user-gold' => $user_gold
	]);
}
