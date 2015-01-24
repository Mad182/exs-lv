<?php

if (!$auth->ok || ($auth->level != 1 && $auth->level != 4)) {
	redirect();
}

if (isset($_GET['game'])) {
	$gameid = (int) $_GET['game'];
	$game = $db->get_row("SELECT * FROM ig_games WHERE id = '$gameid'");

	if ($game) {

		$tpl->newBlock('ig-newimage');

		if (isset($_POST['newimage-title'])) {
			$newtitle = sanitize(h(trim($_POST['newimage-title'])));
			$newdif = (int) $_POST['newimage-dif'];
			if (!$newdif) {
				$newdif = 2;
			}
			$newhint = sanitize(h(trim($_POST['newimage-hint'])));
			$newimage = sanitize(h(trim($_POST['newimage-image'])));
			if ($db->query("INSERT INTO ig_items (game_id,image,title,hint,dif) VALUES ('$game->id','$newimage','$newtitle','$newhint','$newdif')")) {
				$tpl->newBlock('ig-newimage-success');
			}
		}

		if (isset($_GET['delete'])) {
			$delete = (int) $_GET['delete'];
			$db->query("DELETE FROM ig_items WHERE id = '$delete' AND game_id = '$game->id' LIMIT 1");
		}

		$images = $db->get_results("SELECT * FROM ig_items WHERE game_id = '$gameid' ORDER BY id DESC");

		if ($images) {

			$tpl->newBlock('ig-listimages');
			foreach ($images as $image) {
				$tpl->newBlock('ig-listimages-node');
				$tpl->assign(array(
					'listimage-id' => $image->id,
					'listimage-title' => $image->title,
					'listimage-dif' => $image->dif,
					'listimage-image' => $image->image,
					'listimage-game_id' => $image->game_id,
					'listimage-categoryid' => $category->id,
				));
			}
		}
	}
} else {

	if (isset($_POST['newgame-date'])) {
		$day = date('Y-m-d', strtotime($_POST['newgame-date']));
		$db->query("INSERT INTO ig_games (date) VALUES ('$day')");
		redirect('/?c=' . $category->id);
	}

	$tpl->newBlock('ig-newgame');

	$games = $db->get_results("SELECT * FROM ig_games ORDER BY date DESC");
	if ($games) {
		$tpl->newBlock('ig-listgame');
		foreach ($games as $game) {
			$tpl->newBlock('ig-listgame-node');
			$tpl->assign(array(
				'listgame-id' => $game->id,
				'listgame-date' => $game->date,
				'listgame-categoryid' => $category->id,
			));
		}
	}
}
?>
