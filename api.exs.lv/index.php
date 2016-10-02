<?php
/**
 *  exs.lv mobilā API projekts.
 *
 *  Tajā ietilpst:
 *
 *  0. API dokumentācija (api.exs.lv)
 *  1. Android lietotnes API (android.exs.lv)
 *  2. iOS lietotnes API (ios.exs.lv)
 *
 *  Android:    https://play.google.com/store/apps/details?id=lv.exs.android
 *  Publicēta:  2015. gada 1. aprīlis
 */

require('../exs.lv/configdb.php');
require(CORE_PATH . '/includes/class.mdb.php');
require(CORE_PATH . '/includes/class.auth.php');
require(CORE_PATH . '/includes/functions.core.php');
require(CORE_PATH . '/includes/site_loader.php');

/*
|--------------------------------------------------------------------------
|   Šiem projektiem kopēja konfigurācija.
|--------------------------------------------------------------------------
*/

if (isset($_GET['error404'])) { // via .htaccess
    die('Error 404: Lapa nav atrasta!');
} else if (isset($_GET['error403'])) { // via .htaccess
    die('Error 403: Tev nav brīv apskatīt pieprasīto failu vai mapi!');
}

// $_GET parametru rewrite hack
$var0 = '/'; // "sadaļa"
$var1 = $var2 = $var3 = false; // lai var izmantot "if ($var1)"
if(!empty($_GET['params'])) {
	$parts = explode('/', $_GET['params']);
	if (!empty($parts[0])) { $var0 = $parts[0]; }
	if (!empty($parts[1])) { $var1 = $parts[1]; }
	if (!empty($parts[2])) { $var2 = $parts[2]; }
	if (!empty($parts[3])) { $var3 = $parts[3]; }
}

// objektu inicializācija
session_start();
$db = new mdb($username, $password, $database, $hostname);
unset($password);

//memcached konekcija
$m = new Memcached;
$m->addServer($mc_host, $mc_port);

$site_access = get_site_access(); // nepieciešams Auth klasei
$auth = new Auth();
$tpl = null; // nepieciešams dokumentācijas projektam

$busers = get_banlist();
$online_users = get_online();

// līdzšinējais $lang sakrīt ar api/android/ios projektu, nevis to,
// kāds projekts lietotnē patiesībā tiek skatīts, tāpēc papildu mainīgais
$api_lang = 1; // pēc noklusējuma exs.lv

// ja configdb.php failā $img_server tiek definēts, nenorādot protokolu,
// tas jāpievieno atpakaļ, lai lietotnes atpazītu adreses
if (isset($img_server) && substr($img_server, 0, 2) === '//') {
	if (!empty($_SERVER['HTTPS'])) {
		$img_server = 'https:'.$img_server;
	} else {
		$img_server = 'http:'.$img_server;
	}
}

/*
|--------------------------------------------------------------------------
|   Individuālas projektu darbības.
|--------------------------------------------------------------------------
*/

// api.exs.lv
if ($lang === 6) {
    
    header('Content-Type: text/html; charset=UTF-8');
    
    require(CORE_PATH . '/includes/class.templatepower.php');
    $tpl = new TemplatePower(API_PATH . '/api_docs/index.tpl');
    
    if (($tpl2 = $m->get('tpl_api_docs')) === false || $debug === true) {
        $tpl->prepare();
        // iekešo sadaļas template, tādējādi -20% ielādes laikam
        $m->set('tpl_api_docs', $tpl, false, 3600);
    } else {
        $tpl = $tpl2;
        unset($tpl2);
    }
    
    include(API_PATH . '/routes_docs.php');
}

// android.exs.lv
if ($lang === 2) {
    header('Content-Type: application/json; charset=UTF-8');
    include(API_PATH . '/routes_android.php');
}

// ios.exs.lv
if ($lang === 4) {
    header('Content-Type: application/json; charset=UTF-8');
    include(API_PATH . '/routes_ios.php');
}

$db->close();
exit;
