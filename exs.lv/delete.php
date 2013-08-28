<?php

if (PHP_SAPI !== 'cli') {
	echo 'CLI only!';
	exit;
}

echo 'delete.php started' . "\n";

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

$user = (int) $argv[1];

if($user) {
	$db->query("UPDATE users SET 
			`nick` = 'Dzēsts #".$user."',
			`pwd` = '',
			`mail` = '',
			`lastseen` = `date`,
			`avatar` = '',
			`av_alt` = '0',
			`level` = 0,
			`signature` = '',
			`skype` = '',
			`web` = '',
			`about` = '',
			`custom_title` = '',
			`yt_name` = '',
			`twitter` = '',
			`last_action` = '',
			`draugiem_id` = 0,
			`facebook_id` = '0',
			`persona` = '' 
			WHERE id = '$user'");

	$db->query("DELETE FROM `clans_members` WHERE `user` = '$user'");
	$db->query("DELETE FROM `notify` WHERE `user_id` = '$user'");
	$db->query("DELETE FROM `notes` WHERE `user_id` = '$user'");
	$db->query("DELETE FROM `cat_moderators` WHERE `user_id` = '$user'");
	$db->query("DELETE FROM `viewprofile` WHERE `profile` = '$user'");
	$db->query("DELETE FROM `friends` WHERE `friend1` = '$user'");
	$db->query("DELETE FROM `friends` WHERE `friend2` = '$user'");
	$db->query("DELETE FROM `bookmarks` WHERE `userid` = '$user'");
	$db->query("DELETE FROM `autoawards` WHERE `user_id` = '$user'");
	
	echo 'user #' . $user . " deleted\n\n";

}

