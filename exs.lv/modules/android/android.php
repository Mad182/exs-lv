<?php 
/**
 *  exs.lv Android lietotnes modulis
 *
 *  Apstrādā visus no Android saņemtos pieprasījumus.
 */

// submoduļos ir pārbaude, vai šāds mainīgais definēts,
// lai failus neskatītos pa tiešo
$sub_include = true;



/**
 *  Katram pieprasījumam, kas nonācis šajā modulī,
 *  uz lietotni atpakaļ atgriež JSON datus šādā formātā:
 *
 *  $json = array(
 *      'banned'    => int,     // 0 - viss ok, 1 - ip liegums, 2 - profila liegums
 *      'state'     => string   // error/success
 *      'message'   => string,  // ziņa, kas lietotnē tiek izcelta, ja "state" == "error"
 *      'is_online' => bool,    // statuss, kas apzīmē, vai lietotājs ir autorizēts
 *      'pagedata'  => array()  // veiktā pieprasījuma atbilde
 *  );
 */


// atgriežamā json objekta mainīgie;
// to saturs pēc nepieciešamības maināms katra apakšmoduļa iekšienē
$json_banned    = 0;
$json_state     = 'success';
$json_message   = '';
$json_page      = null;


// primāri ir noteikt, vai šai IP ir liegums neatkarīgi no tā, vai
// lietotājs ir autorizējies, vai nē
$ip_banned = $db->get_row("
    SELECT * FROM `banned` 
    WHERE 
        `active` = 1 AND 
        `ip` = '".sanitize($auth->ip)."'
    LIMIT 1
");

// pieprasījums pēc informācijas par uzlikto liegumu;
// šis nedrīkst atgriezt $json_state = 'error', citādi lietotne nevis
// pārbaudīs, vai lietotājs vēl ir bloķēts, bet gan izvadīs kļūdas paziņojumu
if (isset($_GET['viewcat']) && $_GET['viewcat'] == 'ban-info') {

    if ($auth->ok) {        
        if ($ip_banned) {
            $json_banned = 1;
            a_set_ban_info(1, $ip_banned);        
        } else if (!empty($busers) && !empty($busers[$auth->id])) {
            $json_banned = 2;
            a_set_ban_info(2);            
        }
    } else {        
        if ($ip_banned) {
            $json_banned = 1;
            a_set_ban_info(1, $ip_banned);
        }
    }

// bloķēta IP
} else if ($ip_banned) {

    // lietotnē lietotājs tiks pārvirzīts uz aktivitāti, kurā redzēs
    // paziņojumu ar lieguma datiem
    $json_banned = 1;
    a_set_ban_info(1, $ip_banned);    
    
// autorizētu pieprasījumu apstrāde
} else if ($auth->ok) {

    // ja mistisku iemeslu dēļ lietotnē uzskata, ka lietotājs nav pieteicies,
    // bet nonāk te, tad labāk izautorizēt
    if (isset($_GET['login'])) {
        $auth->logout();
        //TODO: log this, bet it kā lietotnē problēma novērsta
    }

    // primāri laikam jau ļaut izlogoties arī tad, ja ir profila liegums :)
    // bet lietotnē neesmu iestrādājis logout pogu lieguma skatā, mwhahaha
    if (isset($_GET['logout'])) {
        $auth->logout();        
        // šeit nav jēgas rūpēties par atbildi, jo lietotne tādu negaidīs

    // pārbauda, vai ir profila liegums, lai lietotnē varētu parādīt paziņojumu
	} else if (!empty($busers) && !empty($busers[$auth->id])) {
		$json_banned = 2;        
        a_set_ban_info(2);

    // atvērs pieprasīto moduli un tajā izpildīs darbības
	} else if (file_exists(CORE_PATH . '/modules/android/submodules/' . $category->textid . '.php')) {
		include(CORE_PATH . '/modules/android/submodules/' . $category->textid . '.php');
        
    // šeit var nonākt mistiskās situācijās, kad kaut kas ar cepumiem nav
    // sasinhronizējies starp serveri un lietotni
	} else {
        a_error('Kļūdains pieprasījums');
        // TODO: log this
    }

// neautorizētu pieprasījumu apstrāde
} else if (isset($_GET['login'])) {
    
    // lokālai testēšanai
    if (isset($mypasswd) && isset($auto_login) && $auto_login === true) {
        $auth->login('durvis', $mypasswd, $auth->xsrf);
        
        if (!$auth->ok) {
            a_error('Kļūdaini dati');

        } else if (!empty($busers) && !empty($busers[$auth->id])) {
            $json_banned = 2;
            a_set_ban_info(2);
        }

	} else if (isset($_POST['username']) && isset($_POST['password'])) {
    
		$auth->login($_POST['username'], $_POST['password'], $auth->xsrf);
        
        if (!$auth->ok) {
            a_error('Nepareizi ievadīti piekļuves dati');

        } else if (!empty($busers) && !empty($busers[$auth->id])) {
            $json_banned = 2;
            a_set_ban_info(2);
        }
    }

// citas situācijas, kādām teorētiski rasties nevajadzētu, ja vien android
// adreses nesāk rakstīt no datora, jo autorizācijas aktivitātē vienīgais
// pieprasījums tiek veikts ar pieteikšanās datiem ($_POST)
} else {
    a_error('Kļūdains pieprasījums');
}

$arr = array(
    'banned'    => $json_banned,
	'state'     => $json_state,
	'message'   => $json_message,
	'is_online' => $auth->ok,
	'pagedata'  => $json_page
);

echo json_encode($arr);
exit;
