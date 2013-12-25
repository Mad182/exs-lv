<?php

//usage:  php movegroup.php GROUPID DOMAINID

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

$group = (int)$argv[1];
$destionation = (int)$argv[2];

$clan = $db->get_row("SELECT * FROM `clans` WHERE `id` = $group LIMIT 1");
if (!empty($clan)) {
	$upd = $db->query("UPDATE `clans` SET `lang` = $destionation WHERE `id` = '".(int)$clan->id."' ");
	if(!empty($clan->strid)) {
		$upd = $db->query("UPDATE `cat` SET `lang` = 0 WHERE `textid` = '".sanitize($clan->strid)."' ");
	}
	$upd = $db->query("UPDATE `miniblog` SET `lang` = $destionation WHERE `groupid` = '".(int)$clan->id."' ");
	die($clan->title . " moved...\n");
} else {
	die("Error: group not found!\n");
}
