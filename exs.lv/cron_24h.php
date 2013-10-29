<?php

/*
  cron_24.php
  Izpildās katru dienu 24:00
  0 0 * * * exs php /home/www/exs.lv/cron_24h.php
  reseto dienas statistikas, piešķir dienas apbalvojumus, noņem vecos banus, importē flash spēles
 */

if (PHP_SAPI !== 'cli') {
	echo 'CLI only!';
	exit;
}

echo 'cron_24h.php started' . "\n";

chdir(__DIR__);
ini_set('memory_limit', '256M');
error_reporting(0);
ini_set('display_errors', 'On');

require('configdb.php');
require(CORE_PATH . '/includes/class.mdb.php');
require('includes/functions.core.php');

//mysql konekcija
$db = new mdb($username, $password, $database, $hostname);
unset($password);

//memcached konekcija
$m = new Memcache;
$m->connect($mc_host, $mc_port);

########################## TOPA TĪRĪŠANA

$daily_first = $db->get_var("SELECT id FROM users ORDER BY today DESC LIMIT 1");
$db->query("UPDATE `users` SET `daily_first` = `daily_first`+1, `credit` = `credit`+1 WHERE `id` = '$daily_first'");

update_karma($daily_first, true);

$db->query("UPDATE `users` SET `days_in_row` = `days_in_row`+1 WHERE `seen_today` = 1");
$db->query("UPDATE `users` SET `max_in_row` = `days_in_row` WHERE `days_in_row` > `max_in_row`");
$db->query("UPDATE `users` SET `days_in_row` = 0 WHERE `seen_today` = 0");
$db->query("UPDATE `users` SET `seen_today` = 0, `vote_today` = 0, `today` = 0");

######################### Vecie warni automātiki noņemas pēc 2 mēnešiem

$warns = $db->get_results("SELECT * FROM warns WHERE active = 1 AND created < '" . date('Y-m-d H:i:s', strtotime('-2 months')) . "'");

foreach ($warns as $warn) {
	$db->query("UPDATE `warns` SET
  `removed_by` = `created_by`,
  `removed` = NOW(),
  `modified` = NOW(),
  `remove_reason` = '" . sanitize('Pagājuši 2 mēneši (auto noņemšana)') . "',
   `active` = '0'
   WHERE id = '$warn->id'
  LIMIT 1");
	notify($warn->user_id, 11);
	$db->query("UPDATE `users` SET `warn_count` = warn_count-1 WHERE `id` = '$warn->user_id' LIMIT 1");
	get_user($warn->user_id, true);
}


########################## Noņem IP banu veciem baniem
$db->query("UPDATE `banned` SET `ip` = '--' WHERE `time` < '" . strtotime('-6 months') . "' AND `ip` != '--'");
$db->query("DELETE FROM `banned` WHERE `ip` = '--' AND `user_id` = '0'");


######################## HANGMAN

$user = $db->get_row("SELECT * FROM wg_results WHERE date = '" . date('Y-m-d', time() - 600) . "' AND user_id != '0' ORDER BY points DESC, games ASC LIMIT 1");
if ($user) {
	$db->query("UPDATE users SET daily_hangman = daily_hangman+1 WHERE id = '$user->user_id'");
	update_karma($user->user_id, true);
}

######################### wos
$db->query("TRUNCATE TABLE `async_ip`");



############################ draugiem.lv sekotāji
$str = file_get_contents('http://www.draugiem.lv/exs.lv/js/fans/?count=1000');
$str = explode('[',$str);
$str = explode(']',$str[1]);
$str = json_decode('['.$str[0].']');

foreach ($str as $usr) {
	if (stristr($usr->url, '/user/')) {
		$id = str_replace(array('/user/', '/'), '', $usr->url);
	} else {
		$id = get_between($usr->image, '/i_', '.jpg');
	}
	if ($id > 1000) {
		if (!$db->get_var("SELECT count(*) FROM `draugiem_followers` WHERE id = '$id'")) {
			$db->query("INSERT INTO `draugiem_followers` (id) VALUES ('$id')");
			echo $id . "\n";
		}
	}
}

