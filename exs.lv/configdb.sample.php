<?php

// atkomentēt uz servera ar Windows OS
// uz Windows nav šādas Linux funkcijas
//function sys_getloadavg() {return 1;}
// uz Windows 05.11.2016. ir pieejams tikai Memcache php extension
/*class Memcached extends Memcache {    
    public function addServer($mc_host, $mc_port) {
        parent::connect($mc_host, $mc_port);
    }
    public function set($key, $obj, $seconds) {
        parent::set($key, $obj, false, $seconds);
    }
}*/

//mysql master
$hostname = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'exs';
$password = getenv('DB_PASS') ?: 'dev';
$database = getenv('DB_NAME') ?: 'exs';

//smtp
$smtp_hostname = 'smtp.gmail.com';
$smtp_port = 465;
$smtp_encryption = 'ssl';
$smtp_account = 'user@gmail.com';
$smtp_password = '***';

//memcached
$mc_host = getenv('MC_HOST') ?: 'localhost';
$mc_port = getenv('MC_PORT') ?: 11211;

//last.fm
$lastfm_apikey = '';
$lastfm_secret = '';

//formu tokenu salt
$remote_salt = 'BgpgSvz21ku6C2tcEGVLqwWj8fXkeSA9';

// lokālās vides servera IP, kas norādīta arī kā virtualhosts,
// lai no lietotnes lokāli varētu veikt pieprasījumus uz šo IP
$android_local_ip = null; // piemēram, '192.168.88.5'
$ios_local_ip = null;

//facebook login
$fb_api_id = null;
$fb_api_key = null;

//draugiem pase
$dr_api_id = null;
$dr_api_key = null;

//Steam login
$steam_api_key = ""; 		//API atslēga
$steam_domain_name = ""; 	//domēns, kas rādas steam lapā
$steam_login_page = ""; 	//uz kurieni redirektēt pēc logina

//embed.ly API key
$embed_ly_key = 'your_key';

//include folderi
if (!defined('ROOT_PATH')) {
	define('ROOT_PATH', getenv('ROOT_PATH') ?: '/var/www/exs-lv');
}
if (!defined('CORE_PATH')) {
	define('CORE_PATH', ROOT_PATH . '/exs.lv');
}
if (!defined('IMG_PATH')) {
	define('IMG_PATH', ROOT_PATH . '/img.exs.lv');
}
if (!defined('LIB_PATH')) {
	define('LIB_PATH', ROOT_PATH . '/libs');
}
if (!defined('API_PATH')) {
	define('API_PATH', ROOT_PATH . '/api.exs.lv');
}

// paziņojums ja lapā tiek veikti darbi un tā nav piejama
//echo(file_get_contents(CORE_PATH . '/tmpl/maintenance.tpl'));
//exit;

//debug konfigurācija
$dev_ips_file = (defined('ROOT_PATH') ? ROOT_PATH : __DIR__ . '/..') . '/developer_ips.php';
if (file_exists($dev_ips_file)) {
	include_once($dev_ips_file);
}
if (!isset($dev_ips) || !is_array($dev_ips)) {
	$dev_ips = ['127.0.0.1', '::1'];
}

//domēns no kura lādēt pseido-statiskos (/css, /js) failus
//testējot lapu uz sava lokālā servera var aizvietot ar tukšumu
$static_server = 'https://static.exs.lv';

//domēns, no kura ielādēt attēlus
//uz lokālā servera var aizvietot ar tukšumu
$img_server = 'https://img.exs.lv';

/**
 * Pareizas klienta IP adreses noteikšana ir somewhat tricky,
 * jo dažām lapām tiek lietots cloudflare un varnish,
 * dažām tikai varnish, un lokāli visticamāk nav ne viena ne otra
 *
 * aplikācijā REMOTE_ADDR netiek izmantots vispār, tā vietā HTTP_X_FORWARDED_FOR,
 * jeb $auth->ip, visur kur pieejams $auth objekts
 * kuru iepriekš pārrakstam ar (cerams) pareizo IP
 */

//cloudflare gadījums, vienalga vai ar vai bez varnish
if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
	$_SERVER['HTTP_X_FORWARDED_FOR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];

//ja pieprasījums ir bez varnish
} elseif (empty($_SERVER['HTTP_X_VARNISH'])) {
	$_SERVER['HTTP_X_FORWARDED_FOR'] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
}

if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
	$addr = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
	$_SERVER['HTTP_X_FORWARDED_FOR'] = trim(end($addr));
}

$client_ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '127.0.0.1';
$req_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

if ((in_array($client_ip, $dev_ips) || !empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 8080 || getenv('IS_DEV')) && !isset($_GET['_']) && !isset($_POST['newtags']) && substr($req_uri, -4) != '.jpg') {
	$start_time = microtime(true);
	$debug = true;
	ini_set('display_errors', 'On');
	error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
} else {
	error_reporting(0);
	$debug = false;
}

//defaultās vērtības, mainīgo inicializācija
$page_title = 'Spēļu portāls';
$inprofile = false;
$new_msg_string = '';
$pagepath = '';
$new_ap_string = '';
$tpl_options = '';
$cat = 'index';
$skin = 'main';
$idb_count = '';
$add_css = array();
$add_js = array();
$users_cache = array();
$mention_counter = 0;
$hashtag_counter = 0;
$lang = 1;
$generic_f_icon = 'modules/forums/images/generic.png';
$disable_emotions = 0;
$profile_views_limit = 30;

//multibyte atbalsts
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');

//karma no kuras sakot var labot savus postus
$min_post_edit = 100;

//karma no kuras sakot var labot savus rakstus
$min_page_edit = 0;

//cik ilgi var labot savus rakstus (0 = bezgalīgi)
$page_edit_time = 7200; //2 stundas

//koementāri (level 1) vienā foruma lapā
$comments_per_page = 50;

//sadaļa, kurā parādās aptaujas jautājumu tēmas
$polls_cat = 0;

//instrukcijas meklētājiem
$robotstag = array();

//opengraph and twitter mata tags for layout
$opengraph_meta = array();
$twitter_meta = array();

