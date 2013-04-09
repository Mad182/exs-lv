<?php

/*
  cron_lol.php
  League of Legends tops
 */

if (PHP_SAPI !== 'cli') {
	echo 'CLI only!';
	exit;
}

echo 'cron_lol.php started' . "\n\n";

chdir(__DIR__);
ini_set('memory_limit', '256M');
error_reporting(0);
ini_set('display_errors', 'On');

require('configdb.php');
require(CORE_PATH . '/includes/class.mdb.php');
require(CORE_PATH . '/includes/functions.core.php');


//mysql konekcija
$db = new mdb($username, $password, $database, $hostname);
unset($password);

//memcached konekcija
$m = new Memcache;
$m->connect($mc_host, $mc_port);


function get_data($url) {

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, '6');
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}


$players = $db->get_results("SELECT `id`, `url`, `lol_nick` FROM `lol_players` WHERE `active` = 1 ORDER BY rand()");

$date = date('Y-m-d');

foreach($players as $player) {

	echo $player->lol_nick . "... ";
	
	$source = get_data($player->url);
	$needle = '<div style="display: inline-block; margin-left: -8px; vertical-align: middle; font: bold 24px/32px &quot;Trebuchet MS&quot;;">';
	$sakums = strpos($source, $needle);
	$sakums = $sakums +126;
	$strikis = substr($source, $sakums, 4);
	$rez = strpos($source, $needle, $sakums + strlen($needle));
	$rez = $rez + 126;
	$strikis2 = substr($source, $rez, 4);
	$lks = (int)preg_replace('/</', '', $strikis2);
	
	if($lks > 0) {
		if($db->get_var("SELECT count(*) FROM `lol_tracking` WHERE `player_id` = '$player->id' AND `date` = '$date'")) {
			$db->query("UPDATE `lol_tracking` SET `lks` = '$lks' WHERE `player_id` = '$player->id' AND `date` = '$date'");
			echo 'record updated';
		} else {
			$db->query("INSERT INTO `lol_tracking` (`player_id`, `date`, `lks`) VALUES ('$player->id', '$date', '$lks')");
			echo 'record added';
		}
		$db->query("UPDATE `lol_players` SET `updated` = NOW() WHERE `id` = '$player->id'");
	} else {
		echo '(!) error (!)';	
	}
	echo "\n";
	
	sleep(rand(2,5));
	
}

echo "\nDone!\n";
