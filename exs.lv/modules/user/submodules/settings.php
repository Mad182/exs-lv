<?php

/**
 * Lietotāja personalizētie lapas iestatījumi
 */
if (isset($_POST['submit']) && check_token('usersettings', $_POST['xsrf_token'])) {

	$inprofile->show_code = (bool) $_POST['edit-show_code'];
	$inprofile->show_lol = (bool) $_POST['edit-show_lol'];
	$inprofile->show_rs = (bool) $_POST['edit-show_rs'];
	$inprofile->showsig = (bool) $_POST['edit-enablesig'];
	$inprofile->skin = (int) $_POST['edit-skin'];
	$inprofile->pm_notify_email = (int) $_POST['edit-pm_notify_email'];

	$db->update('users', $auth->id, [
		'show_code' => $inprofile->show_code,
		'show_lol' => $inprofile->show_lol,
		'show_rs' => $inprofile->show_rs,
		'showsig' => $inprofile->showsig,
		'skin' => $inprofile->skin,
		'pm_notify_email' => $inprofile->pm_notify_email
	]);

	$auth->reset();
	update_karma($auth->id, true);

	set_flash('Izmaiņas saglabātas!', 'success');
	redirect('/user/settings');
}

$sigmark = '';
if ($inprofile->showsig) {
	$sigmark = ' checked="checked"';
}

$show_codemark = '';
if ($inprofile->show_code) {
	$show_codemark = ' checked="checked"';
}
$show_lolmark = '';
if ($inprofile->show_lol) {
	$show_lolmark = ' checked="checked"';
}

$show_rsmark = '';
if ($inprofile->show_rs) {
	$show_rsmark = ' checked="checked"';
}

$tpl->newBlock('user-profile-settings');

//show form
$tpl->assign([
	'edit-enablesig-mark' => $sigmark,
	'edit-show_code-mark' => $show_codemark,
	'edit-show_lol-mark' => $show_lolmark,
	'edit-show_rs-mark' => $show_rsmark,
	'user-skin-' . $inprofile->skin => ' selected="selected"',
	'user-pm_notify_email-' . $inprofile->pm_notify_email => ' selected="selected"',
	'xsrf' => make_token('usersettings')
]);

$page_title = 'Tavi lapas iestatījumi';

