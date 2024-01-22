<?php

/**
 * Sākumlapas jaunumi
 */
if (isset($_GET['skip'])) {
	$skip = (int) $_GET['skip'];
} else {
	$skip = 0;
}

$end = 5;

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
			`pages`.`avatar` AS `avatar`,
			`pages`.`image` AS `image`,
			`pages`.`intro` AS `intro`,
			`pages`.`attach` AS `attach`
		FROM
			`pages`
		WHERE
			`pages`.`category` = '1'
		ORDER BY
			`pages`.`attach` DESC,
			`pages`.`id` DESC
		LIMIT
			$skip,$end");
			
if(!empty($articles)) {

	foreach ($articles as $article) {
		$tpl->newBlock('index-news-node');
		if (!empty($article->intro)) {
			$article->text = $article->intro;
		} else {
			$article->text = textlimit(strip_tags(trim(str_replace(['&nbsp;', '<br>', '<li>'], ' ', youtube_title($article->text)))), 680);
			$article->intro = sanitize($article->text);
			$db->query("UPDATE pages SET intro = '$article->intro' WHERE id = '$article->id' LIMIT 1");
		}
		
		$class = '';
		
		if($article->attach) {
			$class = 'sticky';
		}
		
		$user = get_user($article->author);
		
		if (!$user->deleted) {
			$author_link = '<a href="/user/' . $user->id . '" rel="author" title="Autora profils">' . usercolor($user->nick, $user->level, false, $user->id) . '</a>';
		} else {
			$author_link = '<em>dzēsts</em>';
		}

		$date = display_time(strtotime($article->date), false);
		$tpl->assign([
			'url' => '/read/' . $article->strid,
			'aurl' => '/user/' . $user->id,
			'title' => $article->title,
			'date' => $date,
			'author' => $author_link,
			'posts' => $article->posts,
			'intro' => textlimit($article->text, 350),
			'class' => $class,
			'avatar' => get_avatar($user, 's')
		]);

		if (!empty($article->image)) {
			$tpl->newBlock('news-image');
			$tpl->assign([
				'url' => '/read/' . $article->strid,
				'title' => $article->title,
				'image' => trim($article->image)
			]);
		} elseif (!empty($article->avatar)) {
			$tpl->newBlock('news-av');
			$tpl->assign([
				'url' => '/read/' . $article->strid,
				'title' => $article->title,
				'image' => trim($article->avatar)
			]);
		}
		
	}

}

//pager
$pager = pager($category->stat_topics, $skip, $end, '/?skip=');
$tpl->assignGlobal([
	'pager-next' => $pager['next'],
	'pager-prev' => $pager['prev'],
	'pager-numeric' => $pager['pages']
]);

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
		`users`.`id` = `pages`.`author` AND
		`pages`.`lang` = 1
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
		$av = '<a href="/read/' . $article->strid . '"><img style="width:64px;height:64px" class="av index-av" src="'.$img_server . '/' . $article->avatar . '" alt="' . htmlspecialchars($article->title) . '" /></a>';
	}

	$title = textlimit($article->title, 50, '...');

	$len = 90;
	if(strlen($title) > 28) {
		$len = 58;
	}

	$tpl->assign([
		'node-url' => '/read/' . $article->strid,
		'title' => $title,
		'date' => $date,
		'intro' => trim_intro($article->text, $len),
		'av' => $av
	]);
}

$list_cats = [
	'games' => 81,
	'movies' => 80,
	'music' => 323
];

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
			0,2");

	foreach ($articles as $article) {

		$tpl->newBlock('index-' . $cat_type . '-node');

		if (!empty($article->intro)) {
			$article->text = $article->intro;
		} else {
			$article->text = textlimit(strip_tags(trim(str_replace('<li>', ' • ', str_replace(['&nbsp;', '<br>'], ' ', add_smile($article->text))))), 600);
		}

		$av = '';
		if (!empty($article->avatar)) {
			$av = '<a href="/read/' . $article->strid . '"><img style="width:64px;height:64px" class="av index-av" src="'.$img_server . '/' . $article->avatar . '" alt="' . htmlspecialchars($article->title) . '" /></a>';
		}

		$title = textlimit($article->title, 50, '...');

		$len = 90;
		if(strlen($title) > 28) {
			$len = 58;
		}

		$tpl->assign([
			'node-url' => '/read/' . $article->strid,
			'title' => $title,
			'date' => $date,
			'intro' => trim_intro($article->text, $len),
			'av' => $av
		]);
	}
}

$tpl->assignGlobal('index-log', get_index_events());
$tpl->assignGlobal('newsactive', ' class="active"');

