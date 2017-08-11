<?php

/**
 * Foruma kategoriju saraksta skats
 */
$add_css[] = 'forum.css';

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
} elseif ($auth->level == 1 && !empty($_GET['movedown'])) {
	move_cat($_GET['movedown'], 'down');
}

$fcategorys = [];
$cats = $db->get_results("SELECT `id`,`title`,`textid` FROM `cat` WHERE `parent` = '$category->id' AND `module` = 'forums' ORDER BY `ordered` ASC");
if (empty($cats)) {
	$cats[0] = $category;
}
if (!empty($cats)) {
	foreach ($cats as $cat) {
		$tpl->newBlock('forum-list');
		$tpl->assign([
			'title' => $cat->title,
			'textid' => $cat->textid,
			'columns' => $columns
		]);

		//foruma kategoriju pievienošana
		if ($auth->level == 1 && !$auth->mobile) {
			$tpl->newBlock('forum-list-add');
			$tpl->assign([
				'id' => $cat->id
			]);
		}

		$add = '';
		if (!im_mod()) {
			$add = ' AND `mods_only` = 0';
		}

		$forums = $db->get_results("SELECT `title`, `textid`, `icon`, `id`, `content`, `stat_topics`, `stat_com`, `mods_only_post`, `status` FROM `cat` WHERE `parent` = '$cat->id' AND `module` = 'list'" . $add . " ORDER BY `ordered` ASC");

		foreach ($forums as $forum) {
			if ((!$forum->mods_only_post || im_mod()) && $forum->status == 'active') {
				$fcategorys[] = [
					'id' => $forum->id,
					'title' => $forum->title,
				];
			}

			$subcats = $db->get_results("SELECT `id`, `title`, `textid` FROM `cat` WHERE `parent` = '$forum->id' AND `module` = 'list'" . $add . " ORDER BY `ordered` ASC");

			$addcats = '';
			if (!empty($subcats)) {
				foreach ($subcats as $subcat) {
					$addcats .= " OR `category` = '" . $subcat->id . "'";
				}
			}

			$topic = $db->get_row("SELECT `title`, `strid`, `bump`, `author` FROM `pages` WHERE `category` = '" . $forum->id . "' " . $addcats . " ORDER BY `bump` DESC LIMIT 1");

			$add = '';
			$finfo = get_cat($forum->textid);
			if (!empty($finfo->mods)) {
				$add = '<br />Moderatori: ';
				$mods = [];
				foreach ($finfo->mods as $mod) {
					$mods[] = userlink($mod);
				}
				$add .= implode(', ', $mods);
			}

			$tpl->newBlock('forum-item');

			$tpl->assign([
				'title' => $forum->title,
				'textid' => $forum->textid,
				'content' => $forum->content . $add,
			]);

			if (!empty($topic)) {

				$tpl->assign([
					'date' => display_time(strtotime($topic->bump)),
					'topic' => '<a href="/read/' . $topic->strid . '" title="' . h($topic->title) . '">' . textlimit($topic->title, 32) . '</a>',
					'author' => userlink($topic->author)
				]);
			}

			if ($auth->level == 1 && !$auth->mobile) {
				//foruma kategoriju admin rīki
				$tpl->assign([
					'uplink' => ' <a class="forum-admin-tool" href="?moveup=' . $forum->id . '">&#8593;</a> ',
					'downlink' => ' <a class="forum-admin-tool" href="?movedown=' . $forum->id . '">&#8595;</a> ',
					'addlink' => '<br /><a class="forum-admin-tool" href="/forum-add/' . $forum->textid . '">+add</a> ',
					'editlink' => ' <a class="forum-admin-tool" href="/forum-edit/' . $forum->textid . '">edit</a> '
				]);
			}

			if ($columns == 4) {

				//category icon
				if (empty($forum->icon)) {
					$forum->icon = $generic_f_icon;
				}
				$tpl->newBlock('forum-item-avatar');
				$tpl->assign([
					'icon' => $forum->icon,
					'textid' => $forum->textid
				]);

				//category stats
				$tpl->newBlock('forum-item-stats');
				$tpl->assign([
					'posts' => $forum->stat_com,
					'topics' => $forum->stat_topics,
					'txt-posts' => lv_dsk($forum->stat_com, 'posts', 'posti'),
					'txt-topics' => lv_dsk($forum->stat_topics, 'tēma', 'tēmas')
				]);
			}


			$add = '';
			if (!im_mod()) {
				$add = ' AND `mods_only` = 0';
			}

			if (!empty($subcats)) {
				$tpl->newBlock('subcats');
				foreach ($subcats as $subcat) {
					$tpl->newBlock('subcats-node');
					$tpl->assign([
						'title' => $subcat->title,
						'textid' => $subcat->textid
					]);
					$fcategorys[] = [
						'id' => $subcat->id,
						'title' => '&nbsp;&nbsp;&raquo;&nbsp;' . $subcat->title
					];


					$subcats2 = $db->get_results("SELECT `id`, `title` FROM `cat` WHERE `parent` = '$subcat->id' AND `module` = 'list'" . $add . " ORDER BY `ordered` ASC");

					if (!empty($subcats2)) {

						foreach ($subcats2 as $subcat2) {
							$fcategorys[] = [
								'id' => $subcat2->id,
								'title' => '&nbsp;&nbsp;&nbsp;&nbsp;&raquo;&nbsp;' . $subcat2->title
							];


							$subcats3 = $db->get_results("SELECT `id`, `title` FROM `cat` WHERE `parent` = '$subcat2->id' AND `module` = 'list'" . $add . " ORDER BY `ordered` ASC");

							if (!empty($subcats3)) {

								foreach ($subcats3 as $subcat3) {
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
	}
}

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

