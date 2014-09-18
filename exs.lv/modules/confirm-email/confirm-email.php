<?php

/**
 * E-pasta apstiprināšana
 */
if ($ban = $db->get_var("SELECT `id` FROM `banned` WHERE `ip` = '$auth->ip' AND `time`+`length` > '" . time() . "' AND (`lang` = 0 OR `lang` = '$lang') ORDER BY `time` DESC LIMIT 1")) {
	$auth->logout();
	set_flash('Pieeja lapai ir liegta!', 'error');
	redirect('http://exs.lv/?c=125&bid=' . $ban);
}


if (isset($_GET['var1']) && (strlen($_GET['var1']) === 64 || strlen($_GET['var1']) === 16)) {

	$userdata = $db->get_row("SELECT * FROM `users` WHERE
					`email_token` = '" . sanitize($_GET['var1']) . "' AND
					`email_time` > '" . date('Y-m-d H:i:s', strtotime('-6 hours')) . "' LIMIT 1");

	if (!empty($userdata)) {

		$db->update('users', $userdata->id, array(
			'email_new' => 'null',
			'email_time' => 'null',
			'email_token' => 'null',
			'mail_confirmed' => 'NOW()',
			'mail' => $userdata->email_new
		));

		set_flash('E-pasta adrese veiksmīgi apstiprināta un nomainīta!', 'success');

	} else {

		set_flash('Kļūdains links vai beidzies tā derīguma termiņš!', 'error');

	}
	redirect();
}
