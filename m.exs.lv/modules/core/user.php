<?php

$userid = (int) $_GET['u'];
$user = $db->get_row("SELECT * FROM `users` WHERE `id` = '" . $userid . "'");

if ($user) {
	redirect('/user/' . $user->id, true);
} else {
	$tpl->newBlock('error-nouser');
	$page_title = 'Kļūda: profils nav atrasts!';
}
