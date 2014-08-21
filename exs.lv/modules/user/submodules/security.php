<?php

/**
 * Lietotāja paroles maiņa
 */
$tpl->newBlock('user-profile-security');

//write changes
if (isset($_POST['submit'])) {

	if (!empty($_POST['password-1']) && !empty($_POST['password-2']) && $_POST['password-1'] === $_POST['password-2']) {
		if (password_verify($_POST['password-old'], $inprofile->password) || ($inprofile->password == '' && (!empty($inprofile->draugiem_id) || !empty($inprofile->facebook_id)))) {
			if (strlen($_POST['password-1']) > 5) {

				$newpass = password_hash($_POST['password-1'], PASSWORD_BCRYPT, array("cost" => 14));

				$db->update('users', $auth->id, array('pwd' => '', 'password' => $newpass));

				$auth->login($inprofile->nick, $_POST['password-1']);
				set_flash('Izmaiņas saglabātas!', 'success');
			} else {
				set_flash('<strong>Kļūda:</strong> jaunā parole ir pārāk īsa!', 'error');
			}
		} else {
			set_flash('<strong>Kļūda:</strong> esošā parole ievadīta nepareizi!', 'error');
		}
	} else {
		set_flash('<strong>Kļūda:</strong> paroles nesakrīt!', 'error');
	}

	redirect('/user/security');
}

$page_title = 'Tava parole';
