<?php

/**
 * Blogu ierakstu statistika
 */
 
//updato kategoriju statistiku
update_blog_stats();

//kārtošana
$ord = 'stat_topics';
if (isset($_GET['order'])) {
	if ($_GET['order'] == 'views') {
		$ord = 'stat_views';
	} elseif ($_GET['order'] == 'comments') {
		$ord = 'stat_com';
	}
}

//parāda tās sadaļas, kurās ir viesmaz viens ieraksts
$blogs = $db->get_results("SELECT * FROM `cat` WHERE `isblog` != '0' AND `stat_topics` > 0 ORDER BY " . $ord . " DESC");
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

