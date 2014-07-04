<?php
/**
 * 	RuneScape prasmju sadaļa
 */

class Skills extends Controller {

    private $max_per_page;

    public function index() {
    
        $this->max_per_page = 6;
    
        // jquery pieprasījums atgriezt citu prasmes rakstu lapu
        if (isset($_GET['skill']) && isset($_GET['page'])) {
            echo $this->get_skill_page();
            exit;
        
        // tabula ar nepieciešamo xp skaitu katram līmenim
        } else if (isset($_GET['var1']) && $_GET['var1'] === 'xp-table') {
            $this->view->newBlock('list-tabs');
            $this->view->assign('tab-xptable', 'active');
            $this->view->newBlock('skills-xp-table');
        
        // ar prasmēm saistīti fakti
        } else if (isset($_GET['var1']) && $_GET['var1'] === 'facts') {
            $this->view->newBlock('list-tabs');
            $this->view->assign('tab-facts', 'active');        
            $this->view->newBlock('skills-facts');
        
        // prasmju saraksts ar tām piesaistītiem rakstiem
        } else {
            $this->show_skill_blocks();
        }
    }
    
    /**
     *  Saraksts ar RuneScape prasmēm
     *
     *  Pie katras prasmes ir tās attēls, neliels apraksts un blakus -
     *  prasmes sadaļai pievienotie raksti
     */
    private function show_skill_blocks() {
        
        $this->view->newBlock('list-tabs');
        $this->view->assign('tab-skills', 'active');
        $this->view->newBlock('skills-intro-text');

        $this->model('models/guides');
    
        $pages = $this->guides->fetch_skills();        
        if (!$pages) {
            $this->view->newBlock('no-guides-found');
            return;
        }
        
        $this->view->newBlock('skills');

        $skill_counter  = 0; 
        $skill_id       = 0; // fiksē ciklā ejošo prasmi
        $page_counter   = 0; // skaita rakstus katras prasmes iekšienē

        foreach ($pages as $skill) {

            // constitution atsevišķi nebūs,
            // jo jau parādās pie Melee, kas atzīmēta kā prasme/kategorija
            if ($skill->cat_id == 191) {
                continue;
            }

            // mainoties prasmei, izveido jaunu prasmes bloku
            if ($skill_id != $skill->cat_id) {

                $skill_counter++;

                // ja vairāk par x linkiem, izvada pogu uz nākamo lapu
                if ($page_counter > $this->max_per_page) {
                    $this->view->gotoBlock('skill');
                    $addr  = '<a class="skill-pager" ' .
                        'href="/prasmes?skill='.$skill_id.'&amp;page=2">';
                    $addr .= 'Tālāk &rsaquo;&rsaquo;</a>';
                    
                    $this->view->newBlock('skill-pages');
                    $this->view->assign('next', $addr);
                }

                if ((int)$skill->members_only === 1) {
                    $skill->members_only = 
                        '<img src="/bildes/runescape/star-p2p-small.png" ' .
                        'title="Pieejama tikai maksājošajiem spēlētājiem">';
                } else {
                    $skill->members_only = '';
                }
                $skill->img = '/bildes/runescape/skills/'.$skill->img;

                $this->view->newBlock('skill');
                $this->view->assignAll($skill);

                // pārmet jaunā rindā katru nepāra prasmi
                if ($skill_counter % 2 != 0) {
                    $this->view->assign('linebreak', ' style="clear:left"');
                } else {
                    $this->view->assign('linebreak', '');
                }

                $skill_id = $skill->cat_id;
                $page_counter = 0;
            }

            $page_counter++;
            if ($page_counter > $this->max_per_page) {
                continue;
            }

            // izdrukā prasmes blokā rakstu
            if ($skill->page_id != '0') {
                $skill->cat_title = textlimit($skill->cat_title, 30);
                $this->view->newBlock('new-skill-guide');
                $this->view->assignAll($skill);
            }
        }

        // ciklā pārbaude pēdējai prasmei tika izlaista, tāpēc jāpārbauda šeit
        if ($page_counter > $this->max_per_page) {
            $addr  = '<a class="skill-pager" ' .
                'href="/prasmes?skill='.$skill_id.'&amp;page=2">' . 
                'Tālāk &rsaquo;&rsaquo;</a>';
            $this->view->assign('next', $addr);
        }
    }
    
    /**
     *  Atgriež atbildi js pieprasījumam par prasmes rakstiem
     *  noteiktā rakstu lappusē
     */
    private function get_skill_page() {

        $this->model('models/guides');
        
        $pages = $this->guides->fetch_skill_pages($this->max_per_page);        
        if (!$pages) {
            return '';
        }

        if (($view = $this->view('submodules/skills')) === false) return '';
        $view->newBlock('js-skill-pages');
        
        if ($_GET['page'] == 2) {
            $view->newBlock('page-block-back');
        } else {
            $view->newBlock('page-block');
        }        
        $view->assign('skill-id', (int)$_GET['skill']);

        foreach ($pages as $page) {
            $short_title = textlimit($page->title, 40);
            $view->newBlock('skill-page');
            $view->assignAll($page);
            $view->assign('short-title', $short_title);
        }
        
        return $view->getOutputContent();
    }
}
