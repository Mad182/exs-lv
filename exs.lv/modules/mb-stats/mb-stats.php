<?php

if (isset($_GET['var1']) && $_GET['var1'] == 'chart.jpg') {

	$expires = 300;
	header('Pragma: public');
	header('Cache-Control: max-age=' . $expires);
	header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');

	$sid = (int) $_GET['var1'];

	if (isset($_GET['user'])) {
		$user = (int) $_GET['user'];
		$servers = $db->get_results("SELECT COUNT(*) as `count`, DATE(`date`) as `date` FROM `miniblog` WHERE `author` = '$user' AND `miniblog`.`removed` = '0' GROUP BY DATE(`miniblog`.`date`) ORDER BY `date` DESC LIMIT 365");
	} else {
		$servers = $db->get_results("SELECT COUNT(*) as `count`, DATE(`date`) as `date` FROM `miniblog` WHERE `miniblog`.`removed` = '0' GROUP BY DATE(`miniblog`.`date`) ORDER BY `date` DESC LIMIT 365");
	}

	$values = array();
	foreach ($servers as $server) {
		$values[$server->date] = $server->count;
	}

	$data = array();
	for ($i = 0; $i <= 364; $i++) {
		$key = date('Y-m-d', strtotime('-' . $i . ' days'));
		if (!empty($values[$key])) {
			$data[$key] = $values[$key];
		} else {
			$data[$key] = 0;
		}
	}

	$values = array_reverse($data);

	// Get the total number of columns we are going to plot

	$columns = count($values) + 1;
	$max = max($values);
	$min = min($values);

	// Get the height and width of the final image

	$width = 750;
	$height = 160;

	// Set the amount of space between each column

	$padding = 1;

	// Get the width of 1 column

	$column_width = ($width - 12) / $columns;

	// Generate the image variables

	$im = imagecreate($width, $height + 20);
	$gray = imagecolorallocate($im, 0xcc, 0xcc, 0xcc);
	$gray_lite = imagecolorallocate($im, 0xee, 0xee, 0xee);
	$gray_dark = imagecolorallocate($im, 0x7f, 0x7f, 0x7f);
	$white = imagecolorallocate($im, 0xff, 0xff, 0xff);

	// Fill in the background of the image

	imagefilledrectangle($im, 0, 0, $width, $height + 20, $white);

	$maxv = 0;

	// Calculate the maximum value we are going to plot

	foreach ($values as $key => $val) {
		$maxv = max($val, $maxv);
	}


	$font = 'modules/servers/Ubuntu-R.ttf';

	// Max
	imagettftext($im, 7, 0, 0, 8, $gray_dark, $font, $max);
	imagettftext($im, 7, 0, 0, 160, $gray_dark, $font, 0);


	// Now plot each column
	$total = 0;
	$i = 0;
	foreach ($values as $key => $val) {

		$i++;
		$total = $total + $val;
		$column_height = ($height / 100) * (( $val / $maxv) * 100);

		$x1 = $i * $column_width;
		$y1 = $height - $column_height;
		$x2 = (($i + 1) * $column_width) - $padding;
		$y2 = $height;

		imagefilledrectangle($im, $x1 + 12, $y1, $x2 + 12, $y2, $gray);

		// This part is just for 3D effect

		imageline($im, $x1 + 12, $y1, $x1 + 12, $y2, $gray_lite);
		imageline($im, $x1 + 12, $y2, $x2 + 12, $y2, $gray_lite);
		imageline($im, $x2 + 12, $y1, $x2 + 12, $y2, $gray_dark);
	}

	$average = round($total / $columns, 2);

	imagettftext($im, 8, 0, 12, 178, $gray_dark, $font, 'Miniblogu postu skaits pēdējās 365 dienās | min: ' . $min . ' | max: ' . $max . ' | vidēji: ' . $average);

	// Send the PNG header information. Replace for JPEG or GIF or whatever
	header("Content-type: image/png");
	imagepng($im);
	exit;
}



if (isset($_GET['var1']) && $_GET['var1'] == 'chart2.jpg') {

	$expires = 600;
	header('Pragma: public');
	header('Cache-Control: max-age=' . $expires);
	header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');

	$sid = (int) $_GET['var1'];

	$servers = $db->get_results("SELECT COUNT(*) as `count`, DATE(`date`) as `date` FROM `miniblog` WHERE `miniblog`.`removed` = '0' GROUP BY DATE(`miniblog`.`date`) ORDER BY `date` DESC");

	$values = array();
	foreach ($servers as $server) {
		$values[] = $server->count;
	}

	$values = array_reverse($values);

	// Get the total number of columns we are going to plot

	$columns = count($values);
	$max = max($values);
	$min = min($values);

	// Get the height and width of the final image

	$width = 750;
	$height = 160;

	// Set the amount of space between each column

	$padding = 1;

	// Get the width of 1 column

	$column_width = ($width - 12) / $columns;

	// Generate the image variables

	$im = imagecreate($width, $height + 20);
	$gray = imagecolorallocate($im, 0xcc, 0xcc, 0xcc);
	$gray_lite = imagecolorallocate($im, 0xee, 0xee, 0xee);
	$gray_dark = imagecolorallocate($im, 0x7f, 0x7f, 0x7f);
	$white = imagecolorallocate($im, 0xff, 0xff, 0xff);

	// Fill in the background of the image

	imagefilledrectangle($im, 0, 0, $width, $height + 20, $white);

	$maxv = 0;

	// Calculate the maximum value we are going to plot

	for ($i = 0; $i < $columns; $i++)
		$maxv = max($values[$i], $maxv);


	$font = 'modules/servers/Ubuntu-R.ttf';

	// Max
	imagettftext($im, 7, 0, 0, 8, $gray_dark, $font, $max);
	imagettftext($im, 7, 0, 0, 160, $gray_dark, $font, 0);


	// Now plot each column
	$total = 0;
	for ($i = 0; $i < $columns; $i++) {
		$total = $total + $values[$i];
		$column_height = ($height / 100) * (( $values[$i] / $maxv) * 100);

		$x1 = $i * $column_width;
		$y1 = $height - $column_height;
		$x2 = (($i + 1) * $column_width) - $padding;
		$y2 = $height;

		imagefilledrectangle($im, $x1 + 12, $y1, $x2 + 12, $y2, $gray);

		// This part is just for 3D effect

		imageline($im, $x1 + 12, $y1, $x1 + 12, $y2, $gray_lite);
		imageline($im, $x1 + 12, $y2, $x2 + 12, $y2, $gray_lite);
		imageline($im, $x2 + 12, $y1, $x2 + 12, $y2, $gray_dark);
	}

	$average = round($total / $columns, 2);

	imagettftext($im, 8, 0, 12, 178, $gray_dark, $font, 'Miniblogu postu skaits "ever" | min: ' . $min . ' | max: ' . $max . ' | vidēji: ' . $average);

	// Send the PNG header information. Replace for JPEG or GIF or whatever
	header("Content-type: image/png");
	imagepng($im);
	exit;
}

$tpl->prepare();
