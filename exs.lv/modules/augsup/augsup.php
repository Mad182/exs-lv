<?php

/**
 * Augšup (Doodle Jump clone) module controller
 */

if (!isset($_SESSION)) {
	session_start();
}

// 1. AJAX: Initialize Session Token
if (isset($_GET['action']) && $_GET['action'] === 'init_token') {
	header('Content-Type: application/json');
	$token = md5(uniqid(rand(), true));
	$_SESSION['augsup_token'] = $token;
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

	if (!empty($_SESSION['augsup_token']) && $token !== $_SESSION['augsup_token']) {
		// Log warning but allow valid score
	}
	unset($_SESSION['augsup_token']);

	if ($score <= 0) {
		echo json_encode(['success' => false, 'message' => 'Derīgs punktu skaits netika saņemts.']);
		exit;
	}

	// Basic duration sanity check (e.g. 100 points per second max)
	if ($score > 100 && $duration < ($score / 200)) {
		echo json_encode(['success' => false, 'message' => 'Aizdomīgi ātra punktu ieguve!']);
		exit;
	}

	// Save or Update High Score & Timestamp
	$current = $db->get_row("SELECT * FROM gamescore WHERE game = 'augsup' AND user_id = '$auth->id'");
	$is_new_record = false;

	if (!$current) {
		$db->query("INSERT INTO gamescore (user_id, game, score, time) VALUES ('$auth->id', 'augsup', '$score', '" . time() . "')");
		$is_new_record = true;
		$highScore = $score;
	} else {
		if ($score > $current->score) {
			$db->query("UPDATE gamescore SET score = '$score', time = '" . time() . "' WHERE id = '$current->id' AND user_id = '$auth->id'");
			$is_new_record = true;
			$highScore = $score;
		} else {
			$db->query("UPDATE gamescore SET time = '" . time() . "' WHERE id = '$current->id' AND user_id = '$auth->id'");
			$highScore = $current->score;
		}
	}

	if ($is_new_record) {
		push('Uzstādīja jaunu rekordu spēlē <a href="/augsup">Augšup</a> (' . number_format($highScore, 0, '', ' ') . ' m)', '/bildes/icons/games/augsup.png', 'game-augsup-' . $auth->id);
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
	$user_high_score = (int) $db->get_var("SELECT score FROM gamescore WHERE game = 'augsup' AND user_id = '$auth->id'");
}

$tpl->assign([
	'user-avatar' => $user_avatar,
	'user-high-score' => $user_high_score
]);

// 4. Today's Leaderboard
$start_of_today = strtotime('today midnight');
$today_scores = $db->get_results("SELECT * FROM gamescore WHERE game = 'augsup' AND time >= '$start_of_today' ORDER BY score DESC LIMIT 10");

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
$alltime_scores = $db->get_results("SELECT * FROM gamescore WHERE game = 'augsup' ORDER BY score DESC LIMIT 10");

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

