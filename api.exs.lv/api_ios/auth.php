<?php
/**
 *  iOS autentificēšanās apakšmodulis.
 */

require(API_PATH.'/shared/shared.auth.php');

/**
 *  Autentificēšanās mēģinājums.
 *  ios.exs.lv/auth/login
 */
if ($var1 === 'login') {
    api_auth_login();	

/**
 *  Izautorizēšanās mēģinājums.
 *  ios.exs.lv/auth/logout
 */
} else if ($var1 === 'logout') {    
    api_auth_logout();

/**
 *  2fa atslēgas iesniegšana.
 *  ios.exs.lv/auth/2fa
 */
} else if ($var1 === '2fa') {    
    api_auth_accept_2fa();
    
/**
 *  Citas situācijas.
 */
} else {
    api_log('Sasniegts auth moduļa "else" bloks.');
    api_error('hellou... are thou lost?');
}
