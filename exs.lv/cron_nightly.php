<?php

/*
  cron_nightly.php
  Izpildās katru dienu naktī, kad ir maz apmeklētāju un maza slodze.
  30 3    * * *   exs php /home/www/exs.lv/cron_nightly.php
  iztīra vecos lietotāju logus un profila skatījumus, optimizē tabulas un citi "smagie" cleanup darbi
 */

if (PHP_SAPI !== 'cli') {
	echo 'CLI only!';
	exit;
}

echo 'cron_nightly.php started' . "\n";

chdir(__DIR__);
ini_set('memory_limit', '256M');
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require('configdb.php');
require(CORE_PATH . '/includes/class.mdb.php');
require(CORE_PATH . '/includes/functions.core.php');

//mysql konekcija
$db = new mdb($username, $password, $database, $hostname);
unset($password);

//memcached konekcija
$m = new Memcached;
$m->addServer($mc_host, $mc_port);

####################### PROFILA SKATIJUMU UN LOGU TIRISANA
$users = $db->get_results("SELECT `id` FROM `users` WHERE `deleted` = 0");
$i = 0;
foreach ($users as $user) {

	$langs = [1,3,7,9];

	foreach($langs as $clean) {
		$db->query("DELETE FROM `userlogs` WHERE user='$user->id' AND `lang` = '$clean' AND id NOT IN (SELECT * FROM (SELECT id FROM userlogs WHERE user='$user->id' AND `lang` = '$clean' ORDER BY id DESC LIMIT 100) AS TAB)");
		$db->query("DELETE FROM `notify` WHERE user_id='$user->id' AND `lang` = '$clean' AND id NOT IN (SELECT * FROM (SELECT id FROM notify WHERE  user_id='$user->id' AND `lang` = '$clean' ORDER BY bump DESC LIMIT 100) AS TAB)");
	}

	$db->query("DELETE FROM `viewprofile` WHERE profile='$user->id' AND id NOT IN (SELECT * FROM (SELECT id FROM viewprofile WHERE profile='$user->id' ORDER BY `time` DESC LIMIT 100) AS TAB)");

	update_karma($user->id, true);

	$i++;
}

echo 'cleanup un karma update... ' . $i . '... ok' . "\n";


//old notifications
$db->query("DELETE FROM `logs` WHERE `created` < '".date('Y-m-d H:i', strtotime('-1 month'))."'");
$db->query("DELETE FROM `api_logs` WHERE `created_at` < '".date('Y-m-d H:i', strtotime('-1 month'))."'");
$db->query("DELETE FROM `visits` WHERE `lastseen` < '".date('Y-m-d H:i', strtotime('-10 years'))."'");
$db->query("DELETE FROM `failed_logins` WHERE `date` < '".date('Y-m-d H:i', strtotime('-1 week'))."'");
$db->query("DELETE FROM `users_tmp` WHERE `created` < '".date('Y-m-d H:i', strtotime('-1 month'))."'");
$db->query("DELETE FROM `miniblog` WHERE `date` < '".date('Y-m-d H:i', strtotime('-1 year'))."' AND `removed` = 1");
$db->query("DELETE FROM `comments` WHERE `date` < '".date('Y-m-d H:i', strtotime('-1 year'))."' AND `removed` = 1");
$db->query("DELETE FROM `galcom` WHERE `date` < '".date('Y-m-d H:i', strtotime('-1 year'))."' AND `removed` = 1");
$db->query("DELETE FROM `miniblog_ver` WHERE `modified` < '".date('Y-m-d H:i', strtotime('-1 year'))."'");
$db->query("DELETE FROM `pages_ver` WHERE `time` < '".strtotime('-2 years')."'");
$db->query("UPDATE `users` SET `private` = 1 WHERE `lastseen` < '".date('Y-m-d H:i', strtotime('-3 years'))."'");
$db->query("UPDATE `users` SET `user_agent` = '' WHERE `lastseen` < '".date('Y-m-d H:i', strtotime('-3 months'))."'");
$db->query("DELETE FROM `cat` WHERE `isblog` > 0 AND `stat_topics` = 0 and isblog in(SELECT id FROM users WHERE lastseen < '".date('Y-m-d H:i', strtotime('-3 months'))."')");
$db->query("UPDATE `images` SET private = 1 WHERE `uid` in(SELECT id FROM users WHERE lastseen < '".date('Y-m-d H:i', strtotime('-10 years'))."')");


//remove old users
$users = $db->get_results("SELECT * FROM `users` WHERE `lastseen` < '".date('Y-m-d H:i', strtotime('-10 years'))."' AND `posts` < 3 AND `deleted` = 0");

foreach($users as $user) {
	
	$db->query("DELETE FROM `users` WHERE `id` = '$user->id'");
	$db->query("DELETE FROM `clans_members` WHERE `user` = '$user->id'");
	$db->query("DELETE FROM `banned` WHERE `user_id` = '$user->id'");
	$db->query("DELETE FROM `warns` WHERE `user_id` = '$user->id'");
	$db->query("DELETE FROM `notify` WHERE `user_id` = '$user->id'");
	$db->query("DELETE FROM `notes` WHERE `user_id` = '$user->id'");
	$db->query("DELETE FROM `cat_moderators` WHERE `user_id` = '$user->id'");
	$db->query("DELETE FROM `steam_player_info` WHERE `user_id` = '$user->id'");
	$db->query("DELETE FROM `viewprofile` WHERE `profile` = '$user->id'");
	$db->query("DELETE FROM `viewprofile` WHERE `viewer` = '$user->id'");
	$db->query("DELETE FROM `bookmarks` WHERE `userid` = '$user->id'");
	$db->query("DELETE FROM `autoawards` WHERE `user_id` = '$user->id'");
	$db->query("DELETE FROM `lastfm_tracks` WHERE `user_id` = '$user->id'");
	$db->query("DELETE FROM `userlogs` WHERE `user` = '$user->id'");
	$db->query("DELETE FROM `images` WHERE `uid` = '$user->id'");
	$db->query("UPDATE `comments` SET `removed` = 1 WHERE `author` = '$user->id'");
	$db->query("UPDATE `galcom` SET `removed` = 1 WHERE `author` = '$user->id'");
	$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `author` = '$user->id'");
	$db->query("UPDATE `pages` SET `private` = 1 WHERE `author` = '$user->id'");


}

//uploaded images
$images = $db->get_results("SELECT * FROM `imgupload` WHERE `user_id` NOT IN(SELECT id FROM users WHERE deleted = 0)");
if(!empty($images)) {
	foreach($images as $image) {
		unlink('/home/www/img.exs.lv/' . $image->path . '/' . $image->file);
		unlink('/home/www/img.exs.lv/' . $image->path . '/small/' . $image->file);
		$db->query("DELETE FROM `imgupload` WHERE `id` = '$image->id' LIMIT 1");
		echo "deleting image ".$image->id."... ok\n";
	}
}

//gif avatars assigned to deleted users
$anim = $db->get_results("SELECT *  FROM `animations` WHERE `user_id` > 0 and `user_id` not in(select id from users where deleted = 0)");

foreach($anim as $av) {
	unlink("/home/www/exs.lv/dati/bildes/u_small/" . $av->image);
	unlink("/home/www/exs.lv/dati/bildes/u_large/" . $av->image);
	unlink("/home/www/exs.lv/dati/bildes/useravatar/" . $av->image);
	$db->query("DELETE FROM `animations` WHERE `id` = ". $av->id);
}

$db->query("DELETE FROM `friends` WHERE `friend1` NOT IN(SELECT id FROM users WHERE deleted = 0)");
$db->query("DELETE FROM `friends` WHERE `friend2` NOT IN(SELECT id FROM users WHERE deleted = 0)");
$db->query("DELETE FROM `miniblog` WHERE `author` NOT IN(SELECT id FROM users WHERE deleted = 0)");
$db->query("DELETE FROM `pages` WHERE `author` NOT IN(SELECT id FROM users WHERE deleted = 0)");
$db->query("DELETE FROM `pages` WHERE `category` NOT IN(SELECT id FROM cat)");
$db->query("DELETE FROM `visits` WHERE `user_id` NOT IN(SELECT `id` FROM `users` WHERE `deleted` = 0)");
$db->query("DELETE FROM `pm` WHERE `from_uid` NOT IN(SELECT id FROM users WHERE deleted = 0)");
$db->query("DELETE FROM `pm` WHERE `to_uid` NOT IN(SELECT id FROM users WHERE deleted = 0)");
$db->query("DELETE FROM `avatar_history` WHERE `user_id` NOT IN(SELECT id FROM users WHERE deleted = 0)");
$db->query("UPDATE `users` SET `lastip` = NULL WHERE deleted = 1");
$db->query("DELETE FROM `miniblog` WHERE type = 'miniblog' and parent > 0 and parent not in(SELECT * FROM (SELECT id from miniblog WHERE parent = 0) as TAB)");
$db->query("DELETE FROM miniblog WHERE reply_to > 0 and reply_to not in(SELECT * FROM (SELECT id from miniblog) as TAB)");
$db->query("DELETE FROM `user_interests` WHERE user_id not in(SELECT id FROM users WHERE deleted = 0)");
$db->query("DELETE FROM `movie_ratings` WHERE user_id NOT IN(SELECT id FROM users WHERE deleted = 0)");
$db->query("DELETE FROM `pages` WHERE `author` NOT IN(SELECT id FROM users WHERE deleted = 0) and category in (select id from cat where isblog > 0)");
$db->query("DELETE FROM `cat` WHERE `isblog` > 0 and isblog NOT IN(select id from users where deleted = 0)");
$db->query("DELETE FROM `comments` WHERE `pid` NOT IN(SELECT id FROM pages)");
$db->query("DELETE FROM `comments` WHERE `author` NOT IN(SELECT id FROM users WHERE deleted =0)");
$db->query("DELETE FROM `images` WHERE `uid` NOT IN(SELECT id FROM users WHERE deleted = 0)");
$db->query("DELETE FROM `galcom` WHERE `bid` NOT IN(SELECT id FROM images)");
$db->query("DELETE FROM `galcom` WHERE `author` NOT IN(SELECT id FROM users WHERE deleted =0)");
$db->query("DELETE FROM `cat` WHERE `module` LIKE 'group' and content not in(select id from clans) and textid != 'group'");
$db->query("DELETE FROM `ajax_comments` WHERE user_id not in(select id from users where deleted = 0)");
$db->query("DELETE FROM pages WHERE category NOT IN(SELECT id FROM cat)");
$db->query("DELETE FROM `desas` WHERE `user_1` NOT IN(SELECT id FROM users WHERE deleted = 0) AND  `user_2` NOT IN(SELECT id FROM users WHERE deleted = 0)");
$db->query("DELETE FROM `taged` WHERE `type` = 0 AND page_id NOT IN(SELECT id FROM pages)");
$db->query("DELETE FROM `taged` WHERE `type` = 1 AND page_id NOT IN(SELECT id FROM images)");
$db->query("DELETE FROM `taged` WHERE `type` = 2 AND page_id NOT IN(SELECT id FROM miniblog WHERE removed = 0)");
$db->query("DELETE FROM `tags` WHERE id NOT IN(SELECT tag_id FROM taged)");
$db->query("DELETE FROM `bookmarks` WHERE `foreign_table` = 'pages' AND `pageid` NOT IN(SELECT id FROM pages)");
$db->query("DELETE FROM `bookmarks` WHERE `userid` NOT IN(SELECT id FROM users WHERE deleted = 0)");
$db->query("DELETE FROM `wg_games` WHERE `user_id` NOT IN(SELECT id FROM users WHERE deleted = 0)");
$db->query("DELETE FROM `cat_ignore` WHERE `user_id` NOT IN(SELECT id FROM users WHERE deleted = 0)");
$db->query("DELETE FROM `cat_ignore` WHERE `category_id` NOT IN(SELECT id FROM cat)");
$db->query("DELETE FROM `cat_moderators` WHERE `category_id` NOT IN(SELECT id FROM cat)");
$db->query("DELETE FROM `cat_moderators` WHERE `user_id` NOT IN(SELECT id FROM users WHERE deleted = 0)");
$db->query("DELETE FROM `awards` WHERE `user` NOT IN(SELECT id FROM users WHERE deleted = 0)");
$db->query("DELETE FROM `pages_ver` WHERE `pid` NOT IN(SELECT id FROM pages)");
$db->query("DELETE FROM `wg_results` WHERE `user_id` NOT IN(SELECT id FROM users WHERE deleted = 0)");
$db->query("DELETE FROM `movie_ratings` WHERE `page_id` NOT IN(SELECT id FROM pages)");
$db->query("DELETE FROM `users_groups` WHERE `user_id` NOT IN(SELECT id FROM users WHERE deleted = 0)");
$db->query("DELETE FROM `miniblog_ver` WHERE `mbid` NOT IN(SELECT id FROM miniblog)");
$db->query("DELETE FROM `movie_data` WHERE page_id NOT IN(select id FROM pages)");
$db->query("DELETE FROM `movie_genres` WHERE page_id NOT IN(select id FROM pages)");
$db->query("DELETE FROM `reports` WHERE created_by NOT IN(select id from users where deleted = 0)");
$db->query("DELETE FROM `clans_tabs` WHERE `clan_id` NOT IN(SELECT id FROM clans)");
$db->query("DELETE FROM `viewprofile` WHERE `profile`  NOT IN(SELECT id FROM users WHERE deleted = 0)");
$db->query("DELETE FROM `viewprofile` WHERE `viewer` NOT IN(SELECT id FROM users WHERE deleted = 0)");
$db->query("DELETE FROM `notify` WHERE `type` IN(3,8,13,14) AND `foreign_key` NOT IN(SELECT id FROM miniblog WHERE removed  = 0)");
$db->query("DELETE FROM `notify` WHERE `type` IN(2,15) AND `foreign_key` NOT IN(SELECT id FROM pages)");
$db->query("DELETE FROM `notify` WHERE `type` IN(1,16) AND `foreign_key` NOT IN(SELECT id FROM images)");
$db->query("DELETE FROM `sidelinks` WHERE category not in(select id from cat)");
$db->query("DELETE FROM `mc_users` WHERE `id` NOT IN(select id from users where deleted = 0)");
$db->query("DELETE FROM `warns` WHERE `created_by` NOT IN(select id from users where deleted = 0)");
$db->query("DELETE FROM `clans_ver` WHERE `group_id` NOT IN (select id from clans)");
$db->query("DELETE FROM `banned` WHERE `ip` LIKE '--' and `user_id` not in(SELECT id FROM users WHERE deleted = 0)");
$db->query("DELETE FROM `banned` WHERE `user_id` not in(SELECT id FROM users WHERE deleted = 0) AND `time` < '" . strtotime('-1 month') . "' ");
$db->query("DELETE FROM `banned` WHERE `author` NOT IN(SELECT id FROM users)");
$db->query("DELETE FROM `poll` WHERE `group` = 0 and topic not in(select id from pages)");
$db->query("DELETE FROM `poll` WHERE `group` > 0 and `group` not in(select id from clans)");
$db->query("DELETE FROM `questions` WHERE pid not in(select id from poll)");
$db->query("DELETE FROM `responses` WHERE qid not in(select id from questions);");
$db->query("DELETE FROM `clans_members` WHERE user not in(SELECT id FROM users WHERE deleted = 0)");
$db->query("DELETE FROM `clans_members` WHERE clan not in(SELECT id FROM clans)");
$db->query("DELETE FROM `miniblog` WHERE `groupid` > 0 and `groupid` NOT IN(select id from clans)");

//junk
$db->query("DELETE FROM `junk` WHERE `approved_by` NOT IN(SELECT id FROM users WHERE deleted =0)");
$db->query("DELETE FROM `junk` WHERE `author` != 0 AND `author` NOT IN(SELECT id FROM users WHERE deleted =0)");
$db->query("DELETE FROM `junk_votes` WHERE `junk_id` NOT IN(SELECT id FROM junk)");
$db->query("DELETE FROM `junk_votes` WHERE `user_id` NOT IN(SELECT id FROM users WHERE deleted = 0)");
$db->query("DELETE FROM `miniblog` WHERE `type` = 'junk' and parent NOT IN(SELECT id FROM junk)");

$db->query("OPTIMIZE TABLE `notify`");
$db->query("OPTIMIZE TABLE `logs`");
$db->query("OPTIMIZE TABLE `api_logs`");
$db->query("OPTIMIZE TABLE `visits`");
$db->query("OPTIMIZE TABLE `failed_logins`");
$db->query("OPTIMIZE TABLE `viewprofile`");
$db->query("OPTIMIZE TABLE `users_tmp`");
$db->query("OPTIMIZE TABLE `users`");
$db->query("OPTIMIZE TABLE `wg_results`");
$db->query("OPTIMIZE TABLE `wg_games`");
$db->query("OPTIMIZE TABLE `bookmarks`");
$db->query("OPTIMIZE TABLE `autoawards`");
$db->query("OPTIMIZE TABLE `avatar_history`");
$db->query("OPTIMIZE TABLE `clan_members`");
$db->query("OPTIMIZE TABLE `friends`");
$db->query("OPTIMIZE TABLE `warns`");
$db->query("OPTIMIZE TABLE `pages`");
$db->query("OPTIMIZE TABLE `pages_ver`");
$db->query("OPTIMIZE TABLE `images`");
$db->query("OPTIMIZE TABLE `galcom`");
$db->query("OPTIMIZE TABLE `comments`");
$db->query("OPTIMIZE TABLE `junk_votes`");
$db->query("OPTIMIZE TABLE `cat`");
$db->query("OPTIMIZE TABLE `pm`");
$db->query("OPTIMIZE TABLE `desas`");
$db->query("OPTIMIZE TABLE `tags`");
$db->query("OPTIMIZE TABLE `taged`");
$db->query("OPTIMIZE TABLE `reports`");
$db->query("OPTIMIZE TABLE `user_interests`");
$db->query("OPTIMIZE TABLE `clans`");
$db->query("OPTIMIZE TABLE `clans_members`");
$db->query("OPTIMIZE TABLE `clans_ver`");
$db->query("OPTIMIZE TABLE `users_groups`");
$db->query("OPTIMIZE TABLE `movie_ratings`");
$db->query("OPTIMIZE TABLE `imgupload`");
$db->query("OPTIMIZE TABLE `banned`");


$cats = $db->get_results("SELECT id FROM cat");
foreach ($cats as $cat) {
	update_stats($cat->id);
}

echo "update cat stats... ok\n";


$db->query("DELETE FROM `taged` WHERE `tag_id` IN(SELECT id FROM `tags` WHERE `name` LIKE '%;%')");
$db->query("DELETE FROM `tags` WHERE `name` LIKE '%;%'");
echo "remve ugly tags... ok\n";



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
		//echo ".";
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
			//echo ".";
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
			//echo ".";
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
		//echo ".";
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
			//echo ".";
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
			//echo ".";
		}

	} else {
		$db->query("DELETE FROM userlogs WHERE id = '".$post->id."' LIMIT 1");
		echo "\n".$post->id." deleted\n";
	}

}


$posts = $db->get_results("SELECT `id`, `action` FROM  `userlogs` WHERE  `action` LIKE  'Izveidoja <a href=\"/say/%'");

foreach($posts as $post) {

        $action = str_replace('Izveidoja <a href="/say/', '', $post->action);
        $action = explode('/', $action);
        $action = explode('-', $action[1]);

        $img = $action[0];

        if(empty($img)) {
                echo "\nPOST ID NOT FOUND\n\n\n\n";
				print_r($post);
                continue;
        }

        $mb = $db->get_var("SELECT count(*) FROM miniblog WHERE id = '".sanitize($img)."' and removed = 0 LIMIT 1");

        if(!empty($mb)) {
                //echo ".";
        } else {
               $db->query("DELETE FROM userlogs WHERE id = '".$post->id."' LIMIT 1");
                echo "\n".$post->id." deleted\n";
        }
}


$db->query("OPTIMIZE TABLE `userlogs`");


$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%dieviete.lv%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%28690182%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%Anziķis%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%rus.tvnet.lv%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%gifmaker.me%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%gifcreator.me%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%/giphy.com%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%makeagif.com%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%imgur.com/vidgif%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%imgflip.com%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%zamzar.com%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%gifreducer.com%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%gifmagic.com%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%mp4togif.com%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%bloggif.com%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%picasion.com%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%animizer.net%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%gifgifs.com%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%strike.lv%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%.rt.com%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%sputniknews%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%docupub.com%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%smallpdf.com%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%loopagain.com%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%gifgif.io%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%lunapic%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%ritakafija.lv%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%boot.lv%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%tomstv.lv%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%nozagts.com%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%vesti.lv%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%montana.lv%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%resizeimage.net%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%pravdareport.com%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%toolur.com%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%pdftojpg.me%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%passwordsgenerator.net%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%odnoklassniki.ru%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%//vk.ru%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%yandex.ru%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%zamzar.com%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%onlinevideoconverter%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%cloudconvert.com%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%apkmirror%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%stormfront%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%nigg%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%1337x.to%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%putlocker%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%youtube%mp3%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%catchmp3.net%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%youtube%downl%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%downl%youtube%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%nra.lv%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%filmas%online%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%hiddenlol%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%hitler%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%convert2mp3.net%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%zivar%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%patv.eu%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%poker%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%casino%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%kazino%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%skatīties%online%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE '%lopatko%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE '%lupatko%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE '%optibet%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE '%totalizāt%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE '%totalizat%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE '%fano.in%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE '%inperil%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%ivar%zvirbul%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE  '%viņas melo labāk%'");
sleep(1);
$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `text` LIKE '%hokejatv%'");


