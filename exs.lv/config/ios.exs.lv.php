<?php
/**
 *  exs.lv iOS lietotnei paredzēto atbilžu konfigurācija.
 */


// lietotnē būs iespējota pārslēgšanās starp vairākiem apakšprojektiem, bet tiem
// nepieciešams jauns mainīgais, jo parastais $lang (ios.exs.lv) nemainās
$api_lang = 1;


// ios pieprasījumos nedrīkst atgriezt kļūdas (ja vien tās nav 
// json formātā), bet var gadīties, ka iekš configdb.php tās jau ir iespējotas;
// lai kļūdas lokāli redzētu, var iekš configdb.php pievienot šādu mainīgo
if (!isset($ios_local)) {

	// ar $_GET['debug'] atstāsim variantu, 
	// kā kļūdas tomēr apskatīt arī live versijā
	if ($debug === true && !isset($_GET['debug'])) {
		ini_set('display_errors', 0);
		error_reporting(0);
		$debug = false;
	}
}


/*
|--------------------------------------------------------------------------
|   HTTPS, sesiju un cepumu uzstādījumi.
|--------------------------------------------------------------------------
*/

if (!$is_local && (!isset($ios_local_ip) || $_SERVER['SERVER_NAME'] !== $ios_local_ip)) {

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

	$secure_login = true;

} else if (!isset($ios_local_ip) || $_SERVER['SERVER_NAME'] !== $ios_local_ip) {
    ini_set('session.cookie_domain', '.exs.dev');
}

require_once(CORE_PATH . '/includes/functions.core.php');
