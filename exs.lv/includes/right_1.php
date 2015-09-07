<?php

/**
 * exs.lv labais sidebar
 */

//profile box
if (isset($category) && $category->isblog != 0 && empty($inprofile)) {
	$inprofile = get_user($category->isblog);
}

if (!empty($inprofile) && !$inprofile->deleted && ($auth->ok === true || !$inprofile->private)) {
	$avatar = get_avatar($inprofile, 'l');

	$tpl->newBlock('profile-box');
	$tpl->assignGlobal(array(
		'url' => '/user/' . $inprofile->id,
		'profile-nick' => h($inprofile->nick),
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

	//avatara maiņas links, ja nav avatara
	if ($auth->id === $inprofile->id && empty($auth->avatar)) {
		$tpl->newBlock('profilebox-updateavatar');
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

	if (!empty($inprofile->lastfm_username)) {
		$tpl->newBlock('profilebox-lastfm-link');
		$tpl->assign(array(
			'name' => $inprofile->lastfm_username
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
	}
}

/*$wallpaper = $db->get_var("SELECT `image` FROM `wallpapers` WHERE `date` <= '" . date('Y-m-d') . "' ORDER BY `date` DESC LIMIT 1");
if ($wallpaper) {
	$tpl->newBlock('daily-wallpaper');
	$tpl->assignGlobal('wallpaper-image', $wallpaper);
	unset($wallpaper);
}*/

//jaunākās junk bildes
$junks = $db->get_results("SELECT `id`, `thb`, `title`, `posts` FROM `junk` WHERE `removed` = 0 ORDER BY `bump` DESC LIMIT 3");
if ($junks) {
	$tpl->newBlock('side-junk');
	foreach ($junks as $junk) {
		$tpl->newBlock('side-junk-node');
		$tpl->assign(array(
			'id' => $junk->id,
			'thb' => $junk->thb,
			'title' => h($junk->title),
			'posts' => $junk->posts
		));
	}
	unset($junks);
}

$tpl->newBlock('mb-box');
$sel = 'all';
if (!empty($_COOKIE['last-mbs-tab']) && $_COOKIE['last-mbs-tab'] === 'friends') {
	$mbs = get_latest_mbs('friends');
	$sel = 'friends';
} elseif (!empty($_COOKIE['last-mbs-tab']) && $_COOKIE['last-mbs-tab'] === 'music') {
	$mbs = get_latest_mbs('music');
	$sel = 'music';
} else {
	$mbs = get_latest_mbs('all');
}

$tpl->assign('out', $mbs);

if ($auth->ok) {
	$tpl->newBlock('mb-tabs');
}
$tpl->assignGlobal(array(
	$sel . '-selected' => 'active '
));

//dienas labākā komentāra bloks
$best = get_todays_top_comment();
if (!empty($best)) {
	$tpl->newBlock('daily-best');
	$tpl->assign($best);
}

//neapstiprināto junk bilžu skaits modiem
if (im_mod()) {
	$newimgs = $db->get_var("SELECT count(*) FROM `junk_queue` WHERE `approved` = 0");;
	$iappstr = '';
	if ($newimgs) {
		$iappstr = '&nbsp;(<strong class="r">' . $newimgs . '</strong>)';
	}
	$tpl->newBlock('junk-info');
	$tpl->assign(array(
		'count' => $iappstr
	));
}

if ($auth->ok === true) {
	$tpl->assignGlobal('miniblog-add', '&nbsp;<a href="/say/' . $auth->id . '#content" class="mb-create" title="Pievienot jaunu ierakstu">Izveidot</a>');
}

/**
 * exs.lv kreisais sidebar
 */
$sel = 'pages';
if($auth->ok === true && empty($_COOKIE['last-sidebar-tab']) || !empty($_COOKIE['last-sidebar-tab']) && $_COOKIE['last-sidebar-tab'] == 'events') {
	$out = get_notify($auth->id);
	$sel = 'events';
} elseif (!empty($_COOKIE['last-sidebar-tab']) && $_COOKIE['last-sidebar-tab'] == 'gallery') {
	$out = get_latest_images();
	$sel = 'gallery';
} else {
	$out = get_latest_posts();
}

//lietotāja notifikācijas
if ($auth->ok === true) {
	$tpl->newBlock('notification-list');
}

$tpl->assignGlobal(array(
	'latest-noscript' => $out,

	//'csgo-monitor' => get_game_monitor('http://csgo.exs.lv/monitor/index.php'),
	/*
	<h3><strong>CS:GO</strong> csgo.exs.lv</h3>
	<div class="box">
		{csgo-monitor}
	</div>
	*/

	'user-top' => user_top(),
	$sel . '-selected' => 'active '
));
unset($out);

//izvēlne
$parent_id = get_top($category->id);
if ($parent_id != 0) {
	$menuitems = $db->get_results("SELECT `id`,`title`,`textid`,`parent` FROM `cat` WHERE `parent` = '" . $parent_id . "' AND `parent` != '110' AND `parent` != '101' AND `parent` != '319' AND `mods_only` = '0' ORDER BY `title` ASC");

	if ($menuitems) {
		$tpl->newBlock('menu-list');
		$tpl->assign(array(
			'topid' => $parent_id,
			'title' => get_cat($parent_id)->title
		));
		foreach ($menuitems as $menuitem) {
			$tpl->newBlock('menu-node');
			$sel = '';
			if (!empty($category)) {
				if ($category->id == $menuitem->id || $category->parent == $menuitem->id) {
					$sel = ' class="active"';
				}
			}
			$tpl->assign(array(
				'title' => $menuitem->title,
				'url' => '/' . $menuitem->textid,
				'sel' => $sel,
				'id' => $menuitem->id,
			));

			if (in_array($menuitem->id, array(79)) && !empty($sel)) {
				$children = $db->get_results("SELECT `textid`,`id`,`title` FROM `cat` WHERE `parent` = '$menuitem->id' ORDER BY `id` ASC");
				if ($children) {
					$tpl->newBlock('menu-list-sub');
					foreach ($children as $child) {
						if ($category->id == $child->id) {
							$sel = ' class="active"';
						} else {
							$sel = '';
						}
						$tpl->newBlock('menu-node-sub');
						$tpl->assign(array(
							'title' => $child->title,
							'url' => '/' . $child->textid,
							'sel' => $sel
						));
					}
				}
			}
		}
	}
}

//grupas
if ($groups = get_latest_groups()) {
	$tpl->newBlock('groups-l-list');
	foreach ($groups as $group) {
		$tpl->newBlock('groups-l-node');

		if (!empty($group->strid)) {
			$group->link = '/' . $group->strid;
		} else {
			$group->link = '/group/' . $group->id;
		}

		$tpl->assign(array(
			'title' => $group->title,
			'link' => $group->link
		));
	}
	unset($groups);
}

//filmu meklētājs
if ($category->module == 'movies') {
	$tpl->newBlock('movie-search');

	if (isset($_GET['genre'])) {
		$_GET['genres'] = array($_GET['genre']);

		if (translate_genres($_GET['genre']) != $_GET['genre']) {
			$page_title = translate_genres($_GET['genre']);
		}
	} elseif (isset($_GET['genres']) && count($_GET['genres']) == 1) {
		if (translate_genres($_GET['genres'][0]) != $_GET['genres'][0]) {
			redirect('/filmas/search/?genre=' . $_GET['genres'][0]);
		}
	}

	$genres = $db->get_col("SELECT DISTINCT(`genre`) FROM `movie_genres` ORDER BY `genre` ASC");
	foreach ($genres as $genre) {
		$tpl->newBlock('genre-node');
		$tpl->assign(array(
			'genre' => $genre,
			'translated' => translate_genres($genre)
		));
		if (!empty($_GET['genres']) && in_array($genre, $_GET['genres'])) {
			$tpl->assign('checked', ' checked="checked"');
		}
	}
}


//include(CORE_PATH . '/modules/core/poll.php');

