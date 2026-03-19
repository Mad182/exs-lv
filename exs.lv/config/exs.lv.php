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
$fb_api_id = '';
$fb_api_key = '';

// draugiem pase
$dr_api_id = '';
$dr_api_key = '';

// steam login
$steam_api_key = ""; // API atslēga
$steam_domain_name = "exs.lv"; // domēns, kas rādās steam lapā
$steam_login_page = "https://exs.lv/steam-login"; // uz kurieni pārvirzīt pēc logina

// twitter login
$CONSUMER_KEY = '';
$CONSUMER_SECRET = '';

$polls_cat = 2;

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
