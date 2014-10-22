<?php

/**
 * Info par domēniem, kuri atbilst katram $lang (lai veidotu linkus starp projektiem u.c.)
 */
$config_domains = array(
	1 => array(
		'domain' => 'm.exs.lv',
		'include' => 'exs.lv',
		'prefix' => '',
		'ssl' => true
	),
	3 => array(
		'domain' => 'm.coding.lv',
		'include' => 'coding.lv',
		'prefix' => 'code',
		'ssl' => true
	),
	7 => array(
		'domain' => 'mlol.exs.lv',
		'include' => 'lol.exs.lv',
		'prefix' => 'lol',
		'ssl' => false
	),
	9 => array(
		'domain' => 'mrs.exs.lv',
		'include' => 'runescape.exs.lv',
		'prefix' => 'runescape',
		'ssl' => false
	)
);

// saīsinātais runescape projekta domēns
if ($_SERVER['SERVER_NAME'] === 'm.rs.exs.lv' || $_SERVER['SERVER_NAME'] === 'dev.m.rs.exs.lv' || $_SERVER['SERVER_NAME'] === 'm.runescape.exs.lv') {
	redirect('https://mrs.exs.lv' . $_SERVER['REQUEST_URI'], true);
}

// saīsinātais lol projekta domēns
if ($_SERVER['SERVER_NAME'] === 'm.lol.exs.lv' || $_SERVER['SERVER_NAME'] === 'dev.m.lol.exs.lv') {
	redirect('https://mlol.exs.lv' . $_SERVER['REQUEST_URI'], true);
}

$found = false;
foreach ($config_domains as $lang => $site) {

	if ($_SERVER['SERVER_NAME'] === $site['domain'] || $_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === 'dev.' . $site['domain']) {
		require(CORE_PATH . '/config/' . $site['include'] . '.php');
		$found = true;
		break;
	} elseif ($_SERVER['SERVER_NAME'] === 'www.' . $site['domain']) {
		redirect('https://' . str_replace('www.', '', $_SERVER['SERVER_NAME']) . $_SERVER['REQUEST_URI'], true);
	}
}

//domain not found, redirect to exs.lv
if (!$found) {
	redirect('https://m.exs.lv' . $_SERVER['REQUEST_URI'], true);
}

//remove index.php from urls
if ($_SERVER['REQUEST_URI'] == '/index.php' && empty($_POST)) {
	redirect('/', true);
}

