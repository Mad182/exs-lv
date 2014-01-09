<?php

/**
 * Nopirkt iespēju izmantot custom title
 */
if (isset($_GET['buytitle_pay']) && $_GET['buytitle_pay'] == 'true') {

	if ($user->credit < 3) {
		set_flash('Nepietiek exs.lv kredīta!');
	} else {
		$db->query("UPDATE users SET custom_title_paid = '1', credit = credit-'3' WHERE id = ('" . $user->id . "')");
		set_flash('Tagad Tu vari mainīt savu lietotāja nosaukumu!', 'success');
	}

	redirect('/user/edit');
}

$pay = '';
if ($user->credit >= 3) {
	$pay = '<p><a href="/user/buytitle/?buytitle_pay=true"><strong>Nopirkt iespēju mainīt lietotāja nosaukumu</strong></a></p>';
}

$tpl->newBlock('user-profile-buytitle');
$tpl->assign(array(
	'user-credit' => $user->credit,
	'pay' => $pay
));

$tpl->assignGlobal(array(
	'user-id' => $user->id,
	'user-nick' => htmlspecialchars($user->nick),
	'active-tab-profile' => 'active',
	'profile-sel' => ' class="selected"'
));

$page_title = 'Lietotāja nosaukuma maiņa';
