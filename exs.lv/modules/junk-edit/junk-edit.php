<?php

if (im_mod() && isset($_GET['var1'])) {

	$mbid = intval($_GET['var1']);
	$mb = $db->get_row("SELECT * FROM `junk` WHERE `id` = '$mbid' AND `removed` = 0 AND `lang` = '$lang'");

	if (!empty($mb)) {

		if (isset($_POST['submit-changes']) && !empty($_POST['title'])) {

			$title = sanitize(nl2br(h(strip_tags($_POST['title']))));
			$db->query("UPDATE `junk` SET
					`title` = '$title',
					`edit_time` = " . time() . ",
					`edit_user` = $auth->id,
					`edit_times` = `edit_times`+1
				WHERE `id` = $mb->id");

			$auth->log('Laboja junk', 'junk', $mb->id);

			redirect('/junk/' . $mb->id);
		}

		$tpl->newBlock('mb-edit');
		$tpl->assign([
			'id' => $mb->id,
			'title' => h($mb->title),
			'cat-url' => $category->textid
		]);
	}
} else {
	if (!$auth->ok) {
		set_flash('Jāielogojas, lai labotu!', 'error');
	}
	redirect();
}
