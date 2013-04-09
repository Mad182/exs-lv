<?php

if (isset($_POST['file'])) {

	$file = ezgif_filename($_POST['file']);
	$fullPath = '/home/www/img.exs.lv/tmp/' . $file;

	if (file_exists($fullPath)) {

		$imsize = getimagesize($fullPath);

		$width = (int) $_POST['width'];
		$height = (int) $_POST['height'];

		if ($height > 800) {
			$height = 800;
		}

		if ($width > 800) {
			$width = 800;
		}

		if ($width <= 0 && $height > 0) {
			$width = round($height * $imsize[0] / $imsize[1]);
		}

		if ($height <= 0 && $width > 0) {
			$height = round($width * $imsize[1] / $imsize[0]);
		}
		
		if ($width <= 0 && $height <= 0) {
			die('Please specify width or height!');
		}

		$out = 'gif_' . $width . 'x' . $height . '_' . substr(md5(time() . $auth->ip . rand(0, 9999)), 0, 6) . '.gif';

		switch ($_POST['method']) {
			case 'im-coalesce':

				$str = "convert '/home/www/img.exs.lv/tmp/" . $file . "' -coalesce '/home/www/img.exs.lv/tmp/coalesce_" . $file . "'";
				$test = `$str`;

				$str2 = "convert -size " . $imsize[0] . "x" . $imsize[1] . " '/home/www/img.exs.lv/tmp/coalesce_" . $file . "' -resize " . $width . "x" . $height . " '/home/www/img.exs.lv/tmp/" . $out . "'";
				$test2 = `$str2`;

				break;
			case 'im':

				$str = "convert -size " . $imsize[0] . "x" . $imsize[1] . " '/home/www/img.exs.lv/tmp/" . $file . "' -resize " . $width . "x" . $height . " '/home/www/img.exs.lv/tmp/" . $out . "'";
				$test = `$str`;

				break;

			case 'gifsicle':
			default:

				$str = "gifsicle --resize " . $width . "x" . $height . " '/home/www/img.exs.lv/tmp/" . $file . "' > '/home/www/img.exs.lv/tmp/" . $out . "'";
				$test = `$str`;

				break;
		}

		$fs = filesize('/home/www/img.exs.lv/tmp/' . $out);

		echo '<p><img src="http://img.exs.lv/tmp/' . $out . '" alt="" /></p>';

		echo '<p>File size: ' . human_filesize($fs) . ', width: ' . $width . 'px, height: ' . $height . 'px</p>';

		echo '<p>' . ezgif_menu($out) . '</p>';

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
			$tpl->newBlock('resize-upl');
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
		redirect('/resize/' . $foo->file_dst_name);
	} else {
		set_flash('Error: ' . $foo->error, 'error');
		$tpl->newBlock('resize-upl');
	}
} elseif (isset($_GET['var1'])) {

	$file = ezgif_filename($_GET['var1']);
	$fullPath = '/home/www/img.exs.lv/tmp/' . $file;
	if (file_exists($fullPath)) {
		$imsize = getimagesize($fullPath);

		$tpl->newBlock('resize');
		$tpl->assign(array(
			'file' => $file,
			'menu' => ezgif_menu($file),
			'width' => $imsize[0],
			'height' => $imsize[1]
		));
	} else {
		set_flash('File not found :(', 'error');
		redirect('/resize');
	}
} else {
	$tpl->newBlock('resize-upl');
}

$tpl->newBlock('meta-description');
$tpl->assign('description', 'Simple and free tool to resize (scale) animated gifs online with multiple methods');
