<?php

/**
 * e-pasta adreses maiņa
 */
$tpl->newBlock('user-profile-email');

//write changes
if (isset($_POST['submit'])) {

	if (password_verify($_POST['password-old'], $inprofile->password) || ($inprofile->password == '' && (!empty($inprofile->draugiem_id) || !empty($inprofile->facebook_id)))) {

		if (filter_var($_POST['edit-mail'], FILTER_VALIDATE_EMAIL)) {
			$inprofile->mail = email2db($_POST['edit-mail']);

			$db->update('users', $auth->id, array(
				'mail' => $inprofile->mail
			));

			$auth->reset();
			update_karma($auth->id, true);
			set_flash('Izmaiņas veiksmīgi saglabātas!', 'success');
		} else {
			set_flash('<strong>Kļūda:</strong> nederīga e-pasta adrese!', 'error');
		}
	} else {
		set_flash('<strong>Kļūda:</strong> esošā parole ievadīta nepareizi!', 'error');
	}

	redirect('/user/email');
}

//show form
$tpl->gotoBlock('user-profile-email');
$tpl->assign(array(
	'user-mail' => $inprofile->mail
));

$page_title = 'E-pasta adreses maiņa';
