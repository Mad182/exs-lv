<?php
/**
 *  Ar kvestiem saistītu ierakstu pievienošana un rediģēšana
 *
 *  Tiek iekļauts lists.php failā
 */

!isset($sub_include) and die('No hacking, pls.');

/**
 *  Jauna ieraksta pievienošana
 */
if ($_GET['var1'] === 'new') {

    if (isset($_POST['title'])) {

        // virsraksta pārbaude
        $title = '';
        if (isset($_POST['title'])) {
            $title = trim(strip_tags($_POST['title']));
        }        
        if (strlen($title) < 3) {
            set_flash('Pārāk īss ieraksta nosaukums!');
            redirect('/' . $_GET['viewcat'] . '/new');
        }
        
        // pārbauda, vai raksts ar norādīto strid eksistē
        $strid = '';
        $page_id = 0;
        if (isset($_POST['strid'])) {
            $strid = trim(strip_tags($_POST['strid']));
        }        
        if ($strid !== '') {
            $if_exists = $db->get_row("
                SELECT `id` FROM `pages` 
                WHERE 
                    `strid` = '" . sanitize($strid) . "' AND 
                    `category` IN(" . implode(',', $arr_quest_cats) . ")
            ");
            if ($if_exists > 0) {
                $page_id = (int)$if_exists->id;
            } else {
                set_flash('Raksts ar norādīto adresi neeksistē!');
                redirect('/' . $_GET['viewcat'] . '/new');
            }
        }
        
        $insert = $db->query("
            INSERT INTO `rs_pages`
                (page_id, title, created_by, created_at)
            VALUES(
                '" . (int)$page_id . "',
                '" . sanitize($title) . "',
                '" . (int)$auth->id . "',
                NOW()
            )
        ");
    
        set_flash('Ieraksts pievienots');
        redirect('/'.$_GET['viewcat']);
    
    } else {
        $tpl->newBlock('new-page-form');
    }
}


/**
 *  Esoša ieraksta rediģēšana
 */
else if ($_GET['var1'] === 'edit') {

}


/**
 *  Kļūda... kā šeit nonāca? Laikam norādīja nepareizu $_GET['var1'].
 */
else {
    set_flash('Kļūdaini norādīta adrese');
    redirect('/' . $_GET['viewcat']);
}
