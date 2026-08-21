<?php
/**
 *  RuneScape apakšprojekta konfigurācija.
 */

/*
|--------------------------------------------------------------------------
|   Projekta globālie mainīgie.
|--------------------------------------------------------------------------
*/

$tpl_options = '';
$page_title = 'King Black Dragon\'s Lair';

// aptauju sadaļas id (nav, jo aptaujas ieraksta miniblogos)
$polls_cat = 0;

// bot user id
$rsbot_id = 33342; // "Wise Old Man"

// sadaļu id, kas tiek izmantoti /rsmod un /rshelp modulī
$cat_f2p_quests = 99;
$cat_p2p_quests = 100;
$cat_miniquests = 193;
$cats_quests    = [99, 100];
$cat_quests     = [99, 100, 193];

$cat_minigames      = 160;
$cat_distractions   = 792;
$cat_activities     = [160, 792];
$cat_achievements   = 194;
$cat_guilds = 791;

$cat_rsnews = 599;
$cat_padomi = 5;

// runescape jaunumu rakstu bilžu folderis
$dir_news_images = CORE_PATH.'/bildes/runescape/news/';

// cik ziņu rakstus rādīt sākumlapā
$rs_news_count = 11;


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

require(CORE_PATH . '/modules/runescape/functions.php');
