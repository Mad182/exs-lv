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

	$isblog = get_blog_by_user($inprofile->id);
	if ($isblog) {
		$blog = get_cat($isblog);
		$count = $db->get_var("SELECT count(*) FROM `pages` WHERE `category` = '" . $isblog . "'");
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

$wallpaper = $db->get_var("SELECT image FROM wallpapers WHERE date <= '" . date('Y-m-d') . "' ORDER BY date DESC LIMIT 1");
if ($wallpaper) {
	$tpl->newBlock('daily-wallpaper');
	$tpl->assignGlobal('wallpaper-image', $wallpaper);
}

$bumps = $db->get_results("SELECT `id`, `thb`, `title`, `posts` FROM `junk` WHERE `removed` = 0 ORDER BY `bump` DESC LIMIT 6");
if ($bumps) {
	$tpl->newBlock('side-junk');
	foreach ($bumps as $junk) {
		$tpl->newBlock('side-junk-node');
		$tpl->assign(array(
			'id' => $junk->id,
			'thb' => $junk->thb,
			'title' => $junk->title,
			'posts' => $junk->posts
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

//random tagi
/*$cloud = rand(0, 200);
$cache_created = filemtime(CORE_PATH . '/cache/tags/' . $cloud . '.html');
if (!$cache_created or (time() - $cache_created) > 432000) {
	$tags = $db->get_results("SELECT * FROM `tags` ORDER BY rand() LIMIT 20");
	if ($tags) {
		$out = '';
		foreach ($tags as $tag) {
			$count = $db->get_var("SELECT count(*) FROM `taged` WHERE `tag_id` = '$tag->id'");
			if (!$count) {
				$db->query("DELETE FROM `tags` WHERE `id` = '$tag->id' LIMIT 1");
			}
			$size = (7 + ceil(log($count + 1) * 3.6));
			if ($size > 28) {
				$size = 28;
			}
			$out .= '<a style="font-size:' . $size . 'px" href="/tag/' . $tag->slug . '">' . htmlspecialchars($tag->name) . '</a> ';
		}
	}
	$handle = fopen(CORE_PATH . '/cache/tags/' . $cloud . '.html', 'wb');
	fwrite($handle, $out);
	fclose($handle);
} else {
	$out = file_get_contents(CORE_PATH . '/cache/tags/' . $cloud . '.html');
}

$tpl->newBlock('tags-list-side');
$tpl->assign('out', $out);
*/

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

if(date('Y-m-d') == '2013-04-05' || date('Y-m-d') == '2013-04-06') {
	$tpl->newBlock('football');
}
