<?php

if (isset($_GET['var1'])) {

	$file = ezgif_filename($_GET['var1']);

	$fullPath = '/home/www/img.exs.lv/tmp/' . $file;

	if ($fd = fopen($fullPath, "r")) {
		$fsize = filesize($fullPath);
		header('Content-Description: File Transfer');
		header("Content-type: image/gif");
		header('Content-Disposition: attachment; filename="' . $file . '"');
		header("Content-length: $fsize");
		header("Cache-control: private");
		while (!feof($fd)) {
			$buffer = fread($fd, 2048);
			echo $buffer;
		}
		exit;
	} else {
		set_flash('File not found :(', 'error');
		redirect('/');
	}
} else {
	set_flash('File not found :(', 'error');
	redirect('/');
}
