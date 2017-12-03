<?php
redirect('https://exs.lv/');

/**
 *  rp.exs.lv projekta konfigurācija
 */

/*
|--------------------------------------------------------------------------
|   Projekta globālie mainīgie.
|--------------------------------------------------------------------------
*/

$tpl_options = 'no-left';

// facebok login
$fb_api_id = '480869031952190';
$fb_api_key = 'cbf65eee0645935797da97f33b6a77b1';

// draugiem pase
$dr_api_id = 15005147;
$dr_api_key = 'f38c225b8f65df03c5aaa847b1f052a9';

// 1. līmeņa komentāri vienā foruma lapā
$comments_per_page = 25;

// karma, no kuras sākot var labot savus postus
$min_post_edit = 0;

// karma, no kuras sākot var labot savus rakstus
$min_page_edit = 0;

// cik ilgi var labot savus rakstus (0 = bezgalīgi)
$page_edit_time = 0;

// aptauju sadaļa
$polls_cat = 951;

// rādāmo profila skatījumu skaits
$profile_views_limit = 20;

$page_title = 'MTA:SA roleplay serveris';

$twitter_meta['site'] = '@exs_lv';


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
    ini_set('session.cookie_domain', '.exs.dev');
}
