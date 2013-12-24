<?php
/**
 *  Ar runescape.exs.lv saistītas pārbaudes, 
 *  kuras vieglākai rediģēšanai iznestas ārpus index.php faila.
 */
 
$flash_phrases = array(
    'You attempt to cross the remaining bridge... But you slip and tumble into the darkness.',
    'Blood, pain, and hate!',
    'You throw in the orb of light... A slight shudder runs down your back.',
    'Iban will save us all!',
    'Oh dear! You are dead...'
);

// sākotnēji lapu nevienam nav jāredz
if ( !$auth->ok || $auth->id != 115) {
    set_flash( $flash_phrases[rand(0, count($flash_phrases) - 1)] );
    redirect('http://exs.lv');
}

 
/**
 *  index.php failā jau pēc noklusējuma neautorizēta statusa
 *  gadījumā lapā tiek iekļauts bloks ar login formu,
 *  tāpēc šajā navigācijā tas netiek pārbaudīts, jo strādā tāpat
 */

if ( $auth->ok ) {
    $tpl->newBlock('auth-nav');
    
    // moderatoriem būs redzamas administrēšanas sadaļas (Mod, RS Mod)
    if ( im_mod() ) {
        $tpl->newBlock('mod-nav');
        
        // RS Mod izvēlnes iezīmēšana
        if ( in_array($category->textid, array('gildes')) ) {
            $tpl->assign('active-rsmod', ' active-page');
        }
        // Mod izvēlnes iezīmēšana
        if ( in_array($category->textid, array('banned', 'crows', 'reports', 'checkform', 'log')) ) {
            $tpl->assign('active-mod', ' active-page');
        }
    }
}

// iekrāso atvērto navigācijas cilni ("Cits"), ja atvērta kāda no tās apakšsadaļām
$other_cats = array(
    793,  // pamatinformācija
    788,  // trenēšanās
    787,  // briesmoņu medīšana
    790,  // nauda pelnīšana
    5,    // citi padomi
    346   // RS rakstu arhīvs
    //536  // Priekšmetu datubāze
);

if ( in_array($category->id, $other_cats) ) {
    $tpl->assignGlobal(array(
		'cat-sel-other' => ' class="selected"'
	));
}
