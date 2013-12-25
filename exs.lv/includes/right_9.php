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

//include(CORE_PATH . '/modules/core/poll.php');

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


//  jaunāko izveidoto RuneScape grupu saraksts
if ($groups = get_latest_groups(true) ) {

	$tpl->newBlock('groups-l-list');
    
	foreach ($groups as $group) {
    
		$tpl->newBlock('groups-l-node');

		if(!empty($group->strid)) {
			$group->link = '/'.$group->strid;
		} else {
			$group->link = '/group/'.$group->id;
		}

		$tpl->assign(array(
			'title'     => $group->title,
			'link'      => $group->link,
            'avatar'    => $group->avatar
		));
	}
	unset($groups);
}


if ($auth->ok === true) {
	$tpl->assignGlobal('miniblog-add', '&nbsp;<a href="/say/' . $auth->id . '#content" class="mb-create" title="Pievienot jaunu ierakstu">Izveidot</a>');
}

