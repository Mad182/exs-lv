<?php

if ($auth->ok) {

	$user = $db->get_row("SELECT * FROM users WHERE id = '$auth->id'");

	$tpl->newBlock('buygal');

	$pay = '';
	if ($user->credit >= 3) {

		if (isset($_GET['act']) && $_GET['act'] == 'submitpay') {
			$db->query("UPDATE users SET credit = credit-'3', maximg = maximg+'100' WHERE id = '$auth->id'");
			redirect('/extend-gallery');
		}

		$pay = '<p><a href="/extend-gallery/?act=submitpay"><strong>Paplašināt galeriju</strong></a></p>';
	}

	$tpl->assign(array(
		'maximg' => $user->maximg,
		'credit' => $user->credit,
		'pay' => $pay
	));
}
