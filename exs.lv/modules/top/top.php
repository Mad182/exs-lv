<?php

/**
 * Populārākie raksti
 */
$pages = $db->get_results("SELECT `title`, `strid`, `lang`, `views` FROM `pages` ORDER BY `views` DESC LIMIT 25");
if ($pages) {
	$tpl->newBlock('top-views');
	foreach ($pages as $page) {
		$tpl->newBlock('top-views-node');
		$tpl->assign(array(
			'url' => get_protocol($page->lang) . get_domain($page->lang) . '/read/' . $page->strid,
			'page-vievs-title' => textlimit($page->title, 32, '..'),
			'page-vievs-views' => $page->views,
		));
	}
}


$pages = $db->get_results("SELECT `title`, `strid`, `lang`, `posts` FROM `pages` ORDER BY `posts` DESC LIMIT 25");
if ($pages) {
	$tpl->newBlock('top-comments');
	foreach ($pages as $page) {
		$tpl->newBlock('top-comments-node');
		$tpl->assign(array(
			'url' => get_protocol($page->lang) . get_domain($page->lang) . '/read/' . $page->strid,
			'page-comments-title' => textlimit($page->title, 32, '..'),
			'page-comments-posts' => $page->posts,
		));
	}
}

