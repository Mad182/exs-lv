<?php

//max upload file size
$max_size = '2.6M';
if (im_mod()) {
	$max_size = '10M';
}

function e404() {
	header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
	header("Status: 404 Not Found");
	header("Content-type: image/jpeg");
	echo file_get_contents('bildes/noimage.jpg');
	exit;
}

if (empty($_GET['var1']) || empty($_GET['viewcat'])) {

	if ($auth->ok) {
		$tpl->newBlock('img-upload');

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

			rmkdir('/home/www/img.exs.lv/' . $path);

			ini_set('memory_limit', '160M');
			require(CORE_PATH . '/includes/class.upload.php');
			$foo = new Upload($_FILES['new-image']);
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

			if ($_POST['resize']) {
				$foo->image_resize = true;
				$foo->image_convert = 'jpg';
				$foo->image_x = 540;
				$foo->image_ratio_no_zoom_in = true;
				$foo->jpeg_quality = 97;
			}

			$foo->process('/home/www/img.exs.lv/' . $path . '/');

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

				if ($_POST['resize']) {
					$foo->image_convert = 'jpg';
					$foo->jpeg_quality = 96;
				}

				$foo->process('/home/www/img.exs.lv/' . $path . '/small/');

				$db->query("INSERT INTO `imgupload` (path,user_id,ip,created,file) VALUES ('$path','$auth->id','" . sanitize($auth->ip) . "',NOW(),'" . sanitize($foo->file_dst_name) . "')");

				//optimize png images
				if ($foo->image_src_type == 'png') {
					$str = "optipng '/home/www/img.exs.lv/" . $path . "/" . $foo->file_dst_name . "'";
					$str2 = "optipng '/home/www/img.exs.lv/" . $path . "/small/" . $foo->file_dst_name . "'";
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
					'views' => $image->views,
					'accessed' => $image->accessed,
					'created' => $image->created,
				));
			}
		}

		$pager = pager($total, $skip, $end, 'http://exs.lv/img/?skip=');
		$tpl->assignGlobal(array(
			'pager-next' => $pager['next'],
			'pager-prev' => $pager['prev'],
			'pager-numeric' => $pager['pages']
		));
	} else {
		$tpl->newBlock('error-nologin');
		$tpl->assign('xsrf', $auth->xsrf);
	}
} else {

	if ($_GET['var1'] == 'small') {

		$path = sanitize($_GET['viewcat']);
		$file = sanitize($_GET['var2']);
		$img = $db->get_row("SELECT * FROM `imgupload` WHERE `path` = '$path' AND `file` = '$file' LIMIT 1");

		if ($img) {
			if ($auth->id != $img->user_id) {
				$db->query("UPDATE `imgupload` SET `accessed` = NOW() WHERE `id` = '$img->id'");
			}
			$imgpath = '/home/www/img.exs.lv/' . $img->path . '/small/' . $img->file;

			if (!file_exists($imgpath)) {
				require(CORE_PATH . '/includes/class.upload.php');
				$foo = new Upload('/home/www/img.exs.lv/' . $img->path . '/' . $img->file);
				$file = explode('.', $img->file);
				$foo->file_new_name_body = $file[0];
				$foo->allowed = array('image/*');
				$foo->image_resize = true;
				$foo->image_ratio_crop = true;
				$foo->image_x = 150;
				$foo->image_y = 112;
				$foo->process('/home/www/img.exs.lv/' . $path . '/small/');
			}

			$size = getimagesize($imgpath);
			$fp = fopen($imgpath, "rb");
			if ($size && $fp) {
				header("Content-type: {$size['mime']}");
				fpassthru($fp);
				exit;
			} else {
				e404();
			}
		} else {
			e404();
		}
	}

	$path = sanitize($_GET['viewcat']);
	$file = sanitize($_GET['var1']);
	$img = $db->get_row("SELECT * FROM `imgupload` WHERE `path` = '$path' AND `file` = '$file' LIMIT 1");

	if ($img) {
		$db->query("UPDATE `imgupload` SET `views` = `views`+1, `accessed` = NOW() WHERE `id` = '$img->id'");
		$imgpath = '/home/www/img.exs.lv/' . $img->path . '/' . $img->file;
		$size = getimagesize($imgpath);
		$fp = fopen($imgpath, "rb");
		if ($size && $fp) {
			header("Content-type: {$size['mime']}");
			fpassthru($fp);
			exit;
		} else {
			e404();
		}
	} else {
		e404();
	}
}
