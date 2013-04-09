<?php

if (!$auth->ok) {
	set_flash('Jāielogojas, lai piekļūtu šai sadaļai!');
	redirect();
}

if ($handle = opendir('bildes/fugue-icons/')) {
	while (false !== ($file = readdir($handle))) {
		if ($file != "." && $file != "..") {
			$tpl->newBlock('d-icon');
			$tpl->assign('icon', $file);
		}
	}
	closedir($handle);
}
