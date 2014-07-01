<?php
/**
 *  Ar minispēlēm saistītu ierakstu pievienošana un rediģēšana
 *
 *  Tiek izsaukts no lists.php faila
 */

class Minigames extends Controller {

    /**
     *  Pēc adreses parametriem izsauc pārējās funkcijas
     */
    public function index() {

        $this->model('models/list_minigames');
        
        // jauna ieraksta pievienošana
        if (isset($_GET['var1']) && $_GET['var1'] === 'new') {
        
            if (isset($_POST['submit'])) {
                $this->list_minigames->post_new($_POST);
                set_flash('Ieraksts pievienots');
                redirect('/'.$_GET['viewcat']);
            }
            $this->view->newBlock('minigame-form');

        // ieraksta rediģēšana
        } else if (isset($_GET['var1']) && $_GET['var1'] === 'edit' && 
                   isset($_GET['var2'])) {

            if (isset($_POST['submit'])) {
                $this->list_minigames->update_entry($_GET['var2'], $_POST);
                set_flash('Ieraksts atjaunots');
                redirect('/'.$_GET['viewcat']);
            }
            $this->show_edit_form($_GET['var2']);

        // hackz
        } else {
            set_flash('No hacking, pls');
            redirect('/' . $_GET['viewcat']);
        }
    }

    /**
     *  Ieraksta rediģēšanas forma
     */
    private function show_edit_form($entry_id = 0) {
    
        $entry = $this->list_minigames->fetch_entry($entry_id);
        if (!$entry) {
            set_flash('Ieraksts neeksistē');
            redirect('/'.$_GET['viewcat']);
        }
    
        $this->view->newBlock('minigame-form');
        $this->view->assignAll($entry);
        $this->view->assign('strid', $entry->strid);
        
        // free/members only
        if ((bool)$entry->members_only) {
            $this->view->assign('sel-members', ' selected="selected"');
        }
    }
}
