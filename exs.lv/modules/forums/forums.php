<?php

$ignore_tla = true;
$add_css .= ',forum.css';

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
							VALUES ('$strid', '$textid', '$newcat', '$body', '$title', '$auth->id', NOW(), NOW(), '$auth->ip', '$lang', '".intval($disable_emotions)."')");

			update_stats($newcat);
			userlog($auth->id, 'Aizsāka foruma tēmu <a href="/read/' . $strid . '">' . $title . '</a>');
			update_karma($auth->id);

			redirect('/read/' . $strid);
		} else {
			set_flash('Jāuzgaida vismaz 1 minūti, pirms vari pievienot jaunu tēmu!', 'error');
		}
	}
}

$tpl->newBlock('forum');
$tpl->assign('title', $category->title);

if ($auth->level == 1 && isset($_GET['moveup'])) {
	$move = $db->get_row("SELECT * FROM `cat` WHERE `id` = '" . intval($_GET['moveup']) . "'");
	$upper = $db->get_row("SELECT * FROM `cat` WHERE `isforum` = 1 AND `parent` = '$move->parent' AND `ordered` < '$move->ordered' ORDER BY `ordered` DESC LIMIT 1");
	if ($move && $upper) {
		$db->query("UPDATE `cat` SET `ordered` = '$move->ordered' WHERE `id` = '$upper->id' LIMIT 1");
		$db->query("UPDATE `cat` SET `ordered` = '$upper->ordered' WHERE `id` = '$move->id' LIMIT 1");
	}
}

if ($auth->level == 1 && isset($_GET['movedown'])) {
	$move = $db->get_row("SELECT * FROM `cat` WHERE `id` = '" . intval($_GET['movedown']) . "'");
	$upper = $db->get_row("SELECT * FROM `cat` WHERE `isforum` = 1 AND `parent` = '$move->parent' AND `ordered` > '$move->ordered' ORDER BY `ordered` ASC LIMIT 1");
	if ($move && $upper) {
		$db->query("UPDATE `cat` SET `ordered` = '$move->ordered' WHERE `id` = '$upper->id' LIMIT 1");
		$db->query("UPDATE `cat` SET `ordered` = '$upper->ordered' WHERE `id` = '$move->id' LIMIT 1");
	}
}

$fcategorys = array();
$cats = $db->get_results("SELECT `id`,`title`,`textid` FROM `cat` WHERE `parent` = '$category->id' AND `module` = 'forums' ORDER BY `ordered` ASC");
if (empty($cats)) {
	$cats[0] = $category;
}
if (!empty($cats)) {
	foreach ($cats as $cat) {
		$tpl->newBlock('forum-list');
		$tpl->assign(array(
			'title' => $cat->title,
			'textid' => $cat->textid
		));

		//foruma kategoriju pievienošana
		if ($auth->level == 1) {
			$tpl->newBlock('forum-list-add');
			$tpl->assign(array(
				'id' => $cat->id
			));
		}

		$add = '';
		if (!im_mod()) {
			$add = ' AND `mods_only` = 0';
		}

		$forums = $db->get_results("SELECT `title`, `textid`, `icon`, `id`, `content`, `stat_topics`, `stat_com`, `mods_only_post` FROM `cat` WHERE `parent` = '$cat->id' AND `module` = 'list'" . $add . " ORDER BY `ordered` ASC");

		foreach ($forums as $forum) {
			if (!$forum->mods_only_post || im_mod()) {
				$fcategorys[] = array(
					'id' => $forum->id,
					'title' => $forum->title,
				);
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
				$mods = array();
				foreach ($finfo->mods as $mod) {
					$minfo = get_user($mod);
					$mods[] = '<a href="/user/' . $minfo->id . '">' . usercolor($minfo->nick, $minfo->level, false, $minfo->id) . '</a>';
				}
				$add .= implode(', ', $mods);
			}

			$tpl->newBlock('forum-item');

			if (empty($forum->icon)) {
				$forum->icon = $generic_f_icon;
			}

			$tpl->assign(array(
				'title' => $forum->title,
				'textid' => $forum->textid,
				'icon' => $forum->icon,
				'content' => $forum->content . $add,
				'posts' => $forum->stat_com,
				'topics' => $forum->stat_topics,
				'txt-posts' => lv_dsk($forum->stat_com, 'posts', 'posti'),
				'txt-topics' => lv_dsk($forum->stat_topics, 'tēma', 'tēmas')
			));



			if (!empty($topic)) {
				$author = get_user($topic->author);
				$tpl->assign(array(
					'date' => display_time_simple(strtotime($topic->bump)),
					'topic' => '<a href="/read/' . $topic->strid . '" title="' . htmlspecialchars($topic->title) . '">' . textlimit($topic->title, 32) . '</a>',
					'author' => '<a href="/user/' . $author->id . '">' . usercolor($author->nick, $author->level, false, $author->id) . '</a>'
				));
			}

			if ($auth->level == 1) {
				//foruma kategoriju kārtošana
				$tpl->assign(array(
					'uplink' => ' <a href="?moveup=' . $forum->id . '">&#8593;</a> ',
					'downlink' => ' <a href="?movedown=' . $forum->id . '">&#8595;</a> '
				));

				//foruma apakškategoriju pievienošana
				$tpl->assign(array(
					'addlink' => ' <a href="/forum-add/' . $forum->id . '">+pievienot</a> '
				));
			}

			$add = '';
			if (!im_mod()) {
				$add = ' AND `mods_only` = 0';
			}

			if (!empty($subcats)) {
				$tpl->newBlock('subcats');
				foreach ($subcats as $subcat) {
					$tpl->newBlock('subcats-node');
					$tpl->assign(array(
						'title' => $subcat->title,
						'textid' => $subcat->textid
					));
					$fcategorys[] = array(
						'id' => $subcat->id,
						'title' => '&nbsp;&nbsp;&raquo;&nbsp;' . $subcat->title,
					);


					$subcats2 = $db->get_results("SELECT `id`, `title` FROM `cat` WHERE `parent` = '$subcat->id' AND `module` = 'list'" . $add . " ORDER BY `ordered` ASC");

					if (!empty($subcats2)) {

						foreach ($subcats2 as $subcat2) {
							$fcategorys[] = array(
								'id' => $subcat2->id,
								'title' => '&nbsp;&nbsp;&nbsp;&nbsp;&raquo;&nbsp;' . $subcat2->title,
							);
						}
					}
				}
			}
		}
	}
}

//form
if ($auth->ok) {

	$tpl->newBlock('tinymce-enabled');
	$tpl->newBlock('forum-addtopic');
	$tpl->assign('forum-check', md5($category->title . $remote_salt . $auth->id));

	if (!empty($fcategorys)) {
		foreach ($fcategorys as $fcategory) {
			$tpl->newBlock('select-category');
			$sel = '';
			if ((isset($_GET['cat']) && $_GET['cat'] == $fcategory['id']) || (!isset($_GET['cat']) && $fcategory['id'] == 232)) {
				$sel = ' selected="selected"';
			}
			$tpl->assign(array(
				'id' => $fcategory['id'],
				'title' => $fcategory['title'],
				'sel' => $sel
			));
		}
	}
} else {
	$tpl->newBlock('forum-addtopic-no');
}

$pagepath = $category->title;
if ($category->parent) {
	$category2 = get_cat($category->parent);
	$pagepath = '<a href="/' . $category2->textid . '">' . $category2->title . '</a> / ' . $pagepath;
	$page_title = $page_title . ' | ' . $category2->title;
}

if($category->textid == 'index' && !empty($category->content)) {
	$tpl->newBlock('meta-description');
	$tpl->assign('description', htmlspecialchars($category->content));
}
