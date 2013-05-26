<?php

$out = get_latest_posts();
$tpl->newBlock('main-layout-right');
$tpl->assign(array(
	'latest-noscript' => $out,
));
unset($out);

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
} elseif (!empty($ingroup)) {

	$tpl->newBlock('group-box');
	if ($ingroup->avatar) {
		$av = $ingroup->avatar;
	} else {
		$av = 'none.png';
	}
	$tpl->assign(array(
		'group-id' => $ingroup->id,
		'group-title' => $ingroup->title,
		'group-av' => $av,
		'av-path' => 'u_large',
	));
}

include(CORE_PATH . '/modules/core/poll.php');

$tpl->newBlock('friendssay-box');
$tpl->assign('out', get_latest_mbs());

//lietotāja notifikācijas
if ($auth->ok === true) {
	if ($html = get_notify($auth->id)) {
		$tpl->newBlock('notification-list');
		$tpl->assign('out', $html);
		unset($html);
	}

	$tpl->assignGlobal('miniblog-add', '&nbsp;<a href="/say/' . $auth->id . '#content" class="mb-create" title="Pievienot jaunu ierakstu">Izveidot</a>');
}
