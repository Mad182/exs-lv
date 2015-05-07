<?php

/**
 * Info par domēniem, kuri atbilst katram $lang
 * (lai veidotu linkus starp projektiem u.c.)
 */
$config_domains = array(
	1 => array(
		'domain' => 'exs.lv',
		'alias' => null,
		'prefix' => '',
		'ssl' => true
	),
	2 => array(
		'domain' => 'android.exs.lv',
		'alias' => $android_local_ip,
		'prefix' => 'android',
		'ssl' => true
	),
	3 => array(
		'domain' => 'coding.lv',
		'alias' => null,
		'prefix' => 'code',
		'ssl' => true
	),
	5 => array(
		'domain' => 'rp.exs.lv',
		'alias' => null,
		'prefix' => 'mta',
		'ssl' => true
	),
	7 => array(
		'domain' => 'lol.exs.lv',
		'alias' => null,
		'prefix' => 'lol',
		'ssl' => true
	),
	8 => array(
		'domain' => 'secure.exs.lv',
		'alias' => null,
		'prefix' => 'secure',
		'ssl' => true
	),
	9 => array(
		'domain' => 'runescape.exs.lv',
		'alias' => 'rs.exs.lv',
		'prefix' => 'runescape',
		'ssl' => true
	)
);

$found = false;
foreach ($config_domains as $lang => $site) {

	if ($_SERVER['SERVER_NAME'] === $site['domain'] || 
		(!is_null($site['alias']) && $_SERVER['SERVER_NAME'] === $site['alias']) || 
		$_SERVER['SERVER_NAME'] === 'localhost' || 
		$_SERVER['SERVER_NAME'] === 'dev.' . $site['domain']) {

		require CORE_PATH . '/config/' . $site['domain'] . '.php';
		$found = true;
		break;
	} elseif ($_SERVER['SERVER_NAME'] === 'www.' . $site['domain']) {
		if (empty($site['ssl'])) {
			$proto = 'http://';
		} else {
			$proto = 'https://';
		}
		redirect($proto . str_replace('www.', '', $_SERVER['SERVER_NAME']) . $_SERVER['REQUEST_URI'], true);
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

