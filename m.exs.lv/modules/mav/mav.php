<?php

/**
 * Ģenerē lietotāju un grupu avatarus mobilajai versijai
 * (mazāks failu izmērs un animētie padarīti nekustīgi)
 */
$img = isset($_GET['img']) && is_string($_GET['img']) ? $_GET['img'] : '';
$img = str_replace("\0", '', $img);
$base_dir = realpath(CORE_PATH . '/dati/bildes/useravatar');

$valid_original = false;
$path = '';
if ($base_dir !== false && $img !== '' && strpos($img, '..') === false && preg_match('/^[a-zA-Z0-9\/_.-]+\.(jpg|jpeg|png|gif|webp)$/i', $img)) {
	$candidate = realpath($base_dir . '/' . ltrim($img, '/'));
	if ($candidate !== false && strpos($candidate, $base_dir . DIRECTORY_SEPARATOR) === 0 && is_file($candidate)) {
		$img_info = @getimagesize($candidate);
		if ($img_info !== false) {
			$valid_original = $candidate;
			if (substr($img, 1, 1) === '/' && substr($img, 3, 1) === '/' && preg_match('/^[a-zA-Z0-9]\/[a-zA-Z0-9]\/$/', substr($img, 0, 4))) {
				$path = substr($img, 0, 4);
			}
		}
	}
}

if ($valid_original !== false) {
	require_once(LIB_PATH . '/verot/src/class.upload.php');
	$foo = new Upload($valid_original);
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
	header("Content-Type: " . ($img_info['mime'] ?? 'image/jpeg'));
	readfile($valid_original);
} else {
	header("Content-Type: image/png");
	readfile('av/none.png');
}
exit;

