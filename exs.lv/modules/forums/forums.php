<?php

/**
 * Foruma kategoriju saraksta skats
 */
$add_css[] = 'forum.26989092.min.css';

$columns = 4;
if ($auth->mobile) {
	$columns = 2;
}

//add
if ($auth->ok && isset($_POST['new-topic-title']) && isset($_POST['new-topic-body'])) {
	$body = trim($_POST['new-topic-body']);
	$title = trim($_POST['new-topic-title']);
	$newcat = (int) $_POST['new-topic-category'];
	require(CORE_PATH . '/includes/class.comment.php');
	$addcom = new Comment();
	if ($body && $title && $addcom->check_isforum($newcat)) {
		if (!isset($_SESSION['antiflood']) or $_SESSION['antiflood'] < time() - 45) {

			if (!isset($_POST['token']) || $_POST['token'] != md5($category->title . $remote_salt . $auth->id)) {
				set_flash('Kļūdains pieprasījums! Hacking around?', 'error');
				redirect();
			}

			$_SESSION["antiflood"] = time();
			$title = title2db($title);
			$body = htmlpost2db($body);
			$textid = date('YmdHis');
			$strid = mkslug_newpage($title);

			//write to database
			$db->query("INSERT INTO `pages` (`strid`, `textid`, `category`, `text`, `title`, `author`, `date`, `bump`, `ip`, `lang`, `disable_emotions`)
							VALUES ('$strid', '$textid', '$newcat', '$body', '$title', '$auth->id', NOW(), NOW(), '$auth->ip', '$lang', '" . intval($disable_emotions) . "')");

			update_stats($newcat);
			userlog($auth->id, 'Aizsāka foruma tēmu <a href="/read/' . $strid . '">' . $title . '</a>');
			update_karma($auth->id);
			clear_latest_posts_cache($lang);
			clear_forum_cache($lang);

			redirect('/read/' . $strid);
		} else {
			set_flash('Jāuzgaida vismaz 1 minūti, pirms vari pievienot jaunu tēmu!', 'error');
		}

		/* nepazaudē satura laukā ierakstīto ja nav aizpildīts nosaukums */
	} elseif (!empty($body) && empty($title)) {
		set_flash('Lūdzu norādi tēmas nosaukumu!', 'error');
		$tpl->assignGlobal('forum-content', h(trim($_POST['new-topic-body'])));
	}
}

$tpl->newBlock('forum');
$tpl->assign('title', $category->title);

//sadaļu pārkārtošana
if ($auth->level == 1 && !empty($_GET['moveup'])) {
	move_cat($_GET['moveup'], 'up');
	clear_forum_cache($lang);
} elseif ($auth->level == 1 && !empty($_GET['movedown'])) {
	move_cat($_GET['movedown'], 'down');
	clear_forum_cache($lang);
}

//canonical
if($category->textid === 'index') {
    $canonical = $opengraph_meta['url'] = 'https://' . $_SERVER['HTTP_HOST'] . '/';
} else {
    $canonical = $opengraph_meta['url'] = 'https://' . $_SERVER['HTTP_HOST'] . '/' . $category->textid;
}

$v = get_forum_cache_version($lang);
$is_admin = ($auth->level == 1 && !$auth->mobile);
$cache_key = 'forum_idx_' . $lang . '_' . $columns . '_' . intval($is_admin) . '_' . intval(im_mod()) . '_' . intval($auth->ok) . '_' . $v;

$cached_data = $m->get($cache_key);
if ($cached_data !== false && is_array($cached_data)) {
	$forum_table_html = $cached_data['html'];
	$fcategorys = $cached_data['cats'];
} else {
	// 1. Batch-fetch all relevant categories for this language
	$all_cats = $db->get_results("SELECT `id`, `parent`, `title`, `textid`, `icon`, `content`, `stat_topics`, `stat_com`, `mods_only_post`, `status`, `mods_only`, `private`, `ordered`, `module` FROM `cat` WHERE (`lang` = '$lang' OR `lang` = 0) ORDER BY `ordered` ASC");

	$cats_by_parent = [];
	if (!empty($all_cats)) {
		foreach ($all_cats as $c) {
			$cats_by_parent[$c->parent][] = $c;
		}
	}

	$is_mod = im_mod();
	$is_auth = ($auth->ok === true);

	$can_view_cat = function($c) use ($is_mod, $is_auth) {
		if (!$is_mod && !empty($c->mods_only)) {
			return false;
		}
		if (!$is_auth && !empty($c->private)) {
			return false;
		}
		return true;
	};

	// Root categories (sub-boards or self)
	$cats = [];
	if (isset($cats_by_parent[$category->id])) {
		foreach ($cats_by_parent[$category->id] as $c) {
			if ($c->module === 'forums' && $can_view_cat($c)) {
				$cats[] = $c;
			}
		}
	}
	if (empty($cats)) {
		$cats[0] = $category;
	}

	// 2. Pre-gather all forum boards, subcategories, and category IDs to batch fetch latest topics and moderators
	$forum_list_data = [];
	$all_target_cat_ids = [];
	$forum_subcat_ids = [];
	$forum_ids = [];

	foreach ($cats as $cat) {
		$forums = [];
		if (isset($cats_by_parent[$cat->id])) {
			foreach ($cats_by_parent[$cat->id] as $c) {
				if ($c->module === 'list' && $can_view_cat($c)) {
					$forums[] = $c;
					$forum_ids[] = $c->id;
					$all_target_cat_ids[] = $c->id;
					$forum_subcat_ids[$c->id] = [];
				}
			}
		}

		$forum_list_data[$cat->id] = [
			'cat' => $cat,
			'forums' => []
		];

		foreach ($forums as $forum) {
			$subcats = [];
			if (isset($cats_by_parent[$forum->id])) {
				foreach ($cats_by_parent[$forum->id] as $c) {
					if ($c->module === 'list' && $can_view_cat($c)) {
						$subcats[] = $c;
						$forum_subcat_ids[$forum->id][] = $c->id;
						$all_target_cat_ids[] = $c->id;

						if (isset($cats_by_parent[$c->id])) {
							foreach ($cats_by_parent[$c->id] as $c2) {
								if ($c2->module === 'list' && $can_view_cat($c2)) {
									$forum_subcat_ids[$forum->id][] = $c2->id;
									$all_target_cat_ids[] = $c2->id;

									if (isset($cats_by_parent[$c2->id])) {
										foreach ($cats_by_parent[$c2->id] as $c3) {
											if ($c3->module === 'list' && $can_view_cat($c3)) {
												$forum_subcat_ids[$forum->id][] = $c3->id;
												$all_target_cat_ids[] = $c3->id;
											}
										}
									}
								}
							}
						}
					}
				}
			}

			$forum_list_data[$cat->id]['forums'][] = [
				'forum' => $forum,
				'subcats' => $subcats
			];
		}
	}

	// 3. Batch fetch moderators for all forum boards in one query
	$moderators_by_cat = [];
	if (!empty($forum_ids)) {
		$f_id_in = implode(',', array_map('intval', array_unique($forum_ids)));
		$mods_results = $db->get_results("SELECT `category_id`, `user_id` FROM `cat_moderators` WHERE `category_id` IN ($f_id_in)");
		if (!empty($mods_results)) {
			foreach ($mods_results as $m_row) {
				$moderators_by_cat[$m_row->category_id][] = $m_row->user_id;
			}
		}
	}

	// 4. Batch fetch latest topics for all categories in one query
	$latest_topics = [];
	if (!empty($all_target_cat_ids)) {
		$cat_id_in = implode(',', array_map('intval', array_unique($all_target_cat_ids)));
		$topic_rows = $db->get_results("
			SELECT `category`, `title`, `strid`, `bump`, `author`
			FROM (
				SELECT `category`, `title`, `strid`, `bump`, `author`,
				       ROW_NUMBER() OVER (PARTITION BY `category` ORDER BY `bump` DESC) AS `rn`
				FROM `pages`
				WHERE `category` IN ($cat_id_in)
			) AS `sub`
			WHERE `rn` = 1
		");
		if (!empty($topic_rows)) {
			foreach ($topic_rows as $t_row) {
				$latest_topics[$t_row->category] = $t_row;
			}
		}
	}

	// 5. Build HTML table string
	$fcategorys = [];
	$out = '<table id="forum">';

	foreach ($forum_list_data as $cat_id => $group_data) {
		$cat = $group_data['cat'];
		$out .= '<tr><th class="first" colspan="' . $columns . '"><a href="/' . $cat->textid . '">' . $cat->title . '</a>';
		if ($is_admin) {
			$out .= '<span style="float:right;font-size:9px;"><a href="/forum-add/' . $cat->id . '">+pievienot</a></span>';
		}
		$out .= '</th></tr>';

		foreach ($group_data['forums'] as $fitem) {
			$forum = $fitem['forum'];
			$subcats = $fitem['subcats'];

			if ((!$forum->mods_only_post || im_mod()) && $forum->status == 'active') {
				$fcategorys[] = [
					'id' => $forum->id,
					'title' => $forum->title,
				];
			}

			// Find newest topic
			$topic = null;
			$all_forum_cats = array_merge([$forum->id], $forum_subcat_ids[$forum->id] ?? []);
			foreach ($all_forum_cats as $cid) {
				if (isset($latest_topics[$cid])) {
					if ($topic === null || strtotime($latest_topics[$cid]->bump) > strtotime($topic->bump)) {
						$topic = $latest_topics[$cid];
					}
				}
			}

			$add = '';
			if (!empty($moderators_by_cat[$forum->id])) {
				$add = '<br>Moderatori: ';
				$mods = [];
				foreach ($moderators_by_cat[$forum->id] as $mod_uid) {
					$mod_usr = get_user($mod_uid);
					if ($mod_usr && !empty($mod_usr->nick)) {
						$mods[] = '<a href="/user/' . $mod_uid . '">' . usercolor($mod_usr->nick, $mod_usr->level, 'disable', $mod_uid) . '</a>';
					}
				}
				$add .= implode(', ', $mods);
			}

			$admin_links = '';
			if ($is_admin) {
				$admin_links = ' <a class="forum-admin-tool" href="?moveup=' . $forum->id . '">&#8593;</a> ' .
				               ' <a class="forum-admin-tool" href="?movedown=' . $forum->id . '">&#8595;</a> ' .
				               '<br><a class="forum-admin-tool" href="/forum-add/' . $forum->textid . '">+add</a> ' .
				               ' <a class="forum-admin-tool" href="/forum-edit/' . $forum->textid . '">edit</a> ';
			}

			$subcats_html = '';
			if (!empty($subcats)) {
				$subcats_html = '<ul class="subcat-list">';
				foreach ($subcats as $subcat) {
					$subcats_html .= '<li><a href="/' . $subcat->textid . '">' . $subcat->title . '</a></li>';
					$fcategorys[] = [
						'id' => $subcat->id,
						'title' => '&nbsp;&nbsp;&raquo;&nbsp;' . $subcat->title
					];

					if (isset($cats_by_parent[$subcat->id])) {
						foreach ($cats_by_parent[$subcat->id] as $subcat2) {
							if ($subcat2->module === 'list' && $can_view_cat($subcat2)) {
								$fcategorys[] = [
									'id' => $subcat2->id,
									'title' => '&nbsp;&nbsp;&nbsp;&nbsp;&raquo;&nbsp;' . $subcat2->title
								];

								if (isset($cats_by_parent[$subcat2->id])) {
									foreach ($cats_by_parent[$subcat2->id] as $subcat3) {
										if ($subcat3->module === 'list' && $can_view_cat($subcat3)) {
											$fcategorys[] = [
												'id' => $subcat3->id,
												'title' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&raquo;&nbsp;' . $subcat3->title
											];
										}
									}
								}
							}
						}
					}
				}
				$subcats_html .= '</ul>';
			}

			$last_col = '';
			if (!empty($topic)) {
				$topic_usr = get_user($topic->author);
				$author_link = '';
				if ($topic_usr && !empty($topic_usr->nick) && empty($topic_usr->deleted)) {
					$author_link = '<a href="/user/' . $topic->author . '" rel="author">' . usercolor($topic_usr->nick, $topic_usr->level, 'disable', $topic->author) . '</a>';
				} else {
					$author_link = '<em>dzēsts</em>';
				}
				$last_col = '<a href="/read/' . $topic->strid . '" title="' . h($topic->title) . '">' . textlimit($topic->title, 32) . '</a><br>' .
				            display_time(strtotime($topic->bump)) . '<br>no: ' . $author_link;
			}

			$out .= '<tr>';
			if ($columns == 4) {
				$icon = !empty($forum->icon) ? $forum->icon : $generic_f_icon;
				$out .= '<td class="forum-avatar"><a href="/' . $forum->textid . '"><img width="48" height="48" src="/' . $icon . '" alt="" /></a></td>';
			}

			$out .= '<td><h2><a href="/' . $forum->textid . '">' . $forum->title . '</a></h2>' .
			        '<p>' . $forum->content . $add . $admin_links . '</p>' .
			        $subcats_html . '</td>';

			if ($columns == 4) {
				$out .= '<td class="stat">' .
				        $forum->stat_topics . '&nbsp;' . lv_dsk($forum->stat_topics, 'tēma', 'tēmas') . '<br>' .
				        $forum->stat_com . '&nbsp;' . lv_dsk($forum->stat_com, 'posts', 'posti') .
				        '</td>';
			}

			$out .= '<td class="last">' . $last_col . '</td>';
			$out .= '</tr>';
		}
	}
	$out .= '</table>';
	$forum_table_html = $out;

	$m->set($cache_key, ['html' => $forum_table_html, 'cats' => $fcategorys], 21600);
}

$tpl->assign('forum-table-html', $forum_table_html);

//form
if ($auth->ok && $category->status == 'active') {

	if (!$auth->mobile) {
		$tpl->newBlock('tinymce-enabled');
	}
	$tpl->newBlock('forum-addtopic');
	$tpl->assign('forum-check', md5($category->title . $remote_salt . $auth->id));

	if (!empty($fcategorys)) {
		foreach ($fcategorys as $fcategory) {
			$tpl->newBlock('select-category');
			$sel = '';
			if ((isset($_GET['cat']) && $_GET['cat'] == $fcategory['id']) || (!isset($_GET['cat']) && $fcategory['id'] == 232)) {
				$sel = ' selected="selected"';
			}
			$tpl->assign([
				'id' => $fcategory['id'],
				'title' => $fcategory['title'],
				'sel' => $sel
			]);
		}
	}

	$tpl->newBlock('forum-new');
} elseif ($category->status == 'active') {
	$tpl->newBlock('forum-addtopic-no');
}

$pagepath = $category->title;
if ($category->parent) {
	$category2 = get_cat($category->parent);
	$pagepath = '<a href="/' . $category2->textid . '">' . $category2->title . '</a> / ' . $pagepath;
	$page_title = $page_title . ' | ' . $category2->title;
}

if ($category->textid == 'index' && !empty($category->content) && !$auth->mobile) {
	$tpl->newBlock('meta-description');
	$tpl->assign('description', h($category->content));
}
