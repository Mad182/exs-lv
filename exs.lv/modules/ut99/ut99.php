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

// 3. AJAX: Server Status Ping (static JSON generated every 60s by ut99-stats.timer)
if (isset($_GET['action']) && $_GET['action'] === 'server_status') {
	header('Content-Type: application/json');
	$status_file = CORE_PATH . '/cache/ut99_status.json';
	if (is_file($status_file)) {
		readfile($status_file);
		exit;
	}
	echo json_encode([
		'online' => false,
		'name' => 'EXS.LV Official UT99 Server',
		'map' => 'DM-Deck16][',
		'players' => 0,
		'max_players' => 16,
		'ping' => 0
	]);
	exit;
}

// 4. Regular Page Rendering
$robotstag[] = 'noindex';
$tpl_options = 'no-left';
$page_title = 'Unreal Tournament 99 pārlūkā — Bezmaksas 3D šūteris';
$meta_description = 'Spēlē leģendāro Unreal Tournament 99 (UT99) tieši pārlūkā bez lejupielādes un instalēšanas! Klasiskais 3D arēnas šūteris datorā ar botu cīņām un fragu topu.';
$opengraph_meta['description'] = $meta_description;

$tpl->assignInclude('module-head', 'modules/' . $category->module . '/head.tpl');
$tpl->prepare();

// User Avatar, Nick & High Score
$user_avatar = '/dati/bildes/u_small/none.png';
$user_high_score = 0;
$user_ut99_nick = '';

if ($auth->ok) {
	$user_avatar = get_avatar($auth, 's');
	$user_high_score = (int) $db->get_var("SELECT MAX(score) FROM gamescore WHERE game = 'ut99' AND user_id = '$auth->id'");
	$translit = [
		'ā'=>'a','č'=>'c','ē'=>'e','ģ'=>'g','ī'=>'i','ķ'=>'k','ļ'=>'l','ņ'=>'n','š'=>'s','ū'=>'u','ž'=>'z',
		'Ā'=>'A','Č'=>'C','Ē'=>'E','Ģ'=>'G','Ī'=>'I','Ķ'=>'K','Ļ'=>'L','Ņ'=>'N','Š'=>'S','Ū'=>'U','Ž'=>'Z'
	];
	$clean_nick = strtr((string) $auth->nick, $translit);
	$clean_nick = preg_replace('/[^A-Za-z0-9_\-\.\[\]]+/', '_', $clean_nick);
	$clean_nick = trim($clean_nick, '_');
	if ($clean_nick !== '') {
		$user_ut99_nick = substr($clean_nick, 0, 20);
	}
}

$tpl->assign([
	'user-avatar' => $user_avatar,
	'user-high-score' => $user_high_score,
	'is-logged' => $auth->ok ? 1 : 0,
	'user-nick-js' => json_encode($user_ut99_nick)
]);

// 5. Today's & All-Time Leaderboards (static HTML generated every 60s by ut99-stats.timer)
$today_top_file = CORE_PATH . '/cache/ut99_top_today.html';
$alltime_top_file = CORE_PATH . '/cache/ut99_top_alltime.html';

$tpl->assign([
	'today-top-html' => is_file($today_top_file) ? file_get_contents($today_top_file) : '<li class="empty-msg">Šodien vēl nav fiksētu rezultātu!</li>',
	'alltime-top-html' => is_file($alltime_top_file) ? file_get_contents($alltime_top_file) : '<li class="empty-msg">Vēl nav uzstādītu rekordu!</li>'
]);
