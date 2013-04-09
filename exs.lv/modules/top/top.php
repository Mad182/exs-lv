<?php

$pages = $db->get_results("SELECT id,title,views FROM pages ORDER BY views DESC LIMIT 25");
if ($pages) {
	$tpl->newBlock('top-views');
	foreach ($pages as $page) {
		$tpl->newBlock('top-views-node');
		$tpl->assign(array(
			'node-url' => mkurl('page', $page->id, $page->title),
			'page-vievs-title' => textlimit($page->title, 32, '..'),
			'page-vievs-views' => $page->views,
		));
	}
}


$pages = $db->get_results("SELECT id,title,posts FROM pages ORDER BY posts DESC LIMIT 25");
if ($pages) {
	$tpl->newBlock('top-comments');
	foreach ($pages as $page) {
		$tpl->newBlock('top-comments-node');
		$tpl->assign(array(
			'node-url' => mkurl('page', $page->id, $page->title),
			'page-comments-title' => textlimit($page->title, 32, '..'),
			'page-comments-posts' => $page->posts,
		));
	}
}
