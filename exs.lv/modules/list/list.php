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
	$v = get_forum_cache_version($lang);
	$is_admin = ($auth->level == 1 && !$auth->mobile);
	$cache_key_subcats = 'forum_subcats_' . $category->id . '_' . $lang . '_' . intval($is_admin) . '_' . intval(im_mod()) . '_' . $v;

	$subcats_html = $m->get($cache_key_subcats);
	if ($subcats_html === false) {
		$add = '';
		if (!im_mod()) {
			$add = ' AND `mods_only` = 0';
		}
		$subcats = $db->get_results("SELECT * FROM `cat` WHERE `parent` = '$category->id' AND `module` = 'list'" . $add . " ORDER BY `ordered` ASC");

		if (!empty($subcats)) {
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

			$sub_out = '<table id="forum"><tr><th class="first" colspan="4">Apakšsadaļas</th></tr>';
			foreach ($subcats as $forum) {
				$icon = !empty($forum->icon) ? $forum->icon : $generic_f_icon;

				$add_mods = '';
				if (!empty($moderators_by_cat[$forum->id])) {
					$add_mods = '<br>Moderatori: ';
					$mods = [];
					foreach ($moderators_by_cat[$forum->id] as $mod_uid) {
						$mod_usr = get_user($mod_uid);
						if ($mod_usr && !empty($mod_usr->nick)) {
							$mods[] = '<a href="/user/' . $mod_uid . '">' . usercolor($mod_usr->nick, $mod_usr->level, 'disable', $mod_uid) . '</a>';
						}
					}
					$add_mods .= implode(', ', $mods);
				}

				$admin_links = '';
				if ($auth->level == 1) {
					$admin_links = '<br><a class="forum-admin-tool" href="/forum-add/' . $forum->textid . '">+add</a> ' .
					               ' <a class="forum-admin-tool" href="/forum-edit/' . $forum->textid . '">+edit</a> ';
				}

				$sub2_html = '';
				if (!empty($subcats2_by_parent[$forum->id])) {
					$sub2_html = '<ul class="subcat-list">';
					foreach ($subcats2_by_parent[$forum->id] as $subcat2) {
						$sub2_html .= '<li><a href="/' . $subcat2->textid . '">' . $subcat2->title . '</a></li>';
					}
					$sub2_html .= '</ul>';
				}

				$last_topic_str = '';
				if (isset($latest_topics[$forum->id])) {
					$t_row = $latest_topics[$forum->id];
					$t_usr = get_user($t_row->author);
					$author_link = ($t_usr && !empty($t_usr->nick) && empty($t_usr->deleted))
						? '<a href="/user/' . $t_row->author . '" rel="author">' . usercolor($t_usr->nick, $t_usr->level, 'disable', $t_row->author) . '</a>'
						: '<em>dzēsts</em>';
					$last_topic_str = '<a href="/read/' . $t_row->strid . '" title="' . h($t_row->title) . '">' . textlimit($t_row->title, 32) . '</a><br>' .
					                  display_time(strtotime($t_row->bump)) . '<br>no: ' . $author_link;
				}

				$sub_out .= '<tr class="forum-row"><td class="forum-avatar"><a href="/' . $forum->textid . '"><img class="forum-icon" width="40" height="40" src="/' . $icon . '" alt="" /></a></td>' .
				            '<td class="forum-main"><h3><a href="/' . $forum->textid . '">' . $forum->title . '</a></h3>' .
				            (!empty($forum->content) || !empty($add_mods) || !empty($admin_links) ? '<p class="forum-desc">' . $forum->content . $add_mods . $admin_links . '</p>' : '') .
				            $sub2_html . '</td>' .
				            '<td class="stat td-stats"><span class="stat-item">' . $forum->stat_topics . '&nbsp;' . lv_dsk($forum->stat_topics, 'tēma', 'tēmas') . '</span><span class="stat-item">' . $forum->stat_com . '&nbsp;' . lv_dsk($forum->stat_com, 'posts', 'posti') . '</span></td>' .
				            '<td class="last td-last">' . $last_topic_str . '</td></tr>';
			}
			$sub_out .= '</table>';
			$subcats_html = $sub_out;
		} else {
			$subcats_html = '';
		}
		$m->set($cache_key_subcats, $subcats_html, 21600);
	}

	if (!empty($subcats_html)) {
		$tpl->newBlock('listsubcats');
		$tpl->assign('forum-subcats-html', $subcats_html);
	}
}

if (!$category->mods_only || im_mod()) {

	if ($category->module == 'list' && $category->isforum) {
		if ($skip) {
			$page_title = $page_title . ' (lapa ' . ($skip / $end + 1) . ')';
		}
		$add_css[] = 'forum.4215ef3f.min.css';
		$page_title = $page_title . ' - forums';
		$root_cat = get_cat(get_top($category->parent));

		$tpl->newBlock('list-forum');
		$tpl->assign([
			'title' => $category->title,
			'catid' => $category->id,
			'strid' => ($root_cat ? $root_cat->textid : '')
		]);

		if ($auth->ok) {
			$tpl->newBlock('forum-new');
			$tpl->assign([
				'catid' => $category->id,
				'strid' => ($root_cat ? $root_cat->textid : '')
			]);
		}

		$v = get_forum_cache_version($lang);
		$cache_key_topics = 'forum_topics_' . $category->id . '_' . $skip . '_' . $lang . '_' . $v;
		$topics_html = $m->get($cache_key_topics);

		if ($topics_html === false) {
			$articles = $db->get_results("SELECT
				`pages`.`id` AS `id`,
				`pages`.`title` AS `title`,
				`pages`.`strid` AS `strid`,
				`pages`.`date` AS `date`,
				`pages`.`author` AS `author`,
				`pages`.`closed` AS `closed`,
				`pages`.`attach` AS `attach`,
				`pages`.`posts` AS `posts`,
				`users`.`nick` AS `nick`,
				`users`.`level` AS `level`,
				`users`.`deleted` AS `author_deleted`
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

			$t_out = '';
			if (!empty($articles)) {
				$t_out .= '<table id="forum" class="forum-table forum-topics"><thead><tr class="forum-topics-header"><th colspan="2" class="first th-topics">Tēmas</th><th class="stat th-replies">Atbildes</th><th class="last th-date">Datums</th></tr></thead><tbody>';
				foreach ($articles as $article) {
					if (!$article->nick) {
						$article->nick = 'Nezināms';
						$article->level = 0;
					}
					$date = display_time(strtotime($article->date));

					$title_display = $article->title;
					if ($article->attach) {
						$title_display = '<strong><img src="//img.exs.lv/bildes/attach-small.gif" alt="Piesprausts:" title="Piesprausts" /> ' . $article->title . '</strong>';
					}

					$type = $article->attach ? 'sticky_' : 'topic_';
					$closed = $article->closed ? '_locked' : '';
					$timg = $type . 'read' . $closed . '.gif';

					if (!$article->author_deleted && !empty($article->nick)) {
						$author_link = '<a href="/user/' . $article->author . '" rel="author">' . usercolor($article->nick, $article->level, 'disable', $article->author) . '</a>';
					} else {
						$author_link = '<em>dzēsts</em>';
					}

					$t_out .= '<tr class="topic-row"><td class="topic-icon-cell"><img class="topic-icon" width="19" height="18" src="//img.exs.lv/bildes/' . $timg . '" alt="" /></td>' .
					          '<td class="topic-title-cell"><h3><a href="/read/' . $article->strid . '">' . $title_display . '</a></h3></td>' .
					          '<td class="stat td-replies"><span class="stat-num">' . $article->posts . '</span></td>' .
					          '<td class="last td-last td-date">' . $date . '<br><span class="topic-author-label">no:&nbsp;</span>' . $author_link . '</td></tr>';
				}
				$t_out .= '</tbody></table>';
			}

			$total_count = (isset($category->stat_topics) && is_numeric($category->stat_topics) && (int)$category->stat_topics > 0)
				? (int)$category->stat_topics
				: (int)$db->get_var("SELECT count(*) FROM `pages` WHERE `category` = '$category->id'");

			$pager = pager($total_count, $skip, $end, '/' . $category->textid . '/?skip=');
			if (!empty($pager['pages']) || !empty($pager['next']) || !empty($pager['prev'])) {
				$t_out .= '<p class="core-pager">' . $pager['next'] . ' ' . $pager['pages'] . ' ' . $pager['prev'] . '</p>';
			}

			$topics_html = $t_out;
			$m->set($cache_key_topics, $topics_html, 21600);
		}

		$tpl->assign('forum-topics-html', $topics_html);

	} elseif ($category->module == 'list') {

		$user_avatar_field = ($category->intro) ? ", `users`.`avatar` AS `user_avatar`, `users`.`av_alt` AS `user_av_alt`" : "";

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

		if ($skip) {
			$page_title = $page_title . ' (lapa ' . ($skip / $end + 1) . ')';
		}

		if ($category->intro) {
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

					$u_avatar = get_avatar((object)[
						'avatar' => $article->user_avatar ?? 'none.png',
						'av_alt' => $article->user_av_alt ?? 0
					], 's');

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
