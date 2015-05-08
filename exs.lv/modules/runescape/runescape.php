<?php
/** 
 *  RuneScape apakšprojekta sākumlapas satura modulis.
 *
 *  Autors: Edgars P. 
 */

if ($auth->ok) {
	set_action('sākumlapu');
}

// mod opšns
if ($auth->id == 115) {

	if (isset($_GET['magic']) && $_GET['magic'] == 'swaptitles') {
		swap_titles();
	}
	
	if (isset($_GET['magic']) && $_GET['magic'] == 'readrss') {
		read_rss(true);
	}
	
	if (isset($_GET['magic']) && $_GET['magic'] == 'recreate') {
		create_news('rs3');
		create_news('oldschool');
	}
}

// sākumlapā rādīs ierakstus no runescape.com RSS feed
// (izvēle starp Oldschool un RuneScape 3 versiju)

$news_type = 'rs3';
if (isset($_COOKIE['last-rsnews-tab']) &&
	$_COOKIE['last-rsnews-tab'] === 'oldschool') {
	$news_type = 'oldschool';
}

//read_rss(); // iekšēji funkcija nolasīs tikai reizi x minūtēs

$tpl->newBlock('news-tabs');
$tpl->assign($news_type.'-selected', 'active '); 
$tpl->assign('selected-news', fetch_news($news_type));
