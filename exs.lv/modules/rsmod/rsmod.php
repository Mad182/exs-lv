<?php
/**
 *  RuneScape pamācību sadaļu (kvesti/prasmes u.tml.) administrācijas panelis
 *
 *  Šis modulis apkopo tās sadaļas, kurās iespējams veikt izmaiņas
 *  RuneScape pamācību sadaļām, piemēram, pievienojot jaunas rakstu sērijas,
 *  mainot rakstu secību sērijā, izveidojot rakstu "placeholders" u.c.
 */

class Rsmod extends Controller {

    public function index() {
    
        if (!im_mod()) {
            set_flash('Error 403: Permission denied!');
            redirect();
        }
        // temp fix
        global $tpl_options;
        $tpl_options = 'no-left';

        $this->load_submodule();
    }
    
    
    /**
     *  Apakšmoduļa ielāde
     */
    private function load_submodule() {
        
        // array_key ir lapas "textid"
        $submodules = array( // [0] .php, [1] .tpl
            'all-quests'        => array('lists', 'lists'),
            'all-miniquests'    => array('lists', 'lists'),
            'all-minigames'     => array('lists', 'lists'),
            'all-distractions'  => array('lists', 'lists'),
            'all-guilds'        => array('lists', 'lists'),
            'all-unlisted'      => array('unlisted', 'lists'),
            'series'            => array('series', ''),
            'skills'            => array('skills', '')
        );

        if (!isset($submodules[$this->category->textid])) {
            set_flash('Kļūdaini norādīta adrese');
            redirect();
        }
        
        $path = CORE_PATH.'/modules/'.$this->category->module.'/submodules/';
        $php_file = $path.$submodules[$this->category->textid][0].'.php';
        $tpl_file = $path.$submodules[$this->category->textid][1].'.tpl';
            
        if ($submodules[$this->category->textid][1] !== '') {
            $this->tpl->assignInclude('sub-template', $tpl_file);
            $this->tpl->prepare();
        }
        include($php_file);
        
        $class_name = as_classname($submodules[$this->category->textid][0]);        
        if ($class_name === false) return false;
        
        $controller = new $class_name();
        $controller->index();
    }
}
