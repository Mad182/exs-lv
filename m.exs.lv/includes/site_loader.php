<?php

/**
 * Info par domēniem, kuri atbilst katram $lang (lai veidotu linkus starp projektiem u.c.)
 */
$config_domains = array(
	1 => array(
		'domain' => 'm.exs.lv',
		'prefix' => '',
		'ssl' => true
	),
	3 => array(
		'domain' => 'm.coding.lv',
		'prefix' => 'code',
		'ssl' => false
	),
	5 => array(
		'domain' => 'm.rp.exs.lv',
		'prefix' => 'mta',
		'ssl' => false
	),
	7 => array(
		'domain' => 'm.lol.exs.lv',
		'prefix' => 'lol',
		'ssl' => false
	),
	9 => array(
		'domain' => 'm.runescape.exs.lv',
		'prefix' => 'runescape',
		'ssl' => false
	)
);


$found = false;
foreach ($config_domains as $lang => $site) {

	if ($_SERVER['SERVER_NAME'] === $site['domain'] || $_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === 'dev.' . $site['domain']) {
		require(CORE_PATH . '/config/' . str_replace(array('m.', 'dev.'), '', $site['domain']) . '.php');
		$found = true;
		break;
	} elseif ($_SERVER['SERVER_NAME'] === 'www.' . $site['domain']) {
		redirect('http://' . str_replace('www.', '', $_SERVER['SERVER_NAME']) . $_SERVER['REQUEST_URI'], true);
	}
}

//domain not found, redirect to exs.lv
if (!$found) {
	redirect('http://m.exs.lv' . $_SERVER['REQUEST_URI'], true);
}

//remove index.php from urls
if ($_SERVER['REQUEST_URI'] == '/index.php' && empty($_POST)) {
	redirect('/', true);
}
