<?php
/**
 * Trūkstošās spēļu serveru kartes
 */
$robotstag[] = 'noindex';

if ($auth->level > 0) {
	$tpl->newBlock('image_upload-admin');

	if (isset($_FILES['new-image'])) {

		require(CORE_PATH . '/includes/class.upload.php');
		$foo = new Upload($_FILES['new-image']);
		$foo->image_resize = true;
		$foo->image_convert = 'jpg';
		$foo->image_x = 256;
		$foo->image_y = 128;
		$foo->allowed = ['image/*'];
		$foo->image_ratio_crop = true;
		$foo->jpeg_quality = 98;
		$foo->file_overwrite = true;
		$foo->process(IMG_PATH . '/maps/csgo/');

		if ($foo->processed) {
			$title = str_replace('.jpg', '', $foo->file_dst_name);
			set_flash('Karte "' . $foo->file_dst_name . '" veiksmīgi pievienota', 'success');
			redirect('/' . $category->textid);
		}
	}
}

