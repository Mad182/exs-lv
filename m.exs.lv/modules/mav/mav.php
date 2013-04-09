<?php

$original = CORE_PATH . '/dati/bildes/useravatar/' . $_GET['img'];

if (file_exists($original)) {
	require_once(CORE_PATH . '/includes/class.upload.php');
	$foo = new Upload($original);
	$foo->file_safe_name = false;
	$foo->image_resize = true;
	$foo->image_x = 45;
	$foo->image_y = 45;
	$foo->allowed = array('image/*');
	$foo->image_ratio_crop = true;
	$foo->jpeg_quality = 90;
	$foo->file_auto_rename = false;
	$foo->file_overwrite = true;
	$foo->process('av/');
	header("Content-Type: image/jpg");
	echo file_get_contents('av/' . $_GET['img']);
} else {
	header("Content-Type: image/png");
	echo file_get_contents('av/none.png');
}
exit;
