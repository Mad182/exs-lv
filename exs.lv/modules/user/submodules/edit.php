<?php

/**
 * Lietotāja personīgā informācija
 */
$tpl->newBlock('user-profile-edit');

//write changes
if (isset($_POST['submit']) && check_token('edituser', $_POST['xsrf_token'])) {

	$inprofile->skype = input2db($_POST['edit-skype'], 20);
	$inprofile->yt_name = input2db($_POST['edit-yt_name'], 20);
	$inprofile->twitter = input2db($_POST['edit-twitter'], 30);

	if ($inprofile->karma >= 500 || im_mod() || $inprofile->custom_title_paid) {
		$inprofile->custom_title = input2db($_POST['edit-custom_title'], 32);
	}

	$inprofile->web = '';
	if (!empty($_POST['edit-web']) && $auth->posts >= 10) {
		if (substr($_POST['edit-web'], 0, 4) != 'http') {
			$web = 'http://' . $_POST['edit-web'];
		} else {
			$web = $_POST['edit-web'];
		}
		if (filter_var($web, FILTER_VALIDATE_URL) && curl_get($web)) {
			$inprofile->web = sanitize(filter_var($web, FILTER_SANITIZE_URL));
		}
	}

	//some users are not allowed to add signature
	if (!empty($inprofile->allow_signature)) {
		$inprofile->signature = htmlpost2db($_POST['edit-signature']);
		$inprofile->about = htmlpost2db($_POST['edit-about']);
	}

	$inprofile->city = (int) $_POST['edit-city'];

	$db->update('users', $auth->id, array(
		'web' => $inprofile->web,
		'city' => $inprofile->city,
		'skype' => $inprofile->skype,
		'rs_nick' => $inprofile->rs_nick,
		'signature' => $inprofile->signature,
		'about' => $inprofile->about,
		'yt_name' => $inprofile->yt_name,
		'twitter' => $inprofile->twitter,
		'custom_title' => $inprofile->custom_title
	));

	$auth->reset();
	update_karma($auth->id, true);

	set_flash('Izmaiņas saglabātas!', 'success');
	redirect('/user/edit');
}

//show form
$tpl->gotoBlock('user-profile-edit');
$tpl->assign(array(
	'user-nick' => $inprofile->nick,
	'user-skype' => $inprofile->skype,
	'user-yt_name' => $inprofile->yt_name,
	'user-twitter' => $inprofile->twitter,
	'user-web' => h($inprofile->web),
	'user-date' => $inprofile->date,
	'xsrf' => make_token('edituser')
));

if (!empty($inprofile->allow_signature)) {
	$tpl->newBlock('sig-about-edit');
	$tpl->assign(array(
		'user-signature' => h($inprofile->signature),
		'user-about' => h($inprofile->about)
	));
} else {
	$tpl->newBlock('sig-about-disabled');
}

if ($inprofile->karma >= 500 || im_mod() || $inprofile->custom_title_paid) {
	$tpl->newBlock('custom_title');
	$tpl->assign(array(
		'user-custom_title' => $inprofile->custom_title,
	));
} else {
	$tpl->newBlock('custom_title_buy');
}

$citys = $db->get_results("SELECT * FROM `city` ORDER BY `id` ASC");

foreach ($citys as $city) {
	$tpl->newBlock('user-profile-edit-city');
	if ($inprofile->city == $city->id) {
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

$page_title = 'Tavs profils';

$tpl->newBlock('tinymce-enabled');

