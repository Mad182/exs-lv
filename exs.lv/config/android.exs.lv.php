<?php
/**
 *  exs.lv Android lietotnei paredzēto atbilžu konfigurācija.
 */

// lietotnē iespējota pārslēgšanās starp vairākiem apakšprojektiem, bet tiem
// nepieciešams jauns mainīgais, jo parastais $lang (android.exs.lv) nemainās
$android_lang = 1;

// ja pieprasījums ir uz android.exs.lv, kur visas atbildes tiek gaidītas
// json formātā, php kļūdas izvadīt nedrīkst, bet var gadīties, ka
// iekš configdb.php tās jau ir iespējotas
if (!isset($android_local)) {
    ini_set('display_errors', 0);
    error_reporting(0);
    $debug = false;
}

// auto-login visos subdomēnos
if ($_SERVER['SERVER_NAME'] !== 'localhost' &&
	substr($_SERVER['SERVER_NAME'], 0, 4) !== 'dev.' &&
	$_SERVER['SERVER_NAME'] !== $android_local_ip) {

	// secure cookies
	ini_set('session.cookie_domain', '.exs.lv');
	ini_set('session.cookie_httponly', 1);
	ini_set('session.cookie_secure', 1);
	ini_set('session.use_only_cookies', 1);
	$secure_login = true;
}

require(CORE_PATH . '/includes/functions.exs.php');
require(CORE_PATH . '/includes/functions.android.php');
