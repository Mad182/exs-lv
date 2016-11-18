<?php

//usage:  php delgroup.php ID

if (PHP_SAPI !== 'cli') {
	echo 'CLI only!';
	exit;
}

echo 'delpage.php started' . "\n";

chdir(__DIR__);
ini_set('memory_limit', '256M');
error_reporting(0);
ini_set('display_errors', 'Off');
require('configdb.php');
require(CORE_PATH . '/includes/class.mdb.php');
require(CORE_PATH . '/includes/functions.core.php');

//mysql konekcija
$db = new mdb($username, $password, $database, $hostname);

$pages = $db->get_results("SELECT * FROM `pages` WHERE `strid` = 'cruel-talker'");

foreach($pages as $page) {
	echo 'Deleting page ' . $page->title . "\n\n";

	$db->query("DELETE FROM `pages` WHERE `id` = '$page->id' LIMIT 1");

	$db->query("DELETE FROM `comments` WHERE `pid` = '$page->id'");
	echo "deleted " . $db->affected_rows . " comments...\n";

	$db->query("DELETE FROM `bookmarks` WHERE `pageid` = '$page->id' AND `foreign_table` = 'pages'");
	echo "deleted " . $db->affected_rows . " bookmarks...\n";

	echo "\n\n";

}

die("\nfinished!\n\n");

