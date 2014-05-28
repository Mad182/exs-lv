<?php

/**
 *  Kādreizējā sākumlapa
 */
if (!isset($sub_include)) {
	die('No hacking, pls.');
}

/* --------------------------------------- */
//	 Sākumlapa
/* --------------------------------------- */ else if (isset($_GET['viewcat']) && $_GET['viewcat'] == 'runescape'/* && in_array($auth->id,array(21018,115)) */) {

	$tpl_options = 'no-left';
	$tpl->newBlock('runescape-mainpage');

	$total = $db->get_var("SELECT count(*) FROM `pages` WHERE category = ('" . $category->id . "')");
	$page_count = ceil($total / 15);

	$current_page = (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 && $_GET['page'] <= $page_count) ? (int) $_GET['page'] : 1;
	$skip = 15 * ($current_page - 1);
	$end = 15;

	$articles = $db->get_results("SELECT
  		`pages`.`id` AS `id`,
  		`pages`.`strid` AS `strid`,
  		`pages`.`title` AS `title`,
  		`pages`.`date` AS `date`,
  		`pages`.`author` AS `author`,
  		`pages`.`text` AS `text`,
  		`pages`.`posts` AS `posts`,
  		`pages`.`avatar` AS `avatar`,
  		`pages`.`views` AS `views`,
  		`pages`.`intro` AS `intro`,
  		`users`.`nick` AS `nick`,
  		`users`.`level` AS `level`
  	FROM `pages`,`users` WHERE `pages`.`category` IN (599) AND `users`.`id` = `pages`.`author`
    ORDER BY `pages`.`date` DESC LIMIT $skip,$end ");

	//if($skip) {$page_title =	$page_title . ' - lapa ' . ($skip/$end+1);}

	if ($articles) {
		$tpl->newBlock('rsarticles');

		foreach ($articles as $article) {
			if (!$article->nick) {
				$article->nick = 'Nezināms';
				$article->level = 0;
			}

			$article->date = display_time(strtotime($article->date));
			if (empty($article->intro)) {
				$article->intro = sanitize($article->text);
				$db->query("UPDATE pages SET intro = '$article->intro' WHERE id = '$article->id' LIMIT 1");
			}
			$article->intro = textlimit($article->intro, 500, $replacer = '...');
			$article->title = str_replace(array('[RuneScape] ', '[Runescape] ', '[rs] ', '[RS] ', '[runescape] '), '', $article->title);
			$author = mkurl('user', $article->author, $article->nick);
			$article->author = usercolor($article->nick, $article->level);

			$tpl->newBlock('rsarticle');
			$tpl->assignAll($article);
			$tpl->assign('aurl', $author);
			if ($article->avatar) {
				$tpl->newBlock('rsarticle-avatar');
				$tpl->assign(array(
					'image' => trim($article->avatar),
					'alt' => trim(htmlspecialchars($article->title))
				));
			}
		}
	}


// lapošana
	if ($current_page <= $page_count/* && $current_page > 0 */) {

		$toLeft = -2;
		$toRight = 2;
		$difference = $page_count - $current_page;
		if ($page_count < 5) {
			$diff = $current_page - 1;
			$toLeft = ($diff > 2) ? -2 : -$diff;
			$toRight = ($difference > 2) ? 2 : $difference;
		} else if ($page_count >= 5) {
			if ($current_page < 4) {
				$toLeft = 1 - $current_page;
				$toRight = 5 - $current_page;
			} else if ($current_page > $page_count - 2) {
				$toLeft = $difference - 4;
				$toRight = $difference;
			}
		}
		$all_pages = '';
		if ($current_page > 3) { // izvada pirmo lapu pirms bultiņas
			$all_pages .= '<li class="start"><a href="/runescape/?page=1">1</a></li>';
		}
		if ($current_page > 1) { // izvada bultiņu uz iepriekšējo lapu
			$all_pages .= '<li class="arrows"><a href="/runescape/?page=' . ($current_page - 1) . '">««</a></li>';
		}
		for ($a = ($current_page + $toLeft); $a <= ($current_page + $toRight); $a++) { // izvada lapas pa vidu starp bultiņām
			if ($a == $current_page) {
				$all_pages .= '<li class="active">' . $a . '</li>';
			} else {
				$all_pages .= '<li><a href="/runescape/?page=' . $a . '">' . $a . '</a></li>';
			}
		}
		if ($current_page < $page_count) { // bultiņa uz nākamo lapu
			$all_pages .= '<li class="arrows"><a href="/runescape/?page=' . ($current_page + 1) . '">»»</a></li>';
		}

		if ($current_page < $page_count - 2) { // pēdējā lapa aiz labās bultiņas
			$all_pages .= '<li class="end"><a href="/runescape/?page=' . $page_count . '">' . $page_count . '</a></li>';
		}
		$tpl->newBlock('all-rs-pages');
		$tpl->assign('all-pages', $all_pages);
	}
	// lapošanas END
}
