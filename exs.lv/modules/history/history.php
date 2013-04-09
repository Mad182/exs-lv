<?php

if ($auth->ok) {
	if (isset($_GET['page'])) {
		$hpid = (int) $_GET['page'];
		$record = $db->get_row("SELECT * FROM pages_ver WHERE id = '$hpid' LIMIT 1");
		if ($record) {
			$tpl->assignGlobal('htext', add_smile($record->text, 1));
			$page_title = $record->title . ' - Arhīvs';
		} else {
			die('Kļūdains pieprasījums!');
		}
	} else {
		die('Nav norādīta lapa!');
	}
}
