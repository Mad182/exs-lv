<?php

/**
 * Komentāru vērtēšana (+/-)
 */
if ($auth->ok) {

	/** neļauj vienā sekundē pievienot vairāk kā vienu vērtējumu
	 * pagaiu variants lai izsargātos no plusiņu flooda
	 *
	 * TODO: vajadzetu uztaisīt inteliģentāku sistemu, kas saprot,
	 * ka pēc kārtas pievienoti piemēram 5 vērtējumi pārāk īsā laikā,
	 * un vairs neļauj no šī profila un/vai ip vērtēt šajā dienā
	 */
	if (isset($_SESSION['antiflood_rate']) && microtime(true) - $_SESSION['antiflood_rate'] < 0.5) {
		$_SESSION["antiflood_rate"] = microtime(true);
		$db->query("UPDATE `users` SET `vote_today` = `vote_today`+3 WHERE `id` = '$auth->id'");
		die('Hold your horses!');
	}
	$_SESSION["antiflood_rate"] = microtime(true);

	$table = 'comments';

	if (isset($_GET['type'])) {
		if ($_GET['type'] == 'gallery') {
			$table = 'galcom';
		} elseif ($_GET['type'] == 'mb') {
			$table = 'miniblog';
		}
	}

	$vc = (int) $_GET['vc'];

	$comment = $db->get_row("SELECT `id`,`vote_users`,`vote_value`,`author` FROM `" . $table . "` WHERE `id` = '$vc'");

	if (!empty($comment)) {

		if ($comment->author == $auth->id) {
			die('Par savu komentāru? Tiešām?');
		}

		$check = substr(md5($comment->id . $remote_salt . $auth->id), 0, 5);

		if (!empty($comment->vote_users)) {
			$voters = unserialize($comment->vote_users);
		} else {
			$voters = array();
		}

		$voted = in_array($auth->id, $voters);

		if (isset($_GET['check']) && !$voted && $_GET['check'] == $check && isset($_GET['action'])) {
			$voters[] = $auth->id;
			$comment->vote_users = serialize($voters);

			// balsojumu limits
			$limit = (5 + $auth->karma / 30);
			if(im_mod()) {
				$limit += 50;
			}

			if ($auth->vote_today >= $limit) {
				die('Sasniegts dienas limits');
			} elseif ($_GET['action'] == 'plus') {

				$db->query("UPDATE `" . $table . "` SET vote_value = vote_value+1, vote_users = '" . $comment->vote_users . "' WHERE id = '$vc'");
				$db->query("UPDATE users SET vote_others = vote_others+1, vote_total = vote_total+1, vote_today = vote_today+1 WHERE id = '$auth->id'");
				$comment->vote_value++;
				get_user($auth->id, true);
			} else {

				$db->query("UPDATE `" . $table . "` SET vote_value = vote_value-1, vote_users = '" . $comment->vote_users . "' WHERE id = '$vc'");
				$db->query("UPDATE users SET vote_others = vote_others-1, vote_total = vote_total+1, vote_today = vote_today+1 WHERE id = '$auth->id'");
				$comment->vote_value = $comment->vote_value - 1;
				get_user($auth->id, true);
			}

			if (isset($_GET['_'])) {
				if ($comment->vote_value > 0) {
					$vclass = 'positive';
					$comment->vote_value = '+' . $comment->vote_value;
				} elseif ($comment->vote_value < 0) {
					$vclass = 'negative';
				} else {
					$vclass = 'zero';
				}
				die('<span class="r-val ' . $vclass . '">' . $comment->vote_value . '</span><span class="voted1"></span><span class="voted2"></span>');
			}

			die('Voted!');
		} else {
			die('Jau nobalsots');
		}
	} else {
		die('Nav komentāra');
	}
} else {
	die('Jāielogojas');
}
