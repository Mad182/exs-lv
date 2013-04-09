<?php

$out = '';
if (isset($_GET['skill'])) {
	$skill = (int) $_GET['skill'];
	$start = (isset($_GET['page'])) ? (int) $_GET['page'] * 5 - 5 : 0;
	$lapa = (isset($_GET['page'])) ? $_GET['page'] : 1;

	$pages = $db->get_results("SELECT `title`,`strid` FROM `pages` WHERE `category` = '$skill' ORDER BY `title` ASC LIMIT $start,5");
	if ($pages) {
		//$out .= '<p>Saistītie raksti</p>';
		foreach ($pages as $page) {
			$short_title = textlimit($page->title, 40);
			$out .= '<a title="' . $page->title . '" href="/read/' . $page->strid . '">' . $short_title . '</a><br />';
		}
	}
	// ja vairāk par 5 linkiem, izvada pogas 'atpakaļ' vai 'tālāk'
	$page_count = $db->get_var("SELECT count(*) FROM `pages` WHERE `category` = '$skill'");
	if ($page_count > 5) {
		$pages = ceil($page_count / 5);
		if ($lapa > 1 || $lapa < $pages) {
			$out .= '<div class="skill-pages">';
		}
		if ($lapa > 1) {
			$out .= '<a class="skill-pager" href="/rs-skills/?skill=' . $skill . '&page=' . ($lapa - 1) . '">&lsaquo;&lsaquo; Atpakaļ</a>';
		}
		if ($lapa < $pages) {
			$out .= '<a class="skill-pager" href="/rs-skills/?skill=' . $skill . '&page=' . ($lapa + 1) . '">Tālāk &rsaquo;&rsaquo;</a>';
		}
		if ($lapa > 1 || $lapa < $pages) {
			$out .= '</div>';
		}
	}
}
//echo "This test is being run on ".$_SERVER['HTTP_USER_AGENT']."<br /><br />";
echo $out;
exit;
?>