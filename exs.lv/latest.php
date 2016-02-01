<?php

/**
 * Jaunākie posti lapas malā (TIKAI ajax pieprasījumam, kad lietotājs manuāli klikšķina uz taba)
 */
require('configdb.php');

require(CORE_PATH . '/includes/class.mdb.php');
require(CORE_PATH . '/includes/class.auth.php');
require(CORE_PATH . '/includes/functions.core.php');

/* nosaka, kuru lapu rādīt (exs.lv, coding.lv, etc) */
require(CORE_PATH . '/includes/site_loader.php');

session_start();

//mysql konekcija
$db = new mdb($username, $password, $database, $hostname);

//memcached konekcija
$m = new Memcache;
$m->connect($mc_host, $mc_port);

$site_access = get_site_access();

header('Content-Type: text/html; charset=utf-8');

$auth = new Auth();

if (isset($_GET['type']) && $_GET['type'] == 'images') {
	echo get_latest_images();
} else {
	if ($lang === 9) { // #rs
		echo rs_get_latest_pages();
	} else {
		echo get_latest_posts();
	}
}
