<?php

/**
 * Animated avatars
 */
$tpl->assign(array(
	'user-id' => $auth->id
));

$owned = false;
$avs = $db->get_results("SELECT * FROM `animations` WHERE `user_id` = '0' OR `user_id` = '$auth->id' ORDER BY `user_id` DESC, `id` DESC");
if ($avs) {
	foreach ($avs as $av) {
		$tpl->newBlock('av-node');

		$own = '';
		if ($auth->ok && $auth->id == $av->user_id) {
			$own = 'background:#3a6;';
			$owned = true;
		}

		$tpl->assign(array(
			'id' => $av->id,
			'image' => $av->image,
			'owned' => $own,
		));
	}
}

$user = $db->get_row("SELECT * FROM users WHERE id = '$auth->id'");

if ($auth->ok && ($user->credit >= 5 || $owned)) {
	$tpl->newBlock('av-buy');

	if (isset($_POST['avatarid'])) {
		$av = (int) $_POST['avatarid'];

		$avrow = $db->get_row("SELECT * FROM `animations` WHERE `id` = '$av'");

		if ($avrow->user_id == 0 && $user->credit >= 5) {
			$db->query("UPDATE animations SET user_id = '$auth->id' WHERE id = '$av'");
			$db->query("UPDATE `users` SET credit = credit-5, av_alt = '1', avatar = '$avrow->image' WHERE id = '$auth->id'");
			redirect('/user/' . $auth->id);
		} elseif ($avrow->user_id == $auth->id) {

			// support for old avatars with one size
			$av_alt = 0;
			if (file_exists(CORE_PATH . '/dati/bildes/u_large/' . $avrow->image)) {
				$av_alt = 1;
			}

			$db->query("UPDATE `users` SET `av_alt` = '$av_alt', `avatar` = '$avrow->image' WHERE `id` = '$auth->id'");
			redirect('/user/' . $auth->id);
		} else {
			set_flash('Izvēlētais avatars jau ir aizņemts vai nepietiek exs kredīta!', 'error');
		}
	}
} elseif ($auth->ok) {
	$tpl->newBlock('av-credit');
}
