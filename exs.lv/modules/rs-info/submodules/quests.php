<?php
/**
 *  Quests
 */
!isset($sub_include) and die('No hacking, pls.');

// raksta informācijas rediģēšana
if (isset($_GET['var1']) && $_GET['var1'] == 'edit' && isset($_GET['var2']) ) {

    $levels = array(
        array(1, 'Viegls'), 
        array(2, 'Vidējs'), 
        array(3, 'Grūts'), 
        array(4, 'Master'), 
        array(5, 'Grandmaster'), 
        array(6, 'Special')
    );
    $length = array(
        array(1, 'Īss'), 
        array(2, 'Vidējs'), 
        array(3, 'Garš'), 
        array(4, 'Ļoti garš'), 
        array(5, 'Ļoti, ļoti garš')
    );

    // atlasa ar kvestiem saistītos rakstus no `pages` un,
    // ja iespējams, piesaista klāt `rs_pages`
    $guide = $db->get_row("
        SELECT 
            `pages`.`id`                AS `page_id`, 
            `pages`.`strid`             AS `page_strid`, 
            `pages`.`title`             AS `page_title`,
            `pages`.`category`          AS `page_category`,
            
            IFNULL(`rs_pages`.`id`, 0)  AS `rspage_id`,
            `rs_pages`.`class_id`       AS `rspage_class`,
            `rs_pages`.`difficulty`     AS `rspage_difficulty`,
            `rs_pages`.`length`         AS `rspage_length`,
            `rs_pages`.`description`    AS `rspage_description`,
            `rs_pages`.`extra`          AS `rspage_extra`,
            `rs_pages`.`quests`         AS `rspage_quests`,
            `rs_pages`.`skills`         AS `rspage_skills`,
            `rs_pages`.`location`       AS `rspage_location`,
            `rs_pages`.`members_only`   AS `rspage_members_only`
        FROM `pages`
            LEFT JOIN `rs_pages` ON (
                `pages`.`id`                = `rs_pages`.`page_id` AND
                `rs_pages`.`is_placeholder` = 0 AND
                `rs_pages`.`deleted_by`     = 0
            )
        WHERE 
            `pages`.`id` = ".(int)$_GET['var2']." AND
            `pages`.`lang` = 9
    ");
    
    if (!$guide) {
        set_flash('Kļūdaini norādīta adrese!');
        redirect('/'.$_GET['viewcat']);
    }
    
    // ieraksta rindu `rs_pages` tabulā
    if ($guide->rspage_id == '0') {
        $ins = $db->query("INSERT INTO `rs_pages` (page_id, category_id, created_by, created_at) VALUES(
            ".(int)$guide->id.",
            ".(int)$guide->category.",
            ".(int)$auth->id.",
            '".time()."'
        )");
        if (!$ins) {
            set_flash('Kļūda!');
            redirect('/'.$_GET['viewcat']);
        }
        $guide->rspage_id = $db->insert_id;
    }
    
    
    // atjauno raksta informāciju pēc $_POST datiem
    if (isset($_POST['location'])) {

        $location     = (isset($_POST['location']) ? sanitize($_POST['location']) : '');
        $skills       = (isset($_POST['skills']) ? sanitize($_POST['skills']) : '');
        $quests       = (isset($_POST['quests']) ? sanitize($_POST['quests']) : '');
        $extra        = (isset($_POST['extra']) ? sanitize($_POST['extra']) : '');
        $difficulty   = (isset($_POST['difficulty']) ? (int)$_POST['difficulty'] : 0);
        $length       = (isset($_POST['length']) ? (int)$_POST['length'] : 0);
        $storyline    = (isset($_POST['storyline']) ? (int)$_POST['storyline'] : 0);
        $members_only = (isset($_POST['members_only']) ? (int)$_POST['members_only'] : 0);
        $description  = (isset($_POST['description']) ? sanitize($_POST['description']) : '');
        $date         = (isset($_POST['date']) ? date('d/m/Y', strtotime($_POST['date'])) : '');
        
        $short_date   = '';
        if (isset($_POST['date'])) {
            $short_date = (int)substr(sanitize($_POST['date']), -2, 2);
        }
        
        $db->query("
            UPDATE `rs_pages` SET
                `location`      = '$location',
                `skills`        = '$skills',
                `quests`        = '$quests',
                `extra`         = '$extra',
                `year`          = '$date',
                `difficulty`    = '$difficulty',
                `members_only`  = '$members_only',
                `length`        = '$length',
                `class_id`      = '$storyline',
                `description`   = '$description',
                `date`          = '$date'
            WHERE 
                `page_id`           = $guide->page_id AND
                `deleted_by`        = 0 AND
                `is_placeholder`    = 0                    
        ");
    
        set_flash('Raksts "'.$guide->page_title.'", iespējams, atjaunots!');
        redirect('/'.$_GET['viewcat']);
    }
    
    // izdrukā lapā rediģēšanas formu ar visiem raksta datiem
    else {

        $tpl->newBlock('quest-edit');
        $tpl->assignAll($guide);
        
        // free/members only
        if ($guide->rspage_members_only == 1) {
            $tpl->assign('selected-members', ' selected="selected"');
        } else {
            $tpl->assign('selected-free', ' selected="selected"');
        }
        
        // izvēlne ar kvestu sērijām
        $storylines = $db->get_results("SELECT `id`, `title` FROM `rs_classes` WHERE `category` = 'series' ORDER BY `title` ASC");
        if ($storylines) {
            foreach ($storylines as $storyline) {
                $tpl->newBlock('edit-storyline');
                $tpl->assign(array(
                    'story-id' => $storyline->id,
                    'story-title' => $storyline->title
                ));
                if ($storyline->id == $guide->rspage_class) {
                    $tpl->assign('selected', ' selected="selected"');
                }
            }
        }
        
        // izvēlne ar sarežģītības līmeņiem
        foreach ($levels as $level) {
            $tpl->newBlock('edit-difficulty');
            $tpl->assign(array(
                'level-id' => $level[0],
                'level-title' => $level[1]
            ));
            if ($level[0] == $guide->rspage_difficulty) {
                $tpl->assign('selected', ' selected="selected"');
            }
        }
        
        // izvēlne ar kvesta ilgumu
        foreach ($length as $single) {
            $tpl->newBlock('edit-length');
            $tpl->assign(array(
                'length-id' => $single[0],
                'length-title' => $single[1]
            ));
            if ($single[0] == $guide->rspage_length) {
                $tpl->assign('selected', ' selected="selected"');
            }
        }

    }
}





// izdrukā lapā sarakstu ar visiem visu veidu kvestiem
else {

    $cats = array(
        array(100, 'Member Quests'), 
        array(99, 'Free Quests'), 
        array(193, 'Miniquests')
    );

    $levels = array(
        1 => '<span style="color:#16B937;">Novice</span>', 
        2 => '<span style="color:#FA620D;">Intermediate</span>', 
        3 => '<span style="color:#0EDAE2;">Experienced</span>',
        4 => '<span style="color:#2777aa;text-transform:uppercase;">Master</span>',
        5 => '<span style="color:#e93546;text-transform:uppercase;">Grandmaster</span>',
        6 => '<span style="color:#e453e2;text-transform:uppercase;">Special</span>'
    );
    $diffs = array(1, 2, 3, 4, 5, 6);


    foreach ($cats as $cat) {

        $list_guides = $db->get_results("
            SELECT
                `pages`.`id`        AS `page_id`,
                `pages`.`strid`     AS `page_strid`,
                `pages`.`title`     AS `page_title`,
                `pages`.`category`  AS `page_category`,
                
                `users`.`id`        AS `user_id`,
                `users`.`nick`      AS `user_nick`,
                
                IFNULL(`rs_pages`.`id`, 0)  AS `rspage_id`,
                `rs_pages`.`difficulty`     AS `rspage_difficulty`,
                
                IFNULL(`rs_classes`.`id`, 0)  AS `rsclasses_id`,
                `rs_classes`.`title`          AS `rsclasses_title`
            FROM `pages`
                JOIN `users` ON (
                    `pages`.`author`    = `users`.`id` AND
                    `users`.`deleted`   = 0
                )
                LEFT JOIN `rs_pages` ON (
                    `pages`.`id`                = `rs_pages`.`page_id` AND
                    `rs_pages`.`is_placeholder` = 0 AND
                    `rs_pages`.`deleted_by`     = 0
                )
                LEFT JOIN `rs_classes` ON `rs_pages`.`class_id` = `rs_classes`.`id`
            WHERE 
                `pages`.`category`  = ".(int)$cat[0]." AND
                `pages`.`lang`      = ".(int)$lang."
            ORDER BY `pages`.`title` ASC            
        ");
        
        if ($list_guides) {
        
            $tpl->newBlock('quest-list');
            $tpl->assign('cat-title', $cat[1]);
        
            foreach ($list_guides as $guide) {        

                $guide->user_nick = '<a style="font-size:11px" href="#">'.$guide->user_nick.'</a>';
                
                $tpl->newBlock('list-guide');
                $tpl->assignAll($guide);
                
                // kvesta sarežģītība
                if ( $guide->rspage_id != '0' && in_array($guide->rspage_difficulty, $diffs) ) {
                    $tpl->assign('rspage_difficulty', $levels[$guide->rspage_difficulty]);
                } 
                // ieraksta `rs_pages` tabulā
                else if ($guide->rspage_id == '0') {
                
                    $tpl->assign('rspage_difficulty', '--');
                    
                    $ins = $db->query("INSERT INTO `rs_pages` (page_id, category_id, created_by, created_at) VALUES(
                        ".(int)$guide->page_id.",
                        ".(int)$guide->page_category.",
                        ".(int)$auth->id.",
                        '".time()."'
                    )");
                    
                } else {
                    $tpl->assign('rspage_difficulty', '--');
                }
            }
        }

    }
}