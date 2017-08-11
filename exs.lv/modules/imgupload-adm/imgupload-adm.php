<?php

/**
 * img.exs.lv jaunāko attēlu apskate un dzēšana
 */
if ($auth->id != 1) {
	redirect();
}

$images = $db->get_results("SELECT * FROM `imgupload` ORDER BY `id` DESC LIMIT 500");

$out = '';
if (!empty($images)) {
	foreach ($images as $img) {
		if (isset($_GET['delete'])) {
			if ($img->id == $_GET['delete']) {
				$db->query("DELETE FROM `imgupload` WHERE `id` = '$img->id' LIMIT 1");
				unlink(IMG_PATH . '/' . $img->path . '/' . $img->file);
				unlink(IMG_PATH . '/' . $img->path . '/small/' . $img->file);
				continue;
			}
		}
		$out .= '<a href="?delete=' . $img->id . '"><img style="width: 50px;" src="' . $img_server . '/' . $img->path . '/small/' . $img->file . '" alt="" /></a> ';
	}
}

$tpl->assignGlobal('out-pics', $out);
