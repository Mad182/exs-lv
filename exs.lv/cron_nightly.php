<?php

/*
  cron_nightly.php
  Izpildās katru dienu naktī, kad ir maz apmeklētāju un maza slodze.
  30 3    * * *   exs php /home/www/exs.lv/cron_nightly.php
  iztīra vecos lietotāju logus un profila skatījumus, optimizē tabulas un citi "smagie" cleanup darbi
 */

if (PHP_SAPI !== 'cli') {
	echo 'CLI only!';
	exit;
}

echo 'cron_nightly.php started' . "\n";

chdir(__DIR__);
ini_set('memory_limit', '256M');
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require('configdb.php');
require(CORE_PATH . '/includes/class.mdb.php');
require(CORE_PATH . '/includes/functions.core.php');

//mysql konekcija
$db = new mdb($username, $password, $database, $hostname);
unset($password);

//memcached konekcija
$m = new Memcache;
$m->connect($mc_host, $mc_port);

####################### PROFILA SKATIJUMU UN LOGU TIRISANA
$users = $db->get_results("SELECT id FROM users");
$i = 0;
foreach ($users as $user) {
	$db->query("DELETE FROM `userlogs` WHERE user='$user->id' AND `lang` = '1' AND id NOT IN (SELECT * FROM (SELECT id FROM userlogs WHERE user='$user->id' AND `lang` = '1' ORDER BY id DESC LIMIT 120) AS TAB)");

	$db->query("DELETE FROM `userlogs` WHERE user='$user->id' AND `lang` = '3' AND id NOT IN (SELECT * FROM (SELECT id FROM userlogs WHERE user='$user->id' AND `lang` = '3' ORDER BY id DESC LIMIT 120) AS TAB)");

	$db->query("DELETE FROM `userlogs` WHERE user='$user->id' AND `lang` = '5' AND id NOT IN (SELECT * FROM (SELECT id FROM userlogs WHERE user='$user->id' AND `lang` = '5' ORDER BY id DESC LIMIT 120) AS TAB)");


	$db->query("DELETE FROM `viewprofile` WHERE profile='$user->id' AND id NOT IN (SELECT * FROM (SELECT id FROM viewprofile WHERE profile='$user->id' ORDER BY `time` DESC LIMIT 100) AS TAB)");

	$i++;
}

echo 'cleanup... ' . $i . '... ok' . "\n";

$cats = $db->get_results("SELECT id FROM cat");
foreach ($cats as $cat) {
	update_stats($cat->id);
}

echo "update cat stats... ok\n";

$db->query("DELETE FROM `serverlist_log` WHERE `when` < '" . strtotime('-1 week') . "'");
echo "serverlist clean... ok\n";


$db->query("DELETE FROM `taged` WHERE `tag_id` IN(SELECT id FROM `tags` WHERE `name` LIKE '%;%')");
$db->query("DELETE FROM `tags` WHERE `name` LIKE '%;%'");
echo "remve ugly tags... ok\n";


$db->query("OPTIMIZE TABLE `ajax_comments`, `animations`, `approve`, `autoawards`, `awards`, `banned`, `bookmarks`, `cat`, `city`, `clans`, `clans_categories`, `clans_members`, `clans_paid`, `clans_tabs`, `comments`, `counter`, `counter_ip`, `desas`, `desas_moves`, `downloads`, `drafts`, `emails`, `facts`, `flash_games`, `friends`, `galcom`, `gamescore`, `ig_games`, `ig_items`, `ig_results`, `images`, `imgupload`, `items_db`, `items_db_cats`, `items_db_queue`, `items_db_three`, `items_db_whitelist`, `lolimages`, `lostmaps`, `miniblog`, `nhl`, `nick_history`, `notes`, `notify`, `pages`, `pages_ver`, `pm`, `poll`, `portfolio`, `qgame_answers`, `qgame_questions`, `questions`, `responses`, `rpg_users`, `rs_help`, `serverlist`, `serverlist_log`, `sidelinks`, `sms`, `taged`, `tags`, `userlogs`, `users`, `users_tmp`, `viewprofile`, `vouches`, `wallpapers`, `warns`, `wg_games`, `wg_results`, `wg_words`, `ytlocal`, `ytrss`, `zgame`, `zvera_pics`");

$users = $db->get_results("SELECT `id` FROM `users` ORDER BY `lastseen` DESC LIMIT 10000");

$i = 0;
foreach ($users as $val) {
	update_karma($val->id, true);
	$i++;
	if ($i > 99) {
		$i = 0;
		sleep(1);
	}
}
