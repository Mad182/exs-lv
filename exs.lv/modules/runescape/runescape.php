<?php
/**	
 *	runescape apakšprojekta sākumlapas modulis
 */

$flash_phrases = array(
    'You attempt to cross the remaining bridge... But you slip and tumble into the darkness.',
    'Blood, pain, and hate!',
    'You throw in the orb of light... A slight shudder runs down your back.',
    'Iban will save us all!',
    'Oh dear! You are dead...'
);

// sākotnēji lapu nevienam nav jāredz
if ($auth->id != 115) {
    set_flash( $flash_phrases[rand(0, count($flash_phrases) - 1)] );
    redirect('http://exs.lv');
}