<?php
/**
 *  Parent klase MVC arhitektūras modeļiem
 *
 *  Modeļi, kas šo klasi atvasina, meklējami moduļu mapēs.
 */

class Model {
    
    /**
     *  Pievieno modelim atsauces uz atsevišķiem 
     *  projekta globālajiem mainīgajiem
     */
    public function __construct() {

        $globals = array(
            'db', 'auth', 'lang', 'debug', 'category', 'm'
        );
        foreach ($globals as $global) {
            global ${$global};
            $this->{$global} =& ${$global};
        }
    }
    
    /**
     *  Manuāla globālo mainīgo piesaiste no atvasinātajām klasēm
     *
     *  @param array $arr   masīvs ar mainīgo nosaukumiem
     */
    protected function globals($arr = null) {
        
        if (empty($arr) || !is_array($arr)) {
            return false;
        }
        
        foreach ($arr as $element) {
            global ${$element};
            $this->{$element} =& ${$element};
        }
    }
}
