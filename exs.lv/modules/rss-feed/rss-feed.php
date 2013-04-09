<?php

header('Content-type: text/xml; charset=utf-8');

if($lang == 3) {
	$site_title = 'coding.lv jaunumi';
	$site_description = 'Web programmēšanas forums';
	$site_url = 'http://coding.lv/';
	$index_categories = array();
} elseif($lang == 5) {
	$site_title = 'rp.exs.lv jaunumi';
	$site_description = 'MTA:SA roleplay serveris';
	$site_url = 'http://rp.exs.lv/';
	$index_categories = array(948, 947);
} else {
	$site_title = 'exs.lv jaunumi';
	$site_description = 'Spēļu portāls';
	$site_url = 'http://exs.lv/';
	$index_categories = array(1, 81);
}

if (isset($_GET['var1']) && $_GET['var1'] == 'all' || !isset($_GET['var1']) && $lang == 3) {
	$articles = $db->get_results("SELECT * FROM `pages` WHERE `lang` = '$lang' ORDER BY `date` DESC LIMIT 20");
	$title = 'Visi raksti - ' . $site_title;
} elseif (isset($_GET['var1']) && $cat = get_cat($_GET['var1'])) {
	$articles = $db->get_results("SELECT * FROM `pages` WHERE `category` = '$cat->id' AND `lang` = '$lang' ORDER BY `date` DESC LIMIT 20");
	$title = $cat->title . ' - ' . $site_title;
} else {
	$articles = $db->get_results("SELECT * FROM `pages` WHERE `category` IN(".implode(',', $index_categories).") AND `lang` = '$lang' ORDER BY `date` DESC LIMIT 20");
	$title = $site_title;
}

$tpl->newBlock('feed');
$tpl->assign(array(
	'title' => htmlspecialchars($title),
	'link' => htmlspecialchars($site_url),
	'description' => htmlspecialchars($site_description),
	'self' => htmlspecialchars(substr($site_url, 0, -1) . $_SERVER['REQUEST_URI'])
));

if(!empty($articles)) {
	foreach ($articles as $article) {

		$author = get_user($article->author);
		$url = $site_url . 'read/' . $article->strid;

		$text = add_smile($article->text);

		if ($article->avatar) {
			$text = '<p><a href="' . $url . '"><img style="border:0;float:left;margin: 6px 7px 0 0" src="' . $site_url . $article->avatar . '" alt="' . htmlspecialchars($article->title) . '" /></a></p>' . $text;
		}

		$text = str_replace('="/', '="'.$site_url, $text);

		$cat = get_cat($article->category);

		$tpl->newBlock('feed-item');
		$tpl->assign(array(
			'title' => htmlspecialchars($article->title),
			'link' => htmlspecialchars($url),
			'description' => htmlspecialchars($text),
			'date' =>  gmdate('r', strtotime($article->date)),
			'creator' =>  htmlspecialchars($author->nick),
			'category' => htmlspecialchars($cat->title)
		));
	}
}

$tpl->printToScreen();
exit;

