<?php

/* nosaka, kuru lapu rādīt (exs.lv, coding.lv, etc) */
if ($_SERVER['SERVER_NAME'] === 'm.exs.lv') {
	require(CORE_PATH . '/config/exs-lv.php');
} elseif ($_SERVER['SERVER_NAME'] === 'm.coding.lv') {
	require(CORE_PATH . '/config/coding-lv.php');
} elseif ($_SERVER['SERVER_NAME'] === 'm.rp.exs.lv') {
	require(CORE_PATH . '/config/mtaforum.php');
} elseif ($_SERVER['SERVER_NAME'] === 'm.lol.exs.lv') {
	require(CORE_PATH . '/config/lol-exs-lv.php');
} else {
	redirect('http://m.exs.lv' . $_SERVER['REQUEST_URI'], true);
}


if ($_SERVER['REQUEST_URI'] == '/index.php' && empty($_POST)) {
	redirect('/', true);
}

/**
 * Info par domēniem, kuri atbilst katram $lang (lai veidotu linkus starp projektiem u.c.)
 */
$config_domains = array(
	1 => array(
		'domain' => 'm.exs.lv',
		'prefix' => ''
	),
	3 => array(
		'domain' => 'm.coding.lv',
		'prefix' => 'code'
	),
	5 => array(
		'domain' => 'm.rp.exs.lv',
		'prefix' => 'mta'
	),
	7 => array(
		'domain' => 'm.lol.exs.lv',
		'prefix' => 'lol'
	)
);
