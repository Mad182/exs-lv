<?php

if (isset($_GET['skip'])) {
	$skip = (int) $_GET['skip'];
} else {
	$skip = 0;
}

$end = 60;
if ($category->intro) {
	$end = 25;
}
if ($category->showall) {
	$end = 400;
}
$sortby = "`pages`.`date` DESC";

$pagepath = $category->title;
if ($category->parent) {
	$category2 = $db->get_row("SELECT title,textid FROM `cat` WHERE `id` = '$category->parent'");
	$pagepath = '<a href="/' . $category2->textid . '">' . $category2->title . '</a> / ' . $pagepath;
}

if (!$category->mods_only or ($auth->ok && ($auth->level == 1 or $auth->level == 2))) {

	$articles = $db->get_results("
		SELECT
			`pages`.`id` AS `id`,
			`pages`.`title` AS `title`,
			`pages`.`date` AS `date`,
			`pages`.`author` AS `author`,
			`pages`.`posts` AS `posts`,
			`pages`.`closed` AS `closed`,
			`pages`.`text` AS `text`,
			`pages`.`avatar` AS `avatar`,
			`pages`.`readby` AS `readby`,
			`pages`.`views` AS `views`,
			`pages`.`attach` AS `attach`,
			`pages`.`category` AS `category`,
			`pages`.`intro` AS `intro`,
			`users`.`nick` AS `nick`,
			`users`.`level` AS `level`
		FROM
			`pages`,
			`users`
		WHERE
			`pages`.`category` IN (1,10,11,80,81,323,565,611,651) AND
			`users`.`id` = `pages`.`author`
		ORDER BY
			" . $sortby . "
		LIMIT
			$skip,$end");

	if ($articles) {

		$total = $db->get_var("SELECT count(*) FROM pages WHERE category = ('" . $category->id . "')");

		if ($skip) {
			$page_title = $page_title . ' - lapa ' . ($skip / $end + 1);
		}

		$tpl->newBlock('list-articles');
		$tpl->assign(array(
			'articles-title' => $category->title,
			'articles-catid' => $category->id
		));

		foreach ($articles as $article) {
			if (!$article->nick) {
				$article->nick = 'Nezināms';
				$article->level = 0;
			}
			$tpl->newBlock('list-articles-node');

			$date = display_time(strtotime($article->date));

			if (!empty($article->intro)) {
				$article->text = $article->intro;
			} else {
				$article->text = textlimit(strip_tags(trim(str_replace('<li>', ' • ', str_replace(array('&nbsp;', '<br />'), ' ', $article->text)))), 680);
				$article->text = preg_replace("#(^|[\n ]|<a(.*?)>)http://(www\.)?youtube\.com/watch\?v=([a-zA-Z0-9\-_]+)(</a>)?#im", '<div class="video-thb"><a href="/?p=' . $article->id . '"><img src="http://img.youtube.com/vi/$4/2.jpg" alt="" /></a><p>Youtube video</p><div class="c"></div></div>', $article->text, 1);
				$videoid = get_between($article->text, 'img.youtube.com/vi/', '/2.jpg"');
				if ($videoid) {
					$contents = file_get_contents('http://gdata.youtube.com/feeds/api/videos/' . $videoid);
					$v_description = '<strong><a href="/?p=' . $article->id . '">' . get_between($contents, "<media:title type='plain'>", '</media:title>') . '</a></strong><br />';
					$v_description .= textlimit(get_between($contents, "<media:description type='plain'>", '</media:description>'), 270);
					$article->text = str_replace('<p>Youtube video</p>', '<p>' . $v_description . '</p>', $article->text);
				}
				$article->intro = sanitize($article->text);
				$db->query("UPDATE pages SET intro = '$article->intro' WHERE id = '$article->id' LIMIT 1");
			}

			$cat = $db->get_var("SELECT title FROM cat WHERE id = '$article->category'");

			$tpl->assign(array(
				'cat' => $cat,
				'articles-node-id' => $article->id,
				'node-url' => mkurl('page', $article->id, $article->title),
				'aurl' => mkurl('user', $article->author, $article->nick),
				'articles-node-title' => $article->title,
				'articles-node-views' => $article->views,
				'articles-node-date' => $date,
				'articles-node-author' => usercolor($article->nick, $article->level),
				'articles-node-posts' => $article->posts,
				'articles-node-intro' => textlimit($article->text, 140, '...')
			));
			if ($article->avatar) {
				$tpl->newBlock('list-articles-node-avatar');
				$tpl->assign(array(
					'node-avatar-image' => trim($article->avatar),
					'node-avatar-alt' => trim(htmlspecialchars($article->title))
				));
			}
		}
	}
} else {
	redirect();
}
