<?php
/**
 *  Parent klase exs mvc arhitektūras kontrolleru klasēm.
 *  Kontrolleri, kas šo klasi atvasina, meklējami attiecīgo moduļu mapēs.
 *
 *
 *  Daudzas no šeit iekļautajām funkcijas ievadē saņem faila nosaukumu.
 *  Ar nosaukumu jāsaprot, ka norādīts tajā drīkst būt arī ceļš,
 *  sākot no atvērtā moduļa mapes. Formāta piemēri:
 *
 *      "modul_name"
 *      "submodules/subsubmodules/modul_name"
 *
 *  Nosaukumā pieļaujamie simboli: 
 *      A-Z, a-z, 0-9, "_", "-", "/".
 */

class Controller {
    
    // atsauce uz $tpl globālo mainīgo
    protected $view;
    
    // ceļš pa mapēm uz atvērto moduli
    private $path;
    
    /**
     *  Pievienos kontrollerim atsauces uz dažiem
     *  projekta globālajiem mainīgajiem.
     */
    public function __construct() {

        $globals = array(
            'db', 'auth', 'lang',
            'tpl_options', 'debug',
            'm', 'ss',
            'category', 'page_title',
            'img_server'
        );
        foreach ($globals as $global) {
            global ${$global};
            $this->{$global} =& ${$global};
        }

        global $tpl;
        $this->view =& $tpl;
        $this->path = CORE_PATH.'/modules/'.$this->category->module.'/';
    }
    
    /**
     *  Nepieciešamos globālos mainīgos var piesaistīt arī manuāli.
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
    
    /**
     *  Ielādēs norādīto datu modeli.
     */
    protected function model($file_path = '') {

        $last_part = $this->get_last_part($file_path);
        
        // nosaukums mainīgajam, caur kādu varēs atsaukties uz modeli
        $allowed = "/[^a-z0-9_]/i";
        $variable_name = preg_replace($allowed, '', $last_part);

        if (empty($variable_name) || isset($this->{$variable_name})) {
            $this->quit('Neizdevās izveidot mainīgo ar šādu modeļa nosaukumu.');
        }

        // faila nosaukuma pārbaudes
        $file_path = $this->as_file_name($file_path);
        if (empty($file_path)) $this->quit('Norādīts nederīgs modeļa nosaukums.');

        if (!file_exists($this->path.$file_path.'.php')) {
            $this->quit('Norādītais \''.$file_path.'.php\' fails neeksistē.');
        }
        
        require_once(CORE_PATH.'/modules/runescape/class.model.php');
        require($this->path.$file_path.'.php');

        // inicializēs modeļa objektu
        $class_name = 'Model_'.as_class_name($last_part);
        $this->{$variable_name} = new $class_name();
    }

    /**
     *  Atgriezīs norādīto skatu kā jaunu template objektu.
     *
     *  @param string $file_path    faila nosaukums
     *  @return TemplatePower       template objekts
     */
    protected function view($file_path = '') {
        
        $file_path = $this->as_file_name($file_path);   
        if (empty($file_path)) $this->quit('Norādīts nederīgs skata nosaukums.');
     
        if (!file_exists($this->path.$file_path.'.tpl')) {
            $this->quit('Norādītais \''.$file_path.'.tpl\' fails neeksistē.');
        }

        $tpl = new TemplatePower($this->path.$file_path.'.tpl');
        $tpl->prepare();
        
        return $tpl;
    }
    
    /**
     *  Ielādēs skatu norādītajā parent skatā.
     *
     *  @param string $file_path     faila nosaukums
     *  @param string $parent_block  bloka nosaukums, kurā saturu ielādēt
     */
    protected function subview($file_path = '', $parent_block = '') {
        
        $file_path = $this->as_file_name($file_path);
        $parent_block = trim($parent_block);
        
        if (empty($file_path) || empty($parent_block)) {
            $this->quit('Kļūdaini norādīti parametri.');
        }

        if (!file_exists($this->path.$file_path.'.tpl')) {
            $this->quit('Norādītais \''.$file_path.'.tpl\' fails neeksistē.');
        }
        
        $this->view->assignInclude($parent_block, $this->path.$file_path.'.tpl');
        $this->view->prepare();
    }
    
    /**
     *  Ielādēs norādīto apakšmoduli un inicializēs tajā esošo klasi.
     */
    protected function submodule($file_path = '') {

        $file_path = $this->as_file_name($file_path);        
        if (empty($file_path)) $this->quit('Kļūdaini norādīti parametri');

        if (!file_exists($this->path.$file_path.'.php')) {
            $this->quit('Norādītais \''.$file_path.'.php\' fails neeksistē.');
        }
        
        require($this->path.$file_path.'.php');

        $class_name = as_class_name($this->get_last_part($file_path));
        if (empty($class_name)) {
            $this->quit('Norādīts nederīgs klases nosaukums.');
        }

        $class = new $class_name();
        if (!method_exists($class, 'index')) {
            $this->quit('Klasi inicializēt neizdevās.');
        }
        $class->index();
    }
    
    /**
     *  Pārbaudīs, vai lietotājs ir tiesīgs skatīt sadaļu.
     */
    protected function check_permission($string = 'mod') {
        if ($string === 'mod' && !im_mod()) {
            set_flash('Pieeja liegta!');
            redirect();
        } else if ($string === 'ra' && $this->auth->level > 3) {
            set_flash('Pieeja liegta!');
            redirect();
        } else if ($string === 'user' && !$this->auth->ok) {
            set_flash('Pieeja liegta! Lūdzu, autorizējies.');
            redirect();
        }
    }
    
    /**
     *  Apstrādās faila nosaukumu atbilstoši šīs klases formātam,
     *  dzēšot neatļautos simbolus.
     */
    private function as_file_name($file_name = '') {

        $file_name = trim($file_name);
        $allowed = "/[^a-z0-9_\-\/]/i";
        $file_name = preg_replace($allowed, '', $file_name);
        
        return $file_name;
    }
    
    /**
     *  No virknes formātā <nosaukums>/<nosaukums>/.. atgriezīs pēdējo daļu.
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
     *  Atgriezīs kļūdas paziņojumu un pārtrauks satura ielādi.
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
