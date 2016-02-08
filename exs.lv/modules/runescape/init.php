<?php
/**
 *  Šis fails tiek iekļauts root index.php failā vēl pirms moduļa ielādes,
 *  lai varētu veikt papildpārbaudes, ar tām neaizrakstot pilnu index failu.
 */

// īslaicīgs kods, lai paslēptu vairs nevajadzīgus "NEW"
$today = date('Y-m-d H:i:s');
if ($today < '2016-02-13 00:00:00') {
    $tpl->assignGlobal('skills-is-new', '&nbsp;<span class="is-new">new</span>');
    $tpl->assignGlobal('hs-is-new', '&nbsp;<span class="is-new">new</span>');
}

// atkarībā no izvēlētajiem iestatījumiem lapai tiks
// izvēlēts atbilstošs fona attēls
$bg_name = 'goats.jpg'; // pēc noklusējuma
if ($auth->ok) {
    if ($auth->rs_bg == 0) {
        $bg_name = 'lost-city-of-the-elves.jpg';
    } else if ($auth->rs_bg == 1) {
        $bg_name = 'goats.jpg';
    } else {
        $bg_name = 'runescape-map.jpg';
    }
}
$tpl->newBlock('rs-background-css');
$tpl->assign('background-title', $bg_name);
 
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
    // īslaicīgs kods, lai paslēptu vairs nevajadzīgu "NEW"
    if ($today < '2016-02-15 00:00:00') {
        $tpl->assignGlobal('paint-is-new', '&nbsp;<span class="is-new">new</span>');
    }

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
