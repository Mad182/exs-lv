<?php

/**
 * Blogu ierakstu statistika
 */
$blogs = $db->get_results("SELECT * FROM cat WHERE isblog != '0'");

foreach ($blogs as $blog) {
	update_stats($blog->id);
}

$ord = 'stat_topics';
if (isset($_GET['order'])) {
	if ($_GET['order'] == 'views') {
		$ord = 'stat_views';
	} elseif ($_GET['order'] == 'comments') {
		$ord = 'stat_com';
	}
}

$blogs = $db->get_results("SELECT * FROM cat WHERE isblog != '0' ORDER BY " . $ord . " DESC");
foreach ($blogs as $blog) {
	$tpl->newBlock('bs-list-node');
	if ($blog->newlink) {
		$url = '/' . $blog->textid;
	} else {
		$url = '/?c=' . $blog->id;
	}
	$tpl->assign(array(
		'url' => $url,
		'title' => $blog->title,
		'p_count' => $blog->stat_topics,
		'c_count' => $blog->stat_com,
		'w_count' => $blog->stat_views
	));
}
