<?php 
/**
 *  Android lietotnes modulis
 *
 *  Apstrādā visus no Android saņemtos pieprasījumus.
 */

$sub_include    = true;     // submoduļos ir pārbaude, vai šāds mainīgais definēts
$submodule      = '';       // iekļaujamais submodulis

// izvēlas īsto submoduli
if (isset($_GET['var1'])) {

    // autorizācija
    if ($_GET['var1'] == 'login') {
        $submodule = 'login';
    }
    // rakstu skatīšanās
    else if ($_GET['var1'] == 'page') {
        $submodule = 'page';
    }
    
}


// ja submodulis ir norādīts un eksistē, to atver
if ($submodule != ''  && file_exists(CORE_PATH . '/modules/android/submodules/' . $submodule . '.php')) {
    include(CORE_PATH . '/modules/android/submodules/' . $submodule . '.php');
} 
// citos gadījumos izvada muļķības
else {
    echo json_encode( array(
        'state'     => 'error',
        'message'   => 'Hello World!'
    )); 
}
exit;