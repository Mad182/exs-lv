<?php

/**
 * Aizmirstas paroles atjaunošana
 */
$robotstag = array('noindex', 'follow');

if ($ban = $db->get_var("SELECT `id` FROM `banned` WHERE `ip` = '$auth->ip' AND `time`+`length` > '" . time() . "' AND (`lang` = 0 OR `lang` = '$lang') ORDER BY `time` DESC LIMIT 1")) {
	$auth->logout();
	set_flash('Pieeja lapai ir liegta!', 'error');
	redirect('/?c=125&bid=' . $ban);
}

if (!$auth->ok) {

	/* pārbauda vai lietotājs neizmanto tor */
	if ($auth->is_tor_exit()) {
		set_flash('Paroles maiņa no tavas IP adreses šobrīd nav iespējama ;(', 'error');
		redirect();
	}

	if (isset($_GET['var1']) && strlen($_GET['var1']) > 15) {

		$userdata = $db->get_row("SELECT * FROM `users` WHERE
					`reset_token` = '" . sanitize($_GET['var1']) . "' AND
					`reset_time` > '" . date('Y-m-d H:i:s', strtotime('-6 hours')) . "' LIMIT 1");

		if (!empty($userdata)) {
			$newpass = createPassword(8);
			$newhash = password_hash($newpass, PASSWORD_BCRYPT, array("cost" => 14));

			//send email
			$subject = 'Tava jaunā parole ' . $_SERVER['HTTP_HOST'];
			$message = '
					<h3>Parole nomainīta veiksmīgi!</h3>
					<p>
						Tava jaunā parole ir <b>' . $newpass . '</b>
					</p>';

			if (send_email($userdata->mail, $subject, $message)) {
				$db->query("UPDATE `users` SET `password` = '$newhash', `pwd` = '', `reset_token` = '', `reset_time` = '0000-00-00 00:00:00' WHERE `id` = '$userdata->id'");
				$auth->log('Nomainīta parole (e-pasts nosūtīts)', 'users', $userdata->id);
				set_flash('Tava jaunā parole nosūtīta uz e-pastu!', 'success');
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

			$pwd_token = substr(hash('sha256', uniqid() . $userdata->mail . $auth->ip), 0, 16);

			//link protocol
			$proto = get_protocol($lang);

			//send email
			$subject = 'Paroles maiņa ' . $_SERVER['HTTP_HOST'];
			$message = '
				<h3>Sveicināts/-a!</h3>
				<p>
					Kāds (mēs ceram, ka Tu) pieprasīja Tavam profilam paroles maiņu portālā ' . $_SERVER['HTTP_HOST'] . '
				</p>
				<p>
					Lai apstiprinātu paroles maiņu, nospied uz saites vai iekopē to pārlūkprogrammas adreses joslā.
				</p>
				<p>
					<a href="' . $proto . $_SERVER['HTTP_HOST'] . '/forgot-password/' . $pwd_token . '">' . $proto . $_SERVER['HTTP_HOST'] . '/forgot-password/' . $pwd_token . '</a>
				</p>
				<p style="font-size:90%;margin: 20px 0 10px;color: #888">
					Paroles maiņa tika pieprasīta no IP adreses ' . $auth->ip . '.<br />
					Ja neesi veicis šo darbību, lūdzu informē par to ' . $_SERVER['HTTP_HOST'] . ' administrāciju, norādot minēto IP adresi.
				</p>';

			if (send_email($userdata->mail, $subject, $message)) {
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

