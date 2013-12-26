<?php

/*
 * Lietotāja personalizētie lapas iestatījumi
 */

//write changes
if (isset($_POST['submit'])) {

	$user->show_code = (bool) $_POST['edit-show_code'];
	$user->show_lol = (bool) $_POST['edit-show_lol'];
	$user->show_rp = (bool) $_POST['edit-show_rp'];
	$user->show_rs = (bool) $_POST['edit-show_rs'];
	$user->showsig = (bool) $_POST['edit-enablesig'];
	$user->skin = (int) $_POST['edit-skin'];

	$db->update('users', $auth->id, array(
		'show_code' => $user->show_code,
		'show_lol' => $user->show_lol,
		'show_rp' => $user->show_rp,
		'show_rs' => $user->show_rs,
		'showsig' => $user->showsig,
		'skin' => $user->skin,
	));

	$auth->reset();
	update_karma($auth->id, true);

	set_flash('Izmaiņas saglabātas!', 'success');
	redirect('/user/settings');

}

if ($user->showsig) {
	$sigmark = ' checked="checked"';
} else {
	$sigmark = '';
}
if ($user->show_code) {
	$show_codemark = ' checked="checked"';
} else {
	$show_codemark = '';
}
if ($user->show_lol) {
	$show_lolmark = ' checked="checked"';
} else {
	$show_lolmark = '';
}
if ($user->show_rp) {
	$show_rpmark = ' checked="checked"';
} else {
	$show_rpmark = '';
}
if ($user->show_rs) {
	$show_rsmark = ' checked="checked"';
} else {
	$show_rsmark = '';
}

$tpl->newBlock('user-profile-settings');

//show form
$tpl->assign(array(
	'edit-enablesig-mark' => $sigmark,
	'edit-show_code-mark' => $show_codemark,
	'edit-show_lol-mark' => $show_lolmark,
	'edit-show_rp-mark' => $show_rpmark,
	'edit-show_rs-mark' => $show_rsmark,
	'user-skin-' . $user->skin => ' selected="selected"',
));

$tpl->assignGlobal(array(
	'user-id' => $user->id,
	'user-nick' => htmlspecialchars($user->nick),
	'active-tab-profile' => 'active',
	'profile-sel' => ' class="selected"'
));

$page_title = 'Tavi lapas iestatījumi';
