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
    'profiles',
    'miniblogs',
    'groups',
    'inbox'
    // 'news'
);
$list_public_cats = array( // šīm pieeja vienmēr
    'auth',
    'collections'
    // 'viewstates'
);

// pārbauda, kura sadaļa adresē pieprasīta
$cat_private = '';
$cat_public = '';

if ($var0 && in_array($var0, $list_public_cats)) {
    $cat_public = $var0;
} else if ($var0 && in_array($var0, $list_public_cats)) {
    $cat_private = $var0;
}

/**
 *  Katrs pieprasījums, kas nonācis šajā failā,
 *  atpakaļ saņem atbildi šādā JSON masīva formātā:
 *
 *  $json = array(
 *      'success'       => bool      // vai pieprasījums bija veiksmīgs?
 *      'message'       => string,   // kļūdas paziņojums, ja "state" === "error"
 *      'ban_type'      => int,      // 0 - viss ok, 1 - ip liegums, 2 - profila liegums
 *      'logged_in'     => bool,     // statuss, kas apzīmē, vai lietotājs ir autentificēts
 *      'xsrf_token'    => string,   // anti-xsrf atslēga, kas pievienojama adrešu galā
 *      'response'      => null|JSONObject   // detalizētāks saturs kā atbilde pieprasījumam
 *  );
 */


// atgriežamā json objekta mainīgie;
// to saturs pēc nepieciešamības maināms katra apakšmoduļa iekšienē
$json_success   = true;
$json_message   = '';
$json_banned    = 0;
$json_page      = null;


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
if ($var0 === 'ban_details') {
    // ios.exs.lv/ban_details

	if ($ip_banned) {
		api_fetch_ban(1, $ip_banned);
	} else if ($auth->ok && !empty($busers) && !empty($busers[$auth->id])) {
		api_fetch_ban(2);
	} else {
        api_info('Lietotājam nav liegta piekļuve exs.lv.');
    }

// pārbauda, vai lietotājam ir IP liegums
} else if ($ip_banned) {

	api_fetch_ban(1, $ip_banned);
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
    
// autorizētu pieprasījumu apstrāde
} else if ($auth->ok) {
 
	// pārbauda, vai lietotājam ir profila liegums
	if (!empty($busers) && !empty($busers[$auth->id])) {      
		api_fetch_ban(2);

	// ielādē ne-publisko sadaļu un tajā izpilda darbības
	} else if ($cat_private !== '' &&
               file_exists(API_PATH . '/api_ios/' . $cat_private . '.php')) {
		include(API_PATH . '/api_ios/' . $cat_private . '.php');

    // citu sadaļu pieprasījumi
	} else {
        if ($var0 === '/') { // 'index' sadaļas atvēršanu par kļūdu neuzskatīsim
            api_info('Hello world!');
        } else {
            api_log('Pieprasīta neeksistējoša sadaļa.');
            api_error('Pieprasīta neeksistējoša sadaļa.');
        }
	}

} else {
    // ja lietotājs pēc ilgākas pauzes atkal atver lietotni un
    // sūta pieprasījumu, bet serveris jau dzēsis sesiju, nonāk šeit
    api_log('Neatpazīts pieprasījums no neautentificēta lietotāja.');
	//api_error('Lūdzu, autorizējies!');
}


/*
|--------------------------------------------------------------------------
|   Atbilde pieprasījumam.
|--------------------------------------------------------------------------
*/

echo json_encode(array(
	'success'    => $json_success,
	'message'    => $json_message,
	'ban_type'   => $json_banned,
	'logged_in'  => $auth->ok,
	'xsrf_token' => api_make_xsrf(),
	'response'   => $json_page
), JSON_UNESCAPED_UNICODE);

exit;
