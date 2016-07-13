<?php
/**
 *  exs.lv Android API projekta konfigurācija.
 */


// android pieprasījumos nedrīkst atgriezt kļūdas (ja vien tās nav 
// json formātā), bet var gadīties, ka iekš configdb.php tās jau ir iespējotas
if (!isset($is_local)) {

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

// ja nedarbina lokālā vidē...
if (!$is_local && $_SERVER['SERVER_NAME'] !== $android_local_ip) {

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

} else if ($_SERVER['SERVER_NAME'] !== $android_local_ip) {
    ini_set('session.cookie_domain', '.exs.dev');
}
