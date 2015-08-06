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

// facebook login
$fb_api_id = '382758518536064';
$fb_api_key = 'e91d56558adbab5e25c97d3eb46cf2bb';

// draugiem.lv pase
$dr_api_id = 15005147;
$dr_api_key = 'f38c225b8f65df03c5aaa847b1f052a9';

$robotstag[] = 'noodp';

// aptauju sadaļas id (nav, jo aptaujas ieraksta miniblogos)
$polls_cat = 0;

// bot user id
$rsbot_id = 33342; // "Wise Old Man"

// sadaļu id, kas tiek izmantoti /rsmod un /rshelp modulī
$cat_f2p_quests = 99;
$cat_p2p_quests = 100;
$cat_miniquests = 193;
$cats_quests    = array(99, 100);
$cat_quests     = array(99, 100, 193);

$cat_minigames      = 160;
$cat_distractions   = 792;
$cat_activities     = array(160, 792);
$cat_achievements   = 194;
$cat_guilds = 791;

$cat_rsnews = 599;
$cat_padomi = 5;


/*
|--------------------------------------------------------------------------
|   HTTPS, sesiju un cepumu uzstādījumi.
|--------------------------------------------------------------------------
*/

if (!$auth->is_local) {
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
}

require(CORE_PATH . '/includes/functions.runescape.php');
