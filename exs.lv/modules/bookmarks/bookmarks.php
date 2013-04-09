<?php

$tpl->newBlock('profile-menu');
$tpl->assign('user-menu-add', ' grāmatzīmes');

if (isset($_GET['var1'])) {
	$userid = (int) $_GET['var1'];
} else {
	$userid = $auth->id;
}

if ($inprofile = get_user($userid)) {

	$page_title = $inprofile->nick . ' grāmatzīmes';
	$tpl->assignGlobal(array(
		'user-id' => $inprofile->id,
		'user-nick' => htmlspecialchars($inprofile->nick),
		'active-tab-bookmarks' => 'active'
	));
	$tpl->newBlock('user-bookmarks');

	//delete
	if ($auth->ok && $auth->id == $inprofile->id && isset($_GET['delete'])) {
		$delete = (int) $_GET['delete'];
		$db->query("DELETE FROM bookmarks WHERE id = '$delete' AND userid = '$auth->id'");
		redirect('/bookmarks/' . $auth->id);
	}

	$articles = $db->get_results("SELECT id,pageid FROM bookmarks WHERE userid = ('" . $inprofile->id . "') ORDER BY id DESC");
	if ($articles) {
		$tpl->newBlock('user-bookmarks-list');
		foreach ($articles as $article) {
			$info = $db->get_row("SELECT id,title,date,posts,strid FROM pages WHERE id = ('" . $article->pageid . "')");
			$tpl->newBlock('user-bookmarks-node');

			//delete link
			$delete = '';
			if ($auth->ok && $auth->id == $inprofile->id) {
				$delete = '[<a title="Dzēst no izlases" class="red confirm" href="?delete=' . $article->id . '"><img src="/bildes/x.png" alt="x" title="Dzēst no izlases" /></a>]';
			}

			$tpl->assign(array(
				'id' => $info->id,
				'url' => '/read/' . $info->strid,
				'title' => $info->title,
				'date' => substr($info->date, 0, 10),
				'posts' => $info->posts,
				'delete' => $delete
			));
		}
	}
	$pagepath = '';
} else {
	set_flash('Kļūda: profils nav atrasts!');
	redirect();
}

