<?php

/**
 * Sadaļa, kur lietotājs var izvēlēties,
 * no kurām foruma sadaļām nevēlas redzēt jaunumus
 */
$robotstag[] = 'noindex';

if (!$auth->ok) {
	set_flash('Jāielogojas!', 'error');
	redirect();
}

$add_css[] = 'forum.da938bac.min.css';

if (isset($_POST['submit-ignore']) && !empty($_POST['forum']) && is_array($_POST['forum'])) {
	$posted_ids = array_values(array_unique(array_filter(array_map('intval', array_keys($_POST['forum'])))));
	if (!empty($posted_ids)) {
		$ids_in = implode(',', $posted_ids);
		$valid_col = $db->get_col("SELECT `id` FROM `cat` WHERE `id` IN ($ids_in)");
		$valid_cats = $valid_col ? array_flip($valid_col) : [];

		$existing_col = $db->get_col("SELECT `category_id` FROM `cat_ignore` WHERE `user_id` = '$auth->id' AND `category_id` IN ($ids_in)");
		$existing_ignored = $existing_col ? array_flip($existing_col) : [];

		foreach ($_POST['forum'] as $forum => $status) {
			$forum = (int) $forum;
			if (!isset($valid_cats[$forum])) {
				continue;
			}
			if ($status == 1) {
				if (!isset($existing_ignored[$forum])) {
					$db->query("INSERT INTO `cat_ignore` (`user_id`,`category_id`) VALUES ('$auth->id','$forum')");
					$existing_ignored[$forum] = true;
				}
			} else {
				if (isset($existing_ignored[$forum])) {
					$db->query("DELETE FROM `cat_ignore` WHERE `user_id` = '$auth->id' AND `category_id` = '$forum'");
					unset($existing_ignored[$forum]);
				}
			}
		}
	}
}

$ignored_col = $db->get_col("SELECT `category_id` FROM `cat_ignore` WHERE `user_id` = '$auth->id'");
$ignored_ids = $ignored_col ? array_flip($ignored_col) : [];

$cats = $db->get_results("SELECT `id`,`title`,`textid` FROM `cat` WHERE `lang` = '$lang' AND `module` = 'forums' ORDER BY `ordered` ASC");

if (!empty($cats)) {
	$add = !im_mod() ? ' AND `mods_only` = 0' : '';
	$cat_ids = array_map(function ($c) { return (int) $c->id; }, $cats);
	$cat_ids_in = implode(',', $cat_ids);
	$all_forums = $db->get_results("SELECT `parent`, `title`, `textid`, `icon`, `id`, `content`, `stat_topics`, `stat_com` FROM `cat` WHERE (`lang` = '$lang' OR `lang` = 0) AND `parent` IN ($cat_ids_in) AND `module` = 'list'" . $add . " ORDER BY `ordered` ASC");
	$forums_by_parent = [];
	if (!empty($all_forums)) {
		foreach ($all_forums as $f) {
			$forums_by_parent[(int) $f->parent][] = $f;
		}
	}

	foreach ($cats as $cat) {
		$forums = $forums_by_parent[(int) $cat->id] ?? [];
		if ($forums) {
			$tpl->newBlock('forum-list');
			$tpl->assign([
				'title' => $cat->title,
				'textid' => $cat->textid
			]);

			foreach ($forums as $forum) {

				$tpl->newBlock('forum-item');

				if (isset($ignored_ids[$forum->id])) {
					$disabled = ' selected="selected"';
					$enabled = '';
				} else {
					$enabled = ' selected="selected"';
					$disabled = '';
				}

				if (empty($forum->icon)) {
					$forum->icon = $generic_f_icon;
				}

				$tpl->assign([
					'title' => $forum->title,
					'textid' => $forum->textid,
					'disabled' => $disabled,
					'enabled' => $enabled,
					'id' => $forum->id,
					'icon' => $forum->icon
				]);
			}
		}
	}
}

