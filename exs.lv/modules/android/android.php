<?php 
/**
 *  Android lietotnes modulis
 *
 *  Apstrādā visus no Android saņemtos pieprasījumus.
 */

$sub_include    = true;     // submoduļos ir pārbaude, vai šāds mainīgais definēts
$submodule      = '';       // iekļaujamais submodulis


/**
 *  Katram pieprasījumam, kas nonācis šajā modulī,
 *  uz lietotni atpakaļ atgriež JSON datus šādā formātā:
 *
 *  $json = array(
 *      'state'     => string       // error/success (vēl pielietojumu izdomāšu)
 *      'message'   => string,      // ziņa, kas lietotnē tiek izcelta, ja "state" == "error"
 *      'auth'      => bool,        // statuss, kas apzīmē, vai lietotājs ir autorizēts
 *      'userdata'  => array(),     // masīvs, kas satur datus par lietotāju (id, niks, līmenis, utt.)
 *      'pagedata'  => array()      // ar konkrēto sadaļu saistīta informācija
 *  );
 */


// json objekta mainīgie
$json_state     = 'success';
$json_message   = '';
$json_user      = array('id' => $auth->id, 'nick' => $auth->nick, 'level' => $auth->level);
$json_page      = null;



if ($auth->ok) {

    // ja submodulis ir norādīts un eksistē, to atver
    if (file_exists(CORE_PATH . '/modules/android/submodules/' . $category->textid . '.php')) {
        include(CORE_PATH . '/modules/android/submodules/' . $category->textid . '.php');
    } 
    
    else if (isset($_GET['logout'])) {
    
        $auth->logout();
        
        $json_user = array('id' => $auth->id, 'nick' => $auth->nick, 'level' => $auth->level);
    }
}
// vēlas autorizēties
else if (isset($_GET['login'])) {

    if (isset($_POST['username']) && isset($_POST['password'])) {
        $auth->login($_POST['username'], $_POST['password'], $auth->xsrf);
    }

    $json_user = array('id' => $auth->id, 'nick' => $auth->nick, 'level' => $auth->level);
}



$arr = array(
    'state'     => $json_state,
    'message'   => $json_message,
    'auth'      => $auth->ok,
    'userdata'  => $json_user,
    'pagedata'  => $json_page
);

echo json_encode($arr);
exit;
