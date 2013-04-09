<?php

if (isset($_POST['file'])) {

	$file = ezgif_filename($_POST['file']);

	$x = (int) $_POST['x1'];
	$y = (int) $_POST['y1'];
	$w = (int) $_POST['w'];
	$h = (int) $_POST['h'];

	$out = 'gif_' . $w . 'x' . $h . '_' . substr(md5(time() . $auth->ip . rand(0, 9999)), 0, 6) . '.gif';

	switch ($_POST['method']) {
		case 'gifsicle':

			$str = "gifsicle --crop " . $x . "," . $y . "+" . $w . "x" . $h . " '/home/www/img.exs.lv/tmp/" . $file . "' > '/home/www/img.exs.lv/tmp/" . $out . "'";
			break;

		case 'im':
		default:

			$str = "convert '/home/www/img.exs.lv/tmp/" . $file . "' -crop " . $w . "x" . $h . "+" . $x . "+" . $y . " +repage '/home/www/img.exs.lv/tmp/" . $out . "'";
			break;
	}

	$test = `$str`;

	echo '<p><img src="http://img.exs.lv/tmp/' . $out . '" alt="" /></p>';

	echo '<p>' . ezgif_menu($out) . '</p>';

	echo '<p>Please do not hotlink, but save the image when finished.<br />Files are not stored here indefinitely.<br />You can host images at <a href="http://imgur.com/" target="_blank">http://imgur.com/</a></p>';
	exit;
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
			$tpl->newBlock('crop-upl');
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
		redirect('/crop/' . $foo->file_dst_name);
	} else {
		set_flash('Error: ' . $foo->error, 'error');
		$tpl->newBlock('crop-upl');
	}
} elseif (isset($_GET['var1'])) {

	$file = ezgif_filename($_GET['var1']);
	$fullPath = '/home/www/img.exs.lv/tmp/' . $file;
	if (file_exists($fullPath)) {
		$tpl->newBlock('crop');
		$tpl->assign('file', $file);
		$tpl->assign('menu', ezgif_menu($file));
	} else {
		set_flash('File not found :(', 'error');
		redirect('/crop');
	}
} else {
	$tpl->newBlock('crop-upl');
}

$tpl->newBlock('meta-description');
$tpl->assign('description', 'Crop selection of animated gif image online - simple and easy');

