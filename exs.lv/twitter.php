<?php

if (PHP_SAPI !== 'cli') {
	echo 'CLI only!';
	exit;
}

echo 'twitter.php started' . "\n";

chdir(__DIR__);
ini_set('memory_limit', '128M');
ini_set('display_errors', 'Off');
error_reporting(0);
set_time_limit(0);
require('configdb.php');
require(CORE_PATH . '/includes/class.mdb.php');
require(CORE_PATH . '/includes/functions.core.php');

//mysql konekcija
$db = new mdb($username, $password, $database, $hostname);
unset($password);

//memcached konekcija
$m = new Memcache;
$m->connect($mc_host, $mc_port);

//lietotaji kuriem sekot
$users = array(
	'110089364' => array(
		'nick' => 'Maadinsh',
		'uid' => 1,
		'gid' => 0
	),
	'172640737' => array(
		'nick' => 'SkaForUs',
		'uid' => 1548,
		'gid' => 0
	),
	'123597407' => array(
		'nick' => 'ieluvingrosana',
		'uid' => 17077,
		'gid' => 11
	),
	/*'932730655' => array(
		'nick' => 'Rikinators',
		'uid' => 22051,
		'gid' => 0
	),*/
	/*'347040745' => array(
		'nick' => 'StaticFake',
		'uid' => 13004,
		'gid' => 0
	),*/
	'65642258' => array(
		'nick' => 'Trakais18',
		'uid' => 1216,
		'gid' => 0
	),
	'36635020' => array(
		'nick' => 'viesty09',
		'uid' => 2145,
		'gid' => 0
	),
	'14276842' => array(
		'nick' => 'skakri',
		'uid' => 16261,
		'gid' => 0
	),
	'104146775' => array(
		'nick' => 'exs_lv',
		'uid' => 1,
		'gid' => 0
	),
	'347038330' => array(
		'nick' => 'Styrnucis',
		'uid' => 2222,
		'gid' => 0
	),
	/*'15518000' => array(
		'nick' => 'freq test',
		'uid' => 0,
		'gid' => 0
	)*/ // freq tweeter test
);

/**
 * Unshorten links
 *
 * @param  string $text
 * @return string
 */
function unshorten_links($text) {
	$pattern  = '#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#';
	$callback = create_function('$matches', '
		$url = array_shift($matches);
		$url_parts = parse_url($url);

		$text = parse_url($url, PHP_URL_HOST) . parse_url($url, PHP_URL_PATH);

		$ch = curl_init($text);
		curl_setopt_array($ch, array(
			CURLOPT_FOLLOWLOCATION => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_SSL_VERIFYHOST => FALSE, // suppress certain SSL errors
			CURLOPT_SSL_VERIFYPEER => FALSE,
		));
		curl_exec($ch);
		$text = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		curl_close($ch);

		return $text;
	');

	return preg_replace_callback($pattern, $callback, $text);
}

function cgets($ch, $line) {
	global $users, $db, $m;
	$length = strlen($line);
	printf("Received %d byte\n", $length);

	$tweet = json_decode($line);
	if (isset($tweet->text)) {

		if ($tweet->retweet_count == 0 && !isset($tweet->in_reply_to_user_id) && !empty($users[$tweet->user->id_str])) {
			$user = $users[$tweet->user->id_str];
			$tweet->unixtime = strtotime($tweet->created_at);
			$tweet->datetime = date('Y-m-d H:i:s', $tweet->unixtime);

			echo $user['nick'] ."@ " . $tweet->datetime . ":\n" . $tweet->text . "\n\n";

			$tweet->text = htmlpost2db(unshorten_links($tweet->text));

			$db->query("INSERT INTO `miniblog` (`author`,`groupid`,`date`,`text`,`ip`,`bump`,`twitterid`,`twitteruser`) VALUES ('".$user['uid']."','".$user['gid']."','" . $tweet->datetime . "','" . $tweet->text . "','127.0.0.1','" . time() . "','".$tweet->id ."','".$user['nick']."')");

			if (!$user['gid']) {
				$ins = $db->insert_id;
				$topic = $db->get_row("SELECT * FROM `miniblog` WHERE `id` = '$ins'");
				$topic->text = mention($topic->text, "#", 'mb', $topic->id);
				$db->query("UPDATE `miniblog` SET `text` = '" . sanitize($topic->text) . "' WHERE id = '$topic->id'");

				$title = mb_get_title($topic->text);
				$strid = mb_get_strid($title);
				$url = '/say/' . $user['uid'] . '/' . $ins . '-' . $strid;
				userlog($user['uid'], 'Jauns ieraksts no twitter <a href="' . $url . '">&quot;' . textlimit($title, 32, '...') . '&quot;</a>', '', 'mb-new-' . $ins);
				notify($user['uid'], 3, $ins, $url, 'twitter');
			}
		}
	}
	flush();
	return $length;
}

while(true) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://stream.twitter.com/1/statuses/filter.json');
	curl_setopt($ch, CURLOPT_WRITEFUNCTION, 'cgets');
	curl_setopt($ch, CURLOPT_BUFFERSIZE, 20000); // we want all tweet data in buffer, so json isn't malformed; we're not writing to a file
	curl_setopt($ch, CURLOPT_USERPWD, 'exs_lv:shuffle');
	curl_setopt($ch, CURLOPT_HEADER, TRUE); // debug
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS,
		http_build_query(
			array(
				'follow'=>implode(array_keys($users), ',')
			)
		, '', '&')
	);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_TIMEOUT, 1000000); // ~ 11 days
	curl_exec($ch);
	echo curl_getinfo($ch); // debug
}
