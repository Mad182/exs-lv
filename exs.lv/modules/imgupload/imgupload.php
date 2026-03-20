<?php

/**
 * Attēlu augšupielāde img.exs.lv
 */
if ($auth->ok) {

	if (isset($_GET['delete']) && check_token('delimg-' . $_GET['delete'], $_GET['token'])) {
		$delete = intval($_GET['delete']);
		
		$image = $db->get_row("SELECT * FROM `imgupload` WHERE `user_id` = '$auth->id' AND `id` = '$delete' LIMIT 1");

		if($image) {

			unlink('/home/www/img.exs.lv/' . $image->path . '/' . $image->file);
			unlink('/home/www/img.exs.lv/' . $image->path . '/small/' . $image->file);
			$db->query("DELETE FROM `imgupload` WHERE `user_id` = '$auth->id' AND `id` = '$image->id' LIMIT 1");

			$auth->log('Izdzēsa attēlu', 'imgupload', $image->id);

			set_flash('Attēls dzēsts!', 'success');

		} else {
			set_flash('Kļūdains pieprasījums', 'error');
		}

		redirect('/img');
	}

	//max upload file size
	if (im_mod()) {
		$max_size = '12M';
		ini_set('memory_limit', '200M');
	} else {
		$max_size = '3M';
		ini_set('memory_limit', '150M');
	}

	$tpl->newBlock('img-upload');

	// runescape attēliem iespējams pievienot ūdenszīmi
	if ($lang == 9) {
		$tpl->newBlock('rs-watermark-checkbox');
	}

	if (isset($_FILES['new-image'])) {

		$slug = mkslug($auth->nick);
		if (strlen($slug) < 3) {
			$slug .= '0';
		}
		if (strlen($slug) < 3) {
			$slug .= '0';
		}
		if (strlen($slug) < 3) {
			$slug .= '0';
		}

		$path = substr($slug, 0, 1) . '/' . substr($slug, 1, 1) . '/' . $slug;

		rmkdir(IMG_PATH . '/' . $path);

		require_once(LIB_PATH . '/verot/src/class.upload.php');

		$foo = new Upload($_FILES['new-image']);
		$foo->image_max_pixels = 200000000;
		$foo->mime_check = true;
		$foo->no_script = true;
		$foo->file_max_size = $max_size;
		$foo->allowed = ['image/*'];

		if ($foo->image_src_type == 'bmp') {
			$foo->image_convert = 'png';
		}

		if ($foo->image_src_type != 'gif') {
			$foo->image_resize = true;
			$foo->image_ratio = true;
			$foo->image_y = 1800;
			$foo->image_x = 1800;
			$foo->image_ratio_no_zoom_in = true;
		}

		if (!empty($_POST['resize'])) {
			$foo->image_resize = true;
			$foo->image_convert = 'jpg';
			$foo->image_x = 640;
			$foo->image_ratio_no_zoom_in = true;
			$foo->jpeg_quality = 99;
		}

		if (!empty($_POST['resize960'])) {
			$foo->image_resize = true;
			$foo->image_convert = 'jpg';
			$foo->image_x = 960;
			$foo->image_ratio_no_zoom_in = true;
			$foo->jpeg_quality = 99;
		}


		// runescape.exs.lv ūdenszīme
		if ($lang == 9 && isset($_POST['add-watermark'])) {

			// lielizmēra attēliem būs lielāka ūdenszīme
			if ($foo->image_src_x > 1000 && $foo->image_x > 1000) {

				$foo->image_watermark = CORE_PATH . '/bildes/runescape/watermarks/watermark-large.png';
				$foo->image_watermark_y = -10;

				if (isset($_POST['position-left'])) {
					$foo->image_watermark_x = 10;
				} else {
					$foo->image_watermark_x = -10;
				}

				// mazāka izmēra attēliem būs maza ūdenszīme
			} else {

				$foo->image_watermark = CORE_PATH . '/bildes/runescape/watermarks/watermark-mini.png';
				$foo->image_watermark_y = -1;

				if (isset($_POST['position-left'])) {
					$foo->image_watermark_x = 7;
				} else {
					$foo->image_watermark_x = -3;
				}
			}
		}
		

		$foo->process(IMG_PATH . '/' . $path . '/');

		if ($foo->processed) {

			$original_file = $foo->file_dst_pathname;
			$new_filename = $foo->file_dst_name_body . '.avif';
			$new_file_path = $foo->file_dst_path . $new_filename;

			$foo->image_resize = true;
			$foo->image_ratio_no_zoom_in = false;
			$foo->image_ratio_crop = true;
			$foo->image_ratio = false;
			$foo->image_x = 150;
			$foo->image_y = 112;
			if ($foo->image_src_type == 'bmp') {
				$foo->image_convert = 'png';
			}

			if (!empty($_POST['resize'])) {
				$foo->image_convert = 'jpg';
				$foo->jpeg_quality = 99;
			}

			$foo->image_watermark = '';

			$foo->process(IMG_PATH . '/' . $path . '/small/');

			// Convert main image to AVIF
			$original_thumb = $foo->file_dst_pathname;
			$new_thumb_path = $foo->file_dst_path . $new_filename;

			shell_exec("avifenc " . escapeshellarg($original_file) . " " . escapeshellarg($new_file_path));
			shell_exec("avifenc " . escapeshellarg($original_thumb) . " " . escapeshellarg($new_thumb_path));

			if (file_exists($new_file_path)) {
				unlink($original_file);
				unlink($original_thumb);
				$foo->file_dst_name = $new_filename;
			}

			$db->query("INSERT INTO `imgupload` (path,user_id,ip,created,file) VALUES ('$path','$auth->id','" . sanitize($auth->ip) . "',NOW(),'" . sanitize($foo->file_dst_name) . "')");

			$tpl->newBlock('img-upload-success');

			$tpl->assign('file', $foo->file_dst_name);
			$tpl->assign('path', $path);
		} else {
			set_flash('Kļūda: ' . $foo->error, 'error');
		}
	}

	$total = $db->get_var("SELECT count(*) FROM `imgupload` WHERE `user_id` = '$auth->id'");

	if (isset($_GET['skip'])) {
		$skip = (int) $_GET['skip'];
	} else {
		$skip = 0;
	}
	$end = 10;

	$images = $db->get_results("SELECT * FROM `imgupload` WHERE `user_id` = '$auth->id' ORDER BY `created` DESC LIMIT $skip,$end");
	if (!empty($images)) {
		foreach ($images as $image) {
			$tpl->newBlock('img-upload-item');
			$tpl->assign([
				'id' => $image->id,
				'token' => make_token('delimg-' . $image->id),
				'file' => $image->file,
				'path' => $image->path,
				'created' => $image->created,
			]);
		}
	}

	$pager = pager($total, $skip, $end, '/img/?skip=');
	$tpl->assignGlobal([
		'pager-next' => $pager['next'],
		'pager-prev' => $pager['prev'],
		'pager-numeric' => $pager['pages']
	]);
} else {
	$tpl->newBlock('error-nologin');
	$tpl->assign('xsrf', $auth->xsrf);
}
