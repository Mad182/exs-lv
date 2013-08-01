<?php

require(CORE_PATH . '/modules/mta-ucp/functions.mta-ucp.php');

if(!empty($_POST['username']) && isset($_POST['oldpass']) && isset($_POST['newpass']) && isset($_POST['newpass2'])) {
	$nick = sanitize($_POST['username']);

	$oldpass = mta_hash($_POST['oldpass']);
	$newpass = mta_hash($_POST['newpass']);

	if(strlen($_POST['newpass']) < 6) {
		set_flash('Parolei jābūt vismaz 6 simbolus garai!', 'error');
		redirect('/' . $category->textid);
	}

	if(in_array(strtolower($_POST['newpass']), array(
		'123456',
		'1234567',
		'12345678',
		'654321',
		'password',
		'parole',
		'sanandreas',
		'san andreas',
		'qwerty'
	))) {
		set_flash('Pārāk vienkārša parole!', 'error');
		redirect('/' . $category->textid);
	}

	if($_POST['newpass'] !== $_POST['newpass2']) {
		set_flash('Ievadītās paroles nesakrīt!', 'error');
		redirect('/' . $category->textid);
	}

	$mtadb = new mdb('exs', 'gnzNhE3Q', 'rpdb', 'mta.exs.lv');

	$account = $mtadb->get_row("SELECT * FROM `accounts` WHERE `username` = '$nick' AND `password` = '$oldpass' LIMIT 1");
	sleep(1);

	if(empty($account)) {
		set_flash('Nepareiza parole/lietotājvārds!', 'error');
		redirect('/' . $category->textid);
	}

	$mtadb->query("UPDATE `accounts` SET `password` = '$newpass' WHERE `id` = '$account->id'");
	set_flash('Parole veiksmīgi nomainīta!', 'success');
	redirect('/' . $category->textid);

}

