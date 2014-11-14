<?php

/**
 * CS serveru saraksts
 */
$robotstag[] = 'noindex';

if (isset($_GET['var1']) && isset($_GET['var2']) && $_GET['var2'] == 'players_online') {
	$sid = (int) $_GET['var1'];

	// This array of values is just here for the example.

	$time = (time() - 24 * 60 * 60);

	$servers = $db->get_results("SELECT `players` FROM `serverlist_log` WHERE `server_id` = '$sid' AND `when` > '$time' ORDER BY `when` DESC");

	$values = array();
	foreach ($servers as $server) {
		$values[] = $server->players;
	}

	$values = array_reverse($values);

	// Get the total number of columns we are going to plot

	$columns = count($values);
	$max = max($values);
	$min = min($values);

	// Get the height and width of the final image

	$width = 530;
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

	imagettftext($im, 8, 0, 12, 178, $gray_dark, $font, 'Online pēdējo 24 h laikā min: ' . $min . ' | max: ' . $max . ' | vidēji: ' . $average);

	// Send the PNG header information. Replace for JPEG or GIF or whatever
	header("Content-type: image/png");
	imagepng($im);
	exit;
} elseif (isset($_GET['var1']) && isset($_GET['var2']) && $_GET['var2'] == 'players_online_week') {
	$sid = (int) $_GET['var1'];

	// This array of values is just here for the example.

	$time = (time() - 24 * 60 * 60 * 7);

	$servers = $db->get_results("SELECT `players` FROM `serverlist_log` WHERE `server_id` = '$sid' AND `when` > '$time' ORDER BY `when` DESC");

	$values = array();
	foreach ($servers as $server) {
		$values[] = $server->players;
	}

	$values = array_reverse($values);

	// Get the total number of columns we are going to plot

	$columns = count($values);
	$max = max($values);
	$min = min($values);

	// Get the height and width of the final image

	$width = 530;
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
	}

	$average = round($total / $columns, 2);

	imagettftext($im, 8, 0, 12, 178, $gray_dark, $font, 'Online pēdējo 7 dienu laikā min: ' . $min . ' | max: ' . $max . ' | vidēji: ' . $average);

	// Send the PNG header information. Replace for JPEG or GIF or whatever
	header("Content-type: image/png");
	imagepng($im);
	exit;
} elseif (isset($_GET['var1'])) {
	$sid = (int) $_GET['var1'];

	$server = $db->get_row("SELECT * FROM serverlist WHERE id = '$sid'");

	if (file_exists('bildes/cs/' . strtolower($server->map) . '.jpg')) {
		$mapimg = strtolower($server->map);
	} else {
		$mapimg = 'none';
	}


	$tpl->newBlock('csview');
	$tpl->assign(array(
		'id' => $server->id,
		'uid' => $server->uid,
		'port' => $server->port,
		'online' => $server->online,
		'time' => time(),
		'maxplayers' => $server->maxplayers,
		'players' => $server->players,
		'mapimg' => $mapimg,
		'address' => htmlspecialchars($server->address),
		'map' => strtolower(htmlspecialchars(textlimit(trim($server->map), 30, '...'))),
		'title' => htmlspecialchars(textlimit($server->title, 64, '...')),
		'code' => htmlspecialchars('<iframe src="http://exs.lv/server.php?s=' . $server->uid . '&color=333333&bgcolor=FFFFFF&padding=4" hspace="0" vspace="0" border="0" frameborder="0" scrolling="no" width="170" height="200"><a href="http://exs.lv/servers/' . $server->id . '">CS serveris ' . htmlspecialchars(strip_tags($server->title)) . '</a></iframe>'),
	));

	$maps = $db->get_results("SELECT DISTINCT map FROM serverlist_log WHERE server_id = '$sid' AND map != '' LIMIT 40");
	$totalcount = 0;
	$coludmaps = array();
	foreach ($maps as $map) {
		$count = $db->get_var("SELECT COUNT(*) FROM serverlist_log WHERE server_id = '$sid' AND map = '" . sanitize($map->map) . "'");
		$totalcount += $count;
		$coludmaps[$map->map] = $count;
	}

	$totalsize = count($coludmaps) * 14;
	foreach ($coludmaps as $map => $count) {
		$size = ceil($totalsize / $totalcount * $count);
		if ($size > 48) {
			$size = 48;
		}
		if ($size < 9) {
			$size = 9;
		}
		$tpl->newBlock('maplist');
		$tpl->assign(array(
			'map' => $map,
			'size' => $size,
		));
	}

	$pagepath = '<a href="/' . $category->textid . '">' . $category->title . '</a> / ' . htmlspecialchars(textlimit($server->title, 30, '...'));
	$page_title = htmlspecialchars(textlimit($server->title, 30, '...')) . ' - CS Serveris';
} else {

	$tpl->newBlock('csservlist');
	$pagepath = '';

	$end = 50;
	if (isset($_GET['page'])) {
		$skip = (int) $_GET['page'] * $end;
	} else {
		$skip = 0;
	}

	$servers = $db->get_results("SELECT id,port,online,maxplayers,players,address,map,title FROM serverlist WHERE map != '' ORDER BY weight ASC, online DESC, players DESC, updated DESC LIMIT $skip,$end");
	foreach ($servers as $server) {
		if (file_exists('bildes/cs/' . strtolower($server->map) . '.jpg')) {
			$mapimg = strtolower($server->map);
		} else {
			$mapimg = 'none';
		}
		$tpl->newBlock('csserver');
		$tpl->assign(array(
			'id' => $server->id,
			'port' => $server->port,
			'online' => $server->online,
			'maxplayers' => $server->maxplayers,
			'players' => $server->players,
			'address' => htmlspecialchars($server->address),
			'mapimg' => $mapimg,
			'map' => strtolower(htmlspecialchars(textlimit(trim($server->map), 30, '...'))),
			'title' => htmlspecialchars(textlimit($server->title, 30, '...'))
		));
	}

	$total = $db->get_var("SELECT count(*) FROM serverlist WHERE map != ''");
	if ($total > $end) {
		if ($skip > 0) {
			if ($skip > $end) {
				$iepriekseja = $skip - $end;
			} else {
				$iepriekseja = 0;
			}
			$pager_next = '<a class="pager-next" title="Iepriekšējā lapa" href="/Servers/?page=' . $iepriekseja / $end . '">&laquo;</a>';
		} else {
			$pager_next = '';
		}
		$pager_prev = '';
		if ($total > $skip + $end) {
			$pager_prev = '<span>-</span> <a class="pager-prev" title="Nākamā lapa" href="/Servers/?page=' . ($skip + $end) / $end . '">&raquo;</a>';
		}
		$startnext = 0;
		$page_number = 0;
		$pager_numeric = '';
		while ($total - $startnext > 0) {
			$page_number++;
			$class = '';
			if ($skip == $startnext) {
				$class = ' class="selected"';
			}
			$pager_numeric .= '<span>-</span> <a href="/Servers/?page=' . $startnext / $end . '"' . $class . '>' . $page_number . '</a> ';
			$startnext = $startnext + $end;
		}
		$tpl->assignGlobal(array(
			'pager-next' => $pager_next,
			'pager-prev' => $pager_prev,
			'pager-numeric' => $pager_numeric
		));
	}
}

$tpl->newBlock('meta-description');
$tpl->assign('description', 'Populārāko Latvijas Counter Strike un Counter Strike Source serveru saraksts, pēc online spēlētāju skaita');

