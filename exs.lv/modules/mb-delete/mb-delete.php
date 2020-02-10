<?php

/**
 *  Dzēšana notiek divos veidos:
 *
 *  1. Izmantojot jQuery, atgriež atpakaļ dzēsta komentāra paziņojumu.
 *  2. Bez jQuery (dzēšot galveno mb vai atslēdzot javascriptu) - novirza uz sākumlapu.
 */
if ($auth->ok && isset($_GET['var1']) && check_token('delmb', $_GET['token'])) {

	$mbid = intval($_GET['var1']);
	$mb = $db->get_row("SELECT * FROM `miniblog` WHERE `id` = '$mbid' AND `lang` = '$lang'");

	if (!empty($mbid) && !empty($mb) && $mb->removed == 0 && ( (im_mod() && strtotime($mb->date) > time() - 286400) || ($mb->author == $auth->id) || ($auth->level == 1 && $debug))) {

		//level 2
		if ($mb->parent != 0 && $mb->reply_to != 0) {
			$db->query("UPDATE miniblog SET removed = '1' WHERE id = '" . $mbid . "' LIMIT 1");

			$auth->log('Izdzēsa miniblogu', 'miniblog', $mbid);

			if (!isset($_GET['_'])) {
				return2mb($mb);
			} else {
				$message = '<p class="deleted-entry">Saturs dzēsts!';
				// moderatoriem apskatāms dzēstā ieraksta saturs
				if (im_mod() && !$auth->mobile && $lang == 1) {
					$message .= '<a style="float:right" class="deleted-content" href="/mbview/' . $mb->id . '">skatīt saturu</a>';
				}
				$message .= '</p>';
                
				echo json_encode(['state' => 'success', 'message' => $message]);
				exit;
			}

		//level 1
		} elseif ($mb->parent != 0) {
			$db->query("UPDATE miniblog SET removed = '1' WHERE id = '" . $mbid . "' LIMIT 1");
			
			$auth->log('Izdzēsa miniblogu', 'miniblog', $mbid);
            
			if (!isset($_GET['_'])) {
				return2mb($mb);
			} else {
				$message = '<p class="deleted-entry">Saturs dzēsts!';
				// moderatoriem apskatāms dzēstā ieraksta saturs
				if (im_mod() && !$auth->mobile && $lang == 1) {
					$message .= '<a style="float:right" class="deleted-content" href="/mbview/' . $mb->id . '">skatīt saturu</a>';
				}
				$message .= '</p>';
                
				echo json_encode(['state' => 'success', 'message' => $message]);
				exit;
			}

			//main
		} else {
			$db->query("UPDATE miniblog SET removed = '1' WHERE id = '" . $mbid . "' LIMIT 1");
			$db->query("UPDATE miniblog SET removed = '1' WHERE parent = '" . $mbid . "'");
			$db->query("DELETE FROM `userlogs` WHERE `multi` = 'mb-answ-" . $mbid . "'");
			$db->query("DELETE FROM `userlogs` WHERE `multi` = 'g-" . $mbid . "'");
			$db->query("DELETE FROM `notify` WHERE `foreign_key` = '" . $mbid . "'");
			$auth->log('Izdzēsa miniblogu', 'miniblog', $mbid);
		}
	}

	if(!empty($mb->groupid)) {
		redirect('/group/' . $mb->groupid);
	}

}

redirect();

