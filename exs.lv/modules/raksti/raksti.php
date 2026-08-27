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
		`users`.`level` AS `level`,
		`users`.`deleted` AS `author_deleted`,
		`users`.`avatar` AS `user_avatar`
	FROM
		`pages`
	LEFT JOIN
		`users` ON `users`.`id` = `pages`.`author`
	WHERE
		`pages`.`category` IN (11,80,323,565,611,651)
	ORDER BY
		`pages`.`date` DESC
	LIMIT
		$skip,$end");

if ($articles) {

	if ($skip) {
		$page_title = $page_title . ' - lapa ' . ($skip / $end + 1);
	}

	$tpl->newBlock('list-articles');
	$tpl->assign([
		'articles-title' => $category->title,
		'articles-catid' => $category->id
	]);

	// Pre-fetch category titles for the article categories
	$article_cats = [];
	$cat_rows = $db->get_results("SELECT `id`, `title`, `textid` FROM `cat` WHERE `id` IN (11,80,323,565,611,651)");
	if (!empty($cat_rows)) {
		foreach ($cat_rows as $cr) {
			$article_cats[$cr->id] = $cr;
		}
	}

	foreach ($articles as $article) {

		$tpl->newBlock('list-node');

		$date = display_time(strtotime($article->date));

		if (!empty($article->intro)) {
			$article->text = $article->intro;
		} else {
			$article->text = textlimit(strip_tags(trim(str_replace('<li>', ' • ', str_replace(['&nbsp;', '<br>'], ' ', youtube_title($article->text))))), 680);
			$article->intro = sanitize($article->text);
			$db->query("UPDATE `pages` SET `intro` = '$article->intro' WHERE `id` = '$article->id' LIMIT 1");
		}

		$cat = $article_cats[$article->category] ?? get_cat($article->category);
		$cat_title = $cat ? $cat->title : '';
		$cat_textid = $cat ? $cat->textid : '';

		if (!$article->author_deleted && !empty($article->nick)) {
			$author_link = '<a href="/user/' . $article->author . '" rel="author">' . usercolor($article->nick, $article->level, false, $article->author) . '</a>';
		} else {
			$author_link = '<em>dzēsts</em>';
		}

		$user_avatar = !empty($article->user_avatar) ? $article->user_avatar : 'none.png';

		$tpl->assign([
			'cat' => $cat_title,
			'cat-strid' => $cat_textid,
			'url' => '/read/' . $article->strid,
			'aurl' => '/user/' . $article->author,
			'title' => $article->title,
			'views' => $article->views,
			'date' => $date,
			'author' => $author_link,
			'posts' => $article->posts,
			'intro' => $article->text,
			'avatar' => '/bildes/avatari/s_' . $user_avatar
		]);
		if ($article->avatar) {
			$tpl->newBlock('list-avatar');
			$tpl->assign([
				'image' => trim($article->avatar),
				'alt' => trim(h($article->title))
			]);
		}
	}
}

unset($pagepath);
