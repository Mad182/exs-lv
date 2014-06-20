<?php
/**
 *  Kvestiem nepieciešamo prasmju līmeņu pārvaldība
 *
 *  Saraksts, kurā katrai prasmei var norādīt augstāko nepieciešamo līmeni un
 *  kvestu, kuram tāds nepieciešams.
 */

$tpl->newBlock('skill-requirements');

$skills = $db->get_results("SELECT * FROM `rs_skills` ORDER BY `title` ASC");

if (!$skills) {
    $tpl->newBlock('no-skills-added');
} else {
    

    // prasību atjaunošana pēc iesniegtas formas
    if (isset($_POST['submit'])) {
        
        foreach ($skills as $skill) {
        
            if (isset($_POST['level-'.$skill->id]) && 
                isset($_POST['quest-'.$skill->id])) {
                
                $page_value = 0;
                $title_value = '';
                
                // pārbauda, vai eksistē raksts ar tādu strid
                if (trim($_POST['quest-'.$skill->id] !== '')) {
                
                    $title_value = input2db($_POST['quest-'.$skill->id], 256);
                    $check = $db->get_row("
                        SELECT `id`, `strid` FROM `pages` 
                        WHERE
                            `strid` = '".$title_value."' AND
                            `lang` = 9 AND
                            `category` IN(".implode(',', $cat_quests).")
                    ");
                    if ($check) {
                        $page_value = $check->id;
                        $title_value = $check->strid;
                    }              
                }

                $level_value = (int)$_POST['level-'.$skill->id];
                if ($level_value < 1) {
                    $level_value = 1;
                } else if ($level_value > 120) {
                    $level_value = 99;
                }

                $db->query("
                    UPDATE `rs_skills` 
                    SET 
                        `level`      = ".$level_value.",
                        `page_id`    = ".$page_value.",
                        `page_title` = '".$title_value."'
                    WHERE `id` = ".(int)$skill->id."
                ");
            }
        }

        set_flash('Informācija atjaunināta');
        redirect('/'.$_GET['viewcat']);
    }


    // saraksts ar prasībām tabulas formā
    $tpl->newBlock('skills-notes');
    $tpl->newBlock('skills-form');
    $tpl->newBlock('skills-column');
    
    // tabulai būs divas kolonnas
    $counter = 0;
    $split_by = floor(count($skills) / 2);
    
    foreach ($skills as $data) {
    
        $tpl->newBlock('single-skill');
        $tpl->assignAll($data);
        
        // prasībām, kas nav prasmes, būs cits fons
        // (piemēram, combat, tasks, total u.c.)
        if ($data->is_special) {
            $tpl->assign('special', ' class="is-special-input"');
        } else if ($data->page_id == 0) {
            $tpl->assign('special', ' class="is-not-set"');
        }
        
        if ($counter++ == $split_by) {
            $tpl->newBlock('skills-column');
        }
    }
}
