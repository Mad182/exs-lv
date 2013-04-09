<?php

if (isset($_GET['c']) && !isset($_GET['adr'])) {
	header("HTTP/1.1 301 Moved Permanently");
	redirect('/' . $category->textid);
}

$adr = '';
$port = '27015';
$bgcolor = 'F3F3FF';
$color = '333333';
$height = '180';
$width = '140';
$padding = '4';

if (isset($_GET['adr'])) {
	$adr = sanitize(strtolower(trim($_GET['adr'])));
	$port = (int) $_GET['port'];
	$bgcolor = htmlspecialchars(strip_tags(substr(trim($_GET['bgcolor']), 0, 6)));
	$color = htmlspecialchars(strip_tags(substr(trim($_GET['color']), 0, 6)));
	$height = (int) $_GET['height'];
	$width = (int) $_GET['width'];
	$padding = (int) $_GET['padding'];
	$hash = base64_encode('cs,' . $adr . ',' . $port);
}

$add = '';
if (isset($_GET['players']) && $_GET['players'] == 'true') {
	$add .= '&players=true';
	$pinput = '<input type="hidden" value="true" name="players" />';
} else {
	$pinput = '';
}

if (isset($_GET['notitle']) && $_GET['notitle'] == 'true') {
	$add .= '&notitle=true';
	$notitle = '<input type="checkbox" value="true" name="notitle" checked="checked" />';
} else {
	$notitle = '<input type="checkbox" value="true" name="notitle" />';
}

$tpl->newBlock('csmon');
$tpl->assign(array(
	'adr' => htmlspecialchars($adr),
	'port' => $port,
	'bgcolor' => $bgcolor,
	'color' => $color,
	'height' => $height,
	'width' => $width,
	'padding' => $padding,
	'players' => $pinput,
	'notitle' => $notitle
));


if (isset($_GET['adr'])) {
	$tpl->newBlock('csinc');

	$haddr = 'http://exs.lv/servers/';
	$htitle = 'CS serveri';
	$stitle = '';
	if ($srv = $db->get_row("SELECT * FROM `serverlist` WHERE `uid` = '" . sanitize($hash) . "'")) {
		$haddr .= $srv->id;
		$stitle = htmlspecialchars(strip_tags($srv->title));
		$htitle .= 's ' . $stitle;
	}

	$out = '<!-- cs monitor ' . htmlspecialchars($adr) . ' start -->
<iframe src="http://exs.lv/server.php?s=' . $hash . '&color=' . $color . '&bgcolor=' . $bgcolor . '&padding=' . $padding . $add . '" hspace="0" vspace="0" border="0" frameborder="0" scrolling="no" width="' . $width . '" height="' . $height . '"><a href="' . $haddr . '">' . $htitle . '</a></iframe>
<!-- cs monitor end -->';

	$out_php = '<?php
//' . $htitle . '
echo file_get_contents("http://exs.lv/server.php?php=true&players=true&s=' . $hash . $add . '");
?>';

	$tpl->assign(array(
		'code' => htmlspecialchars($out),
		'code-php' => htmlspecialchars($out_php),
		'iframe' => $out
	));
}

$i = 0;
foreach ($db->get_results("SELECT `uid` FROM `serverlist` WHERE `online` = 1 ORDER BY `updated` DESC LIMIT 3") as $server) {
	$i++;
	$tpl->newBlock('cslatest');
	$tpl->assign(array(
		'src' => 'http://exs.lv/server.php?s=' . $server->uid
	));
}

$pagepath = $category->title;
if ($category->parent) {
	$category2 = get_cat($category->parent);
	$pagepath = '<a href="/' . $category2->textid . '">' . $category2->title . '</a> / ' . $pagepath;
	if ($category2->parent) {
		$category3 = get_cat($category2->parent);
		$pagepath = '<a href="/' . $category3->textid . '">' . $category3->title . '</a> / ' . $pagepath;
	}
}
