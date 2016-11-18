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

$json = [];

$vals = $db->get_results("SELECT
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
		`users`.`avatar` AS `avatar`,
		`users`.`level` AS `level`
	FROM
		`miniblog`, `users`
	WHERE
		`miniblog`.`parent` = '" . $mbid . "' AND
		`miniblog`.`id` > '" . $lastid . "' AND
		`miniblog`.`lang` = '$lang' AND
		`users`.`id` = `miniblog`.`author`
	ORDER BY `miniblog`.`id` ASC LIMIT 20");

if ($vals) {

	session_start();

	require('./includes/class.auth.php');

	//memcached konekcija
	$m = new Memcached;
	$m->addServer($mc_host, $mc_port);

	$site_access = get_site_access();

	$auth = new Auth();

	//"cake day"
	$cday_users = get_cakeday();

	if (!$auth->ok) {
		die('login required');
	}

	foreach ($vals as $val) {
		if ($val->avatar == '') {
			$val->avatar = 'none.png';
		}
		$json['id'] = $val->id;

		$level = get_mb_level($val->id);

		$limit = 3;
		if ($val->groupid) {
			$group = $db->get_row("SELECT `id`, `public`, `owner`  FROM `clans` WHERE `id` = '$val->groupid'");
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
		}
	
		$val->date = strtotime($val->date);
		$out = '<a id="m' . $val->id . '" href="/user/' . $val->author . '"><img width="40" height="40" class="av" src="/av/' . $val->avatar . '" alt="" /></a><div class="valonse-content">';
		if ($auth->ok && $level < $limit) {
			$out .= '<a href="' . $val->id . '" class="mb-reply-to mb-icon">Atbilde</a>';
		}
		if ($auth->ok && $auth->id != $val->author && isset($_GET['url'])) {
			$out .= '<div class="mb-rater">' . mb_rater($val) . '</div>';
		}
		$out .= '<p class="post-info"><a href="' . $val->author . '">' . usercolor($val->nick, $val->level, true, $val->author) . '</a> ' . display_time($val->date);


		//labot (ja ieraksts jau nav dzēsts)
		if ($val->mb_removed == 0 && !$intro && ($val->date > time() - 1800 || ($auth->level == 2 && $val->author == $auth->id && $val->date > time() - 86400) || $auth->level == 1 || $auth->id == 115) &&
				(im_mod() || (!$closed && $auth->karma >= $min_post_edit && $val->author == $auth->id))) {
			$out .= ' <a href="/edit/' . $val->id . '" class="post-button post-edit" title="Labot komentāru">labot</a>';
		}

		//dzēst (ja ieraksts jau nav dzēsts)
		if ($val->mb_removed == 0 && !$intro && $auth->ok === true && ( (!$closed && $auth->id == $val->author && $auth->level == 3 && $val->date > time() - 1800) || (im_mod() && $val->date > time() - 86400) )) {
			$out .= ' <a href="/delete/' . $val->id . '?token=' . make_token('delmb') . '" class="post-button post-delete delete-fast" title="Dzēst komentāru">dzēst</a>';
		}

		//moderatoriem - par šo minibloga ierakstu iedot brīdinājumu (saīsinam ceļu un tādējādi slinkumu)
		if ($val->mb_removed == 0 && $auth->ok && im_mod() && $auth->id != $val->author) {
			$out .= ' <a href="/warns/' . $val->author . '/commentid/' . $val->id . '" class="post-button post-warn" title="Brīdināt">brīdināt</a>';
		}


		$out .= '</p>';
		if ($val->mb_removed == 1) {
			$out .= '<p class="deleted-entry">Saturs dzēsts!</p>';
		} else {
			$out .= '<div class="post-content">' . add_smile($val->text) . '</div>';
		}

		$out .= '<ul class="valonses-' . $val->id . ' level-' . ($level + 1) . '"><li style="display:none"></li></ul><div class="c"></div><div class="reply-ph"></div>';
		$out .= '</div>';
		$json['comment'][$val->reply_to][] = $out;
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
