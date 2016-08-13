<?php

$user = get_user(intval($_GET['y']));
if (!empty($user->yt_name)) {
	redirect('https://exs.lv/youtube/' . $user->id . '/' . mkslug($user->yt_name), true);
}

redirect('https://exs.lv/', true);

