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

$lang = 1;

//mysql konekcija
$db = new mdb($username, $password, $database, $hostname);

function reverse_htmlentities($mixed) {
	$htmltable = get_html_translation_table(HTML_ENTITIES);
	foreach ($htmltable as $key => $value) {
		$mixed = ereg_replace(addslashes($value), $key, $mixed);
	}
	return $mixed;
}

//memcached konekcija
$m = new Memcache;
$m->connect($mc_host, $mc_port);

$source = (int)$argv[1];
$destionation = (int)$argv[2];

$db->query("DELETE FROM `cat` WHERE `id` = '$source' LIMIT 1");
$db->query("UPDATE `pages` SET `category` = '$destionation' WHERE `category` = '$source'");
update_stats($destionation);

die('ok');

