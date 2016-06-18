<?php 
/**
 *  exs.lv iOS lietotnes modulis.
 *  Apstrādā visus no iOS lietotnes saņemtos pieprasījumus.
 *
 *  Ieviests: 14.06.2016. (@burvis)
 */
 
/*
|--------------------------------------------------------------------------
|   Pamatkonfigurācija.
|--------------------------------------------------------------------------
*/

// submoduļos ir pārbaude, vai šāds mainīgais definēts,
// lai failus neskatītos pa tiešo
$sub_include = true;

// $_GET mainīgo shortcuti
// TODO: sarakstīt jau globālā exs līmenī
/*$var1 = (isset($_GET['var1']) ? $_GET['var1'] : false);
$var2 = (isset($_GET['var2']) ? $_GET['var2'] : false);
$var3 = (isset($_GET['var3']) ? $_GET['var3'] : false);*/

// ja configdb.php failā $img_server tiek definēts, nenorādot protokolu,
// protokols jāpievieno, lai iOS atpazītu adreses
if (isset($img_server) && substr($img_server, 0, 2) === '//') {
	if (!empty($_SERVER['HTTPS'])) {
		$img_server = 'https:'.$img_server;
	} else {
		$img_server = 'http:'.$img_server;
	}
}

/**
 *  Katrs pieprasījums, kas nonācis šajā modulī,
 *  atpakaļ saņem atbildi šādā JSON masīva formātā:
 *
 *  $json = array(
 *      'success'       => bool      // vai pieprasījums bija veiksmīgs?
 *      'message'       => string,   // kļūdas paziņojums, ja "state" === "error"
 *      'ban_type'      => int,      // 0 - viss ok, 1 - ip liegums, 2 - profila liegums
 *      'logged_in'     => bool,     // statuss, kas apzīmē, vai lietotājs ir autentificēts
 *      'xsrf_token'    => string,   // anti-xsrf atslēga, kas pievienojama adrešu galā
 *      'response'      => array()   // detalizētāks saturs kā atbilde pieprasījumam
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
	(`lang` = 5 OR `lang` = ".(int)$api_lang.")
	LIMIT 1
");


/*
|--------------------------------------------------------------------------
|   Pieprasījumu pārvaldība.
|--------------------------------------------------------------------------
*/

// info par liegumu jāvar noskaidrot jebkurā brīdī, tāpēc pirmā pārbaude
if ($category->textid === 'ban_details') {
    // ios.exs.lv/ban_details

	if ($ip_banned) {
		api_fetch_ban(1, $ip_banned);
	} else if ($auth->ok && !empty($busers) && !empty($busers[$auth->id])) {
		api_fetch_ban(2);
	} else {
        api_info('Lietotājam nav liegta piekļuve exs.lv');
    }

// pārbauda, vai lietotājam ir IP liegums
} else if ($ip_banned) {

	api_fetch_ban(1, $ip_banned);
	if ($auth->ok) {
		$auth->logout();
	}
    
// logout pieprasījums
} else if ($category->textid == 'letmeout') {
    // ios.exs.lv/letmeout
    
    if ($auth->ok) {
        $auth->logout();
    } else {
        api_log('Neautentificējies lietotājs centās iziet no sistēmas');
        api_error('Lietotājs nemaz nav autentificējies');
    }
    
// login pieprasījums
} else if ($category->textid === 'letmein') {
    // ios.exs.lv/letmein
    
    // ja mistisku iemeslu dēļ lietotnē uzskata, ka lietotājs nav pieteicies,
	// bet serveris domā pretēji, labāk izautorizēt
    if ($auth->ok) {
        api_log('Autentificējies lietotājs centās autentificēties vēlreiz. Veikta automātiska izlogošana');
        $auth->logout();
    } 
    
    if (isset($_POST['username']) && isset($_POST['password'])) {

        $auth->login($_POST['username'], $_POST['password'], $auth->xsrf);
        
        if (!$auth->ok) {
            api_log('Neizdevies autentificēšanās mēģinājums - kļūdaini piekļuves dati');
            api_error('Kļūdaini norādīti piekļuves dati');
        } else if (!empty($busers) && !empty($busers[$auth->id])) {
            api_log('Pēc autentificēšanās konstatēts, ka lietotājam ir profila liegums');
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
        api_log('Veicot autentificēšanos, nav saņemts lietotājvārds un/vai parole');
        api_error('Kļūdaini norādīti piekļuves dati');
    }
    
// autorizētu pieprasījumu apstrāde
} else if ($auth->ok) {
 
	// pārbauda, vai lietotājam ir profila liegums
	if (!empty($busers) && !empty($busers[$auth->id])) {      
		api_fetch_ban(2);
        
    // sākotnējiem izstrādes testiem...
    } else if (isset($_GET['welcome'])) {
        api_append(array('message' => 'Hello World!'));
        api_append_profile_info();

	// atver pieprasīto moduli un tajā izpilda darbības
	} else if ($category->textid !== 'index' &&
               file_exists(CORE_PATH . '/modules/ios/submodules/' . $category->textid . '.php')) {
		include(CORE_PATH . '/modules/ios/submodules/' . $category->textid . '.php');
		
	// šeit var nonākt mistiskās situācijās, kad kaut kas ar cepumiem nav
	// sasinhronizējies starp serveri un lietotni
	} else {
		api_log('Pieprasīta neeksistējoša sadaļa');
		api_error('Pieprasīti dati ar nepareizu adresi');
	}

} else {
    // ja lietotājs pēc ilgākas pauzes atkal atver lietotni un
    // sūta pieprasījumu, bet serveris jau dzēsis sesiju, nonāk šeit
    api_log('Neatbilstošs pieprasījums no neautentificēta lietotāja');
	api_error('Lūdzu, autorizējies');
}


/*
|--------------------------------------------------------------------------
|   Atbilde pieprasījumam.
|--------------------------------------------------------------------------
*/

// atgriež atbildi uz pieprasījumu JSON objekta formā
echo json_encode(array(
	'success'    => $json_success,
	'message'    => $json_message,
	'ban_type'   => $json_banned,
	'logged_in'  => $auth->ok,
	'xsrf_token' => api_make_xsrf(),
	'response'   => $json_page
));

// pēc šī faila vairs nekādu pārbaužu un darbību nebūs
exit;
