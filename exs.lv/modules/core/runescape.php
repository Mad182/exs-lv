<?php

/**
 *  Ar runescape.exs.lv saistītas pārbaudes, 
 *  kuras vieglākai rediģēšanai iznestas ārpus index.php faila.
 */
/**
 *  index.php failā jau pēc noklusējuma neautorizēta statusa
 *  gadījumā lapā tiek iekļauts bloks ar login formu,
 *  tāpēc šajā navigācijā tas netiek pārbaudīts, jo strādā tāpat
 */
if ($auth->ok) {
	$tpl->newBlock('auth-nav');

	// moderatoriem būs redzamas administrēšanas sadaļas (Mod, RS Mod)
	if (im_mod()) {

		// Mod izvēlnes iezīmēšana
		$tpl->newBlock('mod-nav');
		if (in_array($category->textid, array('banned', 'crows', 'reports', 'checkform', 'log'))) {
			$tpl->assign('active-mod', ' class="selected"');
		}

		// RS Mod izvēlnes iezīmēšana
		if ($auth->id == 115) {
			$tpl->newBlock('rsmod-nav');
			if (in_array($category->textid, array('gildes'))) {
				$tpl->assign('active-rsmod', ' class="selected"');
			}
		}
	}
}

// iekrāso atvērto navigācijas cilni ("Cits"), ja atvērta kāda no tās apakšsadaļām
$other_cats = array(
	793, // pamatinformācija
	788, // trenēšanās
	787, // briesmoņu medīšana
	790, // nauda pelnīšana
	5, // citi padomi
	346, // RS rakstu arhīvs
	1087  // Oldschool RuneScape pamācības
		//789   // RS stāsti & vēsture 
		//536  // Priekšmetu datubāze
);

if (in_array($category->id, $other_cats)) {
	$tpl->assignGlobal(array(
		'cat-sel-other' => ' class="selected"'
	));
}
