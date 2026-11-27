<?php

/**
 * img.exs.lv jaunāko attēlu apskate un dzēšana
 */
if ($auth->id != 1) {
	redirect();
}

if (isset($_GET['delete']) && check_token('delimgadm-' . $_GET['delete'], $_GET['token'] ?? '')) {
	$del_id = (int) $_GET['delete'];
	$img = $db->get_row("SELECT * FROM `imgupload` WHERE `id` = '$del_id' LIMIT 1");
	if ($img) {
		$db->query("DELETE FROM `imgupload` WHERE `id` = '$img->id' LIMIT 1");
		if (file_exists(IMG_PATH . '/' . $img->path . '/' . $img->file)) {
			unlink(IMG_PATH . '/' . $img->path . '/' . $img->file);
		}
		if (file_exists(IMG_PATH . '/' . $img->path . '/small/' . $img->file)) {
			unlink(IMG_PATH . '/' . $img->path . '/small/' . $img->file);
		}
		die('ok');
	}
}

$total = $db->get_var("SELECT count(*) FROM `imgupload`");

if (isset($_GET['var2'])) {
	$skip = max(0, (int) $_GET['var2']);
} else {
	$skip = 0;
}
$end = 500;

$images = $db->get_results("SELECT * FROM `imgupload` ORDER BY `id` DESC LIMIT $skip,$end");

$out = '';
if (!empty($images)) {
	foreach ($images as $img) {
		$out .= '<a href="?delete=' . $img->id . '&amp;token=' . make_token('delimgadm-' . $img->id) . '"><img src="' . $img_server . '/' . $img->path . '/small/' . $img->file . '" style="max-width:200px" /></a> ';
	}
}

	$pager = pager($total, $skip, $end, '/imgupload-adm/skip/');

	$out .= '<p class="core-pager">'.$pager['next'].' '.$pager['pages'].' '.$pager['prev'].'</p>';

$tpl->assignGlobal('out-pics', $out);
