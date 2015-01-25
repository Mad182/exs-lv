<?php
/**
 *  Android miniblogu apakšmodulis.
 *
 *  Apstrādā pieprasījumus saistībā ar darbībām miniblogos, tai skaitā
 *  jauna minibloga pievienošanu vai esoša komentēšanu, vērtēšanu u.c.
 */

// nebūs iespējams skatīt failu pa tiešo
!isset($sub_include) and die('Error loading page!');

require_once(CORE_PATH.'/modules/android/functions.miniblogs.php');

// piegriezies rakstīt isset pārbaudi un neērto $_GET
$var1 = (!empty($_GET['var1'])) ? $_GET['var1'] : '';
$var2 = (!empty($_GET['var2'])) ? $_GET['var2'] : '';
$var3 = (!empty($_GET['var3'])) ? $_GET['var3'] : '';


/**
 *  Atgriezīs jaunāko miniblogu sarakstu.
 *  (/miniblogs/getlist)
 */
if ($var1 === 'getlist') {
    a_fetch_miniblogs();

/**
 *  Jauna minibloga pievienošana vai esoša minibloga komentēšana.
 *  (/miniblogs/{new|comment})
 */
} else if ($var1 === 'new' || $var1 === 'comment') {
    
    if (empty($_POST['group_id']) || empty($_POST['parent_id']) ||
        empty($_POST['content']) || empty($_POST['is_private'])) {
        a_error('Kļūdains pieprasījums');
        if ($var1 === 'new') {
            a_log('Netika iesniegti minibloga ieraksta pievienošanas dati');
        } else {
            a_log('Netika iesniegti minibloga komentēšanas dati');
        }
    } else {
        a_add_miniblog(array(
            'group_id' => $_POST['group_id'],
            'parent_id' => $_POST['parent_id'],
            'is_private' => $_POST['is_private'],
            'content' => $_POST['content']
        ));
    }

/**
 *  Minibloga vērtēšana ar plusu vai mīnusu.
 *  (/miniblogs/{miniblog_id}/{plus|minus}/{comment_id|(optional)})
 */
} else if (!empty($var1) && !empty($var2) &&
           in_array($var2, array('plus', 'minus'))) {

    // miniblogā esoša komentāra vērtēšana
    if (!empty($var3)) {
        a_rate_comment($var3, ($var2 === 'plus'));
    } else { // galvenā minibloga vērtēšana
        a_rate_comment($var1, ($var2 === 'plus'));
    }

/**
 *  Minibloga satura atgriešana ar visiem komentāriem.
 *  (/miniblog/{miniblog_id})
 */
} else if (!empty($var1)) {
    a_fetch_miniblog($var1);

/**
 *  Citi gadījumi.
 */
} else {
    a_error('Kļūdaini veikts pieprasījums');
}
