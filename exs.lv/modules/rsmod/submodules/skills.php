<?php
/**
 *  Kvestiem nepieciešamo prasmju līmeņu pārvaldība
 *
 *  Saraksts, kurā katrai prasmei var norādīt augstāko nepieciešamo līmeni un
 *  kvestu, kuram tāds nepieciešams.
 */

class Skills extends Controller {

    public function index() {

        $this->model('models/skills');

        if (isset($_POST['submit'])) {
            $this->submit($_POST);
        } else {
            $this->show_skills();
        }
    }

    /**
     *  Parādīs prasmju datu rediģēšanas formu
     */
    private function show_skills() {

        $this->view->newBlock('skill-requirements');

        $skills = $this->skills->fetch_skills();        
        if (!$skills) {
            $this->view->newBlock('no-skills-added');
            return;
        }

        $this->view->newBlock('skills-notes');
        $this->view->newBlock('skills-form');
        $this->view->newBlock('skills-column');

        // tabulai būs divas kolonnas
        $counter = 0;
        $split_by = floor(count($skills) / 2);

        foreach ($skills as $data) {

            $this->view->newBlock('single-skill');
            $this->view->assignAll($data);

            // prasībām, kas nav prasmes, būs cits fons
            // (piemēram, combat, tasks, total u.c.)
            if ($data->is_special) {
                $this->view->assign('special', ' class="is-special-input"');
            } else if ($data->page_id == 0) {
                $this->view->assign('special', ' class="is-not-set"');
            }

            if ($counter++ == $split_by) {
                $this->view->newBlock('skills-column');
            }
        }
    }

    /**
     *  Atjaunos informāciju datubāzē
     *
     *  @param array $post_arr  masīvs ar $_POST datiem
     */
    private function submit($post_arr = null) {

        $skills = $this->skills->fetch_skills();

        if (empty($post_arr) || !$skills) {
            set_flash('Informāciju neizdevās atjaunot');
            redirect('/'.$_GET['viewcat']);
        }
        
        foreach ($skills as $skill) {
        
            if (isset($post_arr['level-'.$skill->id]) && 
                isset($post_arr['quest-'.$skill->id])) {
                
                $page_value = 0;
                $title = '';
                
                // pārbauda, vai eksistē raksts ar tādu strid
                if ($post_arr['quest-'.$skill->id] !== '') {
                
                    $title = input2db($post_arr['quest-'.$skill->id], 256);
                    $article = $this->skills->fetch_page($title);
                    
                    if ($article) {
                        $page_value = $article->id;
                        $title = $article->strid;
                    }
                }

                $level_value = (int)$post_arr['level-'.$skill->id];
                if ($level_value < 1) {
                    $level_value = 1;
                } else if ($level_value > 2500) {
                    $level_value = 99;
                }

                // atjauno informāciju datubāzē
                $values = array(
                    'level'         => $level_value,
                    'page_id'       => (int)$page_value,
                    'page_title'    => $title
                );
                $this->db->update('rs_skills', (int)$skill->id, $values);
            }
        }

        set_flash('Informācija atjaunināta');
        redirect('/'.$_GET['viewcat']);
    }
}
