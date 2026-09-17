<?php

/**
 * EXS.LV - Tanki 1990 (Battle City) Controller
 */

if (!isset($_SESSION)) {
	session_start();
}

// 1. Anti-Cheat Token Initialization
if (isset($_GET['action']) && $_GET['action'] === 'init_token') {
	header('Content-Type: application/json');
	$token = md5(uniqid(rand(), true));
	$_SESSION['tanki_token'] = $token;
	$_SESSION['tanki_start_time'] = time();
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
	if (empty($token) || empty($_SESSION['tanki_token']) || $token !== $_SESSION['tanki_token']) {
		echo json_encode(['success' => false, 'error' => 'Nederīgs sesijas žetons. Lūdzu pārlādējiet spēli.']);
		exit;
	}

	// Invalidate token
	unset($_SESSION['tanki_token']);
	unset($_SESSION['tanki_start_time']);

	$score = isset($_POST['score']) ? intval($_POST['score']) : 0;
	$stage = isset($_POST['stage']) ? intval($_POST['stage']) : 1;
	$duration = isset($_POST['duration']) ? intval($_POST['duration']) : 0;

	// Anti-cheat sanity checks
	if ($score <= 0) {
		echo json_encode(['success' => false, 'error' => 'Nederīgs punktu skaits']);
		exit;
	}

	// Plausibility check: maximum reasonable points per second
	if ($score > 2000000 || ($score > 1000 && $duration > 0 && ($score / $duration) > 600)) {
		echo json_encode(['success' => false, 'error' => 'Aizdomīgi ātra punktu iegūšana']);
		exit;
	}

	// Previous personal best
	$prev_best = (int)$db->get_var("SELECT MAX(score) FROM gamescore WHERE game = 'tanki' AND user_id = '$auth->id'");
	$is_new_record = (empty($prev_best) || $score > $prev_best);

	// Get Top 3 before insertion for overtake alerts
	$prev_top = get_game_top_users('tanki');

	// Insert new score record
	$db->query("INSERT INTO gamescore (user_id, game, score, time) VALUES ('$auth->id', 'tanki', '$score', '" . time() . "')");

	// Trigger notifications for overtaken players
	check_game_record_loss('tanki', $auth->id, $prev_top);

	$highScore = max($prev_best, $score);

	// Global activity stream push on personal best
	if ($is_new_record) {
		push(
			'Uzstādīja jaunu rekordu spēlē <a href="/tanki">Tanki 1990</a> (' . number_format($highScore, 0, '', ' ') . ' punktu)',
			'/bildes/icons/games/tanki.png',
			'game-tanki-' . $auth->id
		);
	}

	// Current rank in all-time leaderboard
	$rank = (int)$db->get_var("SELECT COUNT(DISTINCT user_id) + 1 FROM gamescore WHERE game = 'tanki' AND score > '$score'");

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
$meta_description = 'Spēlē klasisko Tanki 1990 (Battle City) tanku arkādes spēli tiešsaistē par brīvu! Aizsargā EXS bāzi, iznīcini ienaidnieku tankus, vāc bonusus un kļūsti par līderi topos.';
$opengraph_meta['description'] = $meta_description;

$tpl->assignInclude('module-head', 'modules/' . $category->module . '/head.tpl');
$tpl->prepare();

// User Avatar & High Score
$user_avatar = '/dati/bildes/u_small/none.png';
$user_high_score = 0;

if ($auth->ok) {
	$user_avatar = get_avatar($auth, 's');
	$user_high_score = (int)$db->get_var("SELECT MAX(score) FROM gamescore WHERE game = 'tanki' AND user_id = '$auth->id'");
}

$tpl->assign([
	'user-avatar' => $user_avatar,
	'user-high-score' => $user_high_score
]);

// Guest Notice Alert
if (!$auth->ok) {
	$tpl->newBlock('guest-notice');
}

// Helper for medal icons
function format_tanki_rank_badge($rank) {
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
	WHERE game = 'tanki' AND time >= '$start_of_today' 
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
				'user-place' => format_tanki_rank_badge($rank++),
				'user-url' => mkurl('user', $u->id, $u->nick),
				'user-nick' => usercolor($u->nick, $u->level),
				'score' => number_format($sc->score, 0, '', ' '),
				'user-special' => $is_me ? ' class="my-rank-row" style="background: rgba(224, 102, 102, 0.15); font-weight: bold;"' : ''
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
	WHERE game = 'tanki' 
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
				'user-place' => format_tanki_rank_badge($rank++),
				'user-url' => mkurl('user', $u->id, $u->nick),
				'user-nick' => usercolor($u->nick, $u->level),
				'score' => number_format($sc->score, 0, '', ' '),
				'user-special' => $is_me ? ' class="my-rank-row" style="background: rgba(224, 102, 102, 0.15); font-weight: bold;"' : ''
			]);
		}
	}
} else {
	$tpl->newBlock('alltime-empty');
}
