<?php
/**
 * Trūkstošās spēļu serveru kartes
 */
$robotstag[] = 'noindex';

if ($auth->level > 0) {
	$tpl->newBlock('image_upload-admin');

	if (isset($_FILES['new-image'])) {

		require_once(LIB_PATH . '/verot/src/class.upload.php');
		$foo = new Upload($_FILES['new-image']);
		$foo->image_max_pixels = 200000000;
		$foo->image_resize = true;
		$foo->image_convert = 'gif';
		$foo->image_x = 256;
		$foo->image_y = 128;
		$foo->allowed = ['image/*'];
		$foo->image_ratio_crop = true;
		$foo->jpeg_quality = 98;
		$foo->file_overwrite = false;
		$foo->process(IMG_PATH . '/maps/ut2004/');

		if ($foo->processed) {
			$title = str_replace('.gif', '', $foo->file_dst_name);
			set_flash('Karte "' . $foo->file_dst_name . '" veiksmīgi pievienota', 'success');
			redirect('/' . $category->textid);
		}
	}
}

