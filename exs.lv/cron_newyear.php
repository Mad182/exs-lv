<?php

if (PHP_SAPI !== 'cli') {
	echo 'CLI only!';
	exit;
}

echo 'cron_newyear.php started' . "\n";

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

$db->query("UPDATE users SET year_first = 1 WHERE id IN(SELECT DISTINCT(author) FROM `miniblog` WHERE `date` LIKE '%-01-01 00:00:%')");
$db->query("UPDATE users SET year_first = 1 WHERE id IN(SELECT DISTINCT(author) FROM `comments` WHERE `date` LIKE '%-01-01 00:00:%')");
$db->query("UPDATE users SET year_first = 1 WHERE id IN(SELECT DISTINCT(author) FROM `galcom` WHERE `date` LIKE '%-01-01 00:00:%')");

