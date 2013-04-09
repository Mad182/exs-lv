<?php

if (isset($_GET['var1'])) {
	$userid = (int) $_GET['var1'];
	$inprofile = get_user($userid);
} elseif ($auth->ok) {
	$inprofile = get_user($auth->id);
}

if ($inprofile) {

	if ($auth->ok) {
		set_action($inprofile->nick . ' draugus');
	}

	include(CORE_PATH . '/includes/class.friend.php');
	$friend = new Friend();

	//confirm friendship
	if ($auth->ok && $inprofile->id == $auth->id && isset($_GET['confirm'])) {
		$confirm = (int) $_GET['confirm'];
		$friend->confirm_friendship($auth->id, $confirm);
	}

	//deny or delete friendship
	if ($auth->ok && $inprofile->id == $auth->id && isset($_GET['deny'])) {
		$deny = (int) $_GET['deny'];
		$friend->delete_friend($deny);
	}

	$tpl->newBlock('profile-menu');
	$tpl->assign('user-menu-add', ' draugi');

	$page_title = $inprofile->nick . ' | draugi';
	$tpl->assignGlobal(array(
		'user-id' => $inprofile->id,
		'user-nick' => htmlspecialchars($inprofile->nick),
		'active-tab-friends' => 'active'
	));

	$tpl->newBlock('user-friends');

	$friends = $db->get_results("SELECT id,friend1,friend2 FROM friends WHERE (friend1 = ('" . $inprofile->id . "') OR friend2 = ('" . $inprofile->id . "')) AND confirmed = '1' ORDER BY date_confirmed DESC");
	if ($friends) {
		foreach ($friends as $friend) {
			if ($friend->friend1 == $inprofile->id) {
				$theother = $friend->friend2;
			} else {
				$theother = $friend->friend1;
			}
			$friendinfo = get_user($theother);

			$avatar = get_avatar($friendinfo);

			$tpl->newBlock('user-friend-node');
			$tpl->assign(array(
				'friend-id' => $theother,
				'friend-nick' => usercolor($friendinfo->nick, $friendinfo->level),
				'friend-avatar' => $avatar,
				'friend-title' => htmlspecialchars($friendinfo->nick)
			));
			//cancel friendship
			if ($auth->ok && $inprofile->id == $auth->id) {
				$tpl->newBlock('user-friend-delete');
				$tpl->assign('friendship-id', $friend->id);
			}
		}
	}

	//pending
	if ($auth->ok && $inprofile->id == $auth->id) {
		$friendsp = $db->get_results("SELECT `id`,`friend1`,`friend2`,`date` FROM `friends` WHERE `friend2` = ('" . $inprofile->id . "') AND `confirmed` = '0' ORDER BY `date` DESC");
		if ($friendsp) {
			$tpl->newBlock('user-friend-pending');
			foreach ($friendsp as $friend) {
				$friendinfo = get_user($friend->friend1);

				$avatar = get_avatar($friendinfo);

				$tpl->newBlock('user-friend-pending-node');
				$tpl->assign(array(
					'friend-id' => $friend->friend1,
					'friend-date' => substr($friend->date, 0, 10),
					'friendship-id' => $friend->id,
					'friend-nick' => usercolor($friendinfo->nick, $friendinfo->level),
					'friend-avatar' => $avatar,
					'friend-title' => htmlspecialchars($friendinfo->nick)
				));
			}
		}
	}
} else {
	set_flash('Šāds lietotājs netika atrasts, iespējams kļūdains links!', 'error');
	redirect();
}

$pagepath = '';
