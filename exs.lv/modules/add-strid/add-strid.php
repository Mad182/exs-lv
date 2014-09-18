<?php

/**
 * Pievieno grupai strid
 */
if ($auth->id != 1) {
	redirect();
}

if (isset($_POST['id']) && isset($_POST['strid'])) {

	$id = (int) $_POST['id'];
	$title = sanitize($_POST['strid']);
	$strid = mkslug($_POST['strid']);

	if ($id) {
		$db->query("INSERT INTO `cat` (`textid`,`title`,`module`,`sitemap`,`content`,`lang`,`options`) 
				VALUES ('$strid','$title','group',0,'$id','$lang','no-left') ");

		$db->query("UPDATE `cat` SET `ordered` = '$db->insert_id' WHERE id = '$db->insert_id'");

		$db->query("UPDATE `clans` SET `strid` = '$strid' WHERE `id` = '$id' LIMIT 1");

		//nodzēš grupu sarakstu no memcache
		$m->delete('latest_groups_' . $lang);

		redirect('/' . $strid);
	}
}

