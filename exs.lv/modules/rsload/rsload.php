<?php
/**
 *  Uz šo moduli novirzāmi dažnedažādi exs RuneScape apakšprojektā
 *  izvietoti ajax pieprasījumi, kas ielādē HTML (vai līdzīgu) saturu.
 *
 *  Piemērs pieprasījuma adresei:
 *      https://runescape.exs.lv/rsload?what={..}
 *
 *  Autors: Edgars P.
 */

require_once(CORE_PATH . '/modules/runescape/functions.runescape.php');

$default_response = 'error';
 
if (!isset($_GET['what'])) {
    echo $default_response;
    exit;
}

// sākumlapā, nospiežot uz cilnes, ielādēs jaunākos rs ziņu ierakstus
if ($_GET['what'] === 'runescape') {
    echo fetch_news('runescape');
    
// sākumlapā, nospiežot uz cilnes, ielādēs jaunākos oldschool ziņu ierakstus
} else if ($_GET['what'] === 'oldschool') {
    echo fetch_news('oldschool');

} else {
    echo $default_response;
}

exit;
