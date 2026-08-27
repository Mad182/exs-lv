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

if($category->textid === 'html-pamati' || $category->textid === 'css-pamati' || $category->textid === 'php-pamati' || $category->textid === 'javascript-pamati') {
	$end = 20;
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

$canonical = $opengraph_meta['url'] = 'https://' . $_SERVER['HTTP_HOST'] . '/' . $category->textid;

//list sub cats
if ($category->isforum) {
	$add = '';
	if (!im_mod()) {
		$add = ' AND `mods_only` = 0';
	}
	$subcats = $db->get_results("SELECT * FROM `cat` WHERE `parent` = '$category->id' AND `module` = 'list'" . $add . " ORDER BY `ordered` ASC");

	if (!empty($subcats)) {
		$tpl->newBlock('listsubcats');

		$subcat_ids = [];
		foreach ($subcats as $s) {
			$subcat_ids[] = (int) $s->id;
		}

		$subcat_id_in = implode(',', $subcat_ids);

		// Batch fetch 2nd level subcategories
		$subcats2_by_parent = [];
		$subcats2_results = $db->get_results("SELECT `id`, `parent`, `title`, `textid` FROM `cat` WHERE `parent` IN ($subcat_id_in) AND `module` = 'list'" . $add . " ORDER BY `ordered` ASC");
		if (!empty($subcats2_results)) {
			foreach ($subcats2_results as $s2) {
				$subcats2_by_parent[$s2->parent][] = $s2;
			}
		}

		// Batch fetch moderators for all subcategories
		$moderators_by_cat = [];
		$mods_results = $db->get_results("SELECT `category_id`, `user_id` FROM `cat_moderators` WHERE `category_id` IN ($subcat_id_in)");
		if (!empty($mods_results)) {
			foreach ($mods_results as $m_row) {
				$moderators_by_cat[$m_row->category_id][] = $m_row->user_id;
			}
		}

		// Batch fetch latest topics for each subcategory
		$latest_topics = [];
		$topic_rows = $db->get_results("
			SELECT `category`, `title`, `strid`, `bump`, `author`
			FROM (
				SELECT `category`, `title`, `strid`, `bump`, `author`,
				       ROW_NUMBER() OVER (PARTITION BY `category` ORDER BY `bump` DESC) AS `rn`
				FROM `pages`
				WHERE `category` IN ($subcat_id_in)
			) AS `sub`
			WHERE `rn` = 1
		");
		if (!empty($topic_rows)) {
			foreach ($topic_rows as $t_row) {
				$latest_topics[$t_row->category] = $t_row;
			}
		}

		foreach ($subcats as $forum) {

			$tpl->newBlock('listsubcats-node');

			if (empty($forum->icon)) {
				$forum->icon = $generic_f_icon;
			}

			$add_mods = '';
			if (!empty($moderators_by_cat[$forum->id])) {
				$add_mods = '<br>Moderatori: ';
				$mods = [];
				foreach ($moderators_by_cat[$forum->id] as $mod_uid) {
					$mods[] = userlink($mod_uid);
				}
				$add_mods .= implode(', ', $mods);
			}

			$tpl->assign([
				'id' => $forum->id,
				'title' => $forum->title,
				'textid' => $forum->textid,
				'icon' => $forum->icon,
				'content' => $forum->content . $add_mods,
				'posts' => $forum->stat_com,
				'topics' => $forum->stat_topics,
				'txt-posts' => lv_dsk($forum->stat_com, 'posts', 'posti'),
				'txt-topics' => lv_dsk($forum->stat_topics, 'tēma', 'tēmas')
			]);

			if (isset($latest_topics[$forum->id])) {
				$topic = $latest_topics[$forum->id];
				$tpl->assign([
					'date' => display_time(strtotime($topic->bump)),
					'topic' => '<a href="/read/' . $topic->strid . '" title="' . h($topic->title) . '">' . textlimit($topic->title, 32) . '</a>',
					'author' => userlink($topic->author)
				]);
			}

			if ($auth->level == 1) {
				//foruma apakškategoriju pievienošana/labošana
				$tpl->assign([
					'addlink' => '<br><a class="forum-admin-tool" href="/forum-add/' . $forum->textid . '">+add</a> ',
					'editlink' => ' <a class="forum-admin-tool" href="/forum-edit/' . $forum->textid . '">+edit</a> '
				]);
			}

			if (!empty($subcats2_by_parent[$forum->id])) {
				$tpl->newBlock('subcats');
				foreach ($subcats2_by_parent[$forum->id] as $subcat2) {
					$tpl->newBlock('subcats-node');
					$tpl->assign([
						'title' => $subcat2->title,
						'textid' => $subcat2->textid
					]);
				}
			}
		}
	}
}

if (!$category->mods_only || im_mod()) {

	$user_avatar_field = ($category->intro) ? ", `users`.`avatar` AS `user_avatar`" : "";

	$articles = $db->get_results("SELECT
		`pages`.`id` AS `id`,
		`pages`.`title` AS `title`,
		`pages`.`strid` AS `strid`,
		`pages`.`date` AS `date`,
		`pages`.`author` AS `author`,
		`pages`.`closed` AS `closed`,
		`pages`.`text` AS `text`,
		`pages`.`avatar` AS `avatar`,
		`pages`.`readby` AS `readby`,
		`pages`.`views` AS `views`,
		`pages`.`attach` AS `attach`,
		`pages`.`intro` AS `intro`,
		`pages`.`posts` AS `posts`,
		`users`.`nick` AS `nick`,
		`users`.`level` AS `level`,
		`users`.`deleted` AS `author_deleted`" . $user_avatar_field . "
	FROM
		`pages`
	LEFT JOIN
		`users` ON `users`.`id` = `pages`.`author`
	WHERE
		`pages`.`category` = " . (int)$category->id . " AND
		`pages`.`lang` = " . (int)$lang . "
	ORDER BY
		" . $sortby . "
	LIMIT
		$skip,$end");

	if ($category->module == 'list') {

		if ($skip) {
			$page_title = $page_title . ' (lapa ' . ($skip / $end + 1) . ')';
		}

		if ($category->isforum) {

			$add_css[] = 'forum.26989092.min.css';

			$page_title = $page_title . ' - forums';

			$root_cat = get_cat(get_top($category->parent));

			$tpl->newBlock('list-forum');
			$tpl->assign([
				'title' => $category->title,
				'catid' => $category->id,
				'strid' => ($root_cat ? $root_cat->textid : '')
			]);
			
			if($auth->ok) {
				$tpl->newBlock('forum-new');
				$tpl->assign([
					'catid' => $category->id,
					'strid' => ($root_cat ? $root_cat->textid : '')
				]);
			}

			if (!empty($articles)) {
				foreach ($articles as $article) {
					if (!$article->nick) {
						$article->nick = 'Nezināms';
						$article->level = 0;
					}
					$tpl->newBlock('list-forum-node');

					$date = display_time(strtotime($article->date));

					if ($article->attach) {
						$article->title = '<strong><img src="//img.exs.lv/bildes/attach-small.gif" alt="Piesprausts:" title="Piesprausts" /> ' . $article->title . '</strong>';
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
					$readby = !empty($article->readby) ? @unserialize($article->readby) : [];
					if (!is_array($readby)) {
						$readby = [];
					}
					if ($auth->ok && !in_array($auth->id, $readby)) {
						$read = 'unread';
					}
					$timg = $type . $read . $closed . '.gif';

					if (!$article->author_deleted && !empty($article->nick)) {
						$author_link = '<a href="/user/' . $article->author . '" rel="author">' . usercolor($article->nick, $article->level, false, $article->author) . '</a>';
					} else {
						$author_link = '<em>dzēsts</em>';
					}

					$tpl->assign([
						'id' => $article->id,
						'url' => '/read/' . $article->strid,
						'title' => $article->title,
						'timg' => $timg,
						'date' => $date,
						'author' => $author_link,
						'posts' => $article->posts,
					]);
				}
			}

			//list for categories with intro text
		} elseif ($category->intro) {
			$tpl->newBlock('list-articles');
			$tpl->assign([
				'title' => $category->title,
				'catid' => $category->id,
				'strid' => $category->textid
			]);

			if (!empty($articles)) {
				foreach ($articles as $article) {
					if (!$article->nick) {
						$article->nick = 'Nezināms';
						$article->level = 0;
					}
					$tpl->newBlock('list');

					$date = display_time(strtotime($article->date));

					if ($article->attach) {
						$article->title = '<strong><img src="//img.exs.lv/bildes/attach-small.gif" alt="Piesprausts:" title="Piesprausts" /> ' . $article->title . '</strong>';
					}

					if (!empty($article->intro)) {
						$article->text = $article->intro;
					} else {
						$article->text = textlimit(strip_tags(trim(str_replace('<li>', ' • ', str_replace(['&nbsp;', '<br>'], ' ', add_smile($article->text))))), 600);
						$article->intro = sanitize($article->text);
						$db->query("UPDATE `pages` SET `intro` = '$article->intro' WHERE `id` = '$article->id' LIMIT 1");
					}
					
					if (!$article->author_deleted && !empty($article->nick)) {
						$author_link = '<a rel="author" href="/user/' . $article->author . '">' . usercolor($article->nick, $article->level, false, $article->author) . '</a>';
					} else {
						$author_link = '<em>dzēsts</em>';
					}

					$u_avatar = !empty($article->user_avatar) ? '/bildes/avatari/s_' . $article->user_avatar : '/bildes/avatari/s_none.png';

					$tpl->assign([
						'id' => $article->id,
						'url' => '/read/' . $article->strid,
						'title' => $article->title,
						'views' => $article->views,
						'date' => $date,
						'author' => $author_link,
						'posts' => $article->posts,
						'intro' => $article->text,
						'avatar' => $u_avatar
					]);

					if ($article->avatar) {
						$tpl->newBlock('list-avatar');
						$tpl->assign([
							'url' => '/read/' . $article->strid,
							'image' => '/' . trim($article->avatar),
							'node-avatar-alt' => trim(h($article->title))
						]);
					}
				}
			}
		} else {

			//list for categories w/o intro text
			$tpl->newBlock('list-articles-short');
			$tpl->assign([
				'title' => $category->title,
				'catid' => $category->id,
				'strid' => $category->textid
			]);

			if (!empty($articles)) {
				foreach ($articles as $article) {

					$tpl->newBlock('list-articles-short-node');

					if ($article->attach) {
						$article->title = '<strong><img src="//img.exs.lv/bildes/attach-small.gif" alt="Piesprausts:" title="Piesprausts" />' . $article->title . '</strong>';
					}
					
					if (!$article->author_deleted && !empty($article->nick)) {
						$author_link = '<a href="/user/' . $article->author . '" rel="author">' . usercolor($article->nick, $article->level, false, $article->author) . '</a>';
					} else {
						$author_link = '<em>dzēsts</em>';
					}

					$tpl->assign([
						'id' => $article->id,
						'url' => '/read/' . $article->strid,
						'title' => $article->title,
						'date' => $article->date,
						'author' => $author_link
					]);
				}
			}
		}

		$total_count = (isset($category->stat_topics) && is_numeric($category->stat_topics) && (int)$category->stat_topics > 0)
			? (int)$category->stat_topics
			: (int)$db->get_var("SELECT count(*) FROM `pages` WHERE `category` = '$category->id'");

		$pager = pager($total_count, $skip, $end, '/' . $category->textid . '/?skip=');
		$tpl->assignGlobal([
			'pager-next' => $pager['next'],
			'pager-prev' => $pager['prev'],
			'pager-numeric' => $pager['pages']
		]);
	} else {
		$tpl->newBlock('error-catempty');
		$tpl->assign('title', $category->title);
	}
} else {
	set_flash('Tu nevari apskatīt šo lapu!', 'error');
	redirect();
}
