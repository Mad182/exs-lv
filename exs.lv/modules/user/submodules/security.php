<?php

/**
 * Lietotāja profila drošības iestatījumi -
 * paroles un e-pasta adreses maiņa
 */
$tpl->newBlock('user-profile-security');

//write changes
if (isset($_POST['submit'])) {

	if (filter_var($_POST['edit-mail'], FILTER_VALIDATE_EMAIL)) {
		$user->mail = email2db($_POST['edit-mail']);
	} else {
		set_flash('Nederīga e-pasta adrese!', 'error');
		redirect('/user/security');
	}

	$db->update('users', $auth->id, array(
		'mail' => $user->mail
	));

	$auth->reset();
	update_karma($auth->id, true);

	if (!empty($_POST['password-1']) && !empty($_POST['password-2']) && $_POST['password-1'] === $_POST['password-2']) {
		if (password_verify($_POST['password-old'], $user->password) || ($user->password == '' && (!empty($user->draugiem_id) || !empty($user->facebook_id)))) {
			if (strlen($_POST['password-1']) > 5) {

				$newpass = password_hash($_POST['password-1'], PASSWORD_BCRYPT, array("cost" => 14));

				$db->update('users', $auth->id, array('pwd' => '', 'password' => $newpass));

				$auth->login($user->nick, $_POST['password-1']);
			} else {
				set_flash('Ievadītā parole ir pārāk īsa!', 'error');
				redirect('/user/security');
			}
		} else {
			set_flash('Paroles nesakrīt!', 'error');
			redirect('/user/security');
		}
	}

	set_flash('Izmaiņas saglabātas!', 'success');
	redirect('/user/security');
}

//show form
$tpl->gotoBlock('user-profile-security');
$tpl->assign(array(
	'user-mail' => $user->mail
));

$tpl->assignGlobal(array(
	'user-id' => $user->id,
	'user-nick' => htmlspecialchars($user->nick),
	'active-tab-profile' => 'active',
	'profile-sel' => ' class="selected"'
));

$page_title = 'Tava parole un e-pasts';
