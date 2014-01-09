<?php

$userid = (int) $_GET['b'];
$user = get_user($userid);
if ($user) {
	//for profile sidebox
	$inprofile = $user;

	$tpl->assignInclude('module-currrent', 'modules/core/bookmarks.tpl');
	$tpl->prepare();
	$tpl->newBlock('profile-menu');

	if ($user->yt_name) {
		$tpl->newBlock('yt-tab');
	}

	$page_title = $user->nick . ' grāmatzīmes';
	$tpl->assignGlobal(array(
		'user-id' => $user->id,
		'user-nick' => htmlspecialchars($user->nick),
		'active-tab-bookmarks' => ' activeTab'
	));
	$tpl->newBlock('user-bookmarks');

	//delete
	if ($auth->ok && $auth->id == $userid && isset($_GET['delete'])) {
		$delete = (int) $_GET['delete'];
		$db->query("DELETE FROM bookmarks WHERE id = '$delete' AND userid = '$auth->id'");
		header('Location: /?b=' . $auth->id);
		exit;
	}

	$articles = $db->get_results("SELECT id,pageid FROM bookmarks WHERE userid = ('" . $user->id . "') ORDER BY id DESC");
	if ($articles) {
		$tpl->newBlock('user-bookmarks-list');
		foreach ($articles as $article) {
			$info = $db->get_row("SELECT id,title,date,posts FROM pages WHERE id = ('" . $article->pageid . "')");
			$tpl->newBlock('user-bookmarks-node');

			//delete link
			$delete = '';
			if ($auth->ok && $auth->id == $userid) {
				$delete = '[<a title="Dzēst no izlases" class="sarkans" href="/?b=' . $auth->id . '&amp;delete=' . $article->id . '"><img src="/bildes/x.png" alt="x" title="Dzēst no izlases" /></a>]';
			}

			$tpl->assign(array(
				'articles-node-id' => $info->id,
				'node-url' => mkurl('page', $info->id, $info->title),
				'articles-node-title' => $info->title,
				'articles-node-date' => substr($info->date, 0, 10),
				'articles-node-posts' => $info->posts,
				'articles-node-delete' => $delete
			));
		}
	}
} else {
	$tpl->newBlock('error-nouser');
	$page_title = 'Kļūda: profils nav atrasts!';
}
?>