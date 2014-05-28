<?php

/**
 *  Ar runescape.exs.lv saistītas pārbaudes, 
 *  kuras vieglākai rediģēšanai iznestas ārpus index.php faila.
 */
 

// ja datubāzē pie kategorijas kā projekts/valoda norādīta 0, tad rs apakšprojektā
// kolonnas jārāda otrādi, nekā norādīts "options" laukā;
// tieši rs projekta sadaļām jau būs norādīts pareizais izvietojums
if ($category->lang != $lang) {
    if ($category->options == 'no-left') {
        $category->options = 'no-right';
    }
    elseif ($category->options == 'no-right') {
        $category->options = 'no-left';
    }
}
if ($category->module == 'group') {
    $category->options = 'no-right';
}



// "Lobby" cilne iekrāsosies tikai tad, ja tieši index sadaļa būs atvērta;
// ignorēs tās reizes, kad atvērta apakškategorija
if ($category->id == 1863) {
    $tpl->assignGlobal('cat_sel_1863', ' class="selected"');
}




// index.php failā jau pēc noklusējuma neautorizēta statusa
// gadījumā lapā tiek iekļauts bloks ar login formu,
// tāpēc šajā navigācijā tas netiek pārbaudīts, jo strādā tāpat

if ($auth->ok) {
	$tpl->newBlock('auth-nav');
    
    // RS Mod izvēlne
    if (im_rs_mod()) {
        $tpl->newBlock('rsmod-nav');
        if ($auth->id == 115) {
            $tpl->newBlock('quest-management-link');
        }
    }

	if (im_mod()) {

		// Mod izvēlne
		$tpl->newBlock('mod-nav');
		if (in_array($category->textid, array('banned', 'crows', 'reports', 'checkform', 'log'))) {
			$tpl->assign('active-mod', ' class="selected"');
		}
	}
}
