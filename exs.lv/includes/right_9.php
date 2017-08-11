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
if ($auth->ok === true && $category->textid === 'index') {
    if ($html = get_notify($auth->id)) {
        $tpl->newBlock('notification-list');
        $tpl->assign('out', $html);
        unset($html);
    }
}


// jaunākais lietotāju albumos
//   - galerijās jārāda, jo otra kolonna nebūs redzama
//   - rshelp sadaļās nerāda, jo labāk, lai redzamāki ir jaunākie raksti
//   - index lapā nerāda, jo tad rādīs jau otrā kolonnā
if ($category->textid === 'gallery' ||
        ($category->module !== 'rshelp' && $category->textid !== 'index')) {
    $tpl->newBlock('latest-images-right');
    $tpl->assign('latest-images', get_latest_images());
}


// jaunākais rakstu sadaļās (tai skaitā, blogos)
$tpl->newBlock('latest-pages');
$tpl->assign('latest-pages', rs_get_latest_pages());


// informatīvs bloks par saziņas kanāliem
$tpl->newBlock('communication-box');


// aptaujas
include(CORE_PATH . '/modules/core/poll.php');
