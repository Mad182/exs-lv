<?php
/**
 *  iOS autentificēšanās apakšmodulis.
 */

require(API_PATH.'/shared/shared.auth.php');

/**
 *  Autentificēšanās mēģinājums.
 *  ios.exs.lv/login
 */
if ($var1 === 'login') {
    api_auth_login();	

/**
 *  Izautorizēšanās mēģinājums.
 *  ios.exs.lv/logout
 */
} else if ($var1 === 'logout') {    
    api_auth_logout();

/**
 *  Citas situācijas.
 */
} else {
    api_log('Sasniegts auth moduļa "else" bloks.');
    api_error('hellou... are thou lost?');
}
