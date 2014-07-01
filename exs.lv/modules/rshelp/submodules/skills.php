<?php
/**
 * 	RuneScape prasmju sadaļa
 */

class Skills extends Controller {

    public function index() {
    
        // izdrukā lapā ievadtekstu par prasmēm kā tādām
        $this->view->newBlock('skills-intro');

        $this->model('models/other');
        $this->show_skill_blocks();
        
        $this->view->newBlock('skills-facts');
        $this->view->newBlock('skills-xp-table');
    }
    
    /**
     *
     */
    private function show_skill_blocks() {
    
        $pages = $this->other->fetch_skill_pages();
        
        if (!$pages) {
            $this->view->newBlock('..');
            return;
        }
        
        $this->view->newBlock('skills');

        $skill_counter = 0; // skaita izvadīto prasmju skaitu    
        $skill_id = 0; // fiksē ciklā ejošo prasmi
        $page_counter = 0; // skaita rakstus katras prasmes ietvarā

        foreach ($pages as $skill) {

            // constitution atsevišķi nebūs,
            // jo jau parādās pie Melee, kas atzīmēta kā prasme/kategorija
            if ($skill->cat_id == 191) {
                continue;
            }

            // mainoties prasmei, izveido jaunu prasmes bloku
            if ($skill_id != $skill->cat_id) {

                $skill_counter++;

                // ja vairāk par 5 linkiem, izvada pogu uz nākamo lapu;
                // pirms pirmās prasmes neizvadīs, jo skaitītājs ir 0,
                // turpretī pēdējo prasmi izlaidīs, jo izies ārpus cikla,
                // tāpēc tā jāpārbauda pēc cikla
                if ($page_counter > 5) {
                    $this->view->gotoBlock('skill');
                    $addr = '<a class="skill-pager" href="/rs-skills/?skill=' . $skill_id . '&amp;page=2">';
                    $addr .= 'Tālāk &rsaquo;&rsaquo;</a>';
                    
                    $this->view->newBlock('skill-pages');
                    $this->view->assign('next', $addr);
                }

                // pārbaude, vai izdevās pieprasījumā atlasīt
                // papildinformāciju no rs klašu tabulas
                if ($skill->class_id != '0') {
                    $skill->members_only = ($skill->members_only == 1) ?
                            ' <img src="/bildes/runescape/p2p_small.png" title="members only">' : '';
                    $skill->class_img = '/bildes/runescape/skills/' . $skill->class_img;
                } else {
                    $skill->members_only = '';
                    $skill->class_img = '';
                    $skill->class_info = '';
                }

                $this->view->newBlock('skill');
                $this->view->assign(array(
                    'title' => $skill->cat_title,
                    'img' => $skill->class_img,
                    'info' => $skill->class_info,
                    'members' => $skill->members_only
                ));

                // pārmet jaunā rindā katru nepāra prasmi
                if ($skill_counter % 2 != 0) {
                    $this->view->assign('linebreak', ' style="clear:left"');
                } else {
                    $this->view->assign('linebreak', '');
                }

                // Linux fontu dēļ Linux lietotājiem uzliek citu klasi ar citiem bloku izmēriem
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'inux') !== false) {
                    $this->view->assign('forlinux', '-2');
                }

                $skill_id = $skill->cat_id;
                $page_counter = 0; // pie katras prasmes jāizvada tikai pirmie pieci raksti
            }

            // jāzina pievienoto rakstu skaits, lai prasmes blokā
            // pēc vajadzības izvadītu pārvietošanos pa rakstu lappusēm
            $page_counter++;

            // pie katras prasmes nebūs vairāk par 5 rakstiem
            if ($page_counter > 5) {
                continue;
            }

            // izdrukā prasmes blokā rakstu
            $skill->cat_title = textlimit($skill->cat_title, 30);
            $this->view->newBlock('skill-link');
            $this->view->assignAll($skill);
        }

        // ciklā pārbaude pēdējai prasmei tika izlaista, tāpēc jāpārbauda šeit
        if ($page_counter > 5) {
            $addr = '<a class="skill-pager" href="/rs-skills/?skill=' . $skill_id . '&amp;page=2">';
            $addr .= 'Tālāk &rsaquo;&rsaquo;</a>';
            $this->view->assign('next', $addr);
        }
    }
}
