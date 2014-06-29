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
            'db', 'auth', 'lang', 'debug', 'category'
        );
        foreach ($globals as $global) {
            global ${$global};
            $this->{$global} =& ${$global};
        }
    }
}
