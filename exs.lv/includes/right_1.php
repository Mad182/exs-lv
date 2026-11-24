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
	$tpl->assignGlobal([
		'url' => '/user/' . $inprofile->id,
		'profile-nick' => h($inprofile->nick),
		'profile-slug' => mkslug($inprofile->nick),
		'profile-id' => $inprofile->id,
		'avatar' => $avatar,
		'profile-top-awards' => get_top_awards($inprofile->id)
	]);

	if (!empty($inprofile->custom_title)) {
		$tpl->assign([
			'custom_title' => ' <small>(' . $inprofile->custom_title . ')</small>'
		]);
	}

	if($auth->ok === true) {

		//avatara maiņas links, ja nav avatara
		if ($auth->id === $inprofile->id && empty($auth->avatar)) {
			$tpl->newBlock('profilebox-updateavatar');
		}

		//pm links
		if ($auth->id != $inprofile->id) {
			$tpl->newBlock('profilebox-pm-link');
		}

		//warn links
		if (($auth->id == $inprofile->id || im_mod()) && !in_array($inprofile->level, [1])) {
			$tpl->newBlock('profilebox-warn');
			if ($inprofile->warn_count > 0) {
				$tpl->assign([
					'profile-warns' => '&nbsp;(' . $inprofile->warn_count . ')',
					'class' => ' class="active"'
				]);
			}
		}

		//twitter, youtube, lastfm linki
		if (!empty($inprofile->twitter)) {
			$tpl->newBlock('profilebox-twitter-link');
			$tpl->assign([
				'twitter' => $inprofile->twitter
			]);
		}

		if (!empty($inprofile->yt_name)) {
			$tpl->newBlock('profilebox-yt-link');
			$tpl->assign([
				'yt-name' => $inprofile->yt_name,
				'yt-slug' => mkslug($inprofile->yt_name)
			]);
		}
	}

	$isblog = get_blog_by_user($inprofile->id);
	if ($isblog) {
		$blog = get_cat($isblog);
		$count = $db->get_var("SELECT count(*) FROM `pages` WHERE `category` = '" . $isblog . "' AND `lang` = " . intval($lang));
		if($count > 0) {
			$tpl->newBlock('profilebox-blog-link');
			$tpl->assign([
				'url' => '/' . $blog->textid,
				'count' => $count
			]);
		}
	}
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
$tpl->assignGlobal([
	$sel . '-selected' => 'active '
]);

//dienas labākā komentāra bloks
$best = get_todays_top_comment();
if (!empty($best)) {
	$tpl->newBlock('daily-best');
	$tpl->assign($best);
}

if ($auth->ok === true) {
	$tpl->assignGlobal('miniblog-add', '&nbsp;<a href="/say/' . $auth->id . '#content" class="mb-create" title="Pievienot jaunu ierakstu">Izveidot</a>');
}

/**
 * exs.lv kreisais sidebar
 */
$sel = 'pages';
if (!empty($_COOKIE['last-sidebar-tab']) && $_COOKIE['last-sidebar-tab'] === 'gallery') {
	$out = get_latest_images();
	$sel = 'gallery';
} else {
	$out = get_latest_posts();
}

//lietotāja notifikācijas
if ($auth->ok === true) {
	$tpl->newBlock('notification-list');
	$tpl->assign('html', get_notify($auth->id));
}

$tpl->assignGlobal([
	'latest-noscript' => $out,
	'user-top' => user_top(),
	$sel . '-selected' => 'active '
]);
unset($out);

//izvēlne
$parent_id = get_top($category->id);
if ($parent_id != 0) {
	if (($menuitems = $m->get('cat_submenu_' . $parent_id)) === false) {
		$menuitems = $db->get_results("SELECT `id`,`title`,`textid`,`parent` FROM `cat` WHERE `parent` = '" . $parent_id . "' AND `parent` != '110' AND `parent` != '101' AND `parent` != '319' AND `mods_only` = '0' ORDER BY `title` ASC");
		$m->set('cat_submenu_' . $parent_id, $menuitems, 21600);
	}

	if ($menuitems) {
		$tpl->newBlock('menu-list');
		$tpl->assign([
			'topid' => $parent_id,
			'title' => get_cat($parent_id)->title
		]);

		$menuitem_ids = array_map(function($item) { return (int) $item->id; }, $menuitems);
		$subchildren_by_parent = [];
		if (!empty($menuitem_ids)) {
			$sub_cache_key = 'cat_subchildren_' . implode('_', $menuitem_ids);
			if (($all_subchildren = $m->get($sub_cache_key)) === false) {
				$all_subchildren = $db->get_results("SELECT `textid`,`id`,`title`,`parent` FROM `cat` WHERE `parent` IN (" . implode(',', $menuitem_ids) . ") AND `status` = 'active' AND `mods_only` = '0' ORDER BY `id` ASC");
				$m->set($sub_cache_key, $all_subchildren ?: [], 21600);
			}
			if (!empty($all_subchildren)) {
				foreach ($all_subchildren as $sc) {
					$subchildren_by_parent[$sc->parent][] = $sc;
				}
			}
		}

		foreach ($menuitems as $menuitem) {
			$tpl->newBlock('menu-node');
			$is_active = false;
			if (!empty($category)) {
				if ($category->id == $menuitem->id || $category->parent == $menuitem->id) {
					$is_active = true;
				} elseif (!empty($category->parent)) {
					$cat_parent = get_cat($category->parent);
					if ($cat_parent && $cat_parent->parent == $menuitem->id) {
						$is_active = true;
					}
				}
			}
			$sel = $is_active ? ' class="selected active"' : '';
			$tpl->assign([
				'title' => $menuitem->title,
				'url' => '/' . $menuitem->textid,
				'sel' => $sel,
				'id' => $menuitem->id,
			]);

			if (!empty($subchildren_by_parent[$menuitem->id])) {
				$tpl->newBlock('menu-list-sub');
				foreach ($subchildren_by_parent[$menuitem->id] as $child) {
					$sub_sel = '';
					if (!empty($category)) {
						if ($category->id == $child->id || $category->parent == $child->id) {
							$sub_sel = ' class="selected active"';
						}
					}
					$tpl->newBlock('menu-node-sub');
					$tpl->assign([
						'title' => $child->title,
						'url' => '/' . $child->textid,
						'sel' => $sub_sel
					]);
				}
			}
		}
	}
}

//grupas
if($auth->ok === true) {
	if ($groups = get_latest_groups()) {
		$tpl->newBlock('groups-l-list');
		foreach ($groups as $group) {
			$tpl->newBlock('groups-l-node');

			if (!empty($group->strid)) {
				$group->link = '/' . $group->strid;
			} else {
				$group->link = '/group/' . $group->id;
			}

			$tpl->assign([
				'title' => $group->title,
				'link' => $group->link
			]);
		}
		unset($groups);
	}
}

//filmu meklētājs
if ($category->module === 'movies') {
	$tpl->newBlock('movie-search');

	if (isset($_GET['genre'])) {
		$_GET['genres'] = [$_GET['genre']];

		if (translate_genres($_GET['genre']) != $_GET['genre']) {
			$page_title = h(translate_genres($_GET['genre']));
		}
	} elseif (isset($_GET['genres']) && count($_GET['genres']) == 1) {
		if (translate_genres($_GET['genres'][0]) != $_GET['genres'][0]) {
			redirect('/filmas/search/?genre=' . $_GET['genres'][0]);
		}
	}

	$genres = $db->get_col("SELECT DISTINCT(`genre`) FROM `movie_genres` ORDER BY `genre` ASC");
	foreach ($genres as $genre) {
		$tpl->newBlock('genre-node');
		$tpl->assign([
			'genre' => $genre,
			'translated' => translate_genres($genre)
		]);
		if (!empty($_GET['genres']) && in_array($genre, $_GET['genres'])) {
			$tpl->assign('checked', ' checked="checked"');
		}
	}
}

// UT99 server monitor (cached 60s in Memcached)
if (($ut99_mon = $m->get('ut99_sidebar_monitor_v1')) === false) {
	$ut99_mon = [
		'online' => false,
		'map' => 'DM-Oblivion',
		'numplayers' => 0,
		'maxplayers' => 16,
		'players' => []
	];
	$fp = @fsockopen('udp://127.0.0.1', 7778, $errno, $errstr, 0.2);
	if ($fp) {
		fwrite($fp, "\\status\\");
		stream_set_timeout($fp, 0, 150000);
		$raw = '';
		for ($pkt = 0; $pkt < 6; $pkt++) {
			$chunk = fread($fp, 4096);
			if ($chunk === false || $chunk === '') {
				break;
			}
			$raw .= $chunk;
			if (strpos($raw, '\\final\\') !== false) {
				break;
			}
		}
		fclose($fp);

		if (!empty($raw)) {
			$ut99_mon['online'] = true;
			$parts = explode('\\', ltrim($raw, '\\'));
			$kv = [];
			for ($i = 0; $i + 1 < count($parts); $i += 2) {
				$kv[strtolower($parts[$i])] = $parts[$i + 1];
			}
			if (!empty($kv['mapname'])) {
				$ut99_mon['map'] = $kv['mapname'];
			}
			if (isset($kv['numplayers'])) {
				$ut99_mon['numplayers'] = (int) $kv['numplayers'];
			}
			if (isset($kv['maxplayers'])) {
				$ut99_mon['maxplayers'] = (int) $kv['maxplayers'];
			}
			$players_map = [];
			foreach ($kv as $k => $v) {
				if (preg_match('/^(player|frags|ping)_(\d+)$/', $k, $pm)) {
					$idx = (int) $pm[2];
					$players_map[$idx][$pm[1]] = $v;
				}
			}
			$plist = [];
			foreach ($players_map as $p) {
				if (!isset($p['player']) || trim($p['player']) === '') {
					continue;
				}
				$plist[] = [
					'name' => $p['player'],
					'frags' => isset($p['frags']) ? (int) $p['frags'] : 0,
					'ping' => isset($p['ping']) ? trim($p['ping']) : '0'
				];
			}
			usort($plist, function ($a, $b) {
				return $b['frags'] <=> $a['frags'];
			});
			$ut99_mon['players'] = $plist;
		}
	}
	$m->set('ut99_sidebar_monitor_v1', $ut99_mon, 60);
}

$tpl->newBlock('ut99-server-monitor');
$tpl->assign([
	'ut99-mon-status-class' => $ut99_mon['online'] ? 'online' : 'offline',
	'ut99-mon-status-text' => $ut99_mon['online'] ? 'Tiešsaistē' : 'Bezsaistē',
	'ut99-mon-players' => (int) $ut99_mon['numplayers'],
	'ut99-mon-maxplayers' => (int) $ut99_mon['maxplayers'],
	'ut99-mon-map' => h($ut99_mon['map'])
]);
$ut99_map_file = strtolower(basename($ut99_mon['map'])) . '.jpg';
if (file_exists(CORE_PATH . '/bildes/ut/' . $ut99_map_file)) {
	$tpl->newBlock('ut99-mon-mapimg');
	$tpl->assign([
		'ut99-mon-mapimg' => '/bildes/ut/' . rawurlencode($ut99_map_file),
		'ut99-mon-map' => h($ut99_mon['map'])
	]);
}
if (!empty($ut99_mon['players'])) {
	$tpl->newBlock('ut99-mon-players-table');
	foreach ($ut99_mon['players'] as $p) {
		$tpl->newBlock('ut99-mon-player-row');
		$tpl->assign([
			'pname' => h($p['name']),
			'pfrags' => (int) $p['frags'],
			'pping' => h($p['ping']) . ' ms'
		]);
	}
} else {
	$tpl->newBlock('ut99-mon-empty');
}

include(CORE_PATH . '/modules/core/poll.php');

