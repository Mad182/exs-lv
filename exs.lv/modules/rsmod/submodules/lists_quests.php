<?php
/**
 *  Ar [mini-]kvestiem saistītu ierakstu pievienošana un rediģēšana
 *
 *  Tiek izsaukts no lists.php faila
 */

class Quests extends Controller {

    // kvestu sarežģītība
    private $arr_levels = array(
        1 => 'Novice', 
        2 => 'Intermediate', 
        3 => 'Experienced', 
        4 => 'Master', 
        5 => 'Grandmaster', 
        6 => 'Special'
    );

    // kvestu ilgums
    private $arr_length = array(
        1 => 'Īss', 
        2 => 'Vidējs', 
        3 => 'Ilgs', 
        4 => 'Ļoti ilgs', 
        5 => 'Ļoti, ļoti ilgs'
    );

    /**
     *  Pēc adreses parametriem izsauc pārējās funkcijas
     */
    public function index() {

        $this->model('models/list_quests');
        
        // jauna ieraksta pievienošana
        if (isset($_GET['var1']) && $_GET['var1'] === 'new') {

            if (isset($_POST['submit'])) {
                $this->model->post_new_quest($_POST);
                set_flash('Ieraksts pievienots');
                redirect('/'.$_GET['viewcat']);
            }
            $this->show_new_form();
            
        // esoša ieraksta rediģēšana
        } else if (isset($_GET['var1']) && $_GET['var1'] === 'edit' && 
                   isset($_GET['var2'])) {

            if (isset($_POST['submit'])) {
                $this->model->update_quest($_GET['var2'], $_POST);
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
     *  Jauna ieraksta pievienošanas forma
     */
    private function show_new_form() {
        
        $this->view->newBlock('quest-form');
        
        // kvesta sarežģītības izvēlne
        foreach ($this->arr_levels as $level => $value) {
            $this->view->newBlock('add-difficulty');
            $this->view->assign(array(
                'level-id' => $level,
                'level-title' => $value
            ));
        }
        
        // kvesta ilguma izvēlne
        foreach ($this->arr_length as $length => $value) {
            $this->view->newBlock('add-length');
            $this->view->assign(array(
                'length-id' => $length,
                'length-title' => $value
            ));
        }
    }
    
    /**
     *  Esoša ieraksta rediģēšanas forma
     */
    private function show_edit_form($entry_id = 0) {
    
        $entry = $this->model->fetch_entry($entry_id);        
        if (!$entry) {
            set_flash('Ieraksts neeksistē');
            redirect('/'.$_GET['viewcat']);
        }

        $this->view->newBlock('quest-form');
        $this->view->assignAll($entry);
        $this->view->assign('strid', $entry->strid);
        
        if ((bool)$entry->members_only) {
            $this->view->assign('sel-members', ' selected="selected"');
        }
        if ((bool)$entry->age) {
            $this->view->assign('sel-sixth', ' selected="selected"');
        }
        if ((bool)$entry->voice_acted) {
            $this->view->assign('sel-voiced', ' selected="selected"');
        }
        
        // kvesta sarežģītības izvēlne
        foreach ($this->arr_levels as $level => $value) {
            $this->view->newBlock('add-difficulty');
            $this->view->assign(array(
                'level-id' => $level,
                'level-title' => $value
            ));
            if ((int)$entry->difficulty === $level) {
                $this->view->assign('selected', ' selected="selected"');
            }
        }
        
        // kvesta ilguma izvēlne
        foreach ($this->arr_length as $length => $value) {
            $this->view->newBlock('add-length');
            $this->view->assign(array(
                'length-id' => $length,
                'length-title' => $value
            ));
            if ((int)$entry->length === $length) {
                $this->view->assign('selected', ' selected="selected"');
            }
        }
    }
}
