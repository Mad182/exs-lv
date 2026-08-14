<?php

/**
 * Wordle (Latviešu valodā) spēle ar rezultātu saglabāšanu un anti-cheat aizsardzību
 */

// 1. AJAX session token creation
if (isset($_GET['action']) && $_GET['action'] == 'init_token') {
	header('Content-Type: application/json');
	$token = bin2hex(random_bytes(16));
	$_SESSION['wordle_token'] = $token;
	$_SESSION['wordle_start_time'] = time();
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
	$guesses = isset($_POST['guesses']) ? intval($_POST['guesses']) : 0;
	$mode = isset($_POST['mode']) ? trim($_POST['mode']) : 'daily';

	// Anti-Cheat Check 1: Token Verification
	if (empty($_SESSION['wordle_token']) || empty($token) || !hash_equals($_SESSION['wordle_token'], $token)) {
		echo json_encode(['success' => false, 'message' => 'Nekorekts spēles žetons (Token verification failed).']);
		exit;
	}

	$start_time = isset($_SESSION['wordle_start_time']) ? intval($_SESSION['wordle_start_time']) : time();
	$duration = max(1, time() - $start_time);

	// Clear token to prevent replay attacks
	unset($_SESSION['wordle_token']);
	unset($_SESSION['wordle_start_time']);

	if ($time_sec <= 0 || $guesses < 1 || $guesses > 6) {
		echo json_encode(['success' => false, 'message' => 'Nekorekts spēles rezultāts!']);
		exit;
	}

	// Anti-Cheat Check 2: Minimum plausible time (at least 3 seconds)
	if ($time_sec < 3 || $duration < 2) {
		echo json_encode(['success' => false, 'message' => 'Uzrādītais spēles laiks ir pārāk mazs!']);
		exit;
	}

	// Score formula: Lower score is better! (guesses * 1000 + time_sec)
	// Example: 2 guesses in 25s = 2025 score. 5 guesses in 12s = 5012 score.
	$composite_score = ($guesses * 1000) + min(999, $time_sec);

	$db->query("INSERT INTO gamescore (user_id, game, score, time) VALUES ('$auth->id', 'wordle', '$composite_score', '" . time() . "')");
	$insert_id = $db->insert_id();

	if ($insert_id) {
		$rank = $db->get_var("SELECT COUNT(*) + 1 FROM gamescore WHERE game = 'wordle' AND score < '$composite_score'");
		$mins = floor($time_sec / 60);
		$s = $time_sec % 60;
		$formatted_time = sprintf('%02d:%02d', $mins, $s);

		echo json_encode([
			'success' => true,
			'message' => 'Tavs rezultāts (' . $guesses . ' mēģinājumi, ' . $formatted_time . ') veiksmīgi saglabāts topos!',
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
	// Today's Top Scores (Lowest composite score is best)
	$tpl->assign(['active-tab-top' => ' active']);
	$today_start = strtotime('today midnight');
	$topusers = $db->get_results("SELECT * FROM gamescore WHERE game = 'wordle' AND time >= '$today_start' ORDER BY score ASC LIMIT 100");

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
				$guesses_cnt = floor($topuser->score / 1000);
				$sec = $topuser->score % 1000;
				$mins = floor($sec / 60);
				$s = $sec % 60;
				$score_str = $guesses_cnt . '/6 mēģinājumi (' . sprintf('%02d:%02d', $mins, $s) . ')';

				$tpl->assign([
					'user-place' => $icon,
					'user-special' => $special,
					'user-url' => mkurl('user', $topuser->user_id, $usr->nick),
					'user-nick' => usercolor($usr->nick, $usr->level),
					'user-score' => $score_str,
					'user-time' => time_ago($topuser->time)
				]);
				$i++;
			}
		}
	}
} elseif ($act == 'overall-top') {
	// All-Time Top Scores
	$tpl->assign(['active-tab-overall-top' => ' active']);
	$topusers = $db->get_results("SELECT * FROM gamescore WHERE game = 'wordle' ORDER BY score ASC LIMIT 100");

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
				$guesses_cnt = floor($topuser->score / 1000);
				$sec = $topuser->score % 1000;
				$mins = floor($sec / 60);
				$s = $sec % 60;
				$score_str = $guesses_cnt . '/6 mēģinājumi (' . sprintf('%02d:%02d', $mins, $s) . ')';

				$tpl->assign([
					'user-place' => $icon,
					'user-special' => $special,
					'user-url' => mkurl('user', $topuser->user_id, $usr->nick),
					'user-nick' => usercolor($usr->nick, $usr->level),
					'user-score' => $score_str,
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

	$best_score = 0;
	if ($auth->ok && $auth->id > 0) {
		$best_score = intval($db->get_var("SELECT MIN(score) FROM gamescore WHERE game = 'wordle' AND user_id = '$auth->id'"));
	}
	$formatted_best = '--';
	if ($best_score > 0) {
		$g = floor($best_score / 1000);
		$sec = $best_score % 1000;
		$mins = floor($sec / 60);
		$s = $sec % 60;
		$formatted_best = $g . '/6 (' . sprintf('%02d:%02d', $mins, $s) . ')';
	}

	$tpl->assign(['user-best-score' => $formatted_best]);
}
