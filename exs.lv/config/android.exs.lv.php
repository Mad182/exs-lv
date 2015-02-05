<?php
/**
 *  exs.lv Android lietotnei paredzēto atbilžu konfigurācija.
 */

// lietotnē būs iespējota pārslēgšanās starp vairākiem apakšprojektiem, bet tiem
// nepieciešams jauns mainīgais, jo parastais $lang (android.exs.lv) nemainās
$android_lang = 1;

// android pieprasījumos nedrīkst atgriezt kļūdas (ja vien tās nav 
// json formātā), bet var gadīties, ka iekš configdb.php tās jau ir iespējotas;
// lai kļūdas lokāli redzētu, var iekš configdb.php pievienot šādu mainīgo
if (!isset($android_local)) {

    // ar $_GET['debug'] atstāsim variantu, 
    // kā kļūdas tomēr apskatīt arī live versijā
    if ($debug === true && !isset($_GET['debug'])) {
        ini_set('display_errors', 0);
        error_reporting(0);
        $debug = false;
    }
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

require_once(CORE_PATH . '/includes/functions.exs.php');
require_once(CORE_PATH . '/includes/functions.android.php');
