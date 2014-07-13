<?php

/**
 * exs.lv kreisais sidebar
 */
$sel = 'pages';
if (!empty($_COOKIE['last-sidebar-tab']) && $_COOKIE['last-sidebar-tab'] == 'gallery') {
	$out = get_latest_images();
	$sel = 'gallery';
} else {
	$out = get_latest_posts();
}

$fsel = 'fact-all';
if (!empty($_COOKIE['last-facts-tab']) && $_COOKIE['last-facts-tab'] == 'fact-rs') {
	$fact = $db->get_var("SELECT `text` FROM `facts_rs` LIMIT " . rand(0, 172) . ",1") . ' <a href="/fact/rs" class="moar">Citu &raquo;</a>';
	$fsel = 'fact-rs';
} else {
	$fact = $db->get_var("SELECT `text` FROM `facts` LIMIT " . rand(0, 44) . ",1") . ' <a href="/fact" class="moar">Citu &raquo;</a>';
}

function get_csgo_monitor($force = false) {
	global $m;
	if ($force || !($html = $m->get('csgo_monitor'))) {
		$html = curl_get('http://csgo.exs.lv/monitor/index.php');
		if(!$html) {
			$html = 'Offline';
		}
		$m->set('csgo_monitor', $html, false, 45);
	}
	return $html;
}

$monitor = get_csgo_monitor();

$tpl->newBlock('main-layout-left');
$tpl->assign(array(
	'latest-noscript' => $out,
	'random-fact' => $fact,
	'csgo-monitor' => $monitor,
	$sel . '-selected' => 'active ',
	$fsel . '-selected' => 'active '
));
unset($out);
unset($monitor);

//izvēlne
$parent_id = get_top($category->id);
if ($parent_id != 0) {
	$menuitems = $db->get_results("SELECT `id`,`title`,`textid`,`parent` FROM `cat` WHERE `parent` = '" . $parent_id . "' AND `parent` != '110' AND `parent` != '101' AND `mods_only` = '0' ORDER BY `title` ASC");

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

//top users
$tusers = $db->get_results("SELECT `id`,`nick`,`today`,`level`,`av_alt`,`avatar` FROM `users` WHERE `today` > 0 ORDER BY `today` DESC LIMIT 9");
if ($tusers) {
	$tpl->newBlock('user-top');
	foreach ($tusers as $tuser) {
		$tpl->newBlock('user-top-node');
		$tpl->assign(array(
			'user' => usercolor($tuser->nick, $tuser->level, false, $tuser->id),
			'url' => '/user/' . $tuser->id,
			'today' => $tuser->today,
			'avatar' => get_avatar($tuser, 's')
		));
	}
}
unset($tusers);

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

//lietotāja notifikācijas
if ($auth->ok === true) {
	if ($html = get_notify($auth->id)) {
		$tpl->newBlock('notification-list');
		$tpl->assign('out', $html);
		unset($html);
	}
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

//player online chart
$chart_items = $db->get_results("SELECT `time`, `count` FROM `players_online` WHERE `game` = 'csgo' AND `time` > '".date('Y-m-d H:i:s', strtotime('-1 day'))."' ORDER BY `time` ASC");

if($chart_items) {
	$tpl->newBlock('google-chart');

	$items = array();
	$last = null;
	foreach($chart_items as $item) {

		if($last === null) {
			$last = $item->count;
		} else {
			$items[] = "['".substr($item->time,0,16)."', ".ceil(($item->count+$last)/2)."]";
			$last = null;
		}

	}

	$tpl->assign('chart-items', implode(',', $items));
}

unset($chart_items);
unset($items);
