<?php

if(isset($_POST['file'])) {

	$file = ezgif_filename($_POST['file']);

	$path = '/tmp/'.mkslug($file).'/';

	$delay = (int)$_POST['delay'];
	if($delay <= 0) {
		$delay = 1;
	}

	$out = 'ezgif_animate_'.uniqid().'.gif';

	if(is_dir('/home/www/img.exs.lv'.$path)) {

		$fstirng = '';
		foreach($_POST['files'] as $frame) {
			$fstirng .= ' /home/www/img.exs.lv'.$path.ezgif_filename($frame);
		}

		$str = "gifsicle --delay=".$delay." --loop".$fstirng." > /home/www/img.exs.lv/tmp/".$out;
		$test = `$str`;

		$fs = filesize('/home/www/img.exs.lv/tmp/' . $out);

		echo '<p><img src="http://img.exs.lv/tmp/' . $out . '" alt="" /></p>';

		echo '<p>File size: ' . human_filesize($fs) . '</p>';

		echo '<p>' . ezgif_menu($out) . '</p>';

		echo '<p>Please do not hotlink, but save the image when finished.<br />Files are not stored here indefinitely.</p>';
		exit;

	} else {
		die('Error while trying to animate.');
	}


} elseif (isset($_FILES['new-image'])) {


	if ($foo->processed) {
		redirect('/animate/' . $foo->file_dst_name);
	} else {
		set_flash('Error: ' . $foo->error, 'error');
		$tpl->newBlock('split-upl');
	}

} elseif (isset($_GET['var1'])) {

	$file = ezgif_filename($_GET['var1']);

	$folder = '/home/www/img.exs.lv/tmp/'.$file;

	if(!empty($file) && is_dir($folder)) {
		$tpl->newBlock('animate');
		$tpl->assign('file', $file);
		$files = array();
		if ($handle = opendir($folder)) {
			while (false !== ($ffile = readdir($handle))) {
				if ($ffile != "." && $ffile != "..") {
					$files[] = $ffile;
				}
			}
			closedir($handle);

			sort($files);


			foreach($files as $ffile) {
				$tpl->newBlock('animate-frame');
				$tpl->assign(array(
					'file' => $ffile,
					'folder' => $file,
				));
			}

		}

	} else {
		set_flash('File not found :(', 'error');
		redirect('/animate');
	}

} else {
	$tpl->newBlock('animate-upl');
}

//$tpl->newBlock('meta-description');
//$tpl->assign('description', 'Split animated gif image into individual frames for editing or viewing them separately');

