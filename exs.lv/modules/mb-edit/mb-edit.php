<?php

if ($auth->ok && isset($_GET['var1'])) {

	$mbid = intval($_GET['var1']);
	$mb = $db->get_row("SELECT * FROM `miniblog` WHERE `id` = '$mbid' AND `removed` = 0 AND `lang` = '$lang'");

	if (!empty($mb) &&
			(strtotime($mb->date) > time() - 1860 || $auth->level == 1 || $auth->id == 115) &&
			(im_mod() || (!$mb->closed && $auth->karma >= $min_post_edit && $mb->author == $auth->id))) {

		if (isset($_POST['submit-changes']) && !empty($_POST['text'])) {
			$text = htmlpost2db($_POST['text']);
			$db->query("UPDATE `miniblog` SET
					`text` = '$text',
					`edit_time` = " . time() . ",
					`edit_user` = $auth->id,
					`edit_times` = `edit_times`+1
				WHERE `id` = $mb->id");

			$auth->log('Laboja miniblogu', 'miniblog', $mb->id);

			$newpost = $db->get_row("SELECT * FROM `miniblog` WHERE id = '$mb->id'");
			if (!empty($newpost->parent)) {
				$parentid = $newpost->parent;
			} else {
				$parentid = $newpost->id;
			}
			if ($newpost->type == 'junk') {
				$type = 'junk';
			} elseif ($newpost->groupid) {
				$type = 'group';
			} else {
				$type = 'mb';
			}
			$newpost->text = mention($newpost->text, '#', $type, $parentid);
			$db->query("UPDATE `miniblog` SET `text` = '" . sanitize($newpost->text) . "' WHERE id = '$newpost->id'");


			return2mb($mb);
		}

		$tpl->newBlock('mb-edit');
		$tpl->assign(array(
			'id' => $mb->id,
			'text' => htmlspecialchars($mb->text),
			'cat-url' => $category->textid
		));
		$tpl->newBlock('tinymce-enabled');
	} else {
		if (strtotime($mb->date) < time() - 1860) {
			set_flash('Šo ierakstu vairs nevar labot!', 'error');
		} else {
			set_flash('Tev nav tiesību labot šo ierakstu!', 'error');
		}
		redirect();
	}
} else {
	if (!$auth->ok) {
		set_flash('Jāielogojas, lai labotu ierakstu!', 'error');
	}
	redirect();
}
