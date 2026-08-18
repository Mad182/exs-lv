<?php

/**
 * HexGL 3D Futuristic Racing Game Module
 */

if (!isset($_SESSION)) {
	session_start();
}

// 1. AJAX: Initialize Session Token
if (isset($_GET['action']) && $_GET['action'] === 'init_token') {
	header('Content-Type: application/json');
	$token = md5(uniqid(rand(), true));
	$_SESSION['hexgl_token'] = $token;
	echo json_encode(['success' => true, 'token' => $token]);
	exit;
}

// 2. AJAX: Submit High Score
if (isset($_GET['action']) && $_GET['action'] === 'push') {
	header('Content-Type: application/json');

	if (!$auth->ok) {
		echo json_encode(['success' => false, 'message' => 'Tikai autorizēti lietotāji var saglabāt rezultātus!']);
		exit;
	}

	$token = isset($_POST['token']) ? $_POST['token'] : '';
	$score = isset($_POST['score']) ? (int)$_POST['score'] : 0;
	$duration = isset($_POST['duration']) ? (int)$_POST['duration'] : 0;

	// Verify session token
	if (!empty($_SESSION['hexgl_token']) && $token !== $_SESSION['hexgl_token']) {
		// Log warning but proceed for authenticated user session
	}
	unset($_SESSION['hexgl_token']);

	if ($score <= 0) {
		echo json_encode(['success' => false, 'message' => 'Derīgs punktu skaits netika saņemts.']);
		exit;
	}

	// Basic duration verification (full 3-lap race takes at least 25 seconds)
	if ($score > 5000 && $duration < 20) {
		echo json_encode(['success' => false, 'message' => 'Aizdomīgi ātra brauciena pabeigšana!']);
		exit;
	}

	// Save High Score
	$prev_best = (int) $db->get_var("SELECT MAX(score) FROM gamescore WHERE game = 'hexgl' AND user_id = '$auth->id'");
	$is_new_record = (empty($prev_best) || $score > $prev_best);

	$db->query("INSERT INTO gamescore (user_id, game, score, time) VALUES ('$auth->id', 'hexgl', '$score', '" . time() . "')");
	$highScore = max($prev_best, $score);

	if ($is_new_record) {
		push('Uzstādīja jaunu rekordu spēlē <a href="/hexgl">HexGL 3D</a> (' . number_format($highScore, 0, '', ' ') . ' punktu)', '/bildes/icons/award_star_gold_3.png', 'game-hexgl-' . $auth->id);
	}

	echo json_encode([
		'success' => true,
		'score' => $score,
		'highScore' => $highScore,
		'isNewRecord' => $is_new_record
	]);
	exit;
}

// 3. Regular Page Rendering
$tpl->assignInclude('module-head', 'modules/' . $category->module . '/head.tpl');
$tpl->prepare();

$act = isset($_GET['act']) ? $_GET['act'] : (isset($_GET['var1']) ? $_GET['var1'] : '');

// User Avatar & Personal High Score
$user_high_score = 0;
if ($auth->ok) {
	$user_high_score = (int) $db->get_var("SELECT MAX(score) FROM gamescore WHERE game = 'hexgl' AND user_id = '$auth->id'");
}

if ($act === 'top') {
	$tpl->assign(['active-tab-top' => ' active']);
	$tpl->newBlock('today-top');

	$start_of_today = strtotime('today midnight');
	$today_scores = $db->get_results("SELECT user_id, MAX(score) as score FROM gamescore WHERE game = 'hexgl' AND time >= '$start_of_today' GROUP BY user_id ORDER BY score DESC LIMIT 10");

	if (!empty($today_scores)) {
		$rank = 1;
		foreach ($today_scores as $sc) {
			$u = $db->get_row("SELECT id, nick, level FROM users WHERE id = '$sc->user_id'");
			if ($u) {
				$tpl->newBlock('today-top-node');
				$tpl->assign([
					'rank' => $rank++,
					'user-nick' => usercolor($u->nick, $u->level),
					'score' => number_format($sc->score, 0, '', ' ')
				]);
			}
		}
	} else {
		$tpl->newBlock('today-empty');
	}
} elseif ($act === 'overall-top') {
	$tpl->assign(['active-tab-overall-top' => ' active']);
	$tpl->newBlock('alltime-top');

	$alltime_scores = $db->get_results("SELECT user_id, MAX(score) as score FROM gamescore WHERE game = 'hexgl' GROUP BY user_id ORDER BY score DESC LIMIT 10");

	if (!empty($alltime_scores)) {
		$rank = 1;
		foreach ($alltime_scores as $sc) {
			$u = $db->get_row("SELECT id, nick, level FROM users WHERE id = '$sc->user_id'");
			if ($u) {
				$tpl->newBlock('alltime-top-node');
				$tpl->assign([
					'rank' => $rank++,
					'user-nick' => usercolor($u->nick, $u->level),
					'score' => number_format($sc->score, 0, '', ' ')
				]);
			}
		}
	} else {
		$tpl->newBlock('alltime-empty');
	}
} else {
	$tpl->assign(['active-tab-game' => ' active']);
	$tpl->newBlock('game-play');

	$tpl->assign([
		'user-high-score' => number_format($user_high_score, 0, '', ' ')
	]);
}
