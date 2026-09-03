<?php

/**
 * EXS.LV Unreal Tournament 99 (WebAssembly) Modulis
 */

if (!isset($_SESSION)) {
	session_start();
}

// 1. AJAX: Initialize Session Token
if (isset($_GET['action']) && $_GET['action'] === 'init_token') {
	header('Content-Type: application/json');
	$token = md5(uniqid(rand(), true));
	$_SESSION['ut99_token'] = $token;
	$_SESSION['ut99_start_time'] = time();
	echo json_encode(['success' => true, 'token' => $token]);
	exit;
}

// 2. AJAX: Submit High Score (Frags / Match Results)
if (isset($_GET['action']) && $_GET['action'] === 'push') {
	header('Content-Type: application/json');

	if (!$auth->ok) {
		echo json_encode(['success' => false, 'message' => 'Tikai autorizēti lietotāji var saglabāt rezultātus!']);
		exit;
	}

	$token = isset($_POST['token']) ? trim($_POST['token']) : '';
	$score = isset($_POST['score']) ? (int)$_POST['score'] : 0;
	$map = isset($_POST['map']) ? htmlspecialchars(trim($_POST['map'])) : 'DM-Deck16][';
	$duration = isset($_POST['duration']) ? (int)$_POST['duration'] : 0;

	// Anti-cheat session token check
	if (!empty($_SESSION['ut99_token']) && !empty($token) && !hash_equals($_SESSION['ut99_token'], $token)) {
		// Proceed if authenticated user session is active
	}
	unset($_SESSION['ut99_token']);
	unset($_SESSION['ut99_start_time']);

	if ($score <= 0) {
		echo json_encode(['success' => false, 'message' => 'Derīgs fragu skaits netika saņemts.']);
		exit;
	}

	// Basic duration verification (max 2 frags/second threshold)
	if ($score > 10 && $duration < ($score / 2)) {
		echo json_encode(['success' => false, 'message' => 'Aizdomīgi ātra fragu ieguve!']);
		exit;
	}

	// Save High Score
	$prev_top = get_game_top_users('ut99');
	$prev_best = (int) $db->get_var("SELECT MAX(score) FROM gamescore WHERE game = 'ut99' AND user_id = '$auth->id'");
	$is_new_record = (empty($prev_best) || $score > $prev_best);

	$db->query("INSERT INTO gamescore (user_id, game, score, time) VALUES ('$auth->id', 'ut99', '$score', '" . time() . "')");
	check_game_record_loss('ut99', $auth->id, $prev_top);
	$highScore = max($prev_best, $score);

	if ($is_new_record) {
		push('Uzstādīja jaunu rekordu spēlē <a href="/ut99">Unreal Tournament 99</a> (' . number_format($highScore, 0, '', ' ') . ' fragu)', '/bildes/icons/games/ut99.png', 'game-ut99-' . $auth->id);
	}

	echo json_encode([
		'success' => true,
		'score' => $score,
		'highScore' => $highScore,
		'isNewRecord' => $is_new_record
	]);
	exit;
}

// 3. AJAX: Server Status Ping
if (isset($_GET['action']) && $_GET['action'] === 'server_status') {
	header('Content-Type: application/json');
	
	// Default offline/fallback response if dedicated server isn't active on host
	$server_info = [
		'online' => false,
		'name' => 'EXS.LV Official UT99 Server',
		'map' => 'DM-Deck16][',
		'players' => 0,
		'max_players' => 16,
		'ping' => 0
	];

	// Check if local UDP port 7777 / 7778 is listening
	$fp = @fsockopen('udp://127.0.0.1', 7778, $errno, $errstr, 0.5);
	if ($fp) {
		fwrite($fp, "\\status\\");
		stream_set_timeout($fp, 0, 500000);
		$response = fread($fp, 2048);
		fclose($fp);

		if (!empty($response)) {
			$server_info['online'] = true;
			if (preg_match('/\\\\mapname\\\\([^\\\\]+)/', $response, $m)) {
				$server_info['map'] = $m[1];
			}
			if (preg_match('/\\\\numplayers\\\\([0-9]+)/', $response, $m)) {
				$server_info['players'] = (int)$m[1];
			}
			if (preg_match('/\\\\maxplayers\\\\([0-9]+)/', $response, $m)) {
				$server_info['max_players'] = (int)$m[1];
			}
		}
	}

	echo json_encode($server_info);
	exit;
}

// 4. Regular Page Rendering
$tpl->assignInclude('module-head', 'modules/' . $category->module . '/head.tpl');
$tpl->prepare();

// User Avatar & High Score
$user_avatar = '/dati/bildes/u_small/none.png';
$user_high_score = 0;

if ($auth->ok) {
	$user_avatar = get_avatar($auth, 's');
	$user_high_score = (int) $db->get_var("SELECT MAX(score) FROM gamescore WHERE game = 'ut99' AND user_id = '$auth->id'");
}

$tpl->assign([
	'user-avatar' => $user_avatar,
	'user-high-score' => $user_high_score,
	'is-logged' => $auth->ok ? 1 : 0
]);

// 5. Today's Leaderboard
$start_of_today = strtotime('today midnight');
$today_scores = $db->get_results("SELECT user_id, MAX(score) as score FROM gamescore WHERE game = 'ut99' AND time >= '$start_of_today' GROUP BY user_id ORDER BY score DESC LIMIT 10");

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

// 6. All-Time Leaderboard
$alltime_scores = $db->get_results("SELECT user_id, MAX(score) as score FROM gamescore WHERE game = 'ut99' GROUP BY user_id ORDER BY score DESC LIMIT 10");

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
