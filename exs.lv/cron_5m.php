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

function get_twitter_mb($username, $exs_userid = 17077, $exs_groupid = 0) {
	global $db;

	$xml = simplexml_load_file('https://api.twitter.com/1/statuses/user_timeline/' . $username . '.xml?count=3');
	if ($xml) {
		$newtweets = array();
		foreach ($xml->status as $tweet) {
			if (substr($tweet->text, 0, 1) != '@' && substr($tweet->text, 0, 13) != 'Just reported') {
				if (!$db->get_var("SELECT count(*) FROM miniblog WHERE twitterid = '$tweet->id'")) {
					if (!empty($tweet->text) && strlen($tweet->text) > 5) {
						$tweet->text = htmlpost2db($tweet->text);
						$newtweets[] = array($tweet->text, date('Y-m-d H:i:s', strtotime($tweet->created_at)), $tweet->id, strtotime($tweet->created_at));
					}
				}
			}
		}

		if ($newtweets) {
			$newtweets = array_reverse($newtweets);
			foreach ($newtweets as $tw) {

				if ($exs_groupid == 91) {
					$exists = $db->get_var("SELECT id FROM miniblog WHERE groupid = '$exs_groupid' AND twitteruser = '" . sanitize($username) . "' AND `date` LIKE '" . date('Y-m-d') . "%' AND parent = '0' ORDER BY id DESC LIMIT 1");
					if (!$exists) {
						$db->query("INSERT INTO miniblog (author,groupid,date,text,ip,bump,twitterid,twitteruser) VALUES ('$exs_userid','$exs_groupid',NOW(),'" . $tw[0] . "','127.0.0.1','" . $tw[3] . "','" . $tw[2] . "','" . sanitize($username) . "')");
					} else {
						$db->query("INSERT INTO miniblog (parent,author,groupid,date,text,ip,bump,twitterid,twitteruser) VALUES ('$exists','$exs_userid','$exs_groupid','" . $tw[1] . "','" . $tw[0] . "','127.0.0.1','" . $tw[3] . "','" . $tw[2] . "','" . sanitize($username) . "')");
						$db->query("UPDATE miniblog SET bump = '" . time() . "', posts = posts+1 WHERE id = '$exists'");
					}
				} else {
					$db->query("INSERT INTO miniblog (author,groupid,date,text,ip,bump,twitterid,twitteruser) VALUES ('$exs_userid','$exs_groupid','" . $tw[1] . "','" . $tw[0] . "','127.0.0.1','" . time() . "','" . $tw[2] . "','" . sanitize($username) . "')");

					if (!$exs_groupid) {
						$ins = $db->insert_id;
						$body = $db->get_var("SELECT `text` FROM `miniblog` WHERE `id` = '$ins'");

						$topic = $db->get_row("SELECT * FROM `miniblog` WHERE `id` = '$ins'");
						$topic->text = mention($topic->text, "#", 'mb', $topic->id);
						$db->query("UPDATE `miniblog` SET `text` = '" . sanitize($topic->text) . "' WHERE id = '$topic->id'");

						$title = mb_get_title(stripslashes($body));
						$strid = mb_get_strid($title);
						$url = '/say/' . $exs_userid . '/' . $ins . '-' . $strid;
						userlog($exs_userid, 'Jauns ieraksts no twitter <a href="' . $url . '">&quot;' . textlimit($title, 32, '...') . '&quot;</a>', '', 'mb-new-' . $ins);
						notify($exs_userid, 3, $ins, $url, 'twitter');
					}
				}
			}
			update_karma($exs_userid);
			if (17077 == $exs_userid) {
				$db->query("UPDATE `users` SET `lastseen` = NOW() WHERE `id` = '$exs_userid'");
			}
		}
		echo $username . ' done. Waiting...
';
	} else {
		echo $username . ' fail. WTF?...
';
	}
	sleep(2);
}

function get_rss_youtube($url, $exs_userid = 17077, $exs_groupid = 0) {
	global $db;

	$xml = simplexml_load_file($url);
	//pr($xml);
	if ($xml) {
		$newtweets = array();
		foreach ($xml->entry as $item) {
			//pr($item);
			$link = str_replace('&feature=youtube_gdata', '', $item->link['href']);
			if (!$db->get_var("SELECT count(*) FROM miniblog WHERE twitterid = '" . md5($link) . "'")) {
				//pr($link);
				$newtweets[] = array(
					sanitize('<p><strong>' . stripslashes($item->title) . '</strong><br /><a href="' . $link . '">' . $link . '</a><br />' . stripslashes($item->content) . '</p>'),
					date('Y-m-d H:i:s', strtotime($item->published)),
					md5($link),
					strtotime($item->published)
				);
			}
		}

		// pr($newtweets);

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

function get_rss_mb($url, $exs_userid = 17077, $exs_groupid = 0) {
	global $db;

	$xml = simplexml_load_file($url);
	if ($xml) {
		$newtweets = array();
		foreach ($xml->channel->item as $item) {
			if (!$db->get_var("SELECT count(*) FROM miniblog WHERE twitterid = '" . md5($item->link) . "'")) {
				$newtweets[] = array(
					sanitize('<p>Jaunākā sērija:<br /><a href="' . $item->link . '">' . $item->title . '</a></p>'),
					date('Y-m-d H:i:s', strtotime($item->pubDate)),
					md5($item->link),
					strtotime($item->pubDate)
				);
			}
		}

		//pr($newtweets);

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

$rand = file_get_contents('cache/cronupd.txt');
if ($rand == 1) {
	get_rss_mb('http://showrss.karmorra.info/feeds/24.rss', 20908, 45); //dexter
	$get_img->xkcd();
}
if ($rand == 2) {
	get_rss_mb('http://showrss.karmorra.info/feeds/62.rss', 20908, 88); //supernatural
	get_rss_mb('http://showrss.karmorra.info/feeds/5.rss', 20908, 177); //big bang
}
if ($rand == 3) {
	get_rss_mb('http://showrss.karmorra.info/feeds/68.rss', 20908, 24); //weeds
	get_rss_mb('http://showrss.karmorra.info/feeds/37.rss', 20908, 188); //HIMYM
}
if ($rand == 4) {
	get_rss_mb('http://showrss.karmorra.info/feeds/10.rss', 20908, 201); //Chuck
	get_rss_mb('http://showrss.karmorra.info/feeds/63.rss', 20908, 250); // 2 1/2 men
}
if ($rand == 5) {

//	$db->query("TRUNCATE TABLE `async_ip`");

	//get_twitter_mb('brocode', 21638, 188); //barney stinson no himym
	//get_twitter_mb('geocaching_lv', 17077, 91);
}
if ($rand == 6 && rand(0, 5) == 1) {
	get_rss_youtube('http://gdata.youtube.com/feeds/api/users/GoGeocaching/uploads', 20908, 91);
}
$rand++;
if ($rand > 7) {


	$cats = $db->get_results("SELECT id FROM cat");
	foreach ($cats as $cat) {
		update_stats($cat->id);
	}

	$rand = 1;
}
file_put_contents('cache/cronupd.txt', $rand);


$last = file_get_contents('cache/twitter.txt');
if ($last == 1) {
	get_twitter_mb('Maadinsh', 1);
	//get_twitter_mb('Rikinators', 22051);
	$get_img->reddit();
}
if ($last == 2) {
//	get_twitter_mb('StaticFake', 13004);
	get_twitter_mb('Trakais18', 1216);
}
if ($last == 3) {
	get_twitter_mb('viesty09', 2145);
	get_twitter_mb('SkaForUs', 1548); //jesus
}
if ($last == 4) {
	get_twitter_mb('skakri', 16261);
	get_twitter_mb('Styrnucis', 2222);
	$get_img->reddit();
}

$last++;
if ($last > 5) {
	$last = 1;
}

//get_twitter_mb('ieluvingrosana', 17077, 11);
//get_twitter_mb('klabcraft',17077,52);
//get_twitter_mb('evergladeonline',17077,3);
//get_twitter_mb('LatvianRocky',8531);
//get_twitter_mb('Apmulsums',17077,11);
//get_twitter_mb('miks9992',18139);
//get_twitter_mb('officialjagex',17077);
//get_twitter_mb('MrJimThunder',17077);
//get_twitter_mb('Wourrst',6890);
//get_twitter_mb('dzerualu',17077,20);
//get_twitter_mb('ArtursAlksnis',14887); //Danalabi
//get_twitter_mb('ArwiidC',398);
//get_twitter_mb('Teljsh',8872);
//get_twitter_mb('RV1Gmemes',17077,171);

file_put_contents('cache/twitter.txt', $last);

//blogi (200 karma)
$users = $db->get_results("SELECT id,nick FROM users WHERE karma > '199' AND karma < '220'");
foreach ($users as $user) {
	if (!$db->get_var("SELECT count(*) FROM cat WHERE isblog = '$user->id'")) {
		$nick = sanitize($user->nick);
		$db->query("INSERT INTO cat (textid,title,isblog,parent) VALUES ('" . strtolower(mkslug($nick)) . "','$nick blogs','$user->id','110')");
		$db->query("INSERT INTO pm (from_uid,to_uid,date,ip,title,text,is_read) VALUES ('1','$user->id','" . date('Y-m-d H:i:s') . "','127.0.0.1','Blogs exs.lv','" . '<p>Čau ' . $nick . '!</p><p>Tu esi sasniedzis 200+ karmas līmeni, tādēļ Tev piešķirts Exs.lv blogs.</p><p>Bloga administrācijai vari piekļūt <a href="/myblog">šeit</a>.</p><p style="font-size:90%;color: #888;">Šī ziņa ir nosūtīta automātiski.</p>' . "','0')");
		userlog($user->id, 'Sasniedza 200 karmas līmeni un ieguva <a href="/' . strtolower(mkslug($nick)) . '">blogu</a>');
		$m->delete('isb_' . $user->id);
		update_karma($user->id, true);
		$db->query("UPDATE `cat` SET `ordered` = `id` WHERE `ordered` = 0");
	}
}
