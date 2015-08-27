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
		`pages`.`intro` AS `intro`
	FROM
		`pages`
	WHERE
		`pages`.`category` IN (11,80,323,565,611,651)
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

		$user = get_user($article->author);

		$tpl->newBlock('list-node');

		$date = display_time(strtotime($article->date));

		if (!empty($article->intro)) {
			$article->text = $article->intro;
		} else {
			$article->text = textlimit(strip_tags(trim(str_replace('<li>', ' • ', str_replace(array('&nbsp;', '<br />'), ' ', youtube_title($article->text))))), 680);
			$article->intro = sanitize($article->text);
			$db->query("UPDATE pages SET intro = '$article->intro' WHERE id = '$article->id' LIMIT 1");
		}

		$cat = get_cat($article->category);

		if (!$user->deleted) {
			$author_link = '<a rel="author" href="/user/' . $user->id . '" rel="author">' . usercolor($user->nick, $user->level, false, $user->id) . '</a>';
		} else {
			$author_link = '<em>dzēsts</em>';
		}

		$tpl->assign(array(
			'cat' => $cat->title,
			'cat-strid' => $cat->textid,
			'articles-node-id' => $article->id,
			'url' => '/read/' . $article->strid,
			'aurl' => '/user/' . $user->id,
			'title' => $article->title,
			'views' => $article->views,
			'date' => $date,
			'author' => $author_link,
			'posts' => $article->posts,
			'intro' => $article->text,
			'avatar' => get_avatar($user, 's')
		));
		if ($article->avatar) {
			$tpl->newBlock('list-avatar');
			$tpl->assign(array(
				'image' => trim($article->avatar),
				'alt' => trim(h($article->title))
			));
		}
	}
}

unset($pagepath);

