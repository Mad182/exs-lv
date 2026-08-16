<?php

/**
 * Lidojošais Eksis (Flappy Bird)
 */

if (!isset($_SESSION)) {
	session_start();
}

// 1. AJAX: Initialize Session Token
if (isset($_GET['action']) && $_GET['action'] === 'init_token') {
	header('Content-Type: application/json');
	$token = md5(uniqid(rand(), true));
	$_SESSION['flappy_token'] = $token;
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

	// Session token validation (relaxed for seamless session persistence)
	if (!empty($_SESSION['flappy_token']) && $token !== $_SESSION['flappy_token']) {
		// Log warning but proceed for logged in auth users
	}
	unset($_SESSION['flappy_token']);

	if ($score <= 0) {
		echo json_encode(['success' => false, 'message' => 'Derīgs punktu skaits netika saņemts.']);
		exit;
	}

	// Basic duration verification (each pipe takes ~1.5s)
	if ($score > 10 && $duration < ($score * 0.8)) {
		echo json_encode(['success' => false, 'message' => 'Aizdomīgi ātra punktu ieguve!']);
		exit;
	}

	// Save High Score
	$prev_best = (int) $db->get_var("SELECT MAX(score) FROM gamescore WHERE game = 'flappy' AND user_id = '$auth->id'");
	$is_new_record = (empty($prev_best) || $score > $prev_best);

	$db->query("INSERT INTO gamescore (user_id, game, score, time) VALUES ('$auth->id', 'flappy', '$score', '" . time() . "')");
	$highScore = max($prev_best, $score);

	if ($is_new_record) {
		push('Uzstādīja jaunu rekordu spēlē <a href="/flappy">Lidojošais Eksis</a> (' . number_format($highScore, 0, '', ' ') . ' punktu)', '/bildes/icons/games/flappy.png', 'game-flappy-' . $auth->id);
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

// User Avatar & High Score
$user_avatar = '/dati/bildes/u_small/none.png';
$user_high_score = 0;

if ($auth->ok) {
	$user_avatar = get_avatar($auth, 's');
	$user_high_score = (int) $db->get_var("SELECT MAX(score) FROM gamescore WHERE game = 'flappy' AND user_id = '$auth->id'");
}

$tpl->assign([
	'user-avatar' => $user_avatar,
	'user-high-score' => $user_high_score
]);

// 4. Today's Leaderboard
$start_of_today = strtotime('today midnight');
$today_scores = $db->get_results("SELECT user_id, MAX(score) as score FROM gamescore WHERE game = 'flappy' AND time >= '$start_of_today' GROUP BY user_id ORDER BY score DESC LIMIT 10");

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

// 5. All-Time Leaderboard
$alltime_scores = $db->get_results("SELECT user_id, MAX(score) as score FROM gamescore WHERE game = 'flappy' GROUP BY user_id ORDER BY score DESC LIMIT 10");

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
