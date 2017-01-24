<?php 
/**
 *  exs.lv Android lietotnes API projekts.
 *
 *  Visi uz android.exs.lv veiktie pieprasījumi nonāk šeit.
 *  Fails atgriež atbildi JSON formātā.
 *
 *  Ieviests: 2014. gada pavasaris.
 */

require(API_PATH . '/shared/shared.functions.php');
require(API_PATH . '/shared/android.functions.php');

/*
|--------------------------------------------------------------------------
|   Pamatkonfigurācija.
|--------------------------------------------------------------------------
*/

// saraksts ar "sadaļām", kuras var pieprasīt caur adresi,
// piemēram, https://android.exs.lv/inbox/
$list_private_cats = array( // šīm pieeja, ja lietotājs ir autentificējies
    'random',
    'miniblogs',
    'groups',
    'inbox',
    'collections'
    // 'news'
);
$list_public_cats = array( // šīm pieeja vienmēr
    'auth',
    'collections'
);

// fiksē sadaļu, kas adresē pieprasīta
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
 *  $json = array(
 *      'state'     => string   // error/success
 *      'message'   => string,  // ziņa, kas lietotnē tiek izcelta, ja "state" == "error"
 *      'is_banned' => int,     // 0 - viss ok, 1 - ip liegums, 2 - profila liegums
 *      'is_online' => bool,    // statuss, kas apzīmē, vai lietotājs ir autorizēts
 *      'xsrf'      => string,  // anti-xsrf atslēga, kas pievienojama adrešu galā
 *      'response'  => array()  // veiktā pieprasījuma atbilde
 *  );
 */

 
// atgriežamā json objekta mainīgie;
// to saturs pēc nepieciešamības maināms katra apakšmoduļa iekšienē
$json_state     = 'success';
$json_message   = '';
$json_banned    = 0;
$json_page      = null;
$json_2fa       = false;


// primāri katrā pieprasījumā tiek noteikts, vai lietotājam ir IP liegums,
// un tikai pēc tam interesējas par autorizācijas statusu u.c. info
$ip_banned = $db->get_row("
	SELECT * FROM `banned` 
	WHERE `active` = 1 AND `ip` = '".sanitize($auth->ip)."' AND
	(`lang` = 0 OR `lang` = ".(int)$api_lang.")
	LIMIT 1
");

/*
|--------------------------------------------------------------------------
|   Pieprasījuma apstrāde.
|--------------------------------------------------------------------------
*/

// ja lietotājs lietotnē ir nonācis lieguma skatā, tas var pieprasīt svaigu
// info par lieguma statusu, lai noteiktu, vai tāds vēl pastāv, tāpēc šādai
// info ir jābūt noskaidrojamai vienmēr
if (isset($_GET['banstatus'])) {

	if ($ip_banned) {
		api_fetch_ban(1, $ip_banned);
	} else if ($auth->ok && !empty($busers) && !empty($busers[$auth->id])) { 
		api_fetch_ban(2);
	}

// lietotājs saņem info par IP liegumu un citam saturam nepiekļūst
} else if ($ip_banned) {
	api_fetch_ban(1, $ip_banned);
	if ($auth->ok) {
		$auth->logout();
	}
	
// publiskas sadaļas pieprasījums
} else if ($cat_public !== '') {
    
    if (!file_exists(API_PATH . '/api_android/' . $cat_public . '.php')) {
        api_log('Netika atrasts publiskas API sadaļas PHP fails.');
        api_error('Exs serverī ieperinājušās blusas. ;( Pacietību!');
    } else {
        include(API_PATH . '/api_android/' . $cat_public . '.php');
    } 
    
// autorizētu pieprasījumu apstrāde
} else if ($auth->ok) {

	// ja mistisku iemeslu dēļ lietotnē uzskata, ka lietotājs nav pieteicies,
	// bet serveris domā pretēji un atved šeit, labāk izautorizēt
	if (isset($_GET['login'])) {
		api_log('Kā pieteicies lietotājs centās pieteikties atkārtoti.');
        api_error('Darbība neizdevās! Mēģini vēlreiz.');
		$auth->logout();

	// primāri laikam jau ļaut izlogoties arī tad, ja ir profila liegums :)
	// bet lietotnē neesmu iestrādājis logout pogu lieguma skatā, mwhahaha
    } else if (isset($_GET['logout'])) {
		$auth->logout();

	// pārbauda, vai ir profila liegums, lai lietotnē varētu parādīt paziņojumu
	} else if (!empty($busers) && !empty($busers[$auth->id])) {      
		api_fetch_ban(2);

	// atvērs pieprasīto moduli un tajā izpildīs darbības
	} else {

        // 2-factor-authentication iespējots? jāpieprasa kods
        $request_2fa = false;
        if ($auth->auth_2fa && empty($_SESSION['2fa'])) {
            require(API_PATH.'/shared/shared.auth.php');
            $request_2fa = api_auth_2fa_request();
        }
        
        if ($request_2fa) {
            $json_2fa = true;
            // profila info, lai, piemēram, iegūtu lietotājvārdu un
            // avatara adresi, kuru parādīt 2fa koda ievades skatā
            api_append_profile_info();
        } else {
        
            if ($cat_private !== '' &&
                   file_exists(API_PATH . '/api_android/' . $cat_private . '.php')) {
                include(API_PATH . '/api_android/' . $cat_private . '.php');
                
            // šeit var nonākt mistiskās situācijās, kad kaut kas ar cepumiem nav
            // sasinhronizējies starp serveri un lietotni
            } else {
                api_log('Pieteicies lietotājs veica nezināmu pieprasījumu.');
                api_error('Kļūdains pieprasījums.');
            }
        }
    }

// neautorizēti var būt tikai autorizēšanās pieprasījumi
// TODO: dzēst, tiklīdz lietotnes v2.0 tiks laista dzīvajā
} else if (isset($_GET['login'])) {

	if (isset($_POST['username']) && isset($_POST['password'])) {
	
		$auth->login($_POST['username'], $_POST['password'], $auth->xsrf);
		
		if (!$auth->ok) {
			api_error('Nepareizi ievadīti piekļuves dati.');
		} else if (!empty($busers) && !empty($busers[$auth->id])) {
			api_fetch_ban(2);
		} else {
		
			// atzīmēs kā android lietotāju, lai saņemtu medaļu
			if ($auth->android_seen == 0) {
				$db->update('users', $auth->id, array(
					'android_seen' => 1
				));
				$auth->android_seen = 1;
			}
		
			api_append_profile_info();
		}
	}

// ja lietotājs pēc ilgākas pauzes atkal atver lietotni un sūta pieprasījumu,
// bet serveris jau dzēsis sesiju, nonāks šeit
} else {
	api_error('Lūdzu, autorizējies.');
}

/*
|--------------------------------------------------------------------------
|   Atbilde pieprasījumam.
|--------------------------------------------------------------------------
*/

$arr = array(
	'state'     => $json_state,
	'message'   => $json_message,
	'is_banned' => $json_banned,
	'is_online' => $auth->ok,
	'xsrf'      => api_make_xsrf(),
	'response'  => $json_page
);

if ($json_2fa) {
    $arr['2fa_required'] = true;
}

echo json_encode($arr, JSON_UNESCAPED_UNICODE);
exit;
