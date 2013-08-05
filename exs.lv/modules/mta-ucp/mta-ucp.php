<?php

if(!empty($_POST['username']) && isset($_POST['oldpass']) && isset($_POST['newpass']) && isset($_POST['newpass2'])) {

	sleep(1);

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
		'gtasanandreas',
		'san andreas',
		'gta san andreas',
		'qwerty',
		'asdfgh',
		'111111',
		'aaaaaa',
		'      ',
		'******',
		'mtaexslv',
		'mta.exs.lv',
		'rpexslv',
		'rp.exs.lv',
		'roleplay',
		'jaunaparole',
		'newpass',
		'newpassword',
		'test123',
		'pass123',
		'role play',
		'mtaexs'
	))) {
		set_flash('Pārāk vienkārša parole!', 'error');
		redirect('/' . $category->textid);
	}

	if($_POST['newpass'] !== $_POST['newpass2']) {
		set_flash('Ievadītās paroles nesakrīt!', 'error');
		redirect('/' . $category->textid);
	}

	$mtadb = new mdb($mta_username, $mta_password, $mta_database, $mta_hostname);

	$account = $mtadb->get_row("SELECT * FROM `accounts` WHERE `username` = '$nick' AND `password` = '$oldpass' LIMIT 1");

	if(empty($account)) {
		set_flash('Nepareiza parole/lietotājvārds!', 'error');
		redirect('/' . $category->textid);
	}

	$mtadb->query("UPDATE `accounts` SET `password` = '$newpass', `loginhash` = NULL WHERE `id` = '$account->id' LIMIT 1");
	set_flash('Parole veiksmīgi nomainīta!', 'success');
	redirect('/' . $category->textid);

}

