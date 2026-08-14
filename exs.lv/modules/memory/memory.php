<?php

/**
 * Atmiņas spēle (Memory Game) ar augstas karmas lietotāju avatariem,
 * maināmu tīkla izmēru un anti-cheat aizsardzību.
 */

// 1. AJAX: Initialize session token and card deck
if (isset($_GET['action']) && $_GET['action'] == 'init_token') {
	header('Content-Type: application/json');
	
	$grid = isset($_GET['grid']) ? trim($_GET['grid']) : '4x4';
	$valid_grids = [
		'4x4' => ['rows' => 4, 'cols' => 4, 'pairs' => 8, 'mult' => 1.0],
		'6x4' => ['rows' => 4, 'cols' => 6, 'pairs' => 12, 'mult' => 1.5],
		'6x6' => ['rows' => 6, 'cols' => 6, 'pairs' => 18, 'mult' => 2.0],
	];

	if (!isset($valid_grids[$grid])) {
		$grid = '4x4';
	}

	$grid_info = $valid_grids[$grid];
	$pairs_count = $grid_info['pairs'];

	// Fetch high karma users with avatars
	$high_karma_users = $db->get_results("SELECT id, nick, level, karma, avatar, av_alt FROM users WHERE deleted = 0 AND karma > 0 ORDER BY karma DESC LIMIT 80");

	if (!$high_karma_users || count($high_karma_users) < $pairs_count) {
		// Fallback query if not enough users
		$high_karma_users = $db->get_results("SELECT id, nick, level, karma, avatar, av_alt FROM users WHERE deleted = 0 ORDER BY id ASC LIMIT 80");
	}

	// Shuffle and pick required number of unique users for card pairs
	shuffle($high_karma_users);
	$selected_users = array_slice($high_karma_users, 0, $pairs_count);

	$cards = [];
	foreach ($selected_users as $idx => $u) {
		$av_url = get_avatar($u, 'm');
		// Add pair (two cards per user)
		$cards[] = [
			'pair_id' => $idx,
			'user_id' => $u->id,
			'nick' => $u->nick,
			'avatar' => $av_url
		];
		$cards[] = [
			'pair_id' => $idx,
			'user_id' => $u->id,
			'nick' => $u->nick,
			'avatar' => $av_url
		];
	}

	// Shuffle card positions
	shuffle($cards);

	// Generate security token
	$token = bin2hex(random_bytes(16));
	$_SESSION['memory_game'] = [
		'token' => $token,
		'start_time' => time(),
		'grid' => $grid,
		'pairs' => $pairs_count,
		'multiplier' => $grid_info['mult']
	];

	echo json_encode([
		'success' => true,
		'token' => $token,
		'grid' => $grid,
		'pairs' => $pairs_count,
		'cards' => $cards
	]);
	exit;
}

// 2. AJAX: Score submission with Anti-Cheat validation
if (isset($_GET['action']) && $_GET['action'] == 'push') {
	header('Content-Type: application/json');

	if (!$auth->ok) {
		echo json_encode(['success' => false, 'message' => 'Nesi autorizējies! Vispirms ienāc savā profilā.']);
		exit;
	}

	$sess = isset($_SESSION['memory_game']) ? $_SESSION['memory_game'] : null;
	$token = isset($_POST['token']) ? trim($_POST['token']) : '';
	$moves = isset($_POST['moves']) ? intval($_POST['moves']) : 0;
	$client_duration = isset($_POST['duration']) ? intval($_POST['duration']) : 0;

	// Anti-Cheat Check 1: Token verification
	if (!$sess || empty($token) || !hash_equals($sess['token'], $token)) {
		echo json_encode(['success' => false, 'message' => 'Nekorekts spēles žetons (Token verification failed).']);
		exit;
	}

	$start_time = intval($sess['start_time']);
	$server_duration = max(1, time() - $start_time);
	$pairs = intval($sess['pairs']);
	$multiplier = floatval($sess['multiplier']);

	// Clear token to prevent replay attacks
	unset($_SESSION['memory_game']);

	// Anti-Cheat Check 2: Minimum move count (must be at least equal to pair count)
	if ($moves < $pairs) {
		echo json_encode(['success' => false, 'message' => 'Nekorekts gājienu skaits!']);
		exit;
	}

	// Anti-Cheat Check 3: Duration check (at least 0.4s per pair minimum threshold)
	$min_duration = max(3, ceil($pairs * 0.4));
	if ($server_duration < $min_duration) {
		echo json_encode(['success' => false, 'message' => 'Spēles laiks ir pārāk īss!']);
		exit;
	}

	// Compute score
	$base_points = $pairs * 100;
	$move_bonus = max(0, ($pairs * 3 - $moves) * 15);
	$time_bonus = max(0, ($pairs * 12 - $server_duration) * 10);
	$raw_score = round(($base_points + $move_bonus + $time_bonus) * $multiplier);
	$score = max(50, $raw_score);

	// Save or Update High Score
	$current = $db->get_row("SELECT * FROM gamescore WHERE game = 'memory' AND user_id = '$auth->id'");
	$is_new_record = false;

	if (!$current) {
		$db->query("INSERT INTO gamescore (user_id, game, score, time) VALUES ('$auth->id', 'memory', '$score', '" . time() . "')");
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
		push('Uzstādīja jaunu rekordu <a href="/memory">Atmiņas spēlē</a> (' . number_format($highScore, 0, '', ' ') . ' punkti)', '/bildes/icons/games/memory.png', 'game-memory-' . $auth->id);
	}

	echo json_encode([
		'success' => true,
		'score' => $score,
		'highScore' => $highScore,
		'isNewRecord' => $is_new_record
	]);
	exit;
}

$tpl->assignInclude('module-head', 'modules/' . $category->module . '/head.tpl');
$tpl->prepare();

$active_sub = isset($_GET['var1']) ? $_GET['var1'] : (isset($_GET['act']) ? $_GET['act'] : '');

if ($active_sub === 'top') {
	$tpl->assign(['active-tab-top' => ' active']);

	$start_of_today = strtotime('today midnight');
	$scores = $db->get_results("SELECT * FROM gamescore WHERE game = 'memory' AND time >= '$start_of_today' ORDER BY score DESC LIMIT 100");
	$tpl->newBlock('memory-top');
	$tpl->assign('top-title', 'Šodienas tops');

	if ($scores) {
		$i = 1;
		foreach ($scores as $score) {
			$special = ($auth->id == $score->user_id) ? ' style="font-weight:bold"' : '';
			if ($i == 1) {
				$icon = '<img src="/bildes/icons/award_star_gold_3.png" alt="1." title="1." />';
			} elseif ($i == 2) {
				$icon = '<img src="/bildes/icons/award_star_silver_3.png" alt="2." title="2." />';
			} elseif ($i == 3) {
				$icon = '<img src="/bildes/icons/award_star_bronze_3.png" alt="3." title="3." />';
			} else {
				$icon = $i . '.';
			}

			$user = $db->get_row("SELECT id, nick, level FROM users WHERE id = '$score->user_id'");
			if ($user) {
				$tpl->newBlock('top-node');
				$tpl->assign([
					'user-place' => $icon,
					'user-special' => $special,
					'user-url' => mkurl('user', $user->id, $user->nick),
					'user-nick' => usercolor($user->nick, $user->level),
					'user-score' => number_format($score->score, 0, '', ' '),
					'user-date' => date('Y-m-d H:i', $score->time)
				]);
				$i++;
			}
		}
	} else {
		$tpl->newBlock('no-scores');
	}
} elseif ($active_sub === 'overall-top') {
	$tpl->assign(['active-tab-overall-top' => ' active']);

	$scores = $db->get_results("SELECT * FROM gamescore WHERE game = 'memory' ORDER BY score DESC LIMIT 100");
	$tpl->newBlock('memory-top');
	$tpl->assign('top-title', 'Visu laiku tops');

	if ($scores) {
		$i = 1;
		foreach ($scores as $score) {
			$special = ($auth->id == $score->user_id) ? ' style="font-weight:bold"' : '';
			if ($i == 1) {
				$icon = '<img src="/bildes/icons/award_star_gold_3.png" alt="1." title="1." />';
			} elseif ($i == 2) {
				$icon = '<img src="/bildes/icons/award_star_silver_3.png" alt="2." title="2." />';
			} elseif ($i == 3) {
				$icon = '<img src="/bildes/icons/award_star_bronze_3.png" alt="3." title="3." />';
			} else {
				$icon = $i . '.';
			}

			$user = $db->get_row("SELECT id, nick, level FROM users WHERE id = '$score->user_id'");
			if ($user) {
				$tpl->newBlock('top-node');
				$tpl->assign([
					'user-place' => $icon,
					'user-special' => $special,
					'user-url' => mkurl('user', $user->id, $user->nick),
					'user-nick' => usercolor($user->nick, $user->level),
					'user-score' => number_format($score->score, 0, '', ' '),
					'user-date' => date('Y-m-d H:i', $score->time)
				]);
				$i++;
			}
		}
	} else {
		$tpl->newBlock('no-scores');
	}
} else {
	$tpl->assign(['active-tab-game' => ' active']);
	$tpl->newBlock('memory-game');

	$user_high_score = 0;
	if ($auth->ok) {
		$user_high_score = (int) $db->get_var("SELECT score FROM gamescore WHERE game = 'memory' AND user_id = '$auth->id'");
	}

	$tpl->assign([
		'user-highscore' => $user_high_score,
		'is-logged' => $auth->ok ? 1 : 0
	]);

	if (!$auth->ok) {
		$tpl->newBlock('guest-notice');
		$tpl->newBlock('seo-text');
	}

	$sidebar_scores = $db->get_results("SELECT * FROM gamescore WHERE game = 'memory' ORDER BY score DESC LIMIT 10");
	if ($sidebar_scores) {
		$i = 1;
		foreach ($sidebar_scores as $score) {
			$user = $db->get_row("SELECT id, nick, level FROM users WHERE id = '$score->user_id'");
			if ($user) {
				$tpl->newBlock('mini-top-node');
				$tpl->assign([
					'place' => $i++,
					'user-url' => mkurl('user', $user->id, $user->nick),
					'user-nick' => usercolor($user->nick, $user->level),
					'score' => number_format($score->score, 0, '', ' ')
				]);
			}
		}
	}
}
