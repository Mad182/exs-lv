<?php
/**
 *  RuneScape tasks/achievement diaries
 */

class Tasks extends Controller {

    public function index() {
    
        $this->model('models/other');

        $this->view->newBlock('tasks');
        
        $this->show_categorised();
        $this->show_uncategorised();        
    }
    
    /**
     *  Parādīs kategorizētos tasks
     */
    private function show_categorised() {
        
        $tasks = $this->other->fetch_tasks();
        if (!$tasks) {
            $this->view->newBlock('..');
            return;
        }
        
        $counter_yes = 1;
        $counter_no  = 1;
        $page_id = 0;

        foreach ($tasks as $page) {

            // izveido jaunu noteikta reģiona bloku
            if ($page->class_id != $page_id) {

                $page_id = $page->class_id;

                // teritorijai ir vismaz viens raksts
                if ($page->page_id != '0') {
                    $this->view->newBlock('tasks-block');
                    $this->view->assignAll($page);
                    
                    if ( ($counter_yes - 1) % 3 == 0 && ($counter_yes - 1) > 0) {
                        $this->view->assign('newline', ' newline');
                    }  // pārmet blokus jaunā rindā	
                    $counter_yes++;
                }
                // teritorijām, kurām nav rakstu, izveido jaunu bloku
                // zem pārējām teritorijām
                else {
                    $this->view->newBlock('tasks-not');
                    $this->view->assignAll($page);
                    
                    if ( ($counter_no - 1) % 3 == 0 && ($counter_no - 1) > 0) {
                        $this->view->assign('newline', ' newline');
                    }  // pārmet blokus jaunā rindā	
                    $counter_no++;
                }            
            }

            // pārbaude novērš situācijas, kad tiek pievienotas teritorijas,
            // kurām nav rakstu
            if ($page->page_id != '0') {
                $this->view->newBlock('task');
                $this->view->assignAll($page);
            }
        }
    }
    
    /**
     *  Parādīs nekategorizētos Tasks
     */
    private function show_uncategorised() {

        $uncategorised = $this->other->fetch_tasks(false);
        if (!$uncategorised) {
            $this->view->newBlock('..');
            return;
        }

        $this->view->newBlock('tasks-block');
        $this->view->assign(array(
            'class_img'     => 'uncategorised.png',
            'class_title'   => 'Nekategorizēti raksti'
        ));

        foreach ($others as $other) {
            $this->view->newBlock('task');
            $this->view->assignAll($other);
        }
    }    
}
