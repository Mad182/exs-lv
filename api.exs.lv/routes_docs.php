<?php
/**
 *  exs.lv API dokumentācijas lapa.
 *
 *  API dokumentācijas moduļi:
 *    1. Android lietotne (nav uzrakstīta, paslēpta)
 *    2. iOS lietotne
 *
 *  Ieviesta: 2016. gada jūnijs.
 */

// pagaidām lapa pieejama tikai pāris lietotājiem:
// mad, burvis, svens, pankijs, viesty, alberts
$devs = array(1, 115, 29176, 18865, 2145, 4506);
if (!$auth->ok || !in_array($auth->id, $devs)) {
    redirect('https://exs.lv');
    exit;
}

// sāna navigācija
switch ($var0) {
    case 'a': // rādīs android
        $tpl->assign('active-0', 'class="is-active" ');
        // $tpl->newBlock('android-logo');
        $tpl->newBlock('android-navig');        
        break;
    default: // rādīs ios
        $tpl->assign('active-1', 'class="is-active" ');
        // $tpl->newBlock('ios-logo');
        $tpl->newBlock('ios-navig');
        break;
}

// ielasa un parāda Android moduļa sadaļas saturu
if ($var0 === 'a') {    

    $filename = 'intro.html';
    if (is_string($var1)) {
        switch ($var1) {
            case 'miniblogs':
                $filename = 'miniblogs.html';
                break;
            case 'groups':
                $filename = 'groups.html';
                break;
            case 'messages':
                $filename = 'messages.html';
                break;
            case 'other':
                $filename = 'other.html';
                break;
            default:
                $filename = 'intro.html';
                break;
        }
    }
    
    $content = '';
    if (!empty($filename) && file_exists(API_PATH.'/api_docs/html_android/'.$filename)) {
        $content = file_get_contents(API_PATH.'/api_docs/html_android/'.$filename);
    }
    
    $tpl->assignGlobal(array(
        'page_content' => $content,
        'active-'.str_replace('.html', '', $filename) => 'is_active'
    ));
}

// ielasa un parāda iOS moduļa sadaļas saturu
else {    

    $filename = 'intro.html';
    if (is_string($var1)) {
        switch ($var1) {
            case 'miniblogs':
                $filename = 'miniblogs.html';
                break;
            case 'groups':
                $filename = 'groups.html';
                break;
            case 'messages':
                $filename = 'messages.html';
                break;
            case 'other':
                $filename = 'other.html';
                break;
            default:
                $filename = 'intro.html';
                break;
        }
    }
    
    $content = '';
    if (!empty($filename) && file_exists(API_PATH.'/api_docs/html_ios/'.$filename)) {
        $content = file_get_contents(API_PATH.'/api_docs/html_ios/'.$filename);
    }
    
    $tpl->assignGlobal(array(
        'page_content' => $content,
        'active-'.str_replace('.html', '', $filename) => 'is_active'
    ));    
}

$tpl->assignGlobal(array(
	'static-server' => $static_server
));


// izprintē šī moduļa jeb lapas saturu
$tpl->printToScreen();
