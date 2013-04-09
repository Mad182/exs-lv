<?php

if(isset($_GET['var1'])) {

	$file = ezgif_filename($_GET['var1']);

	$filename_no_ext = mkslug($file);

	header("Content-Type: archive/zip");
	header("Content-Disposition: attachment; filename=$filename_no_ext".".zip");
	$tmp_zip = tempnam ("tmp", "tempname") . ".zip";

	if(is_dir('/home/www/img.exs.lv/tmp/'.$filename_no_ext.'/')) {

		//change directory so the zip file doesnt have a tree structure in it.
		chdir('/home/www/img.exs.lv/tmp/'.$filename_no_ext.'/');

		// zip the stuff (dir and all in there) into the tmp_zip file
		exec('zip '.$tmp_zip.' *.gif');

		// calc the length of the zip. it is needed for the progress bar of the browser
		$filesize = filesize($tmp_zip);
		header("Content-Length: $filesize");

		// deliver the zip file
		$fp = fopen("$tmp_zip","r");
		echo fpassthru($fp);

		// clean up the tmp zip file
		unlink($tmp_zip);

		exit;

	}

}

set_flash('File not found :(', 'error');
redirect('/');
