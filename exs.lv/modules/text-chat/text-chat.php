<?php

$add_css[] = 'ajax-comments.css';

require_once(CORE_PATH . '/includes/ajax_comments.php');
if (isset($_GET['ajax']) || isset($_POST['ajax'])) {
	echo comments_block('cat-' . $category->id, $_GET['ajax']);
	exit;
}

$tpl->assignInclude('module-head', 'modules/' . $category->module . '/head.tpl');
$tpl->prepare();

if ($auth->ok) {

	if (!$lastid = (int) $db->get_var("SELECT id FROM ajax_comments WHERE parent = 'cat-" . $category->id . "' ORDER BY id DESC LIMIT 1")) {
		$lastid = 1;
	}

	$tpl->newBlock('chat-head');
	$tpl->assign(array(
		'slug' => $category->textid,
		'lastid' => $lastid
	));
}

if (isset($_GET['edit']) && im_mod()) {

	if (isset($_POST['content'])) {
		$content = htmlpost2db($_POST['content']);
		$db->query("UPDATE `cat` SET `content` = '$content' WHERE `id` = '$category->id' LIMIT 1");
		get_cat($category->id, true);
		get_cat($category->textid, true);
		redirect('/' . $category->textid);
	}

	$tpl->newBlock('text-edit');
	$tpl->assign(array(
		'title' => $category->title,
		'content' => htmlspecialchars($category->content),
	));
} else {
	$tpl->newBlock('text-body');
	$tpl->assign(array(
		'title' => $category->title,
		'content' => $category->content,
		'comments' => comments_block('cat-' . $category->id)
	));

	if (im_mod()) {
		$tpl->newBlock('options');
	}
}

