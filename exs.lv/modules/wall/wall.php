<?php

//load css
$add_css[] = 'wall.99174903.min.css';

/**
 * Sākumlapa
 */
set_action('sākumlapu');

if(!empty($auth->firstpage) && $auth->firstpage === 'wall' && empty($_GET['var1']) || !empty($_GET['var1']) && $_GET['var1'] === 'wall') {

	if(!empty($_GET['var1']) && $auth->ok && $auth->firstpage !== 'wall') {
		$db->update('users', $auth->id, ['firstpage' => 'wall']);
		$auth->reset();
		$auth->mobile = 0;
	}

	require(CORE_PATH . '/modules/wall/tab-wall.php');
} else {

	if(!empty($_GET['var1']) && $auth->ok && $auth->firstpage !== 'news') {
		$db->update('users', $auth->id, ['firstpage' => 'news']);
		$auth->reset();
		$auth->mobile = 0;
	}

	require(CORE_PATH . '/modules/wall/tab-news.php');
	$home_skip = isset($_GET['skip']) ? (int) $_GET['skip'] : 0;
	if ($home_skip > 0) {
		$page_num = (int) floor($home_skip / 5) + 1;
		$page_title = 'Spēļu un izklaides portāls (lapa ' . $page_num . ')';
		$canonical = $opengraph_meta['url'] = 'https://' . $_SERVER['HTTP_HOST'] . '/?skip=' . $home_skip;
	} else {
		$page_title = 'Spēļu un izklaides portāls, tiešsaistes spēles un forums';
		$canonical = $opengraph_meta['url'] = 'https://' . $_SERVER['HTTP_HOST'] . '/';
	}
}

$pagepath = '';

$opengraph_meta['title'] = 'Spēļu un izklaides portāls exs.lv';
$opengraph_meta['type'] = 'website';
if (empty($opengraph_meta['url'])) {
	$opengraph_meta['url'] = 'https://exs.lv/';
}
$opengraph_meta['image'] = 'https://exs.lv/bildes/exs-lv-screenshot.png';
$meta_description = $opengraph_meta['description'] = 'Viens no senākajiem un populārākajiem spēļu portāliem Latvijā. Diskusijas, tiešsaistes spēles, spēļu un filmu apskati, jaunumi un kopiena.';
$twitter_meta['card'] = 'summary_large_image';

