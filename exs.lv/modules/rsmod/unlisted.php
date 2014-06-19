<?php
/**
 *  Nekategorizēto pamācību saraksts
 *
 *  Tabulas formā aplūkojams saraksts ar tām pamācībām kādā no
 *  galvenajām rakstu sadaļām, kuras nav piesaistītas nevienam
 *  ierakstam `rs_pages` tabulā. 
 *
 *  Noder, kad kāds lietotājs uzraksta jaunu rakstu.
 *
 *  Starp pārbaudāmajām sadaļām ietilpst:
 *
 *      - kvesti (f2p, p2p, minikvesti)
 *      - minispēles
 *      - distractions & diversions
 */

!isset($sub_include) and die('No hacking, pls.');

$tpl->newBlock('list-tabs');
$tpl->assign('tab-unlisted', 'active');
$tpl->newBlock('list-intro-unlisted');

$cats = array($cat_f2p_quests, $cat_p2p_quests, $cat_miniquests, 
              $cat_minigames, $cat_distractions);


// atlasa cilnes ierakstus no `rs_pages` tabulas
$found_pages = $db->get_results("
    SELECT
        `pages`.`id`     AS `id`,
        `pages`.`strid`  AS `strid`,
        `pages`.`title`  AS `title`
    FROM `pages`
        LEFT JOIN `rs_pages` ON ( 
            `pages`.`id` = `rs_pages`.`page_id` AND
            `rs_pages`.`deleted_by` = 0
        )
    WHERE
        `pages`.`lang` = 9 AND
        `pages`.`category` IN(".implode(',', $cats).") AND
        `rs_pages`.`id` IS NULL
    ORDER BY `pages`.`title` ASC
");

if (!$found_pages) {
    $tpl->newBlock('list-no-pages');
} else {

    $tpl->newBlock('list-all-unlisted');
    
    $saved_letter = '';

    foreach ($found_pages as $guide) {

        $tpl->newBlock('unlisted-page');
        
        $tpl->assign(array(
            'page_id'   => $guide->id,
            'strid'     => $guide->strid,
            'title'     => $guide->title
        ));
    }
}
