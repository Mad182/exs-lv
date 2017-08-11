<?php

/**
 * Lietotāja personalizētais lapas headeris
 */
if (!$auth->ok) {
	set_flash('Jāielogojas, lai piekļūtu šai sadaļai!', 'error');
	redirect();
}

foreach (glob(CORE_PATH . "/bildes/personas/*.jpg") as $file) {

	$file = basename($file);

	$tpl->newBlock('persona');
	$tpl->assign('file', $file);
	$tpl->assign('link', str_replace('.','-type-',$file));

	if ($auth->ok && isset($_GET['var1']) && str_replace('.','-type-',$_GET['var1']) == str_replace('.','-type-',$file)) {
		$db->query("UPDATE users SET persona = '" . sanitize($file) . "' WHERE id = '$auth->id'");
		get_user($auth->id, true);
		set_flash('Tēma nomainīta!', 'success');
		redirect('/augsa');
	}
}

