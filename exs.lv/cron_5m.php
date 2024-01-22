<?php

/**
 * cron_5m.php
 * Izpildās ar 5 minūšu intervālu
 * \/5 * * * * exs php /home/www/exs.lv/cron_5m.php
 * miniblogu twitter un rss ieraksti, blogu piešķiršana sasniedzot karmu
 */
if (PHP_SAPI !== 'cli') {
	echo 'CLI only!';
	exit;
}

echo 'cron_5m.php started' . "\n";

chdir(__DIR__);
ini_set('memory_limit', '256M');
error_reporting(0);
ini_set('display_errors', 'Off');

require('configdb.php');
require(CORE_PATH . '/includes/class.mdb.php');
require(CORE_PATH . '/includes/functions.core.php');

//mysql konekcija
$db = new mdb($username, $password, $database, $hostname);
unset($password);

//memcached konekcija
$m = new Memcached;
$m->addServer($mc_host, $mc_port);

$cats = $db->get_results("SELECT id FROM cat");
foreach ($cats as $cat) {
	update_stats($cat->id);
}

//remove bans
$db->query("UPDATE `banned` SET `active` = 0 WHERE `time`+`length` < '" . time() . "'");

