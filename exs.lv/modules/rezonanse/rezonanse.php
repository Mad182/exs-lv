<?php

/**
 * EXS.LV - Rezonanse (Synth Pulse Arena) Controller
 */

// 1. Initialize Anti-Cheat Token
if (isset($_GET['action']) && $_GET['action'] === 'init_token') {
	header('Content-Type: application/json');
	$_SESSION['rezonanse_token'] = md5(uniqid(rand(), true));
	$_SESSION['rezonanse_start_time'] = time();
	echo json_encode(['token' => $_SESSION['rezonanse_token']]);
	exit;
}

// 2. Score Submission AJAX Handler
if (isset($_GET['action']) && $_GET['action'] === 'push') {
	header('Content-Type: application/json');

	if (!$auth->ok) {
		echo json_encode(['success' => false, 'error' => 'Nav pieslēdzies']);
		exit;
	}

	$token = isset($_POST['token']) ? trim($_POST['token']) : '';
	if (empty($token) || empty($_SESSION['rezonanse_token']) || $token !== $_SESSION['rezonanse_token']) {
		echo json_encode(['success' => false, 'error' => 'Nederīgs sesijas žetons']);
		exit;
	}

	// Invalidate token to prevent replay
	unset($_SESSION['rezonanse_token']);

	$score = isset($_POST['score']) ? intval($_POST['score']) : 0;
	$combo = isset($_POST['combo']) ? intval($_POST['combo']) : 0;
	$duration = isset($_POST['duration']) ? intval($_POST['duration']) : 0;
	$perfects = isset($_POST['perfects']) ? intval($_POST['perfects']) : 0;

	// Anti-cheat sanity checks
	if ($score <= 0) {
		echo json_encode(['success' => false, 'error' => 'Nederīgs punktu skaits']);
		exit;
	}

	// Basic timing sanity check (must have survived at least 3 seconds)
	if ($duration < 3 || $score > 1000000) {
		echo json_encode(['success' => false, 'error' => 'Aizdomīgi rezultāta parametri']);
		exit;
	}

	// Check user previous best
	$prev_best = (int)$db->get_var("SELECT MAX(score) FROM gamescore WHERE game = 'rezonanse' AND user_id = '$auth->id'");
	$is_new_record = (empty($prev_best) || $score > $prev_best);

	// Get Top 3 before insertion for overtake notifications
	$prev_top = get_game_top_users('rezonanse');

	// Insert new score record
	$db->query("INSERT INTO gamescore (user_id, game, score, time) VALUES ('$auth->id', 'rezonanse', '$score', '" . time() . "')");

	// Trigger notifications for players who lost top 1/2/3
	check_game_record_loss('rezonanse', $auth->id, $prev_top);

	$highScore = max($prev_best, $score);

	// Activity stream push on personal best
	if ($is_new_record) {
		push(
			'Uzstādīja jaunu rekordu spēlē <a href="/rezonanse">Rezonanse</a> (' . number_format($highScore, 0, '', ' ') . ' punktu)',
			'/bildes/icons/games/rezonanse.png',
			'game-rezonanse-' . $auth->id
		);
	}

	// Current rank in all-time leaderboard
	$rank = (int)$db->get_var("SELECT COUNT(DISTINCT user_id) + 1 FROM gamescore WHERE game = 'rezonanse' AND score > '$score'");

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
$meta_description = 'Spēlē Rezonanse (Synth Pulse Arena) tiešsaistē EXS.LV! Hipnotiska ritma un izdzīvošanas arkāde, kur spēles pasaule ir reāllaika sintezators.';
$opengraph_meta['description'] = $meta_description;

$tpl->assignInclude('module-head', 'modules/' . $category->module . '/head.tpl');
$tpl->prepare();

// User Avatar & High Score
$user_avatar = '/dati/bildes/u_small/none.png';
$user_high_score = 0;

if ($auth->ok) {
	$user_avatar = get_avatar($auth, 's');
	$user_high_score = (int)$db->get_var("SELECT MAX(score) FROM gamescore WHERE game = 'rezonanse' AND user_id = '$auth->id'");
}

$tpl->assign([
	'user-avatar' => $user_avatar,
	'user-high-score' => $user_high_score
]);

// Guest Notice Alert
if (!$auth->ok) {
	$tpl->newBlock('guest-notice');
}

// Helper for top rank medal icons
if (!function_exists('format_rank_badge')) {
	function format_rank_badge($rank) {
		if ($rank === 1) {
			return '<img src="/bildes/icons/award_star_gold_3.png" alt="1." title="1. vieta" />';
		} elseif ($rank === 2) {
			return '<img src="/bildes/icons/award_star_silver_3.png" alt="2." title="2. vieta" />';
		} elseif ($rank === 3) {
			return '<img src="/bildes/icons/award_star_bronze_3.png" alt="3." title="3. vieta" />';
		}
		return $rank . '.';
	}
}

// 4. Today's Leaderboard
$start_of_today = strtotime('today midnight');
$today_scores = $db->get_results("
	SELECT user_id, MAX(score) as score, MIN(time) as best_time 
	FROM gamescore 
	WHERE game = 'rezonanse' AND time >= '$start_of_today' 
	GROUP BY user_id 
	ORDER BY score DESC, best_time ASC 
	LIMIT 20
");

if (!empty($today_scores)) {
	$rank = 1;
	foreach ($today_scores as $sc) {
		$u = $db->get_row("SELECT id, nick, level FROM users WHERE id = '$sc->user_id'");
		if ($u) {
			$tpl->newBlock('today-top-node');
			$is_me = ($auth->ok && $auth->id == $u->id);
			$tpl->assign([
				'user-place' => format_rank_badge($rank++),
				'user-url' => mkurl('user', $u->id, $u->nick),
				'user-nick' => usercolor($u->nick, $u->level),
				'score' => number_format($sc->score, 0, '', ' '),
				'user-special' => $is_me ? ' class="my-rank-row" style="background: rgba(6, 182, 212, 0.12); font-weight: bold;"' : ''
			]);
		}
	}
} else {
	$tpl->newBlock('today-empty');
}

// 5. All-Time Leaderboard
$alltime_scores = $db->get_results("
	SELECT user_id, MAX(score) as score, MIN(time) as best_time 
	FROM gamescore 
	WHERE game = 'rezonanse' 
	GROUP BY user_id 
	ORDER BY score DESC, best_time ASC 
	LIMIT 20
");

if (!empty($alltime_scores)) {
	$rank = 1;
	foreach ($alltime_scores as $sc) {
		$u = $db->get_row("SELECT id, nick, level FROM users WHERE id = '$sc->user_id'");
		if ($u) {
			$tpl->newBlock('alltime-top-node');
			$is_me = ($auth->ok && $auth->id == $u->id);
			$tpl->assign([
				'user-place' => format_rank_badge($rank++),
				'user-url' => mkurl('user', $u->id, $u->nick),
				'user-nick' => usercolor($u->nick, $u->level),
				'score' => number_format($sc->score, 0, '', ' '),
				'user-special' => $is_me ? ' class="my-rank-row" style="background: rgba(6, 182, 212, 0.12); font-weight: bold;"' : ''
			]);
		}
	}
} else {
	$tpl->newBlock('alltime-empty');
}
