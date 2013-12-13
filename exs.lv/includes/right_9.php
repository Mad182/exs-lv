<?php

$tpl->newBlock('main-layout-right');

//profile box
if (isset($category) && $category->isblog != 0 && empty($inprofile)) {
	$inprofile = get_user($category->isblog);
}

if (!empty($inprofile)) {

	$avatar = get_avatar($inprofile, 'l');

	$tpl->newBlock('profile-box');
	$tpl->assignGlobal(array(
		'url' => '/user/' . $inprofile->id,
		'profile-nick' => htmlspecialchars($inprofile->nick),
		'profile-slug' => mkslug($inprofile->nick),
		'profile-id' => $inprofile->id,
		'avatar' => $avatar,
		'profile-top-awards' => get_top_awards($inprofile->id)
	));

	if (!empty($inprofile->custom_title)) {
		$tpl->assign(array(
			'custom_title' => ' <span style="font-size:11px">(' . $inprofile->custom_title . ')</span>'
		));
	}

	if ($auth->ok === true && $auth->id != $inprofile->id) {
		$tpl->newBlock('profilebox-pm-link');
	}

	//warnu links un skaits
	if ($auth->ok === true
			&& ($auth->id == $inprofile->id
			|| im_mod())
			&& !in_array($inprofile->level, array(1, 2))) {
		$tpl->newBlock('profilebox-warn');
		if ($inprofile->warn_count > 0) {
			$tpl->assign(array(
				'profile-warns' => '&nbsp;(' . $inprofile->warn_count . ')',
				'class' => ' class="active"'
			));
		}
	}

	if (!empty($inprofile->twitter)) {
		$tpl->newBlock('profilebox-twitter-link');
		$tpl->assign(array(
			'twitter' => $inprofile->twitter
		));
	}

	if (!empty($inprofile->yt_name)) {
		$tpl->newBlock('profilebox-yt-link');
		$tpl->assign(array(
			'yt-name' => $inprofile->yt_name,
			'yt-slug' => mkslug($inprofile->yt_name)
		));
	}
}

include(CORE_PATH . '/modules/core/poll.php');

$tpl->newBlock('friendssay-box');
$sel = 'all';
if ($auth->ok && !empty($_COOKIE['last-mbs-tab']) && $_COOKIE['last-mbs-tab'] == 'friends') {
	$mbs = get_latest_mbs(1);
	$sel = 'friends';
} else {
	$mbs = get_latest_mbs();
}

$tpl->assign('out', $mbs);

if($auth->ok) {
	$tpl->newBlock('friendssay-tabs');
	$tpl->assign(array(
		$sel . '-selected' => 'active '
	));
}

if ($auth->ok === true) {
	$tpl->assignGlobal('miniblog-add', '&nbsp;<a href="/say/' . $auth->id . '#content" class="mb-create" title="Pievienot jaunu ierakstu">Izveidot</a>');
}

$top_players = $db->get_results("
	SELECT
		DISTINCT(`lol_tracking`.`player_id`) as `player_id`,
		`lol_players`.`lol_nick` as `lol_nick`,
		`lol_players`.`server` as `server`,
		`lol_tracking`.`lks` as `lks`
	FROM
		`lol_players`,
		`lol_tracking`
	WHERE
		`lol_players`.`id` = `lol_tracking`.`player_id` AND
		`lol_tracking`.`date` = (SELECT MAX(`date`) FROM `lol_tracking`)
	ORDER BY
		`lol_tracking`.`lks` DESC
	LIMIT 10
");

if(!empty($top_players)) {
	$tpl->newBlock('lol-top');
	foreach($top_players as $plr) {
		$tpl->newBlock('lol-top-node');
		$tpl->assignAll($plr);
	}
}
