<?php

/**
 * e-pasta adreses maiņa
 */
$robotstag[] = 'noindex';

deny_proxies();

$tpl->newBlock('user-profile-delete');

//write changes
if (isset($_POST['submit'])) {

	if (password_verify($_POST['password-old'], $inprofile->password) || ($inprofile->password == '' && (!empty($inprofile->draugiem_id) || !empty($inprofile->facebook_id) || !empty($inprofile->twitter_id))) && check_token('delete', $_POST['xsrf_token'])) {

		$auth->log('Izdzēsa profilu (' .  $auth->nick . ')', 'users', $auth->id);

		$db->query("UPDATE users SET 
				`nick` = 'Dzēsts #" . $auth->id. "',
				`pwd` = '',
				`password` = '',
				`mail` = '',
				`mail_confirmed` = null,
				`lastseen` = `date`,
				`avatar` = '',
				`av_alt` = '0',
				`level` = 0,
				`signature` = '',
				`skype` = '',
				`web` = '',
				`about` = '',
				`custom_title` = '',
				`yt_name` = '',
				`twitter` = '',
				`last_action` = '',
				`draugiem_id` = 0,
				`rating` = 0,
				`facebook_id` = '0',
				`twitter_id` = null,
				`steam_id` = null,
				`persona` = '',
				`token` = '',
				`reset_token` = '',
				`lastfm_username` = null,
				`lastfm_sessionkey` = null,
				`lastfm_subscriber` = null,
				`lastfm_token` = null,
				`lastfm_updated` = null,
				`city` = 0,
				`connected_profiles` = '',
				`user_agent` = '',
				`deleted` = 1
				WHERE id = '$auth->id'");

		$db->query("DELETE FROM `clans_members` WHERE `user` = '$auth->id'");
		$db->query("DELETE FROM `banned` WHERE `user_id` = '$auth->id'");
		$db->query("DELETE FROM `warns` WHERE `user_id` = '$auth->id'");
		$db->query("DELETE FROM `notify` WHERE `user_id` = '$auth->id'");
		$db->query("DELETE FROM `notes` WHERE `user_id` = '$auth->id'");
		$db->query("DELETE FROM `cat_moderators` WHERE `user_id` = '$auth->id'");
		$db->query("DELETE FROM `viewprofile` WHERE `profile` = '$auth->id'");
		$db->query("DELETE FROM `viewprofile` WHERE `viewer` = '$auth->id'");
		$db->query("DELETE FROM `friends` WHERE `friend1` = '$auth->id'");
		$db->query("DELETE FROM `friends` WHERE `friend2` = '$auth->id'");
		$db->query("DELETE FROM `bookmarks` WHERE `userid` = '$auth->id'");
		$db->query("DELETE FROM `autoawards` WHERE `user_id` = '$auth->id'");
		$db->query("DELETE FROM `lastfm_tracks` WHERE `user_id` = '$auth->id'");
		$db->query("DELETE FROM `userlogs` WHERE `user` = '$auth->id'");
		$db->query("DELETE FROM `images` WHERE `uid` = '$auth->id'");
		$db->query("UPDATE `comments` SET `removed` = 1 WHERE `author` = '$auth->id'");
		$db->query("UPDATE `galcom` SET `removed` = 1 WHERE `author` = '$auth->id'");
		$db->query("UPDATE `miniblog` SET `private` = 1 WHERE `author` = '$auth->id'");

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

