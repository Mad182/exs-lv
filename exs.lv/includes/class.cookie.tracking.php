<?php

/**
 * Class cookieTracker
 *
 * Profilu sasaiste pēc cepumiem.
 */

class cookieTracker
{
    private $_cookiename;
    private $_securekey;
    private $_iv;
    private $_db;

    function __construct($name, $secret, $db)
    {
        $this->_cookiename = $name;
        $this->_securekey = $secret;
        $this->_iv = mcrypt_create_iv(32);
        $this->_db = $db;
    }

    /**
     * @method setCookie()
     *
     * Saglabā vai atjauno cepumu datus, ja cepums jau eksistē. Cepums tiek kriptēts ar vienkāršu 2 way encryption
     */
    function setCookie()
    {
        //saglabā vai atjauno cookie data. Neloģisks vārds apjukumam
        if(!isset($_COOKIE[$this->_cookiename])){
            $id = $this->cookieEncrypt($_SESSION['auth_id']);
            setcookie($this->_cookiename, $id, time()+(86400*365), "/");

        } else {
            //pārbaudam, vai cepumā jau nav lietotāja ID
            $ids_decrypted = $this->cookieDecrypt();
            $cookie_ids = explode(',', $ids_decrypted);

            if(!in_array(($_SESSION['auth_id']), $cookie_ids)){
                $cookie_crypted = $this->cookieEncrypt($ids_decrypted.','.$_SESSION['auth_id']);
                setcookie($this->_cookiename, $cookie_crypted, time()+(86400*365), "/");
                $_COOKIE[$this->_cookiename] = $cookie_crypted;
            }
        }
        $this->updateCookieDatabase();
    }

    /**
     * @method updateCookieDatabase()
     *
     * Atkriptē cepumu un ieraksta tā saturu datubāzē pretim saistītajiem lietotājiem
     */
    private function updateCookieDatabase()
    {
        //cookie pārbaude, darbojamies ar datubāzi
        if(isset($_COOKIE[$this->_cookiename])){
            $ids_decrypted = $this->cookieDecrypt();
            $cookie_ids = explode(',', $ids_decrypted);

            foreach($cookie_ids as $id){
                //iegūstam profila, kurš ir cepumā, piesaistītos profilus
                $connected_ids = explode(',', $this->_db->get_var("SELECT `connected_profiles` FROM `users` WHERE `id` = $id"));
                //piesaistam visus cepuma profilus, kuru datubāzē tieši šim profilam nav
                foreach($cookie_ids as $id_connected){
                    if(!in_array($id_connected, $connected_ids) && $id_connected != $id){
                        $id_connected = $this->_db->escape_string($id_connected);
                        $this->_db->query("UPDATE `users` SET `connected_profiles` = concat(`connected_profiles`, $id_connected, ',') WHERE `id` = $id");
                    }
                }
            }

        }
    }

    /**
     * @method cookieEncrypt()
     * @method cookieDecrypt()
     *
     * Cepumu kriptēšanas un atkriptēšanas metodes
     */

    private function cookieEncrypt($data)
    {
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->_securekey, $data, MCRYPT_MODE_ECB, $this->_iv));
    }

    private function cookieDecrypt()
    {
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->_securekey, base64_decode($_COOKIE[$this->_cookiename]), MCRYPT_MODE_ECB, $this->_iv));
    }

}