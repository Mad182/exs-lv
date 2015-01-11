<?php

/**
 * cron_lastfm.php
 * Izpildās ar 3 minūšu intervālu
 * \/3 * * * * exs php /home/www/exs.lv/cron_lastfm.php
 */
if (PHP_SAPI !== 'cli') {
	echo 'CLI only!';
	exit;
}

echo 'cron_lastfm.php started' . "\n";

chdir(__DIR__);
ini_set('memory_limit', '128M');
error_reporting(0);
ini_set('display_errors', 'Off');

require('configdb.php');
require(CORE_PATH . '/includes/class.mdb.php');
require(CORE_PATH . '/includes/functions.core.php');
require(LIB_PATH . '/phplastfm/lastfmapi/lastfmapi.php');

//mysql konekcija
$db = new mdb($username, $password, $database, $hostname);
unset($password);

//memcached konekcija
$m = new Memcache;
$m->connect($mc_host, $mc_port);

$users = $db->get_results("SELECT `id`, `nick` FROM `users` WHERE `lastfm_username` IS NOT NULL AND `lastfm_updated` < '".(time()-120)."' ORDER BY `lastfm_updated` ASC");

foreach ($users as $user) {
	echo "\nupdating " . $user->nick . '... ';
	echo "status: " . lastfm_update_tracks($user->id);
	sleep(2);
}

echo "\n\nfinished!\n";

