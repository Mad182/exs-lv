<?php
/**
 *  Kontrolleru parent klase
 *
 *  Visi pārējie kontrolleri šo atvasina.
 *  Globālie mainīgie, kas nepieciešami kontrolleros, definējami tikai šeit.
 */

class Controllers {
    
    protected $db;
    protected $auth;
    protected $tpl;
    protected $model;
    
    public function __construct() {
        global $db, $auth, $tpl, $model;

        $this->db = $db;
        $this->auth = $auth;
        $this->tpl = $tpl;
        $this->model = $model;
    }
}
