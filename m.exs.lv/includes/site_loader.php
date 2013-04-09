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
