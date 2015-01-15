<?php

/**
 * Steam autorizācija
 * lai varētu ievākt datus par steam spēlētājiem
 */
if ($auth->ok === true) {

    //jau ir steam_id
    if (!empty($auth->steam_id)) {
        $tpl->newBlock('steam-success');
    } else {
        require_once(LIB_PATH . '/openid/openid.php');
        $openid = new LightOpenID($steam_domain_name);


        if (!$openid->mode) {

//            Redirekts uz steam login lapu
            if (isset($_GET['login'])) {
                $openid->identity = 'http://steamcommunity.com/openid';
                header('Location: ' . $openid->authUrl());
            }

            //athentifikācijas templeits
            $tpl->newBlock('steam-auth');

        } elseif ($openid->mode == 'cancel') {
//      Profilu savienošana atcelta, rādam paziņojumu:
            $tpl->newBlock('steam-auth-cancelled');
        } else {

//          Validācija un piesaista lietotājam steam_id
            if ($openid->validate()) {
                $id = $openid->identity;
                $ptn = "/^http:\/\/steamcommunity\.com\/openid\/id\/(7[0-9]{15,25}+)$/";
                preg_match($ptn, $id, $matches);

                $db->update('users', $auth->id, array(
                    'steam_id' => $matches[1]
                ));

                $tpl->newBlock('steam-success');
            } else {
                $tpl->newBlock('steam-auth-error');
            }
        }
    }
} else {
    //login logs
    $tpl->newBlock('error-nologin');
}
