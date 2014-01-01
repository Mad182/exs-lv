<?php
/**
 *	RuneScape tasks/achievement diaries
 */
!isset($sub_include) and die('No hacking, pls.');

$tpl->newBlock('tasks');

// no datubāzes atlasa Tasku klases;
// 112 - nekategorizēto rakstu klase, kas tiek apstrādāta zemāk
$tasks = $db->get_results("
    SELECT 
        `rs_classes`.`id`       AS `class_id`, 
        `rs_classes`.`title`    AS `class_title`, 
        `rs_classes`.`img`      AS `class_img`,
        IFNULL(`pages`.`id`, 0) AS `page_id`,
        `pages`.`strid`         AS `page_strid`,
        `pages`.`title`         AS `page_title`
    FROM `rs_classes`
        LEFT JOIN `pages` ON (
            `rs_classes`.`id` = `pages`.`rsclass` AND
            `pages`.`category` = 194
        )
    WHERE 
        `rs_classes`.`category` = 'tasks' AND
        `rs_classes`.`id` != 112
    ORDER BY 
        `rs_classes`.`ordered` ASC
");

if ($tasks) {
    $counter = 0;
    $page_id = 0;
    
    foreach ($tasks as $page) {
    
        if ($page->class_id != $page_id) {
        
            $page_id = $page->class_id;
        
            // teritorijai ir vismaz viens raksts
            if ($page->page_id != '0') {
                $tpl->newBlock('tasks-block');
                $tpl->assignAll($page);
            }
            // teritorijām, kurām nav rakstu, izveido jaunu bloku
            // zem pārējām teritorijām
            else {
                $tpl->newBlock('tasks-not');
                $tpl->assignAll($page);
            }
            
            if ($counter % 3 == 0) {
                $tpl->assign('newline', ' newline');
            }  // pārmet blokus jaunā rindā
            $counter++;
        }
        
        // pārbaude novērš situācijas, kad tiek pievienotas teritorijas,
        // kurām nav rakstu
        if ($page->page_id != '0') {
            $tpl->newBlock('task');
            $tpl->assignAll($page);
        }
    }
}

// pa teritorijām nekategorizētie raksti
$others = $db->get_results("
    SELECT 
        `strid` AS `page_strid`,
        `title` AS `page_title`
    FROM `pages` 
    WHERE 
        `category` = 194 AND 
        `rsclass` = 0
    ORDER BY 
        `title` ASC
");
    
if ($others) {

    $tpl->newBlock('tasks-block');
    $tpl->assign(array(
        'class_img'   => 'uncategorised.png',
        'class_title' => 'Nekategorizēti raksti'
    ));
    
    foreach ($others as $other) {
        $tpl->newBlock('task');
        $tpl->assignAll($other);
    }
    
}