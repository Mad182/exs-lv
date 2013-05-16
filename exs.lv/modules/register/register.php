<?php

if (!$auth->ok) {

	/* show registration form */
	$tpl->newBlock('registration-form');

	$tpl->assignGlobal('rules', $db->get_var("SELECT text FROM pages WHERE id = 32137"));

	$regdata = array();
	$regdata['mail'] = '';
	$regdata['nick'] = '';
	$regdata['pass'] = '';
	$regdata['mailok'] = false;
	$regdata['mailunique'] = false;
	$regdata['nickok'] = false;
	$regdata['passok'] = false;
	$regdata['botsok'] = false;
	$regdata['agree'] = false;

	if (isset($_POST['tavaiesauka'])) {

		//check mail
		if (filter_var($_POST['age'], FILTER_VALIDATE_EMAIL)) {

			$regdata['mail'] = email2db($_POST['age']);

			if ($db->get_row("SELECT * FROM users WHERE mail = ('" . $regdata['mail'] . "')") || $db->get_row("SELECT * FROM users_tmp WHERE mail = ('" . $regdata['mail'] . "')")) {
				$tpl->newBlock('invalid-mail-taken');
				$regdata['mail'] = '';
			} else {
				$regdata['mailok'] = true;
			}

			$emparts = explode('@', $_POST['age']);
			if ($db->get_var("SELECT count(*) FROM `email_blacklist` WHERE `domain` = '" . sanitize($emparts[1]) . "'")) {
				set_flash('Neatļauts e-pasts!', 'error');
				redirect('/' . $category->textid);
			}
		} else {
			$tpl->newBlock('invalid-mail');
		}

		//check nick
		if (strlen(trim($_POST['tavaiesauka'])) > 2 && strlen(trim($_POST['tavaiesauka'])) <= 16) {
			$regdata['nick'] = sanitize(trim($_POST['tavaiesauka']));
			$regdata['nickok'] = true;
			if (mkslug($regdata['nick']) == 'page' || mkslug($regdata['nick']) == '-' || $db->get_row("SELECT * FROM users WHERE nick = ('" . $regdata['nick'] . "') OR  nick = ('" . mkslug($regdata['nick']) . "')") || $db->get_row("SELECT * FROM users_tmp WHERE nick = ('" . $regdata['nick'] . "') OR  nick = ('" . mkslug($regdata['nick']) . "')")) {
				$tpl->newBlock('invalid-nick-taken');
				$regdata['nick'] = '';
				$regdata['nickok'] = false;
			}

			if (stristr($regdata['nick'], '@') || stristr($regdata['nick'], '.') || stristr($regdata['nick'], '*') || stristr($regdata['nick'], '#')) {
				set_flash("Neatļauti simboli nikā", "error");
				$regdata['nick'] = '';
				$regdata['nickok'] = false;
			}
		} else {
			$tpl->newBlock('invalid-nick-len');
		}

		//check password
		if (strlen($_POST['omnomnom']) < 6) {
			$tpl->newBlock('invalid-pass-len');
		} elseif ($_POST['omnomnom'] !== $_POST['url']) {
			$tpl->newBlock('invalid-pass-mach');
		} else {
			$regdata['pass'] = pwd($_POST['omnomnom']);
			$regdata['passok'] = true;
		}

		if (strtolower($_POST['password']) == '7' or strtolower($_POST['password']) == 'septiņi' or strtolower($_POST['password']) == 'septini') {
			$regdata['botsok'] = true;
		} else {
			$tpl->newBlock('invalid-bots');
		}

		if ($_POST['agree']) {
			$regdata['agree'] = true;
		} else {
			$tpl->newBlock('invalid-agree');
		}
	}

	//if all ok, create user
	if ($regdata['mailok'] && $regdata['nickok'] && $regdata['passok'] && $regdata['botsok'] && $regdata['agree']) {

		$hash = substr(md5($regdata['nick'] . $regdata['pass'] . $regdata['mail'] . time()), 0, 10);


		//write down
		$db->query("INSERT INTO users_tmp (`nick`,`password`,`mail`,`created`,`hash`)
				 VALUES ('" . $regdata['nick'] . "','" . $regdata['pass'] . "','" . $regdata['mail'] . "',NOW(),'$hash')");

		$tpl->gotoBlock('registration-form');
		$tpl->newBlock('greetings');

		//suta e-pastu
		require_once(LIB_PATH . '/swiftmailer/lib/swift_required.php');

		$transport = Swift_SmtpTransport::newInstance($smtp_hostname, $smtp_port, $smtp_encryption)->setUsername($smtp_account)->setPassword($smtp_password);

		$mailer = Swift_Mailer::newInstance($transport);
		$message = Swift_Message::newInstance();
		$message->setSubject('Reģistrācija portālā ' . $_SERVER['HTTP_HOST']);
		$message->setFrom(array('info@exs.lv' => ucfirst($_SERVER['HTTP_HOST']) . ' community'));
		$message->setTo(stripslashes($regdata['mail']));
		$message->setBody('<h4>Sveiki!</h4><p>Paldies, ka reģistrējies portālā ' . $_SERVER['HTTP_HOST'] . '! Ceram, ka labi pavadīsi laiku :)</p>
		<p>Lai pabeigtu reģistrāciju, nospied uz saites vai iekopē to pārlūkprogrammas adreses joslā.</p>
		<p><a href="http://' . $_SERVER['HTTP_HOST'] . '/confirm/' . $hash . '">http://' . $_SERVER['HTTP_HOST'] . '/confirm/' . $hash . '</a></p>
		<p>__<br />' . $_SERVER['HTTP_HOST'] . ' adminu un moderatoru komanda!</p>');
		$message->setContentType("text/html");
		$mailer->send($message);
	} else {

		//fill form fields
		$tpl->gotoBlock('registration-form');
		$tpl->newBlock('form-fields');
		$tpl->assign(array(
			'new-nick' => htmlspecialchars($regdata['nick']),
			'new-mail' => htmlspecialchars($regdata['mail'])
		));
	}
} else {
	set_flash("Tu jau esi reģistrējies :D");
	redirect();
}
