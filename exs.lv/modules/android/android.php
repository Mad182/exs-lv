<?php 
/**
 *  Android lietotnes modulis
 *
 *  Apstrādā visus no Android saņemtos pieprasījumus
 */

$sub_include    = true;     // submoduļos ir pārbaude, vai šāds mainīgais definēts
$android_lang   = 1;        // nākotnē atbalstīs dažādus apakšprojektus


/**
 *  Katram pieprasījumam, kas nonācis šajā modulī,
 *  uz lietotni atpakaļ atgriež JSON datus šādā formātā:
 *
 *  $json = array(
 *      'state'     => string       // error/success
 *      'message'   => string,      // ziņa, kas lietotnē tiek izcelta, ja "state" == "error"
 *      'auth'      => bool,        // statuss, kas apzīmē, vai lietotājs ir autorizēts
 *      'userdata'  => array(),     // masīvs, kas satur datus par lietotāju (id, niks, līmenis, utt.)
 *      'pagedata'  => array()      // ar konkrēto sadaļu saistīta informācija
 *  );
 */


// json objekta mainīgie;
// to saturs maināms katra apakšmoduļa iekšienē
$json_state     = 'success';
$json_message   = '';
$json_user      = a_fetch_user();
$json_page      = null;



if ($auth->ok) {

    // ja submodulis ir norādīts un eksistē, to atver
    if (file_exists(CORE_PATH . '/modules/android/submodules/' . $category->textid . '.php')) {
        include(CORE_PATH . '/modules/android/submodules/' . $category->textid . '.php');
    } 
    
    else if (isset($_GET['logout'])) {
    
        $auth->logout();        
        $json_user = a_fetch_user();
    }
}
// vēlas autorizēties
else if (isset($_GET['login'])) {

    if (isset($_POST['username']) && isset($_POST['password'])) {
        $auth->login($_POST['username'], $_POST['password'], $auth->xsrf);
    }
    $json_user = a_fetch_user();
}



$arr = array(
    'state'     => $json_state,
    'message'   => $json_message,
    'auth'      => $auth->ok,
    'userdata'  => $json_user,
    'pagedata'  => $json_page
);

header('Content-Type: application/json');

echo json_encode($arr);
exit;
