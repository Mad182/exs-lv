<?php

/**
 * Reģistrācijas apstiprinājums (lietotājs nospiedis linku e-pastā)
 */
$robotstag[] = 'noindex';

if (isset($_GET['var1'])) {

	$hash = sanitize($_GET['var1']);

	$user = $db->get_row("SELECT * FROM `users_tmp` WHERE `hash` = '$hash'");

	if ($user) {

		$db->query("INSERT INTO users (`id`,`nick`,`password`,`mail`,`mail_confirmed`,`date`,`lastseen`,`lastip`,`skin`,`user_agent`,`source_site`)
		VALUES (NULL,'" . sanitize($user->nick) . "','" . $user->password . "','" . $user->mail . "',NOW(),'" . $user->created . "',NOW(),'" . sanitize($auth->ip) . "','3','" . sanitize($_SERVER['HTTP_USER_AGENT']) . "', '$lang')");

		$newid = $db->insert_id;

		if ($lang == 3) {
			$db->query("UPDATE `users` SET `show_code` = '1' WHERE `id` = '$newid'");
		}
		if ($lang == 5) {
			$db->query("UPDATE `users` SET `show_rp` = '1' WHERE `id` = '$newid'");
		}
		if ($lang == 7) {
			$db->query("UPDATE `users` SET `show_lol` = '1' WHERE `id` = '$newid'");
		}
		if ($lang == 9) {
			$db->query("UPDATE `users` SET `show_rs` = '1' WHERE `id` = '$newid'");
		}

		$db->query("INSERT INTO `visits` (`user_id`,`site_id`,`ip`,`lastseen`) VALUES ('$newid','$lang','$auth->ip',NOW())");

		$greet = '<h3>Čau!</h3><p>Sveicu Tevi ar pievienošanos ' . $_SERVER['HTTP_HOST'] . ' lietotāju pulkam!</p><p>Ceru uz Tavu aktivitāti mūsu komūnā.<br />Ja rodas kādas neskaidrības, apskaties <a href="http://exs.lv/read/buj">biežāk uzdotos jautājumus</a> vai arī droši jautā mums - administratoriem un moderatoriem (visi ar sarkaniem vai ziliem nikiem).</p><p>Lai Tev laba diena! :mjau:</p><p style="font-size:90%;color:#888">Šī ziņa ir nosūtīta automātiski.</p>';

		//send private message and add notification
		$db->query("INSERT INTO pm (from_uid,to_uid,date,ip,title,text,is_read) VALUES ('1','$newid',NOW(),'127.0.0.1','Čau! Tikko piereģistrējies!? :)','$greet','0')");
		notify($newid, 9);

		//log registration
		userlog($newid, 'Reģistrējās mājas lapā. Sveicam exiešu pulkā ;)', '/bildes/users-icon.png');

		//log-in new user
		$_SESSION['auth_id'] = $newid;
		$_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);

		update_karma($newid);

		//remove user from temp table
		$db->query("DELETE FROM `users_tmp` WHERE `id` = '$user->id'");

		//redirect to private messages, to read greet msg
		redirect('/pm');
	}
}

$tpl->newBlock('err');

