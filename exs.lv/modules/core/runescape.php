<?php
/**
 *  Ar runescape.exs.lv saistītas pārbaudes, 
 *  kuras vieglākai rediģēšanai iznestas ārpus index.php faila.
 */
 
/*$flash_phrases = array(
    'Blood, pain, and hate!',
    'You throw in the orb of light... A slight shudder runs down your back.',
    'Iban will save us all!',
    'Oh dear! You are dead...'
);

// sākotnēji lapu nevienam nav jāredz
if ( !im_mod() ) {
    set_flash( $flash_phrases[rand(0, count($flash_phrases) - 1)] . ' (Sadaļa būs pieejama nedaudz vēlāk!)');
    redirect('http://exs.lv');
}*/

 
/**
 *  index.php failā jau pēc noklusējuma neautorizēta statusa
 *  gadījumā lapā tiek iekļauts bloks ar login formu,
 *  tāpēc šajā navigācijā tas netiek pārbaudīts, jo strādā tāpat
 */

if ( $auth->ok ) {
    $tpl->newBlock('auth-nav');
    
    // moderatoriem būs redzamas administrēšanas sadaļas (Mod, RS Mod)
    if ( im_mod() ) {
    
        // Mod izvēlnes iezīmēšana
        $tpl->newBlock('mod-nav');
        if ( in_array($category->textid, array('banned', 'crows', 'reports', 'checkform', 'log')) ) {
            $tpl->assign('active-mod', ' active-page');
        }
        
        // RS Mod izvēlnes iezīmēšana
        if ( $auth->id == 115 ) {
            $tpl->newBlock('rsmod-nav');            
            if ( in_array($category->textid, array('gildes')) ) {
                $tpl->assign('active-rsmod', ' active-page');
            }
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
    346,  // RS rakstu arhīvs
    1087 // Oldschool RuneScape pamācības
    //789   // RS stāsti & vēsture 
    //536  // Priekšmetu datubāze
);

if ( in_array($category->id, $other_cats) ) {
    $tpl->assignGlobal(array(
		'cat-sel-other' => ' class="selected"'
	));
}

if ( $auth->id == 115 && isset($_GET['update-groups'])) {

    $rsclans = $db->get_results("SELECT `id`,`strid` FROM `clans` WHERE `category_id` = 4");
    if ($rsclans) {
        foreach ($rsclans as $clan) {
            $upd = $db->query("UPDATE `clans` SET `lang` = 9 WHERE `id` = '".(int)$clan->id."' ");
            $upd = $db->query("UPDATE `cat` SET `lang` = 9 WHERE `textid` = '".sanitize($clan->strid)."' ");
            $upd = $db->query("UPDATE `miniblog` SET `lang` = 9 WHERE `groupid` = '".(int)$clan->id."' ");
        }
    }
    redirect();
}

