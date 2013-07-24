<?php

if (!isset($_GET['viewcat']) || $_GET['viewcat'] !== $category->textid) {
	redirect('/' . $category->textid, true);
}

if (isset($_GET['skip'])) {
	$skip = (int) $_GET['skip'];
} else {
	$skip = 0;
}

$end = 10;

$pagepath = $category->title;
if ($category->parent) {
	$category2 = get_cat($category->parent);
	$pagepath = '<a href="/' . $category2->textid . '">' . $category2->title . '</a> / ' . $pagepath;
}

$articles = $db->get_results("
	SELECT
		`pages`.`id` AS `id`,
		`pages`.`title` AS `title`,
		`pages`.`date` AS `date`,
		`pages`.`strid` AS `strid`,
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
		`pages`.`category` IN (10,11,323,565,611,651) AND
		`users`.`id` = `pages`.`author`
	ORDER BY
		`pages`.`date` DESC
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
			$article->text = textlimit(strip_tags(trim(str_replace('<li>', ' • ', str_replace(array('&nbsp;', '<br />'), ' ', youtube_title($article->text))))), 680);
			$article->intro = sanitize($article->text);
			$db->query("UPDATE pages SET intro = '$article->intro' WHERE id = '$article->id' LIMIT 1");
		}

		$tpl->assign(array(
			'cat' => get_cat($article->category)->title,
			'articles-node-id' => $article->id,
			'node-url' => '/read/' . $article->strid,
			'aurl' => mkurl('user', $article->author, $article->nick),
			'articles-node-title' => $article->title,
			'articles-node-views' => $article->views,
			'articles-node-date' => $date,
			'articles-node-author' => usercolor($article->nick, $article->level),
			'articles-node-posts' => $article->posts,
			'articles-node-intro' => $article->text
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
