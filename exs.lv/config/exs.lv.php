<?php
/**
 *  exs.lv projekta konfigurācija
 */

/*
|--------------------------------------------------------------------------
|   Sociālo tīklu atslēgas.
|--------------------------------------------------------------------------
*/ 

// facebook login
$fb_api_id = '353222841436117';
$fb_api_key = 'f6ac0e495e8b5a09ff2ea463383dc57c';

// draugiem pase
$dr_api_id = 15005147;
$dr_api_key = 'f38c225b8f65df03c5aaa847b1f052a9';

// steam login
$steam_api_key = "D92CAC5D0E6086FAD16936C2B644EFDA"; // API atslēga
$steam_domain_name = "exs.lv"; // domēns, kas rādās steam lapā
$steam_login_page = "https://exs.lv/steam-login"; // uz kurieni pārvirzīt pēc logina

// twitter login
$CONSUMER_KEY = 'r7Wjk5VoxlMVdDrkK7wN3X6q2';
$CONSUMER_SECRET = 'axdLXZy6tm5pM1nB4VM2IZ9UvZKotH22xZlrvNPOKkge86UFen';

$polls_cat = 2;

$robotstag[] = 'noodp';

$twitter_meta['site'] = '@exs_lv';
$opengraph_meta['locale'] = 'lv_LV';

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

require(CORE_PATH . '/includes/functions.exs.php');
