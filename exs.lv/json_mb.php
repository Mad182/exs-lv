<?php

/**
 * Miniblogu ajax update
 */
if (!isset($_GET['mbid']) || !isset($_GET['lastid'])) {
	die('err');
}

require('configdb.php');

require(CORE_PATH . '/includes/class.mdb.php');
require(CORE_PATH . '/includes/functions.core.php');

/* nosaka, kuru lapu rādīt (exs.lv, coding.lv, etc) */
require(CORE_PATH . '/includes/site_loader.php');

$db = new mdb($username, $password, $database, $hostname);

$mbid = (int) $_GET['mbid'];
$lastid = (int) $_GET['lastid'];
$lastedit = (int) $_GET['et'];

// apakšprojekti, kuriem rādīt ziņošanas podziņu
$allowed_sites = array(1, 7, 9); // exs.lv; lol.exs.lv; runescape.exs.lv


if (isset($_GET['type']) && $_GET['type'] == 'junk') {
	$type = 'junk';
} else {
	$type = 'miniblog';
}

$json = array();

$resps = $db->get_results("SELECT
		`miniblog`.`text` AS `text`,
		`miniblog`.`vote_value` AS `vote_value`,
		`miniblog`.`vote_users` AS `vote_users`,
		`miniblog`.`date` AS `date`,
		`miniblog`.`author` AS `author`,
		`miniblog`.`groupid` AS `groupid`,
		`miniblog`.`id` AS `id`,
		`miniblog`.`posts` AS `posts`,
		`miniblog`.`reply_to` AS `reply_to`,
		`miniblog`.`removed` AS `mb_removed`,
		`users`.`nick` AS `nick`,
		`users`.`decos` AS `decos`,
		`users`.`avatar` AS `avatar`,
		`users`.`av_alt` AS `av_alt`,
		`users`.`level` AS `level`
	FROM
		`miniblog`, `users`
	WHERE
		`miniblog`.`parent` = '" . $mbid . "' AND
		`miniblog`.`type` = '" . $type . "' AND
		`miniblog`.`id` > '" . $lastid . "' AND
		`users`.`id` = `miniblog`.`author`
	ORDER BY `miniblog`.`id` ASC LIMIT 20");

if ($resps) {

	session_start();

	require(CORE_PATH . '/includes/class.auth.php');

	//memcached konekcija
	$m = new Memcache;
	$m->connect($mc_host, $mc_port);

	$site_access = get_site_access();

	$auth = new Auth();

	//"cake day"
	$cday_users = get_cakeday();

	foreach ($resps as $resp) {

		$json['id'] = $resp->id;

		$level = get_mb_level($resp->id);

		if ($resp->groupid) {
			$limit = 4;


			$group = $db->get_row("SELECT `id`, `public`, `owner`  FROM `clans` WHERE `id` = '$resp->groupid'");
			if (!$group->public) {
				if (!$auth->ok) {
					continue;
				}
				if (
						$auth->id != $group->owner &&
						$auth->id != 1 &&
						!$db->get_var("SELECT count(*) FROM clans_members WHERE clan = '$group->id' AND user = '$auth->id' AND approve = '1'")) {
					continue;
				}
			}
		} else {
			$limit = 2;
		}

		$out = '<div class="mb-av"><a id="m' . $resp->id . '" href="/user/' . $resp->author . '"><img class="av" width="45" height="45" src="' . get_avatar($resp, 's') . '" alt="" /></a>';

		if (!empty($resp->decos)) {
			$decos = unserialize($resp->decos);
			if (!empty($decos)) {
				$di = 0;
				foreach ($decos as $deco) {
					$out .= '<img src="' . $deco['icon'] . '" alt="' . $deco['title'] . '" title="' . $deco['title'] . '" class="user-deco deco-pos-' . $di . '" />';
					$di++;
				}
			}
		}

		$out .= '</div><div class="response-content">';
		if ($auth->ok && $level < $limit) {
			$out .= '<a href="' . $resp->id . '" class="mb-reply-to mb-icon">Atbilde</a>';
		}
		if ($auth->ok && isset($_GET['url'])) {
			$out .= '<div class="mb-rater">' . mb_rater($resp, htmlspecialchars(strip_tags($_GET['url']))) . '</div>';
		}
		$resp->date = strtotime($resp->date);
		$out .= '<p class="post-info"><a href="/user/' . $resp->author . '">' . usercolor($resp->nick, $resp->level, true, $resp->author) . '</a> ' . display_time($resp->date);

		//permalink
		$out .= ' <a href="#m' . $resp->id . '" class="post-button comment-permalink" title="Saite uz komentāru">#</a>';

		//poga lietotāja pārkāpuma noziņošanai (ja ieraksts jau nav dzēsts)
		if ($resp->mb_removed == 0 && $auth->ok && !$auth->mobile && in_array($lang, $allowed_sites)) {
			$out .= ' <a class="post-button report-user" href="/report/miniblog/' . $resp->id . '" title="Ziņot par pārkāpumu">ziņot</a>';
		}

		//labot (ja ieraksts jau nav dzēsts)
		if ($resp->mb_removed == 0 && !$auth->mobile && !$intro && ($resp->date > time() - 1800 || ($auth->level == 2 && $resp->author == $auth->id && $resp->date > time() - 86400) || $auth->level == 1 || $auth->id == 115) &&
				(im_mod() || (!$closed && $auth->karma >= $min_post_edit && $resp->author == $auth->id))) {
			$out .= ' <a href="/edit/' . $resp->id . '" class="post-button post-edit" title="Labot komentāru">labot</a>';
		}

		//dzēst (ja ieraksts jau nav dzēsts)
		if ($resp->mb_removed == 0 && $auth->ok && ( ($auth->id == $resp->author && $auth->level == 3 && $resp->date > time() - 1800) || (im_mod() && $resp->date > time() - 86400) )) {
			$out .= ' <a href="/delete/' . $resp->id . '?token=' . make_token('delmb') . '" class="post-button post-delete delete-fast" title="Dzēst komentāru">dzēst</a>';
		}

		//moderatoriem - par šo minibloga ierakstu iedot brīdinājumu (saīsinam ceļu un tādējādi slinkumu)
		if ($resp->mb_removed == 0 && $auth->ok && im_mod() && $auth->id != $resp->author) {
			$out .= ' <a href="/warns/' . $resp->author . '/commentid/' . $resp->id . '" class="post-button post-warn" title="Brīdināt">brīdināt</a>';
		}

		$out .= '</p>';
		if ($resp->mb_removed == 1) {
			$out .= '<p class="deleted-entry">Saturs dzēsts!';
			// moderatoriem apskatāms dzēstā ieraksta saturs
			if (im_mod() && !$auth->mobile) {
				$out .= '<a style="float:right" class="deleted-content" href="/mbview/' . $resp->id . '">skatīt saturu</a>';
			}
			$out .= '</p>';
		} else {
			$out .= '<div class="post-content">' . add_smile($resp->text) . '</div>';
		}

		$out .= '<ul class="responses-' . $resp->id . ' level-' . ($level + 1) . '"><li style="display:none"></li></ul><div class="c"></div><div class="reply-ph"></div>';
		$out .= '</div>';
		$json['comment'][$resp->reply_to][] = $out;
	}
}

$time = $db->get_var("SELECT `date` FROM `miniblog` WHERE `parent` = '" . $mbid . "' AND `id` = '$lastid'");
if ($time) {
	$time = strtotime($time);
	$json['et'] = $time;
	$compare = max($time, $lastedit);
	$edit_since = $db->get_results("SELECT
		`text`, `id`, `edit_time`
		FROM
		`miniblog`
		WHERE
		`parent` = " . $mbid . " AND
		`edit_time` > " . $compare);
	if ($edit_since) {
		foreach ($edit_since as $edit) {
			if ($edit->edit_time > $json['et']) {
				$json['et'] = $edit->edit_time;
			}
			$json['edits'][$edit->id] = add_smile($edit->text);
		}
	}
}

header("Content-type: application/json");
echo json_encode($json);
