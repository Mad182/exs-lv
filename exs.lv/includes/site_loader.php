<?php

/* nosaka, kuru lapu rādīt (exs.lv, coding.lv, etc) */
if ($_SERVER['SERVER_NAME'] === 'exs.lv' || $_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === 'dev.exs.lv') {
	require(CORE_PATH . '/config/exs-lv.php');

} elseif ($_SERVER['SERVER_NAME'] === 'coding.lv') {
	require(CORE_PATH . '/config/coding-lv.php');

} elseif ($_SERVER['SERVER_NAME'] === 'lfs.lv') {
	require(CORE_PATH . '/config/lfs-lv.php');

} elseif ($_SERVER['SERVER_NAME'] === 'rp.exs.lv') {
	require(CORE_PATH . '/config/mtaforum.php');

} elseif ($_SERVER['SERVER_NAME'] === 'lol.exs.lv') {
	require(CORE_PATH . '/config/lol-exs-lv.php');

} elseif ($_SERVER['SERVER_NAME'] === 'ezgif.com') {
	require(CORE_PATH . '/config/ezgif-com.php');

} elseif ($_SERVER['SERVER_NAME'] === 'www.code.exs.lv' || $_SERVER['SERVER_NAME'] === 'code.exs.lv' || $_SERVER['SERVER_NAME'] === 'www.coding.lv') {
	redirect('http://coding.lv' . $_SERVER['REQUEST_URI'], true);
} elseif ($_SERVER['SERVER_NAME'] === 'www.ezgif.com') {
	redirect('http://ezgif.com' . $_SERVER['REQUEST_URI'], true);
} elseif ($_SERVER['SERVER_NAME'] === 'www.lfs.lv') {
	redirect('http://lfs.lv' . $_SERVER['REQUEST_URI'], true);
} elseif ($_SERVER['SERVER_NAME'] === 'www.lol.exs.lv') {
	redirect('http://lol.exs.lv' . $_SERVER['REQUEST_URI'], true);
} elseif ($_SERVER['SERVER_NAME'] === 'mta-forum.exs.lv' || $_SERVER['SERVER_NAME'] === 'www.rp.exs.lv') {
	redirect('http://rp.exs.lv' . $_SERVER['REQUEST_URI'], true);
} else {
	redirect('http://exs.lv' . $_SERVER['REQUEST_URI'], true);
}


if ($_SERVER['REQUEST_URI'] == '/index.php' && empty($_POST)) {
	redirect('/', true);
}
