<?php

/**
 * Sagatavo raksta tekstu lai to varētu rādīt kā ievadu
 */
function trim_intro($text, $len = 140) {

	//get rid of smilies, will strip images later
	$text = add_smile($text);

	//remove unneeded symbols
	$text = str_replace(array('Spēles nosaukums:', '&nbsp;', "\t", "\n", chr(0xC2) . chr(0xA0)), ' ', $text);

	//replace list items with dots
	$text = str_replace('<li>', ' • ', $text);

	//remove repeated spaces
	$text = preg_replace('/ +/', ' ', $text);

	return ucfirst(textlimit(trim(trim(strip_tags($text)), chr(0xC2) . chr(0xA0)), $len));
}
