<?php
/**
 *  RuneScape random faktu pārvaldība.
 *
 *	Moduļa adrese: 		exs.lv/rsfacts
 *	Pēdējās izmaiņas: 	24.12.2013 ( Edgars )
 */

if ( $lang != 9 ) {
    redirect('http://exs.lv');
    die();
}


// atgriež kādu runescape faktu jquery pieprasījumam;
// fakts tiek izdrukāts virs lapas banera
if ( isset($_GET['_']) ) {
    
    $facts_count = $db->get_var("SELECT count(*) FROM `facts_rs` WHERE `is_short` = 1 ");
    if ( $facts_count > 0 ) {
    
        $rand = rand(0, $facts_count - 1);
        $single_fact = $db->get_row("SELECT `text` FROM `facts_rs` WHERE `is_short` = 1 LIMIT $rand, 1");
        
        if ( $single_fact ) {
        
            echo strip_tags($single_fact->text);
        }
        else {
            echo 'Neizdevās atlasīt nevienu RuneScape faktu! ;(';
        }
    }
    else {
        echo 'Neizdevās atlasīt nevienu RuneScape faktu! ;(';
    }
    exit;
}

$all_facts = $db->get_results("SELECT `id`,`text` FROM `facts_rs` WHERE `is_short` = 1 ORDER BY `id` ASC");
$counter = 1;
foreach ($all_facts as $fact) {
    
    echo $counter . '. '.$fact->text.'<br>';
    $counter++;
    
    /*if ( mb_strlen(strip_tags(trim($fact->text))) <= 140) {
        echo $counter . '. '.$fact->text.'<br>';
        $counter++;
        $db->update('facts_rs', $fact->id, array('is_short' => 1));
    }*/
}

exit;
