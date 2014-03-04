<?php

/**
 * 	Pamācībām pievienojamā papildinformācija.
 */
// ne-moderatorus sūtām prom
if (!im_mod()) {
	set_flash('Error 403: Permission denied!');
	redirect();
}
$sub_include = true;


// array_keys ir lapas textid
$submodules = array(
	'info-quests'    => 'quests.php',
	'info-distractions'    => 'distractions.php'
);


// iekļauj lapā pareizo apakšmoduli
if (isset($submodules[$category->textid])) {

	if (file_exists(CORE_PATH . '/modules/rs-info/submodules/' . $submodules[$category->textid])) {
    
        $tpl->assignInclude('sub-template', CORE_PATH . '/modules/rs-info/submodules/' . str_replace('php', 'tpl', $submodules[$category->textid]));
		$tpl->prepare();
        
		include(CORE_PATH . '/modules/rs-info/submodules/' . $submodules[$category->textid]);
        
	} else {
		set_flash('Kļūdaini norādīta adrese!');
		redirect();
	}
} else {
	set_flash('Kļūdaini norādīta adrese!');
	redirect();
}