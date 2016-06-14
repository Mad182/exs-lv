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
 *      'state'     => string    // "error" vai "success"
 *      'message'   => string,   // kļūdas paziņojums, ja "state" === "error"
 *      'is_banned' => int,      // 0 - viss ok, 1 - ip liegums, 2 - profila liegums
 *      'is_online' => bool,     // statuss, kas apzīmē, vai lietotājs ir autentificēts
 *      'xsrf'      => string,   // anti-xsrf atslēga, kas pievienojama adrešu galā
 *      'response'  => array()   // detalizētāks saturs kā atbilde pieprasījumam
 *  );
 */


// atgriežamā json objekta mainīgie;
// to saturs pēc nepieciešamības maināms katra apakšmoduļa iekšienē
$json_state     = 'success';
$json_message   = '';
$json_banned    = 0;
$json_page      = null;


// dati par lietotāja IP liegumu, ja tādi ir
$ip_banned = $db->get_row("
	SELECT * FROM `banned` 
	WHERE `active` = 1 AND `ip` = '".sanitize($auth->ip)."' AND
	(`lang` = 0 OR `lang` = ".(int)$api_lang.")
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
		a_fetch_ban(1, $ip_banned);
	} else if ($auth->ok && !empty($busers) && !empty($busers[$auth->id])) {
		a_fetch_ban(2);
	} else {
        a_info('Lietotājam nav liegta piekļuve exs.lv');
    }

// pārbauda, vai lietotājam ir IP liegums
} else if ($ip_banned) {

	a_fetch_ban(1, $ip_banned);
	if ($auth->ok) {
		$auth->logout();
	}
    
// logout pieprasījums
} else if ($category->textid == 'letmeout') {
    // ios.exs.lv/letmeout
    
    if ($auth->ok) {
        $auth->logout();
    } else {
        a_log('Neautentificējies lietotājs centās iziet no sistēmas');
        a_error('Lietotājs nemaz nav autentificējies');
    }
    
// login pieprasījums
} else if ($category->textid === 'letmein') {
    // ios.exs.lv/letmein
    
    // ja mistisku iemeslu dēļ lietotnē uzskata, ka lietotājs nav pieteicies,
	// bet serveris domā pretēji un atved šeit, labāk izautorizēt
    if ($auth->ok) {
        a_log('Autentificējies lietotājs centās autentificēties vēlreiz. Veikta automātiska izlogošana');
        $auth->logout();
    } 
    
    if (isset($_POST['username']) && isset($_POST['password'])) {

        $auth->login($_POST['username'], $_POST['password'], $auth->xsrf);
        
        if (!$auth->ok) {
            a_log('Neizdevies autentificēšanās mēģinājums - kļūdaini piekļuves dati');
            a_error('Kļūdaini norādīti piekļuves dati');
        } else if (!empty($busers) && !empty($busers[$auth->id])) {
            a_log('Pēc autentificēšanās konstatēts, ka lietotājam ir profila liegums');
            a_fetch_ban(2);
        } else { // autentificēšanās OK
        
            // atzīmē kā iOS lietotāju, lai saņemtu medaļu
            if ($auth->ios_seen == 0) {
                $db->update('users', $auth->id, array(
                    'ios_seen' => 1
                ));
                $auth->ios_seen = 1;
            }
        
            // pēc veiksmīgas autentificēšanās atbildei pievienojama
            // svaigāko lietotāja profila informāciju
            a_append_profile_info();
        }
    } else {
        a_log('Veicot autentificēšanos, nav saņemts lietotājvārds un/vai parole');
        a_error('Kļūdaini norādīti piekļuves dati');
    }
    
// autorizētu pieprasījumu apstrāde
} else if ($auth->ok) {
 
	// pārbauda, vai lietotājam ir profila liegums
	if (!empty($busers) && !empty($busers[$auth->id])) {      
		a_fetch_ban(2);

	// atver pieprasīto moduli un tajā izpilda darbības
	} else if ($category->textid !== 'index' &&
               file_exists(CORE_PATH . '/modules/ios/submodules/' . $category->textid . '.php')) {
		include(CORE_PATH . '/modules/ios/submodules/' . $category->textid . '.php');
		
	// šeit var nonākt mistiskās situācijās, kad kaut kas ar cepumiem nav
	// sasinhronizējies starp serveri un lietotni
	} else {
		a_log('Pieprasīta neeksistējoša sadaļa');
		a_error('Pieprasīti dati ar neeksistējošu adresi');
	}

} else {
    // ja lietotājs pēc ilgākas pauzes atkal atver lietotni un
    // sūta pieprasījumu, bet serveris jau dzēsis sesiju, nonāk šeit
    a_log('Neatbilstošs pieprasījums no neautentificēta lietotāja');
	a_error('Lūdzu, autorizējies');
}


/*
|--------------------------------------------------------------------------
|   Atbilde pieprasījumam.
|--------------------------------------------------------------------------
*/

// atgriež atbildi uz pieprasījumu JSON objekta formā
echo json_encode(array(
	'state'     => $json_state,
	'message'   => $json_message,
	'is_banned' => $json_banned,
	'is_online' => $auth->ok,
	'xsrf'      => a_make_xsrf(),
	'response'  => $json_page
));

// pēc šī faila vairs nekādu pārbaužu un darbību nebūs
exit;
