<?php
/**
 * 	RuneScape kvestu sēriju pārvaldība
 *
 *  Sēriju izmaiņas, kvestu piesaiste tām, to secība utt.
 */

class Series extends Controller {

    /**
     *  Pēc adreses parametriem izsauc pārējās funkcijas
     */
    public function index() {

        $this->model('models/series');

        $var1 = isset($_GET['var1']) ? $_GET['var1'] : '';
        $var2 = isset($_GET['var2']) ? (int)$_GET['var2'] : 0;
        $var3 = isset($_GET['var3']) ? $_GET['var3'] : '';
        $is_jquery = isset($_GET['_']) ? true : false;
        
        if ($is_jquery) {
            
            if ($var1 === '') {
                die('Kļūdaini atvērta adrese');
            
            // saraksts ar visiem kvestiem
            } else if ($var1 === 'list' && $var2 > 0) {
                echo json_encode(array('state' => 'success',
                    'content' => $this->display_all_quests($var2)));
            
            // saraksts ar sērijai piesaistītiem kvestiem
            } else if ($var1 === 'getlist' && $var2 > 0) {
                echo json_encode(array('state' => 'success',
                    'content' => $this->display_series_quests($var2)));
            
            // sērijas kvestu secības atjaunošana
            } else if ($var1 === 'order' && $var2 !== '' && 
                       isset($_POST['json_check'])) {

                $arr = $this->reorder_quests($var2, $_POST);
                echo json_encode(array(
                    'state' => $arr[0], 
                    'message' => $arr[1],
                    'content' => $arr[2]
                ));

            // pievieno/dzēš no sērijas kādu kvestu
            } else if (($var1 === 'add' || $var1 === 'del') && 
                        $var2 !== '' && $var3 !== '') {
                
                $arr = $this->set_series_quest($var1, $var2, $var3);
                
                echo json_encode(array(
                    'state' => $arr[0],
                    'error' => $arr[1],
                    'type' => $arr[2],
                    'series_id' => $arr[3],
                    'url' => $arr[4]
                ));
            } else {
                die('Kļūdaini atvērta adrese');
            }            
            exit;
        
        // parasts ne-jquery pieprasījums
        } else {
            if ($var1 === 'update' && isset($_POST['submit'])) {
                $this->reorder_series($_POST);
            } else {
                $this->display_series();
            }
        }        
    }    
    
    /**
     *  Parāda lapā kvestu sērijas un to izmaiņu formu
     */
    private function display_series() {

        $this->view->newBlock('all-series-block');

        $series = $this->model->fetch_series();
        $series_count = $this->model->count_series();
        
        if ($series === false || $series_count === false) {
            $this->view->newBlock('no-series-found');
            return;
        }

        $this->view->newBlock('series-notes');
        $this->view->newBlock('series-form');

        // skaits, aiz kura sarakstu pārdalīt uz pusēm
        $col_split = floor($series_count / 2);
        $counter = 0;

        foreach ($series as $single) {
        
            // lapā būs redzamas divas kolonnas ar sērijām
            if ($counter == 0 || $counter == $col_split) {
                $this->view->newBlock('series-column');
            }
            $counter++;
            
            $this->view->newBlock('series-row');
            $this->view->assignAll($single);          

            // katrai sērijai ir izvēlne ar kārtas numuriem
            for ($i = 1; $i <= $series_count; $i++) {
                $selected = ($i == $single->ordered_by) ? 
                    ' selected="selected"' : '';
                $this->view->newBlock('selection-option');
                $this->view->assign(array(
                    'ordered_by' => $i,
                    'selected' => $selected
                ));
            }
        }
    }    
    
    /**
     *  Atgriež html saturu ar sērijas kvestiem
     */
    function display_series_quests($series_id = 0) {
    
        $series_id = (int)$series_id;
        if ($series_id < 1) return '';

        // izmanto lokālo skatu, jo šo funkciju izsauc jquery
        if (($view = $this->view('rsmod')) === false) return '';
        
        $view->newBlock('series-quests-block');
        $view->assign(array(
            'category-url'  => $_GET['viewcat'],
            'series-id'     => $series_id
        ));
        
        $quests = $this->model->fetch_series_quests($series_id);
        
        if (!$quests) {
            $view->newBlock('no-series-quests');
            return $view->getOutputContent();
        } else {
            $view->newBlock('submit-button');
        }   

        $quest_count = count($quests);
        
        // pievienos sarakstam katru atlasīto kvestu
        $view->newBlock('quest-list');
        foreach ($quests as $quest) {
        
            $view->newBlock('series-quest');
            $view->assignAll($quest);

            // uzreiz atzīmēs jau iepriekš izvēlēto kārtas numuru
            for ($i = 1; $i <= $quest_count; $i++) {
                $view->newBlock('option-param');
                $view->assign('value', $i);
                if ($i == $quest->ordered_by) {
                    $view->assign('selected', ' selected="selected"');
                }
            }
        }
        
        return $view->getOutputContent();
    }    

    /**
     *  Atgriež html saturu ar visiem kvestiem
     */
    function display_all_quests($series_id = 0) {
        
        $series_id = (int)$series_id;

        // izveido lokālo skatu, jo šo funkciju izsauc jquery
        if (($view = $this->view('rsmod')) === false) return '';
        $view->newBlock('all-quests-block');

        // sērijas eksistences pārbaude
        if ($series_id < 1) {
            $view->newBlock('wrong-params');
            return $view->getOutputContent();
        }        
        $series = $this->model->fetch_single_series($series_id);
        if (!$series) {
            $view->newBlock('wrong-params');
            return $view->getOutputContent();
        }        
        
        $get_quests = $this->model->fetch_quests($series_id);
        if (!$get_quests) {
            $view->newBlock('series-not-found');
            return $view->getOutputContent();
        }
        
        $view->newBlock('all-quests-list');
        $view->assign(array(
            'category-url' => $_GET['viewcat'],
            'series-id' => $series->id,
            'series-title' => $series->title
        ));
        
        foreach ($get_quests as $quest) {
        
            $view->newBlock('list-single-quest');
            $view->assign(array(
                'series-id' => $series_id,
                'page-id'   => $quest->id,
                'title'     => $quest->title,
                'type'      => ($quest->quests_id != '0') ? 'del' : 'add'
            ));
            
            // iekrāsos kvestu, ja tas jau ir pievienots atvērtajai sērijai
            $marker = ($quest->quests_id != '0') ? 'mark-added' : 'mark-neutral';
            $view->assign('marker', $marker);
        }
        
        return $view->getOutputContent();
    }

    /**
     *  Sēriju secības izmaiņas
     */
    private function reorder_series($post_arr = null) {

        // atlasa visas sērijas, lai katrai pārbaudītu iesniegtu datu esamību
        $series = $this->model->fetch_series();
        if ($post_arr == null || $series === false) {
            set_flash('Darbība neizdevās');
            redirect('/'.$_GET['viewcat']);
        }

        foreach ($series as $single) {
            if (isset($post_arr['order_' . $single->id]) && 
                isset($post_arr['title_' . $single->id])) {

                $order = (int)$post_arr['order_' . $single->id];
                $title = input2db($post_arr['title_' . $single->id], 256);
                
                $arr = array('ordered_by' => $order, 'title' => $title);
                $this->db->update('rs_series', (int)$single->id, $arr);
            }
        }
        
        set_flash('Sēriju secība un nosaukumi atjaunoti');
        redirect('/'.$_GET['viewcat']);    
    }    
    
    /**
     *  Sērijas kvestu secības maiņa
     */
    private function reorder_quests($series_id = 0, $post_arr = null) {
    
        $series_id = (int)$series_id;
        
        if ($series_id < 1 || $post_arr == null) {
            $arr = array('error', 'Darbība neizdevās', '');
            return $arr;
        }
        
        $series = $this->model->check_series($series_id);        
        if (!$series) {
            $arr = array('error', 'Darbība neizdevās', '');
            return $arr;
        }
        
        $quests = $this->model->fetch_series_quests($series_id);
        if ($quests === false) {
            $arr = array('error', 'Darbība neizdevās', '');
            return $arr;
        }

        // atjauno katra kvesta secību
        foreach ($quests as $single) {
        
            if (isset($post_arr['order-'.$single->id])) {

                $order = (int)$post_arr['order-'.$single->id];
                
                $data = array(
                    'ordered_by' => $order,
                    'updated_by' => (int)$this->auth->id,
                    'updated_at' => time()
                );
                
                $this->db->update('rs_series_quests', $single->id, $data);
            }        
        }
        
        $arr = array('success', 'Secība atjaunota', 
            $this->display_series_quests($series_id));
        return $arr;
    }

    /**
     *  Pievieno sērijai vai no tās dzēš kādu kvestu
     */
    private function set_series_quest($type = null, $series_id = 0,
                                      $quest_id = 0) {
            
        // ievades parametru pārbaudes
        if ($type !== 'add' && $type !== 'del') {
            return array('error', 'Darbība neizdevās', '', '', '');
        }
        
        $series_id = (int)$series_id;
        $quest_id = (int)$quest_id;

        if (!$this->model->check_series($series_id)) {
            return array('error', 'Norādītā sērija neeksistē', '', '', '');
        }
        if (!$this->model->check_quest($quest_id)) {
            return array('error', 'Norādītais kvests neeksistē', '', '', '');
        }
        
        // pārbauda, vai ieraksts par šādu sērijas un kvesta kombināciju 
        // eksistē, lai varētu veikt attiecīgus labojumus
        $entry = $this->model->get_series_quest($series_id, $quest_id);
        
        $query_response = false;
        if ($type === 'del') {
            if ($entry) {
                $query_response = $this->model->remove_series_quest($entry->id);
            } else { // ja nav dzēšama ieraksta, pieņem, ka viss kārtībā
                $query_response = true;
            }
        } else if ($type === 'add') {
            if (!$entry) {
                $query_response = $this->model->set_series_quest(
                    $series_id, $quest_id);
            } else {
                $query_response = $this->model->set_series_quest(
                    $series_id, $quest_id, $entry->id);
            }
        }

        $error = ($query_response) ? 'success' : 'error';
        $type = ($type === 'del') ? 'add' : 'del';
        $url = '/series/'.$type.'/'.$series_id.'/'.$quest_id;

        return array($error, '', $type, $series_id, $url);
    }
}
