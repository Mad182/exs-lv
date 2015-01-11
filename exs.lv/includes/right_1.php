<?php

/**
 * exs.lv labais sidebar
 */
$tpl->newBlock('main-layout-right');

//profile box
if (isset($category) && $category->isblog != 0 && empty($inprofile)) {
	$inprofile = get_user($category->isblog);
}

if (!empty($inprofile) && !$inprofile->deleted) {

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
			'custom_title' => ' <small>(' . $inprofile->custom_title . ')</small>'
		));
	}

	//pm links
	if ($auth->ok === true && $auth->id != $inprofile->id) {
		$tpl->newBlock('profilebox-pm-link');
	}

	//warnu links un skaits
	if ($auth->ok === true && ($auth->id == $inprofile->id || im_mod()) && !in_array($inprofile->level, array(1))) {
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

	$isblog = get_blog_by_user($inprofile->id);
	if ($isblog) {
		$blog = get_cat($isblog);
		$count = $db->get_var("SELECT count(*) FROM `pages` WHERE `category` = '" . $isblog . "' AND `lang` = " . intval($lang));
		$tpl->newBlock('profilebox-blog-link');
		$tpl->assign(array(
			'url' => '/' . $blog->textid,
			'count' => $count
		));

		$tpl->newBlock('blog-latest-list');
		$tpl->assign('html', get_blog_latest($isblog));
	}
} elseif (!empty($ingroup)) {
	$tpl->newBlock('group-box');
	$ingroup->av_alt = 1;
	$av = get_avatar($ingroup, 'l');
	$tpl->assign(array(
		'group-id' => $ingroup->id,
		'group-title' => $ingroup->title,
		'group-av' => $av
	));
}

$wallpaper = $db->get_var("SELECT `image` FROM `wallpapers` WHERE `date` <= '" . date('Y-m-d') . "' ORDER BY `date` DESC LIMIT 1");
if ($wallpaper) {
	$tpl->newBlock('daily-wallpaper');
	$tpl->assignGlobal('wallpaper-image', $wallpaper);
	unset($wallpaper);
}

//jaunākās junk bildes
$junks = $db->get_results("SELECT `id`, `thb`, `title`, `posts` FROM `junk` WHERE `removed` = 0 ORDER BY `bump` DESC LIMIT 4");
if ($junks) {
	$tpl->newBlock('side-junk');
	foreach ($junks as $junk) {
		$tpl->newBlock('side-junk-node');
		$tpl->assign(array(
			'id' => $junk->id,
			'thb' => $junk->thb,
			'title' => htmlspecialchars($junk->title),
			'posts' => $junk->posts
		));
	}
	unset($junks);
}

include(CORE_PATH . '/modules/core/poll.php');

$tpl->newBlock('friendssay-box');
$sel = 'all';
if ($auth->ok && !empty($_COOKIE['last-mbs-tab']) && $_COOKIE['last-mbs-tab'] === 'friends') {
	$mbs = get_latest_mbs('friends');
	$sel = 'friends';
} elseif ($auth->ok && !empty($_COOKIE['last-mbs-tab']) && $_COOKIE['last-mbs-tab'] === 'music') {
	$mbs = get_latest_mbs('music');
	$sel = 'music';
} else {
	$mbs = get_latest_mbs('all');
}

$tpl->assign('out', $mbs);

if ($auth->ok) {
	$tpl->newBlock('friendssay-tabs');
	$tpl->assignGlobal(array(
		$sel . '-selected' => 'active '
	));
}

if (im_mod()) {
	$newimgs = $db->get_var("SELECT count(*) FROM `junk_queue` WHERE `approved` = 0");
	$iappstr = '';
	if ($newimgs) {
		$iappstr = '&nbsp;(<span class="r">' . $newimgs . '</span>)';
	}
	$tpl->newBlock('junk-info');
	$tpl->assign(array(
		'count' => $iappstr
	));
}

if ($auth->ok === true) {
	$tpl->assignGlobal('miniblog-add', '&nbsp;<a href="/say/' . $auth->id . '#content" class="mb-create" title="Pievienot jaunu ierakstu">Izveidot</a>');
}

if ($auth->skin == 1) {
	$tpl->assignGlobal('twitter-theme', ' data-theme="dark"');
}

