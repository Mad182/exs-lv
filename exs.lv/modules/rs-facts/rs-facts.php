<?php

/**
 *  RuneScape random faktu pārvaldība.
 *
 * 	Moduļa adrese: 		exs.lv/rsfacts
 */
if ($lang != 9) {
	die();
}


// atgriež kādu runescape faktu jquery pieprasījumam;
// fakts tiek izdrukāts virs lapas banera
if (isset($_GET['_'])) {

	$facts_count = $db->get_var("SELECT count(*) FROM `facts_rs` WHERE `is_short` = 1 ");
	if ($facts_count > 0) {

		$rand = rand(0, $facts_count - 1);
		$single_fact = $db->get_row("SELECT `text` FROM `facts_rs` WHERE `is_short` = 1 LIMIT $rand, 1");

		if ($single_fact) {
			echo json_encode(array('state' => 'success', 'content' => strip_tags($single_fact->text)));
		} else {
			echo json_encode(array('state' => 'error', 'content' => 'Neizdevās atlasīt nevienu RuneScape faktu! ;('));
		}
	} else {
		echo json_encode(array('state' => 'error', 'content' => 'Neizdevās atlasīt nevienu RuneScape faktu! ;('));
	}
}

exit;
