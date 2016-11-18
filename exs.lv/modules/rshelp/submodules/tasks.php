<?php
/**
 *  RuneScape Achievements
 *  (agrāk saukti par Tasks un Achievement Diaries).
 */

class Tasks extends Controller {

    public function index() {
    
        $this->view->newBlock('tasks-intro-text');
        
        $this->model('models/guides');

        $this->show_regions();
        $this->show_uncategorised();
    }

    /**
     *  Parādīs Achievements reģionus un piesaistīs tiem
     *  rakstus no `rs_pages` tabulas (tai skaitā placeholders).
     *
     *  Ja reģionam rakstu nav, rādīs tikai tā nosaukumu un attēlu
     *  (zem reģioniem, kuriem raksti ir).
     */
    private function show_regions() {
        
        $tasks = $this->guides->fetch_tasks();
        if (!$tasks) {
            $this->view->newBlock('no-tasks-found');
            return;
        }
        
        $this->view->newBlock('tasks');
        
        $counter_yes = 1;
        $counter_no  = 1;
        $page_id = 0;

        foreach ($tasks as $task) {

            // izveido jaunu noteikta reģiona bloku
            if ($task->id != $page_id) {

                $page_id = $task->id;

                // teritorijai ir vismaz viens raksts
                if ($task->page_id != '0') {

                    $this->view->newBlock('tasks-has-pages');
                    $this->view->assignAll($task);
                    
                    // pārmet blokus jaunā rindā    
                    if (($counter_yes - 1) % 3 == 0 && ($counter_yes - 1) > 0){
                        $this->view->assign('newline', ' newline');
                    }
                    $counter_yes++;

                // teritorijām, kurām nav rakstu, izveido jaunu bloku
                // zem pārējām teritorijām, 
                // kurā redzams būs tikai nosaukums un attēls
                } else {

                    $this->view->newBlock('tasks-no-pages');
                    $this->view->assignAll($task);
                    
                    // pārmet blokus jaunā rindā
                    if (($counter_no - 1) % 3 == 0 && ($counter_no - 1) > 0) {
                        $this->view->assign('newline', ' newline');
                    }   
                    $counter_no++;
                }            
            }

            if ($task->page_id != '0') {
                $this->view->newBlock('task');
                $this->view->assignAll($task);
            }
        }
    }
    
    /**
     *  Parādīs vienu bloku ar rakstiem, 
     *  kuri nevienam esošajam reģionam nav piesaistīti.
     */
    private function show_uncategorised() {

        $uncategorised = $this->guides->fetch_uncategorized_tasks();
        if (!$uncategorised) {
            return;
        }

        $this->view->newBlock('tasks-has-pages');
        $this->view->assign([
            'img'           => 'uncategorised.png',
            'series_title'  => 'Nekategorizēti raksti'
        ]);

        foreach ($uncategorised as $task) {
            $this->view->newBlock('task');
            $this->view->assignAll($task);
        }
    }    
}
