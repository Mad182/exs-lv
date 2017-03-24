<?php
/**
 *  Android exs.lv "kolekciju" apakšmodulis.
 *
 *  Apstrādā pieprasījumus saistībā ar vienreiz lejuplādējamu informāciju.
 */
 
require API_PATH.'/shared/shared.collections.php';

/**
 *  Smaidiņu saraksta izdrukāšana.
 *  //android.exs.lv/collections/smilies
 */
if ($var1 === 'smilies') {    
    api_collections_smilies();
}

/**
 *  Izdrukā sarakstu ar attēliem, kādi tiek izmantoti notifikācijās.
 *  //android.exs.lv/collections/notifications
 */
else if ($var1 === 'notifications') {    
    api_collections_notifs();
}

/**
 *  Citas situācijas.
 */
else {
    api_log('Sasniegts kolekciju moduļa "else" bloks.');
    api_error('hellou... are thou lost?');
}
