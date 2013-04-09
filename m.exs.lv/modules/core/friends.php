<?php
$userid = (int)$_GET['f'];
$user = get_user($userid);
if($user) {

	//for profile sidebox
	$inprofile = $user;

  include('includes/class.friend.php');
	$friend = new Friend();

	//confirm friendship
	if($auth->ok && $user->id == $auth->id && isset($_GET['confirm'])) {
		$confirm = (int)$_GET['confirm'];
		$friend->confirm_friendship($auth->id,$confirm);
	}
	
	//deny or delete friendship
	if($auth->ok && $user->id == $auth->id && isset($_GET['deny'])) {
		$deny = (int)$_GET['deny'];
		$friend->delete_friend($deny);
	}

	$tpl->assignInclude('module-currrent','modules/core/friends.tpl');
	$tpl->prepare();
	$tpl->newBlock('profile-menu');
	
	if($user->yt_name) {
		$tpl->newBlock('yt-tab');
	}
	
	$page_title = $user->nick . ' draugi';
	$tpl->assignGlobal(array(
	  'user-id' => $user->id,
		'user-nick' => htmlspecialchars($user->nick),
		'active-tab-friends' => ' activeTab'
	));
	
	$tpl->newBlock('user-friends');
	
	$friends = $db->get_results("SELECT id,friend1,friend2 FROM friends WHERE (friend1 = ('" . $user->id . "') OR friend2 = ('" . $user->id . "')) AND confirmed = '1' ORDER BY date_confirmed DESC");
	if($friends) {
	  foreach ($friends as $friend) {
			if($friend->friend1 == $user->id) {
				$theother = $friend->friend2;
			} else {
				$theother = $friend->friend1;
			}
		  $friendinfo = get_user($theother);

			//default avatar image
			if($friendinfo->avatar == '') {$friendinfo->avatar = $config['default_user_avatar'];}

			$tpl->newBlock('user-friend-node');
			$tpl->assign(array(
	  		'friend-id' => $theother,
			  'friend-nick' => usercolor($friendinfo->nick,$friendinfo->level),
			  'friend-avatar' => $friendinfo->avatar,
			  'friend-title' => htmlspecialchars($friendinfo->nick)
			));
			//cancel friendship
			if($auth->ok && $user->id == $auth->id) {
				$tpl->newBlock('user-friend-delete');
				$tpl->assign('friendship-id',$friend->id);
			}
		}
	}
	
	//pending
	if($auth->ok && $user->id == $auth->id) {
		$friendsp = $db->get_results("SELECT id,friend1,friend2,date FROM friends WHERE friend2 = ('" . $user->id . "') AND confirmed = '0' ORDER BY date DESC");
		if($friendsp) {
			$tpl->newBlock('user-friend-pending');
		  foreach ($friendsp as $friend) {
			  $friendinfo = get_user($friend->friend1);
				//default avatar image
				if($friendinfo->avatar == '') {$friendinfo->avatar = $config['default_user_avatar'];}
				$tpl->newBlock('user-friend-pending-node');
				$tpl->assign(array(
		  		'friend-id' => $friend->friend1,
		  		'friend-date' => substr($friend->date,0,10),
		  		'friendship-id' => $friend->id,
				  'friend-nick' => usercolor($friendinfo->nick,$friendinfo->level),
				  'friend-avatar' => $friendinfo->avatar,
				  'friend-title' => htmlspecialchars($friendinfo->nick)
				));
			}
		}
	}

  $tpl->newBlock('friends-css');

} else {
	$tpl->newBlock('error-nouser');
	$page_title = 'Kļūda: profils nav atrasts!';
}
?>