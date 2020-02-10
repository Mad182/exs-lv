<?php

/**
 * Lietotāja dzēšana no komandrindas
 */
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

$user = (int) $argv[1];

if ($user) {

	//mysql konekcija
	$db = new mdb($username, $password, $database, $hostname);

	//memcached konekcija
	$m = new Memcached;
	$m->addServer($mc_host, $mc_port);

	$db->query("UPDATE users SET 
			`nick` = 'Dzēsts #" . $user . "',
			`pwd` = '',
			`password` = '',
			`mail` = '',
			`mail_confirmed` = null,
			`lastseen` = `date`,
			`avatar` = '',
			`av_alt` = '0',
			`level` = 0,
			`signature` = '',
			`max_in_row` = 0,
			`skype` = '',
			`web` = '',
			`about` = '',
			`custom_title` = '',
			`yt_name` = '',
			`yt_updated` = 0,
			`twitter` = '',
			`last_action` = '',
			`draugiem_id` = 0,
			`posts` = 0,
			`karma` = 0,
			`rating` = 0,
			`facebook_id` = '0',
			`twitter_id` = null,
			`steam_id` = null,
			`email_token` = null,
			`email_new` = null,
			`vote_total` = 0,
			`vote_others` = 0,
			`persona` = '',
			`token` = '',
			`reset_token` = '',
			`reset_time` = null,
			`lastfm_username` = null,
			`lastfm_sessionkey` = null,
			`lastfm_subscriber` = null,
			`lastfm_token` = null,
			`lastfm_updated` = null,
			`auth_secret` = null,
			`connected_profiles` = '',
			`user_agent` = '',
			`deleted` = 1
			WHERE id = '$user'");

	$db->query("DELETE FROM `clans_members` WHERE `user` = '$user'");
	$db->query("DELETE FROM `notify` WHERE `user_id` = '$user'");
	$db->query("DELETE FROM `notes` WHERE `user_id` = '$user'");
	$db->query("DELETE FROM `cat_moderators` WHERE `user_id` = '$user'");
	$db->query("DELETE FROM `viewprofile` WHERE `profile` = '$user'");
	$db->query("DELETE FROM `viewprofile` WHERE `viewer` = '$user'");
	$db->query("DELETE FROM `friends` WHERE `friend1` = '$user'");
	$db->query("DELETE FROM `friends` WHERE `friend2` = '$user'");
	$db->query("DELETE FROM `bookmarks` WHERE `userid` = '$user'");
	$db->query("DELETE FROM `autoawards` WHERE `user_id` = '$user'");
	$db->query("DELETE FROM `userlogs` WHERE `user` = '$user'");
	$db->query("DELETE FROM `images` WHERE `uid` = '$user'");
	$db->query("UPDATE `pages` SET `private` = 1 WHERE `author` = '$user'");

	get_user($user, true);

	echo 'user #' . $user . " deleted\n\n";
}

