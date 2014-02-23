<?php

/**
 * 	RuneScape rakstu sadaļu administrācijas panelis.
 *
 * 	Moduļa adrese: runescape.exs.lv/[..]
 */
// ne-moderatorus sūtām prom
if (!im_mod()) {
	set_flash('Error 403: Permission denied!');
	redirect();
}


$tpl_options = 'no-left';
$sub_include = true;  // submoduļos ir pārbaude, vai šāds mainīgais definēts



// array_keys ir lapas textid
$submodules = array(
	'series'        => 'quests-series.php', // kvestu info pārvaldība
	'rsph'          => 'placeholders.php',  // pamācību placeholderi
	'areas'         => 'areas.php',         // ceļveži
	'rsactivities'  => 'activities.php'     // aktivitātes
);


// iekļauj lapā pareizo apakšmoduli
if (isset($submodules[$category->textid])) {

	if (file_exists(CORE_PATH . '/modules/rsmod/submodules/' . $submodules[$category->textid])) {
		include(CORE_PATH . '/modules/rsmod/submodules/' . $submodules[$category->textid]);
	} else {
		set_flash('Kļūdaini norādīta adrese!');
		redirect();
	}
} else {
	set_flash('Kļūdaini norādīta adrese!');
	redirect();
}
?>