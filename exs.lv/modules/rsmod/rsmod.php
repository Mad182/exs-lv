<?php
/**
 * 	RuneScape pamācību sadaļu (kvesti/prasmes u.tml.) administrācijas panelis
 *
 *  Šis modulis apkopo tās sadaļas, kurās iespējams veikt izmaiņas
 *  RuneScape pamācību sadaļām, piemēram, pievienojot jaunas rakstu sērijas,
 *  mainot rakstu secību sērijā, izveidojot rakstu "placeholders" u.c.
 */

if (!im_mod()) {
	set_flash('Error 403: Permission denied!');
	redirect();
}

$tpl_options = 'no-left';

// submoduļos jāveic pārbaude, vai šāds mainīgais definēts
// (lai nevarētu skatīt failu pa tiešo)
$sub_include = true;

// array_key ir lapas "textid"
$submodules = array(
	'all-quests'        => array('lists.php','lists.tpl'),
	'all-miniquests'    => array('lists.php','lists.tpl'),
	'all-minigames'     => array('lists.php','lists.tpl'),
	'all-distractions'  => array('lists.php','lists.tpl'),
    'all-unlisted'      => array('unlisted.php', 'lists.tpl')
);

// iekļauj lapā pareizos failus
if (isset($submodules[$category->textid])) {

    $php_filename = CORE_PATH.'/modules/'.$category->module.'/'
                             .$submodules[$category->textid][0];

    $tpl_filename = '';
    if ($submodules[$category->textid][1] !== '') {
        $tpl_filename = CORE_PATH.'/modules/'.$category->module.'/'
                                 .$submodules[$category->textid][1];
    }

	if (file_exists($php_filename)) {        
        
        if ($tpl_filename !== '' && file_exists($tpl_filename)) {
            $tpl->assignInclude('sub-template', $tpl_filename);
            $tpl->prepare();
        }
		include($php_filename);

	} else {
		set_flash('Kļūdaini norādīta adrese');
		redirect();
	}
} else {
	set_flash('Kļūdaini norādīta adrese');
	redirect();
}
