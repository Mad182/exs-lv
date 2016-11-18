<?php

if ($auth->ok) {

	$page = (int) $_GET['var1'];
	$comment = (int) $_GET['var2'];

	$page = $db->get_row("SELECT * FROM pages WHERE id = '$page'");

	if (!$page) {
		die('Kļūdains lapas ID');
	}

	if ($page->closed) {
		die('Komentāri ir slēgti');
	}

	$comment = $db->get_row("SELECT * FROM comments WHERE id = '$comment' AND pid = '$page->id'");

	if (!$comment) {
		die('Kļūdains lapas vai komentāra ID');
	}

	$author = $db->get_row("SELECT * FROM users WHERE id = '$comment->author'");

	$tpl->newBlock('rpl-form');
	$tpl->assign([
		'page' => $page->id,
		'comment' => $comment->id,
		'nick' => h($author->nick),
		'xsrf' => make_token('reply')
	]);
} else {
	die('Neesi ielogojies :/');
}


$tpl->printToScreen();
exit;

