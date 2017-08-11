<?php

if (!isset($_GET['viewcat']) || $_GET['viewcat'] !== $category->textid) {
	redirect('/' . $category->textid, true);
}

$tpl->assign('cat-title', $category->title);

$categorys2 = $db->get_results("SELECT `title`,`textid` FROM `cat` WHERE `parent` = '$category->id' ORDER BY `title` ASC");
if ($categorys2) {
	$tpl->newBlock('second-category-ul');
	foreach ($categorys2 as $category2) {
		$tpl->newBlock('second-category-li');
		$tpl->assign([
			'title' => $category2->title,
			'url' => '/' . $category2->textid
		]);
	}
}

$pagepath = $category->title;
if ($category->parent) {
	$category2 = get_cat($category->parent);
	$pagepath = '<a href="/' . $category2->textid . '">' . $category2->title . '</a> / ' . $pagepath;
	$page_title = $page_title . ' | ' . $category2->title;
}
