<?php

/**
 * Junk attēlu apstiprināšana
 */
if (!im_mod()) {
	set_flash('Šī sadaļa paredzēta tikai administrācijai!', 'error');
	redirect();
}

ini_set('memory_limit', '256M');

function make_thumb($data, $path, $id) {
	require_once(CORE_PATH . '/includes/class.upload.php');
	$foo = new Upload($data);
	$foo->allowed = array('image/*');
	$foo->image_resize = true;
	$foo->image_ratio = true;
	$foo->image_y = 130;
	$foo->image_x = 112;
	$foo->file_new_name_body = $id;
	$foo->image_ratio_no_zoom_in = false;
	$foo->image_ratio_crop = true;
	$foo->image_convert = 'jpg';
	$foo->process('/home/www/img.exs.lv/junk/thb/' . $path . '/');
	if ($foo->processed) {
		return $foo->file_dst_name;
	} else {
		return false;
	}
}

if (isset($_GET['var1']) && isset($_GET['var2']) && $_GET['var2'] == 'lol') {
	$id = (int) $_GET['var1'];
	if ($pic = $db->get_row("SELECT * FROM `junk_queue` WHERE `approved` = 0 AND `id` = '$id'")) {

		$data = curl_get($pic->image, 5, 15);
		$ext = substr($pic->image, -4);
		if ($data) {
			$dir1 = substr($pic->id, -1);
			if (!$dir1) {
				$dir1 = 0;
			}
			$dir2 = substr($pic->id, -2, 1);
			if (!$dir2) {
				$dir2 = 0;
			}

			$path = $dir1 . '/' . $dir2;

			rmkdir('/home/www/img.exs.lv/junk/large/' . $path . '/');
			rmkdir('/home/www/img.exs.lv/junk/thb/' . $path . '/');
			$image = '/junk/large/' . $path . '/' . $pic->id . $ext;
			$newfile = '/home/www/img.exs.lv/junk/large/' . $path . '/' . $pic->id . $ext;
			file_put_contents($newfile, $data);
			$thumbnail = make_thumb($newfile, $path, $pic->id);
			$thb = '/junk/thb/' . $path . '/' . $thumbnail;
			if (!empty($thumbnail)) {
				$db->query("INSERT INTO `junk`
					(`author`, `approved_by`, `image`, `thb`, `title`, `date`, `bump`, `ip`, `source`, `link`) VALUES
					('$pic->user_id', '$auth->id', '" . sanitize($image) . "', '" . sanitize($thb) . "', '" . sanitize($pic->title) . "', NOW(), '" . time() . "', '" . $auth->ip . "', '" . sanitize($pic->source) . "', '" . sanitize($pic->link) . "')");

				$db->query("UPDATE `junk_queue` SET `approved` = 1 WHERE `id` = '$id'");
				die('<p class="g">Apstiprināts</p>');
			}
		}

		$db->query("UPDATE `junk_queue` SET `approved` = 2 WHERE `id` = '$id'");
		die('<p class="r">Kļūda, noraidīts</p>');
	} else {
		die('<p class="r">Tāda pikča neatradās ;(</p>');
	}
}

if (isset($_GET['var1']) && isset($_GET['var2']) && $_GET['var2'] == 'wtf') {
	$id = (int) $_GET['var1'];
	if ($db->get_var("SELECT count(*) FROM `junk_queue` WHERE `approved` = 0 AND `id` = '$id'")) {
		$db->query("UPDATE `junk_queue` SET `approved` = 2 WHERE `id` = '$id'");
		die('<p class="r">Noraidīts</p>');
	} else {
		die('<p class="r">Tāda pikča neatradās ;(</p>');
	}
}

$junks = $db->get_results("SELECT * FROM `junk_queue` WHERE `approved` = 0 ORDER BY `id` DESC LIMIT 100");

if (!empty($junks)) {
	$tpl->newBlock('junk-queue');
	foreach ($junks as $junk) {
		$tpl->newBlock('junk-queue-item');
		$tpl->assignAll($junk);
	}
}

