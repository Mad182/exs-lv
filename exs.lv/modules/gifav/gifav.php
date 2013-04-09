<?php

if ($auth->ok) {
	$tpl->newBlock('av-list');
	$tpl->assign(array(
		'user-id' => $auth->id
	));
	$avs = $db->get_results("SELECT * FROM `animations` WHERE `user_id` = '0' ORDER BY `id` DESC");
	if ($avs) {
		foreach ($avs as $av) {
			$tpl->newBlock('av-node');
			$tpl->assign(array(
				'id' => $av->id,
				'image' => $av->image
			));
		}
	}

	$user = $db->get_row("SELECT * FROM users WHERE id = '$auth->id'");

	if ($user->credit >= 5) {
		$tpl->newBlock('av-buy');

		if (isset($_POST['avatarid'])) {
			$av = (int) $_POST['avatarid'];

			$avrow = $db->get_row("SELECT * FROM animations WHERE id = '$av'");

			if ($avrow->user_id == 0) {
				$db->query("UPDATE animations SET user_id = '$auth->id' WHERE id = '$av'");
				$db->query("UPDATE `users` SET credit = credit-5, av_alt = '1', avatar = '$avrow->image' WHERE id = '$auth->id'");
				redirect('/user/' . $auth->id);
			} else {
				set_flash('Izvēlētais avatars jau ir aizņemts!', 'error');
			}
		}
	}
} else {
	$tpl->newBlock('error-nologin');
}
?>
