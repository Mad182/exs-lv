<?php

/**
 * Sākumlapas jaunumi
 */
if (isset($_GET['skip'])) {
	$skip = (int) $_GET['skip'];
} else {
	$skip = 0;
}

$end = 10;

$date = display_time(time());

$tpl->newBlock('news');
$tpl->newBlock('cindex-list');

$articles = $db->get_results("
		SELECT
			`pages`.`id` AS `id`,
			`pages`.`title` AS `title`,
			`pages`.`date` AS `date`,
			`pages`.`author` AS `author`,
			`pages`.`strid` AS `strid`,
			`pages`.`posts` AS `posts`,
			`pages`.`text` AS `text`,
			`pages`.`sm_avatar` AS `sm_avatar`,
			`pages`.`intro` AS `intro`,
			`users`.`nick` AS `nick`,
			`users`.`level` AS `level`
		FROM
			`pages`,
			`users`
		WHERE
			`pages`.`category` = '1' AND
			`users`.`id` = `pages`.`author`
		ORDER BY
			`pages`.`id` DESC
		LIMIT
			$skip,$end");
			
if(!empty($articles)) {

	foreach ($articles as $article) {
		$tpl->newBlock('index-news-node');
		if (!empty($article->intro)) {
			$article->text = $article->intro;
		} else {
			$article->text = textlimit(strip_tags(trim(str_replace(array('&nbsp;', '<br />', '<li>'), ' ', youtube_title($article->text)))), 680);
			$article->intro = sanitize($article->text);
			$db->query("UPDATE pages SET intro = '$article->intro' WHERE id = '$article->id' LIMIT 1");
		}

		if ($article->sm_avatar == '') {
			$article->sm_avatar = '/dati/bildes/useravatar/none.png';
		}

		$date = display_time(strtotime($article->date));
		$tpl->assign(array(
			'node-url' => '/read/' . $article->strid,
			'aurl' => '/user/' . $article->author,
			'title' => $article->title,
			'date' => $date,
			'author' => usercolor($article->nick, $article->level, false, $article->author),
			'posts' => $article->posts,
			'level' => $article->level,
			'intro' => textlimit($article->text, 330),
			'avatar' => trim($article->sm_avatar)
		));
	}

}

//pager
$pager = pager($category->stat_topics, $skip, $end, '/?skip=');
$tpl->assignGlobal(array(
	'pager-next' => $pager['next'],
	'pager-prev' => $pager['prev'],
	'pager-numeric' => $pager['pages']
));

/**
 * Labā kolonna
 */
$tpl->newBlock('cindex-right');

$articles = $db->get_results("
	SELECT
		`pages`.`id` AS `id`,
		`pages`.`title` AS `title`,
		`pages`.`text` AS `text`,
		`pages`.`intro` AS `intro`,
		`pages`.`strid` AS `strid`,
		`pages`.`sm_avatar` AS `avatar`,
		`users`.`nick` AS `nick`,
		`users`.`level` AS `level`
	FROM
		`cat`,
		`pages`,
		`users`
	WHERE
		`pages`.`category` = `cat`.`id` AND
		`cat`.`isblog`!='0' AND
		`users`.`id` = `pages`.`author`
	ORDER BY
		`pages`.`id` DESC
	LIMIT
		0,3");

foreach ($articles as $article) {

	$tpl->newBlock('index-blogs-node');

	if (!empty($article->intro)) {
		$article->text = $article->intro;
	} else {
		$article->text = trim_intro($article->text, 600);
	}

	$av = '';
	if (!empty($article->avatar)) {
		$av = '<a href="/read/' . $article->strid . '"><img style="width:64px; height: 64px;" class="av index-av" src="//img.exs.lv/' . $article->avatar . '" alt="' . htmlspecialchars($article->title) . '" /></a>';
	}

	$tpl->assign(array(
		'node-url' => '/read/' . $article->strid,
		'title' => textlimit($article->title, 26, '...'),
		'date' => $date,
		'intro' => trim_intro($article->text),
		'av' => $av
	));
}

$list_cats = array(
	'games' => 81,
	'movies' => 80,
	'music' => 323
);

foreach ($list_cats as $cat_type => $cat_id) {

	$articles = $db->get_results("
		SELECT
			`pages`.`id` AS `id`,
			`pages`.`title` AS `title`,
			`pages`.`sm_avatar` AS `avatar`,
			`pages`.`text` AS `text`,
			`pages`.`strid` AS `strid`,
			`pages`.`intro` AS `intro`,
			`users`.`nick` AS `nick`,
			`users`.`level` AS `level`
		FROM
			`pages`,
			`users`
		WHERE
			`pages`.`category` = " . $cat_id . " AND
			`users`.`id` = `pages`.`author`
		ORDER BY
			`pages`.`id` DESC
		LIMIT
			0,3");

	foreach ($articles as $article) {

		$tpl->newBlock('index-' . $cat_type . '-node');

		if (!empty($article->intro)) {
			$article->text = $article->intro;
		} else {
			$article->text = textlimit(strip_tags(trim(str_replace('<li>', ' • ', str_replace(array('&nbsp;', '<br />'), ' ', add_smile($article->text))))), 600);
		}

		$av = '';
		if (!empty($article->avatar)) {
			$av = '<a href="/read/' . $article->strid . '"><img style="width:64px; height: 64px;" class="av index-av" src="//img.exs.lv/' . $article->avatar . '" alt="' . htmlspecialchars($article->title) . '" /></a>';
		}

		$tpl->assign(array(
			'node-url' => '/read/' . $article->strid,
			'title' => $article->title,
			'date' => $date,
			'intro' => trim_intro($article->text),
			'av' => $av
		));
	}
}

$tpl->assignGlobal('index-log', get_index_events());
$tpl->assignGlobal('newsactive', ' class="active"');

