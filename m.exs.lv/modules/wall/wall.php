<?php

set_action('mobilo versiju');

$events = array();
$articles = $db->get_results("
		SELECT
			`pages`.`id` AS `id`,
			`pages`.`title` AS `title`,
			`pages`.`strid` AS `strid`,
			`pages`.`date` AS `date`,
			`pages`.`author` AS `author`,
			`pages`.`posts` AS `posts`,
			`pages`.`bump` AS `bump`,
			`pages`.`avatar` AS `avatar`,
			`pages`.`sm_avatar` AS `sm_avatar`,
			`pages`.`intro` AS `intro`,
			`users`.`nick` AS `nick`,
			`users`.`avatar` AS `user_avatar`,
			`users`.`av_alt` AS `av_alt`,
			`cat`.`title` AS `ctitle`
		FROM
			`pages`,
			`cat`,
			`users`
		WHERE
			category != '83' AND category != '6' AND category != '403' AND
			`users`.`id` = `pages`.`author` AND
			`cat`.`id` = `pages`.`category` AND
			`pages`.`bump` != '0000-00-00 00:00:00' AND
			`pages`.`lang` = '$lang'
		ORDER BY
			`pages`.`bump` DESC
		LIMIT
			8");

foreach ($articles as $article) {

	if ($article->sm_avatar) {
		$article->avatar = 'http://exs.lv/' . $article->sm_avatar;
	} elseif ($article->user_avatar) {
		$article->avatar = '/av/' . $article->user_avatar;
	} else {
		$article->avatar = '/av/none.png';
	}

	$url = '/read/' . $article->strid;
	$time = time_ago_m(strtotime($article->bump));
	$article->title = textlimit($article->title, 125, '...');
	$where = ' <span class="where">#' . $article->ctitle . '</span>';

	$events[strtotime($article->bump) . '-' . $url] = array(
		'url' => $url,
		'author' => $article->nick,
		'title' => $article->title,
		'avatar' => $article->avatar,
		'time' => $time,
		'where' => $where,
		'posts' => $article->posts
	);
}

$usergroups = array("`miniblog`.`groupid` = '0'");
if ($auth->ok) {
	$g_owners = $db->get_col("SELECT id FROM clans WHERE owner = '$auth->id'");
	if ($g_owners) {
		foreach ($g_owners as $g_owner) {
			$usergroups[] = "`miniblog`.`groupid` = '" . $g_owner . "'";
		}
	}
	$g_members = $db->get_col("SELECT clan FROM clans_members WHERE user = '$auth->id' AND approve = '1'");
	if ($g_members) {
		foreach ($g_members as $g_member) {
			$usergroups[] = "`miniblog`.`groupid` = '" . $g_member . "'";
		}
	}
}
$groupquery = implode(' OR ', $usergroups);

$mbs = $db->get_results("SELECT
	`miniblog`.`id` AS `id`,
	`miniblog`.`text` AS `text`,
	`miniblog`.`date` AS `date`,
	`miniblog`.`bump` AS `bump`,
	`miniblog`.`author` AS `author`,
	`miniblog`.`posts` AS `posts`,
	`miniblog`.`groupid` AS `groupid`,
	`users`.`avatar` AS `avatar`,
	`users`.`av_alt` AS `av_alt`,
	`users`.`nick` AS `nick`
FROM
	`miniblog`,
	`users`
WHERE
	`miniblog`.`date` > '" . date('Y-m-d H:i:s', strtotime('-1 day')) . "' AND
	`miniblog`.`removed` = '0' AND
	`miniblog`.`parent` = '0' AND
	`miniblog`.`type` = 'miniblog' AND
	`miniblog`.`lang` = '$lang' AND
	(" . $groupquery . ") AND
	`users`.`id` = `miniblog`.`author`
ORDER BY
	`miniblog`.`bump`
DESC LIMIT 18");

if ($mbs) {

	foreach ($mbs as $mb) {
		if ($mb->avatar == '') {
			$mb->avatar = '/av/none.png';
		} else {
			$mb->avatar = '/av/' . $mb->avatar;
		}

		$mb->text = preg_replace("#(^|[\n ]|<a(.*?)>)http://(www\.)?youtube\.com/watch\?v=([a-zA-Z0-9\-_]+)((.*?)</a>)?#ime", 'get_youtube_title("\\4") ', strip_tags(str_replace(array('<br/>', '<br>', '<br />', '<p>', '</p>', '&nbsp;', "\n", "\r"), ' ', $mb->text)));

		if ($mb->groupid != 0) {
			$group = $db->get_row("SELECT * FROM clans WHERE id = '$mb->groupid'");
			if ($group->avatar) {
				$mb->avatar = '/av/' . $group->avatar;
			}
			$url = '/group/' . $mb->groupid . '/forum/' . base_convert($mb->id, 10, 36);
		} else {
			$url_title = mkslug(textlimit($mb->text, 36, ''));
			$url = '/say/' . $mb->author . '/' . $mb->id . '-' . $url_title;
		}

		$mb->text = wordwrap(hide_spoilers($mb->text), 32, "\n", 1);
		if ($mb->groupid != 0) {
			$mb->text = textlimit($mb->text, 125, '...');
			$where = ' <span class="where">@' . $group->title . '</span>';
		} else {
			$mb->text = textlimit($mb->text, 125, '...');
			$where = '';
		}
		$time = time_ago_m($mb->bump);

		$events[$mb->bump . '-' . $url] = array(
			'url' => $url,
			'author' => $mb->nick,
			'title' => $mb->text,
			'avatar' => $mb->avatar,
			'time' => $time,
			'where' => $where,
			'posts' => $mb->posts
		);
	}
}

ksort($events);
$events = array_reverse($events);

if (!empty($events)) {
	$tpl->newBlock('wall-events');
	$i = 0;
	foreach ($events as $event) {
		if ($i++ >= 20) {
			break;
		}
		$tpl->newBlock('wall-events-node');
		$tpl->assign(array(
			'url' => $event['url'],
			'author' => $event['author'],
			'title' => $event['title'],
			'avatar' => $event['avatar'],
			'time' => $event['time'],
			'where' => $event['where'],
			'posts' => $event['posts']
		));
	}
}
