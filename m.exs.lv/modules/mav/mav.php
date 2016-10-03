<?php

/**
 * Ģenerē lietotāju un grupu avatarus mobilajai versijai
 * (mazāks failu izmērs un animētie padarīti nekustīgi)
 */
$original = CORE_PATH . '/dati/bildes/useravatar/' . $_GET['img'];

$path = '';
if (substr($_GET['img'], 1, 1) == '/' && substr($_GET['img'], 3, 1) == '/') {
	$path = substr($_GET['img'], 0, 4);
}

if (file_exists($original)) {
	require_once(CORE_PATH . '/includes/class.upload.php');
	$foo = new Upload($original);
	$foo->file_safe_name = false;
	$foo->image_resize = true;
	$foo->image_x = 90;
	$foo->image_y = 90;
	$foo->allowed = ['image/*'];
	$foo->image_ratio_crop = true;
	$foo->jpeg_quality = 93;
	$foo->file_auto_rename = false;
	$foo->file_overwrite = true;
	$foo->process('av/' . $path);
	header("Content-Type: image/jpg");
	echo file_get_contents($original);
} else {
	header("Content-Type: image/png");
	echo file_get_contents('av/none.png');
}
exit;

