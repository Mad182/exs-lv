<?php

if ((!isset($_GET['viewcat']) || $_GET['viewcat'] !== $category->textid) && $category->textid != 'index') {
	redirect('/' . $category->textid, true);
}

$skip = 0;
if (isset($_GET['skip'])) {
	$skip = (int) $_GET['skip'];
}

$end = 45;
if ($category->intro) {
	$end = 10;
} elseif ($category->showall) {
	$end = 200;
}

if ($category->alphabetical) {
	$sortby = "`pages`.`title` ASC";
} elseif ($category->intro) {
	$sortby = "`pages`.`date` DESC";
} else {
	$sortby = "`pages`.`attach` DESC, `pages`.`date` DESC";
}

if ($category->isforum) {
	$end = 45;
	$sortby = "`pages`.`attach` DESC, `pages`.`bump` DESC";
}

$pagepath = $category->title;
if ($category->parent) {
	$category2 = get_cat($category->parent);
	$pagepath = '<a href="/' . $category2->textid . '">' . $category2->title . '</a> / ' . $pagepath;
	if ($category2->parent) {
		$category3 = get_cat($category2->parent);
		$pagepath = '<a href="/' . $category3->textid . '">' . $category3->title . '</a> / ' . $pagepath;
	}
} else {
	$pagepath = '';
}

//list sub cats
if ($category->isforum) {
	$add = '';
	if (!im_mod()) {
		$add = ' AND `mods_only` = 0';
	}
	$subcats = $db->get_results("SELECT * FROM `cat` WHERE `parent` = '$category->id' AND `module` = 'list'" . $add . " ORDER BY `ordered` ASC");

	if (!empty($subcats)) {
		$tpl->newBlock('listsubcats');
		foreach ($subcats as $forum) {

			$tpl->newBlock('listsubcats-node');

			$subcats2 = $db->get_results("SELECT `id`, `title`, `textid` FROM `cat` WHERE `parent` = '$forum->id' AND `module` = 'list'" . $add . " ORDER BY `ordered` ASC");

			if (empty($forum->icon)) {
				$forum->icon = $generic_f_icon;
			}

			$add = '';
			$finfo = get_cat($forum->textid);
			if (!empty($finfo->mods)) {
				$add = '<br />Moderatori: ';
				$mods = array();
				foreach ($finfo->mods as $mod) {
					$minfo = get_user($mod);
					$mods[] = '<a href="/user/' . $minfo->id . '">' . usercolor($minfo->nick, $minfo->level, false, $minfo->id) . '</a>';
				}
				$add .= implode(', ', $mods);
			}

			$tpl->assign(array(
				'id' => $forum->id,
				'title' => $forum->title,
				'textid' => $forum->textid,
				'icon' => $forum->icon,
				'content' => $forum->content . $add,
				'posts' => $forum->stat_com,
				'topics' => $forum->stat_topics,
				'txt-posts' => lv_dsk($forum->stat_com, 'posts', 'posti'),
				'txt-topics' => lv_dsk($forum->stat_topics, 'tēma', 'tēmas')
			));

			$topic = $db->get_row("SELECT `title`, `strid`, `bump`, `author` FROM `pages` WHERE `category` = '" . $forum->id . "' ORDER BY `bump` DESC LIMIT 1");
			if (!empty($topic)) {
				$author = get_user($topic->author);
				$tpl->assign(array(
					'date' => display_time(strtotime($topic->bump)),
					'topic' => '<a href="/read/' . $topic->strid . '" title="' . htmlspecialchars($topic->title) . '">' . textlimit($topic->title, 32) . '</a>',
					'author' => '<a href="/user/' . $author->id . '">' . usercolor($author->nick, $author->level, false, $author->id) . '</a>'
				));
			}


			if ($auth->level == 1) {
				//foruma apakškategoriju pievienošana/labošana
				$tpl->assign(array(
					'addlink' => '<br /><a class="forum-admin-tool" href="/forum-add/' . $forum->textid . '">+add</a> ',
					'editlink' => ' <a class="forum-admin-tool" href="/forum-edit/' . $forum->textid . '">+edit</a> '
				));
			}

			if (!empty($subcats2)) {
				$tpl->newBlock('subcats');
				foreach ($subcats2 as $subcat2) {
					$tpl->newBlock('subcats-node');
					$tpl->assign(array(
						'title' => $subcat2->title,
						'textid' => $subcat2->textid
					));
				}
			}
		}
	}
}

if (!$category->mods_only || im_mod()) {

	if ($category->intro) {
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
		`pages`.`attach` AS `attach`,
		`pages`.`intro` AS `intro`,
		`users`.`nick` AS `nick`,
		`users`.`level` AS `level`,
		`users`.`gender` AS `gender`,
		`users`.`deleted` AS `author_deleted`
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
	} else {
		$articles = $db->get_results("SELECT
		`pages`.`id` AS `id`,
		`pages`.`title` AS `title`,
		`pages`.`strid` AS `strid`,
		`pages`.`date` AS `date`,
		`pages`.`author` AS `author`,
		`pages`.`closed` AS `closed`,
		`pages`.`attach` AS `attach`,
		`pages`.`views` AS `views`,
		`pages`.`readby` AS `readby`,
		`pages`.`posts` AS `posts`,
		`users`.`nick` AS `nick`,
		`users`.`level` AS `level`,
		`users`.`deleted` AS `author_deleted`
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
	}
	if ($category->module == 'list') {

		if ($skip) {
			$page_title = $page_title . ' (lapa ' . ($skip / $end + 1) . ')';
		}

		if ($category->isforum) {

			$add_css .= ',forum.css';

			$page_title = $page_title . ' - forums';

			$root_cat = get_cat(get_top($category->parent));

			$tpl->newBlock('list-forum');
			$tpl->assign(array(
				'title' => $category->title,
				'catid' => $category->id,
				'strid' => $root_cat->textid
			));
			
			if($auth->ok) {
				$tpl->newBlock('forum-new');
				$tpl->assign(array(
					'catid' => $category->id,
					'strid' => $root_cat->textid
				));
			}

			foreach ($articles as $article) {
				if (!$article->nick) {
					$article->nick = 'Nezināms';
					$article->level = 0;
				}
				$tpl->newBlock('list-forum-node');

				$date = display_time(strtotime($article->date));

				$title_clear = $article->title;

				if ($article->attach) {
					$article->title = '<strong><img src="//img.exs.lv/bildes/attach-small.gif" alt="Piesprausts:" title="Piesprausts" /> ' . $article->title . '</strong>';
				} else {
					$article->title = $article->title;
				}

				$type = 'topic_';
				if ($article->attach) {
					$type = 'sticky_';
				}
				$closed = '';
				if ($article->closed) {
					$closed = '_locked';
				}
				$read = 'read';
				if ($auth->ok && !in_array($auth->id, unserialize($article->readby))) {
					$read = 'unread';
				}
				$timg = $type . $read . $closed . '.gif';

				if (!$article->author_deleted) {
					$author_link = '<a href="/user/' . $article->author . '" rel="author">' . usercolor($article->nick, $article->level, false, $article->author) . '</a>';
				} else {
					$author_link = '<em>dzēsts</em>';
				}

				$tpl->assign(array(
					'id' => $article->id,
					'node-url' => '/read/' . $article->strid,
					'title' => $article->title,
					'timg' => $timg,
					'date' => $date,
					'author' => $author_link,
					'posts' => $article->posts,
				));
			}

			//list for categories with intro text
		} elseif ($category->intro) {
			$tpl->newBlock('list-articles');
			$tpl->assign(array(
				'title' => $category->title,
				'catid' => $category->id,
				'strid' => $category->textid
			));

			foreach ($articles as $article) {
				if (!$article->nick) {
					$article->nick = 'Nezināms';
					$article->level = 0;
				}
				$tpl->newBlock('list');

				$date = display_time(strtotime($article->date));

				if ($article->attach) {
					$article->title = '<strong><img src="//img.exs.lv/bildes/attach-small.gif" alt="Piesprausts:" title="Piesprausts" /> ' . $article->title . '</strong>';
				} else {
					$article->title = $article->title;
				}

				if (!empty($article->intro)) {
					$article->text = $article->intro;
				} else {
					$article->text = textlimit(strip_tags(trim(str_replace('<li>', ' • ', str_replace(array('&nbsp;', '<br />'), ' ', add_smile($article->text))))), 600);
					$article->intro = sanitize($article->text);
					$db->query("UPDATE pages SET intro = '$article->intro' WHERE id = '$article->id' LIMIT 1");
				}

				$tpl->assign(array(
					'id' => $article->id,
					'node-url' => '/read/' . $article->strid,
					'author-id' => $article->author,
					'title' => $article->title,
					'views' => $article->views,
					'date' => $date,
					'author' => usercolor($article->nick, $article->level, false, $article->author),
					'posts' => $article->posts,
					'level' => $article->level,
					'gender' => $article->gender,
					'intro' => $article->text
				));

				if ($article->avatar) {
					$tpl->newBlock('list-avatar');
					$tpl->assign(array(
						'node-avatar-image' => '/' . trim($article->avatar),
						'node-avatar-alt' => trim(htmlspecialchars($article->title))
					));
				}
			}
		} else {

			//list for categories w/o intro text
			$tpl->newBlock('list-articles-short');
			$tpl->assign(array(
				'title' => $category->title,
				'catid' => $category->id,
				'strid' => $category->textid
			));

			foreach ($articles as $article) {

				$tpl->newBlock('list-articles-short-node');

				if ($article->attach) {
					$article->title = '<strong><img src="//img.exs.lv/bildes/attach-small.gif" alt="Piesprausts:" title="Piesprausts" />' . $article->title . '</strong>';
				}

				$tpl->assign(array(
					'id' => $article->id,
					'node-url' => '/read/' . $article->strid,
					'author-id' => $article->author,
					'title' => $article->title,
					'date' => $article->date,
					'author' => usercolor($article->nick, $article->level, false, $article->author)
				));
			}
		}

		$pager = pager($db->get_var("SELECT count(*) FROM `pages` WHERE `category` = '$category->id'"), $skip, $end, '/' . $category->textid . '/?skip=');
		$tpl->assignGlobal(array(
			'pager-next' => $pager['next'],
			'pager-prev' => $pager['prev'],
			'pager-numeric' => $pager['pages']
		));
	} else {
		$tpl->newBlock('error-catempty');
		$tpl->assign('title', $category->title);
	}
} else {
	set_flash('Tu nevari apskatīt šo lapu!', 'error');
	redirect();
}
