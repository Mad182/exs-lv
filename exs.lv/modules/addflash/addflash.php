<?php

/**
 * Flash spēles augšupielāde
 */
$robotstag[] = 'noindex';
 
if ($auth->id != 1) {
	redirect();
}

if (isset($_POST['submit'])) {

	$title = sanitize($_POST['title']);
	$description = sanitize($_POST['description']);
	$slug = mkslug($_POST['title']);
	$flash_file = sanitize($_POST['flash_file']);
	$width = intval($_POST['width']);
	$height = intval($_POST['height']);

	$thb_local = '';
	if (isset($_FILES['thb_local'])) {
		require_once(CORE_PATH . '/includes/class.upload.php');
		$text = strtolower(str_replace('-', '_', $slug));
		$foo = new Upload($_FILES['thb_local']);
		$foo->file_new_name_body = $text;
		$foo->image_resize = true;
		$foo->image_convert = 'jpg';
		$foo->image_x = 93;
		$foo->image_y = 74;
		$foo->allowed = array('image/*');
		$foo->image_ratio_crop = true;
		$foo->jpeg_quality = 94;
		$foo->file_auto_rename = false;
		$foo->file_overwrite = true;
		$foo->process(CORE_PATH . '/upload/flash-games/thb/');
		if ($foo->processed) {
			$thb_local = $text . '.jpg';
			$user->av_alt = 1;
			$foo->clean();
		}
	}

	$db->query("INSERT INTO flash_games (slug,title,thb_local,launch_date,category,category_slug,flash_file,width,height,description,gameplays,rating,rating_count)
		VALUES ('$slug','$title','$thb_local','" . date('Y-m-d') . "','Dažādas','Dazadas','$flash_file','$width','$height','$description','1','4','1')
	");
	userlog($auth->id, 'Pievienoja flash spēli <a href="/flash-speles/' . $slug . '" title="Spēle ' . h($title) . '">' . $title . '</a>', '/upload/flash-games/thb/' . $thb_local);
	redirect('/addflash');
}

