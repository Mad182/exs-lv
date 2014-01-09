<?php

/**
 * 	RuneScape ģildes
 */
!isset($sub_include) and die('No hacking, pls.');


// atlasa runescape ģilžu rakstus un tiem piesaista
// papildinformāciju, ja tāda atrodama `rs_pages` tabulā
$guilds = $db->get_results("
    SELECT 
        `pages`.`id`                AS `page_id`,
        `pages`.`strid`             AS `page_strid`,
        `pages`.`title`             AS `page_title`,
        `pages`.`author`            AS `page_author`,
        
        IFNULL(`rs_pages`.`id`,0)   AS `rspage_id`,
        `rs_pages`.`img`            AS `rspage_img`,
        `rs_pages`.`members_only`   AS `rspage_members_only`,
        `rs_pages`.`location`       AS `rspage_location`,
        `rs_pages`.`extra`          AS `rspage_extra`,
        `rs_pages`.`is_old`         AS `rspage_is_old`
    FROM `pages` 
        LEFT JOIN `rs_pages` ON (
            `pages`.`id`                = `rs_pages`.`page_id` AND
            `rs_pages`.`deleted_by`     = 0 AND
            `rs_pages`.`is_placeholder` = 0
        )
    WHERE 
        `pages`.`category` = 791 
    ORDER BY 
        `pages`.`title` ASC
");

if ($guilds) {

	$tpl->newBlock('guilds');
	$tpl->newBlock('guilds-not');

	foreach ($guilds as $page) {

		// pārbauda, vai pieprasījumā izdevās atlasīt papildinformāciju
		if ($page->rspage_id != '0') {
			$page->rspage_is_old = ($page->rspage_is_old == 1) ?
					'<img class="guild-old" src="/bildes/runescape/info_yellow_sm.png" title="Pamācībai nepieciešamas jaunākas, labākas kvalitātes bildes!" alt="">' :
					'<img class="guild-old" src="/bildes/runescape/info_red_sm.png" title="Pamācību nepieciešams atjaunināt!" alt="">';
			$page->rspage_members_only = ($page->rspage_members_only == 1) ?
					'<img class="guild-icon" src="/bildes/runescape/p2p_small.png" title="Maksājošo spēlētāju ģilde" alt="">' : '';
		} else {
			$page->rspage_is_old = '';
			$page->rspage_members_only = '';
		}

		// ja rakstam ir pievienots attēls, to uzskata par ģildes rakstu
		if ($page->rspage_img != '') {
			$tpl->newBlock('guild');
			$tpl->assignAll($page);
		}
		// pretējā gadījumā rakstu pievieno nekategorizētajiem rakstiem
		else {
			$tpl->newBlock('guild-page');
			$tpl->assignAll($page);
		}
	}
}