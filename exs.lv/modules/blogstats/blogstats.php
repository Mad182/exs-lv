<?php

/**
 * Blogu ierakstu statistika
 */
 
global $db, $m, $tpl;

//kārtošana
$ord = 'stat_topics';
if (isset($_GET['order'])) {
	if ($_GET['order'] == 'views') {
		$ord = 'stat_views';
	} elseif ($_GET['order'] == 'comments') {
		$ord = 'stat_com';
	}
}

//parāda tās sadaļas, kurās ir vismaz viens ieraksts
$cache_key = 'blogstats_' . $ord;
$blogs = $m->get($cache_key);
if ($blogs === false) {
	$blogs = $db->get_results("SELECT * FROM `cat` WHERE `isblog` != '0' AND `stat_topics` > 0 ORDER BY " . $ord . " DESC");
	if ($blogs) {
		$m->set($cache_key, $blogs, 1800);
	}
}

if (!empty($blogs)) {
	foreach ($blogs as $blog) {
		$tpl->newBlock('bs-list-node');
		$tpl->assign([
			'url' => '/' . $blog->textid,
			'title' => $blog->title,
			'p_count' => $blog->stat_topics,
			'c_count' => $blog->stat_com,
			'w_count' => $blog->stat_views
		]);
	}
}


