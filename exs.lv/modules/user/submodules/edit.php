<?php

/**
 * Lietotāja personīgā informācija
 */
$tpl->newBlock('user-profile-edit');

//write changes
if (isset($_POST['submit'])) {

	$user->skype = input2db($_POST['edit-skype'], 20);
	$user->yt_name = input2db($_POST['edit-yt_name'], 20);
	$user->twitter = input2db($_POST['edit-twitter'], 30);

	if ($user->karma >= 500 || im_mod() || $user->custom_title_paid) {
		$user->custom_title = input2db($_POST['edit-custom_title'], 32);
	}

	$user->web = '';
	if (!empty($_POST['edit-web'])) {
		if (substr($_POST['edit-web'], 0, 4) == 'www.') {
			$web = 'http://' . $_POST['edit-web'];
		} else {
			$web = $_POST['edit-web'];
		}
		if (filter_var($web, FILTER_VALIDATE_URL)) {
			$user->web = sanitize(filter_var($web, FILTER_SANITIZE_URL));
		}
	}

	$user->signature = htmlpost2db($_POST['edit-signature']);
	$user->about = htmlpost2db($_POST['edit-about']);

	$user->city = (int) $_POST['edit-city'];

	$db->update('users', $auth->id, array(
		'web' => $user->web,
		'city' => $user->city,
		'skype' => $user->skype,
		'rs_nick' => $user->rs_nick,
		'signature' => $user->signature,
		'about' => $user->about,
		'yt_name' => $user->yt_name,
		'twitter' => $user->twitter,
		'custom_title' => $user->custom_title
	));

	$auth->reset();
	update_karma($auth->id, true);

	set_flash('Izmaiņas saglabātas!', 'success');
	redirect('/user/edit');
}

//show form
$tpl->gotoBlock('user-profile-edit');
$tpl->assign(array(
	'user-nick' => $user->nick,
	'user-skype' => $user->skype,
	'user-yt_name' => $user->yt_name,
	'user-twitter' => $user->twitter,
	'user-web' => htmlspecialchars($user->web),
	'user-signature' => htmlspecialchars($user->signature),
	'user-date' => $user->date,
	'user-about' => htmlspecialchars($user->about)
));

if ($user->karma >= 500 || im_mod() || $user->custom_title_paid) {
	$tpl->newBlock('custom_title');
	$tpl->assign(array(
		'user-custom_title' => $user->custom_title,
	));
} else {
	$tpl->newBlock('custom_title_buy');
}

$citys = $db->get_results("SELECT * FROM `city` ORDER BY `id` ASC");

foreach ($citys as $city) {
	$tpl->newBlock('user-profile-edit-city');
	if ($user->city == $city->id) {
		$sel = ' selected="selected"';
	} else {
		$sel = '';
	}
	$tpl->assign(array(
		'city-id' => $city->id,
		'city-title' => $city->title,
		'city-sel' => $sel
	));
}

$tpl->assignGlobal(array(
	'user-id' => $user->id,
	'user-nick' => htmlspecialchars($user->nick),
	'active-tab-profile' => 'active',
	'profile-sel' => ' class="selected"'
));
$page_title = 'Tavs profils';

$tpl->newBlock('tinymce-enabled');
