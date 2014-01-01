<?php
/**
 *	RuneScape prasmju sadaļa
 */
!isset($sub_include) and die('No hacking, pls.');

// izdrukā lapā ievadtekstu par prasmēm kā tādām
$tpl->newBlock('skills-intro');

// no datubāzes atlasa visas pievienotās prasmju sadaļas un
// katrai no tām atlasa arī piesaistītos rakstus un papildinfo
$pages = $db->get_results("
    SELECT 
        `cat`.`id`              AS `cat_id`,
        `cat`.`title`           AS `cat_title`,
        
        IFNULL(`pages`.`id`, 0) AS `page_id`,
        `pages`.`title`         AS `page_title`,
        `pages`.`strid`         AS `page_strid`,
        
        IFNULL(`rs_classes`.`id`, 0)    AS `class_id`,
        `rs_classes`.`img`              AS `class_img`,
        `rs_classes`.`info`             AS `class_info`,
        `rs_classes`.`members_only`     AS `members_only`
    FROM `cat` 
        LEFT JOIN `pages` ON `cat`.`id` = `pages`.`category`
        LEFT JOIN `rs_classes` ON (
            `cat`.`title`           = `rs_classes`.`title` AND
            `rs_classes`.`category` = 'skills'
        )
    WHERE 
        `cat`.`parent` = 4  
    ORDER BY 
        `cat`.`title` ASC
");

if ($pages) {

    $tpl->newBlock('skills');
    
    $skill_counter  = 0; // skaita izvadīto prasmju skaitu    
    $skill_id       = 0; // fiksē ciklā ejošo prasmi
    $page_counter   = 0; // skaita rakstus katras prasmes ietvarā
    
    foreach ($pages as $skill) {
        
        // constitution atsevišķi nebūs,
        // jo jau parādās pie Melee, kas atzīmēta kā prasme/kategorija
        if ($skill->cat_id == 191) {
            continue;
        }

        // mainoties prasmei, izveido jaunu prasmes bloku
        if ($skill_id != $skill->cat_id) {
        
            $skill_counter++;
            
            // ja vairāk par 5 linkiem, izvada pogu uz nākamo lapu;
            // pirms pirmās prasmes neizvadīs, jo skaitītājs ir 0,
            // turpretī pēdējo prasmi izlaidīs, jo izies ārpus cikla,
            // tāpēc tā jāpārbauda pēc cikla
            if ($page_counter > 5) {
                $tpl->gotoBlock('skill');
                $addr  = '<a class="skill-pager" href="/rs-skills/?skill='.$skill_id.'&amp;page=2">';
                $addr .= 'Tālāk &rsaquo;&rsaquo;</a>';
                $tpl->assign('next', $addr);
            }
            
            // pārbaude, vai izdevās pieprasījumā atlasīt
            // papildinformāciju no rs klašu tabulas
            if ($skill->class_id != '0') {
                $skill->members_only = ($skill->members_only == 1) ? 
                    ' <img src="/bildes/runescape/p2p_small.png" title="members only">' : '';
                $skill->class_img = '/bildes/runescape/skills/'.$skill->class_img;
            }
            else {
                $skill->members_only    = '';
                $skill->class_img       = '';
                $skill->class_info      = '';
            }
            
            $tpl->newBlock('skill');
            $tpl->assign(array(
                'title'     => $skill->cat_title,
                'img'       => $skill->class_img,
                'info'      => $skill->class_info,
                'members'   => $skill->members_only
            ));
        
            // pārmet jaunā rindā katru nepāra prasmi
            if ($skill_counter % 2 != 0) {
                $tpl->assign('linebreak', ' style="clear:left"');
            } else {
                $tpl->assign('linebreak', '');
            }
            
            // Linux fontu dēļ Linux lietotājiem uzliek citu klasi ar citiem bloku izmēriem
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'inux') !== false) {
                $tpl->assign('forlinux', '-2');
            }
            
            $skill_id = $skill->cat_id;
            $page_counter = 0; // pie katras prasmes jāizvada tikai pirmie pieci raksti
        }
        
        // jāzina pievienoto rakstu skaits, lai prasmes blokā
        // pēc vajadzības izvadītu pārvietošanos pa rakstu lappusēm
        $page_counter++;
        
        // pie katras prasmes nebūs vairāk par 5 rakstiem
        if ($page_counter > 5) {            
            continue;
        }
        
        // izdrukā prasmes blokā rakstu
        $skill->cat_title = textlimit($skill->cat_title, 30);
        $tpl->newBlock('skill-link');
        $tpl->assignAll($skill);       
    }
    
    // ciklā pārbaude pēdējai prasmei tika izlaista, tāpēc jāpārbauda šeit
    if ($page_counter > 5) {
        $addr  = '<a class="skill-pager" href="/rs-skills/?skill='.$skill_id.'&amp;page=2">';
        $addr .= 'Tālāk &rsaquo;&rsaquo;</a>';
        $tpl->assign('next', $addr);
    }
    
}

$tpl->newBlock('skills-facts');
$tpl->newBlock('skills-xp-table');