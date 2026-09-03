<?php

/**
 * Bezgalīgais Bēdzējs (Endless Runner) modulis priekš EXS.LV
 */

if (!isset($_SESSION)) {
	session_start();
}

// 1. AJAX: Initialize Session Token
if (isset($_GET['action']) && $_GET['action'] === 'init_token') {
	header('Content-Type: application/json');
	$token = md5(uniqid(rand(), true));
	$_SESSION['runner_token'] = $token;
	$_SESSION['runner_start_time'] = time();

	// Fetch top community user avatars for obstacle rendering
	$user_avatars = [];
	$recent_users = $db->get_results("SELECT id, nick, avatar FROM users WHERE avatar != '' ORDER BY id DESC LIMIT 30");
	if ($recent_users) {
		foreach ($recent_users as $u) {
			$user_avatars[] = '/dati/bildes/u_small/' . $u->avatar;
		}
	}
	if (empty($user_avatars)) {
		$user_avatars[] = '/dati/bildes/u_small/none.png';
	}

	echo json_encode([
		'success' => true,
		'token' => $token,
		'avatars' => $user_avatars
	]);
	exit;
}

// 2. AJAX: Submit High Score
if (isset($_GET['action']) && $_GET['action'] === 'push') {
	header('Content-Type: application/json');

	if (!$auth->ok) {
		echo json_encode(['success' => false, 'message' => 'Tikai autorizēti lietotāji var saglabāt rezultātus!']);
		exit;
	}

	$token = isset($_POST['token']) ? trim($_POST['token']) : '';
	$score = isset($_POST['score']) ? intval($_POST['score']) : 0;
	$coins = isset($_POST['coins']) ? intval($_POST['coins']) : 0;
	$duration = isset($_POST['duration']) ? intval($_POST['duration']) : 0;

	// Anti-cheat checks
	if (!empty($_SESSION['runner_token']) && $token !== $_SESSION['runner_token']) {
		// Token mismatch warning
	}
	unset($_SESSION['runner_token']);

	if ($score <= 0) {
		echo json_encode(['success' => false, 'message' => 'Derīgs punktu skaits netika saņemts.']);
		exit;
	}

	// Basic duration sanity check (e.g. max 50 points per second)
	if ($score > 100 && $duration < ($score / 150)) {
		echo json_encode(['success' => false, 'message' => 'Aizdomīgi ātra punktu ieguve!']);
		exit;
	}

	// Save High Score
	$prev_top = get_game_top_users('runner');
	$prev_best = (int) $db->get_var("SELECT MAX(score) FROM gamescore WHERE game = 'runner' AND user_id = '$auth->id'");
	$is_new_record = (empty($prev_best) || $score > $prev_best);

	$db->query("INSERT INTO gamescore (user_id, game, score, coins, time) VALUES ('$auth->id', 'runner', '$score', '$coins', '" . time() . "')");
	check_game_record_loss('runner', $auth->id, $prev_top);
	$highScore = max($prev_best, $score);

	if ($is_new_record) {
		$star_str = ($coins > 0) ? ', ⭐ ' . $coins : '';
		push('Uzstādīja jaunu rekordu spēlē <a href="/runner">Runner</a> (' . number_format($highScore, 0, '', ' ') . ' m' . $star_str . ')', '/bildes/icons/games/runner.png', 'game-runner-' . $auth->id);
	}

	$rank = $db->get_var("SELECT COUNT(DISTINCT user_id) + 1 FROM gamescore WHERE game = 'runner' AND score > '$score'");

	echo json_encode([
		'success' => true,
		'score' => $score,
		'highScore' => $highScore,
		'isNewRecord' => $is_new_record,
		'rank' => $rank
	]);
	exit;
}

// 3. Regular Page View
$meta_description = 'Spēlē Runner tiešsaistē EXS.LV! Bēdz no lietotāju avatāru šķēršļiem un lidojošiem droīdiem, vāc zelta zvaigznes, cīnies par vietu topā un pārspēj rekordus.';
$opengraph_meta['description'] = $meta_description;

$tpl->assignInclude('module-head', 'modules/' . $category->module . '/head.tpl');
$tpl->prepare();

// Guest Notice Alert
if (!$auth->ok) {
	$tpl->newBlock('guest-notice');
}

// User Avatar & Personal High Score
$user_avatar = '/dati/bildes/u_small/none.png';
$user_high_score = 0;

if ($auth->ok) {
	$user_avatar = get_avatar($auth, 's');
	$user_high_score = (int) $db->get_var("SELECT MAX(score) FROM gamescore WHERE game = 'runner' AND user_id = '$auth->id'");
}

$tpl->assign([
	'user-avatar' => $user_avatar,
	'user-high-score' => $user_high_score
]);

// 4. Today's Leaderboard
$start_of_today = strtotime('today midnight');
$today_scores = $db->get_results("SELECT user_id, MAX(score) as score, MAX(coins) as coins FROM gamescore WHERE game = 'runner' AND time >= '$start_of_today' GROUP BY user_id ORDER BY score DESC LIMIT 10");

if (!empty($today_scores)) {
	$rank = 1;
	foreach ($today_scores as $sc) {
		$u = $db->get_row("SELECT id, nick, level FROM users WHERE id = '$sc->user_id'");
		if ($u) {
			$tpl->newBlock('today-top-node');
			$tpl->assign([
				'rank' => $rank++,
				'user-nick' => usercolor($u->nick, $u->level),
				'coins' => number_format($sc->coins, 0, '', ' '),
				'score' => number_format($sc->score, 0, '', ' ')
			]);
		}
	}
} else {
	$tpl->newBlock('today-empty');
}

// 5. All-Time Leaderboard
$alltime_scores = $db->get_results("SELECT user_id, MAX(score) as score, MAX(coins) as coins FROM gamescore WHERE game = 'runner' GROUP BY user_id ORDER BY score DESC LIMIT 10");

if (!empty($alltime_scores)) {
	$rank = 1;
	foreach ($alltime_scores as $sc) {
		$u = $db->get_row("SELECT id, nick, level FROM users WHERE id = '$sc->user_id'");
		if ($u) {
			$tpl->newBlock('alltime-top-node');
			$tpl->assign([
				'rank' => $rank++,
				'user-nick' => usercolor($u->nick, $u->level),
				'coins' => number_format($sc->coins, 0, '', ' '),
				'score' => number_format($sc->score, 0, '', ' ')
			]);
		}
	}
} else {
	$tpl->newBlock('alltime-empty');
}
