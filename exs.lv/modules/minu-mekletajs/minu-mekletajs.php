<?php

/**
 * Mīnu Meklētājs (Minesweeper) ar rezultātu saglabāšanu un anti-cheat aizsardzību
 */

// 1. AJAX session token creation
if (isset($_GET['action']) && $_GET['action'] == 'init_token') {
	header('Content-Type: application/json');
	$token = bin2hex(random_bytes(16));
	$_SESSION['ms_token'] = $token;
	$_SESSION['ms_start_time'] = time();
	echo json_encode(['success' => true, 'token' => $token]);
	exit;
}

// 2. AJAX score submission
if (isset($_GET['action']) && $_GET['action'] == 'push') {
	header('Content-Type: application/json');

	if (!$auth->ok) {
		echo json_encode(['success' => false, 'message' => 'Nesi autorizējies! Vispirms ienāc savā profilā.']);
		exit;
	}

	$token = isset($_POST['token']) ? trim($_POST['token']) : '';
	$time_sec = isset($_POST['time_sec']) ? intval($_POST['time_sec']) : 0;
	$difficulty = isset($_POST['difficulty']) ? trim($_POST['difficulty']) : 'easy';

	// Anti-Cheat Check 1: Token Verification
	if (empty($_SESSION['ms_token']) || empty($token) || !hash_equals($_SESSION['ms_token'], $token)) {
		echo json_encode(['success' => false, 'message' => 'Nekorekts spēles žetons (Token verification failed).']);
		exit;
	}

	$start_time = isset($_SESSION['ms_start_time']) ? intval($_SESSION['ms_start_time']) : time();
	$duration = max(1, time() - $start_time);

	// Clear token to prevent replay attacks
	unset($_SESSION['ms_token']);
	unset($_SESSION['ms_start_time']);

	if ($time_sec <= 0) {
		echo json_encode(['success' => false, 'message' => 'Nekorekts spēles laiks!']);
		exit;
	}

	// Anti-Cheat Check 2: Minimum plausible time thresholds per difficulty
	$min_allowed = 3;
	if ($difficulty == 'medium') $min_allowed = 12;
	if ($difficulty == 'hard') $min_allowed = 35;

	if ($time_sec < $min_allowed || $duration < ($min_allowed - 2)) {
		echo json_encode(['success' => false, 'message' => 'Uzrādītais spēles laiks ir pārāk mazs izvēlētajai sarežģītībai!']);
		exit;
	}

	// Check if this is a new personal best time (lower is better)
	if ($difficulty == 'easy') {
		$prev_best = $db->get_var("SELECT MIN(score) FROM gamescore WHERE game IN ('minu-mekletajs-easy', 'minu-mekletajs') AND user_id = '$auth->id'");
	} else {
		$prev_best = $db->get_var("SELECT MIN(score) FROM gamescore WHERE game = 'minu-mekletajs-$difficulty' AND user_id = '$auth->id'");
	}
	$is_new_record = (empty($prev_best) || $time_sec < (int)$prev_best);

	// Store time_sec directly into gamescore per difficulty
	$game_key = 'minu-mekletajs-' . $difficulty;
	$db->query("INSERT INTO gamescore (user_id, game, score, time) VALUES ('$auth->id', '$game_key', '$time_sec', '" . time() . "')");
	$insert_id = $db->insert_id();

	if ($insert_id) {
		$mins = floor($time_sec / 60);
		$s = $time_sec % 60;
		$formatted_time = sprintf('%02d:%02d', $mins, $s);

		if ($is_new_record) {
			push('Uzstādīja jaunu rekordu spēlē <a href="/minu-mekletajs">Mīnu Meklētājs</a> (' . $formatted_time . ')', '/bildes/icons/games/minu-mekletajs.png', 'game-minu-mekletajs-' . $auth->id);
		}

		if ($difficulty == 'easy') {
			$rank = $db->get_var("SELECT COUNT(*) + 1 FROM gamescore WHERE game IN ('minu-mekletajs-easy', 'minu-mekletajs') AND score < '$time_sec'");
		} else {
			$rank = $db->get_var("SELECT COUNT(*) + 1 FROM gamescore WHERE game = '$game_key' AND score < '$time_sec'");
		}

		echo json_encode([
			'success' => true,
			'message' => 'Tavs laiks (' . $formatted_time . ') veiksmīgi saglabāts topos!',
			'rank' => $rank
		]);
	} else {
		echo json_encode(['success' => false, 'message' => 'Kļūda saglabājot rezultātu datubāzē!']);
	}
	exit;
}

// 3. Regular Page View
$tpl->assignInclude('module-head', 'modules/' . $category->module . '/head.tpl');
$tpl->prepare();

$act = isset($_GET['act']) ? $_GET['act'] : (isset($_GET['var1']) ? $_GET['var1'] : '');

$difficulties = [
	'easy' => ['title' => 'Iesācējs (9×9, 10 mīnas)', 'badge' => 'easy', 'icon' => '🟢'],
	'medium' => ['title' => 'Vidējs (16×16, 40 mīnas)', 'badge' => 'medium', 'icon' => '🟡'],
	'hard' => ['title' => 'Eksperts (30×16, 99 mīnas)', 'badge' => 'hard', 'icon' => '🔴']
];

if ($act == 'top' || $act == 'overall-top') {
	if ($act == 'top') {
		$tpl->assign(['active-tab-top' => ' active']);
		$today_start = strtotime('today midnight');
	} else {
		$tpl->assign(['active-tab-overall-top' => ' active']);
	}

	foreach ($difficulties as $diff_key => $diff_info) {
		$tpl->newBlock('diff-section');
		$tpl->assign([
			'diff-title' => $diff_info['title'],
			'diff-badge' => $diff_info['badge'],
			'diff-icon' => $diff_info['icon']
		]);

		if ($diff_key == 'easy') {
			$where_game = "game IN ('minu-mekletajs-easy', 'minu-mekletajs')";
		} else {
			$where_game = "game = 'minu-mekletajs-" . $diff_key . "'";
		}

		$where_time = ($act == 'top') ? " AND time >= '$today_start'" : "";
		$topusers = $db->get_results("SELECT * FROM gamescore WHERE $where_game $where_time ORDER BY score ASC LIMIT 100");

		if ($topusers) {
			$tpl->newBlock('top-table');
			$i = 1;
			foreach ($topusers as $topuser) {
				$special = ($auth->ok && $auth->id == $topuser->user_id) ? ' style="font-weight:bold"' : '';
				if ($i == 1) {
					$icon = '<img src="/bildes/icons/award_star_gold_3.png" alt="1." title="1." />';
				} elseif ($i == 2) {
					$icon = '<img src="/bildes/icons/award_star_silver_3.png" alt="2." title="2." />';
				} elseif ($i == 3) {
					$icon = '<img src="/bildes/icons/award_star_bronze_3.png" alt="3." title="3." />';
				} else {
					$icon = $i . '.';
				}

				$usr = $db->get_row("SELECT nick, level FROM users WHERE id = '$topuser->user_id'");
				if ($usr) {
					$tpl->newBlock('top-node');
					$mins = floor($topuser->score / 60);
					$s = $topuser->score % 60;
					$time_str = sprintf('%02d:%02d sek', $mins, $s);

					$tpl->assign([
						'user-place' => $icon,
						'user-special' => $special,
						'user-url' => mkurl('user', $topuser->user_id, $usr->nick),
						'user-nick' => usercolor($usr->nick, $usr->level),
						'user-score' => $time_str,
						'user-time' => time_ago($topuser->time)
					]);
					$i++;
				}
			}
		} else {
			$tpl->newBlock('no-scores');
		}
	}
} else {
	// Game View
	$tpl->assign(['active-tab-game' => ' active']);

	if (!$auth->ok) {
		$tpl->newBlock('game-login');
		$tpl->newBlock('seo-text');
	}

	$tpl->newBlock('game-play');

	$user_bests = [
		'easy' => '--:--',
		'medium' => '--:--',
		'hard' => '--:--'
	];

	if ($auth->ok && $auth->id > 0) {
		foreach (['easy', 'medium', 'hard'] as $d) {
			if ($d == 'easy') {
				$best_sec = intval($db->get_var("SELECT MIN(score) FROM gamescore WHERE game IN ('minu-mekletajs-easy', 'minu-mekletajs') AND user_id = '$auth->id'"));
			} else {
				$best_sec = intval($db->get_var("SELECT MIN(score) FROM gamescore WHERE game = 'minu-mekletajs-$d' AND user_id = '$auth->id'"));
			}
			if ($best_sec > 0) {
				$mins = floor($best_sec / 60);
				$s = $best_sec % 60;
				$user_bests[$d] = sprintf('%02d:%02d', $mins, $s);
			}
		}
	}

	$tpl->assign([
		'user-best-easy' => $user_bests['easy'],
		'user-best-medium' => $user_bests['medium'],
		'user-best-hard' => $user_bests['hard'],
		'user-best-time' => $user_bests['easy']
	]);
}
