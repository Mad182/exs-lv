<?php

/**
 * 	RuneScape ceļveži
 */
!isset($sub_include) and die('No hacking, pls.');

$tpl->newBlock('areas');

$categories = $db->get_results("
    SELECT 
        `rs_classes`.`id`           AS `class_id`,
        `rs_classes`.`title`        AS `class_title`,
        
        IFNULL(`pages`.`id`, 0)     AS `page_id`,
        `pages`.`strid`             AS `page_strid`,
        `pages`.`title`             AS `page_title`,
        `pages`.`author`            AS `page_author`,
        
        IFNULL(`rs_pages`.`id`, 0)  AS `rspage_id`,
        `rs_pages`.`img`            AS `rspage_img`,
        `rs_pages`.`large_img`      AS `rspage_large_img`
        
    FROM `rs_classes` 
        LEFT JOIN `pages` ON (
            `rs_classes`.`id` = `pages`.`rsclass`            
        )
        LEFT JOIN `rs_pages` ON (
            `pages`.`id` = `rs_pages`.`page_id` AND
            `rs_pages`.`deleted_by` = 0
        )
    WHERE 
        `rs_classes`.`category`  = 'areas'
    ORDER BY 
        `rs_classes`.`ordered` ASC,
        `pages`.`title` ASC
");

if ($categories) {

	$cat_id = 0;

	foreach ($categories as $cat) {

		// mainās ceļvežu kategorija
		if ($cat->class_id != $cat_id) {

			$cat_id = $cat->class_id;

			$tpl->newBlock('areas-category');
			$tpl->assignAll($cat);
		}

		// kategorijai ir vismaz viens ceļvedis
		if ($cat->page_id != '0' && $cat->rspage_id != '0') {

			$tpl->newBlock('area-choice');
			$tpl->newBlock('area');

			$cat->rspage_img = ($cat->rspage_img != '') ?
					'<img class="area-ico" src="bildes/runescape/areas/' . $cat->rspage_img . '" alt="">' : '';

			if ($cat->rspage_large_img != '') {
				$cat->rspage_large_img = '<img class="large-img"';
				$cat->rspage_large_img .= ' src="bildes/runescape/areas/large/' . $cat->rspage_large_img . '"';
				$cat->rspage_large_img .= ' title="' . $cat->page_title . '" alt="">';
			}

			$tpl->assignAll($cat);
			/* else {
			  $insert = $db->query("INSERT INTO `rs_help` (page_id,cat,title,auth) VALUES ('" . (int) $page->id . "','195','" . title2db($page->title) . "','" . (int) $page->author . "') ");
			  $tpl->newBlock('area-choice');
			  $tpl->newBlock('area');
			  $tpl->assignAll($page);
			  } */
		}
		// placeholders
		/* $phs = $db->get_results("SELECT `title` FROM `rs_placeholders` WHERE `cat` = '$cat->id' ORDER BY `title` ASC");
		  if ($phs) {
		  $tpl->newBlock('area-choice');
		  $tpl->newBlock('area-more');
		  $sk = 0;
		  foreach ($phs as $ph) {
		  $sk++;
		  $tpl->newBlock('area-choice');
		  $tpl->newBlock('area-placeholder');
		  $tpl->assign('title', $ph->title);
		  if ($sk == 1) {
		  $tpl->assign(array(
		  'area-break' => 'clear:left;',
		  'placeholder-start' => '<div class="ph-hidden" style="display:none">'
		  ));
		  }
		  if ($sk >= count($phs)) {
		  $tpl->assign('placeholder-end', '</div>');
		  }
		  }
		  } */
	}
}

// nekategorizēti raksti
$pages = $db->get_results("SELECT `id` AS `page_id`,`strid` AS `page_strid`,`title` AS `page_title` FROM `pages` WHERE `rsclass` = 0 AND `category` = 195 ORDER BY `title` ASC ");
if ($pages) {
	$tpl->newBlock('areas-category');
	$tpl->assign('class_title', 'Nekategorizēti raksti');
	foreach ($pages as $page) {
		$tpl->newBlock('area-choice');
		$tpl->newBlock('area');
		$tpl->assignAll($page);
	}
}