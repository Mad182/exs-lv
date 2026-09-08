<?php

/**
 * EXS.LV - Arkanoid (Klucīšu lauzējs) Controller
 */

if (!isset($_SESSION)) {
	session_start();
}

// 1. Initialize Anti-Cheat Token
if (isset($_GET['action']) && $_GET['action'] === 'init_token') {
	header('Content-Type: application/json');
	$token = md5(uniqid(rand(), true));
	$_SESSION['arkanoid_token'] = $token;
	$_SESSION['arkanoid_start_time'] = time();
	echo json_encode(['success' => true, 'token' => $token]);
	exit;
}

// 2. Score Submission AJAX Handler
if (isset($_GET['action']) && $_GET['action'] === 'push') {
	header('Content-Type: application/json');

	if (!$auth->ok) {
		echo json_encode(['success' => false, 'error' => 'Tikai reģistrēti lietotāji var saglabāt rezultātus!']);
		exit;
	}

	$token = isset($_POST['token']) ? trim($_POST['token']) : '';
	if (empty($token) || empty($_SESSION['arkanoid_token']) || $token !== $_SESSION['arkanoid_token']) {
		echo json_encode(['success' => false, 'error' => 'Nederīgs sesijas žetons. Lūdzu pārlādējiet spēli.']);
		exit;
	}

	// Invalidate token
	unset($_SESSION['arkanoid_token']);
	unset($_SESSION['arkanoid_start_time']);

	$score = isset($_POST['score']) ? intval($_POST['score']) : 0;
	$round = isset($_POST['round']) ? intval($_POST['round']) : 1;
	$duration = isset($_POST['duration']) ? intval($_POST['duration']) : 0;

	// Anti-cheat sanity checks
	if ($score <= 0) {
		echo json_encode(['success' => false, 'error' => 'Nederīgs punktu skaits']);
		exit;
	}

	// Maximum plausible human score limit & timing check (rate limit per second)
	if ($score > 1000000 || ($score > 1000 && $duration > 0 && ($score / $duration) > 500)) {
		echo json_encode(['success' => false, 'error' => 'Aizdomīgi ātra punktu iegūšana']);
		exit;
	}

	// Check user previous best
	$prev_best = (int)$db->get_var("SELECT MAX(score) FROM gamescore WHERE game = 'arkanoid' AND user_id = '$auth->id'");
	$is_new_record = (empty($prev_best) || $score > $prev_best);

	// Get Top 3 before insertion for overtake notifications
	$prev_top = get_game_top_users('arkanoid');

	// Insert new score record
	$db->query("INSERT INTO gamescore (user_id, game, score, time) VALUES ('$auth->id', 'arkanoid', '$score', '" . time() . "')");

	// Trigger notifications for players who lost top 1/2/3
	check_game_record_loss('arkanoid', $auth->id, $prev_top);

	$highScore = max($prev_best, $score);

	// Activity stream push on personal best
	if ($is_new_record) {
		push(
			'Uzstādīja jaunu rekordu spēlē <a href="/arkanoid">Arkanoid</a> (' . number_format($highScore, 0, '', ' ') . ' punktu)',
			'/bildes/icons/games/arkanoid.png',
			'game-arkanoid-' . $auth->id
		);
	}

	// Current rank in all-time leaderboard
	$rank = (int)$db->get_var("SELECT COUNT(DISTINCT user_id) + 1 FROM gamescore WHERE game = 'arkanoid' AND score > '$score'");

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
$meta_description = 'Spēlē klasisko Arkanoid arkādes spēli tiešsaistē par brīvu! Atsit bumbu ar Vaus kuģi, sašķaidi krāsainos blokus, ķer kapsulu bonusus un iekaro spēlētāju topu.';
$opengraph_meta['description'] = $meta_description;

$tpl->assignInclude('module-head', 'modules/' . $category->module . '/head.tpl');
$tpl->prepare();

// User Avatar & High Score
$user_avatar = '/dati/bildes/u_small/none.png';
$user_high_score = 0;

if ($auth->ok) {
	$user_avatar = get_avatar($auth, 's');
	$user_high_score = (int)$db->get_var("SELECT MAX(score) FROM gamescore WHERE game = 'arkanoid' AND user_id = '$auth->id'");
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

// 4. Today's Leaderboard
$start_of_today = strtotime('today midnight');
$today_scores = $db->get_results("
	SELECT user_id, MAX(score) as score, MIN(time) as best_time 
	FROM gamescore 
	WHERE game = 'arkanoid' AND time >= '$start_of_today' 
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
				'user-special' => $is_me ? ' class="my-rank-row" style="background: rgba(235, 60, 60, 0.1); font-weight: bold;"' : ''
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
	WHERE game = 'arkanoid' 
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
				'user-special' => $is_me ? ' class="my-rank-row" style="background: rgba(235, 60, 60, 0.1); font-weight: bold;"' : ''
			]);
		}
	}
} else {
	$tpl->newBlock('alltime-empty');
}
