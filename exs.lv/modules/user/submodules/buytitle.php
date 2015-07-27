<?php

/**
 * Nopirkt iespēju izmantot custom title
 */

$robotstag[] = 'noindex';

deny_proxies();

if (isset($_GET['buytitle_pay']) && $_GET['buytitle_pay'] == 'true' && check_token('buytitle', $_GET['token'])) {

	if ($inprofile->credit < 3) {
		set_flash('Nepietiek exs.lv kredīta!');
	} else {
		$db->query("UPDATE users SET custom_title_paid = '1', credit = credit-'3' WHERE id = ('" . $inprofile->id . "')");
		set_flash('Tagad Tu vari mainīt savu lietotāja nosaukumu!', 'success');
	}

	redirect('/user/edit');
}

$pay = '';
if ($inprofile->credit >= 3) {
	$pay = '<p><a href="/user/buytitle/?buytitle_pay=true&amp;token=' . make_token('buytitle') . '"><strong>Nopirkt iespēju mainīt lietotāja nosaukumu</strong></a></p>';
}

$tpl->newBlock('user-profile-buytitle');
$tpl->assign(array(
	'user-credit' => $inprofile->credit,
	'pay' => $pay
));

$page_title = 'Lietotāja nosaukuma maiņa';

