<?php
/**
 *  lol.exs.lv projekta konfigurācija
 */

/*
|--------------------------------------------------------------------------
|   Projekta globālie mainīgie.
|--------------------------------------------------------------------------
*/

$tpl_options = '';

// 1. līmeņa komentāri vienā foruma lapā
$comments_per_page = 25;

// karma, no kuras sākot var labot savus postus
$min_post_edit = 0;

// karma, no kuras sākot var labot savus rakstus
$min_page_edit = 0;

// cik ilgi var labot savus rakstus (0 = bezgalīgi)
$page_edit_time = 0;

// aptauju sadaļa
$polls_cat = 1129;

$page_title = 'League of Legends forums';


/*
|--------------------------------------------------------------------------
|   HTTPS, sesiju un cepumu uzstādījumi.
|--------------------------------------------------------------------------
*/

if (!$is_local) {
    // pārvirzīs uz HTTPS saitēm, ja lapa pieprasīta caur HTTP
	if (empty($_SERVER['HTTPS'])) {
		redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true);
	} else {
        // drošam savienojumam nepieciešamie uzstādījumi, kas pie reizes
        // arī autorizēs lietotāju visos subdomēnos
		ini_set('session.cookie_domain', '.exs.lv');
		ini_set('session.cookie_httponly', 1);
		ini_set('session.cookie_secure', 1);
		ini_set('session.use_only_cookies', 1);
	}
} else {
	if (isset($_SERVER['HTTP_HOST']) && str_contains($_SERVER['HTTP_HOST'], '.local')) {
		ini_set('session.cookie_domain', '.exs.local');
	}
}
