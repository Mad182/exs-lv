<?php

/**
 * profila dzēšana
 */
$robotstag[] = 'noindex';

deny_proxies();

$tpl->newBlock('user-profile-delete');

//write changes
if (isset($_POST['submit'])) {

	if (password_verify($_POST['password-old'], $inprofile->password) || ($inprofile->password == '' && (!empty($inprofile->draugiem_id) || !empty($inprofile->facebook_id) || !empty($inprofile->twitter_id))) && check_token('delete', $_POST['xsrf_token'])) {

		$auth->log('Izdzēsa profilu (' .  $auth->nick . ')', 'users', $auth->id);

		$db->query("DELETE FROM `users` WHERE `id` = '$auth->id'");
		$db->query("DELETE FROM `clans_members` WHERE `user` = '$auth->id'");
		$db->query("DELETE FROM `banned` WHERE `user_id` = '$auth->id'");
		$db->query("DELETE FROM `warns` WHERE `user_id` = '$auth->id'");
		$db->query("DELETE FROM `notify` WHERE `user_id` = '$auth->id'");
		$db->query("DELETE FROM `notes` WHERE `user_id` = '$auth->id'");
		$db->query("DELETE FROM `cat_moderators` WHERE `user_id` = '$auth->id'");
		$db->query("DELETE FROM `steam_player_info` WHERE `user_id` = '$auth->id'");
		$db->query("DELETE FROM `viewprofile` WHERE `profile` = '$auth->id'");
		$db->query("DELETE FROM `viewprofile` WHERE `viewer` = '$auth->id'");
		$db->query("DELETE FROM `friends` WHERE `friend1` = '$auth->id'");
		$db->query("DELETE FROM `friends` WHERE `friend2` = '$auth->id'");
		$db->query("DELETE FROM `bookmarks` WHERE `userid` = '$auth->id'");
		$db->query("DELETE FROM `autoawards` WHERE `user_id` = '$auth->id'");
		$db->query("DELETE FROM `lastfm_tracks` WHERE `user_id` = '$auth->id'");
		$db->query("DELETE FROM `userlogs` WHERE `user` = '$auth->id'");
		$db->query("DELETE FROM `images` WHERE `uid` = '$auth->id'");
		$db->query("DELETE FROM `pm` WHERE `from_uid` = '$auth->id'");
		$db->query("DELETE FROM `comments` WHERE `author` = '$auth->id'");
		$db->query("DELETE FROM `galcom` WHERE `author` = '$auth->id'");
		$db->query("DELETE FROM `miniblog` WHERE `author` = '$auth->id'");
		$db->query("DELETE FROM `pages` WHERE `author` = '$auth->id'");

		//refresh in memcached
		get_user($auth->id, true);

		$auth->logout();
		redirect('/');

	} else {
		set_flash('<strong>Kļūda:</strong> parole ievadīta nepareizi!', 'error');
	}

	redirect('/user/delete');
}

//show form
$tpl->gotoBlock('user-profile-delete');
$tpl->assign([
	'xsrf' => make_token('delete')
]);

$page_title = 'Profila dzēšana';

