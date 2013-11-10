<?php

/*
  cron_5m.php
  Izpildās ar 5 minūšu intervālu
 * \/5 * * * * exs php /home/www/exs.lv/cron_5m.php
  miniblogu twitter un rss ieraksti, blogu piešķiršana sasniedzot karmu
 */

if (PHP_SAPI !== 'cli') {
	echo 'CLI only!';
	exit;
}

echo 'cron_5m.php started' . "\n";

chdir(__DIR__);
ini_set('memory_limit', '256M');
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require('configdb.php');
require(CORE_PATH . '/includes/class.mdb.php');
require(CORE_PATH . '/includes/functions.core.php');
require(CORE_PATH . '/includes/class.getimages.php');

$get_img = new getImages();

//mysql konekcija
$db = new mdb($username, $password, $database, $hostname);
unset($password);

//memcached konekcija
$m = new Memcache;
$m->connect($mc_host, $mc_port);

function get_rss_youtube($url, $exs_userid = 17077, $exs_groupid = 0) {
	global $db;

	$xml = simplexml_load_file($url);
	if ($xml) {
		$newtweets = array();
		foreach ($xml->entry as $item) {

			$link = str_replace('&feature=youtube_gdata', '', $item->link['href']);
			if (!$db->get_var("SELECT count(*) FROM miniblog WHERE twitterid = '" . md5($link) . "'")) {

				$newtweets[] = array(
					sanitize('<p><strong>' . stripslashes($item->title) . '</strong><br /><a href="' . $link . '">' . $link . '</a><br />' . stripslashes($item->content) . '</p>'),
					date('Y-m-d H:i:s', strtotime($item->published)),
					md5($link),
					strtotime($item->published)
				);
			}
		}

		if ($newtweets) {
			$newtweets = array_reverse($newtweets);
			foreach ($newtweets as $tw) {

				$exists = $db->get_var("SELECT id FROM miniblog WHERE groupid = '$exs_groupid' AND twitteruser = 'rssbot' AND `date` LIKE '" . date('Y-m-d') . "%' AND parent = '0' ORDER BY id DESC LIMIT 1");
				if (!$exists) {
					$db->query("INSERT INTO miniblog (author,groupid,date,text,ip,bump,twitterid,twitteruser) VALUES ('$exs_userid','$exs_groupid',NOW(),'" . $tw[0] . "','127.0.0.1','" . $tw[3] . "','" . $tw[2] . "','rssbot')");
				} else {
					$db->query("INSERT INTO miniblog (parent,author,groupid,date,text,ip,bump,twitterid,twitteruser) VALUES ('$exists','$exs_userid','$exs_groupid','" . $tw[1] . "','" . $tw[0] . "','127.0.0.1','" . $tw[3] . "','" . $tw[2] . "','rssbot')");
					$db->query("UPDATE miniblog SET bump = '" . time() . "', posts = posts+1 WHERE id = '$exists'");
				}
			}
			$db->query("UPDATE clans SET posts = '" . $db->get_var("SELECT count(*) FROM miniblog WHERE groupid = '$exs_groupid'") . "' WHERE id = '$exs_groupid'");
			update_karma($exs_userid);
			$db->query("UPDATE `users` SET `lastseen` = NOW() WHERE `id` = '$exs_userid'");
		}
	}
	echo $url . ' done. Waiting...
';
	sleep(1);
}

$mta_count = curl_get('http://mta.exs.lv/monitor/count.php');
$db->query("INSERT INTO `mta_chart` (`time`, `count`) VALUES ('".date('Y-m-d H:i').":00', '".intval($mta_count)."')");

get_rss_youtube('http://gdata.youtube.com/feeds/api/users/GoGeocaching/uploads', 20908, 91);

$cats = $db->get_results("SELECT id FROM cat");
foreach ($cats as $cat) {
	update_stats($cat->id);
}

$get_img->xkcd();
$get_img->reddit();

