<?php

/**
 * Tekstuālās sadaļas
 *
 * Attēlo tekstu no content lauka cat tabulā
 */
if (isset($_GET['edit']) && im_mod()) {

	if (isset($_POST['content'])) {
		$content = htmlpost2db($_POST['content']);
		$db->query("UPDATE `cat` SET `content` = '$content' WHERE `id` = '$category->id' LIMIT 1");
		get_cat($category->id, true);
		get_cat($category->textid, true);
		header('Location: /' . $category->textid);
		exit;
	}

	$tpl->newBlock('text-edit');
	$tpl->assign(array(
		'title' => $category->title,
		'content' => h($category->content)
	));
	$tpl->newBlock('tinymce-enabled');
} else {
	$tpl->newBlock('text-body');
	$tpl->assign(array(
		'title' => $category->title,
		'content' => $category->content
	));

	if (im_mod()) {
		$tpl->newBlock('options');
	}
}
