<?php
/**
 *  Saraksti tabulas formā ar RuneScape pamācībām
 *
 *  Šajā sadaļā iespējams izveidot ierakstu par kādu RuneScape pamācību,
 *  kurai raksts lapā var arī neeksistēt. Ierakstiem var piesaistīt eksistējošu
 *  rakstu un citu informāciju. Pamācību sadaļās rāda šajā sadaļā
 *  izveidotos ierakstus, tādējādi tajās redzami arī "placeholders".
 *
 *  Šobrīd pa cilnēm var pārslēgties starp šāda veida rakstiem:
 *
 *      - kvesti (p2p un f2p)
 *      - minikvesti
 *      - minispēles
 *      - distractions & diversions
 *      - ģildes
 *
 *  Cita veida pamācību sadaļās rāda īstos pievienotos rakstus.
 */
 
class Lists extends Controller {

    // /quests|minigames|distractions|guilds/
    private $opened_tab;
    
    /**
     *  Pēc vajadzības izsauc parējās klases funkcijas
     */
    public function index() {
    
        // nosaka atvērto cilni, lai varētu importēt atbilstošo failu
        $arr_links = ['all-miniquests', 'all-minigames', 
                           'all-distractions', 'all-guilds'];
        $this->opened_tab = 'quests';

        if (in_array($_GET['viewcat'], $arr_links)) {
            $this->opened_tab = 
                str_replace('all-', '', mkslug($_GET['viewcat']));
        }
        
        // "active" klase iezīmēs atvērto cilni
        $this->view->newBlock('list-tabs');
        $this->view->assign('tab-' . $this->opened_tab, 'active');
        
        $this->model('models/lists');
    
        // saraksts ar cilnes rakstiem
        if (!isset($_GET['var1'])) {
            $this->show_list();

        // kāda ieraksta dzēšana
        } else if (isset($_GET['var1']) && $_GET['var1'] === 'delete' &&
                   isset($_GET['var2'])) {
            $this->delete_entry($_GET['var2']);

        // kāda ieraksta slēpšana/parādīšana
        } else if (isset($_GET['var1']) && $_GET['var1'] === 'hide' &&
                   isset($_GET['var2'])) {
            $this->toggle_hidden($_GET['var2']);

        // rediģēšana/pievienošana
        } else {
            $this->load_subfile();
        }
    }
    
    /**
     *  Izdrukā visus cilnes ierakstus tabulas veidā
     */
    private function show_list() {
        
        $this->view->newBlock('list-intro-text');
        $this->view->newBlock('list-button-new');
        
        $found_pages = $this->lists->fetch_pages();
        if (!$found_pages) {
            $this->view->newBlock('list-no-pages');
            return;
        }
        $this->view->newBlock('list-all-pages');
        
        $saved_letter = '';

        foreach ($found_pages as $guide) {

            $this->view->newBlock('list-row');            
            $strid = '';

            // ierakstam ir piesaistīts raksts
            if ($guide->page_id != '0') {                
                $this->view->newBlock('list-page');
                $strid = $guide->page_strid;
            } else { // raksts nav piesaistīts
                $this->view->newBlock('list-page-empty');
            }
            
            $this->view->assign([
                'page_id'   => $guide->page_id,
                'rspage_id' => $guide->rspage_id,
                'strid'     => $strid,
                'title'     => $guide->rspage_title
            ]);
            
            // fiksē nosaukuma pirmo burtu, 
            // lai pie tā maiņas tabulā to izceltu
            if ($this->opened_tab === 'quests') {
                if (substr($guide->rspage_title, 0, 1) !== $saved_letter) {
                    $saved_letter = substr($guide->rspage_title, 0, 1);
                    $this->view->assign('splitted-by', 
                                 '<strong>'.$saved_letter.'</strong>');
                    $this->view->assign('splitted-row-style', 
                                 ' class="is-splitted"');
                }
            }
            
            // izbalē rindu, ja attiecīgais ieraksts skaitās "slēpts"
            if ($guide->is_hidden) {
                $this->view->assign('faded-row', ' style="opacity:0.35"');
            }
        }
    }    
    
    /**
     *  Esoša ieraksta dzēšana
     *
     *  Tiek izsaukta no jquery.
     */
    private function delete_entry($entry_id = 0) {

        $response = 'Ieraksts dzēsts';
        $error = 'success';
    
        $val = $this->lists->delete_entry($entry_id);
        if ($val === false) {
            $response = 'Ierakstu dzēst neizdevās';
            $error = 'error';
        }
        
        if (isset($_GET['_'])) {
            echo json_encode(['state' => $error, 
                                   'content' => $response]);
        } else {
            set_flash($response);
            redirect('/' . $category->textid);
        }
        exit;
    }    
    
    /**
     *  Esoša ieraksta paslēpšana/parādīšana
     *
     *  Tiek izsaukta no jquery.
     */
    private function toggle_hidden($entry_id = 0) {
        
        $entry_id = (int)$_GET['var2'];
    
        // pārbauda, vai norādītais ieraksts vispār eksistē
        $get_entry = $this->lists->fetch_entry($entry_id);
        if (!$get_entry) {
            if (isset($_GET['_'])) {
                echo json_encode(['state' => 'error', 
                                       'content' => 'Ieraksts nav atrasts']);
            } else {
                set_flash('Ieraksts nav atrasts');
                redirect('/' . $category->textid);
            }
            exit;
        }

        $swap_to = ($get_entry->is_hidden) ? 0 : 1;
        $swap_text = ($swap_to) ? 'hidden' : 'shown';
        
        $this->lists->toggle_entry($entry_id, $swap_to);        
        
        if (isset($_GET['_'])) {
            echo json_encode(['state' => 'success', 
                                   'content' => $swap_text]);
        } else {
            set_flash(($swap_to) ? 'Ieraksts slēpts' : 
                                   'Ieraksts vairs nav slēpts');
            redirect('/' . $category->textid);
        }
        exit;
    }    
    
    /**
     *  Citām darbībām iekļauj papildfailu atbilstoši atvērtajai cilnei
     */
    private function load_subfile() {
    
        $filename = 'lists_quests.php';
    
        if ($this->opened_tab === 'minigames') {
            $filename = 'lists_minigames.php';
        } else if ($this->opened_tab === 'distractions') {
            $filename = 'lists_minigames.php';
        } else if ($this->opened_tab === 'guilds') {
            $filename = 'lists_guilds.php';
        }

        $path = CORE_PATH.'/modules/'.$this->category->module
                         .'/submodules/'.$filename;
        
        if (file_exists($path)) {
            require($path);
            $class_name = str_replace(['.php','lists_'], '', $filename);
            $class_name = as_class_name($class_name);
            $controller = new $class_name();
            $controller->index();

        } else { // kādēļ neeksistē fails?
            set_flash('Kļūdaini norādīta adrese');
            redirect('/' . $_GET['viewcat']);
        }
    }
}
