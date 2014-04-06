<?php
/**
 *  Android miniblogu apakšmodulis
 *
 *  Kaut ko šeit darīs saistībā ar miniblogiem.
 */

// pa tiešo šeit nebūs nekādas skatīšanās
!isset($sub_include) and die('Error loading page!');



// izvēlēts konkrēts miniblogs;
// parādīs tā saturu un komentārus
if (isset($_GET['var1'])) {


}

else {

    $json_page = fetch_miniblogs();

}
/*
    TODO:
    
        - saraksts ar jaunākajiem miniblogiem
        - minibloga komentāru atjaunošana
        - komentāra pievienošana
        - vērtēšana
        - rediģēšana
        - cits
        - cits
        - ...
*/