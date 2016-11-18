<?php

if (!im_mod()) {
	set_flash('Tev nav pieejas šai sadaļai!', 'error');
	redirect();
}

if (isset($_GET['delete'])) {
	$delete = (int) $_GET['delete'];
	if ($delete) {
		$db->query("DELETE FROM wg_words WHERE id = '$delete' LIMIT 1");
	}
	redirect('/' . $category->textid);
}

if (isset($_GET['edit'])) {
	$edit = (int) $_GET['edit'];
	$fact = $db->get_row("SELECT * FROM wg_words WHERE id = '$edit'");
	if ($fact) {
		$suc = false;
		if (isset($_POST['edit-word'])) {
			$fact->word = sanitize(strtolower(trim($_POST['edit-word'])));
			$fact->hint = sanitize(trim($_POST['edit-hint']));
			$editfact = sanitize(trim($_POST['edit-fact']));
			if ($db->query("UPDATE wg_words SET word = ('$fact->word'), hint = ('$fact->hint') WHERE id = $edit")) {
				$suc = true;
			}
		}

		$tpl->newBlock("facts_admin-edit");
		$tpl->assign([
			'id' => $fact->id,
			'word' => stripslashes($fact->word),
			'hint' => stripslashes($fact->hint)
		]);

		if ($suc) {
			redirect('/' . $category->textid);
		}
	}
} else {

	$tpl->newBlock("facts_admin-add");
	if (isset($_POST['new-word']) && isset($_POST['new-hint'])) {
		$word = sanitize(strtolower(trim($_POST['new-word'])));
		$hint = sanitize(trim($_POST['new-hint']));
		if ($db->query("INSERT INTO wg_words (word,hint) VALUES ('$word','$hint')")) {
			$tpl->newBlock("facts_admin-success");
		}
	}
}

$facts = $db->get_results("SELECT * FROM wg_words ORDER BY id DESC");
if ($facts) {
	$tpl->newBlock("facts_admin-list");
	foreach ($facts as $fact) {
		$tpl->newBlock("facts_admin-list-node");
		$tpl->assign([
			'id' => $fact->id,
			'word' => stripslashes($fact->word),
			'hint' => stripslashes($fact->hint),
		]);
	}
}
