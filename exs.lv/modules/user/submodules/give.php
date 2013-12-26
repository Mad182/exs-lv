<?php

/*
 * Uzdāvināt exs kredītu citam lietotājam
 */

$credit = $db->get_var("SELECT credit FROM users WHERE id = '$auth->id'");
if ($credit) {

	$tpl->newBlock('user-profile-give');

	if (isset($_POST['submit']) && isset($_POST['exs-amount']) && !empty($_POST['exs-amount'])) {
		$amount = intval($_POST['exs-amount']);
		if ($credit >= $amount && $amount > 0) {
			$db->query("UPDATE users SET credit = credit+'" . $amount . "' WHERE id = ('" . $user->id . "')");
			$db->query("UPDATE users SET credit = credit-'" . $amount . "' WHERE id = ('" . $auth->id . "')");

			userlog($auth->id, 'Uzdāvināja ' . $amount . ' expunktus ' . $user->nick, '/dati/bildes/useravatar/' . $user->avatar);
			userlog($user->id, 'Saņēma ' . $amount . ' expunktus no ' . $auth->nick, '/dati/bildes/useravatar/' . $auth->avatar);

			$credit = $credit - $amount;
			set_flash('Pārskaitījums veikts!', 'success');
		} else {
			set_flash('Kļūda!', 'error');
		}
	}

	for ($i = 1; $i <= $credit; $i++) {
		$tpl->newBlock('give-am');
		$tpl->assign(array(
			'value' => $i,
		));
	}
}

$tpl->assignGlobal(array(
	'user-id' => $user->id,
	'user-nick' => htmlspecialchars($user->nick),
	'active-tab-profile' => 'active',
	'profile-sel' => ' class="selected"'
));

$page_title = 'Dāvināt exs kredītu';
