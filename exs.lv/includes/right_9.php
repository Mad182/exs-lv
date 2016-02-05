<?php
/**
 *  RuneScape apakšprojekta labā kolonna un tās saturs
 *  (notifikācijas, jaunākie raksti/attēli, aptauja u.tml.).
 */

$tpl->newBlock('main-layout-right');
 
// ja lietotājs iestatījumos norādījis, ka labo kolonnu vēlas redzēt
// kreisajā pusē, tai piešķir citu CSS klasi, kas to paveic
if ($auth->ok && $auth->rs_layout == 1) {
    $tpl->assign('layout-right-class', 'as-left');
}


// lietotāja notifikācijas
if ($auth->ok === true) {
    if ($html = get_notify($auth->id)) {
        $tpl->newBlock('notification-list');
        $tpl->assign('out', $html);
        unset($html);
    }
}


// informatīvs bloks ar informāciju par Discord kanālu
$tpl->newBlock('discord-box');


// jaunākais galerijās
$sel = 'pages';
if (!empty($_COOKIE['last-sidebar-tab']) && $_COOKIE['last-sidebar-tab'] == 'gallery') {
    $out = get_latest_images();
    $sel = 'gallery';
} else { // jaunākais rakstos/blogos
    $out = rs_get_latest_pages();
}
$tpl->newBlock('latest-box');
$tpl->assign(array(
    'latest-noscript'   => $out,
    $sel . '-selected'  => 'active '
));
unset($out);


// aptaujas
include(CORE_PATH . '/modules/core/poll.php');
