<?php 
/**
 *  exs.lv iOS lietotnes API projekts.
 *
 *  Visi uz ios.exs.lv veiktie pieprasījumi nonāk šeit.
 *  Fails atgriež atbildi JSON formātā.
 *
 *  Ieviests: 2016. gada 14. jūnijs.
 */

require(API_PATH . '/shared/shared.api.php');
require(API_PATH . '/api_ios/functions.ios.php');

/*
|--------------------------------------------------------------------------
|   Pamatkonfigurācija.
|--------------------------------------------------------------------------
*/

// saraksts ar "sadaļām", kuras var pieprasīt caur adresi,
// piemēram, https://ios.exs.lv/inbox/
$category_list = array(
    'random',
    'profiles',
    'miniblogs',
    'groups',
    'inbox',
    'collections'
    // 'news'
);
$category = '';
if ($var0 && in_array($var0, $category_list)) {
    $category = $var0;
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
    
// logout pieprasījums
} else if ($var0 === 'letmeout') {
    // ios.exs.lv/letmeout
    
    if ($auth->ok) {
        $auth->logout();
        api_info('Lietotājs no sistēmas izautorizēts.');
    } else {
        api_log('Neautentificējies lietotājs centās iziet no sistēmas.');
        api_error('Lietotājs nemaz nav autentificējies.');
    }
    
// login pieprasījums
} else if ($var0 === 'letmein') {
    // ios.exs.lv/letmein
    
    // ja mistisku iemeslu dēļ lietotnē uzskata, ka lietotājs nav pieteicies,
	// bet serveris domā pretēji, labāk izautorizēt
    if ($auth->ok) {
        api_log('Autentificējies lietotājs centās autentificēties vēlreiz. Veikta automātiska izlogošana.');
        $auth->logout();
    } 
    
    if (isset($_POST['username']) && isset($_POST['password'])) {

        $auth->login($_POST['username'], $_POST['password'], $auth->xsrf);
        
        if (!$auth->ok) {
            api_log('Neizdevies autentificēšanās mēģinājums - kļūdaini piekļuves dati.');
            api_error('Kļūdaini norādīti piekļuves dati.');
        } else if (!empty($busers) && !empty($busers[$auth->id])) {
            api_log('Pēc autentificēšanās konstatēts, ka lietotājam ir profila liegums.');
            api_fetch_ban(2);
        } else { // autentificēšanās OK
        
            // atzīmē kā iOS lietotāju, lai saņemtu medaļu (kad tāda būs)
            if ($auth->ios_seen == 0) {
                $db->update('users', $auth->id, array(
                    'ios_seen' => 1
                ));
                $auth->ios_seen = 1;
            }
        
            // pēc veiksmīgas autentificēšanās atbildei pievieno
            // svaigāko lietotāja profila informāciju
            api_append_profile_info();
        }
    } else {
        api_log('Veicot autentificēšanos, nav saņemts lietotājvārds un/vai parole.');
        api_error('Kļūdaini norādīti piekļuves dati.');
    }
    
// autorizētu pieprasījumu apstrāde
} else if ($auth->ok) {
 
	// pārbauda, vai lietotājam ir profila liegums
	if (!empty($busers) && !empty($busers[$auth->id])) {      
		api_fetch_ban(2);

	// atver pieprasīto moduli un tajā izpilda darbības
	} else if ($category !== '' &&
               file_exists(API_PATH . '/api_ios/' . $category . '.php')) {
		include(API_PATH . '/api_ios/' . $category . '.php');

    // citu sadaļu pieprasījumi
	} else {
        if ($var0 !== '') { // 'index' sadaļu jeb "/" adresi nelogos
            api_log('Pieprasīta neeksistējoša sadaļa.');
        }
		api_info('Hello world!');
	}

} else {
    // ja lietotājs pēc ilgākas pauzes atkal atver lietotni un
    // sūta pieprasījumu, bet serveris jau dzēsis sesiju, nonāk šeit
    api_log('Neatbilstošs pieprasījums no neautentificēta lietotāja.');
	api_error('Lūdzu, autorizējies!');
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
