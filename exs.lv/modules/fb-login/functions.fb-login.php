<?php

/**
 * Facebook autorizācijas funkcijas
 */

/**
 * Piešķir medaļu par sekošanu FB lapai
 *
 * @global type $db
 * @global type $m
 * @param type $id
 */
function fb_award($id) {
	global $db, $m;
	$existing_awards = get_awards_list($id);
	if (!in_array('facebook-like', $existing_awards)) {
		$title = sanitize('Facebook.com <a href="https://www.facebook.com/exs.lv">like</a>');
		$db->query("INSERT INTO autoawards (user_id,award,title,created) VALUES ('$id','facebook-like','$title',NOW())");
		$db->update('autoawards', $db->insert_id, ['importance' => $db->insert_id]);
		userlog($id, 'Ieguva medaļu &quot;' . stripslashes($title) . '&quot;', '/dati/bildes/awards/facebook-like.png');
		notify($id, 7);
		$m->delete('aw_' . $id);
	}
}
