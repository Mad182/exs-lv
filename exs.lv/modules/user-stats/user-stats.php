<?php

$robotstag[] = 'noindex';

if (isset($_GET['var1']) && $_GET['var1'] == 'json') {

	if (isset($_GET['var2'])) {
		$userid = (int) $_GET['var2'];
		$user = get_user($userid);

		if ($user) {

			$usernames = $db->get_results("SELECT * FROM `nick_history` WHERE `user_id` = $user->id");
			$history = array();
			if (!empty($usernames)) {
				foreach ($usernames as $username) {
					$history[] = array(
						'nick' => h($username->nick),
						'changed' => $username->changed
					);
				}
			}

			$avatar = array(
				'small' => 'https:' . get_avatar($user, 's'),
				'medium' => 'https:' . get_avatar($user, 'm'),
				'large' => 'https:' . get_avatar($user, 'l'),
			);

			$days = ceil((time() - strtotime($user->date)) / 60 / 60 / 24);

			$types = array(
				0 => 'user',
				1 => 'admin',
				2 => 'moderator',
				3 => 'journalist',
				4 => 'vip',
				5 => 'bot'
			);

			$data = array(
				'id' => (int) $user->id,
				'nick' => h($user->nick),
				'posts' => (int) $user->posts,
				'karma' => (int) $user->karma,
				'days' => (int) $days,
				'type' => $types[$user->level],
				'last_seen' => $user->lastseen,
				'nick_history' => $history,
				'avatar' => $avatar
			);

			echo json_encode($data);
			exit;
		} else {
			echo 'err: not found';
		}
	}

	echo 'err: no user';
	exit;
} else {
	echo 'err: no format';
	exit;
}

