<?php
/**
 *  Ar ģildēm saistītu ierakstu pievienošana un rediģēšana
 *
 *  Tiek iekļauts lists.php failā
 */

!isset($sub_include) and die('No hacking, pls.');


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
                    `category` = ".(int)$cat_guilds."
            ");
            if ($if_exists) {
                $page_id = (int)$if_exists->id;
            }
        }

        $title = (isset($_POST['title']) && !empty($_POST['title'])) ?
            input2db($_POST['title'], 255) : '--';

        // citi parametri, kas nav obligāti norādāmi       
        $members_only = (isset($_POST['members_only'])) ? 
            (int)((bool)$_POST['members_only']) : 0;
        $starting_point = (isset($_POST['starting_point'])) ? 
            input2db($_POST['starting_point'], 100) : '';
        $extra = (isset($_POST['extra'])) ? 
            input2db($_POST['extra'], 1024) : '';
        
        $insert = $db->query("
            INSERT INTO `rs_pages`
                (page_id, cat_id, title, members_only, starting_point, extra, 
                 created_by, created_at)
            VALUES(
                $page_id,
                ".(int)$cat_guilds.",
                '$title',
                $members_only,
                '$starting_point',
                '$extra',
                '".(int)$auth->id."',
                '".time()."'
            )
        ");

        set_flash('Ieraksts pievienots');
        redirect('/'.$_GET['viewcat']);
    
    // būs redzama jauna ieraksta pievienošanas forma
    } else {    
        $tpl->newBlock('guild-form');
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
                `pages`.`category` = ".(int)$cat_guilds."
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
                        `category` = ".(int)$cat_guilds."
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
        $entry->members_only = (isset($_POST['members_only'])) ? 
            (bool)$_POST['members_only'] : false;
        $entry->starting_point = (isset($_POST['starting_point'])) ? 
            input2db($_POST['starting_point'], 256) : '';
        $entry->extra = (isset($_POST['extra'])) ? 
            input2db($_POST['extra'], 1024) : '';

        $db->query("
            UPDATE `rs_pages` SET
                `page_id`        = ".$entry->page_id.",
                `title`          = '".$entry->title."',
                `members_only`   = ".(int)$entry->members_only.",
                `starting_point` = '".$entry->starting_point."',
                `extra`          = '".$entry->extra."',
                `updated_by`     = ".(int)$auth->id.",
                `updated_at`     = '".time()."'
            WHERE 
                `id`          = ".(int)$entry->id." AND
                `deleted_by`  = 0               
        ");
    
        set_flash('Ieraksts atjaunots');
        redirect('/'.$_GET['viewcat'].'/edit/'.(int)$entry->id);

    // parāda lapā ieraksta rediģēšanas formu
    } else {

        $tpl->newBlock('guild-form');
        $tpl->assignAll($entry);
        $tpl->assign('strid', $entry->strid);
        
        // free/members only
        if ((bool)$entry->members_only) {
            $tpl->assign('selected-members', ' selected="selected"');
        } else {
            $tpl->assign('selected-free', ' selected="selected"');
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
