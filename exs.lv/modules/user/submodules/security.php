<?php

/**
 * Lietotāja paroles maiņa
 */
$robotstag[] = 'noindex';

deny_proxies();

$tpl->newBlock('user-profile-security');
$tpl->assign('xsrf', make_token('passwd'));

//write changes
if (isset($_POST['submit'])) {

	if (!empty($_POST['password-1']) && !empty($_POST['password-2']) && $_POST['password-1'] === $_POST['password-2'] && check_token('passwd', $_POST['xsrf_token'])) {
		if (password_verify($_POST['password-old'], $inprofile->password) || ($inprofile->password == '' && (!empty($inprofile->draugiem_id) || !empty($inprofile->facebook_id)))) {
			if (strlen($_POST['password-1']) > 5) {

				$bad = $db->get_var("SELECT count(*) FROM `bad_passwords` WHERE `shit` = '".sanitize($_POST['password-1'])."'");

				if($bad) {

					set_flash('<strong>Kļūda:</strong> ievadītā parole ir atrodama nedrošo paroļu sarakstā!', 'error');
				} else {

					$newpass = password_hash($_POST['password-1'], PASSWORD_BCRYPT, ["cost" => 14]);

					$db->update('users', $auth->id, ['pwd' => '', 'password' => $newpass]);

					$auth->log('Nomainīja savu paroli', 'users', $auth->id);

					$auth->login($inprofile->nick, $_POST['password-1']);
					set_flash('Izmaiņas saglabātas!', 'success');

				}
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

