<?php

/**
 * 	Atbildot uz jquery ajax pieprasījumu,
 *  atgriež lapu ar vairākiem norādītās prasmes rakstiem.
 */
$out = '';

if (isset($_GET['skill'])) {

	$skill = (int) $_GET['skill'];
	$start = (isset($_GET['page'])) ? (int) $_GET['page'] * 5 - 5 : 0;
	$current_page = (isset($_GET['page'])) ? $_GET['page'] : 1;

	$pages = $db->get_results("SELECT `title`,`strid` FROM `pages` WHERE `category` = $skill ORDER BY `title` ASC LIMIT $start,5");
	if ($pages) {
		foreach ($pages as $page) {
			$short_title = textlimit($page->title, 40);
			$out .= '<a title="' . $page->title . '" href="/read/' . $page->strid . '">';
			$out .= $short_title . '</a><br>';
		}
	}

	// ja vairāk par 5 rakstiem, izvada pogas 'atpakaļ' vai 'tālāk'
	$page_count = $db->get_var("SELECT count(*) FROM `pages` WHERE `category` = $skill");
	if ($page_count > 5) {
		$pages = ceil($page_count / 5);
		if ($current_page > 1 || $current_page < $pages) {
			$out .= '<div class="skill-pages">';
		}
		if ($current_page > 1) {
			$out .= '<a class="skill-pager" href="/rs-skills/?skill=' . $skill;
			$out .= '&page=' . ($current_page - 1) . '">&lsaquo;&lsaquo; Atpakaļ</a>';
		}
		if ($current_page < $pages) {
			$out .= '<a class="skill-pager" href="/rs-skills/?skill=' . $skill;
			$out .= '&amp;page=' . ($current_page + 1) . '">Tālāk &rsaquo;&rsaquo;</a>';
		}
		if ($current_page > 1 || $current_page < $pages) {
			$out .= '</div>';
		}
	}
}
echo $out;
exit;
?>