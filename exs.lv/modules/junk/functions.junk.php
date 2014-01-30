<?php

/**
 * Parāda datumu "Šodien/Vakar/date"
 */
function display_date_simple($time) {
	if (!$time) {
		$out = '';
	} elseif ($time >= strtotime('today')) {
		$out = 'Šodien';
	} elseif ($time >= strtotime('yesterday')) {
		$out = 'Vakar';
	} else {
		$out = date('d.m.Y.', $time);
	}
	return $out;
}
