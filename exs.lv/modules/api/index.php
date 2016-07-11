<?php 
/**
 *  exs.lv API dokumentācijas lapa.
 *
 *  Izveidota kā atsevišķa lapa ar jaunu dizainu.
 *
 *  API dokumentācijas moduļi:
 *    1. Android lietotne (nav uzrakstīta, paslēpta)
 *    2. iOS lietotne
 */
 
/*
|--------------------------------------------------------------------------
|   Lapas pamatkonfigurācija.
|--------------------------------------------------------------------------
*/

header('Content-Type: text/html; charset=UTF-8');

require('../../configdb.php');
require(CORE_PATH . '/includes/class.mdb.php');
require(CORE_PATH . '/includes/class.auth.php');
require(CORE_PATH . '/includes/class.templatepower.php');
require(CORE_PATH . '/includes/functions.core.php');

// arī šajā lapā der exs.lv projekta konfigurācija
require(CORE_PATH . '/includes/site_loader.php');

// URL rewrite hack
$var1 = $var2 = $var3 = false; // lai var izmantot "if ($var1)"
if(!empty($_GET['params'])) {
	$parts = explode('/', $_GET['params']);
	if (!empty($parts[0])) { $var1 = $parts[0]; }
	if (!empty($parts[1])) { $var2 = $parts[1]; }
	if (!empty($parts[2])) { $var3 = $parts[2]; }
}

// objektu inicializācija
session_start();
$db = new mdb($username, $password, $database, $hostname);
unset($password);
$m = new Memcache;
$m->connect($mc_host, $mc_port);
$site_access = get_site_access(); // nepieciešams Auth klasei
$auth = new Auth();

// pagaidām lapa pieejama tikai pāris lietotājiem
if (!$auth->ok || !in_array($auth->id, array(1, 115, 29176))) { // mad, burvis, svens
    redirect('/');
    exit;
}

// lapas template ielāde
$tpl = new TemplatePower(CORE_PATH . '/modules/api/index.tpl');
if (($tpl2 = $m->get('tpl_exs_api_docs')) === false || $debug === true) {
    // iekešo sadaļas template, tādējādi -20% ielādes laikam
    $tpl->prepare();
    $m->set('tpl_exs_api_docs', $tpl, false, 3600);
} else {
    $tpl = $tpl2;
    unset($tpl2);
}


/*
|--------------------------------------------------------------------------
|   Atgriežamā satura apstrāde.
|--------------------------------------------------------------------------
*/

$project = ($var1 === 'a') ? 'android' : 'ios';

// parāda izvēlētā moduļa sāna navigāciju
switch ($project) {
    case 'ios':
        $tpl->assign('active-1', 'class="is-active" ');
        $tpl->newBlock('ios-logo');
        $tpl->newBlock('ios-navig');
        break;
    default:
        $tpl->assign('active-0', 'class="is-active" ');
        $tpl->newBlock('android-logo');
        $tpl->newBlock('android-navig');
        break;
}

// ielasa un parāda Android moduļa sadaļas saturu
if ($var1 === 'a') {    

    $filename = 'intro.html';
    if (is_string($var2)) {
        switch ($var2) {
            case 'miniblogs':
                $filename = 'miniblogs.html';
                break;
            case 'groups':
                $filename = 'groups.html';
                break;
            case 'messages':
                $filename = 'messages.html';
                break;
            case 'other':
                $filename = 'other.html';
                break;
            default:
                $filename = 'intro.html';
                break;
        }
    }
    
    $content = '';
    if (!empty($filename) && file_exists(CORE_PATH.'/modules/api/html_android/'.$filename)) {
        $content = file_get_contents(CORE_PATH.'/modules/api/html_android/'.$filename);
    }
    
    $tpl->assignGlobal(array(
        'page_content' => $content,
        'active-'.str_replace('.html', '', $filename) => 'is_active'
    ));
}

// ielasa un parāda iOS moduļa sadaļas saturu
if ($var1 === false || $var1 === 'i') {    

    $filename = 'intro.html';
    if (is_string($var2)) {
        switch ($var2) {
            case 'miniblogs':
                $filename = 'miniblogs.html';
                break;
            case 'groups':
                $filename = 'groups.html';
                break;
            case 'messages':
                $filename = 'messages.html';
                break;
            case 'other':
                $filename = 'other.html';
                break;
            default:
                $filename = 'intro.html';
                break;
        }
    }
    
    $content = '';
    if (!empty($filename) && file_exists(CORE_PATH.'/modules/api/html_ios/'.$filename)) {
        $content = file_get_contents(CORE_PATH.'/modules/api/html_ios/'.$filename);
    }
    
    $tpl->assignGlobal(array(
        'page_content' => $content,
        'active-'.str_replace('.html', '', $filename) => 'is_active'
    ));    
}


/*
|--------------------------------------------------------------------------
|   Ielādes beigu fāze.
|--------------------------------------------------------------------------
*/

$tpl->assignGlobal(array(
	'static-server' => $static_server
));

// aizver savienojumu ar datubāzi, ja satura sūtīšana ieilgst
$db->close();
// izprintē šī moduļa jeb lapas saturu
$tpl->printToScreen();
