<?php

function uploadFile($db, $auth, $file) {
	ini_set('memory_limit', '128M');

	require_once(CORE_PATH . '/includes/class.upload.php');

	$text = substr(md5(date('YmdHis')), 0, 8);

	//lielais attels
	$foo = new Upload($file);
	$foo->file_new_name_body = $text;
	$foo->image_convert = 'jpg';
	$foo->image_resize = false;
	$foo->allowed = ['image/*'];
	$foo->process(CORE_PATH . '/dati/wallpapers/');

	//sikbilde
	$foo = new Upload($file);
	$foo->file_new_name_body = $text;
	$foo->image_resize = true;
	$foo->image_convert = 'jpg';
	$foo->image_x = 120;
	$foo->image_y = 100;
	$foo->allowed = ['image/*'];
	$foo->image_ratio_crop = true;
	$foo->jpeg_quality = 96;
	$foo->process(CORE_PATH . '/dati/wallpapers/thb/');

	//write to database
	if ($foo->processed) {

		$last = $db->get_var("SELECT `date` FROM `wallpapers` ORDER BY `date` DESC LIMIT 1");

		$tomorrow = mktime(0, 0, 0, date("m", strtotime($last)), date("d", strtotime($last)) + 1, date("Y", strtotime($last)));

		$tdate = date('Y-m-d', $tomorrow);

		//ja ilgaku laiku nav bijuši jauni wallpapers, sākam ar šodienu, nevis aizpildam vecos datumus
		if ($tdate < date('Y-m-d')) {
			$tdate = date('Y-m-d');
		}

		$image = $text . '.jpg';

		$db->query("INSERT INTO `wallpapers` (`date`,`image`,`author`) VALUES ('$tdate','$image','$auth->id')");
		$auth->log('Pievienoja ekrāntapeti', 'wallpapers', $db->insert_id);
		$foo->clean();
	} else {
		set_flash('Kļūda: ' . $foo->error, 'error');
	}
}

