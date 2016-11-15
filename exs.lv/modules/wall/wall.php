<?php

//load css
$add_css[] = 'wall.css';

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
}

$tpl->assignGlobal([
	'text-enc' => urlencode('Spēļu un izklaides portāls exs.lv'),
	'bookmark-enc' => urlencode('https://exs.lv'),
	'fb-likes' => get_fb_likes('https://exs.lv')
]);

$pagepath = '';

$opengraph_meta['title'] = 'Spēļu un izklaides portāls exs.lv';
$opengraph_meta['type'] = 'article';
$opengraph_meta['url'] = 'https://exs.lv';
$opengraph_meta['image'] = 'https://exs.lv/bildes/exs-lv-screenshot.png';
$opengraph_meta['description'] = 'Viens no senākajiem un populārākajiem spēļu portāliem Latvijā. Diskusijas, atbildes uz jautājumiem, spēļu un filmu apskati, interesanti jaunumi un daudz kas cits...';
$twitter_meta['card'] = 'summary_large_image';

