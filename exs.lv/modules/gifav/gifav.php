<?php

/**
 * Animated avatars
 */
$tpl->assign([
	'user-id' => $auth->id
]);

$owned = false;
$avs = $db->get_results("SELECT * FROM `animations` WHERE `user_id` = '0' OR `user_id` = '$auth->id' ORDER BY `user_id` DESC, `id` DESC");
if ($avs) {
	foreach ($avs as $av) {
		$tpl->newBlock('av-node');

		$own = '';
		if ($auth->ok && $auth->id == $av->user_id) {
			$own = 'background:#3a6;';
			$owned = true;
		}

		$tpl->assign([
			'id' => $av->id,
			'image' => $av->image,
			'title' => ucwords(str_replace(['.gif', '-', '_'], ['', ' ', ' '], $av->image)),
			'owned' => $own,
		]);
	}
}

$user = $auth->ok ? $db->get_row("SELECT * FROM users WHERE id = '$auth->id'") : null;

if ($auth->ok && $user && ($user->credit >= 5 || $owned)) {
	$tpl->newBlock('av-buy');

	if (isset($_POST['avatarid'])) {
		$av = (int) $_POST['avatarid'];

		$avrow = $db->get_row("SELECT * FROM `animations` WHERE `id` = '$av'");

        if ($avrow && $avrow->user_id == 0 && $user->credit >= 5) {
			$db->query("UPDATE animations SET user_id = '$auth->id' WHERE id = '$av'");
			$db->query("UPDATE `users` SET credit = credit-5, av_alt = '1', avatar = '" . sanitize($avrow->image) . "' WHERE id = '$auth->id'");
			redirect('/user/' . $auth->id);
		} elseif ($avrow && $avrow->user_id == $auth->id) {

			// support for old avatars with one size
			$av_alt = 0;
			if (file_exists(CORE_PATH . '/dati/bildes/u_large/' . $avrow->image)) {
				$av_alt = 1;
			}

			$db->query("UPDATE `users` SET `av_alt` = '$av_alt', `avatar` = '" . sanitize($avrow->image) . "' WHERE `id` = '$auth->id'");
			redirect('/user/' . $auth->id);
		} else {
			set_flash('Izvēlētais avatars jau ir aizņemts vai nepietiek exs kredīta!', 'error');
		}
	}
} elseif ($auth->ok) {
	$tpl->newBlock('av-credit');
}

//failu imports no gif-avatars.com
if (im_mod()) {

	if (isset($_POST['gif_avatars_import'])) {
		$id = mkslug($_POST['gif_avatars_id']);

		$contents = curl_get('https://gif-avatars.com/avatar/' . $id . '/json');
		if (empty($contents)) {
			set_flash('Neizdevās iegūt datus no gif-avatars.com!', 'error');
			redirect('/' . $category->textid);
		}

		$json = json_decode($contents);
		$filename = (!empty($json) && !empty($json->filename)) ? basename((string) $json->filename) : '';
		if (empty($json) || $filename === '' || $filename === '.' || $filename === '..') {
			set_flash('Neizdevās noparsēt datus!', 'error');
			redirect('/' . $category->textid);
		}

		if (file_exists(CORE_PATH . '/dati/bildes/useravatar/' . $filename)) {
			set_flash('Tāds avatars jau atrodas uz exs.lv servera!', 'error');
			redirect('/' . $category->textid);
		}

		file_put_contents(CORE_PATH . '/dati/bildes/u_small/' . $filename, curl_get($json->img_45));
		file_put_contents(CORE_PATH . '/dati/bildes/useravatar/' . $filename, curl_get($json->img_90));
		file_put_contents(CORE_PATH . '/dati/bildes/u_large/' . $filename, curl_get($json->img_150));

		if (file_exists(CORE_PATH . '/dati/bildes/useravatar/' . $filename)) {

			$db->query("INSERT INTO `animations` (`image`, `added_by`) VALUES ('" . sanitize($filename) . "', '$auth->id')");
			set_flash('Fails veiksmīgi importēts!', 'success');
			redirect('/' . $category->textid);
		} else {

			set_flash('Neizdevās nokopēt failus!', 'error');
			redirect('/' . $category->textid);
		}
	}

	$tpl->newBlock('import');
}

