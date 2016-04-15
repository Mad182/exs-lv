<?php

/**
 * Jauna lietotāja reģistrācija
 */

deny_proxies();

if (!$auth->ok) {

	$botstring = 'Es tiešām nēesmu ļauns spambots! ' . md5($auth->xsrf . '-' . 'neesmuspambots') . '!';

	/* show registration form */
	$tpl->newBlock('registration-form');

	$field_mail = md5($auth->xsrf . '-' . 'mail');
	$field_nick = md5($auth->xsrf . '-' . 'nick');

	$tpl->assignGlobal('rules', $db->get_var("SELECT text FROM pages WHERE id = 57753"));
	$tpl->assignGlobal('botstring', $botstring);

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

	if (isset($_POST[$field_nick]) && $_POST['www'] === $botstring && check_token('reg', $_POST['reg_token'])) {

		//check mail
		if (filter_var($_POST[$field_mail], FILTER_VALIDATE_EMAIL)) {

			$regdata['mail'] = email2db($_POST[$field_mail]);

			if ($db->get_row("SELECT * FROM users WHERE mail = ('" . $regdata['mail'] . "')") || $db->get_row("SELECT * FROM users_tmp WHERE mail = ('" . $regdata['mail'] . "')")) {
				$tpl->newBlock('invalid-mail-taken');
				$regdata['mail'] = '';
			} else {
				$regdata['mailok'] = true;
			}

			$emparts = explode('@', $_POST[$field_mail]);
			if ($db->get_var("SELECT count(*) FROM `email_blacklist` WHERE `domain` = '" . sanitize($emparts[1]) . "'")) {
				set_flash('Neatļauts e-pasts!', 'error');
				redirect('/' . $category->textid);
			}
		} else {
			$tpl->newBlock('invalid-mail');
		}

		//check nick
		if (strlen(trim($_POST[$field_nick])) > 2 && strlen(trim($_POST[$field_nick])) <= 16) {
			$regdata['nick'] = sanitize(trim($_POST[$field_nick]));
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
			$regdata['pass'] = password_hash($_POST['omnomnom'], PASSWORD_BCRYPT, array("cost" => 14));
			$regdata['passok'] = true;
		}




	    $captcha = false;
	    if(isset($_POST['g-recaptcha-response'])) {
			$captcha=$_POST['g-recaptcha-response'];
		}

		$url = 'https://www.google.com/recaptcha/api/siteverify';
		$data = array('secret' => '6Lc4eR0TAAAAANtY0bNSr0rcXat9-sgDwWRurRIq',
		         'response' => $captcha,
		         'remoteip' => $_SERVER['REMOTE_ADDR']);

		$options = array(
		    'http' => array(
		        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		        'method'  => 'POST',
		        'content' => http_build_query($data) 
		    )
		);

		$context  = stream_context_create($options);
		$response = json_decode(file_get_contents($url, false, $context));

	    if($response->success === true) {
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

		//link protocol
		$proto = get_protocol($lang);

		//send email
		$subject = 'Reģistrācija portālā ' . $_SERVER['HTTP_HOST'];
		$message = '
				<h3>Sveicināts/-a!</h3>
				<p>
					Paldies, ka reģistrējies portālā ' . $_SERVER['HTTP_HOST'] . '!<br />Ceram, ka labi pavadīsi laiku :)
				</p>
				<p>
					Lai pabeigtu reģistrāciju, nospied uz saites vai iekopē to pārlūkprogrammas adreses joslā.
				</p>
				<p>
					<a href="' . $proto . $_SERVER['HTTP_HOST'] . '/confirm/' . $hash . '">' . $proto . $_SERVER['HTTP_HOST'] . '/confirm/' . $hash . '</a>
				</p>
				<p style="font-size:90%;margin: 20px 0 10px;color: #888">
					Profils tika reģistrēts no IP adreses ' . $auth->ip . '.<br />
					Ja neesi veicis šo darbību, ignorē šo vēstuli, un mēs Tevi vairs netraucēsim.
				</p>';

		send_email(stripslashes($regdata['mail']), $subject, $message);
	} else {

		//fill form fields
		$tpl->gotoBlock('registration-form');
		$tpl->newBlock('form-fields');
		$tpl->assign(array(
			'new-nick' => h($regdata['nick']),
			'new-mail' => h($regdata['mail']),
			'field_mail' => $field_mail,
			'field_nick' => $field_nick,
			'reg_token' => make_token('reg')
		));
	}
} else {
	set_flash("Tu jau esi reģistrējies :D");
	redirect();
}

