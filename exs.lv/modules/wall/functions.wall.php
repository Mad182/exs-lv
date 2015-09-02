<?php

/**
 * Aizvāc norādītos html tagus
 */
function strip_selected_tags($text, $tags = array()) {

	$args = func_get_args();
	$text = array_shift($args);
	$tags = func_num_args() > 2 ? array_diff($args, array($text)) : (array) $tags;
	foreach ($tags as $tag) {
		if (preg_match_all('/<' . $tag . '[^>]*>(.*)<\/' . $tag . '>/iU', $text, $found)) {
			$text = str_replace($found[0], $found[1], $text);
		}
	}
	
	//sākumlapā visas bildes lādējam caur https proxy, lai pārlūki nerāda ssl erroru
	$text = str_ireplace('src="http://', 'src="https://images.weserv.nl/?w=300&url=', $text);

	return $text;
}

/**
 * Sagatavo raksta tekstu lai to varētu rādīt kā ievadu
 */
function trim_intro($text, $len = 110) {

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

function get_index_events() {
	global $db, $lang, $img_server;
	$out = '';
	$actions = $db->get_results("SELECT `user`, `action`, `avatar`, `time` FROM `userlogs` WHERE `lang` = '$lang' ORDER BY `time` DESC LIMIT 5");

	if ($actions) {
		$out .= '<ul class="user-actions">';
		foreach ($actions as $action) {

			$user = get_user($action->user);
			if (!$action->avatar) {
				$action->avatar = get_avatar($user, 's');
			}

			$action->avatar = str_replace('http://img.exs.lv/dati', $img_server . '/dati', $action->avatar);

			$out .= '<li><img class="av" style="width:45px;height:45px" src="' . $action->avatar . '" alt="" /><div class="event-content"><span class="post-time">' . time_ago($action->time) . ', ' . $user->nick . '</span>' . $action->action . '</div><div class="c"></div></li>';
		}
		$out .= '</ul>';
	}
	return $out;
}

