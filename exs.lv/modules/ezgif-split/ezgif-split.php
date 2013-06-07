<?php

if (isset($_POST['file'])) {

	$file = ezgif_filename($_POST['file']);
	$fpath = mkslug($file);

	$path = '/tmp/' . $fpath . '/';

	if (!is_dir('/home/www/img.exs.lv' . $path)) {
		mkdir('/home/www/img.exs.lv' . $path, 0700);
		$str = "convert '/home/www/img.exs.lv/tmp/" . $file . "' -coalesce /home/www/img.exs.lv" . $path . "frame_%03d.gif";
		$test = `$str`;
	}

	echo '<p>';
	if ($handle = opendir('/home/www/img.exs.lv' . $path)) {
		while (false !== ($ffile = readdir($handle))) {
			if ($ffile != "." && $ffile != "..") {
				echo '<img src="http://img.exs.lv' . $path . $ffile . '" alt="" /> ';
			}
		}
		closedir($handle);
	}

	echo '</p>';

	echo '<p><a class="small button primary" href="/animate/' . $fpath . '">Edit animation</a> <a class="small button danger" href="/zip/' . $file . '?_">Download frames as ZIP</a> </p>';

	echo '<p>Please do not hotlink, but save the image when finished.<br />Files are not stored here indefinitely.</p>';
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
			$tpl->newBlock('split-upl');
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
		redirect('/split/' . $foo->file_dst_name);
	} else {
		set_flash('Error: ' . $foo->error, 'error');
		$tpl->newBlock('split-upl');
	}
} elseif (isset($_GET['var1'])) {

	$file = ezgif_filename($_GET['var1']);
	$fullPath = '/home/www/img.exs.lv/tmp/' . $file;
	if (file_exists($fullPath)) {
		$tpl->newBlock('split');
		$tpl->assign('file', $file);
		$tpl->assign('menu', ezgif_menu($file));
	} else {
		set_flash('File not found :(', 'error');
		redirect('/split');
	}
} else {
	$tpl->newBlock('split-upl');
}

$tpl->newBlock('meta-description');
$tpl->assign('description', 'Split animated gif image into individual frames for editing or viewing them separately');
