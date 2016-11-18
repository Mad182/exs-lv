<?php

/**
 * Wallpaper pievienošana (moderatoriem)
 */
$robotstag[] = 'noindex';

if (!im_mod()) {
	redirect();
}

// File handling

if (isset($_FILES['new-image'])) {
	// Regular upload
	uploadFile($db, $auth, $_FILES['new-image']);
}

if (isset($_POST['new-image-id'])) {
	// Download from external resource and upload

	require(CORE_PATH . '/includes/class.getwallpapers.php');

	$get_wp = new getWallpapers();

	$temp = tmpfile();
	$temp_filename = stream_get_meta_data($temp);
	$temp_filename = $temp_filename['uri'];
	rename($temp_filename, $temp_filename.='.jpg');

	$id = $_POST['new-image-id'];
	$data = array_merge_recursive($get_wp->reddit(), $get_wp->imgur());

	file_put_contents($temp_filename, curl_get($data[$id]['file']));

	uploadFile($db, $auth, $temp_filename);
	fclose($temp);
}

if (isset($_GET['var1']) && $_GET['var1'] === 'catsite.json') {
	require(CORE_PATH . '/includes/class.getwallpapers.php');

	$get_wp = new getWallpapers();
	die(json_encode(array_merge_recursive($get_wp->reddit(), $get_wp->imgur())));
} else {

	// List
	$tpl->newBlock('add-wp-form');

	$wallpapers = $db->get_results("SELECT image,date FROM wallpapers WHERE date > '" . date('Y-m-d') . "' ORDER BY date ASC");
	if ($wallpapers) {
		foreach ($wallpapers as $image) {
			$tpl->newBlock('wallpaper');
			$tpl->assign([
				'image' => $image->image,
				'date' => $image->date,
				'style' => ' style="color:red"'
			]);
		}
	}
}

