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
$users = $db->get_results("SELECT `id` FROM `users` ORDER BY `lastseen` DESC LIMIT 10000");
$i = 0;
foreach ($users as $user) {

	$langs = array(1,3,5,7,9);

	foreach($langs as $clean) {
		$db->query("DELETE FROM `userlogs` WHERE user='$user->id' AND `lang` = '$clean' AND id NOT IN (SELECT * FROM (SELECT id FROM userlogs WHERE user='$user->id' AND `lang` = '$clean' ORDER BY id DESC LIMIT 200) AS TAB)");
	}

	$db->query("DELETE FROM `viewprofile` WHERE profile='$user->id' AND id NOT IN (SELECT * FROM (SELECT id FROM viewprofile WHERE profile='$user->id' ORDER BY `time` DESC LIMIT 100) AS TAB)");

	update_karma($user->id, true);

	$i++;
}

echo 'cleanup un karma update... ' . $i . '... ok' . "\n";

//sludinājumu dzēšana
$pages = $db->get_results("SELECT id,title FROM pages WHERE category IN(868,869,870) AND `date` < '".date('Y-m-d H:i', strtotime('-3 months'))."'");
foreach($pages as $page) {
	$db->query("DELETE FROM `comments` WHERE `pid` = '$page->id'");
	$db->query("DELETE FROM `pages` WHERE `id` = '$page->id' LIMIT 1");
	$db->query("DELETE FROM `taged` WHERE `page_id` = '$page->id' AND `type` = 0");
	$db->query("DELETE FROM `bookmarks` WHERE `pageid` = '$page->id' AND `foreign_table` = 'pages'");
}

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


//add miniblog column
//$db->query("ALTER TABLE  `miniblog` ADD  `device` SMALLINT NOT NULL DEFAULT  '0'");
