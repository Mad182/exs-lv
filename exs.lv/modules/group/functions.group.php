<?php

/**
 * Updato grupas apstiprināto lietotāju skaitu grupu tabulā
 */
function update_members($group_id) {
	global $db;
	return $db->query("UPDATE `clans` SET `members` = (SELECT count(*) FROM `clans_members` WHERE `clan` = " . intval($group_id) . " AND `approve` = 1) WHERE `id` = " . intval($group_id) . " LIMIT 1");
}

/**
 * Uploado avataru, atgriež faila nosaukumu
 */
function upload_user_avatar($post, $old_filename, $text) {
	global $group;

	$return = $old_filename;

	if (isset($post)) {
		require_once(LIB_PATH . '/verot/src/class.upload.php');
		$text = 'group_' . time() . '_' . $group->id;
		$foo = new Upload($post);
		$foo->image_max_pixels = 200000000;
		$foo->file_new_name_body = $text;
		$foo->image_resize = true;
		$foo->image_convert = 'jpg';
		$foo->image_x = 90;
		$foo->image_y = 90;
		$foo->allowed = ['image/*'];
		$foo->image_ratio_crop = true;
		$foo->jpeg_quality = 97;
		$foo->file_auto_rename = false;
		$foo->file_overwrite = true;
		$foo->process('dati/bildes/useravatar/');
		if ($foo->processed) {

			$foo->file_new_name_body = $text;
			$foo->image_resize = true;
			$foo->image_convert = 'jpg';
			$foo->image_x = 45;
			$foo->image_y = 45;
			$foo->allowed = ['image/*'];
			$foo->image_ratio_crop = true;
			$foo->jpeg_quality = 97;
			$foo->file_auto_rename = false;
			$foo->file_overwrite = true;
			$foo->process('dati/bildes/u_small/');

			$foo->file_new_name_body = $text;
			$foo->image_resize = true;
			$foo->image_convert = 'jpg';
			$foo->image_x = 180;
			$foo->image_y = 220;
			$foo->allowed = ['image/*'];
			$foo->image_ratio_crop = false;
			$foo->image_ratio_no_zoom_in = true;
			$foo->jpeg_quality = 97;
			$foo->file_auto_rename = false;
			$foo->file_overwrite = true;
			$foo->process('dati/bildes/u_large/');

			if (file_exists('dati/bildes/useravatar/' . $text . '.jpg')) {
				$return = $text . '.jpg';
			}
			$foo->clean();
		}
	}
	return $return;
}

/**
 * Updato grupas postu skaitu
 */
function update_post_count($group_id) {
	global $db;
	
	$posts_total = (int) $db->get_var("SELECT count(*) FROM `miniblog` WHERE `groupid` = " . intval($group_id) . " AND `removed` = 0");
	$posts_today = (int) $db->get_var("SELECT count(*) FROM `miniblog` WHERE `groupid` = " . intval($group_id) . " AND `date` > '" . date('Y-m-d') . " 00:00:00' AND `removed` = 0");
	
	$db->query("UPDATE `clans` SET `last_activity` = NOW() WHERE `id` = " . intval($group_id));
	
	return $db->update('clans', $group_id, ['posts' => $posts_total, 'posts_today' => $posts_today]);
}

