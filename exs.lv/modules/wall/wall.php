<?php

//load css
$add_css[] = 'wall.css';

/**
 * Sākumlapa
 */
set_action('sākumlapu');

if(!empty($auth->firstpage) && $auth->firstpage === 'wall' && empty($_GET['var1']) || !empty($_GET['var1']) && $_GET['var1'] === 'wall') {

	if(!empty($_GET['var1']) && $auth->ok && $auth->firstpage !== 'wall') {
		$db->update('users', $auth->id, array('firstpage' => 'wall'));
		$auth->reset();
	}

	require(CORE_PATH . '/modules/wall/tab-wall.php');
} else {

	if(!empty($_GET['var1']) && $auth->ok && $auth->firstpage !== 'news') {
		$db->update('users', $auth->id, array('firstpage' => 'news'));
		$auth->reset();
	}

	require(CORE_PATH . '/modules/wall/tab-news.php');
}

