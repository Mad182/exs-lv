<?php
/**
 *  Distractions & Diversions
 */
!isset($sub_include) and die('No hacking, pls.');

// raksta informācijas rediģēšana
if (isset($_GET['var1']) && $_GET['var1'] == 'edit' && isset($_GET['var2']) ) {

    // atlasa ar kvestiem saistītos rakstus no `pages` un,
    // ja iespējams, piesaista klāt `rs_pages`
    $guide = $db->get_row("
        SELECT 
            `pages`.`id`                AS `page_id`, 
            `pages`.`strid`             AS `page_strid`, 
            `pages`.`title`             AS `page_title`,
            `pages`.`category`          AS `page_category`,
            
            IFNULL(`rs_pages`.`id`, 0)  AS `rspage_id`,
            `rs_pages`.`members_only`   AS `rspage_members_only`,
            `rs_pages`.`description`    AS `rspage_description`,
            `rs_pages`.`location`       AS `rspage_location`
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
            ".(int)$guide->page_id.",
            ".(int)$guide->page_category.",
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

        $location       = (isset($_POST['location']) ? sanitize($_POST['location']) : '');
        $description    = (isset($_POST['description']) ? sanitize($_POST['description']) : '');
        $members_only   = (isset($_POST['members_only']) ? (int)$_POST['members_only'] : 0);
        
        $db->query("
            UPDATE `rs_pages` SET
                `location`      = '$location',                
                `description`   = '$description',
                `members_only`  = '$members_only',
                `updated_by`    = '".(int)$auth->id."',
                `updated_at`    = '".time()."'
            WHERE 
                `page_id`           = $guide->page_id AND
                `deleted_by`        = 0 AND
                `is_placeholder`    = 0                    
        ");
    
        set_flash('Raksts "'.$guide->page_title.'" atjaunots!');
        redirect('/'.$_GET['viewcat']);
    }
    
    // izdrukā lapā rediģēšanas formu ar visiem raksta datiem
    else {

        $tpl->newBlock('distraction-edit');
        $tpl->assignAll($guide);
        
        if ($guide->rspage_id != '0' && $guide->rspage_members_only == 1) {
            $tpl->assign('selected-members', ' selected="selected"');
        }

    }
}





// izdrukā lapā sarakstu ar visiem visu veidu kvestiem
else {

    $cats = array(
        array(160, 'Minispēles'), 
        array(792, 'Distractions & Diversions')
    );

    foreach ($cats as $cat) {
    
        $list_guides = $db->get_results("
            SELECT
                `pages`.`id`        AS `page_id`,
                `pages`.`strid`     AS `page_strid`,
                `pages`.`title`     AS `page_title`,
                `pages`.`category`  AS `page_category`,
                
                `users`.`id`        AS `user_id`,
                `users`.`nick`      AS `user_nick`,
                
                IFNULL(`rs_pages`.`id`, 0)  AS `rspage_id`
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
            WHERE 
                `pages`.`category`  = ".(int)$cat[0]." AND
                `pages`.`lang`      = ".(int)$lang."
            ORDER BY `pages`.`title` ASC            
        ");
        
        if ($list_guides) {
        
            $tpl->newBlock('distraction-list');
            $tpl->assign('cat-title', $cat[1]);
        
            foreach ($list_guides as $guide) {        

                $guide->user_nick = '<a style="font-size:11px" href="#">'.$guide->user_nick.'</a>';
                
                $tpl->newBlock('list-guide');
                $tpl->assignAll($guide);
                
                if ($guide->rspage_id == '0') {
                    
                    $ins = $db->query("INSERT INTO `rs_pages` (page_id, category_id, created_by, created_at) VALUES(
                        ".(int)$guide->page_id.",
                        ".(int)$guide->page_category.",
                        ".(int)$auth->id.",
                        '".time()."'
                    )");
                    
                }   
            }
        }
    }
}
