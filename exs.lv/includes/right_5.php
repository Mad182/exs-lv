<?php

function get_mta_monitor($force = false) {
	global $m;
	if ($force || !($html = $m->get('mta_monitor'))) {
		$html = curl_get('http://mta.exs.lv/monitor/index.php');
		if(!$html) {
			$html = 'Offline';
		}
		$m->set('mta_monitor', $html, false, 45);
	}
	return $html;
}

$posts = get_latest_posts();
//$monitor = get_mta_monitor();
$tpl->newBlock('main-layout-right');
$tpl->assign(array(
	'latest-noscript' => $posts,
	//'mta-monitor' => $monitor
));

unset($posts);
//unset($monitor);

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
	$ingroup->av_alt = 1;
	$av = get_avatar($ingroup, 'l');
	$tpl->assign(array(
		'group-id' => $ingroup->id,
		'group-title' => $ingroup->title,
		'group-av' => $av
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

//player online chart
/*$chart_items = $db->get_results("SELECT `time`, `count` FROM `players_online` WHERE `game` = 'mta' AND `time` > '".date('Y-m-d H:i:s', strtotime('-1 day'))."' ORDER BY `time` ASC");

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
unset($items);*/
