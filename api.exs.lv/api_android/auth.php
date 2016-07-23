<?php
/**
 *  Android autentificēšanās apakšmodulis.
 */

require(API_PATH.'/shared/shared.auth.php');

/**
 *  Atgriež lietotāja xsrf tokenu.
 *  android.exs.lv/auth/gettoken
 */
if ($var1 === 'gettoken') {
    api_auth_get_token();

/**
 *  Autentificēšanās mēģinājums.
 *  android.exs.lv/auth/login
 */
} else if ($var1 === 'login') {
    api_auth_login();	

/**
 *  Izautorizēšanās mēģinājums.
 *  android.exs.lv/auth/logout
 */
} else if ($var1 === 'logout') {    
    api_auth_logout();

/**
 *  2fa atslēgas iesniegšana.
 *  android.exs.lv/auth/2fa
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
