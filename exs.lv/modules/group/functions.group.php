<?php

/* updato grupas apstiprināto lietotāju skaitu grupu tabulā */
function update_members($group) {
	global $db;
	return $db->query("UPDATE `clans` SET `members` = (SELECT count(*) FROM `clans_members` WHERE `clan` = " . intval($group) . " AND `approve` = 1) WHERE `id` = " . intval($group));
}

/* uploado avataru, atgriež faila nosaukumu */
function upload_user_avatar($post, $old_filename, $text) {
	global $group;

	$return = $old_filename;

	if (isset($post)) {
		require_once(CORE_PATH . '/includes/class.upload.php');
		$text = 'group_' . time() . '_' . $group->id;
		$foo = new Upload($post);
		$foo->file_new_name_body = $text;
		$foo->image_resize = true;
		$foo->image_convert = 'jpg';
		$foo->image_x = 90;
		$foo->image_y = 90;
		$foo->allowed = array('image/*');
		$foo->image_ratio_crop = true;
		$foo->jpeg_quality = 97;
		$foo->file_auto_rename = false;
		$foo->file_overwrite = true;
		$foo->process('dati/bildes/useravatar/');
		if ($foo->processed) {

			$foo = new Upload($_FILES['edit-avatar']);
			$foo->file_new_name_body = $text;
			$foo->image_resize = true;
			$foo->image_convert = 'jpg';
			$foo->image_x = 45;
			$foo->image_y = 45;
			$foo->allowed = array('image/*');
			$foo->image_ratio_crop = true;
			$foo->jpeg_quality = 97;
			$foo->file_auto_rename = false;
			$foo->file_overwrite = true;
			$foo->process('dati/bildes/u_small/');

			$foo = new Upload($_FILES['edit-avatar']);
			$foo->file_new_name_body = $text;
			$foo->image_resize = true;
			$foo->image_convert = 'jpg';
			$foo->image_x = 180;
			$foo->image_y = 220;
			$foo->allowed = array('image/*');
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
