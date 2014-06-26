<?php
/**
 *  Parent klase MVC arhitektūras modeļiem
 *
 *  Modeļi, kas šo klasi atvasina, meklējami moduļu mapēs.
 */

class Model {
    
    /**
     *  Globālie mainīgie, kas nepieciešami visos modeļos, definējami šeit.
     *  Specifiskus mainīgos var definēt moduļa modeļa konstruktorā,
     *  no kura obligāti jāizsauc šīs parent klases konstruktors.
     */
    protected $db;
    protected $auth;
    protected $tpl;
    
    public function __construct() {
        global $db, $auth, $tpl;

        $this->db = $db;
        $this->auth = $auth;
        $this->tpl = $tpl;
    }
}
