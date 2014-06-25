<?php
/**
 *  Ar [mini-]kvestiem saistītu ierakstu pievienošana un rediģēšana
 *
 *  Tiek iekļauts lists.php failā
 */

!isset($sub_include) and die('No hacking, pls.');

$arr_levels = array(
    1 => 'Novice', 
    2 => 'Intermediate', 
    3 => 'Experienced', 
    4 => 'Master', 
    5 => 'Grandmaster', 
    6 => 'Special'
);
$arr_length = array(
    1 => 'Īss', 
    2 => 'Vidējs', 
    3 => 'Ilgs', 
    4 => 'Ļoti ilgs', 
    5 => 'Ļoti, ļoti ilgs'
);
// ar to domāti kvestu storylines
$arr_series = $db->get_results("
    SELECT `id`, `title` FROM `rs_series` WHERE `category` = 'series' 
    ORDER BY `title` ASC
");
$series_ids = array();
if ($arr_series) {
    foreach ($arr_series as $series) {
        $series_ids[] = $series->id;
    }
}

/**
 *  Jauna ieraksta pievienošana
 */
if (isset($_GET['var1']) && $_GET['var1'] === 'new') {

    if (isset($_POST['submit'])) {

        // pēc (iespējams) norādītā `strid` nolasa raksta id
        $strid = '';
        $page_id = 0;
        if (isset($_POST['strid'])) {
            $strid = substr(htmlspecialchars(trim(strip_tags(
                            $_POST['strid']))), 0, 255);
        }
        // lauks drīkst būt tukšs, kas nozīmē, ka ieraksts būs placeholderis
        if ($strid !== '') {
            $if_exists = $db->get_row("
                SELECT `id` FROM `pages` 
                WHERE 
                    `strid` = '" . sanitize($strid) . "' AND 
                    `category` IN(" . implode(',', $cat_quests) . ")
            ");
            if ($if_exists) {
                $page_id = (int)$if_exists->id;
            }
        }

        $title = (isset($_POST['title']) && !empty($_POST['title'])) ?
            input2db($_POST['title'], 255) : '--';

        // citi parametri, kas nav obligāti norādāmi
        $starting_point = (isset($_POST['starting_point'])) ? 
            input2db($_POST['starting_point'], 100) : '';
        $skills = (isset($_POST['skills'])) ? 
            input2db($_POST['skills'], 512) : '';
        $quests = (isset($_POST['quests'])) ? 
            input2db($_POST['quests'], 1024) : '';
        $extra = (isset($_POST['extra'])) ? 
            input2db($_POST['extra'], 1024) : '';
        $description = (isset($_POST['description'])) ? 
            input2db($_POST['description'], 1024) : '';
        $date = '01/01/2001';
        if (isset($_POST['date']) && !empty($_POST['date'])) {
            $date = str_replace('/', '-', trim($_POST['date']));
            $date = date('d/m/Y', strtotime($date));
        }
        
        $members_only = (isset($_POST['members_only'])) ? 
            (int)((bool)$_POST['members_only']) : 0;
        $cat = 0;
        if (isset($_GET['viewcat']) && $_GET['viewcat'] === 'all-miniquests') {
            $cat = $cat_miniquests;
        } else {
            $cat = ($members_only) ? $cat_p2p_quests : $cat_f2p_quests;
        }
            
        $difficulty = 0;
        if (isset($_POST['difficulty']) && 
            array_key_exists((int)$_POST['difficulty'], $arr_levels)) {            
            $difficulty = (int)$_POST['difficulty'];
        }
        
        $length = 0;
        if (isset($_POST['length']) && 
            array_key_exists((int)$_POST['length'], $arr_length)) {            
            $length = (int)$_POST['length'];
        }

        $age = 0;
        if (isset($_POST['age']) && (int)$_POST['age'] === 1) {
            $age = 1;
        }
        
        $voice_acted = 0;
        if (isset($_POST['voice_acted']) && (int)$_POST['voice_acted'] === 1) {
            $voice_acted = 1;
        }
        
        $insert = $db->query("
            INSERT INTO `rs_pages`
                (page_id, cat_id, title, members_only, difficulty, 
                 length, age, voice_acted, starting_point, skills, quests, 
                 extra, description, date, created_by, created_at)
            VALUES(
                $page_id,
                ".(int)$cat.",
                '$title',
                $members_only,
                $difficulty,
                $length,
                $age,
                $voice_acted,
                '$starting_point',
                '$skills',
                '$quests',
                '$extra',
                '$description',
                '".sanitize($date)."',
                '".(int)$auth->id."',
                '".time()."'
            )
        ");

        set_flash('Ieraksts pievienots');
        redirect('/'.$_GET['viewcat']);
    
    // būs redzama jauna ieraksta pievienošanas forma
    } else {
    
        $tpl->newBlock('quest-form');
        
        // kvesta sarežģītības izvēlne
        foreach ($arr_levels as $level => $value) {
            $tpl->newBlock('add-difficulty');
            $tpl->assign(array(
                'level-id' => $level,
                'level-title' => $value
            ));
        }
        
        // kvesta garuma izvēlne
        foreach ($arr_length as $length => $value) {
            $tpl->newBlock('add-length');
            $tpl->assign(array(
                'length-id' => $length,
                'length-title' => $value
            ));
        }
    }
}


/**
 *  Esoša ieraksta rediģēšana
 *
 *  $_GET['var2'] === id no `rs_pages` tabulas
 */
else if (isset($_GET['var1']) && $_GET['var1'] === 'edit' && 
         isset($_GET['var2'])) {

    // nolasa datus par norādīto ierakstu
    $entry = $db->get_row("
        SELECT           
            `rs_pages`.*,
            `pages`.`strid` AS `strid`
        FROM `rs_pages`
            LEFT JOIN `pages` ON (
                `rs_pages`.`page_id` = `pages`.`id` AND
                `pages`.`lang` = 9 AND
                `pages`.`category` IN(" . implode(',', $cat_quests) . ")
            )
        WHERE 
            `rs_pages`.`id` = ".(int)$_GET['var2']." AND
            `rs_pages`.`deleted_by` = 0
    ");
    
    if (!$entry) {
        set_flash('Kļūdaini norādīta adrese');
        redirect('/'.$_GET['viewcat']);
    }    
    
    // atjauno raksta informāciju pēc $_POST datiem
    if (isset($_POST['submit'])) {
        
        // pārbauda, vai raksts ar norādīto strid eksistē       
        if (isset($_POST['strid'])) {
            $entry->strid = substr(htmlspecialchars(trim(strip_tags(
                                   $_POST['strid']))), 0, 255);
            if ($entry->strid !== '') {
                $if_exists = $db->get_row("
                    SELECT `id` FROM `pages` 
                    WHERE 
                        `strid` = '" . sanitize($entry->strid) . "' AND 
                        `category` IN(" . implode(',', $cat_quests) . ")
                ");
                if ($if_exists) {
                    $entry->page_id = (int)$if_exists->id;
                } else {
                    $entry->page_id = 0;
                }
            }
        } else {
            $entry->page_id = 0;
        }
        
        $entry->title = (isset($_POST['title']) && !empty($_POST['title'])) ?
            input2db($_POST['title'], 255) : '--';

        // citi parametri, kas nav obligāti norādāmi
        $entry->starting_point = (isset($_POST['starting_point'])) ? 
            input2db($_POST['starting_point'], 256) : '';
        $entry->skills = (isset($_POST['skills'])) ? 
            input2db($_POST['skills'], 512) : '';
        $entry->quests = (isset($_POST['quests'])) ? 
            input2db($_POST['quests'], 1024) : '';
        $entry->extra = (isset($_POST['extra'])) ? 
            input2db($_POST['extra'], 1024) : '';
        $entry->description = (isset($_POST['description'])) ? 
            input2db($_POST['description'], 1024) : '';
        $entry->date = '01/01/2001';
        if (isset($_POST['date']) && !empty($_POST['date'])) {
            $entry->date = str_replace('/', '-', trim($_POST['date']));
            $entry->date = date('d/m/Y', strtotime($entry->date));
        }

        $entry->members_only = (isset($_POST['members_only'])) ? 
            (bool)$_POST['members_only'] : false;

        $entry->difficulty = 0;
        if (isset($_POST['difficulty']) && 
            array_key_exists((int)$_POST['difficulty'], $arr_levels)) {            
            $entry->difficulty = (int)$_POST['difficulty'];
        }
        
        $entry->length = 0;
        if (isset($_POST['length']) && 
            array_key_exists((int)$_POST['length'], $arr_length)) {            
            $entry->length = (int)$_POST['length'];
        }
        
        $entry->age = 0;
        if (isset($_POST['age']) && (int)$_POST['age'] === 1) {
            $entry->age = 1;
        }
        
        $entry->voice_acted = 0;
        if (isset($_POST['voice_acted']) && (int)$_POST['voice_acted'] === 1) {
            $entry->voice_acted = 1;
        }

        $db->query("
            UPDATE `rs_pages` SET
                `page_id`       = ".$entry->page_id.",
                `title`         = '".$entry->title."',
                `members_only`  = ".(int)$entry->members_only.",
                `difficulty`    = ".$entry->difficulty.",
                `length`        = ".$entry->length.",
                `age`           = ".$entry->age.",
                `voice_acted`   = ".$entry->voice_acted.",
                `starting_point` = '".$entry->starting_point."',
                `skills`        = '".$entry->skills."',
                `quests`        = '".$entry->quests."',
                `extra`         = '".$entry->extra."',
                `description`   = '".$entry->description."',
                `date`          = '".sanitize($entry->date)."',
                `updated_by`    = ".(int)$auth->id.",
                `updated_at`    = '".time()."'
            WHERE 
                `id`          = ".(int)$entry->id." AND
                `deleted_by`  = 0               
        ");
    
        set_flash('Ieraksts atjaunots');
        redirect('/'.$_GET['viewcat'].'/edit/'.(int)$entry->id);

    // parāda lapā ieraksta rediģēšanas formu
    } else {

        $tpl->newBlock('quest-form');
        $tpl->assignAll($entry);
        $tpl->assign('strid', $entry->strid);
        
        // kvesta sarežģītības izvēlne
        foreach ($arr_levels as $level => $value) {
            $tpl->newBlock('add-difficulty');
            $tpl->assign(array(
                'level-id' => $level,
                'level-title' => $value
            ));
            if ((int)$entry->difficulty === $level) {
                $tpl->assign('selected', ' selected="selected"');
            }
        }
        
        // kvesta garuma izvēlne
        foreach ($arr_length as $length => $value) {
            $tpl->newBlock('add-length');
            $tpl->assign(array(
                'length-id' => $length,
                'length-title' => $value
            ));
            if ((int)$entry->length === $length) {
                $tpl->assign('selected', ' selected="selected"');
            }
        }

        if ((bool)$entry->members_only) {
            $tpl->assign('sel-members', ' selected="selected"');
        }

        if ((bool)$entry->age) {
            $tpl->assign('sel-sixth', ' selected="selected"');
        }

        if ((bool)$entry->voice_acted) {
            $tpl->assign('sel-voiced', ' selected="selected"');
        }
    }
}


/**
 *  Kļūda... kā šeit nonāca? Laikam norādīja nepareizu $_GET['var1'].
 */
else {
    set_flash('Kļūdaini norādīta adrese');
    redirect('/' . $_GET['viewcat']);
}
