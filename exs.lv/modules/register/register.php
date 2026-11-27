<?php

/**
 * Jauna lietotāja reģistrācija
 */

deny_proxies();

if (!$auth->ok) {

	$botstring = 'Es tiešām nēesmu ļauns spambots! ' . md5($auth->xsrf . '-' . 'neesmĻaunsuspambots') . '!';

	/* show registration form */
	$tpl->newBlock('registration-form');

	$field_mail = md5($auth->xsrf . '-' . 'mail');
	$field_nick = md5($auth->xsrf . '-' . 'nick');

	$tpl->assignGlobal('rules', $db->get_var("SELECT text FROM pages WHERE id = 57753"));
	$tpl->assignGlobal('botstring', $botstring);

	$regdata = [];
	$regdata['mail'] = '';
	$regdata['nick'] = '';
	$regdata['pass'] = '';
	$regdata['mailok'] = false;
	$regdata['mailunique'] = false;
	$regdata['nickok'] = false;
	$regdata['passok'] = false;
	$regdata['botsok'] = false;
	$regdata['agree'] = false;

	if (isset($_POST[$field_nick]) && ($_POST['www'] ?? '') === $botstring && check_token('reg', $_POST['reg_token'] ?? '')) {

		//check mail
		$post_mail = $_POST[$field_mail] ?? '';
		if (filter_var($post_mail, FILTER_VALIDATE_EMAIL)) {

			$regdata['mail'] = email2db($post_mail);

			if ($db->get_row("SELECT id FROM users WHERE mail = ('" . $regdata['mail'] . "')") || $db->get_row("SELECT id FROM users_tmp WHERE mail = ('" . $regdata['mail'] . "')")) {
				$tpl->newBlock('invalid-mail-taken');
				$regdata['mail'] = '';
			} else {
				$regdata['mailok'] = true;
			}

			$emparts = explode('@', $post_mail);
			if (!empty($emparts[1]) && $db->get_var("SELECT count(*) FROM `email_blacklist` WHERE `domain` = '" . sanitize($emparts[1]) . "'")) {
				set_flash('Neatļauts e-pasts!', 'error');
				redirect('/' . $category->textid);
			}
		} else {
			$tpl->newBlock('invalid-mail');
		}

		//check nick
		$trimmed_nick = trim($_POST[$field_nick]);
		if (strlen($trimmed_nick) > 2 && strlen($trimmed_nick) <= 16) {
			$regdata['nick'] = sanitize($trimmed_nick);
			$nick_slug = mkslug($regdata['nick']);
			$regdata['nickok'] = true;
			if ($nick_slug == 'page' || $nick_slug == '-' || $nick_slug == 'frostijs' || $db->get_row("SELECT id FROM users WHERE nick = ('" . $regdata['nick'] . "') OR nick = ('" . $nick_slug . "')") || $db->get_row("SELECT id FROM users_tmp WHERE nick = ('" . $regdata['nick'] . "') OR nick = ('" . $nick_slug . "')")) {
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
		$post_pass = $_POST['omnomnom'] ?? '';
		$post_pass2 = $_POST['url'] ?? '';
		if (strlen($post_pass) < 6) {
			$tpl->newBlock('invalid-pass-len');
		} elseif ($post_pass !== $post_pass2) {
			$tpl->newBlock('invalid-pass-mach');
		} else {
			$regdata['pass'] = password_hash($post_pass, PASSWORD_BCRYPT, ["cost" => 14]);
			$regdata['passok'] = true;
		}




	    $captcha = false;
	    if(isset($_POST['g-recaptcha-response'])) {
			$captcha=$_POST['g-recaptcha-response'];
		}

		$url = 'https://www.google.com/recaptcha/api/siteverify';
		$data = ['secret' => '6Lc4eR0TAAAAANtY0bNSr0rcXat9-sgDwWRurRIq',
		         'response' => $captcha,
		         'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''];

		$options = [
		    'http' => [
		        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		        'method'  => 'POST',
		        'content' => http_build_query($data) 
		    ]
		];

		$context  = stream_context_create($options);
		$resp_raw = @file_get_contents($url, false, $context);
		$response = $resp_raw ? json_decode($resp_raw) : null;

	    if (!empty($response->success) && $response->success === true) {
			$regdata['botsok'] = true;
		} else {
			$tpl->newBlock('invalid-bots');
		}

		if (!empty($_POST['agree'])) {
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
				<h3>Sveiki!</h3>
				<p>
					Paldies, ka reģistrējies portālā ' . $_SERVER['HTTP_HOST'] . '!<br>Ceram, ka labi pavadīsi laiku :)
				</p>
				<p>
					Lai pabeigtu reģistrāciju, nospied uz saites vai iekopē to pārlūkprogrammas adreses joslā.<br>
					To darot Tu apstiprini, ka reģistrējoties norādītā e-pasta adrese ir pareiza un atļauj mums to uzglabāt.
				</p>
				<p>
					<a href="' . $proto . $_SERVER['HTTP_HOST'] . '/confirm/' . $hash . '">' . $proto . $_SERVER['HTTP_HOST'] . '/confirm/' . $hash . '</a>
				</p>
				<p style="font-size:90%;margin: 20px 0 10px;color: #888">
					Ja neesi veicis šo darbību, ignorē šo vēstuli, un mēs Tevi vairs netraucēsim.<br>
					Neapstiprināts profils tiks automātiski dzēsts pēc vienas nedēļas.
				</p>';

		send_email(stripslashes($regdata['mail']), $subject, $message);
	} else {

		//fill form fields
		$tpl->gotoBlock('registration-form');
		$tpl->newBlock('form-fields');
		$tpl->assign([
			'new-nick' => h($regdata['nick']),
			'new-mail' => h($regdata['mail']),
			'field_mail' => $field_mail,
			'field_nick' => $field_nick,
			'reg_token' => make_token('reg')
		]);
	}
} else {
	set_flash("Tu jau esi reģistrējies :D");
	redirect();
}

