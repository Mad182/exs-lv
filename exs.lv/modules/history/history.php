<?php

/**
 * Apskatīt raksta iepriekšējās versijas
 */
$robotstag[] = 'noindex';

if ($auth->ok) {
	if (isset($_GET['page'])) {
		$hpid = (int) $_GET['page'];
		$record = $db->get_row("SELECT * FROM pages_ver WHERE id = '$hpid' LIMIT 1");
		if ($record) {
			$tpl->assignGlobal('htext', add_smile($record->text, 1));
			$page_title = $record->title . ' - Arhīvs';
            
            // runescape apakšprojektā eksistē raksti ar platām tabulām,
            // tāpēc tādiem vienu kolonnu aizvācam
            if ($record->is_wide && $lang == 9) {
                $tpl_options = 'no-left';
            }
    
		} else {
			die('Kļūdains pieprasījums!');
		}
	} else {
		die('Nav norādīta lapa!');
	}
}

