<?php
/**
 *  Apstrādā random Android lietotnes pieprasījumus, 
 *  kurus nav vērts iedalīt kādā specifiskā apakšgrupā.
 */

// nebūs iespējams skatīt failu pa tiešo
!isset($sub_include) and die('Error loading page!');
 

// pieprasītas lietotāja jaunākās notifikācijas
if (isset($_GET['var1']) && $_GET['var1'] == 'notifications') {
    
    $json_page = array(
        'notifications' => a_fetch_notifications()
    );

// atgriezīs sarakstu ar tiešsaistē esošiem lietotājiem 
} else if (isset($_GET['var1']) && $_GET['var1'] == 'online') {

    $json_page = a_fetch_online();
}
