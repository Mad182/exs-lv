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



// index.php failā jau pēc noklusējuma neautorizēta statusa
// gadījumā lapā tiek iekļauts bloks ar login formu,
// tāpēc šajā navigācijā tas netiek pārbaudīts, jo strādā tāpat

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
		}
	}
}



// iekrāso atvērto navigācijas cilni ("Cits"), ja atvērta kāda no tās apakšsadaļām
$other_cats = array(
	793,    // pamatinformācija
	788,    // trenēšanās
	787,    // briesmoņu medīšana
	790,    // nauda pelnīšana
	5,      // citi padomi
	346,    // RS rakstu arhīvs
	1087    // Oldschool RuneScape pamācības
    //789   // RS stāsti & vēsture 
	//536   // Priekšmetu datubāze
);

if (in_array($category->id, $other_cats)) {
	$tpl->assignGlobal(array(
		'cat-sel-other' => ' class="selected"'
	));
}



// ja sadaļā redzamas abas šaurās kolonnas,
// tad virs tām izvada vēl papildblokus
/*if ($category->options == '' && $category->module != 'rshelp' && $category->module != 'rsmod') {

    $tpl->newBlock('main-layout-column-top');
    
    if (isset($_GET['pg'])) {
		$skip = 6 * intval($_GET['pg']);
	} else {
		$skip = 0;
	}
    
	$latest = $db->get_results("
        SELECT
            `images`.`uid`      AS `uid`,
            `images`.`id`       AS `id`,
            `images`.`posts`    AS `posts`,
            `images`.`thb`      AS `thb`,
            `images`.`url`      AS `url`,
            `images`.`readby`   AS `readby`,
            `users`.`nick`      AS `nick`
        FROM
            `images`
        LEFT JOIN
            `users` ON  `images`.`uid` =  `users`.`id`
        WHERE 
            `images`.`lang` = '$lang'
        ORDER BY
            `images`.`bump`
        DESC LIMIT $skip, 12
    ");

	if ($latest) {
    
        $counter = 0;
        
		foreach ($latest as $late) {

            $tpl->newBlock('single-gallery-image');
            $tpl->assign(array(
                'nick'  => htmlspecialchars($late->nick),
                'uid'   => $late->uid,
                'id'    => $late->id,
                'thb'   => $late->thb,
                'posts' => $late->posts
            ));

			if (empty($late->readby) || !in_array($auth->id, unserialize($late->readby))) {
                $tpl->assign('r', ' class="r"');
			}
            if ($counter == 6) {
                $tpl->assign('break-row', ' style="clear:left"');
            }
            $counter++;
		}
	}
}*/