<?php

/**
 * Syncs daily frags for registered EXS.LV users from UT99 dedicated server ngLogs
 * into the `gamescore` table (`game = 'ut99'`), and pre-renders static HTML/JSON
 * files for the right-sidebar server monitor and /ut99 leaderboards.
 *
 * Run every 60s via systemd timer (`ut99-stats.timer`).
 */

if (PHP_SAPI !== 'cli') {
	exit("CLI only\n");
}

$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_URI'] = '/cli';
chdir(__DIR__ . '/..');

define('CORE_PATH', realpath(__DIR__ . '/..'));

require_once CORE_PATH . '/configdb.php';
require_once CORE_PATH . '/includes/class.mdb.php';
require_once CORE_PATH . '/includes/functions.core.php';

$db = new mdb($username, $password, $database, $hostname);
$cache_dir = CORE_PATH . '/cache';
if (!is_dir($cache_dir)) {
	@mkdir($cache_dir, 0775, true);
}

function write_static_atomic($path, $content) {
	$tmp = $path . '.tmp.' . getmypid();
	if (file_put_contents($tmp, $content) !== false) {
		@chmod($tmp, 0644);
		@rename($tmp, $path);
	}
}

$translit = [
	'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n', 'š' => 's', 'ū' => 'u', 'ž' => 'z',
	'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'I', 'Ķ' => 'K', 'Ļ' => 'L', 'Ņ' => 'N', 'Š' => 'S', 'Ū' => 'U', 'Ž' => 'Z'
];

function normalize_ut99_nick($nick, $translit) {
	$clean = strtr((string) $nick, $translit);
	$clean = preg_replace('/[^A-Za-z0-9_\-\.\[\]]+/', '_', $clean);
	$clean = trim($clean, '_');
	return strtolower(substr($clean, 0, 20));
}

function decode_nglog($raw) {
	if (strlen($raw) >= 4 && $raw[1] === "\x00" && $raw[2] === "\x00" && $raw[3] === "\x00") {
		return mb_convert_encoding($raw, 'UTF-8', 'UTF-32LE');
	}
	if (strlen($raw) >= 2 && $raw[1] === "\x00") {
		return mb_convert_encoding($raw, 'UTF-8', 'UTF-16LE');
	}
	return $raw;
}

// =========================================================================
// 1. Sync daily frags from UT99 ngLogs into `gamescore`
// =========================================================================
$log_dir = '/opt/ut99-server/Logs';
$daily_frags = [];

if (is_dir($log_dir)) {
	$cutoff_date = date('Y-m-d', strtotime('-7 days'));
	$files = array_merge(
		glob($log_dir . '/Unreal.ngLog.*.log') ?: [],
		glob($log_dir . '/Unreal.ngLog.*.tmp') ?: []
	);

	foreach ($files as $file) {
		$base = basename($file);
		if (!preg_match('/Unreal\.ngLog\.(\d{4})\.(\d{2})\.(\d{2})\.(\d{2})\.(\d{2})\.(\d{2})\./', $base, $m)) {
			continue;
		}
		$date = "{$m[1]}-{$m[2]}-{$m[3]}";
		if ($date < $cutoff_date) {
			continue;
		}

		$raw = @file_get_contents($file);
		if ($raw === false || $raw === '') {
			continue;
		}

		$text = decode_nglog($raw);
		$lines = preg_split("/\r\n|\n|\r/", $text);
		$players = [];

		foreach ($lines as $line) {
			if ($line === '' || strpos($line, "\t") === false) {
				continue;
			}
			$cols = explode("\t", $line);
			if (count($cols) < 4) {
				continue;
			}
			$type = $cols[1];

			if ($type === 'player') {
				$sub = $cols[2];
				if (($sub === 'Connect' || $sub === 'Rename') && isset($cols[4])) {
					$pname = trim($cols[3]);
					$pid = trim($cols[4]);
					if (!isset($players[$pid])) {
						$players[$pid] = ['name' => $pname, 'is_bot' => null];
					} else {
						$players[$pid]['name'] = $pname;
					}
				} elseif ($sub === 'IsABot' && isset($cols[4])) {
					$pid = trim($cols[3]);
					$is_bot = (strcasecmp(trim($cols[4]), 'True') === 0);
					if (!isset($players[$pid])) {
						$players[$pid] = ['name' => '', 'is_bot' => $is_bot];
					} else {
						$players[$pid]['is_bot'] = $is_bot;
					}
				}
			} elseif ($type === 'kill') {
				$killer_pid = trim($cols[2]);
				if (!isset($players[$killer_pid]) || $players[$killer_pid]['is_bot'] !== false) {
					continue;
				}
				$kname = $players[$killer_pid]['name'];
				if ($kname === '' || strcasecmp($kname, 'Player') === 0) {
					continue;
				}
				$key = strtolower($kname);
				if (!isset($daily_frags[$date][$key])) {
					$daily_frags[$date][$key] = 0;
				}
				$daily_frags[$date][$key]++;
			}
		}
	}
}

$updated = 0;
if (!empty($daily_frags)) {
	$all_nicks = [];
	foreach ($daily_frags as $by_nick) {
		foreach ($by_nick as $nick => $cnt) {
			$all_nicks[$nick] = true;
		}
	}

	$nick_to_uid = [];
	foreach (array_keys($all_nicks) as $lnick) {
		$esc = addslashes($lnick);
		$uid = (int) $db->get_var("SELECT id FROM users WHERE deleted = 0 AND LOWER(nick) = '$esc' LIMIT 1");
		if ($uid > 0) {
			$nick_to_uid[$lnick] = $uid;
		}
	}

	$unresolved = array_diff(array_keys($all_nicks), array_keys($nick_to_uid));
	if (!empty($unresolved)) {
		$candidates = $db->get_results("SELECT id, nick FROM users WHERE deleted = 0 AND lastseen >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
		if (!empty($candidates)) {
			foreach ($candidates as $u) {
				$norm = normalize_ut99_nick($u->nick, $translit);
				if (isset($all_nicks[$norm]) && !isset($nick_to_uid[$norm])) {
					$nick_to_uid[$norm] = (int) $u->id;
				}
			}
		}
	}

	foreach ($daily_frags as $date => $by_nick) {
		$day_start = strtotime($date . ' 00:00:00');
		$day_end = strtotime($date . ' 23:59:59');
		$record_ts = ($date === date('Y-m-d')) ? time() : $day_end;

		foreach ($by_nick as $lnick => $frags) {
			if ($frags <= 0 || !isset($nick_to_uid[$lnick])) {
				continue;
			}
			$uid = (int) $nick_to_uid[$lnick];
			$existing = $db->get_row("SELECT id, score FROM gamescore WHERE game = 'ut99' AND user_id = '$uid' AND time >= '$day_start' AND time <= '$day_end' ORDER BY score DESC LIMIT 1");

			if ($existing) {
				if ((int) $existing->score < $frags) {
					$db->query("UPDATE gamescore SET score = '$frags', time = '$record_ts' WHERE id = '" . (int) $existing->id . "'");
					$updated++;
				}
			} else {
				$db->query("INSERT INTO gamescore (user_id, game, score, time) VALUES ('$uid', 'ut99', '$frags', '$record_ts')");
				$updated++;
			}
		}
	}
}

// =========================================================================
// 2. Render static HTML for Today's Top & All-Time Top leaderboards
// =========================================================================
$start_of_today = strtotime('today midnight');
$today_scores = $db->get_results("SELECT user_id, MAX(score) as score FROM gamescore WHERE game = 'ut99' AND time >= '$start_of_today' GROUP BY user_id ORDER BY score DESC LIMIT 10");

$today_html = '';
if (!empty($today_scores)) {
	$rank = 1;
	foreach ($today_scores as $sc) {
		$u = $db->get_row("SELECT id, nick, level FROM users WHERE id = '" . (int) $sc->user_id . "'");
		if ($u) {
			$today_html .= '<li>'
				. '<span class="top-rank">' . ($rank++) . '.</span>'
				. '<span class="top-user">' . usercolor($u->nick, $u->level) . '</span>'
				. '<strong class="top-score">' . number_format((int) $sc->score, 0, '', ' ') . ' fr.</strong>'
				. '</li>';
		}
	}
}
if ($today_html === '') {
	$today_html = '<li class="empty-msg">Šodien vēl nav fiksētu rezultātu!</li>';
}
write_static_atomic($cache_dir . '/ut99_top_today.html', $today_html);

$alltime_scores = $db->get_results("SELECT user_id, MAX(score) as score FROM gamescore WHERE game = 'ut99' GROUP BY user_id ORDER BY score DESC LIMIT 10");
$alltime_html = '';
if (!empty($alltime_scores)) {
	$rank = 1;
	foreach ($alltime_scores as $sc) {
		$u = $db->get_row("SELECT id, nick, level FROM users WHERE id = '" . (int) $sc->user_id . "'");
		if ($u) {
			$alltime_html .= '<li>'
				. '<span class="top-rank">' . ($rank++) . '.</span>'
				. '<span class="top-user">' . usercolor($u->nick, $u->level) . '</span>'
				. '<strong class="top-score">' . number_format((int) $sc->score, 0, '', ' ') . ' fr.</strong>'
				. '</li>';
		}
	}
}
if ($alltime_html === '') {
	$alltime_html = '<li class="empty-msg">Vēl nav uzstādītu rekordu!</li>';
}
write_static_atomic($cache_dir . '/ut99_top_alltime.html', $alltime_html);

// =========================================================================
// 3. Query UT99 server UDP 7778 and render static HTML monitor + status JSON
// =========================================================================
$ut99_mon = [
	'online' => false,
	'name' => 'EXS.LV Official UT99 Server',
	'map' => 'DM-Deck16][',
	'numplayers' => 0,
	'maxplayers' => 16,
	'players' => []
];

$fp = @fsockopen('udp://127.0.0.1', 7778, $errno, $errstr, 0.3);
if ($fp) {
	fwrite($fp, "\\status\\");
	stream_set_timeout($fp, 0, 250000);
	$raw = '';
	for ($pkt = 0; $pkt < 6; $pkt++) {
		$chunk = fread($fp, 4096);
		if ($chunk === false || $chunk === '') {
			break;
		}
		$raw .= $chunk;
		if (strpos($raw, '\\final\\') !== false) {
			break;
		}
	}
	fclose($fp);

	if (!empty($raw)) {
		$ut99_mon['online'] = true;
		$parts = explode('\\', ltrim($raw, '\\'));
		$kv = [];
		for ($i = 0; $i + 1 < count($parts); $i += 2) {
			$kv[strtolower($parts[$i])] = $parts[$i + 1];
		}
		if (!empty($kv['hostname'])) {
			$ut99_mon['name'] = $kv['hostname'];
		}
		if (!empty($kv['mapname'])) {
			$ut99_mon['map'] = $kv['mapname'];
		}
		if (isset($kv['numplayers'])) {
			$ut99_mon['numplayers'] = (int) $kv['numplayers'];
		}
		if (isset($kv['maxplayers'])) {
			$ut99_mon['maxplayers'] = (int) $kv['maxplayers'];
		}
		$players_map = [];
		foreach ($kv as $k => $v) {
			if (preg_match('/^(player|frags|ping)_(\d+)$/', $k, $pm)) {
				$idx = (int) $pm[2];
				$players_map[$idx][$pm[1]] = $v;
			}
		}
		$plist = [];
		foreach ($players_map as $p) {
			if (!isset($p['player']) || trim($p['player']) === '') {
				continue;
			}
			$plist[] = [
				'name' => $p['player'],
				'frags' => isset($p['frags']) ? (int) $p['frags'] : 0,
				'ping' => isset($p['ping']) ? trim($p['ping']) : '0'
			];
		}
		usort($plist, function ($a, $b) {
			return $b['frags'] <=> $a['frags'];
		});
		$ut99_mon['players'] = $plist;
	}
}

$status_json = json_encode([
	'online' => $ut99_mon['online'],
	'name' => $ut99_mon['name'],
	'map' => $ut99_mon['map'],
	'players' => $ut99_mon['numplayers'],
	'max_players' => $ut99_mon['maxplayers'],
	'ping' => 0
]);
write_static_atomic($cache_dir . '/ut99_status.json', $status_json);

$status_class = $ut99_mon['online'] ? 'online' : 'offline';
$status_text = $ut99_mon['online'] ? 'Tiešsaistē' : 'Bezsaistē';
$map_escaped = htmlspecialchars($ut99_mon['map'], ENT_QUOTES, 'UTF-8');

$map_img_html = '';
$map_file = strtolower(basename($ut99_mon['map'])) . '.jpg';
$map_path = CORE_PATH . '/bildes/ut/' . $map_file;
if (is_file($map_path)) {
	$img_src = '/bildes/ut/' . rawurlencode($map_file) . '?v=' . filemtime($map_path);
	$map_img_html = '<div class="ut99-mon-mapimg"><a href="/ut99"><img src="' . $img_src . '" alt="' . $map_escaped . '" loading="lazy" /></a></div>';
}

if (!empty($ut99_mon['players'])) {
	$rows = '';
	foreach ($ut99_mon['players'] as $p) {
		$rows .= '<tr>'
			. '<td class="ut99-mon-pname">' . htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') . '</td>'
			. '<td class="num">' . (int) $p['frags'] . '</td>'
			. '<td class="num">' . htmlspecialchars($p['ping'], ENT_QUOTES, 'UTF-8') . ' ms</td>'
			. '</tr>';
	}
	$players_html = '<table class="ut99-mon-table">'
		. '<thead><tr><th>Spēlētājs</th><th class="num">Fragi</th><th class="num">Ping</th></tr></thead>'
		. '<tbody>' . $rows . '</tbody>'
		. '</table>';
} else {
	$players_html = '<div class="ut99-mon-empty">Pašlaik serverī nav spēlētāju</div>';
}

$monitor_html = '<div class="widget widget-ut99-monitor">'
	. '<div class="title" role="heading" aria-level="2"><a href="/ut99"><span>UT99 Serveris</span></a></div>'
	. '<div class="box ut99-mon-box">'
	. '<div class="ut99-mon-status ' . $status_class . '">'
	. '<span class="ut99-mon-dot"></span>'
	. '<strong>' . $status_text . '</strong>'
	. '<span class="ut99-mon-count">' . (int) $ut99_mon['numplayers'] . '/' . (int) $ut99_mon['maxplayers'] . '</span>'
	. '</div>'
	. $map_img_html
	. '<div class="ut99-mon-meta">'
	. '<div><span>Adrese:</span> <code>exs.lv:7777</code></div>'
	. '<div><span>Karte:</span> <strong>' . $map_escaped . '</strong></div>'
	. '</div>'
	. $players_html
	. '<div class="ut99-mon-footer"><a href="/ut99">Spēlēt pārlūkā &raquo;</a></div>'
	. '</div>'
	. '</div>';

write_static_atomic($cache_dir . '/ut99_monitor.html', $monitor_html);

echo "UT99 sync complete. Updated/inserted frag rows: $updated | Server: " . ($ut99_mon['online'] ? 'online (' . $ut99_mon['map'] . ', ' . $ut99_mon['numplayers'] . '/' . $ut99_mon['maxplayers'] . ')' : 'offline') . "\n";
