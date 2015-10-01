<?php

/**
 * Attēlu augšupielāde img.exs.lv
 */
if ($auth->ok) {

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

		require_once CORE_PATH . '/includes/class.upload.php';
		$foo = new Upload($_FILES['new-image']);
		$foo->image_max_pixels = 200000000;
		$foo->mime_check = true;
		$foo->no_script = true;
		$foo->file_max_size = $max_size;
		$foo->allowed = array('image/*');

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
			$foo->jpeg_quality = 98;
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
				$foo->jpeg_quality = 96;
			}

			$foo->image_watermark = '';

			$foo->process(IMG_PATH . '/' . $path . '/small/');

			$db->query("INSERT INTO `imgupload` (path,user_id,ip,created,file) VALUES ('$path','$auth->id','" . sanitize($auth->ip) . "',NOW(),'" . sanitize($foo->file_dst_name) . "')");

			//optimize png images
			if ($foo->image_src_type == 'png') {
				$str = "optipng '" . IMG_PATH . "/" . $path . "/" . $foo->file_dst_name . "'";
				$str2 = "optipng '" . IMG_PATH . "/" . $path . "/small/" . $foo->file_dst_name . "'";
				$test = `$str`;
				$test2 = `$str2`;
			}

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
			$tpl->assign(array(
				'file' => $image->file,
				'path' => $image->path,
				'created' => $image->created,
			));
		}
	}

	$pager = pager($total, $skip, $end, '/img/?skip=');
	$tpl->assignGlobal(array(
		'pager-next' => $pager['next'],
		'pager-prev' => $pager['prev'],
		'pager-numeric' => $pager['pages']
	));
} else {
	$tpl->newBlock('error-nologin');
	$tpl->assign('xsrf', $auth->xsrf);
}
