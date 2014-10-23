<?php

/**
 * e-pasta adreses maiņa
 */
$tpl->newBlock('user-profile-email');

//write changes
if (isset($_POST['submit'])) {

	if (password_verify($_POST['password-old'], $inprofile->password) || ($inprofile->password == '' && (!empty($inprofile->draugiem_id) || !empty($inprofile->facebook_id)))) {

		if (filter_var($_POST['edit-mail'], FILTER_VALIDATE_EMAIL)) {

			if ($db->get_row("SELECT * FROM users WHERE mail = ('" . sanitize($_POST['edit-mail']) . "')") || $db->get_row("SELECT * FROM users_tmp WHERE mail = ('" . sanitize($_POST['edit-mail']) . "')")) {
				set_flash('<strong>Kļūda:</strong> šī e-pasta adrese jau tiek lietota!', 'error');
			} else {

				$inprofile->mail = email2db($_POST['edit-mail']);

				$email_token = substr(hash('sha256', uniqid() . $inprofile->mail . $auth->ip), 0, 16);

				//link protocol
				$proto = get_protocol($lang);

				//suta e-pastu
				require_once(LIB_PATH . '/swiftmailer/lib/swift_required.php');

				$transport = Swift_SmtpTransport::newInstance($smtp_hostname, $smtp_port, $smtp_encryption)->setUsername($smtp_account)->setPassword($smtp_password);

				$mailer = Swift_Mailer::newInstance($transport);
				$message = Swift_Message::newInstance();
				$message->setSubject('E-pasta adreses apstiprinājums ' . $_SERVER['HTTP_HOST']);
				$message->setFrom(array('info@exs.lv' => ucfirst($_SERVER['HTTP_HOST']) . ' community'));
				$message->setTo($inprofile->mail);
				$message->setBody('
				<h3>Sveiki!</h3>
				<p>
					Kāds (mēs ceram, ka Tu) pieprasīja Tavam profilam e-pasta adreses maiņu, norādot šo adresi.
				</p>
				<p>
					Lai apstiprinātu e-pasta maiņu, nospied uz zemāk redzamās saites, vai iekopē to pārlūkprogrammas adreses joslā:<br />
					<a href="' . $proto . $_SERVER['HTTP_HOST'] . '/confirm-email/' . $email_token . '">' . $proto . $_SERVER['HTTP_HOST'] . '/confirm-email/' . $email_token . '</a><br />
					<br />
				</p>
				<p>
					E-pasta maiņa tika pieprasīta no IP adreses ' . $auth->ip . '.<br />
					Ja neesi veicis šo darbību, lūdzam informēt par to ' . $_SERVER['HTTP_HOST'] . ' administrāciju, norādot minēto IP adresi.</p>
				<p>__<br />Ar cieņu,<br />' . ucfirst($_SERVER['HTTP_HOST']) . ' adminu un moderatoru komanda!</p>
			');
				$message->setContentType("text/html");

				if ($mailer->send($message)) {

					$db->update('users', $auth->id, array(
						'email_new' => $inprofile->mail,
						'email_time' => 'NOW()',
						'email_token' => $email_token
					));

					$auth->reset();
					update_karma($auth->id, true);
					set_flash('Izmaiņas saglabātas!<br />Savā jaunajā e-pasta adresē pēc brīža saņemsi apstiprinājuma linku, lai varētu pabeigt e-pasta maiņu.', 'success');

					$auth->log('Pieprasīja e-pasta maiņu (apstiprinājuma e-pasts nosūtīts)', 'users', $userdata->id);
				} else {
					set_flash('<strong>Kļūda:</strong> neizdevās nosūtīt apstiprinājuma e-pastu!', 'error');
					$auth->log('Pieprasīja e-pasta maiņu (neizdevās nosūtīt apstiprinājuma e-pastu)', 'users', $userdata->id);
				}
			}
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

