<?php
/**
 *  Parent klase MVC arhitektūras kontrolleriem
 *
 *  Kontrolleri, kas šo klasi atvasina, meklējami moduļu mapēs.
 *
 *  TODO:
 *      - as_classname() - atstarpes jādzēš
 *      - $this->quit() kā funkcija globālajos failos *
 *
 *  Daudzas funkcijas ievadē saņem faila nosaukumu. Ar nosaukumu jāsaprot,
 *  ka norādīts drīkst būt arī ceļš, sākot no atvērtā moduļa mapes.
 *
 *  Nosaukuma piemēri:
 *      "otherModule"
 *      "sub-modules/sub_sub_modules/different"
 *
 *  Nosaukumā pieļaujamie simboli: 
 *      A-Z, a-z, 0-9, "_", "-", "/".
 */

class Controller {
    
    // atsauce uz $tpl globālo mainīgo
    protected $view;
    
    // ceļš pa mapēm uz atvērto moduli
    private $path;
    
    // masīvs ar ielādētajiem modeļiem, lai zinātu, 
    // vai nepieprasa vienu un to pašu atkārtoti
    private $loaded_models;
    
    /**
     *  Pievieno kontrollerim atsauces uz atsevišķiem 
     *  projekta globālajiem mainīgajiem
     */
    public function __construct() {
        
        $globals = array(
            'db', 'auth', 'lang',
            'tpl_options', 'debug',
            'm', 'ss',
            'category', 'page_title'
        );
        foreach ($globals as $global) {
            global ${$global};
            $this->{$global} =& ${$global};
        }

        global $tpl;
        $this->view =& $tpl;
        $this->path = CORE_PATH.'/modules/'.$this->category->module.'/';
        
        $this->loaded_models = array();
    }
    
    /**
     *  Ielādē norādīto modeli
     *  
     *  @param string $file  faila nosaukums
     */
    protected function model($file = '') {
        
        // nosaukums mainīgajam, caur kādu varēs atsaukties uz modeli
        $variable_name = trim($file);
        $allowed = "/[^a-z0-9_]/i";
        $variable_name = preg_replace($allowed, '', $variable_name);

        if (isset($this->{$variable_name})) {
            $this->quit('Neizdevās izveidot mainīgo ar šādu modeļa nosaukumu');
        }

        // faila nosaukuma pārbaudes
        $file = $this->as_file_name($file);
        if (empty($file)) $this->quit('Kļūdaini norādīti parametri');

        if (!file_exists($this->path.$file.'.php')) {
            $this->quit('Norādītais \''.$file.'\' fails neeksistē');
        }
        
        require_once(CORE_PATH . '/includes/class.model.php');
        require($this->path.$file.'.php');
        
        // vienu un to pašu modeli nedrīkst ielādēt atkārtoti;
        // bez kļūdas paziņojuma arī var iztikt, jo modelis jau ir ielādēts
        $class_name = as_classname($this->get_last_part($file));
        if (in_array($class_name, $this->loaded_models)) {
            return;
        }
        
        // inicializē modeļa objektu
        $class_name = 'Model_' . $class_name;
        $this->{$variable_name} = new $class_name();
    }

    /**
     *  Atgriež norādīto skatu kā jaunu template objektu
     *
     *  (Noder jquery funkcijām, kur nepieciešama tikai neliela skata daļa.)
     *
     *  @param string $file     faila nosaukums
     *  @return TemplatePower   template objekts
     */
    protected function view($file = '') {
        
        $file = as_file_name($file);   
        if (empty($file)) $this->quit('Kļūdaini norādīti parametri');
     
        if (!file_exists($this->path.$file.'.tpl')) {
            $this->quit('Norādītais \''.$file.'\' fails neeksistē');
        }

        $tpl = new TemplatePower($this->path.$file.'.tpl');
        $tpl->prepare();
        
        return $tpl;
    }
    
    /**
     *  Ielādē norādīto skatu parent skatā
     *
     *  @param string $file          faila nosaukums
     *  @param string $parent_block  bloka nosaukums, kurā saturu ielādēt
     */
    protected function subview($file = '', $parent_block = '') {
        
        $file = as_file_name($file);
        $parent_block = trim($parent_block);
        
        if (empty($file) || empty($parent_block)) {
            $this->quit('Kļūdaini norādīti parametri');
        }

        if (!file_exists($this->path.$file.'.tpl')) {
            $this->quit('Norādītais \''.$file.'\' fails neeksistē');
        }
        
        $this->view->assignInclude($parent_block, $this->path.$file.'.tpl');
        $this->view->prepare();
    }
    
    /**
     *  Ielādē apakšmoduli un inicializē tajā esošo klasi
     *
     *  @param string $file     faila nosaukums
     */
    protected function submodule($file = '') {

        $file = as_file_name($file);        
        if (empty($file)) {
            $this->quit('Kļūdaini norādīti parametri');
        }

        if (!file_exists($this->path.$file.'.php')) {
            $this->quit('Norādītais \''.$file.'\' fails neeksistē');
        }
        
        require($this->path.$file.'.php');

        $class_name = as_classname($this->get_last_part($file);
        if (empty($class_name)) {
            $this->quit('Kļūda klases nosaukumā');
        }
            
        $class = new $classname();

        if (!method_exists($class, 'index')) {
            $this->quit('Objektu neizdevās inicializēt');
        }
        $class->index();
    }
    
    /**
     *  Pārbauda, vai lietotājs ir tiesīgs skatīt sadaļu
     */
    protected function check_permission($string = 'mod') {
        if ($string === 'mod' && !im_mod()) {
            set_flash('Pieeja liegta!');
        } else if ($string === 'ra' && $this->auth->level > 3) {
            set_flash('Pieeja liegta!');
        } else if ($string === 'user' && !$this->auth->ok) {
            set_flash('Pieeja liegta! Lūdzu, autorizējies.');
        }
        redirect();
    }
    
    /**
     *  Apstrādā faila nosaukumu atbilstoši šīs klases formātam,
     *  dzēšot neatļautos simbolus
     *
     *  @param string $file_name    faila nosaukums
     */
    private function as_file_name($file_name = '') {

        $file_name = trim($file_name);
        $allowed = "/[^a-z0-9_\-\/]/i";
        $file_name = preg_replace($allowed, '', $file_name);
        
        return $file_name;
    }
    
    /**
     *  No virknes formātā <nosaukums>/<nosaukums>/.. atgriež pēdējo daļu
     *
     *  @param string $path_string  faila nosaukums
     */
    private function get_last_part($path_string = '') {
    
        if (empty($path_string)) return '';

        if (($pos = strpos($path_string, '/')) !== false) {
            $matches = explode('/', $path_string);
            $path_string = $matches[sizeof($matches) - 1];
        }
        
        return $path_string;
    }
    
    /**
     *  Atgriezīs kļūdas paziņojumu un pārtrauks satura ielādi
     *
     *  Ja lietotājs nav administrators, drošības apsvērumu dēļ
     *  norādīto paziņojumu neredzēs.
     */
    private function quit($string = '') {
        if ($this->debug) {
            die('Kļūda: '. $string);
        } else {
            die('Ooooops! Sistēmas kļūda. :)');
        }
    }
}
