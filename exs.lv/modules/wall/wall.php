<?php

//load css
$add_css[] = 'wall.css';

/**
 * Sākumlapa
 */
set_action('sākumlapu');

$events = array();

$mods_only = '';
if (!im_mod()) {
	$mods_only = " `cat`.`mods_only` = 0 AND ";
}


############ ARTICLES

$articles = $db->get_results("
		SELECT
			`pages`.`id` AS `id`,
			`pages`.`title` AS `title`,
			`pages`.`text` AS `text`,
			`pages`.`strid` AS `strid`,
			`pages`.`date` AS `date`,
			`pages`.`author` AS `author`,
			`pages`.`posts` AS `posts`,
			`pages`.`bump` AS `bump`,
			`pages`.`avatar` AS `article_avatar`,
			`pages`.`intro` AS `intro`,
			`users`.`nick` AS `nick`,
			`users`.`avatar` AS `avatar`,
			`users`.`av_alt` AS `av_alt`,
			`users`.`level` AS `level`,
			`cat`.`title` AS `ctitle`
		FROM
			`pages`,
			`cat`,
			`users`
		WHERE
			category != '83' AND category != '6' AND category != '403' AND
			`users`.`id` = `pages`.`author` AND
			`cat`.`id` = `pages`.`category` AND
			" . $mods_only . "
			`pages`.`bump` != '0000-00-00 00:00:00' AND
			`pages`.`lang` = '$lang'
		ORDER BY
			`pages`.`bump` DESC
		LIMIT
			10");

foreach ($articles as $article) {

	if ($article->article_avatar) {
		$article->avatar = '/' . $article->article_avatar;
	} else {
		$article->avatar = get_avatar($article, 'm');
	}

	$url = '/read/' . $article->strid;
	$time = time_ago_m(strtotime($article->bump));

	$article->text = wordwrap(hide_spoilers(strip_tags($article->text)), 32, "\n", 1);
	$article->text = textlimit($article->text, 140, '...');

	$where = ' &raquo; <span class="where">' . $article->ctitle . '</span> &raquo; <span class="where">' . $article->title . '</span>';

	$last_post_html = '';
	if ($article->posts > 0) {
		$lastpost = $db->get_row(
				"SELECT
				comments.*,
				`users`.`nick` AS `nick`,
				`users`.`avatar` AS `avatar`,
				`users`.`av_alt` AS `av_alt`,
				`users`.`level` AS `level`
			FROM
				`comments`,
				`users`
			WHERE
				comments.pid = $article->id AND
				comments.removed = 0 AND
				users.id = comments.author
			ORDER BY comments.id DESC
			LIMIT 1"
		);

		if (!empty($lastpost)) {

			$last_post_html = '
			<div class="last-post">
				<img src="' . get_avatar($lastpost, 's') . '" alt="" class="av" style="float:left;width: 32px;height:32px;" />
				<div class="post-info">
					<span class="lastpost-author">' . usercolor($lastpost->nick, $lastpost->level) . ':</span>
				</div>
				<span class="lastpost-text">' . add_smile($lastpost->text) . '</span>
			</div>

			';
		}
	}

	$events[strtotime($article->bump) . '-' . $url] = array(
		'url' => $url,
		'author' => usercolor($article->nick, $article->level),
		'title' => $article->title,
		'avatar' => $article->avatar,
		'time' => $time,
		'where' => $where,
		'posts' => $article->posts,
		'lastpost' => $last_post_html
	);
}


########### IMAGES 

$images = $db->get_results("
		SELECT
			`images`.`id` AS `id`,
			`images`.`text` AS `text`,
			`images`.`date` AS `date`,
			`images`.`uid` AS `author`,
			`images`.`posts` AS `posts`,
			`images`.`bump` AS `bump`,
			`images`.`url` AS `url`,
			`users`.`nick` AS `nick`,
			`users`.`avatar` AS `avatar`,
			`users`.`av_alt` AS `av_alt`,
			`users`.`level` AS `level`
		FROM
			`images`,
			`users`
		WHERE
			`users`.`id` = `images`.`uid` AND
			`images`.`bump` != '0000-00-00 00:00:00' AND
			`images`.`lang` = '$lang'
		ORDER BY
			`images`.`bump` DESC
		LIMIT
			10");

foreach ($images as $image) {

	$image->avatar = get_avatar($image, 'm');

	$url = '/gallery/' . $image->author . '/' . $image->id;
	$time = time_ago_m(strtotime($image->bump));

	$image->text = wordwrap(hide_spoilers(strip_tags($image->text)), 32, "\n", 1);
	$image->text = textlimit($image->text, 140, '...') . '<br /><img class="attels_centrets" style="width:200px;" src="'.$image->url.'" alt="" />';

	$where = ' &raquo; <span class="where">galerija</span>';

	$last_post_html = '';
	if ($image->posts > 0) {
		$lastpost = $db->get_row(
				"SELECT
				galcom.*,
				`users`.`nick` AS `nick`,
				`users`.`avatar` AS `avatar`,
				`users`.`av_alt` AS `av_alt`,
				`users`.`level` AS `level`
			FROM
				`galcom`,
				`users`
			WHERE
				galcom.bid = $image->id AND
				galcom.removed = 0 AND
				users.id = galcom.author
			ORDER BY galcom.id DESC
			LIMIT 1"
		);

		if (!empty($lastpost)) {

			$last_post_html = '
			<div class="last-post">
				<img src="' . get_avatar($lastpost, 's') . '" alt="" class="av" style="float:left;width: 32px;height:32px;" />
				<div class="post-info">
					<span class="lastpost-author">' . usercolor($lastpost->nick, $lastpost->level) . ':</span>
				</div>
				<span class="lastpost-text">' . add_smile($lastpost->text) . '</span>
			</div>

			';
		}
	}

	$events[strtotime($image->bump) . '-' . $url] = array(
		'url' => $url,
		'author' => usercolor($image->nick, $image->level),
		'title' => $image->text,
		'avatar' => $image->avatar,
		'time' => $time,
		'where' => $where,
		'posts' => $image->posts,
		'lastpost' => $last_post_html
	);
}


########### MINIBLOGS

if ($auth->level == 1) {
	$groupquery = '1 = 1';
} else {
	$usergroups = array("`miniblog`.`groupid` = '0'");
	if ($auth->ok === true) {
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
}

$addlang = "`miniblog`.`lang` = '$lang'";

//miniblogi kas nav publiski pieejami
$priv = '';
if(!$auth->ok) {
	$priv = ' AND `miniblog`.`private` = 0 ';
}

$mbs = $db->get_results("SELECT
	`miniblog`.`id` AS `id`,
	`miniblog`.`text` AS `text`,
	`miniblog`.`bump` AS `bump`,
	`miniblog`.`date` AS `date`,
	`miniblog`.`lang` AS `lang`,
	`miniblog`.`author` AS `author`,
	`miniblog`.`posts` AS `posts`,
	`miniblog`.`groupid` AS `groupid`,
	`users`.`avatar` AS `avatar`,
	`users`.`deleted` AS `deleted`,
	`users`.`av_alt` AS `av_alt`,
	`users`.`level` AS `level`,
	`users`.`nick` AS `nick`
FROM
	`miniblog` USE INDEX(`parent_2`),
	`users` USE INDEX(`PRIMARY`)
WHERE
	`miniblog`.`removed` = '0' AND
	`miniblog`.`parent` = '0' AND
	`miniblog`.`type` = 'miniblog' AND
	" . $addlang . " AND
	(" . $groupquery . ") AND
	`users`.`id` = `miniblog`.`author`
	$priv
ORDER BY
	`miniblog`.`bump`
DESC LIMIT 0, 20");

if ($mbs) {

	foreach ($mbs as $mb) {


		$mb->text = mb_get_title($mb->text);

		if ($mb->groupid != 0) {
			$group = $db->get_row("SELECT * FROM clans WHERE id = '$mb->groupid'");
			if ($group->avatar) {
				$mb->avatar = $group->avatar;
				$mb->av_alt = 1;
			}
			$url = '/group/' . $mb->groupid . '/forum/' . base_convert($mb->id, 10, 36);
		} else {
			$url_title = mkslug(textlimit($mb->text, 36, ''));
			$url = '/say/' . $mb->author . '/' . $mb->id . '-' . $url_title;
		}

		$mb->avatar = get_avatar($mb, 'm');

		$mb->text = wordwrap(hide_spoilers($mb->text), 32, "\n", 1);
		if ($mb->groupid != 0) {
			$mb->text = textlimit($mb->text, 140, '...');
			$where = '  &raquo; <span class="where">' . $group->title . '</span>';
		} else {
			$mb->text = textlimit($mb->text, 140, '...');
			$where = '  &raquo; <span class="where">miniblogs</span>';
		}
		$time = time_ago_m($mb->bump);


		$last_post_html = '';
		if ($mb->posts > 0) {
			$lastpost = $db->get_row(
					"SELECT
					miniblog.*,
					`users`.`nick` AS `nick`,
					`users`.`avatar` AS `avatar`,
					`users`.`av_alt` AS `av_alt`,
					`users`.`level` AS `level`
				FROM
					`miniblog`,
					`users`
				WHERE
					miniblog.parent = $mb->id AND
					miniblog.removed = 0 AND
					users.id = miniblog.author
				ORDER BY miniblog.id DESC
				LIMIT 1"
			);

			if (!empty($lastpost)) {

				$last_post_html = '
				<div class="last-post">
					<img src="' . get_avatar($lastpost, 's') . '" alt="" class="av" style="float:left;width: 32px;height:32px;" />
					<div class="post-info">
						<span class="lastpost-author">' . usercolor($lastpost->nick, $lastpost->level) . ':</span>
					</div>
					<span class="lastpost-text">' . add_smile($lastpost->text) . '</span>
				</div>

				';
			}
		}

		$events[$mb->bump . '-' . $url] = array(
			'url' => $url,
			'author' => usercolor($mb->nick, $mb->level),
			'title' => $mb->text,
			'avatar' => $mb->avatar,
			'time' => $time,
			'where' => $where,
			'posts' => $mb->posts,
			'lastpost' => $last_post_html
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
			'posts' => $event['posts'],
			'lastpost' => $event['lastpost']
		));
	}
}

