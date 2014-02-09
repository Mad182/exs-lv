<?php
exit;

$pages = $db->get_results("SELECT * FROM pages WHERE  `edit_user` =26153");

foreach($pages as $p) {
	echo $p->title . '<br />';
	$text = $db->get_row("SELECT * FROM pages_ver WHERE `pid` = $p->id ORDER BY `id` DESC LIMIT 1");

	if(!empty($text->text)) {
		$db->query("UPDATE `pages` SET `text` = '".sanitize($text->text)."' WHERE id = '$p->id'");
	}

}
