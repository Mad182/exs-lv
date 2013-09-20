<?php

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

	$id = $_POST['new-image-id'];
	$data = $get_wp->reddit();

	file_put_contents($temp_filename, file_get_contents($data[$id]['file']));

	uploadFile($db, $auth, $temp_filename);
	fclose($temp);
}

function uploadFile($db, $auth, $file) {
	ini_set('memory_limit', '128M');

	require_once(CORE_PATH . '/includes/class.upload.php');

	$text = substr(md5(date('YmdHis')), 0, 8);

	//lielais attels
	$foo = new Upload($file);
	$foo->file_new_name_body = $text;
	$foo->image_convert = 'jpg';
	$foo->image_resize = false;
	$foo->allowed = array('image/*');
	$foo->process(CORE_PATH . '/dati/wallpapers/');

	//sikbilde
	$foo = new Upload($file);
	$foo->file_new_name_body = $text;
	$foo->image_resize = true;
	$foo->image_convert = 'jpg';
	$foo->image_x = 120;
	$foo->image_y = 100;
	$foo->allowed = array('image/*');
	$foo->image_ratio_crop = true;
	$foo->jpeg_quality = 96;
	$foo->process(CORE_PATH . '/dati/wallpapers/thb/');

	//write to database
	if ($foo->processed) {

		$last = $db->get_var("SELECT date FROM wallpapers ORDER BY date DESC LIMIT 1");

		$tomorrow = mktime(0, 0, 0, date("m", strtotime($last)), date("d", strtotime($last)) + 1, date("Y", strtotime($last)));

		$tdate = date('Y-m-d', $tomorrow);
		$image = $text . '.jpg';

		$db->query("INSERT INTO wallpapers (date,image,author) VALUES ('$tdate','$image','$auth->id')");
		$foo->clean();
	} else {
		set_flash('Kļūda: ' . $foo->error, 'error');
	}
}

if (isset($_GET['var1']) && $_GET['var1'] === 'catsite.json') {
	require(CORE_PATH . '/includes/class.getwallpapers.php');

	$get_wp = new getWallpapers();
	die(json_encode($get_wp->reddit()));
} else {
	$tpl->newBlock('add-wp-form');

	$wallpapers = $db->get_results("SELECT image,date FROM wallpapers WHERE date > '" . date('Y-m-d') . "' ORDER BY date ASC");
	if ($wallpapers) {
		foreach ($wallpapers as $image) {
			$tpl->newBlock('wallpaper');
			$tpl->assign(array(
				'wallpaper-image' => $image->image,
				'wallpaper-date' => $image->date,
				'style' => ' style="color:red"'
			));
		}
	}
}
