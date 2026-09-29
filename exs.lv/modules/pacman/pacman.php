<?php

/**
 * EXS.LV - Pac-Man (Exs-Man) Controller
 */

if (!isset($_SESSION)) {
	session_start();
}

// 1. Anti-Cheat Token Initialization
if (isset($_GET['action']) && $_GET['action'] === 'init_token') {
	header('Content-Type: application/json');
	$token = md5(uniqid(rand(), true));
	$_SESSION['pacman_token'] = $token;
	$_SESSION['pacman_start_time'] = time();
	echo json_encode(['success' => true, 'token' => $token]);
	exit;
}

// 2. Score Submission AJAX Handler
if (isset($_GET['action']) && $_GET['action'] === 'push') {
	header('Content-Type: application/json');

	if (!$auth->ok) {
		echo json_encode(['success' => false, 'guest' => true, 'error' => 'Tikai reģistrēti lietotāji var saglabāt rezultātus topā!']);
		exit;
	}

	$token = isset($_POST['token']) ? trim($_POST['token']) : '';
	if (!empty($_SESSION['pacman_token']) && !empty($token) && !hash_equals($_SESSION['pacman_token'], $token)) {
		echo json_encode(['success' => false, 'error' => 'Nederīgs sesijas žetons. Lūdzu pārlādējiet spēli.']);
		exit;
	}

	// Invalidate session token
	unset($_SESSION['pacman_token']);
	unset($_SESSION['pacman_start_time']);

	$score = isset($_POST['score']) ? intval($_POST['score']) : 0;
	$level = isset($_POST['level']) ? intval($_POST['level']) : 1;
	$duration = isset($_POST['duration']) ? intval($_POST['duration']) : 0;

	// Anti-cheat sanity checks
	if ($score <= 0) {
		echo json_encode(['success' => false, 'error' => 'Nederīgs punktu skaits.']);
		exit;
	}

	// Plausibility check: maximum reasonable points per second (eating dots and ghosts)
	if ($score > 3333360 || ($score > 500 && $duration > 0 && ($score / $duration) > 500)) {
		echo json_encode(['success' => false, 'error' => 'Aizdomīgi ātra punktu iegūšana.']);
		exit;
	}

	// Previous personal best
	$prev_best = (int)$db->get_var("SELECT MAX(score) FROM gamescore WHERE game = 'pacman' AND user_id = '$auth->id'");
	$is_new_record = (empty($prev_best) || $score > $prev_best);

	// Get Top 3 before insertion for overtake alerts
	$prev_top = get_game_top_users('pacman');

	// Insert new score record
	$db->query("INSERT INTO gamescore (user_id, game, score, time) VALUES ('$auth->id', 'pacman', '$score', '" . time() . "')");

	// Trigger notifications for overtaken players
	check_game_record_loss('pacman', $auth->id, $prev_top);

	$highScore = max($prev_best, $score);

	// Global activity stream push on personal best
	if ($is_new_record) {
		push(
			'Uzstādīja jaunu rekordu spēlē <a href="/pacman">Exs-Man</a> (' . number_format($highScore, 0, '', ' ') . ' punktu)',
			'/bildes/icons/games/pacman.png',
			'game-pacman-' . $auth->id
		);
	}

	// Current rank in all-time leaderboard
	$rank = (int)$db->get_var("SELECT COUNT(DISTINCT user_id) + 1 FROM gamescore WHERE game = 'pacman' AND score > '$score'");

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
$meta_description = 'Spēlē leģendāro Pac-Man (Exs-Man) tiešsaistē bez maksas! Vadi savu profila tēlu, ēd punktus un augļu bonusus, bēdz no spokiem un uzstādi labāko rekordu topos.';
$opengraph_meta['description'] = $meta_description;

$tpl->assignInclude('module-head', 'modules/' . $category->module . '/head.tpl');
$tpl->prepare();

// User Avatar & High Score
$user_avatar = '';
$user_high_score = 0;

if ($auth->ok) {
	$av = get_avatar($auth, 's');
	if (strpos($av, 'none.png') === false) {
		$user_avatar = $av;
	}
	$user_high_score = (int)$db->get_var("SELECT MAX(score) FROM gamescore WHERE game = 'pacman' AND user_id = '$auth->id'");
}

$preview_avatar = !empty($user_avatar) ? $user_avatar : '/bildes/icons/games/pacman.png';

$tpl->assign([
	'user-avatar' => $user_avatar,
	'preview-avatar' => $preview_avatar,
	'user-high-score' => $user_high_score,
	'is-logged' => ($auth->ok && !empty($user_avatar)) ? 1 : 0
]);

// Guest Notice Alert
if (!$auth->ok) {
	$tpl->newBlock('guest-notice');
}

// Helper for medal icons
function format_pacman_rank_badge($rank) {
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
	WHERE game = 'pacman' AND time >= '$start_of_today' 
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
				'user-place' => format_pacman_rank_badge($rank++),
				'user-url' => mkurl('user', $u->id, $u->nick),
				'user-nick' => usercolor($u->nick, $u->level),
				'score' => number_format($sc->score, 0, '', ' '),
				'user-special' => $is_me ? ' class="my-rank-row" style="background: rgba(234, 179, 8, 0.15); font-weight: bold;"' : ''
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
	WHERE game = 'pacman' 
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
				'user-place' => format_pacman_rank_badge($rank++),
				'user-url' => mkurl('user', $u->id, $u->nick),
				'user-nick' => usercolor($u->nick, $u->level),
				'score' => number_format($sc->score, 0, '', ' '),
				'user-special' => $is_me ? ' class="my-rank-row" style="background: rgba(234, 179, 8, 0.15); font-weight: bold;"' : ''
			]);
		}
	}
} else {
	$tpl->newBlock('alltime-empty');
}
