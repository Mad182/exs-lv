<?php

/*
 * Avatara attēla maiņa
 */

if ($user->avatar == '') {
	$user->avatar = 'none.png';
}

//write changes
if (isset($_POST['submit'])) {

	/* load libraries */
	require(CORE_PATH . '/includes/class.upload.php');

	//new avatar image
	if (isset($_FILES['edit-avatar'])) {

		$rand = md5(microtime() . $auth->id);
		$avatar_path = substr($rand, 0, 1) . '/' . substr($rand, 1, 1) . '/';

		$text = time() . '_' . $auth->id;
		$foo = new Upload($_FILES['edit-avatar']);
		$foo->file_new_name_body = $text;
		$foo->image_resize = true;
		$foo->image_convert = 'jpg';
		$foo->image_x = 90;
		$foo->image_y = 90;
		$foo->allowed = array('image/*');
		$foo->image_ratio_crop = true;
		$foo->jpeg_quality = 97;
		$foo->file_auto_rename = false;
		$foo->file_overwrite = true;
		$foo->process(CORE_PATH . '/dati/bildes/useravatar/'.$avatar_path);
		if ($foo->processed) {

			$foo->file_new_name_body = $text;
			$foo->image_resize = true;
			$foo->image_convert = 'jpg';
			$foo->image_x = 45;
			$foo->image_y = 45;
			$foo->allowed = array('image/*');
			$foo->image_ratio_crop = true;
			$foo->jpeg_quality = 97;
			$foo->file_auto_rename = false;
			$foo->file_overwrite = true;
			$foo->process(CORE_PATH . '/dati/bildes/u_small/'.$avatar_path);

			$foo->file_new_name_body = $text;
			$foo->image_resize = true;
			$foo->image_convert = 'jpg';
			$foo->image_x = 180;
			$foo->image_y = 240;
			$foo->allowed = array('image/*');
			$foo->image_ratio_crop = false;
			$foo->image_ratio_no_zoom_in = true;
			$foo->jpeg_quality = 97;
			$foo->file_auto_rename = false;
			$foo->file_overwrite = true;
			$foo->process(CORE_PATH . '/dati/bildes/u_large/'.$avatar_path);

			if (file_exists(CORE_PATH . '/dati/bildes/useravatar/' . $avatar_path . $text . '.jpg')) {
				if ($user->avatar != 'none.png' && !empty($user->avatar) && !empty($user->av_alt)) {
					$db->query("INSERT INTO `avatar_history` (user_id,avatar,changed) VALUES ('$user->id','$user->avatar',NOW())");
				}
				$user->avatar = $avatar_path . $text . '.jpg';
				$user->av_alt = 1;
			}
			$foo->clean();
		} else {
			set_flash('Kļūda: ' . $foo->error, 'error');
			redirect('/user/avatar');
		}
	}

	$db->update('users', $auth->id, array(
		'avatar' => $user->avatar,
		'av_alt' => $user->av_alt
	));

	$auth->reset();
	update_karma($auth->id, true);

	if (!empty($_POST['password-1']) && !empty($_POST['password-2']) && $_POST['password-1'] === $_POST['password-2']) {
		if (pwd($_POST['password-old']) == $user->pwd || ($user->pwd == '' && (!empty($user->draugiem_id) || !empty($user->facebook_id)))) {
			if (strlen($_POST['password-1']) > 5) {

				$db->update('users', $auth->id, array('pwd' => pwd($_POST['password-1'])));

				$auth->login($user->nick, $_POST['password-1']);

				$tpl->newBlock('save-pwd');
			} else {
				$tpl->newBlock('invalid-pwdlen');
			}
		} else {
			$tpl->newBlock('invalid-pwd');
		}
	}

	set_flash('Izmaiņas saglabātas!', 'success');
	redirect('/user/avatar');
}

$tpl->newBlock('user-profile-avatar');

$tpl->assignGlobal(array(
	'user-id' => $user->id,
	'user-nick' => htmlspecialchars($user->nick),
	'active-tab-profile' => 'active',
	'profile-sel' => ' class="selected"'
));

$page_title = 'Avatara attēla maiņa';
