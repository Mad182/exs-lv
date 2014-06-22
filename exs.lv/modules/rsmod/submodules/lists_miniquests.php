<?php
/**
 *  Ar minikvestiem saistītu ierakstu pievienošana un rediģēšana
 *
 *  Tiek iekļauts lists.php failā
 */

!isset($sub_include) and die('No hacking, pls.');

/**
 *  Jauna ieraksta pievienošana
 */
if ($_GET['var1'] === 'new') {
    echo 'new';
}


/**
 *  Esoša ieraksta rediģēšana
 */
else if ($_GET['var1'] === 'edit') {
    echo 'edit';
}


/**
 *  Kļūda... kā šeit nonāca? Laikam norādīja nepareizu $_GET['var1'].
 */
else {
    set_flash('Kļūdaini norādīta adrese');
    redirect('/' . $_GET['viewcat']);
}
