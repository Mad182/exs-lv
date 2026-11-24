<?php

/**
 * Syncs daily frags for registered EXS.LV users from UT99 dedicated server ngLogs
 * into the `gamescore` table (`game = 'ut99'`).
 *
 * Designed to run periodically (e.g., every 60s via systemd timer or cron).
 */

if (PHP_SAPI !== 'cli') {
	exit("CLI only\n");
}

$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
chdir(__DIR__ . '/..');

require_once __DIR__ . '/../configdb.php';
require_once __DIR__ . '/../includes/class.mdb.php';

$db = new mdb($username, $password, $database, $hostname);

$log_dir = '/opt/ut99-server/Logs';
if (!is_dir($log_dir)) {
	exit("Log directory not found: $log_dir\n");
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

// Scan ngLog files from the last 7 days
$cutoff_date = date('Y-m-d', strtotime('-7 days'));
$files = array_merge(
	glob($log_dir . '/Unreal.ngLog.*.log') ?: [],
	glob($log_dir . '/Unreal.ngLog.*.tmp') ?: []
);

// daily_frags[YYYY-MM-DD][lowercase_nick] = total_frags
$daily_frags = [];

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

	$players = []; // pid => ['name' => string, 'is_bot' => ?bool]

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
			if (!isset($players[$killer_pid])) {
				continue;
			}
			// Only count confirmed human players (IsABot === false)
			if ($players[$killer_pid]['is_bot'] !== false) {
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

if (empty($daily_frags)) {
	echo "No human frags found in recent ngLogs.\n";
	exit(0);
}

// Resolve player names to registered EXS.LV users
$all_nicks = [];
foreach ($daily_frags as $date => $by_nick) {
	foreach ($by_nick as $nick => $cnt) {
		$all_nicks[$nick] = true;
	}
}

$nick_to_uid = [];
foreach (array_keys($all_nicks) as $lnick) {
	$esc = $db->escape($lnick);
	$uid = (int) $db->get_var("SELECT id FROM users WHERE deleted = 0 AND LOWER(nick) = '$esc' LIMIT 1");
	if ($uid > 0) {
		$nick_to_uid[$lnick] = $uid;
	}
}

// Fallback: match transliterated Latvian nicks for any unresolved names
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

$updated = 0;
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

echo "UT99 frag sync complete. Updated/inserted rows: $updated\n";
