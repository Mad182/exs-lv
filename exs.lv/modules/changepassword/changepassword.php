<?php

if ($ban = $db->get_var("SELECT `id` FROM `banned` WHERE `ip` = '$auth->ip' AND `time`+`length` > '" . time() . "' AND (`lang` = 0 OR `lang` = '$lang') ORDER BY `time` DESC LIMIT 1")) {
	$auth->logout();
	set_flash('Pieeja lapai ir liegta!', 'error');
	redirect('http://exs.lv/?c=125&bid=' . $ban);
}

if (!$auth->ok) {

	if(isset($_GET['var1']) && strlen($_GET['var1']) == 64) {

		$userdata = $db->get_row("SELECT * FROM `users` WHERE 
					`reset_token` = '".sanitize($_GET['var1'])."' AND 
					`reset_time` > '".date('Y-m-d H:i:s', strtotime('-6 hours'))."' LIMIT 1");

		if(!empty($userdata)) {
			$newpass = createPassword(8);
			$newhash = pwd($newpass);

			//suta e-pastu
			require_once(LIB_PATH . '/swiftmailer/lib/swift_required.php');

			$transport = Swift_SmtpTransport::newInstance($smtp_hostname, $smtp_port, $smtp_encryption)->setUsername($smtp_account)->setPassword($smtp_password);

			$mailer = Swift_Mailer::newInstance($transport);
			$message = Swift_Message::newInstance();
			$message->setSubject('Tava jaunā parole ' . $_SERVER['HTTP_HOST']);
			$message->setFrom(array('info@exs.lv' => ucfirst($_SERVER['HTTP_HOST']) . ' community'));
			$message->setTo($userdata->mail);
			$message->setBody('<h3>Sveiki!</h3><p>Kāds (mēs ceram, ka Tu) pieprasīja Tavam profilam paroles maiņu portālā exs.lv. Jaunā parole ir ' . $newpass . '</p><p>Paroles maiņa tika pieprasīta no IP adreses ' . $auth->ip . '. Ja neesi veicis šo darbību, lūdzam informēt par to exs.lv administrāciju, norādot minēto IP adresi.</p><p>__<br />Exs.lv adminu un moderatoru komanda!</p>');
			$message->setContentType("text/html");


			if ($mailer->send($message)) {
				$db->query("UPDATE `users` SET `pwd` = '$newhash', `reset_token` = '', `reset_time` = '0000-00-00 00:00:00' WHERE `id` = '$userdata->id'");
				$auth->log('Nomainīta parole (e-pasts nosūtīts)', 'users', $userdata->id);
				set_flash('Parole nosūtīta uz e-pastu!', 'success');
			} else {
				$auth->log('Neveiksmīga paroles maiņa (neizdevās nosūtīt e-pastu)', 'users', $userdata->id);
				set_flash('Paroles nosūtīšana uz e-pastu neizdevās. Nezināma kļūda :(', 'error');
			}

		}
		redirect();

	}

	$tpl->newBlock('passreset-form');
	if (isset($_POST['pwd-nick']) && isset($_POST['pwd-mail'])) {
		$nick = sanitize($_POST['pwd-nick']);
		$mail = sanitize($_POST['pwd-mail']);
		$userdata = $db->get_row("SELECT * FROM `users` WHERE `nick` = '$nick' AND `mail` = '$mail' AND `pwd` != 'none_kirbis' AND `pwd` != 'fake'");
		if ($userdata) {

			$pwd_token = hash('sha256', uniqid() . $userdata->mail . $auth->ip);

			//suta e-pastu
			require_once(LIB_PATH . '/swiftmailer/lib/swift_required.php');

			$transport = Swift_SmtpTransport::newInstance($smtp_hostname, $smtp_port, $smtp_encryption)->setUsername($smtp_account)->setPassword($smtp_password);

			$mailer = Swift_Mailer::newInstance($transport);
			$message = Swift_Message::newInstance();
			$message->setSubject('Paroles maiņa '.$_SERVER['HTTP_HOST']);
			$message->setFrom(array('info@exs.lv' => ucfirst($_SERVER['HTTP_HOST']) . ' community'));
			$message->setTo($userdata->mail);
			$message->setBody('
				<h3>Sveiki!</h3>
				<p>
					Kāds (mēs ceram, ka Tu) pieprasīja Tavam profilam paroles maiņu portālā exs.lv
				</p>
				<p>
					Lai apstiprinātu paroles maiņu, nospied uz zemāk redzamās saites, vai iekopē to pārlūkprogrammas adreses joslā:<br />
					<a href="http://' . $_SERVER['HTTP_HOST'] . '/forgot-password/'.$pwd_token.'">http://' . $_SERVER['HTTP_HOST'] . '/forgot-password/'.$pwd_token.'</a><br />
					<br />
				</p>
				<p>
					Paroles maiņa tika pieprasīta no IP adreses ' . $auth->ip . '.<br />
					Ja neesi veicis šo darbību, lūdzam informēt par to exs.lv administrāciju, norādot minēto IP adresi.</p>
				<p>__<br />Exs.lv adminu un moderatoru komanda!</p>
			');
			$message->setContentType("text/html");


			if ($mailer->send($message)) {
				$db->query("UPDATE `users` SET `reset_token` = '$pwd_token', `reset_time` = NOW() WHERE `id` = '$userdata->id'");
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
