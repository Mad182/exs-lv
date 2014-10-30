<?php

/**
 * Parāda upload formu un apstrādā POST datus
 */
function gallery_upload() {
	global $db, $tpl, $auth, $remote_salt, $lang;

	$check = md5($remote_salt . $auth->xsrf . '-add-image');

	//add new image
	if (isset($_FILES['new-image'])) {

		if (!isset($_POST['unique-form-check']) || $_POST['unique-form-check'] !== $check) {
			set_flash('Aizdomas par spamu. Attēla pievienošana pārtraukta. Ja uzskati, ka tā ir kļūda, ziņo administrācijai!', 'error');
			redirect('/gallery/' . $auth->id);
		}

		if (!isset($_SESSION['antiflood']) or $_SESSION['antiflood'] < time() - $auth->flood) {
			$_SESSION['antiflood'] = time();

			require_once(CORE_PATH . '/includes/class.upload.php');

			ini_set('memory_limit', '180M');

			$description = post2db($_POST['new-image-description']);

			//interešu kategorijas id. Ja mēģina nofeikot, tad 0
			$interest_id = (isset($_POST['new-image-interest'])) ? (int) $_POST['new-image-interest'] : 0;
			if (!$db->get_var("SELECT count(*) FROM `interests` WHERE `id` = '$interest_id'")) {
				$interest_id = 0;
			}

			$text = date('dHis') . '_' . $auth->id;
			$folder = 'dati/bildes/g' . date('Y') . '_' . date('m') . '/';
			rmkdir($folder);
			$foo = new Upload($_FILES['new-image']);
			$foo->file_new_name_body = 'large_' . $text;
			$foo->image_resize = true;
			$foo->image_convert = 'jpg';
			$foo->image_x = 960;
			$foo->image_y = 800;
			$foo->allowed = array('image/*');
			$foo->image_ratio_no_zoom_in = true;
			$foo->jpeg_quality = 98;
			$foo->process($folder);

			if ($foo->processed) {
				$foo->file_new_name_body = 'thb_' . $text;
				$foo->image_resize = true;
				$foo->image_convert = 'jpg';
				$foo->image_x = 72;
				$foo->image_y = 72;
				$foo->allowed = array('image/*');
				$foo->image_ratio_crop = true;
				$foo->process($folder);
				$foo->clean();

				$file = $folder . 'large_' . $text . '.jpg';
				$thb = $folder . 'thb_' . $text . '.jpg';

				if (file_exists($file)) {
					$db->query("INSERT INTO `images` (`id`,`uid`,`url`,`thb`,`text`,`date`,`bump`,`ip`,`interest_id`,`lang`) VALUES (NULL,'$auth->id','$file','$thb','$description',NOW(),NOW(),'$auth->ip','$interest_id', '$lang')");
					push('Pievienoja <a href="/gallery/' . $auth->id . '/' . $db->insert_id . '">jaunu attēlu ' . textlimit($description, 32, '...') . '</a>', '//img.exs.lv/' . $thb);
					update_karma($auth->id, true);
				}
			} else {
				set_flash('Attēlu neizdevās pievienot: ' . $foo->error, 'error');
			}
		} else {
			set_flash('Izskatās pēc flooda. Pagaidi 10 sekundes, pirms pievieno jaunu attēlu!', 'error');
		}

		redirect('/gallery/' . $auth->id);
	}

	if ($auth->maximg <= $db->get_var("SELECT count(*) FROM `images` WHERE `uid` = '" . $auth->id . "'")) {
		$tpl->newBlock('add-image-max');
		$tpl->assign('max', $auth->maximg);
	} else {
		$tpl->newBlock('add-image-form');
		$tpl->assign('gallery-check', $check);

		if ($lang == 1) {
			$tpl->newBlock('new-image-interest');
			$interests = $db->get_results("SELECT * FROM `interests` ORDER BY `id` ASC");
			foreach ($interests as $interest) {
				$tpl->newBlock('select-new-interest');
				$tpl->assignAll($interest);
			}
		}
	}
}
