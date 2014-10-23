<?php

$user = get_user(intval($_GET['y']));
if (!empty($user->yt_name)) {
	header("HTTP/1.1 301 Moved Permanently");
	redirect('https://exs.lv/youtube/' . $user->id . '/' . mkslug($user->yt_name));
	exit;
} else {
	header("HTTP/1.1 410 Gone");
	$tpl->newBlock('error-nouser');
	$page_title = 'Kļūda: profils nav atrasts!';
}

