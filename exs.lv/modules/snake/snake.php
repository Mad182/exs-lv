<?php

/**
 * Čūska (Snake) spēle ar anti-cheat aizsardzību un rezultātu saglabāšanu
 */

// 1. AJAX session token creation
if (isset($_GET['action']) && $_GET['action'] == 'init_token') {
	header('Content-Type: application/json');
	$token = bin2hex(random_bytes(16));
	$_SESSION['snake_token'] = $token;
	$_SESSION['snake_start_time'] = time();
	echo json_encode(['success' => true, 'token' => $token]);
	exit;
}

// 2. AJAX score submission with anti-cheat checks
if (isset($_GET['action']) && $_GET['action'] == 'push') {
	header('Content-Type: application/json');

	if (!$auth->ok) {
		echo json_encode(['success' => false, 'message' => 'Nesi autorizējies! Vispirms ienāc savā profilā.']);
		exit;
	}

	$token = isset($_POST['token']) ? trim($_POST['token']) : '';
	$score = isset($_POST['score']) ? intval($_POST['score']) : (isset($_GET['score']) ? intval($_GET['score']) : 0);
	$cherries = isset($_POST['cherries']) ? intval($_POST['cherries']) : 0;
	$level = isset($_POST['level']) ? intval($_POST['level']) : 1;

	// Anti-Cheat Check 1: Token Verification & Single Use
	if (empty($_SESSION['snake_token']) || empty($token) || !hash_equals($_SESSION['snake_token'], $token)) {
		echo json_encode(['success' => false, 'message' => 'Nekorekts spēles žetons (Token verification failed).']);
		exit;
	}

	$start_time = isset($_SESSION['snake_start_time']) ? intval($_SESSION['snake_start_time']) : time();
	$duration = max(1, time() - $start_time);

	// Clear token to prevent replay attacks
	unset($_SESSION['snake_token']);
	unset($_SESSION['snake_start_time']);

	if ($score <= 0) {
		echo json_encode(['success' => false, 'message' => 'Nekorekts punktu skaits!']);
		exit;
	}

	// Anti-Cheat Check 2: Max theoretical score check
	// Snake has 6 levels with total 45 cherries * 10 points = 450 max score
	if ($score > 450) {
		echo json_encode(['success' => false, 'message' => 'Uzrādītais rezultāts pārsniedz spēles maksimāli iespējamos punktus (450)!']);
		exit;
	}

	// Anti-Cheat Check 3: Duration check
	// Minimum duration threshold for high scores (> 100 pts requires at least 8 seconds)
	if ($score > 100 && $duration < 6) {
		echo json_encode(['success' => false, 'message' => 'Spēles ilgums ir par īsu uzrādītajam rezultātam!']);
		exit;
	}

	// Save or Update High Score
	$current = $db->get_row("SELECT * FROM gamescore WHERE game = 'snake' AND user_id = '$auth->id'");
	$is_new_record = false;

	if (!$current) {
		$db->query("INSERT INTO gamescore (user_id, game, score, time) VALUES ('$auth->id', 'snake', '$score', '" . time() . "')");
		$is_new_record = true;
		$highScore = $score;
	} elseif ($score > $current->score) {
		$db->query("UPDATE gamescore SET score = '$score', time = '" . time() . "' WHERE id = '$current->id' AND user_id = '$auth->id'");
		$is_new_record = true;
		$highScore = $score;
	} else {
		$highScore = $current->score;
	}

	echo json_encode([
		'success' => true,
		'score' => $score,
		'highScore' => $highScore,
		'isNewRecord' => $is_new_record
	]);
	exit;
}

$tpl->assignInclude('module-head', 'modules/' . $category->module . '/head.tpl');
$tpl->prepare();

$active_sub = isset($_GET['var1']) ? $_GET['var1'] : (isset($_GET['act']) ? $_GET['act'] : '');

if ($active_sub === 'top') {
	$tpl->assign(['active-tab-top' => ' active']);

	$start_of_today = strtotime('today midnight');
	$scores = $db->get_results("SELECT * FROM gamescore WHERE game = 'snake' AND time >= '$start_of_today' ORDER BY score DESC LIMIT 100");
	$tpl->newBlock('snake-top');
	$tpl->assign('top-title', 'Šodienas tops');

	if ($scores) {
		$i = 1;
		foreach ($scores as $score) {
			$special = ($auth->id == $score->user_id) ? ' style="font-weight:bold"' : '';
			if ($i == 1) {
				$icon = '<img src="/bildes/icons/award_star_gold_3.png" alt="1." title="1." />';
			} elseif ($i == 2) {
				$icon = '<img src="/bildes/icons/award_star_silver_3.png" alt="2." title="2." />';
			} elseif ($i == 3) {
				$icon = '<img src="/bildes/icons/award_star_bronze_3.png" alt="3." title="3." />';
			} else {
				$icon = $i . '.';
			}

			$user = $db->get_row("SELECT id, nick, level FROM users WHERE id = '$score->user_id'");
			if ($user) {
				$tpl->newBlock('top-node');
				$tpl->assign([
					'user-place' => $icon,
					'user-special' => $special,
					'user-url' => mkurl('user', $user->id, $user->nick),
					'user-nick' => usercolor($user->nick, $user->level),
					'user-score' => number_format($score->score, 0, '', ' '),
					'user-date' => date('Y-m-d H:i', $score->time)
				]);
				$i++;
			}
		}
	} else {
		$tpl->newBlock('no-scores');
	}
} elseif ($active_sub === 'overall-top') {
	$tpl->assign(['active-tab-overall-top' => ' active']);

	$scores = $db->get_results("SELECT * FROM gamescore WHERE game = 'snake' ORDER BY score DESC LIMIT 100");
	$tpl->newBlock('snake-top');
	$tpl->assign('top-title', 'Visu laiku tops');

	if ($scores) {
		$i = 1;
		foreach ($scores as $score) {
			$special = ($auth->id == $score->user_id) ? ' style="font-weight:bold"' : '';
			if ($i == 1) {
				$icon = '<img src="/bildes/icons/award_star_gold_3.png" alt="1." title="1." />';
			} elseif ($i == 2) {
				$icon = '<img src="/bildes/icons/award_star_silver_3.png" alt="2." title="2." />';
			} elseif ($i == 3) {
				$icon = '<img src="/bildes/icons/award_star_bronze_3.png" alt="3." title="3." />';
			} else {
				$icon = $i . '.';
			}

			$user = $db->get_row("SELECT id, nick, level FROM users WHERE id = '$score->user_id'");
			if ($user) {
				$tpl->newBlock('top-node');
				$tpl->assign([
					'user-place' => $icon,
					'user-special' => $special,
					'user-url' => mkurl('user', $user->id, $user->nick),
					'user-nick' => usercolor($user->nick, $user->level),
					'user-score' => number_format($score->score, 0, '', ' '),
					'user-date' => date('Y-m-d H:i', $score->time)
				]);
				$i++;
			}
		}
	} else {
		$tpl->newBlock('no-scores');
	}
} else {
	$tpl->assign(['active-tab-game' => ' active']);
	$tpl->newBlock('snake-game');

	$user_high_score = 0;
	if ($auth->ok) {
		$user_high_score = (int) $db->get_var("SELECT score FROM gamescore WHERE game = 'snake' AND user_id = '$auth->id'");
	}

	$tpl->assign([
		'user-highscore' => $user_high_score,
		'is-logged' => $auth->ok ? 1 : 0
	]);

	if (!$auth->ok) {
		$tpl->newBlock('guest-notice');
	}

	$sidebar_scores = $db->get_results("SELECT * FROM gamescore WHERE game = 'snake' ORDER BY score DESC LIMIT 10");
	if ($sidebar_scores) {
		$i = 1;
		foreach ($sidebar_scores as $score) {
			$user = $db->get_row("SELECT id, nick, level FROM users WHERE id = '$score->user_id'");
			if ($user) {
				$tpl->newBlock('mini-top-node');
				$tpl->assign([
					'place' => $i++,
					'user-url' => mkurl('user', $user->id, $user->nick),
					'user-nick' => usercolor($user->nick, $user->level),
					'score' => number_format($score->score, 0, '', ' ')
				]);
			}
		}
	}
}
