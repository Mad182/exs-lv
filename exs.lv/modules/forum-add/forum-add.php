<?php

/**
 * Foruma sadaļas pievienošana (adminiem)
 */
$robotstag[] = 'noindex';

if ($auth->level != 1) {
	redirect();
}

if (isset($_GET['var1'])) {

	$parent = get_cat($_GET['var1']);

	if (($parent->module == 'forums' || ($parent->module == 'list' && $parent->isforum)) && isset($_POST['title'])) {

		$title = title2db($_POST['title']);
		$content = '';
		if (!empty($_POST['content'])) {
			$content = sanitize(h(strip_tags($_POST['content'])));
		}
		$textid = mkslug($title);

		if ($db->get_var("SELECT count(*) FROM `cat` WHERE `textid` = '$textid'")) {
			$textid .= '-' . rand(111, 999);
		}

		$db->query("INSERT INTO `cat` (`textid`, `lang`, `module`, `title`, `content`, `parent`, `isforum`) VALUES ('$textid', '$parent->lang', 'list', '$title', '$content', '$parent->id', '1')");
		$db->query("UPDATE `cat` SET `ordered` = '$db->insert_id' WHERE id = '$db->insert_id'");
		redirect('/' . $parent->textid);
	}
}

