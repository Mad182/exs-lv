<?php
/**
 *  Android rakstu apakšmodulis
 *
 *  Kaut ko šeit darīs saistībā ar rakstiem.
 */

// pa tiešo šeit nebūs nekādas skatīšanās
!isset($sub_include) and die('Error loading page!');


// atgriezīs informāciju par jaunākajiem rakstiem
$json_page = get_news();


/*
    TODO:
    
        - rakstu rediģēšana (vai ļaut?)
        - komentāru rediģēšana
        - rakstu komentāru slēgšana
        - rakstu vērtēšana
        - komentāru vērtēšana
        - daudz kas cits
        - ...
*/