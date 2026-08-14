<?php

/**
 * Sudoku spēle ar rezultātu saglabāšanu un anti-cheat aizsardzību
 */

// 1. AJAX session token creation
if (isset($_GET['action']) && $_GET['action'] == 'init_token') {
	header('Content-Type: application/json');
	$token = bin2hex(random_bytes(16));
	$_SESSION['sudoku_token'] = $token;
	$_SESSION['sudoku_start_time'] = time();
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
	if (empty($_SESSION['sudoku_token']) || empty($token) || !hash_equals($_SESSION['sudoku_token'], $token)) {
		echo json_encode(['success' => false, 'message' => 'Nekorekts spēles žetons (Token verification failed).']);
		exit;
	}

	$start_time = isset($_SESSION['sudoku_start_time']) ? intval($_SESSION['sudoku_start_time']) : time();
	$duration = max(1, time() - $start_time);

	// Clear token to prevent replay attacks
	unset($_SESSION['sudoku_token']);
	unset($_SESSION['sudoku_start_time']);

	if ($time_sec <= 0) {
		echo json_encode(['success' => false, 'message' => 'Nekorekts spēles laiks!']);
		exit;
	}

	// Anti-Cheat Check 2: Minimum plausible time thresholds per difficulty
	$min_allowed = 15;
	if ($difficulty == 'medium') $min_allowed = 30;
	if ($difficulty == 'hard') $min_allowed = 60;

	if ($time_sec < $min_allowed || $duration < ($min_allowed - 5)) {
		echo json_encode(['success' => false, 'message' => 'Uzrādītais risināšanas laiks ir par īsu izvēlētajai sarežģītībai!']);
		exit;
	}

	// Store time_sec directly into gamescore (lower time is better!)
	$db->query("INSERT INTO gamescore (user_id, game, score, time) VALUES ('$auth->id', 'sudoku', '$time_sec', '" . time() . "')");
	$insert_id = $db->insert_id();

	if ($insert_id) {
		$rank = $db->get_var("SELECT COUNT(*) + 1 FROM gamescore WHERE game = 'sudoku' AND score < '$time_sec'");
		$mins = floor($time_sec / 60);
		$s = $time_sec % 60;
		$formatted_time = sprintf('%02d:%02d', $mins, $s);

		echo json_encode([
			'success' => true,
			'message' => 'Tavs risinājuma laiks (' . $formatted_time . ') veiksmīgi saglabāts topos!',
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

if ($act == 'top') {
	// Today's Top Scores (Lowest time is best)
	$tpl->assign(['active-tab-top' => ' active']);
	$today_start = strtotime('today midnight');
	$topusers = $db->get_results("SELECT * FROM gamescore WHERE game = 'sudoku' AND time >= '$today_start' ORDER BY score ASC LIMIT 100");

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
	}
} elseif ($act == 'overall-top') {
	// All-Time Top Scores (Lowest time is best)
	$tpl->assign(['active-tab-overall-top' => ' active']);
	$topusers = $db->get_results("SELECT * FROM gamescore WHERE game = 'sudoku' ORDER BY score ASC LIMIT 100");

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
	}
} else {
	// Game View
	$tpl->assign(['active-tab-game' => ' active']);

	if (!$auth->ok) {
		$tpl->newBlock('game-login');
	}

	$tpl->newBlock('game-play');

	$best_sec = 0;
	if ($auth->ok && $auth->id > 0) {
		$best_sec = intval($db->get_var("SELECT MIN(score) FROM gamescore WHERE game = 'sudoku' AND user_id = '$auth->id'"));
	}
	$formatted_best = '--:--';
	if ($best_sec > 0) {
		$mins = floor($best_sec / 60);
		$s = $best_sec % 60;
		$formatted_best = sprintf('%02d:%02d', $mins, $s);
	}

	$tpl->assign(['user-best-time' => $formatted_best]);
}
