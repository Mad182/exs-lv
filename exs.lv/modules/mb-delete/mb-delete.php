<?php

if ($auth->ok && isset($_GET['var1'])) {

	$mbid = intval($_GET['var1']);
	$mb = $db->get_row("SELECT * FROM `miniblog` WHERE `id` = '$mbid' AND `lang` = '$lang'");
	if (!empty($mbid) && !empty($mb) && $mb->removed == 0 && ( (im_mod() && strtotime($mb->date) > time() - 86400) || ($mb->author == $auth->id && $auth->level == 3 && strtotime($mb->date) > time() - 1800) ) ) {

		//level 2
		if ($mb->parent != 0 && $mb->reply_to != 0) {
			$db->query("UPDATE miniblog SET removed = '1' WHERE id = '" . $mbid . "' LIMIT 1");
			//$db->query("UPDATE miniblog SET posts = posts-1 WHERE id = '$mb->parent' LIMIT 1");
			//$db->query("UPDATE miniblog SET posts = posts-1 WHERE id = '$mb->reply_to' LIMIT 1");
			return2mb($mb);

			//level 1
		} elseif ($mb->parent != 0) {
			$db->query("UPDATE miniblog SET removed = '1' WHERE id = '" . $mbid . "' LIMIT 1");
			//$db->query("UPDATE miniblog SET posts = posts-1 WHERE id = '$mb->parent' LIMIT 1");
			return2mb($mb);

			//main
		} else {
			$db->query("UPDATE miniblog SET removed = '1' WHERE id = '" . $mbid . "' LIMIT 1");
			$db->query("UPDATE miniblog SET removed = '1' WHERE parent = '" . $mbid . "'");
		}

		$auth->log('Izdzēsa miniblogu', 'miniblog', $mbid);
	}
}
redirect();
