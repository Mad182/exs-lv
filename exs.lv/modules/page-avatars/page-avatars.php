<?php

$page = intval(str_replace('','',$_GET['img']));

$pinfo = $db->get_row("SELECT * FROM `pages` WHERE `id` = '$page'");

$original = null;
if(!empty($pinfo->avatar) && file_exists($pinfo->avatar)) {
	$original = $pinfo->avatar;
} else {
	$uinfo = get_user($pinfo->author);

	if(!empty($uinfo->avatar) && file_exists('dati/bildes/useravatar/'.$uinfo->avatar)) {
		$original = 'dati/bildes/useravatar/'.$uinfo->avatar;
	} else {

		$category = get_cat($pinfo->category);

		if(!empty($category->icon) && file_exists($category->icon)) {
			$original = $category->icon;
		}

	}

}

if ($original != null && file_exists($original)) {
	require_once(CORE_PATH . '/includes/class.upload.php');
	$foo = new Upload($original);
	$foo->file_safe_name = false;
	$foo->file_new_name_body = $page;
	$foo->image_resize = true;
	$foo->image_x = 32;
	$foo->image_y = 32;
	$foo->allowed = array('image/*');
	$foo->image_ratio_crop = true;
	$foo->jpeg_quality = 95;
	$foo->file_auto_rename = false;
	$foo->file_overwrite = true;
	$foo->image_convert = 'jpg';
	$foo->process('dati/bildes/topic-av/');
	header("Content-Type: image/jpg");
	echo file_get_contents('dati/bildes/topic-av/' . $page .'.jpg');
} else {
	header("Content-Type: image/png");
	echo file_get_contents('dati/bildes/topic-av/none.png');
}
exit;
