<?php

/*
  Miniblogu ajax update
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
		`users`.`nick` AS `nick`,
		`users`.`decos` AS `decos`,
		`users`.`avatar` AS `avatar`,
		`users`.`av_alt` AS `av_alt`,
		`users`.`level` AS `level`
	FROM
		`miniblog`, `users`
	WHERE
		`miniblog`.`parent` = '" . $mbid . "' AND
		`miniblog`.`removed` = '0' AND
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
	$auth = new Auth();

	$is_miniblog = $mbid;
	foreach ($resps as $resp) {
		if ($resp->avatar == '') {
			$resp->avatar = 'none.png';
		}
		if ($resp->av_alt) {
			$u_small_path = 'u_small';
		} else {
			$u_small_path = 'useravatar';
		}
		$json['id'] = $resp->id;

		$level = get_mb_level($resp->id);

		if ($resp->groupid) {
			$limit = 4;


			$group = $db->get_row("SELECT `id`, `public`, `owner`  FROM `clans` WHERE `id` = '$resp->groupid'");
			if(!$group->public) {
				if(!$auth->ok) {
					continue;
				}
				if(
					$auth->id != $group->owner &&
					$auth->id != 1 &&
					!$db->get_var("SELECT count(*) FROM clans_members WHERE clan = '$group->id' AND user = '$auth->id' AND approve = '1'")) {
					continue;

				}
			}

		} else {
			$limit = 2;
		}

		$out = '<div class="mb-av"><a id="m' . $resp->id . '" href="' . mkurl('user', $resp->author, $resp->nick) . '"><img width="45" height="45" src="http://exs.lv/dati/bildes/' . $u_small_path . '/' . $resp->avatar . '" alt="" /></a>';

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
			$out .= '<a href="' . $resp->id . '" class="mb-reply-to mb-icon">Atbildēt</a>';
		}
		if ($auth->ok && isset($_GET['url'])) {
			$out .= '<div class="mb-rater">' . mb_rater($resp, htmlspecialchars(strip_tags($_GET['url']))) . '</div>';
		}
		$resp->date = strtotime($resp->date);
		$out .= '<p class="post-info"><a href="' . mkurl('user', $resp->author, $resp->nick) . '">' . usercolor($resp->nick, $resp->level, true, $resp->author) . '</a> ' . display_time_simple($resp->date);
		$out .= ' <a href="#m' . $resp->id . '" class="comment-permalink">#</a>';
		if ($auth->ok && (($auth->id == $resp->author && $auth->level == 3) || im_mod()) && $resp->date > time() - 600) {
			$out .= ' [<a href="/delete/' . $resp->id . '" class="confirm red">dzēst</a>]';
		}
		if (
				$auth->ok && $resp->date > time() - 3600 &&
				(im_mod() || ($auth->karma > 99 && $resp->author == $auth->id))) {
			$out .= ' [<a href="/edit/' . $resp->id . '">labot</a>]';
		}
		$out .= '</p><div class="post-content">' . add_smile($resp->text) . '</div>';
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
