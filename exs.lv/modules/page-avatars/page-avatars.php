<?php

$page = intval(str_replace('', '', $_GET['img']));

$pinfo = $db->get_row("SELECT * FROM `pages` WHERE `id` = '$page'");

$original = null;

if ($pinfo->category == 80 && $avatar = $db->get_row("SELECT * FROM  `movie_images` WHERE `main` = 1 AND `page_id` = '$pinfo->id' LIMIT 1")) {
	$original = IMG_PATH . $avatar->image;
} elseif (!empty($pinfo->avatar) && file_exists($pinfo->avatar)) {
	$original = $pinfo->avatar;
} else {
	$uinfo = get_user($pinfo->author);

	if (!empty($uinfo->avatar) && file_exists(CORE_PATH . '/dati/bildes/useravatar/' . $uinfo->avatar)) {
		$original = CORE_PATH . '/dati/bildes/useravatar/' . $uinfo->avatar;
	} else {

		$category = get_cat($pinfo->category);

		if (!empty($category->icon) && file_exists($category->icon)) {
			$original = $category->icon;
		}
	}
}

if ($original != null && file_exists($original)) {
	require_once(CORE_PATH . '/includes/class.upload.php');
	$foo = new Upload($original);
	$foo->image_max_pixels = 200000000;
	$foo->file_safe_name = false;
	$foo->file_new_name_body = $page;
	$foo->image_resize = true;
	$foo->image_x = 32;
	$foo->image_y = 32;
	$foo->allowed = array('image/*');
	$foo->image_ratio_crop = true;
	$foo->jpeg_quality = 98;
	$foo->file_auto_rename = false;
	$foo->file_overwrite = true;
	$foo->image_convert = 'jpg';
	$foo->process(CORE_PATH . '/dati/bildes/topic-av/');
	header("Content-Type: image/jpg");
	echo file_get_contents(CORE_PATH . '/dati/bildes/topic-av/' . $page . '.jpg');
} else {
	$expires = 900;
	header('Pragma: public');
	header('Cache-Control: max-age=' . $expires);
	header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
	header("Content-Type: image/png");
	echo file_get_contents(CORE_PATH . '/dati/bildes/topic-av/none.png');
}
exit;
