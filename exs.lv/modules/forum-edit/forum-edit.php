<?php

if ($auth->level != 1) {
	redirect();
}

if (isset($_GET['var1'])) {

	$edit = get_cat($_GET['var1']);

	if ($edit->module == 'list' && $edit->isforum) {

		if (isset($_POST['content']) && !empty($_POST['content'])) {
			$content = sanitize(htmlspecialchars(strip_tags($_POST['content'])));
			$db->query("UPDATE `cat` SET `content` = '$content' WHERE `id` = '$edit->id'");

			$parent = get_cat($edit->parent);
			set_flash('Kategorijas apraksts saglabāts!', 'success');
			redirect('/' . $parent->textid);
		}

		$tpl->newBlock('forum-edit');
		$tpl->assign(array(
			'content' => $edit->content,
			'title' => $edit->title
		));
	} else {
		set_flash('Kategorija, kuru tu mēģini rediģēt nav foruma sadaļa!', 'error');
		redirect();
	}
}
