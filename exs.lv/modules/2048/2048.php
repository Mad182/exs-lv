<?php

/**
 * 2048 Spēle ar anti-cheat aizsardzību un topa rezultātu saglabāšanu
 */

// 1. AJAX session token creation
if (isset($_GET['action']) && $_GET['action'] == 'init_token') {
	header('Content-Type: application/json');
	$token = bin2hex(random_bytes(16));
	$_SESSION['2048_token'] = $token;
	$_SESSION['2048_start_time'] = time();
	echo json_encode(['success' => true, 'token' => $token]);
	exit;
}

// 2. AJAX score submission
if (isset($_GET['action']) && $_GET['action'] == 'push') {
	header('Content-Type: application/json');

	if (!$auth->ok) {
		echo json_encode(['success' => false, 'message' => 'Nesi autorizējies! Vispirms ienāc savā profilā.']);
		exit;
	}

	$token = isset($_POST['token']) ? trim($_POST['token']) : '';
	$score = isset($_POST['score']) ? intval($_POST['score']) : (isset($_GET['score']) ? intval($_GET['score']) : 0);
	$max_tile = isset($_POST['max_tile']) ? intval($_POST['max_tile']) : 0;
	$moves = isset($_POST['moves']) ? intval($_POST['moves']) : 0;

	// Anti-Cheat Check 1: Token Verification
	if (empty($_SESSION['2048_token']) || empty($token) || !hash_equals($_SESSION['2048_token'], $token)) {
		echo json_encode(['success' => false, 'message' => 'Nekorekts spēles žetons (Token verification failed).']);
		exit;
	}

	$start_time = isset($_SESSION['2048_start_time']) ? intval($_SESSION['2048_start_time']) : time();
	$duration = max(1, time() - $start_time);

	// Clear token to prevent replay
	unset($_SESSION['2048_token']);
	unset($_SESSION['2048_start_time']);

	if ($score <= 0) {
		echo json_encode(['success' => false, 'message' => 'Nekorekts punktu skaits!']);
		exit;
	}

	// Anti-Cheat Check 2: Minimum move count & duration ratio validation
	if ($score > 1000 && ($duration < 5 || $moves < 10)) {
		echo json_encode(['success' => false, 'message' => 'Spēles gājienu vai laika ilgums neatbilst uzrādītajam punktu skaitam!']);
		exit;
	}

	// Check if this score is a new personal record
	$prev_top = get_game_top_users('2048');
	$prev_best = (int) $db->get_var("SELECT MAX(score) FROM gamescore WHERE game = '2048' AND user_id = '$auth->id'");
	$is_new_record = (empty($prev_best) || $score > $prev_best);

	// Insert into gamescore table
	$db->query("INSERT INTO gamescore (user_id, game, score, time) VALUES ('$auth->id', '2048', '$score', '" . time() . "')");
	$insert_id = $db->insert_id;

	if ($insert_id || $db->affected_rows > 0) {
		check_game_record_loss('2048', $auth->id, $prev_top);
		if ($is_new_record) {
			push('Uzstādīja jaunu rekordu spēlē <a href="/2048-spele">2048</a> (' . number_format($score, 0, '', ' ') . ' punkti)', '/bildes/icons/games/2048.png', 'game-2048-' . $auth->id);
		}

		// Calculate player rank for this score
		$rank = $db->get_var("SELECT COUNT(*) + 1 FROM gamescore WHERE game = '2048' AND score > '$score'");
		echo json_encode([
			'success' => true,
			'message' => 'Tavs rezultāts (' . number_format($score, 0, '', ' ') . ' punkti) veiksmīgi saglabāts!',
			'rank' => $rank
		]);
	} else {
		echo json_encode(['success' => false, 'message' => 'Kļūda saglabājot rezultātu datubāzē!']);
	}
	exit;
}

// 3. Regular Page View
$tpl->assignInclude('module-head', 'modules/' . $category->module . '/head.tpl');
$tpl->prepare();

$act = isset($_GET['act']) ? $_GET['act'] : (isset($_GET['var1']) ? $_GET['var1'] : '');

if ($act == 'top') {
	// Today's Top Scores
	$tpl->assign(['active-tab-top' => ' active']);
	$today_start = strtotime('today midnight');
	$topusers = $db->get_results("SELECT * FROM gamescore WHERE game = '2048' AND time >= '$today_start' ORDER BY score DESC LIMIT 100");

	if ($topusers) {
		$tpl->newBlock('top-table');
		$i = 1;
		foreach ($topusers as $topuser) {
			$special = ($auth->ok && $auth->id == $topuser->user_id) ? ' style="font-weight:bold"' : '';
			if ($i == 1) {
				$icon = '<img src="/bildes/icons/award_star_gold_3.png" alt="1." title="1." />';
			} elseif ($i == 2) {
				$icon = '<img src="/bildes/icons/award_star_silver_3.png" alt="2." title="2." />';
			} elseif ($i == 3) {
				$icon = '<img src="/bildes/icons/award_star_bronze_3.png" alt="3." title="3." />';
			} else {
				$icon = $i . '.';
			}

			$usr = $db->get_row("SELECT nick, level FROM users WHERE id = '$topuser->user_id'");
			if ($usr) {
				$tpl->newBlock('top-node');
				$tpl->assign([
					'user-place' => $icon,
					'user-special' => $special,
					'user-url' => mkurl('user', $topuser->user_id, $usr->nick),
					'user-nick' => usercolor($usr->nick, $usr->level),
					'user-score' => number_format($topuser->score, 0, '', ' '),
					'user-time' => time_ago($topuser->time)
				]);
				$i++;
			}
		}
	}
} elseif ($act == 'overall-top') {
	// All-Time Top Scores
	$tpl->assign(['active-tab-overall-top' => ' active']);
	$topusers = $db->get_results("SELECT * FROM gamescore WHERE game = '2048' ORDER BY score DESC LIMIT 100");

	if ($topusers) {
		$tpl->newBlock('top-table');
		$i = 1;
		foreach ($topusers as $topuser) {
			$special = ($auth->ok && $auth->id == $topuser->user_id) ? ' style="font-weight:bold"' : '';
			if ($i == 1) {
				$icon = '<img src="/bildes/icons/award_star_gold_3.png" alt="1." title="1." />';
			} elseif ($i == 2) {
				$icon = '<img src="/bildes/icons/award_star_silver_3.png" alt="2." title="2." />';
			} elseif ($i == 3) {
				$icon = '<img src="/bildes/icons/award_star_bronze_3.png" alt="3." title="3." />';
			} else {
				$icon = $i . '.';
			}

			$usr = $db->get_row("SELECT nick, level FROM users WHERE id = '$topuser->user_id'");
			if ($usr) {
				$tpl->newBlock('top-node');
				$tpl->assign([
					'user-place' => $icon,
					'user-special' => $special,
					'user-url' => mkurl('user', $topuser->user_id, $usr->nick),
					'user-nick' => usercolor($usr->nick, $usr->level),
					'user-score' => number_format($topuser->score, 0, '', ' '),
					'user-time' => time_ago($topuser->time)
				]);
				$i++;
			}
		}
	}
} else {
	// Game View
	$tpl->assign(['active-tab-game' => ' active']);

	if (!$auth->ok) {
		$tpl->newBlock('game-login');
		$tpl->newBlock('seo-text');
	}

	$tpl->newBlock('game-play');

	// High score for logged in user
	$user_high_score = 0;
	if ($auth->ok && $auth->id > 0) {
		$user_high_score = intval($db->get_var("SELECT MAX(score) FROM gamescore WHERE game = '2048' AND user_id = '$auth->id'"));
	}
	$all_high_score = intval($db->get_var("SELECT MAX(score) FROM gamescore WHERE game = '2048'"));

	$tpl->assign([
		'user-high-score' => number_format($user_high_score, 0, '', ' '),
		'all-high-score' => number_format($all_high_score, 0, '', ' ')
	]);
}
