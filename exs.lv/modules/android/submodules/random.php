<?php
/**
 *  Apstrādā random Android lietotnes pieprasījumus, 
 *  kurus nav vērts iedalīt kādā specifiskā apakšgrupā.
 */

// pa tiešo šeit nebūs nekādas skatīšanās
!isset($sub_include) and die('Error loading page!');
 

// pieprasītas lietotāja jaunākās notifikācijas
if (isset($_GET['var1']) && $_GET['var1'] == 'notifications') {
    
    $json_page = array(
        'notifications' => a_fetch_notifications()
    );
}
