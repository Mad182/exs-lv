<?php 
/**
 *  exs.lv iOS lietotnes API projekts.
 *
 *  Visi uz ios.exs.lv veiktie pieprasījumi nonāk šeit.
 *  Fails atgriež atbildi JSON formātā.
 *
 *  Ieviests: 2016. gada 14. jūnijs.
 */

require(API_PATH . '/shared/shared.functions.php');
require(API_PATH . '/shared/ios.functions.php');

/*
|--------------------------------------------------------------------------
|   Pamatkonfigurācija.
|--------------------------------------------------------------------------
*/

// saraksts ar "sadaļām", kuras var pieprasīt caur adresi,
// piemēram, https://ios.exs.lv/inbox/
$list_private_cats = array( // šīm pieeja, ja lietotājs ir autentificējies
    'random',
    'statuses',
    'profiles',
    'miniblogs',
    'groups',
    'inbox'
    // 'news'
);
$list_public_cats = array( // šīm pieeja vienmēr
    'auth',
    'collections'
);

// pārbauda, kura sadaļa adresē pieprasīta
$cat_private = '';
$cat_public = '';

if ($var0 && in_array($var0, $list_public_cats)) {
    $cat_public = $var0;
} else if ($var0 && in_array($var0, $list_private_cats)) {
    $cat_private = $var0;
}

/**
 *  Katram pieprasījumam, kas nonācis šajā failā,
 *  uz lietotni atpakaļ atgriež JSON datus šādā formātā:
 *
 *  $json_arr = array(
 *      'status'    => int   // 200|400|440|441|442
 *      'response'  => JSONObj 
 *  );
 *
 *  200 - viss OK, 'response' satur atbildi, kādu klients vēlas sagaidīt
 *  400 - radās kāda kļūda, piemēram, 
 *  440 - lai veiktu šādu pieprasījumu, nepieciešams autentificēties
 *  441 - autentificēšanās 1. solis veikts, bet vēl nav saņemts 2fa kods
 *  442 - IP adresei vai autentificētajam profilam ir liegums
 *
 *  Izņemot '200' kodu, visiem pārējiem statusiem 'response' atslēgas vietā
 *  var būt cita veida saturs, kas sīkāk aprakstīts dokumentācijā.
 */

$json_arr = array(
    'status' => 200
);

// dati par lietotāja IP liegumu, ja tādi ir
$ip_banned = $db->get_row("
	SELECT * FROM `banned` 
	WHERE `ip` = '".sanitize($auth->ip)."' AND
	(`lang` = 0 OR `lang` = ".(int)$api_lang.")
	LIMIT 1
");


/*
|--------------------------------------------------------------------------
|   Pieprasījuma apstrāde.
|--------------------------------------------------------------------------
*/

// info par liegumu jāvar noskaidrot jebkurā brīdī, tāpēc pirmā pārbaude
if ($var0 === 'statuses' && $var1 === 'ban_details') {
    // ios.exs.lv/statuses/ban_details

	if ($ip_banned) {
        api_append('is_active', true);
		api_fetch_ban(1, $ip_banned);
	} else if ($auth->ok && !empty($busers) && !empty($busers[$auth->id])) {
        api_append('is_active', true);
		api_fetch_ban(2);
	} else {
        api_append(array(
            'is_active' => false,
            'info_message' => 'Lietotājam nav liegta piekļuve exs.lv.'
        ));
    }

// pārbauda, vai lietotājam ir IP liegums
} else if ($ip_banned) {

	api_fetch_ban(1, $ip_banned);
    api_status(442);
	if ($auth->ok) {
		$auth->logout();
	}
    
// publiskas sadaļas pieprasījums
} else if ($cat_public !== '') {
    
    if (!file_exists(API_PATH . '/api_ios/' . $cat_public . '.php')) {
        api_log('Netika atrasts publiskas API sadaļas PHP fails.');
        api_error('Exs serverī ieperinājušās blusas. ;( Pacietību!');
    } else {
        include(API_PATH . '/api_ios/' . $cat_public . '.php');
    } 

// pieprasījums sadaļai, kur nepieciešama autentificēšanās
} else if ($cat_private !== '') {
 
    if (!$auth->ok) {
        api_status(440);
        api_append(array('info_message' => 'Lai piekļūtu saturam, nepieciešams autentificēties.'));
	// pārbauda, vai lietotājam ir profila liegums
    } else if (!empty($busers) && !empty($busers[$auth->id])) {      
        api_status(442);
		api_fetch_ban(2);
    } else {

        // 2-factor-authentication iespējots? jāpieprasa kods
        $request_2fa = false;
        if ($auth->auth_2fa && empty($_SESSION['2fa'])) {
            require(API_PATH.'/shared/shared.auth.php');
            $request_2fa = api_auth_2fa_request();
        }
        
        if ($request_2fa) {            
            api_status(441);
            api_append(array(
                'info_message' => 'Lai pabeigtu autentificēšanos, jāiesūta 2fa kods.',
                'token' => api_make_xsrf()
            ));
            // profila info, lai, piemēram, iegūtu lietotājvārdu un
            // avatara adresi, kuru parādīt 2fa koda ievades skatā
            api_append_profile_info();            
        } else {
            
            // ielādē ne-publisko sadaļu un tajā izpilda darbības
            if (file_exists(API_PATH . '/api_ios/' . $cat_private . '.php')) {
                include(API_PATH . '/api_ios/' . $cat_private . '.php');
            } else {
               api_error('API kļūda.');
               api_log('Neeksistē ne-publiskas sadaļas \.php fails.');
            }
        }
    }

} else {
    if ($var0 === '/') { // 'index' sadaļas atvēršanu par kļūdu neuzskatīsim
        api_append(array('info_message' => 'Hello world!'));
    } else {
        api_log('Pieprasīta kļūdaina adrese.');
        api_error('Pieprasīta kļūdaina adrese!');
    }    
}


/*
|--------------------------------------------------------------------------
|   Atbilde pieprasījumam.
|--------------------------------------------------------------------------
*/

echo json_encode($json_arr, JSON_UNESCAPED_UNICODE);
exit;
