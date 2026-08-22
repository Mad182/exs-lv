<?php

/**
 * Lietotāja personalizētie lapas iestatījumi
 */
if (isset($_POST['submit']) && check_token('usersettings', $_POST['xsrf_token'])) {

	$inprofile->show_code = !empty($_POST['edit-show_code']) ? 1 : 0;
	$inprofile->show_lol = !empty($_POST['edit-show_lol']) ? 1 : 0;
	$inprofile->show_rs = !empty($_POST['edit-show_rs']) ? 1 : 0;
	$inprofile->showsig = !empty($_POST['edit-enablesig']) ? 1 : 0;
	$inprofile->skin = isset($_POST['edit-skin']) ? (int) $_POST['edit-skin'] : 0;
	$inprofile->pm_notify_email = isset($_POST['edit-pm_notify_email']) ? (int) $_POST['edit-pm_notify_email'] : 0;

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

