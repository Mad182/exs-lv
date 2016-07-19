<?php
/**
 *  API autentificēšanās sadaļā izmantotās funkcijas.
 */
 
/**
 *  Autentificē lietotāju.
 */
function api_auth_login() {
    global $db, $auth, $busers, $lang;
	
    // ja mistisku iemeslu dēļ lietotnē uzskata, ka lietotājs nav pieteicies,
	// bet serveris domā pretēji, labāk izautorizēt
    // TODO: xsrf pārbaude...
    if ($auth->ok) {
        api_log('Autentificējies lietotājs centās autentificēties vēlreiz. Veikta automātiska izlogošana.');
        $auth->logout();
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
    }
}

/**
 *  Izautorizē lietotāju.
 */
function api_auth_logout() {
    global $auth;
    
    if ($auth->ok) {
        $auth->logout();
        api_info('Lietotājs no sistēmas izautorizēts.');
    } else {
        api_log('Neautentificējies lietotājs centās iziet no sistēmas.');
        api_error('Lietotājs nemaz nav autentificējies.');
    }
}
