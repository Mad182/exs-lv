<?php

if (!isset($_GET['mbid']) || !isset($_GET['lastid'])) {
	die('err');
}

require('../exs.lv/configdb.php');
require(CORE_PATH . '/includes/class.mdb.php');
require(CORE_PATH . '/includes/functions.core.php');

/* nosaka, kuru lapu rādīt (exs.lv, coding.lv, etc) */
require('includes/site_loader.php');

$db = new mdb($username, $password, $database, $hostname);
unset($password);

$mbid = (int) $_GET['mbid'];
$lastid = (int) $_GET['lastid'];
$lastedit = (int) $_GET['et'];

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
		`users`.`avatar` AS `avatar`,
		`users`.`level` AS `level`
	FROM
		`miniblog`, `users`
	WHERE
		`miniblog`.`parent` = '" . $mbid . "' AND
		`miniblog`.`id` > '" . $lastid . "' AND
		`miniblog`.`lang` = '$lang' AND
		`miniblog`.`removed` = '0' AND
		`users`.`id` = `miniblog`.`author`
	ORDER BY `miniblog`.`id` ASC LIMIT 20");

if ($resps) {

	session_start();

	require(CORE_PATH . '/includes/class.auth.php');

	//memcached konekcija
	$m = new Memcache;
	$m->connect($mc_host, $mc_port);
	$auth = new Auth();

	if(!$auth->ok) {
		die('login required');
	}

	foreach ($resps as $resp) {
		if ($resp->avatar == '') {
			$resp->avatar = 'none.png';
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

		$out = '<a id="m' . $resp->id . '" href="' . mkurl('user', $resp->author, $resp->nick) . '"><img width="40" height="40" class="av" src="/av/' . $resp->avatar . '" alt="" /></a><div class="response-content">';
		if ($auth->ok && $level < $limit) {
			$out .= '<a href="' . $resp->id . '" class="mb-reply-to mb-icon">Atbildēt</a>';
		}
		$resp->date = strtotime($resp->date);
		$out .= '<p class="post-info"><a href="' . mkurl('user', $resp->author, $resp->nick) . '">' . usercolor($resp->nick, $resp->level, true, $resp->author) . '</a> ' . display_time_simple($resp->date);
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
?>
