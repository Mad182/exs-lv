<?php

if (isset($_POST['file'])) {

	$file = ezgif_filename($_POST['file']);
	$fullPath = '/home/www/img.exs.lv/tmp/' . $file;

	if (file_exists($fullPath)) {

		$out = 'optijpg_' . ezgif_filename($_POST['method']) . '_' . $file;

		if (!file_exists('/home/www/img.exs.lv/tmp/' . $out)) {
			$str = "cp '/home/www/img.exs.lv/tmp/" . $file . "' '/home/www/img.exs.lv/tmp/" . $out . "' && jpegoptim --strip-all '/home/www/img.exs.lv/tmp/" . $out . "'";
			echo '<code>'.$str.'</code>';
			$test = `$str`;
		}

		$fs = filesize('/home/www/img.exs.lv/tmp/' . $out);
		$fs_old = filesize($fullPath);

		echo '<p><img src="http://img.exs.lv/tmp/' . $out . '" alt="" /></p>';

		echo '<p>File size: ' . human_filesize($fs_old) . ' > ' . human_filesize($fs) . '</p>';

		echo '<p>' . ezgif_jpg_menu($out) . '</p>';

		echo '<p>Please do not hotlink, but save the image when finished.<br />Files are not stored here indefinitely.</p>';
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

		$filename = '/tmp/ezjpg_' . uniqid() . '.jpg';
		$file = file_get_contents($_POST['new-image-url'], false, null, -1, 12582912);
		if ($file) {
			file_put_contents($filename, $file);

			$foo = new Upload($filename);
			$foo->file_new_name_body = substr(md5(time() . $auth->ip . rand(0, 9999)), 0, 10);
			$foo->mime_check = true;
			$foo->no_script = true;
			$foo->file_max_size = '12M';
			$foo->allowed = array('image/jpeg');
			$foo->image_resize = false;
			$foo->process('/home/www/img.exs.lv/tmp/');
			$foo->clean();
		} else {
			set_flash("Oh snap! Couldn't fetch remote file :|", 'error');
			$tpl->newBlock('optimize-upl');
		}
	} else {

		$foo = new Upload($_FILES['new-image']);
		$foo->file_new_name_body = substr(md5(time() . $auth->ip . rand(0, 9999)), 0, 10);
		$foo->mime_check = true;
		$foo->no_script = true;
		$foo->file_max_size = '12M';
		$foo->allowed = array('image/jpeg');
		$foo->image_resize = false;
		$foo->process('/home/www/img.exs.lv/tmp/');
		$foo->clean();
	}

	if ($foo->processed) {
		redirect('/optijpg/' . $foo->file_dst_name);
	} else {
		set_flash('Error: ' . $foo->error, 'error');
		$tpl->newBlock('optimize-upl');
	}
} elseif (isset($_GET['var1'])) {

	$file = ezgif_filename($_GET['var1']);
	$fullPath = '/home/www/img.exs.lv/tmp/' . $file;
	if (file_exists($fullPath)) {
		$imsize = getimagesize($fullPath);

		$tpl->newBlock('optimize');
		$tpl->assign(array(
			'file' => $file,
			'menu' => ezgif_menu($file),
			'width' => $imsize[0],
			'height' => $imsize[1]
		));
	} else {
		set_flash('File not found :(', 'error');
		redirect('/optijpg');
	}
} else {
	$tpl->newBlock('optimize-upl');
}

$tpl->newBlock('meta-description');
$tpl->assign('description', 'Optimize jpg images online to reduce file size');
