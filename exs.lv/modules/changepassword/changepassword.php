<?php

if ($ban = $db->get_var("SELECT `id` FROM `banned` WHERE `ip` = '$auth->ip' AND `time`+`length` > '" . time() . "' AND (`lang` = 0 OR `lang` = '$lang') ORDER BY `time` DESC LIMIT 1")) {
	$auth->logout();
	set_flash('Pieeja lapai ir liegta!', 'error');
	redirect('http://exs.lv/?c=125&bid=' . $ban);
}

if (!$auth->ok) {
	$tpl->newBlock('passreset-form');
	if (isset($_POST['pwd-nick']) && isset($_POST['pwd-mail'])) {
		$nick = sanitize($_POST['pwd-nick']);
		$mail = sanitize($_POST['pwd-mail']);
		$userdata = $db->get_row("SELECT * FROM `users` WHERE `nick` = '$nick' AND `mail` = '$mail' AND `pwd` != 'none_kirbis' AND `pwd` != 'fake'");
		if ($userdata) {
			$newpass = createPassword(6);
			$newhash = pwd($newpass);

			//suta e-pastu
			require_once(LIB_PATH . '/swiftmailer/lib/swift_required.php');

			$transport = Swift_SmtpTransport::newInstance($smtp_hostname, $smtp_port, $smtp_encryption)->setUsername($smtp_account)->setPassword($smtp_password);

			$mailer = Swift_Mailer::newInstance($transport);
			$message = Swift_Message::newInstance();
			$message->setSubject('Tava jaunā parole exs.lv');
			$message->setFrom(array('info@exs.lv' => 'Exs.lv community'));
			$message->setTo($userdata->mail);
			$message->setBody('<h3>Sveiki!</h3><p>Kāds (mēs ceram, ka Tu) pieprasīja Tavam profilam paroles maiņu portālā exs.lv. Jaunā parole ir ' . $newpass . '</p><p>Paroles maiņa tika pieprasīta no IP adreses ' . $auth->ip . '. Ja neesi veicis šo darbību, lūdzam informēt par to exs.lv administrāciju, norādot minēto IP adresi.</p><p>__<br />Exs.lv adminu un moderatoru komanda!</p>');
			$message->setContentType("text/html");

			if ($mailer->send($message)) {
				$db->query("UPDATE `users` SET `pwd` = '$newhash', `password` = '' WHERE `id` = '$userdata->id'");
				$auth->log('Pieprasīja paroles maiņu (e-pasts nosūtīts)', 'users', $userdata->id);
			} else {
				$auth->log('Pieprasīja paroles maiņu (neizdevās nosūtīt e-pastu)', 'users', $userdata->id);
			}

			$tpl->newBlock('greetings');
		} else {
			$tpl->newBlock('invalid-namemail');
		}
	}
} else {
	redirect();
}
