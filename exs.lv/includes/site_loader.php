<?php


/**
 * Info par domēniem, kuri atbilst katram $lang (lai veidotu linkus starp projektiem u.c.)
 */
$config_domains = array(
	1 => array(
		'domain' => 'exs.lv',
		'prefix' => ''
	),
	3 => array(
		'domain' => 'coding.lv',
		'prefix' => 'code'
	),
	5 => array(
		'domain' => 'rp.exs.lv',
		'prefix' => 'mta'
	),
	7 => array(
		'domain' => 'lol.exs.lv',
		'prefix' => 'lol'
	),
	8 => array(
		'domain' => 'secure.exs.lv',
		'prefix' => 'secure'
	)
);



$found = false;
foreach($config_domains as $key => $site) {

	if ($_SERVER['SERVER_NAME'] === $site['domain'] || $_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === 'dev.' . $site['domain']) {
		require(CORE_PATH . '/config/'.$site['domain'].'.php');
		$found = true;
		$lang = $key;
		break;
	} elseif($_SERVER['SERVER_NAME'] === 'www.'.$site['domain']) {
		redirect('http://' . str_replace('www.', '', $_SERVER['SERVER_NAME']) . $_SERVER['REQUEST_URI'], true);
	}

}

//domain not found, redirect to exs.lv
if(!$found) {
	redirect('http://exs.lv' . $_SERVER['REQUEST_URI'], true);
}

//remove index.php from urls
if ($_SERVER['REQUEST_URI'] == '/index.php' && empty($_POST)) {
	redirect('/', true);
}
