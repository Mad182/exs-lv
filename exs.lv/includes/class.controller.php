<?php
/**
 *  Parent klase MVC arhitektūras kontrolleriem
 *
 *  Kontrolleri, kas šo klasi atvasina, meklējami moduļu mapēs.
 */

class Controller {
    
    /**
     *  Globālie mainīgie, kas nepieciešami visos kontrolleros, definējami šeit.
     *  Specifiskus mainīgos var definēt moduļa kontrollera konstruktorā,
     *  no kura obligāti jāizsauc šīs parent klases konstruktors.
     */
    protected $db;
    protected $auth;
    protected $tpl;
    protected $category;
    protected $model;
    
    public function __construct() {
        global $db, $auth, $tpl, $category;

        $this->db = $db;
        $this->auth = $auth;
        $this->tpl = $tpl;
        $this->category = $category;

        $this->model = false;
    }
    
    /**
     *  Ielādē moduļa modeli
     *
     *  Nenorādot modeli, funkcija mēģina ielādēt models.php failu.
     *  Pieļaujamie modeļa nosaukuma varianti:
     *
     *      -   models
     *      -   other_models
     *      -   submodels/submodel_1 
     *  
     *  @param string|empty $string faila nosaukums
     *
     *  TODO: masīvs ar ielādēto modeļu nosaukumiem
     *  TODO: modeļu nosaukumi, ja faili ir vairāki
     */
    protected function load_model($string = '') {    
        require_once(CORE_PATH . '/includes/class.model.php');

        $model_name = 'models.php';
        if ($string !== '') {
            // ext jāpievieno galā, jo parametrā to var arī nenorādīt
            $model_name = str_replace('.php', '', $string) . '.php';
        }
        $path = CORE_PATH . '/modules/' . $this->category->module . '/';
        $file_name = $path . $model_name;

        if (file_exists($file_name)) {
            require($file_name);
        } else if (file_exists($path . 'models.php')) {
            require($path . 'models.php');
        } else {
            die('Ooooops! Sistēmas kļūda. :)');
        }
        
        // izveido modeļa objektu
        $class_name = escape_classname($this->category->module);
        if ($class_name === false) {
            die('Ooooops! Sistēmas kļūda. :)');
        }
        $class_name = 'Model_' . $class_name;
        
        $this->model = new $class_name();
    }
}
