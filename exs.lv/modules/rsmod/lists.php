<?php
/**
 *  Saraksti tabulas formā ar RuneScape pamācībām
 *
 *  Šajā sadaļā iespējams izveidot ierakstu par kādu RuneScape pamācību,
 *  kurai raksts lapā var arī neeksistēt. Ierakstiem var piesaistīt eksistējošu
 *  rakstu un citu informāciju. Pamācību sadaļās rāda šajā sadaļā
 *  izveidotos ierakstus, tādējādi tajās redzami arī "placeholders".
 *
 *  Šobrīd pa cilnēm var pārslēgties starp šāda veida rakstiem:
 *
 *      - kvesti (p2p un f2p)
 *      - minikvesti
 *      - minispēles
 *
 *  Cita veida pamācību sadaļās rāda īstos pievienotos rakstus.
 */

!isset($sub_include) and die('No hacking, pls.');

// nosaka atvērto cilni, lai varētu importēt atbilstošo failu,
// jo rediģēšanas un pievienošanas formas ir atšķirīgas, tāpēc šeit nav
$opened_tab = 'quests';
if ($_GET['viewcat'] === 'all-miniquests') {
    $opened_tab = 'miniquests';
} else if ($_GET['viewcat'] === 'all-minigames') {
    $opened_tab = 'minigames';
}

// cilnēm mainās rakstu kategorijas, kas jānorāda SQL pieprasījumam
$tab_cats = '`pages`.`category` IN(99, 100) OR ' . 
            '`rs_pages`.`ph_cat` IN(99, 100)';
if ($_GET['viewcat'] === 'all-miniquests') {
    $tab_cats = '`pages`.`category` = 193 OR `rs_pages`.`ph_cat` = 193';
} else if ($_GET['viewcat'] === 'all-minigames') {
    $tab_cats = '`pages`.`category` = 160 OR `rs_pages`.`ph_cat` = 160';
}


// "active" klase iezīmēs atvērto cilni
$tpl->newBlock('list-tabs');
$tpl->assign('tab-' . $opened_tab, 'active');
    

/**
 *  Izdrukā visus cilnes ierakstus tabulas formā
 */
if (!isset($_GET['var1'])) {

    /*$levels = array(
        1 => '<span style="color:#16B937;">Novice</span>', 
        2 => '<span style="color:#FA620D;">Intermediate</span>', 
        3 => '<span style="color:#0EDAE2;">Experienced</span>',
        4 => '<span style="color:#2777aa;text-transform:uppercase;">Master</span>',
        5 => '<span style="color:#e93546;text-transform:uppercase;">Grandmaster</span>',
        6 => '<span style="color:#e453e2;text-transform:uppercase;">Special</span>'
    );*/
    
    $tpl->newBlock('list-intro-text');
    $tpl->newBlock('list-button-new');

    // atlasa cilnes ierakstus no `rs_pages` tabulas
    $found_pages = $db->get_results("
        SELECT
            `rs_pages`.`id`         AS `rspage_id`,
            `rs_pages`.`is_hidden`,
            `rs_pages`.`title`      AS `rspage_title`,

            `rs_series`.`id`        AS `rsseries_id`,
            `rs_series`.`title`     AS `rsseries_title`,
            
            IFNULL(`pages`.`id`, 0) AS `page_id`,
            `pages`.`strid`         AS `page_strid`,
            `pages`.`title`         AS `page_title`
            
        FROM `rs_pages`
            LEFT JOIN `rs_series` ON 
                `rs_pages`.`series_id` = `rs_series`.`id`
            LEFT JOIN `pages` ON 
                `rs_pages`.`page_id` = `pages`.`id`
        WHERE
            `rs_pages`.`deleted_by` = 0 AND
            (" . $tab_cats . ")
        ORDER BY `rs_pages`.`title` ASC
    ");

    if (!$found_pages) {
        $tpl->newBlock('list-no-pages');
    } else {

        $tpl->newBlock('list-all-pages');
        
        $saved_letter = '';

        foreach ($found_pages as $guide) {

            $tpl->newBlock('list-row');            
            $strid = '';
            // ierakstam ir piesaistīts raksts
            if ($guide->page_id != '0') {                
                $tpl->newBlock('list-page');
                $strid = $guide->page_strid;
            } else { // raksts nav piesaistīts
                $tpl->newBlock('list-page-empty');
            }
            
            $tpl->assign(array(
                'page_id'   => $guide->page_id,
                'rspage_id' => $guide->rspage_id,
                'strid'     => $strid,
                'title'     => $guide->rspage_title
            ));
            
            // fiksē nosaukuma pirmo burtu, 
            // lai pie tā maiņas tabulā to izceltu
            if ($opened_tab === 'quests') {
                if (substr($guide->rspage_title, 0, 1) !== $saved_letter) {
                    $saved_letter = substr($guide->rspage_title, 0, 1);
                    $tpl->assign('splitted-by', 
                                 '<strong>'.$saved_letter.'</strong>');
                    $tpl->assign('splitted-row-style', 
                                 ' class="is-splitted"');
                }
            }
            
            // izbalē rindu, ja attiecīgais ieraksts skaitās "slēpts"
            if ($guide->is_hidden) {
                $tpl->assign('faded-row', ' style="opacity:0.35"');
            }
        }
    }
}


/**
 *  Esoša ieraksta dzēšana
 *
 *  Ja javascript iespējots, tiks izsaukta caur jQuery,
 *  par ko liecinās ($_GET['_'] == 1)
 */
else if (isset($_GET['var1']) && $_GET['var1'] === 'delete' &&
         isset($_GET['var2']) && is_numeric($_GET['var2'])) {

    $entry_id = (int)$_GET['var2'];
    $delete = $db->query("
        UPDATE `rs_pages`
        SET
            `deleted_by` = '" . (int)$auth->id . "',
            `deleted_at` = '" . time() . "'
        WHERE 
            `id` = $entry_id AND
            `deleted_by` = 0
    ");
    
    if (isset($_GET['_'])) {
        echo json_encode(array('state' => 'success', 
                               'content' => 'Ieraksts dzēsts'));
    } else {
        set_flash('Ieraksts dzēsts');
        redirect('/' . $category->textid);
    }
    exit;
}


/**
 *  Esoša ieraksta "slēptā" statusa atzīmēšana
 *
 *  Ja javascript iespējots, tiks izsaukta caur jQuery,
 *  par ko liecinās ($_GET['_'] == 1)
 */
else if (isset($_GET['var1']) && $_GET['var1'] === 'hide' &&
         isset($_GET['var2']) && is_numeric($_GET['var2'])) {

    $entry_id = (int)$_GET['var2'];
    
    // pārbauda, vai norādītais ieraksts vispār eksistē
    $get_entry = $db->get_row("
        SELECT `id`, `is_hidden` FROM `rs_pages` 
        WHERE `id` = $entry_id AND `deleted_by` = 0
    ");
    if (!$get_entry) {
        if (isset($_GET['_'])) {
            echo json_encode(array('state' => 'error', 
                                   'content' => 'Ieraksts nav atrasts'));
        } else {
            set_flash('Ieraksts nav atrasts');
            redirect('/' . $category->textid);
        }
        exit;
    }
    
    // izmaina vērtību datubāzē un atgriež paziņojumu
    $swap_to = ($get_entry->is_hidden) ? 0 : 1;
    $swap_text = ($swap_to) ? 'hidden' : 'shown';
    
    $swap = $db->query("
        UPDATE `rs_pages`
        SET
            `updated_by` = " . (int)$auth->id . ",
            `updated_at` = '" . time() . "',
            `is_hidden` = $swap_to
        WHERE 
            `id` = $entry_id AND
            `deleted_by` = 0
    ");
    
    if (isset($_GET['_'])) {
        echo json_encode(array('state' => 'success', 'content' => $swap_text));
    } else {
        set_flash(($swap_to) ? 'Ieraksts slēpts' : 
                               'Ieraksts vairs nav slēpts');
        redirect('/' . $category->textid);
    }
    exit;
}


/**
 *  Citiem variantiem iekļaus sadaļas papildfailu
 */
else {

    $php_filename = 'lists_quests.php';
    
    if ($opened_tab === 'miniquests') {
        $php_filename = 'lists_miniquests.php';
    } else if ($opened_tab === 'minigames') {
        $php_filename = 'lists_minigames.php';
    }    
    $php_filename = CORE_PATH.'/modules/'.$category->module.'/'.$php_filename;
    
    if (file_exists($php_filename)) {
        include($php_filename);
    } else { // kādēļ neeksistē fails?
        set_flash('Kļūdaini norādīta adrese');
        redirect('/' . $_GET['viewcat']);
    }
}
