<?php

if ($auth->ok) {
	set_action('sākumlapu');
}

if (isset($_GET['skip'])) {
	$skip = (int) $_GET['skip'];
} else {
	$skip = 0;
}

$end = 10;

$bumps = $db->get_results("SELECT `id`, `thb`, `title`, `posts` FROM `junk` WHERE `removed` = 0 ORDER BY `bump` DESC LIMIT 8");
if ($bumps) {
	$tpl->newBlock('index-junk');
	foreach ($bumps as $junk) {
		$tpl->newBlock('index-junk-node');
		$tpl->assign(array(
			'id' => $junk->id,
			'thb' => $junk->thb,
			'title' => $junk->title,
			'posts' => $junk->posts
		));
	}
}

if (!file_exists('cache/index/' . $lang . '_' . $skip . '.html')) {

	$tpl_cachable = new TemplatePower('modules/index/index-cachable.tpl');
	$tpl_cachable->prepare();

	$tpl_cachable->newBlock('cindex-list');

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
				`pages`.`sm_avatar` AS `sm_avatar`,
				`pages`.`intro` AS `intro`,
				`users`.`nick` AS `nick`,
				`users`.`level` AS `level`
			FROM
				`pages`,
				`users`
			WHERE
				`pages`.`category` = '1' AND
				`pages`.`lang` = '$lang' AND
				`users`.`id` = `pages`.`author`
			ORDER BY
				`pages`.`date` DESC
			LIMIT
				$skip,$end");

	foreach ($articles as $article) {
		$tpl_cachable->newBlock('index-news-node');
		if (!empty($article->intro)) {
			$article->text = $article->intro;
		} else {
			$article->text = textlimit(strip_tags(trim(str_replace('<li>', ' • ', str_replace(array('&nbsp;', '<br />'), ' ', $article->text)))), 680);
			$article->text = preg_replace("#(^|[\n ]|<a(.*?)>)http://(www\.)?youtube\.com/watch\?v=([a-zA-Z0-9\-_]+)(</a>)?#im", '<div class="video-thb"><a href="/read/' . $article->strid . '"><img src="http://img.youtube.com/vi/$4/2.jpg" alt="" /></a><p>Youtube video</p><div class="c"></div></div>', $article->text, 1);
			$videoid = get_between($article->text, 'img.youtube.com/vi/', '/2.jpg"');
			if ($videoid) {
				$contents = file_get_contents('http://gdata.youtube.com/feeds/api/videos/' . $videoid);
				$v_description = '<strong><a href="/read/' . $article->strid . '">' . get_between($contents, "<media:title type='plain'>", '</media:title>') . '</a></strong><br />';
				$v_description .= textlimit(get_between($contents, "<media:description type='plain'>", '</media:description>'), 270);
				$article->text = str_replace('<p>Youtube video</p>', '<p>' . $v_description . '</p>', $article->text);
			}
			$article->intro = sanitize($article->text);
			$db->query("UPDATE pages SET intro = '$article->intro' WHERE id = '$article->id' LIMIT 1");
		}

		if ($article->avatar == '') {
			$article->avatar = '/dati/bildes/useravatar/none.png';
		}

		$date = display_time(strtotime($article->date));
		$tpl_cachable->assign(array(
			'node-url' => '/read/' . $article->strid,
			'aurl' => '/user/' . $article->author,
			'title' => $article->title,
			'date' => $date,
			'author' => usercolor($article->nick, $article->level, false, $article->author),
			'posts' => $article->posts,
			'level' => $article->level,
			'intro' => textlimit($article->text, 240),
			'node-avatar-image' => trim($article->avatar)
		));
	}

	//pager
	$pager = pager($category->stat_topics, $skip, $end, '/?skip=');
	$tpl_cachable->assignGlobal(array(
		'pager-next' => $pager['next'],
		'pager-prev' => $pager['prev'],
		'pager-numeric' => $pager['pages']
	));

	$cache_handle = fopen('cache/index/' . $lang . '_' . $skip . '.html', 'wb');
	fwrite($cache_handle, $tpl_cachable->getOutputContent());
	fclose($cache_handle);

	if (!file_exists('cache/index/right.html')) {

		$tpl_cachable = new TemplatePower('modules/index/index-side.tpl');
		$tpl_cachable->prepare();

		$tpl_cachable->newBlock('cindex-right');


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
				0,4");

		foreach ($articles as $article) {

			$tpl_cachable->newBlock('index-blogs-node');

			if (!empty($article->intro)) {
				$article->text = $article->intro;
			} else {
				$article->text = textlimit(strip_tags(trim(str_replace('<li>', ' • ', str_replace(array('&nbsp;', '<br />'), ' ', add_smile($article->text))))), 600);
			}

			$av = '';
			if(!empty($article->avatar)) {
				$av = '<a href="/read/' . $article->strid . '" class="av"><img src="/'.$article->avatar.'" alt="'.htmlspecialchars($article->title).'" /></a>';
			}

			$tpl_cachable->assign(array(
				'node-url' => '/read/' . $article->strid,
				'title' => textlimit($article->title, 26, '...'),
				'date' => $date,
				'intro' => textlimit(strip_tags(trim(str_replace(array('Spēles nosaukums:', '&nbsp;'), ' ', $article->text))), 90),
				'av' => $av
			));
		}

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
				`pages`.`category` = '81' AND
				`users`.`id` = `pages`.`author`
			ORDER BY
				`pages`.`id` DESC
			LIMIT
				0,4");

		foreach ($articles as $article) {

			$tpl_cachable->newBlock('index-games-node');

			if (!empty($article->intro)) {
				$article->text = $article->intro;
			} else {
				$article->text = textlimit(strip_tags(trim(str_replace('<li>', ' • ', str_replace(array('&nbsp;', '<br />'), ' ', add_smile($article->text))))), 600);
			}

			$av = '';
			if(!empty($article->avatar)) {
				$av = '<a href="/read/' . $article->strid . '" class="av"><img src="/'.$article->avatar.'" alt="'.htmlspecialchars($article->title).'" /></a>';
			}

			$tpl_cachable->assign(array(
				'node-url' => '/read/' . $article->strid,
				'title' => $article->title,
				'date' => $date,
				'intro' => textlimit(strip_tags(trim(str_replace(array('Spēles nosaukums:', '&nbsp;'), ' ', $article->text))), 90),
				'av' => $av
			));
		}

		$articles = $db->get_results("
			SELECT
				`pages`.`id` AS `id`,
				`pages`.`title` AS `title`,
				`pages`.`strid` AS `strid`,
				`pages`.`text` AS `text`,
				`pages`.`sm_avatar` AS `avatar`,
				`pages`.`intro` AS `intro`,
				`users`.`nick` AS `nick`,
				`users`.`level` AS `level`
			FROM
				`pages`,
				`users`
			WHERE
				(`pages`.`category` = '80' OR
				`pages`.`category` = '323') AND
				`users`.`id` = `pages`.`author`
			ORDER BY
				`pages`.`id` DESC
			LIMIT
				0,5");

		foreach ($articles as $article) {

			$tpl_cachable->newBlock('index-movies-node');

			if (!empty($article->intro)) {
				$article->text = $article->intro;
			} else {
				$article->text = textlimit(strip_tags(trim(str_replace('<li>', ' • ', str_replace(array('&nbsp;', '<br />'), ' ', add_smile($article->text))))), 600);
			}

			$av = '';
			if(!empty($article->avatar)) {
				$av = '<a href="/read/' . $article->strid . '" class="av"><img src="/'.$article->avatar.'" alt="'.htmlspecialchars($article->title).'" /></a>';
			}

			$tpl_cachable->assign(array(
				'node-url' => '/read/' . $article->strid,
				'title' => $article->title,
				'date' => $date,
				'intro' => textlimit(strip_tags(trim(str_replace(array('Oriģinālnosaukums:', 'Nosaukums:', '&nbsp;'), ' ', $article->text))), 90),
				'av' => $av
			));


		}

		file_put_contents('cache/index/right.html', $tpl_cachable->getOutputContent());
	}
}

$tpl->assignGlobal('index-cachable', file_get_contents('cache/index/' . $lang . '_' . $skip . '.html'));
$tpl->assignGlobal('index-right', file_get_contents('cache/index/right.html'));
$tpl->assignGlobal('index-log', get_index_events());

$tpl->newBlock('meta-description');
$tpl->assign('description', 'Exs.lv ir spēļu un izklaides portāls, kur jautri pavadīt laiku apspriežot datorspēles, filmas, mūziku, uzspēlēt flash spēles un atrast domubiedrus');

$pagepath = '';
