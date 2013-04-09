<?php

if (!$auth->ok) {
	set_flash('Jāielogojas, lai piekļūtu šai sadaļai!', 'error');
	redirect();
}

if ($handle = opendir('bildes/personas/')) {
	while (false !== ($file = readdir($handle))) {
		if ($file != "." && $file != "..") {
			$tpl->newBlock('persona');
			$tpl->assign('persona', $file);
			if ($auth->ok && isset($_GET['var1']) && $_GET['var1'] == $file) {
				$db->query("UPDATE users SET persona = '" . sanitize($file) . "' WHERE id = '$auth->id'");
				get_user($auth->id, true);
				set_flash('Tēma nomainīta!', 'success');
				redirect('/augsa');
			}
		}
	}
	closedir($handle);
}
