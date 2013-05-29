<?php

$colors_manual = 0;

if (isset($_POST['file'])) {

	$file = ezgif_filename($_POST['file']);
	$fullPath = '/home/www/img.exs.lv/tmp/' . $file;

	if (file_exists($fullPath)) {

		$out = 'gif_' . time() . '_' . $file;


		$effects = '';
		if(!empty($_POST['sepia'])) {
			$effects .= ' -sepia-tone 80%';
		}
		if(!empty($_POST['monochrome'])) {
			$effects .= ' -monochrome';
		}
		if(!empty($_POST['grayscale'])) {
			$effects .= ' -colorspace gray';
		}
		if(!empty($_POST['flip'])) {
			$effects .= ' -flip';
		}
		if(!empty($_POST['flop'])) {
			$effects .= ' -flop';
		}
		if(!empty($_POST['reverse'])) {
			$effects .= ' -coalesce -reverse -quiet -layers OptimizePlus  -loop 0';
		}
		
		if(!empty($effects)) {
			$str = "convert '/home/www/img.exs.lv/tmp/" . $file . "'" . $effects . " '/home/www/img.exs.lv/tmp/" . $out . "'";
			$test = `$str`;
		} else {
			die('No effects selected!');
		}


		if ($_POST['method'] != 'im_color' && $_POST['method'] != 'im_color_dither') {
			$fs = filesize('/home/www/img.exs.lv/tmp/' . $out);

			echo '<p><img src="http://img.exs.lv/tmp/' . $out . '" alt="" /></p>';

			echo '<p>File size: ' . human_filesize($fs) . '</p>';

			echo '<p>' . ezgif_menu($out) . '</p>';
		}

		echo '<p>Please do not hotlink, but save the image when finished.<br />Files are not stored here indefinitely.<br />You can host images at <a href="http://imgur.com/" target="_blank">http://imgur.com/</a></p>';
		exit;
	} else {
		die(add_smile('File not found. Weird... :|'));
	}
} elseif (isset($_FILES['new-image']) || !empty($_POST['new-image-url']) || !empty($_GET['url'])) {

	require_once(CORE_PATH . '/includes/class.upload.php');

	if (empty($_POST['new-image-url']) && !empty($_GET['url'])) {
		$_POST['new-image-url'] = $_GET['url'];
	}

	if (!empty($_POST['new-image-url']) && empty($_FILES['new-image']['name']) && substr($_POST['new-image-url'], 0, 1) != '.' && substr($_POST['new-image-url'], 0, 1) != '/') {

		$filename = '/tmp/ezgif_' . uniqid() . '.gif';
		$file = file_get_contents($_POST['new-image-url'], false, null, -1, 12582912);
		if ($file) {
			file_put_contents($filename, $file);

			$foo = new Upload($filename);
			$foo->file_new_name_body = substr(md5(time() . $auth->ip . rand(0, 9999)), 0, 10);
			$foo->mime_check = true;
			$foo->no_script = true;
			$foo->file_max_size = '12M';
			$foo->allowed = array('image/gif');
			$foo->image_resize = false;
			$foo->process('/home/www/img.exs.lv/tmp/');
			$foo->clean();
		} else {
			set_flash("Oh snap! Couldn't fetch remote file :|", 'error');
			$tpl->newBlock('effects-upl');
		}
	} else {

		$foo = new Upload($_FILES['new-image']);
		$foo->file_new_name_body = substr(md5(time() . $auth->ip . rand(0, 9999)), 0, 10);
		$foo->mime_check = true;
		$foo->no_script = true;
		$foo->file_max_size = '12M';
		$foo->allowed = array('image/gif');
		$foo->image_resize = false;
		$foo->process('/home/www/img.exs.lv/tmp/');
		$foo->clean();
	}

	if ($foo->processed) {
		redirect('/effects/' . $foo->file_dst_name);
	} else {
		set_flash('Error: ' . $foo->error, 'error');
		$tpl->newBlock('effects-upl');
	}
} elseif (isset($_GET['var1'])) {

	$file = ezgif_filename($_GET['var1']);
	$fullPath = '/home/www/img.exs.lv/tmp/' . $file;
	if (file_exists($fullPath)) {
		$imsize = getimagesize($fullPath);
		$fs = filesize($fullPath);

		$tpl->newBlock('effects');
		$tpl->assign(array(
			'file' => $file,
			'menu' => ezgif_menu($file),
			'width' => $imsize[0],
			'height' => $imsize[1],
			'filesize' => human_filesize($fs),
			'manual-colors' => intval($colors_manual)
		));
		
	} else {
		set_flash('File not found :(', 'error');
		redirect('/effects');
	}
} else {
	$tpl->newBlock('effects-upl');
}

$tpl->newBlock('meta-description');
$tpl->assign('description', 'Add effects and modify GIF animation online');
