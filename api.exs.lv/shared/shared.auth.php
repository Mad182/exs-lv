<?php
/**
 *  API autentificēšanās sadaļā izmantotās funkcijas.
 */
 
/**
 *  Autentificē lietotāju.
 */
function api_auth_login() {
    global $db, $auth, $busers, $lang;
    global $json_2fa;
    
    // no TOR autentificēties nebūs ļauts
    if ($auth->is_tor_exit()) {
        api_log('Neizdevies login mēģinājums (IS_TOR_EXIT = true).');
        $auth->logout();
        api_error('Pieprasījumi no šīs IP adreses bloķēti.');
        return;
    }

    // gadījums, kad lietotne uzskata, ka lietotājs nav pieteicies,
    // un cenšas autentificēt, bet serveris domā pretēji
    if ($auth->ok) {
        api_log('Autentificējies lietotājs centās autentificēties vēlreiz.');
        api_append_profile_info();
        return;
    }

    if (!isset($_POST['username']) || !isset($_POST['password'])) {
        api_log('Veicot autentificēšanos, nav saņemts lietotājvārds un/vai parole.');
        api_error('Kļūdaini norādīti piekļuves dati.');
        return;
    }

    $auth->login($_POST['username'], $_POST['password'], $auth->xsrf);
    
    if (!$auth->ok) {
        api_log('Neizdevies autentificēšanās mēģinājums - kļūdaini piekļuves dati.');
        api_error('Kļūdaini norādīti piekļuves dati.');
    } else if (!empty($busers) && !empty($busers[$auth->id])) {
        api_log('Pēc autentificēšanās konstatēts, ka lietotājam ir profila liegums.');
        api_fetch_ban(2);
    } else { // autentificēšanās OK
    
        // atzīmē kā app lietotāju, lai saņemtu medaļu
        if ($lang === 4 && $auth->ios_seen == 0) {
            $db->update('users', $auth->id, array(
                'ios_seen' => 1
            ));
            $auth->ios_seen = 1;
        } else if ($lang === 2 && $auth->android_seen == 0) {
            $db->update('users', $auth->id, array(
                'android_seen' => 1
            ));
            $auth->android_seen = 1;
        }
    
        // pēc veiksmīgas autentificēšanās atbildei pievieno
        // svaigāko lietotāja profila informāciju
        api_append_profile_info();
        
        // ja lietotājam iespējots 2fa, jāpieprasa kods
        if ($auth->auth_2fa && empty($_SESSION['2fa'])) {
            $json_2fa = true;
        }
    }
}

/**
 *  Izautorizē lietotāju.
 */
function api_auth_logout() {
    global $auth;
    
    if (!api_check_xsrf()) {
		api_error('no hacking, pls');
		api_log('Mēģinot izautorizēties, konstatēts XSRF uzbrukums.');
	} else if ($auth->ok) {
        $auth->logout();
        api_info('Lietotājs no sistēmas izautorizēts.');
    } else {
        api_log('Neautentificējies lietotājs centās iziet no sistēmas.');
        api_error('Lietotājs nemaz nav autentificējies.');
    }
}

/**
 *  Pārbauda, vai lietotājam ir jāliek ievadīt 2fa kods, vai tomēr
 *  ierīce atrodama starp tām, kuras lietotājs vēlējies "atcerēties".
 */
function api_auth_2fa_request() {
    global $db, $auth, $json_2fa;
    
    // atlasa iepriekš saglabātās ierīces
    $check_existing = $db->get_results("
        SELECT `cookie`, `token` FROM `tfa_whitelist`
        WHERE `user_id` = ".$auth->id
    );
    
    $device_found = false;
    
    // starp visām saglabātajām ierīcēm meklē tādu, kas sakrīt
    // ar šobrīd izmantoto (pārbaudot cepumu)
	if (!empty($check_existing)) {
		foreach ($check_existing as $device) {
			if(!empty($_COOKIE[$device->cookie]) &&
                  $_COOKIE[$device->cookie] === $device->token) {
				$_SESSION['2fa'] = 1;
                $device_found = true;
			}
		}
	}
    
    // neatrodot iepriekš saglabātu šādu ierīci, norādīs,
    // ka lietotnei jāliek lietotājam ievadīt kodu no Google Authenticator
    if ($device_found === false) {
        $json_2fa = true;
    }
}

/**
 *  Apstrādā pieprasījumu, kurā no lietotāja tiek (cerams)
 *  saņemts 2fa kods.
 */
function api_auth_accept_2fa() {
    global $db, $auth;
    
    if (!$auth->auth_2fa) {
        api_error('Profilam 2fa nav iespējots!');
        api_log('2fa pieprasījums profilam, kuram 2fa nav iespējots.');
        return;
    }
    
    if (!api_check_xsrf()) {
		api_error('no hacking, pls');
		api_log('Iesūtot 2fa kodu, konstatēts XSRF uzbrukums.');
        return;
	} else if (!isset($_POST['2fa_code'])) {
        api_error('Kļūdains 2fa kods!');
        api_log('Nav saņemts 2fa kods.');
        return;
    }
    
    // pārbauda, vai iesūtītais kods ir pareizs
    $ga = new PHPGangsta_GoogleAuthenticator();
	$secret = $auth->auth_secret;
    $checkResult = $ga->verifyCode($secret, $_POST['2fa_code'], 4);
    
    if (!$checkResult) {
        api_error('Kļūdains 2fa kods!');
        api_log('Saņemtais 2fa kods nav pareizs.');
        return;
    }

    $_SESSION['2fa'] = 1;

    // ja lietotājs vēlas, lai viņa izmantoto ierīci "atceras" un
    // kods nebūtu jāievada pārāk bieži, pievieno vērtību cepumos
    if (!empty($_POST['2fa_remember'])) {

        $cookie = md5(uniqid());
        $token = md5(uniqid() . $auth->xsrf);

        $db->insert('tfa_whitelist', array(
            'user_id' => $auth->id,
            'ip' => $auth->ip,
            'cookie' => $cookie,
            'token' => $token,
            'created' => 'NOW()',
            'modified' => 'NOW()'
        ));
 
        setcookie($cookie, $token, time()+2592000, '/', '.exs.lv', 1, 1);
    }
    
    api_info('Autentificēšanās ar 2fa veiksmīga!');
}
