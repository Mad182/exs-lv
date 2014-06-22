<?php
/**
 *  Modeļu parent klase
 *
 *  Visi pārējie modeļi šo atvasina.
 *  Globālie mainīgie, kas nepieciešami modeļos, definējami tikai šeit,
 *  ja vien tie nav pārāk specifiski konkrētajam modelim.
 */

class Models {
    
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
