<?php

//usage:  php delgroup.php ID

if (PHP_SAPI !== 'cli') {
	echo 'CLI only!';
	exit;
}

echo 'delgroup.php started' . "\n";

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

$id = isset($argv[1]) ? (int)$argv[1] : 0;

if($title = $db->get_var("SELECT `title` FROM clans WHERE id = '".$id."'")) {

	echo 'Deleting clan ' . $title . "\n\n";

	$db->query("DELETE FROM `clans` WHERE `id` = '$id' LIMIT 1");
	echo "deleted " . $db->affected_rows . " group...\n";

	$db->query("DELETE FROM `miniblog` WHERE `groupid` = '$id'");
	echo "deleted " . $db->affected_rows . " posts...\n";

	$db->query("DELETE FROM `clans_tabs` WHERE `clan_id` = '$id'");
	echo "deleted " . $db->affected_rows . " tabs...\n";

	$db->query("DELETE FROM `clans_members` WHERE `clan` = '$id'");
	echo "deleted " . $db->affected_rows . " members...\n";

}

die("\nfinished!\n\n");

