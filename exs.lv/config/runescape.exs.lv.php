<?php
/**
 *  RuneScape apakšprojekta konfigurācijas fails
 *
 *  Satur ar apakšprojektu saistītus globālos mainīgos
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
$rsbot_id = 33342;

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


// auto login visos subdomēnos
if ($_SERVER['SERVER_NAME'] !== 'localhost' && substr($_SERVER['SERVER_NAME'], 0, 4) !== 'dev.') {

	//redirect https links
	if (empty($_SERVER['HTTPS'])) {
		redirect("https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true);
	} else {
		//secure cookies
		ini_set('session.cookie_domain', '.exs.lv');
		ini_set('session.cookie_httponly', 1);
		ini_set('session.cookie_secure', 1);
		ini_set('session.use_only_cookies', 1);
	}

}

/*
 * runescape.exs.lv specific functions
 */
require(CORE_PATH . '/includes/functions.runescape.php');
