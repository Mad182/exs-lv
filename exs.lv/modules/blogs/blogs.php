<?php

/**
 * Jaunākie ieraksti blogos
 */
$tpl->newBlock('blogs-body');

$articles = $db->get_results(
		"SELECT
	`pages`.`title` AS `title`,
	`pages`.`intro` AS `intro`,
	`pages`.`text` AS `text`,
	`pages`.`strid` AS `strid`,
	`pages`.`author` AS `authorid`,
	`users`.`avatar` AS `avatar`,
	`users`.`nick` AS `nick`,
	`pages`.`id` AS `id`
FROM
	`pages`,
	`users`,
	`cat`
WHERE
	`pages`.`category` = `cat`.`id` AND
	`pages`.`lang` = $lang AND
	`cat`.`isblog` != 0 AND
	`users`.`id` = `pages`.`author`
ORDER BY
	`pages`.`date` DESC
LIMIT 15");

if ($articles) {
	foreach ($articles as $article) {
		$tpl->newBlock('blogs-featured');
		if ($article->avatar == '') {
			$article->avatar = 'none.png';
		}
		$article->avatar = '/dati/bildes/useravatar/' . $article->avatar;

		if (!empty($article->intro)) {
			$article->text = $article->intro;
		} else {
			$article->text = textlimit(strip_tags(trim(str_replace(['&nbsp;', '<br />'], ' ', youtube_title($article->text)))), 680);
			$article->intro = sanitize($article->text);
			$db->query("UPDATE pages SET intro = '$article->intro' WHERE id = '$article->id' LIMIT 1");
		}

		$tpl->assign([
			'newest-title' => textlimit($article->title, 52),
			'newest-text' => $article->text,
			'url' => '/read/' . $article->strid,
			'aurl' => '/user/' . $article->authorid,
			'newest-author-id' => $article->authorid,
			'newest-author-avatar' => $article->avatar,
			'newest-author-title' => h($article->nick),
		]);
	}
}

unset($pagepath);

