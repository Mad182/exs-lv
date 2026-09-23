<?php

/**
 * Lietotāja grāmatzīmes (raksti)
 */
$robotstag = ['noindex', 'follow'];

if (isset($_GET['var1'])) {
	$userid = (int) $_GET['var1'];
	$inprofile = get_user($userid);
} elseif ($auth->ok) {
	$inprofile = get_user($auth->id);
}

if (!empty($inprofile) && empty($inprofile->deleted)) {

	profile_menu($inprofile, 'bookmarks', 'grāmatzīmes');

	if ($inprofile->private && !$auth->ok) {
		$robotstag = ['noindex', 'nofollow'];
		$tpl->newBlock('user-bookmarks-private');
	} else {
		$tpl->newBlock('user-bookmarks');

	//delete
	if ($auth->ok && $auth->id == $inprofile->id && isset($_GET['delete'])) {
		$delete = (int) $_GET['delete'];
		$db->query("DELETE FROM `bookmarks` WHERE `id` = '$delete' AND `userid` = '$auth->id'");
		count_bookmarks($auth->id);
		redirect('/bookmarks/' . $auth->id);
	}

	$articles = $db->get_results("SELECT id, pageid FROM bookmarks WHERE userid = ('" . $inprofile->id . "') ORDER BY id DESC");
	if ($articles) {
		$tpl->newBlock('user-bookmarks-list');
		foreach ($articles as $article) {
			$info = $db->get_row("SELECT id,title,date,posts,strid FROM pages WHERE id = ('" . $article->pageid . "')");
	
			if(!empty($info)) {
				$tpl->newBlock('user-bookmarks-node');

				//delete link
				$delete = '';
				if ($auth->ok && $auth->id == $inprofile->id) {
					$delete = '[<a title="Dzēst no izlases" class="red confirm" href="?delete=' . $article->id . '"><img src="/bildes/x.png" alt="x" title="Dzēst no izlases" /></a>]';
				}

				$tpl->assign([
					'id' => $info->id,
					'url' => '/read/' . $info->strid,
					'title' => $info->title,
					'date' => substr($info->date, 0, 10),
					'posts' => $info->posts,
					'delete' => $delete
				]);
			}
		}
	} else {
		$robotstag = ['noindex', 'follow'];
		$tpl->newBlock('empty-bookmarks');
	}
	}
	$pagepath = '';
} else {
	set_flash('Kļūda: profils nav atrasts!');
	redirect();
}
