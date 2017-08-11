<?php

if (isset($_GET['skip'])) {
	$skip = (int) $_GET['skip'];
} else {
	$skip = 0;
}

$total = $db->get_var("SELECT count(*) FROM wallpapers WHERE date <= '" . date('Y-m-d') . "'");

$end = 60;

if (im_mod() && $skip === 0) {
	$wallpapers = $db->get_results("SELECT image,date FROM wallpapers WHERE date > '" . date('Y-m-d') . "' ORDER BY date DESC");
	if ($wallpapers) {
		foreach ($wallpapers as $image) {
			$tpl->newBlock('wallpaper');
			$tpl->assign([
				'wallpaper-image' => $image->image,
				'wallpaper-date' => $image->date,
				'style' => ' style="color:red;"'
			]);
		}
	}
}

$wallpapers = $db->get_results("SELECT image,date FROM wallpapers WHERE date <= '" . date('Y-m-d') . "' ORDER BY date DESC LIMIT $skip,$end");
if ($wallpapers) {
	foreach ($wallpapers as $image) {
		$tpl->newBlock('wallpaper');
		$tpl->assign([
			'wallpaper-image' => $image->image,
			'wallpaper-date' => $image->date
		]);
	}
}

$pager = pager($total, $skip, $end, '/' . $category->textid . '/?skip=');
$tpl->assignGlobal([
	'pager-next' => $pager['next'],
	'pager-prev' => $pager['prev'],
	'pager-numeric' => $pager['pages']
]);

