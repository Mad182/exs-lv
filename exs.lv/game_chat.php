<?php

/**
 * EXS.LV Shared Game Chat Backend
 * Real-time messaging API across all browser games
 */

require('configdb.php');
require(CORE_PATH . '/includes/class.mdb.php');
require(CORE_PATH . '/includes/class.auth.php');
require(CORE_PATH . '/includes/functions.core.php');
require(CORE_PATH . '/includes/site_loader.php');

// Memcached connection
$m = new Memcached();
if (defined('Memcached::HAVE_IGBINARY') && Memcached::HAVE_IGBINARY) {
	$m->setOption(Memcached::OPT_SERIALIZER, Memcached::SERIALIZER_IGBINARY);
}
$m->addServer($mc_host, $mc_port);

$db = new mdb($username, $password, $database, $hostname);

if (!isset($_SESSION)) {
	session_start();
}

$site_access = get_site_access();
$auth = new Auth();

header('Content-Type: application/json; charset=utf-8');

$action = isset($_GET['action']) ? trim($_GET['action']) : 'fetch';

// Canonical Game Metadata Map
$game_catalog_meta = [
	'tetris' => ['title' => 'Tetris', 'url' => '/tetris', 'icon' => '🎮'],
	'snake' => ['title' => 'Čūska', 'url' => '/snake', 'icon' => '🐍'],
	'karatavas' => ['title' => 'Karātavas', 'url' => '/karatavas', 'icon' => '🔤'],
	'memory' => ['title' => 'Atmiņas spēle', 'url' => '/memory', 'icon' => '🧠'],
	'2048' => ['title' => '2048', 'url' => '/2048-spele', 'icon' => '🔢'],
	'2048-spele' => ['title' => '2048', 'url' => '/2048-spele', 'icon' => '🔢'],
	'minu-mekletajs' => ['title' => 'Mīnu Meklētājs', 'url' => '/minu-mekletajs', 'icon' => '💣'],
	'sudoku' => ['title' => 'Sudoku', 'url' => '/sudoku', 'icon' => '🔢'],
	'wordle' => ['title' => 'Wordle', 'url' => '/wordle', 'icon' => '🟩'],
	'rulete' => ['title' => 'Rulete', 'url' => '/rulete', 'icon' => '🎰'],
	'desas' => ['title' => 'Desas', 'url' => '/desas', 'icon' => '⭕'],
	'flappy' => ['title' => 'Lidojošais Eksis', 'url' => '/flappy', 'icon' => '🐦'],
	'invaders' => ['title' => 'Space Invaders', 'url' => '/invaders', 'icon' => '👾'],
	'augsup' => ['title' => 'Augšup', 'url' => '/augsup', 'icon' => '🚀'],
	'vardes' => ['title' => 'Vardes', 'url' => '/vardes', 'icon' => '🐸'],
	'runner' => ['title' => 'Runner', 'url' => '/runner', 'icon' => '🏃'],
	'tornis' => ['title' => 'Tornis', 'url' => '/tornis', 'icon' => '🏗️'],
	'arkanoid' => ['title' => 'Arkanoid', 'url' => '/arkanoid', 'icon' => '🧱'],
	'rezonanse' => ['title' => 'Rezonanse', 'url' => '/rezonanse', 'icon' => '💥'],
	'tanki' => ['title' => 'Tanki 1990', 'url' => '/tanki', 'icon' => '🛡️'],
	'ut99' => ['title' => 'Unreal Tournament', 'url' => '/ut99', 'icon' => '⚔️'],
	'speles' => ['title' => 'Spēļu katalogs', 'url' => '/speles', 'icon' => '🎲'],
];

/**
 * Helper to resolve game information
 */
function resolve_game_meta($slug, $title = '') {
	global $game_catalog_meta, $db;
	$slug = trim(strtolower($slug));
	if (isset($game_catalog_meta[$slug])) {
		return $game_catalog_meta[$slug];
	}
	if (!empty($title)) {
		return ['title' => $title, 'url' => '/' . $slug, 'icon' => '🎮'];
	}
	// Try looking up in games table
	$g = $db->get_row("SELECT title, url FROM `games` WHERE `slug` = '" . sanitize($slug) . "' OR `game_code` = '" . sanitize($slug) . "' LIMIT 1");
	if ($g) {
		return ['title' => $g->title, 'url' => $g->url, 'icon' => '🎮'];
	}
	if ($slug === '' || $slug === 'speles') {
		return ['title' => 'Spēļu katalogs', 'url' => '/speles', 'icon' => '🎲'];
	}
	return [
		'title' => ucfirst(str_replace(['-', '_'], ' ', $slug)),
		'url' => '/' . $slug,
		'icon' => '🎮'
	];
}

/**
 * Format a chat message for JSON transport
 */
function format_chat_message($row) {
	global $auth;
	$meta = resolve_game_meta($row->game, $row->game_title);
	$is_author = ($auth->ok && $auth->id == $row->user_id);
	$can_delete = ($auth->ok && (im_mod() || ($is_author && $row->time > (time() - 300))));

	// User formatting
	$author_html = usercolor($row->nick, $row->level, true, $row->user_id);
	$avatar_url = get_avatar($row, 's');

	// Time formatting
	$time_str = date('H:i', $row->time);
	if (date('Y-m-d', $row->time) !== date('Y-m-d')) {
		$time_str = date('d.m H:i', $row->time);
	}

	// Game badge HTML
	$badge_html = '<a href="' . htmlspecialchars($meta['url']) . '" class="chat-game-badge" title="Spēlē ' . htmlspecialchars($meta['title']) . '"><span class="badge-icon">' . $meta['icon'] . '</span> ' . htmlspecialchars($meta['title']) . '</a>';

	// Safe text with smilies & clickable links
	$safe_text = htmlspecialchars($row->message, ENT_QUOTES, 'UTF-8');
	$linked_text = make_clickable($safe_text);
	$formatted_text = add_smile(nl2br($linked_text));

	return [
		'id' => (int)$row->id,
		'user_id' => (int)$row->user_id,
		'nick' => $row->nick,
		'author_html' => $author_html,
		'avatar' => $avatar_url,
		'game_slug' => $row->game,
		'game_title' => $meta['title'],
		'game_url' => $meta['url'],
		'game_badge' => $badge_html,
		'time' => (int)$row->time,
		'time_str' => $time_str,
		'text' => $formatted_text,
		'can_delete' => $can_delete,
		'is_me' => $is_author
	];
}

/**
 * Auto-link URLs safely in plain text
 */
function make_clickable($text) {
	$pattern = '~(https?://[^\s<]+)~i';
	return preg_replace($pattern, '<a href="$1" target="_blank" rel="nofollow noopener noreferrer">$1</a>', $text);
}

// ==========================================
// 1. ACTION: FETCH (Polling & Initial Load)
// ==========================================
if ($action === 'fetch') {
	$last_id = isset($_GET['last_id']) ? intval($_GET['last_id']) : 0;
	$current_game = isset($_GET['game']) ? sanitize(trim($_GET['game'])) : '';
	$current_game_title = isset($_GET['game_title']) ? sanitize(trim($_GET['game_title'])) : '';

	// Update presence if user is logged in
	if ($auth->ok && !empty($current_game)) {
		$meta = resolve_game_meta($current_game, $current_game_title);
		$title_esc = sanitize($meta['title']);
		$now = time();
		$db->query("
			INSERT INTO `game_chat_online` (`user_id`, `game`, `game_title`, `last_seen`) 
			VALUES ('$auth->id', '$current_game', '$title_esc', '$now')
			ON DUPLICATE KEY UPDATE `game` = VALUES(`game`), `game_title` = VALUES(`game_title`), `last_seen` = VALUES(`last_seen`)
		");
	}

	// Purge stale online presence older than 5 minutes
	$stale_threshold = time() - 300;
	$db->query("DELETE FROM `game_chat_online` WHERE `last_seen` < '$stale_threshold'");

	// Fetch active players seen in last 70 seconds
	$active_threshold = time() - 70;
	$raw_online = $db->get_results("
		SELECT gco.user_id, gco.game, gco.game_title, gco.last_seen, u.nick, u.level, u.avatar
		FROM `game_chat_online` gco
		JOIN `users` u ON u.id = gco.user_id
		WHERE gco.last_seen >= '$active_threshold'
		ORDER BY gco.last_seen DESC
		LIMIT 50
	");

	$online_players = [];
	if (!empty($raw_online)) {
		foreach ($raw_online as $p) {
			$meta = resolve_game_meta($p->game, $p->game_title);
			$online_players[] = [
				'user_id' => (int)$p->user_id,
				'nick' => $p->nick,
				'author_html' => usercolor($p->nick, $p->level, true, $p->user_id),
				'avatar' => get_avatar($p, 's'),
				'game_slug' => $p->game,
				'game_title' => $meta['title'],
				'game_url' => $meta['url'],
				'game_icon' => $meta['icon'],
				'is_me' => ($auth->ok && $auth->id == $p->user_id)
			];
		}
	}

	// Fetch messages
	if ($last_id > 0) {
		$raw_msgs = $db->get_results("
			SELECT gc.*, u.nick, u.level, u.avatar
			FROM `game_chat` gc
			JOIN `users` u ON u.id = gc.user_id
			WHERE gc.id > '$last_id' AND gc.removed = 0
			ORDER BY gc.id ASC
			LIMIT 60
		");
	} else {
		// Initial fetch: latest 40 messages ordered ASC
		$raw_msgs = $db->get_results("
			SELECT * FROM (
				SELECT gc.*, u.nick, u.level, u.avatar
				FROM `game_chat` gc
				JOIN `users` u ON u.id = gc.user_id
				WHERE gc.removed = 0
				ORDER BY gc.id DESC
				LIMIT 40
			) sub
			ORDER BY sub.id ASC
		");
	}

	$messages = [];
	$max_id = $last_id;
	if (!empty($raw_msgs)) {
		foreach ($raw_msgs as $m_row) {
			$messages[] = format_chat_message($m_row);
			if ($m_row->id > $max_id) {
				$max_id = (int)$m_row->id;
			}
		}
	}

	echo json_encode([
		'success' => true,
		'messages' => $messages,
		'online_players' => $online_players,
		'online_count' => count($online_players),
		'last_id' => $max_id,
		'current_user_id' => $auth->ok ? (int)$auth->id : 0,
		'is_logged_in' => (bool)$auth->ok
	]);
	exit;
}

// ==========================================
// 2. ACTION: SEND (Post new message)
// ==========================================
if ($action === 'send') {
	if (!$auth->ok) {
		echo json_encode(['success' => false, 'error' => 'Tikai reģistrēti lietotāji var rakstīt spēļu čatā! Lūdzu, ienāc savā profilā.']);
		exit;
	}

	$msg_text = isset($_POST['message']) ? trim($_POST['message']) : '';
	$game_slug = isset($_POST['game']) ? sanitize(trim($_POST['game'])) : 'speles';
	$game_title = isset($_POST['game_title']) ? sanitize(trim($_POST['game_title'])) : '';

	if (empty($msg_text)) {
		echo json_encode(['success' => false, 'error' => 'Ziņa nevar būt tukša!']);
		exit;
	}

	if (mb_strlen($msg_text) > 400) {
		echo json_encode(['success' => false, 'error' => 'Ziņa ir pārāk gara (maksimāli 400 zīmes)!']);
		exit;
	}

	// Flood control: minimum 1.5 seconds between posts
	$last_post_time = (int)$db->get_var("SELECT `time` FROM `game_chat` WHERE `user_id` = '$auth->id' ORDER BY `id` DESC LIMIT 1");
	if ($last_post_time && (time() - $last_post_time) < 2) {
		echo json_encode(['success' => false, 'error' => 'Pārāk ātri! Lūdzu uzgaidi 2 sekundes pirms nākamās ziņas.']);
		exit;
	}

	// Prevent sending identical duplicate message within 60 seconds
	$last_post_msg = $db->get_var("SELECT `message` FROM `game_chat` WHERE `user_id` = '$auth->id' ORDER BY `id` DESC LIMIT 1");
	if ($last_post_msg === $msg_text && (time() - $last_post_time) < 60) {
		echo json_encode(['success' => false, 'error' => 'Identiska ziņa jau tika nosūtīta tikko!']);
		exit;
	}

	$meta = resolve_game_meta($game_slug, $game_title);
	$game_slug_clean = sanitize($game_slug);
	$game_title_clean = sanitize($meta['title']);
	$msg_clean = sanitize($msg_text);
	$now = time();

	$inserted = $db->query("
		INSERT INTO `game_chat` (`user_id`, `game`, `game_title`, `message`, `time`, `removed`)
		VALUES ('$auth->id', '$game_slug_clean', '$game_title_clean', '$msg_clean', '$now', 0)
	");

	if (!$inserted) {
		echo json_encode(['success' => false, 'error' => 'Neizdevās saglabāt ziņu datubāzē.']);
		exit;
	}

	$insert_id = (int)$db->insert_id;

	// Update presence in game_chat_online
	$db->query("
		INSERT INTO `game_chat_online` (`user_id`, `game`, `game_title`, `last_seen`) 
		VALUES ('$auth->id', '$game_slug_clean', '$game_title_clean', '$now')
		ON DUPLICATE KEY UPDATE `game` = VALUES(`game`), `game_title` = VALUES(`game_title`), `last_seen` = VALUES(`last_seen`)
	");

	// Fetch newly created message row for response
	$new_row = $db->get_row("
		SELECT gc.*, u.nick, u.level, u.avatar
		FROM `game_chat` gc
		JOIN `users` u ON u.id = gc.user_id
		WHERE gc.id = '$insert_id' LIMIT 1
	");

	$formatted = $new_row ? format_chat_message($new_row) : null;

	echo json_encode([
		'success' => true,
		'message' => $formatted
	]);
	exit;
}

// ==========================================
// 3. ACTION: DELETE (Moderation)
// ==========================================
if ($action === 'delete') {
	if (!$auth->ok) {
		echo json_encode(['success' => false, 'error' => 'Nav tiesību dzēst ziņu.']);
		exit;
	}

	$msg_id = isset($_POST['id']) ? intval($_POST['id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);
	if ($msg_id <= 0) {
		echo json_encode(['success' => false, 'error' => 'Nederīgs ziņas ID.']);
		exit;
	}

	$msg = $db->get_row("SELECT * FROM `game_chat` WHERE `id` = '$msg_id' LIMIT 1");
	if (!$msg) {
		echo json_encode(['success' => false, 'error' => 'Ziņa nav atrasta.']);
		exit;
	}

	$is_author = ($auth->id == $msg->user_id);
	$can_delete = im_mod() || ($is_author && $msg->time > (time() - 300));

	if (!$can_delete) {
		echo json_encode(['success' => false, 'error' => 'Tev nav tiesību dzēst šo ziņu.']);
		exit;
	}

	$db->query("UPDATE `game_chat` SET `removed` = 1 WHERE `id` = '$msg_id'");

	echo json_encode(['success' => true, 'id' => $msg_id]);
	exit;
}

// Fallback
echo json_encode(['success' => false, 'error' => 'Nezināma darbība']);
exit;
