<?php

//usage:  php mergegroup.php SOURCEID DESTINATIONID

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

$source = (int)$argv[1];
$destionation = (int)$argv[2];

$db->query("DELETE FROM `clans` WHERE `id` = '$source' LIMIT 1");
echo "deleted group...\n";
$db->query("UPDATE `miniblog` SET `groupid` = '$destionation' WHERE `groupid` = '$source'");
echo "changed groupid for " . $db->affected_rows . " posts...\n";

$db->query("DELETE FROM `clans_tabs` WHERE `clan_id` = '$source'");

$members = $db->get_results("SELECT * FROM `clans_members` WHERE `clan` = '$source' AND `approve` = 1");

foreach($members as $member) {
	
	$check = $db->get_var("SELECT count(*) FROM  `clans_members` WHERE `clan` = '$destionation' AND `user` = '$member->user'");	
	if(empty($check)) {
		$db->query("UPDATE `clans_members` SET `clan` = '$destionation', `moderator` = 0 WHERE `id` = '$member->id' LIMIT 1");
	}
	
}

$db->query("DELETE FROM `clans_members` WHERE `clan` = '$source'");

die("finished!\n\n");

