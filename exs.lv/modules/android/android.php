<?php 
/**
 *  exs.lv Android lietotnes modulis
 *
 *  Apstrādā visus no Android saņemtos pieprasījumus.
 */

// submoduļos ir pārbaude, vai šāds mainīgais definēts,
// lai failus neskatītos pa tiešo
$sub_include = true;

// ja configdb.php failā $img_server tiek definēts, nenorādot protokolu,
// tas jāpievieno, lai Android atpazītu adreses
if (isset($img_server) && substr($img_server, 0, 2) === '//') {
	if (!empty($_SERVER['HTTPS'])) {
		$img_server = 'https:'.$img_server;
	} else {
		$img_server = 'http:'.$img_server;
	}
}

/**
 *  Katram pieprasījumam, kas nonācis šajā modulī,
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


// primāri katrā pieprasījumā tiek noteikts, vai lietotājam ir IP liegums,
// un tikai pēc tam interesējas par autorizācijas statusu u.c. info
$ip_banned = $db->get_row("
	SELECT * FROM `banned` 
	WHERE `active` = 1 AND `ip` = '".sanitize($auth->ip)."' AND
	(`lang` = 0 OR `lang` = ".(int)$api_lang.")
	LIMIT 1
");

// ja lietotājs lietotnē ir nonācis lieguma skatā, tas var pieprasīt svaigu
// info par lieguma statusu, lai noteiktu, vai tāds vēl pastāv, tāpēc šādai
// info ir jābūt noskaidrojamai vienmēr
if (isset($_GET['banstatus'])) {

	if ($ip_banned) {
		api_fetch_ban(1, $ip_banned);
	} else if ($auth->ok && !empty($busers) && !empty($busers[$auth->id])) { 
		api_fetch_ban(2);
	}

// lietotnē lietotājs tiks pārvirzīts uz aktivitāti, kurā redzēs
// paziņojumu par liegumu
} else if ($ip_banned) {
	api_fetch_ban(1, $ip_banned);
	if ($auth->ok) {
		$auth->logout();
	}
	
// autorizētu pieprasījumu apstrāde
} else if ($auth->ok) {

	// ja mistisku iemeslu dēļ lietotnē uzskata, ka lietotājs nav pieteicies,
	// bet serveris domā pretēji un atved šeit, labāk izautorizēt
	if (isset($_GET['login'])) {
		api_log('Kā pieteicies lietotājs centās pieteikties atkārtoti');
		$auth->logout();
	}

	// primāri laikam jau ļaut izlogoties arī tad, ja ir profila liegums :)
	// bet lietotnē neesmu iestrādājis logout pogu lieguma skatā, mwhahaha
	if (isset($_GET['logout'])) {
		$auth->logout();        
		// šeit nav jēgas rūpēties par atbildi, jo lietotne tādu negaidīs

	// pārbauda, vai ir profila liegums, lai lietotnē varētu parādīt paziņojumu
	} else if (!empty($busers) && !empty($busers[$auth->id])) {      
		api_fetch_ban(2);

	// atvērs pieprasīto moduli un tajā izpildīs darbības
	} else if (file_exists(CORE_PATH . '/modules/android/submodules/' . $category->textid . '.php')) {
		include(CORE_PATH . '/modules/android/submodules/' . $category->textid . '.php');
		
	// šeit var nonākt mistiskās situācijās, kad kaut kas ar cepumiem nav
	// sasinhronizējies starp serveri un lietotni
	} else {
		api_log('Pieteicies lietotājs veica nezināmu pieprasījumu');
		api_error('Kļūdains pieprasījums (#1)');
	}

// neautorizēti var būt tikai autorizēšanās pieprasījumi
} else if (isset($_GET['login'])) {

	if (isset($_POST['username']) && isset($_POST['password'])) {
	
		$auth->login($_POST['username'], $_POST['password'], $auth->xsrf);
		
		if (!$auth->ok) {
			api_error('Nepareizi ievadīti piekļuves dati');
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
	api_error('Lūdzu, autorizējies');
}

$arr = array(
	'state'     => $json_state,
	'message'   => $json_message,
	'is_banned' => $json_banned,
	'is_online' => $auth->ok,
	'xsrf'      => api_make_xsrf(),
	'response'  => $json_page
);

echo json_encode($arr);
exit;
