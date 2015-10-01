<?php
/**
 * Trūkstošās CS kartes
 */
$robotstag[] = 'noindex';

if ($auth->level > 0) {
	$tpl->newBlock('image_upload-admin');


	$lostmaps = $db->get_results("SELECT * FROM `lostmaps` WHERE `game` = 'cs' ORDER BY `hits` DESC LIMIT 100");
	$out = '<h3>Visvairāk vajadzīgās kartes</h3><table class="table"><tr><th>Karte</th><th>Neveiksmīgi pieprasījumi</th></tr>';
	foreach ($lostmaps as $lostmap) {
		$out .= '<tr><td>' . $lostmap->title . '</td><td>' . $lostmap->hits . '</td></tr>';
	}
	$out .= '</table>';
	$tpl->assign('lost', $out);


	if (isset($_FILES['new-image'])) {

		require(CORE_PATH . '/includes/class.upload.php');
		$foo = new Upload($_FILES['new-image']);
		$foo->image_max_pixels = 200000000;
		$foo->image_resize = true;
		$foo->image_convert = 'jpg';
		$foo->image_x = 128;
		$foo->image_y = 79;
		$foo->allowed = array('image/*');
		$foo->image_ratio_crop = true;
		$foo->jpeg_quality = 98;
		$foo->file_overwrite = true;
		$foo->process(CORE_PATH . '/bildes/cs/');

		if ($foo->processed) {
			$title = str_replace('.jpg', '', $foo->file_dst_name);
			$db->query("DELETE FROM `lostmaps` WHERE `title` = '" . sanitize($title) . "'");
			set_flash('Karte "' . $foo->file_dst_name . '" veiksmīgi pievienota', 'success');
			redirect('/' . $category->textid);
		}
	}
}

