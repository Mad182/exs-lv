<?php

/**
 * Info par domēniem, kuri atbilst katram $lang
 * (lai veidotu linkus starp projektiem u.c.)
 */
$config_domains = array(
	1 => array(
		'domain' => 'exs.lv',
		'prefix' => '',
		'ssl' => true
	),
	2 => array(
		'domain' => 'android.exs.lv',
		'prefix' => 'android',
		'ssl' => true
	),
	3 => array(
		'domain' => 'coding.lv',
		'prefix' => 'code',
		'ssl' => false
	),
	5 => array(
		'domain' => 'rp.exs.lv',
		'prefix' => 'mta',
		'ssl' => true
	),
	7 => array(
		'domain' => 'lol.exs.lv',
		'prefix' => 'lol',
		'ssl' => true
	),
	8 => array(
		'domain' => 'secure.exs.lv',
		'prefix' => 'secure',
		'ssl' => true
	),
	9 => array(
		'domain' => 'runescape.exs.lv',
		'prefix' => 'runescape',
		'ssl' => true
	)
);

// saīsinātais runescape projekta domēns
if ($_SERVER['SERVER_NAME'] === 'rs.exs.lv' || $_SERVER['SERVER_NAME'] === 'dev.rs.exs.lv') {
	redirect('http://' . str_replace('rs', 'runescape', $_SERVER['SERVER_NAME']) . $_SERVER['REQUEST_URI'], true);
}

$found = false;
foreach ($config_domains as $lang => $site) {

	if ($_SERVER['SERVER_NAME'] === $site['domain'] || $_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === 'dev.' . $site['domain']) {
		require CORE_PATH . '/config/' . $site['domain'] . '.php';
		$found = true;
		break;
	} elseif ($_SERVER['SERVER_NAME'] === 'www.' . $site['domain']) {
		redirect('http://' . str_replace('www.', '', $_SERVER['SERVER_NAME']) . $_SERVER['REQUEST_URI'], true);
	}
}

//domain not found, redirect to exs.lv
if (!$found) {
	redirect('http://exs.lv' . $_SERVER['REQUEST_URI'], true);
}

//remove index.php from urls
if ($_SERVER['REQUEST_URI'] == '/index.php' && empty($_POST)) {
	redirect('/', true);
}
