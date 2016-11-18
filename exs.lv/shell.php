<?php

if (PHP_SAPI !== 'cli') {
	echo 'CLI only!';
	exit;
}

echo 'shell.php started' . "\n";

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
$m = new Memcached;
$m->addServer($mc_host, $mc_port);

//ategoriju stati
/*$cats = $db->get_results("SELECT id FROM cat");
foreach ($cats as $cat) {
	update_stats($cat->id);
}*/

/* remove broken links to miniblog posts */
$posts = $db->get_results("SELECT `id`, `multi` FROM  `userlogs` WHERE `multi` LIKE  'gsign%'");

foreach($posts as $post) {

	$img = str_replace('gsign', '', $post->multi);

	if(empty($img)) {
		echo "\n\GROUP ID NOT FOUND\n\n\n\n";
		continue;
	}

	$mb = $db->get_var("SELECT count(*) FROM clans WHERE id = '".sanitize($img)."' LIMIT 1");

	if(!empty($mb)) {
		echo ".";
	} else {
		$db->query("DELETE FROM userlogs WHERE id = '".$post->id."' LIMIT 1");
		echo "\n".$post->id." deleted\n";
	}

}

/* remove broken links to group posts */
$posts = $db->get_results("SELECT `id`, `multi`,`private` FROM  `userlogs` WHERE `multi` LIKE  'g-%'");

foreach($posts as $post) {

	$img = str_replace('g-', '', $post->multi);

	if(empty($img)) {
		echo "\n\POST ID NOT FOUND\n\n\n\n";
		continue;
	}

	$mb = $db->get_var("SELECT count(*) FROM miniblog WHERE id = '".sanitize($img)."' AND removed = 0 LIMIT 1");

	if(!empty($mb)) {

		$private = $db->get_var("SELECT `private` FROM miniblog WHERE id = '".sanitize($img)."' LIMIT 1");
		if($private && !$post->private) {
			$db->query("UPDATE userlogs SET `private` = '$private' WHERE `id` = '".$post->id."' LIMIT 1");
			echo "\n".$post->id." set to private\n";
		} else {
			echo ".";
		}

	} else {
		$db->query("DELETE FROM userlogs WHERE id = '".$post->id."' LIMIT 1");
		echo "\n".$post->id." deleted\n";
	}

}

/* remove broken links to miniblog posts */
$posts = $db->get_results("SELECT `id`, `multi`,`private` FROM  `userlogs` WHERE `multi` LIKE  'mb-answ-%'");

foreach($posts as $post) {

	$img = str_replace('mb-answ-', '', $post->multi);

	if(empty($img)) {
		echo "\n\POST ID NOT FOUND\n\n\n\n";
		continue;
	}

	$mb = $db->get_var("SELECT count(*) FROM miniblog WHERE id = '".sanitize($img)."' AND removed = 0 LIMIT 1");

	if(!empty($mb)) {

		$private = $db->get_var("SELECT `private` FROM miniblog WHERE id = '".sanitize($img)."' LIMIT 1");
		if($private && !$post->private) {
			$db->query("UPDATE userlogs SET `private` = '$private' WHERE `id` = '".$post->id."' LIMIT 1");
			echo "\n".$post->id." set to private\n";
		} else {
			echo ".";
		}

	} else {
		$db->query("DELETE FROM userlogs WHERE id = '".$post->id."' LIMIT 1");
		echo "\n".$post->id." deleted\n";
	}

}

/* remove broken links to image comments */
$posts = $db->get_results("SELECT `id`, `multi` FROM  `userlogs` WHERE `multi` LIKE  'img%'");

foreach($posts as $post) {

	$img = str_replace('img', '', $post->multi);

	if(empty($img)) {
		echo "\n\IMG ID NOT FOUND\n\n\n\n";
		continue;
	}

	$image = $db->get_var("SELECT count(*) FROM images WHERE id = '".sanitize($img)."' LIMIT 1");

	if(!empty($image)) {
		echo ".";
	} else {
		$db->query("DELETE FROM userlogs WHERE id = '".$post->id."' LIMIT 1");
		echo "\n".$post->id." deleted\n";
	}

}


/* remove broken links from userlogs table */
$posts = $db->get_results("SELECT `id`, `action` FROM  `userlogs` WHERE  `action` LIKE  'Komentēja rakstu &quot;<a href=\"/read/%'");

foreach($posts as $post) {

	$action = str_replace('Komentēja rakstu &quot;<a href="/read/', '', $post->action);
	$action = explode('#', $action);
	$strid = $action[0];

	if(empty($strid)) {
		echo "\n\nSTRID NOT FOUND\n\n\n\n";
		continue;
	}

	$page = $db->get_var("SELECT count(*) FROM pages WHERE strid = '".sanitize($strid)."' LIMIT 1");

	if(!empty($page)) {

		$musars = $db->get_var("SELECT `category` FROM `pages` WHERE `strid` = '".sanitize($strid)."' LIMIT 1");
		if($musars == 6) {
			$db->query("DELETE FROM userlogs WHERE id = '".$post->id."' LIMIT 1");
			echo "\n".$post->id." deleted (musars)\n";
		} else {
			echo ".";
		}

	} else {
		$db->query("DELETE FROM userlogs WHERE id = '".$post->id."' LIMIT 1");
		echo "\n".$post->id." deleted\n";
	}

}

/* remove broken links from userlogs table (create topic) */
$posts = $db->get_results("SELECT `id`, `action` FROM  `userlogs` WHERE  `action` LIKE  'Aizsāka foruma tēmu <a href=\"/read/%'");

foreach($posts as $post) {

	$action = str_replace('Aizsāka foruma tēmu <a href="/read/', '', $post->action);
	$action = explode('"', $action);
	$strid = $action[0];

	if(empty($strid)) {
		echo "\n\nSTRID NOT FOUND\n\n\n\n";
		continue;
	}

	$page = $db->get_var("SELECT count(*) FROM pages WHERE strid = '".sanitize($strid)."' LIMIT 1");

	if(!empty($page)) {

		$musars = $db->get_var("SELECT `category` FROM `pages` WHERE `strid` = '".sanitize($strid)."' LIMIT 1");
		if($musars == 6) {
			$db->query("DELETE FROM userlogs WHERE id = '".$post->id."' LIMIT 1");
			echo "\n".$post->id." deleted (musars)\n";
		} else {
			echo ".";
		}

	} else {
		$db->query("DELETE FROM userlogs WHERE id = '".$post->id."' LIMIT 1");
		echo "\n".$post->id." deleted\n";
	}

}



//karma
/*$users = $db->get_results("SELECT `id` FROM `users` ORDER BY `lastseen` DESC");
echo "start update_karma()\n";
$i = 0;
$tot = 0;
foreach ($users as $val) {
	update_karma($val->id, true);
	$i++;
	$tot++;
	if ($i > 99) {
		echo $tot . " updated...\n";
		$i = 0;
		//sleep(1);
	}
}
echo "end update_karma()\n";*/

echo "\ndone!\n";
