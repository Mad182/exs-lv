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

$add_css[] = 'forum.css';

if (isset($_POST['submit-ignore'])) {
	foreach ($_POST['forum'] as $forum => $status) {
		$forum = (int) $forum;
		if ($db->get_var("SELECT count(*) FROM `cat` WHERE `id` = '$forum'")) {
			if ($status == 1) {
				if (!$db->get_var("SELECT count(*) FROM `cat_ignore` WHERE  `user_id` = '$auth->id' AND `category_id` = '$forum'")) {
					$db->query("INSERT INTO `cat_ignore` (`user_id`,`category_id`) VALUES ('$auth->id','$forum')");
				}
			} else {
				if ($id = $db->get_var("SELECT `id` FROM `cat_ignore` WHERE `user_id` = '$auth->id' AND `category_id` = '$forum'")) {
					$db->query("DELETE FROM `cat_ignore` WHERE `user_id` = '$auth->id' AND `category_id` = '$forum'");
				}
			}
		}
	}
}

$cats = $db->get_results("SELECT `id`,`title`,`textid` FROM `cat` WHERE `lang` = '$lang' AND `module` = 'forums' ORDER BY `ordered` ASC");

if (!empty($cats)) {
	foreach ($cats as $cat) {

		$add = '';
		if (!im_mod()) {
			$add = ' AND `mods_only` = 0';
		}

		$forums = $db->get_results("SELECT `title`, `textid`, `icon`, `id`, `content`, `stat_topics`, `stat_com` FROM `cat` WHERE (`lang` = '$lang' OR `lang` = 0) AND `parent` = '$cat->id' AND `module` = 'list'" . $add . " ORDER BY `ordered` ASC");
		if ($forums) {
			$tpl->newBlock('forum-list');
			$tpl->assign([
				'title' => $cat->title,
				'textid' => $cat->textid
			]);

			foreach ($forums as $forum) {

				$tpl->newBlock('forum-item');

				if ($db->get_var("SELECT count(*) FROM `cat_ignore` WHERE `user_id` = '$auth->id' AND `category_id` = '$forum->id'")) {
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

