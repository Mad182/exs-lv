<?php
/**
 *  Šis fails tiek iekļauts root index.php failā vēl pirms moduļa ielādes,
 *  lai varētu veikt papildpārbaudes, ar tām neaizrakstot pilnu index failu.
 */

// atkarībā no izvēlētajiem iestatījumiem lapas fonam tiks
// izvēlēts vai nu viens, vai otrs attēls
if ($auth->ok && $auth->rs_bg == 1) {
    $tpl->newBlock('rs-background-goats');
} else {
    $tpl->newBlock('rs-background-elves');
}
 
// pretēji tam, kā norādīts datubāzē pie sadaļas parametriem,
// lai grupas info tomēr būtu wrappera kreisajā pusē (tā smukāk :))
if ($category->module === 'group') {
    $category->options = 'no-right';
}

// "Lobby" cilne iekrāsosies tikai tad, ja tieši index sadaļa būs atvērta,
// bet ignorēs tās reizes, kad atvērta apakškategorija
if ($category->id == 1863) {
    $tpl->assignGlobal('cat_sel_1863', ' class="selected"');
}

// index.php failā jau pēc noklusējuma neautorizēta statusa
// gadījumā lapā tiek iekļauts bloks ar login formu,
// tāpēc šajā navigācijā tas netiek pārbaudīts, jo strādā tāpat

if ($auth->ok) {
    $tpl->newBlock('auth-nav');

    // RS Mod izvēlne
    if (im_rs_mod()) {
        $tpl->newBlock('rsmod-nav');
    }

    // Mod izvēlne
    if (im_mod()) {
        $tpl->newBlock('mod-nav');
        if (in_array($category->textid, 
            array('banned', 'crows', 'reports', 'checkform', 'log'))) {
            $tpl->assign('active-mod', ' class="selected"');
        }
    }
}
