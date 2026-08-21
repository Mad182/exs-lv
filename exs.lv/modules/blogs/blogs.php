<?php

/**
 * Jaunākie ieraksti blogos
 */

if (isset($_GET['skip'])) {
	$skip = (int) $_GET['skip'];
	if ($skip < 0) {
		$skip = 0;
	}
} else {
	$skip = 0;
}
$end = 15;

$tpl->newBlock('blogs-body');

$total = (int) $db->get_var(
	"SELECT COUNT(*)
	FROM `pages`, `users`, `cat`
	WHERE `pages`.`category` = `cat`.`id` AND
		`pages`.`lang` = $lang AND
		`cat`.`isblog` != 0 AND
		`users`.`id` = `pages`.`author`"
);

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
LIMIT $skip, $end"
);

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
			$article->text = textlimit(strip_tags(trim(str_replace(['&nbsp;', '<br>'], ' ', youtube_title($article->text)))), 680);
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

$pager = pager($total, $skip, $end, '/blogs?skip=');

$tpl->assignGlobal([
	'pager-next' => $pager['next'],
	'pager-prev' => $pager['prev'],
	'pager-numeric' => $pager['pages']
]);

unset($pagepath);
