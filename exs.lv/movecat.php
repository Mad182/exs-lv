<?php

//usage:  php movecat.php SOURCEID DESTINATIONID

if (PHP_SAPI !== 'cli') {
	echo 'CLI only!';
	exit;
}

echo 'movecat.php started' . "\n";

chdir(__DIR__);
ini_set('memory_limit', '256M');
error_reporting(0);
ini_set('display_errors', 'Off');
require('configdb.php');
require(CORE_PATH . '/includes/class.mdb.php');
require(CORE_PATH . '/includes/functions.core.php');

$debug = true;

//mysql konekcija
$db = new mdb($username, $password, $database, $hostname);

//memcached konekcija
$m = new Memcached;
$m->addServer($mc_host, $mc_port);

$source = (int)$argv[1];
$destionation = (int)$argv[2];

$db->query("DELETE FROM `cat` WHERE `id` = '$source' LIMIT 1");
echo "deleted category...\n";
$db->query("UPDATE `cat` SET `parent` = '$destionation' WHERE `parent` = '$source'");
echo "changed parent for " . $db->affected_rows . " categories...\n";
$db->query("UPDATE `pages` SET `category` = '$destionation' WHERE `category` = '$source'");
echo "changed category for " . $db->affected_rows . " pages...\n";
update_stats($destionation);
echo "stats updated...\n";

die("finished!\n\n");

