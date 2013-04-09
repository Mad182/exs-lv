<?php

$colors_manual = 0;

if (isset($_POST['file'])) {

	$file = ezgif_filename($_POST['file']);
	$fullPath = '/home/www/img.exs.lv/tmp/' . $file;

	if (file_exists($fullPath)) {

		$out = 'gif_' . ezgif_filename($_POST['method']) . '_' . $file;

		switch ($_POST['method']) {
			case 'gifsicle_1':
				if (!file_exists('/home/www/img.exs.lv/tmp/' . $out)) {
					$str = "gifsicle -O1 '/home/www/img.exs.lv/tmp/" . $file . "' > '/home/www/img.exs.lv/tmp/" . $out . "'";
					$test = `$str`;
				}
				break;

			case 'gifsicle_2':

				if (!file_exists('/home/www/img.exs.lv/tmp/' . $out)) {
					$str = "gifsicle -O2 " . $width . "x" . $height . " '/home/www/img.exs.lv/tmp/" . $file . "' > '/home/www/img.exs.lv/tmp/" . $out . "'";
					$test = `$str`;
				}

				break;

			case 'im_color_dither':
			default:
				
				$colors_manual = 0;
				if(!empty($_POST['colors'])) {
					$colors_manual = (int)$_POST['colors'];
					if($colors_manual < 1 || $colors_manual > 256) {
						$colors_manual = 0;
					}
				}
				
				if($colors_manual) {
					$colors = array($colors_manual);
				} else {
					$colors = array(200, 128, 90, 64, 32, 16, 8);
				}
				
				
				$fuzz = 5;

				foreach ($colors as $color) {
					$fuzz++;
					$out = 'gif_' . ezgif_filename($_POST['method']) . '_' . $color . '_' . $file;
					if (!file_exists('/home/www/img.exs.lv/tmp/' . $out)) {
						$str = "convert '/home/www/img.exs.lv/tmp/" . $file . "' +dither -colors " . $color . " '/home/www/img.exs.lv/tmp/" . $out . "'";
						$test = `$str`;
					}

					//-fuzz " . $fuzz . "% -layers OptimizeFrame

					$fs = filesize('/home/www/img.exs.lv/tmp/' . $out);
					echo '<p><img src="http://img.exs.lv/tmp/' . $out . '" alt="" /></p>';
					echo '<p>Colors: ' . $color . ', File size: ' . human_filesize($fs) . '</p>';
					echo '<p>' . ezgif_menu($out) . '</p>';
				}

				break;

			case 'im_color':

				$colors_manual = 0;
				if(!empty($_POST['colors'])) {
					$colors_manual = (int)$_POST['colors'];
					if($colors_manual < 1 || $colors_manual > 256) {
						$colors_manual = 0;
					}
				}
				
				if($colors_manual) {
					$colors = array($colors_manual);
				} else {
					$colors = array(200, 128, 90, 64, 32, 16, 8);
				}

				$fuzz = 5;

				foreach ($colors as $color) {
					$out = 'gif_' . ezgif_filename($_POST['method']) . '_' . $color . '_' . $file;
					if (!file_exists('/home/www/img.exs.lv/tmp/' . $out)) {
						$str = "convert '/home/www/img.exs.lv/tmp/" . $file . "' -colors " . $color . " '/home/www/img.exs.lv/tmp/" . $out . "'";
						$test = `$str`;
					}

					//-fuzz " . $fuzz . "% -layers OptimizeFrame 

					$fs = filesize('/home/www/img.exs.lv/tmp/' . $out);
					echo '<p><img src="http://img.exs.lv/tmp/' . $out . '" alt="" /></p>';
					echo '<p>Colors: ' . $color . ', File size: ' . human_filesize($fs) . '</p>';
					echo '<p>' . ezgif_menu($out) . '</p>';
				}

				break;
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
			$tpl->newBlock('optimize-upl');
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
		redirect('/optimize/' . $foo->file_dst_name);
	} else {
		set_flash('Error: ' . $foo->error, 'error');
		$tpl->newBlock('optimize-upl');
	}
} elseif (isset($_GET['var1'])) {

	$file = ezgif_filename($_GET['var1']);
	$fullPath = '/home/www/img.exs.lv/tmp/' . $file;
	if (file_exists($fullPath)) {
		$imsize = getimagesize($fullPath);
		$fs = filesize($fullPath);

		$tpl->newBlock('optimize');
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
		redirect('/optimize');
	}
} else {
	$tpl->newBlock('optimize-upl');
}

$tpl->newBlock('meta-description');
$tpl->assign('description', 'Optimize animated gif images online to reduce file size');
