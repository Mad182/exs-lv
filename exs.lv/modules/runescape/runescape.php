<?php
/** 
 *  RuneScape apakšprojekta sākumlapas modulis.
 *
 *  Šeit nonāk arī atsevišķi vienkārši pieprasījumi,
 *  kuriem netiek veidoti atsevišķi moduļi.
 */

if ($auth->ok) {
    set_action('sākumlapu');
}

// mod opšns
if ((int)$auth->id === 115 && isset($_GET['magic'])) {
    switch ($_GET['magic']) {
        case 'readrss': // lejuplādē jaunākās rs ziņas
            read_rss(true);
            break;
        case 'recreate': // atjauno ziņu cachefailus
            create_news('rs3');
            create_news('oldschool');        
            break;
        case 'logo-list': // dzēš vecos ziņu logo
            get_news_logo_list(isset($_GET['delete']));
    }
}

if ($auth->ok) {
    // fona noformējuma iestatījumi
    if (isset($_GET['bg'])) {
        if ($_GET['bg'] === 'goats') {
            $db->update('users', $auth->id, ['rs_bg' => 1]);
        } else if ($_GET['bg'] === 'map') {
            $db->update('users', $auth->id, ['rs_bg' => 2]);
        } else {
            $db->update('users', $auth->id, ['rs_bg' => 0]);
        }
        $auth->reset();
        redirect();
    }
    // izklājuma iestatījumi
    if (isset($_GET['layout'])) {
        if ($_GET['layout'] === 'sticky') {
            $db->update('users', $auth->id, ['rs_layout' => 0]);
        } else {
            $db->update('users', $auth->id, ['rs_layout' => 1]);
        }
        $auth->reset();
        redirect();
    }
}


// sākumlapā rādīs ierakstus no runescape.com RSS feed
// (izvēle starp OSRS un RuneScape 3 versiju)
$news_type = 'rs3';
if (isset($_COOKIE['last-rsnews-tab']) &&
    $_COOKIE['last-rsnews-tab'] === 'oldschool') {
    $news_type = 'oldschool';
}
read_rss(); // iekšēji funkcija nolasīs tikai reizi x minūtēs

$tpl->newBlock('news-tabs');
$tpl->assign($news_type.'-selected', 'active '); 
$tpl->assign('selected-news', fetch_news($news_type));
