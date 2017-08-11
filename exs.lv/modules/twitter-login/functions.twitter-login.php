<?php

/**
 * Twitter autotizācijas f-jas
 */

/**
 * Piešķir medaļu par sekošanu twitter
 *
 * @global type $db
 * @global type $m
 * @param type $id
 */
function twitter_award($id) {
	global $db, $m;
	$existing_awards = get_awards_list($id);
	if (!in_array('twitter-follower', $existing_awards)) {
		$title = sanitize('Twitter.com <a href="https://twitter.com/exs_lv" rel="nofollow">sekotājs</a>');
		$db->query("INSERT INTO autoawards (user_id,award,title,created) VALUES ('$id','twitter-follower','$title',NOW())");
		$db->update('autoawards', $db->insert_id, ['importance' => $db->insert_id]);
		userlog($id, 'Ieguva medaļu &quot;' . stripslashes($title) . '&quot;', '/dati/bildes/awards/twitter-follower.png');
		notify($id, 7);
		$m->delete('aw_' . $id);
	}
}

