<?php
/**
 *  Parent klase MVC arhitektūras kontrolleriem
 *
 *  Kontrolleri, kas šo klasi atvasina, meklējami moduļu mapēs.
 *  Caur šo klasi pēc vajadzības notiek arī modeļa ielāde.
 */

class Controller {

    /**
     *  Globālie mainīgie, kas nepieciešami visos kontrolleros, definējami šeit.
     *  Specifiskus mainīgos var definēt moduļa kontrollera konstruktorā,
     *  no kura tad obligāti jāizsauc šīs parent klases konstruktors.
     */
    protected $db;
    protected $auth;
    protected $view;
    protected $category;
    protected $debug;
    protected $tpl_options;

    // mainīgais atsauksies uz ielādēto modeli
    protected $model;
    
    public function __construct() {
        global $db, $auth, $tpl, $category, $debug, $tpl_options;

        $this->model = false;

        $this->db =& $db;
        $this->auth =& $auth;
        $this->view =& $tpl;
        $this->category =& $category;
        $this->debug =& $debug;
        $this->tpl_options =& $tpl_options;
    }
    
    
    /**
     *  Ielādē moduļa mapē esošu template failu
     *
     *  @param string $file     faila nosaukums
     *  @return TemplatePower   template objekts
     */
    protected function view($file = '') {
        
        $file = trim($file);        
        if ($file == '') $this->display_error('Template fails neeksistē');

        $file = CORE_PATH.'/modules/'.$this->category->module.'/'.$file.'.tpl';
        
        if (!file_exists($file)) {
            return false;
        }

        $tpl = new TemplatePower($file);
        $tpl->prepare();
        
        return $tpl;
    }
    
    
    /**
     *  Ielādē moduļa modeli
     *
     *  Nosaukumā (arī faila) drīkst būt tikai burti, cipari, "_" un "/".
     *
     *  Ja modeļa fails atrodas moduļa apakšmapē, drīkst norādīt ceļu,
     *  piemēram,
     *      
     *      submodels/model_1
     *          vai
     *      models/user_profile
     *  
     *  @param string $model_name   modeļa nosaukums bez faila paplašinājuma
     */
    protected function model($model_string = '') {

        if ($this->model !== false) 
            $this->display_error('Vienlaicīgi kontrollerī var ielādēt tikai ' . 
                'vienu modeli!');

        // atstās tikai pieļaujamos simbolus
        $model_string = $this->escape_model_string($model_string);
        if (empty($model_string)) return false;
        
        // nolasīs pēdējo string'a daļu (aka klases nosaukumu), 
        // ja norādīts arī ceļš uz modeli
        $class_name = $model_string;
        if (($pos = strpos($model_string, '/')) !== false) { 

            $matches = explode('/', $model_string);
            $last_match = $matches[sizeof($matches) - 1];
            $last_match = as_classname($last_match);

            if ($last_match === false) 
                $this->display_error('Nepareizi norādīts modelis!');
            $class_name = $last_match;
        }

        require_once(CORE_PATH . '/includes/class.model.php');
        $file_path = CORE_PATH . '/modules/' . $this->category->module 
                                 . '/' . $model_string . '.php';

        if (file_exists($file_path)) {
            require($file_path);
        } else {
            $this->display_error('Norādītais modelis neeksistē!');
        }

        // inicializē modeļa objektu
        $class_name = 'Model_' . $class_name;        
        $this->model = new $class_name();
    }
    
    
    /**
     *  Atbrīvo atsauci uz ielādēto modeli, lai varētu ielādēt citu
     */
    protected function clear_model() {
        $this->model = false;
    }
    
    
    /**
     *  Eskeipo modeļa faila nosaukumu
     *
     *  Atstās nosaukumā tikai burtus, ciparus, "_" un "/".
     */
    private function escape_model_string($string = '') {
    
        if (empty($string) || trim($string) === '') 
            return trim($string);

        // modeļa nosaukumā drīkst būt "/", 
        // lai varētu norādīt ceļu uz apakšmapi
        $allowed = "/[^a-z0-9_\/]/i";
        $string = preg_replace($allowed, '', $string);
        
        return $string;
    }
    
    
    /**
     *  Pievieno jaunu template faila bloku
     */
    protected function block($string = '') {

        $string = trim($string);        
        if (empty($string)) return false;
        
        $this->view->newBlock($string);
    }
    
    
    /**
     *  Lapas izstrādātājiem būs redzami kļūdas paziņojumi
     */
    private function display_error($string = '') {
        if ($this->debug) {
            die('Kļūda: '. $string);
        } else {
            die('Ooooops! Sistēmas kļūda. :)');
        }
    }
    
    
    /**
     *  Pārbauda, vai lietotājs ir tiesīgs skatīt sadaļu
     */
    protected function check_permission() {
        if (!im_mod()) {
            set_flash('Error 403: Permission denied!');
            redirect();
        }
    }
}
