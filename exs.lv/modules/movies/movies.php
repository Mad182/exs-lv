<?php

if (!isset($_GET['viewcat']) || $_GET['viewcat'] !== $category->textid) {
	redirect('/' . $category->textid, true);
}

if (!isset($_GET['var1']) || $_GET['var1'] != 'search') {

	$skip = 0;
	if (isset($_GET['skip'])) {
		$skip = (int) $_GET['skip'];
	}

	$end = 10;
	$sortby = "`pages`.`date` DESC";

	$pagepath = '';


	$articles = $db->get_results("SELECT
		`pages`.`id` AS `id`,
		`pages`.`title` AS `title`,
		`pages`.`strid` AS `strid`,
		`pages`.`date` AS `date`,
		`pages`.`author` AS `author`,
		`pages`.`posts` AS `posts`,
		`pages`.`closed` AS `closed`,
		`pages`.`text` AS `text`,
		`pages`.`avatar` AS `avatar`,
		`pages`.`readby` AS `readby`,
		`pages`.`views` AS `views`,
		`pages`.`intro` AS `intro`,
		`users`.`nick` AS `nick`,
		`users`.`level` AS `level`
	FROM
		`pages`,
		`users`
	WHERE
		`pages`.`category` = " . $category->id . " AND
		`pages`.`lang` = $lang AND
		`users`.`id` = `pages`.`author`
	ORDER BY
		" . $sortby . "
	LIMIT
		$skip,$end");


	if ($skip) {
		$page_title = $page_title . ' - lapa ' . ($skip / $end + 1);
	}

	$tpl->newBlock('list-articles');
	$tpl->assign([
		'title' => $category->title,
		'strid' => $category->textid
	]);

	foreach ($articles as $article) {
		if (!$article->nick) {
			$article->nick = 'Nezināms';
			$article->level = 0;
		}
		$tpl->newBlock('list');

		$date = display_time(strtotime($article->date));

		if (!empty($article->intro)) {
			$article->text = $article->intro;
		} else {
			$article->text = textlimit(strip_tags(trim(str_replace('<li>', ' • ', str_replace(['&nbsp;', '<br />'], ' ', add_smile($article->text))))), 500);
			$article->intro = sanitize($article->text);
			$db->query("UPDATE `pages` SET `intro` = '$article->intro' WHERE `id` = '$article->id' LIMIT 1");
		}
		
		$user = get_user($article->author);
		$avatar = get_avatar($user, 's');

		$tpl->assign([
			'id' => $article->id,
			'node-url' => '/read/' . $article->strid,
			'author-id' => $article->author,
			'title' => $article->title,
			'views' => $article->views,
			'date' => $date,
			'author' => usercolor($article->nick, $article->level),
			'posts' => $article->posts,
			'level' => $article->level,
			'intro' => $article->text,
			'avatar' => $avatar
		]);


		if ($movie_data = $db->get_row("SELECT * FROM  `movie_data` WHERE `page_id` = '$article->id' LIMIT 1")) {
			if (!empty($movie_data->title_lv)) {
				$tpl->assign('title-lv', ' &nbsp;<small>' . $movie_data->title_lv . '</small>');
			}

			if (!empty($movie_data->year)) {
				$tpl->assign('year', '<strong>Gads:</strong> ' . $movie_data->year . '<br />');
			}

			if (!empty($movie_data->runtime)) {
				$tpl->assign('runtime', '<strong>Garums:</strong> ' . $movie_data->runtime . ' minūtes<br />');
			}

			if (!empty($movie_data->type) && $movie_data->type == 'series') {
				$tpl->assign('title-prefix', '<span class="title-prefix series">Seriāls</span> ');
			}
		}


		if ($genres = $db->get_col("SELECT `genre` FROM `movie_genres` WHERE `page_id` = '$article->id'")) {
			$gen = [];
			foreach ($genres as $genre) {
				$gen[] = '<a href="/filmas/search?genre=' . $genre . '">' . translate_genres($genre) . '</a>';
			}
			$tpl->assign('genres', '<strong>Žanrs:</strong> ' . implode(' / ', $gen) . '<br />');
		}


		if ($avatar = $db->get_row("SELECT * FROM  `movie_images` WHERE `main` = 1 AND `page_id` = '$article->id' LIMIT 1")) {
			$tpl->newBlock('list-avatar');
			$tpl->assign([
				'image' => $avatar->thb,
				'alt' => h($avatar->title)
			]);
		}
	}


	$pager = pager($db->get_var("SELECT count(*) FROM `pages` WHERE `category` = '$category->id'"), $skip, $end, '/' . $category->textid . '/?skip=');
	$tpl->assignGlobal([
		'pager-next' => $pager['next'],
		'pager-prev' => $pager['prev'],
		'pager-numeric' => $pager['pages']
	]);
} else {

	$sortby = "`pages`.`date` DESC";

	$pagepath = '';

	$genreq = '';

	$page_title = 'Filmu meklētājs';

	if (isset($_GET['genre'])) {
		$_GET['genres'] = [$_GET['genre']];

		if (translate_genres($_GET['genre']) != $_GET['genre']) {
			$page_title = translate_genres($_GET['genre']) . ' - filmu meklēšana';
		}
	}

	if (!empty($_GET['genres'])) {
		$genres = [];
		foreach ($_GET['genres'] as $genre) {
			$genres[] = "'" . sanitize(h(trim($genre))) . "'";
		}
		$pages = $db->get_col("SELECT DISTINCT(`page_id`) FROM `movie_genres` WHERE `genre` IN(" . implode(',', $genres) . ")");
		$genreq = " AND `pages`.`id` IN(" . implode(',', $pages) . ") ";
	}



	$articles = $db->get_results("SELECT
		`pages`.`id` AS `id`,
		`pages`.`title` AS `title`,
		`pages`.`strid` AS `strid`,
		`pages`.`date` AS `date`,
		`pages`.`author` AS `author`,
		`pages`.`posts` AS `posts`,
		`pages`.`closed` AS `closed`,
		`pages`.`avatar` AS `avatar`,
		`pages`.`readby` AS `readby`,
		`pages`.`views` AS `views`,
		`pages`.`intro` AS `intro`,
		`users`.`nick` AS `nick`,
		`users`.`level` AS `level`
	FROM
		`pages`,
		`users`
	WHERE
		`pages`.`category` = " . $category->id . " AND
		`pages`.`lang` = $lang AND
		`users`.`id` = `pages`.`author`
		$genreq
	ORDER BY
		" . $sortby . "
	LIMIT
		100");

	$tpl->newBlock('list-search');
	$tpl->assign([
		'title' => h($page_title),
		'strid' => $category->textid
	]);

	foreach ($articles as $article) {
		if (!$article->nick) {
			$article->nick = 'Nezināms';
			$article->level = 0;
		}
		$tpl->newBlock('movie');

		$date = display_time(strtotime($article->date));

		$tpl->assign([
			'id' => $article->id,
			'node-url' => '/read/' . $article->strid,
			'author-id' => $article->author,
			'title' => $article->title,
			'views' => $article->views,
			'date' => $date,
			'author' => usercolor($article->nick, $article->level),
			'posts' => $article->posts,
			'level' => $article->level
		]);


		if ($movie_data = $db->get_row("SELECT * FROM  `movie_data` WHERE `page_id` = '$article->id' LIMIT 1")) {
			
		}


		if ($avatar = $db->get_row("SELECT * FROM  `movie_images` WHERE `main` = 1 AND `page_id` = '$article->id' LIMIT 1")) {
			$tpl->assign([
				'image' => $avatar->thb,
				'alt' => h($avatar->title)
			]);
		}
	}
}
