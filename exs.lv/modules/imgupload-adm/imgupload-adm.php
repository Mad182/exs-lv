<?php

/**
 * img.exs.lv jaunāko attēlu apskate un dzēšana
 */
if ($auth->id != 1) {
	redirect();
}

$total = $db->get_var("SELECT count(*) FROM `imgupload`");

if (isset($_GET['var2'])) {
	$skip = (int) $_GET['var2'];
} else {
	$skip = 0;
}
$end = 500;

$images = $db->get_results("SELECT * FROM `imgupload` ORDER BY `id` DESC LIMIT $skip,$end");

$out = '';
if (!empty($images)) {
	foreach ($images as $img) {
		if (isset($_GET['delete'])) {
			if ($img->id == $_GET['delete']) {
				$db->query("DELETE FROM `imgupload` WHERE `id` = '$img->id' LIMIT 1");
				unlink(IMG_PATH . '/' . $img->path . '/' . $img->file);
				unlink(IMG_PATH . '/' . $img->path . '/small/' . $img->file);
				die('ok');
				continue;
			}
		}
		$out .= '<a href="?delete=' . $img->id . '"><img src="' . $img_server . '/' . $img->path . '/small/' . $img->file . '" style="max-width:200px" /></a> ';
	}
}

	$pager = pager($total, $skip, $end, '/imgupload-adm/skip/');

	$out .= '<p class="core-pager">'.$pager['next'].' '.$pager['pages'].' '.$pager['prev'].'</p>';

$tpl->assignGlobal('out-pics', $out);
