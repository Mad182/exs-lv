<?php
/**
 *  exs.lv projektu un apakšprojektu konfigurācijas fails.
 *
 *  Veic atvērtā projekta noteikšanu, lai turpmāk lapā
 *  ielādētu tieši tam paredzētu saturu.
 */

/*
|--------------------------------------------------------------------------
|   Globālie projekta vides mainīgie.
|--------------------------------------------------------------------------
*/

// vai atvērta lokālā izstrādes vide?
$is_local = 0;

// vai atvērta mobilā versija, ar to saprotot
// kādu no "m." apakšprojektiem, nevis Android vai citas OS lietotni?
$is_mobile = 0;


/*
|--------------------------------------------------------------------------
|   Lapā izmantoto domēnu dati.
|--------------------------------------------------------------------------
*/

$config_domains = array(
	1 => array(
		'domain' => 'exs.lv',
		'prefix' => '',
		'ssl' => true
	),
	2 => array(
		'domain' => 'android.exs.lv',
		'prefix' => 'android',
		'ssl' => true
	),
	3 => array(
		'domain' => 'coding.lv',
		'prefix' => 'code',
		'ssl' => true
	),
    4 => array(
        'domain' => 'ios.exs.lv',
        'prefix' => 'ios',
        'ssl' => true
    ),
	5 => array(
		'domain' => 'rp.exs.lv',
		'prefix' => 'mta',
		'ssl' => true
	),
	7 => array(
		'domain' => 'lol.exs.lv',
		'prefix' => 'lol',
		'ssl' => true
	),
	8 => array(
		'domain' => 'secure.exs.lv',
		'prefix' => 'secure',
		'ssl' => true
	),
	9 => array(
		'domain' => 'runescape.exs.lv',
		'prefix' => 'runescape',
		'ssl' => true
	)
);

// saraksts ar visiem ieviestajiem domēniem/subdomēniem un to atslēgām;
// ar tā palīdzību izvairāmies no liekas iepriekšējā masīva pārstaigāšanas.
$arr_domains = array(

	'localhost' => 1,
	'exs.lv' => 1,    
	'coding.lv' => 3,
	'secure.exs.lv' => 8,    
	
	// apakšprojekti
	'android.exs.lv' => 2,
	$android_local_ip => 2,   
    'ios.exs.lv' => 4,
	'rp.exs.lv' => 5,
	'lol.exs.lv' => 7,
	'runescape.exs.lv' => 9,
	
	// mobilās versijas
	// (cloudflāres dēļ neveidojam vēl vairāk domēna līmeņu)
	'm.exs.lv' => 1,
	'm.coding.lv' => 3,
	'mlol.exs.lv' => 7,
	'mrs.exs.lv' => 9,
	
	// izstrādes versijas, kas pieejamas tikai lokālā vidē;
	// apzināti izvairāmies no tā paša "exs.lv" domēna izmantošanas,
	// lai lokāli nerastos problēmas ar pārlūkiem un HSTS
	// (https://stackoverflow.com/questions/25277457/google-chrome-redirecting-localhost-to-https)
	'exs.dev' => 1,
	'android.exs.dev' => 2,
    'ios.exs.dev' => 4,
	'coding.dev' => 3,
	'rp.exs.dev' => 5,
	'lol.exs.dev' => 7,
	'rs.exs.dev' => 9,
	
	// mobilās izstrādes versijas
	'm.exs.dev' => 1,
	'm.coding.dev' => 3,
	'mlol.exs.dev' => 7,
	'mrs.exs.dev' => 9
);


/*
|--------------------------------------------------------------------------
|   Atvērtā projekta noteikšana.
|--------------------------------------------------------------------------
*/

if (isset($arr_domains[$_SERVER['HTTP_HOST']])) {  
  
	$lang = $arr_domains[$_SERVER['HTTP_HOST']];
	
	if (substr($_SERVER['HTTP_HOST'], -4) === '.dev') {
		$is_local = 1;
	}
	if (strpos($_SERVER['HTTP_HOST'], 'm') === 0) {
		$is_mobile = 1;
	}
	
// valīdas saites ar 'www.' priekšā tiks pārvirzītas uz saitēm bez 'www.'
} else if (strpos($_SERVER['HTTP_HOST'], 'www.') === 0) {

	$name = str_replace('www.', '', $_SERVER['HTTP_HOST']);   
	if (!empty($name) && isset($arr_domains[$name])) {
		$proto = 'https://';
		if (empty($config_domains[$arr_domains[$name]]['ssl'])) {
			$proto = 'http://';
		}
		redirect($proto . $name . $_SERVER['REQUEST_URI'], true);
	}

//rs redirektējam uz runescape lai neveidojas dublikāts saturam
} elseif($_SERVER['HTTP_HOST'] == 'rs.exs.lv') {
	redirect('https://runescape.exs.lv' . $_SERVER['REQUEST_URI'], true);

//ja nekas nav atpazīts, redirektējam uz exs.lv
} else {
	redirect('https://exs.lv' . $_SERVER['REQUEST_URI'], true);
}


/*
|--------------------------------------------------------------------------
|   Projekta konfigurācijas ielāde.
|--------------------------------------------------------------------------
*/

// ja neizdosies noteikt apakšprojektu, pārvirzīs uz exs.lv
if ($lang > 0) {
	require CORE_PATH . '/config/' . $config_domains[$lang]['domain'] . '.php';
} else {
	if ($is_mobile) {
		redirect('https://m.exs.lv' . $_SERVER['REQUEST_URI'], true);
	}
	redirect('https://exs.lv' . $_SERVER['REQUEST_URI'], true);
}

// dzēsīs 'index.php' no saitēm, lai tās būtu glītākas
if ($_SERVER['REQUEST_URI'] === '/index.php' && empty($_POST)) {
	redirect('/', true);
}
