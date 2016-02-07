<?php
/**
 *  RuneScape kvestu pamācību sadaļas, tai skaitā sākumlapa,
 *  p2p/f2p kvesti un minikvesti
 */

class Quests extends Controller {
    
    public function __construct() {
        $this->globals(array(
            'cat_f2p_quests', 
            'cat_miniquests'
        ));
        parent::__construct();
    }

    public function index() {
        
        $this->view->newBlock('list-tabs');
        
        if ($this->category->textid === 'kvestu-pamacibas' && 
            isset($_GET['var1'])) {
        
            // statistikas cilne
            if ($_GET['var1'] === 'stats') {
                if ($this->auth->id == 115 && isset($_GET['force'])) {
                    $this->stats_tab(true);
                } else {
                    $this->stats_tab();
                }
            
            // faktu cilne
            } else if ($_GET['var1'] === 'facts') {
                $this->view->assign('tab-facts', 'active');
                $this->view->newBlock('facts-block');
            
            // cilne ar prasmju prasībām
            } else if ($_GET['var1'] === 'skill-reqs') {
                $this->reqs_tab();
            
            } else {
                set_flash('No hacking, pls');
                redirect();
            }

        } else if ($this->category->textid === 'kvestu-pamacibas') {
            $this->series_tab();

        } else if ($this->category->textid === 'p2p-kvesti') {
            $this->show_p2p();
            
        } else if ($this->category->textid === 'f2p-kvesti') {
            $this->show_common_quests($this->cat_f2p_quests);

        } else if ($this->category->textid === 'mini-kvesti') {
            $this->show_common_quests($this->cat_miniquests);

        } else {
            set_flash('No hacking, pls');
            redirect();
        }
    }
    
    /**
     *  Sēriju cilne
     */
    private function series_tab() {
    
        $this->tpl_options = 'no-left-right';
    
        $this->view->assign('tab-series', 'active');
        $this->view->newBlock('series-intro-text');        
        $this->view->assign('intro-image', 
                            '/bildes/runescape/intro/khazard.png');

        $this->model('models/quests');
       
        $series = $this->quests->fetch_series();    
        if (!$series) {
            $this->view->newBlock('no-series-found');
            return;
        }

        $this->view->newBlock('series-block');
        
        $temp_series    = 0; // ciklā fiksē ejošo sērijas id
        $series_count   = 0;

        foreach ($series as $single) {

            // izveido jaunu sēriju, ja nesakrīt pieglabātais id
            if ($single->series_id != $temp_series) {
            
                $this->view->newBlock('single-series');
                $this->view->assignAll($single);

                $series_count++;
                $temp_series = $single->series_id;

                // ik pēc x sērijām pārlec uz jaunu rindu
                if ($series_count > 1 && ($series_count - 1) % 4 == 0) {
                    $this->view->assign('newline', ' style="clear:left"');
                }
            }

            $addr = $single->title;
            if ($single->pages_id != '0') {
                $addr = '<a href="/read/'.$single->strid.'">'.$addr.'</a>';
            } else {
                $addr = '<a class="cluetip" href="javascript:void(0)"'.
                    ' title="|Pamācība iztrūkst">'.$addr.'</a>';
            }

            $this->view->newBlock('series-quest');
            $this->view->assign('quest', $addr);
        }
    }
    
    /**
     *  P2P kvestu cilne
     */
    private function show_p2p() {

        $this->view->assign('tab-p2p', 'active');
        $this->view->newBlock('series-intro-text');
        $this->view->assign('intro-image', 
            '/bildes/runescape/intro/vampyre-juvinate.png');
        $this->view->newBlock('p2p-quests-block');
        
        $this->model('models/quests');

        $quests = $this->quests->fetch_p2p_quests();
        if (!$quests) {
            $this->view->newBlock('no-p2p-quests');
            return;
        }
        $this->view->newBlock('p2p-quests');

        // kvesti tiek kategorizēti pēc alfabēta burtiem;
        // mainīgais fiksē ejošo burtu
        $letter = '';

        foreach ($quests as $data) {
        
            // cluetip info
            $data->extra = '';

            $author = '';
            if ($data->page_id != '0') {                
                if ($user = get_user($data->author)) {
                    $url = mkurl('user', $user->id, $user->nick);
                    $nick = usercolor($user->nick, $user->level);
                    $author = '<a href="'.$url.'">'.$nick.'</a>';
                }
                $data->strid = '/read/'.$data->strid;
            } else {
                $data->strid = 'javascript:void(0)';
                $data->extra = ' class="cluetip placeholder" '.
                    'title="|Pamācība iztrūkst"';
            }
            $data->author = $author;

            $this->view->newBlock('p2p-quest');
            $this->view->assignAll($data);

            // ja nepieciešams, pārmaina fiksēto burtu
            if (substr($data->title, 0, 1) != $letter) {
                $letter = substr($data->title, 0, 1);
                $this->view->assign(array(
                    'letter' => '<b>'.$letter.'</b>',
                    'border' => ' class="border"',
                ));
            }
        }
    }
    
    /**
     *  F2P/minikvestu cilne
     *
     *  @param $category_id     vai nu f2p, vai minikvestu sadaļa
     */
    private function show_common_quests($category_id = 0) {

        $intro_img  = 'citharede-sister.png';
        $folder     = 'miniquests';
        if ((int)$category_id === $this->cat_f2p_quests) {
            $intro_img  = 'hazelmere.png';
            $folder     = 'freequests';
            $this->view->assign('tab-f2p', 'active');
        } else {
            $this->view->assign('tab-miniquests', 'active');
        }

        $this->view->newBlock('series-intro-text');
        $this->view->assign('intro-image', 
                            '/bildes/runescape/intro/'.$intro_img);                            
        $this->view->newBlock('common-quests');
        
        $this->model('models/quests');
        $pages = $this->quests->fetch_common_quests($category_id);
        if (!$pages) {
            $this->view->newBlock('no-quests-found');
            return;
        }
        $this->view->newBlock('quests-found');

        foreach ($pages as $quest) {

            // lauku vērtību pārbaudes
            $author = '';
            if ($user = get_user($quest->author)) {
                $quest->author = 
                    'no <a href="'.mkurl('user', $user->id, $user->nick).'">';
                $quest->author .= 
                    usercolor($user->nick, $user->level).'</a>';
            }
            
            if (!empty($quest->date)) {
                $quest->date = '@ '.date('d.m.Y', strtotime($quest->date));
            }   
            
            // "placeholderiem" nav adreses, kuru atvērt
            if ($quest->page_id == '0') {
                $quest->strid = 'javascript:void(0);';
            } else {
                $quest->strid = '/read/'.$quest->strid;
            }
            
            // attēls + cluetip
            $image = '';
            $cluetip = $quest->title;
            $clue_class = '';
            if ($quest->page_id == '0') {
                $cluetip = '|Pamācība iztrūkst';
                $clue_class = ' class="cluetip"';
                $quest->clue = ' class="cluetip placeholder"'.
                    ' title="|Pamācība iztrūkst"';
            }
            if (empty($quest->image)) {
                $image = '/bildes/runescape/fallback-wide.png';
            } else {
                $image = '/bildes/runescape/'.$folder.'/'.$quest->image;
            }
            $quest->image  = '<img src="'.$image.'"'.$clue_class.
                ' title="'.$cluetip.'" alt="">';
            
            // sākotnēji nav paragrāfa tagu, lai nebūtu lieki tukšu rindu
            if (!empty($quest->description)) {
                $quest->description = textlimit($quest->description, 280, '...');
                $quest->description = '<p>'.$quest->description.'</p>';
            }

            $this->view->newBlock('common-quest');
            $this->view->assignAll($quest);
        }
    }
    
    /**
     *  Statistikas cilne
     */
    private function stats_tab($force = false) {

        $this->view->assign('tab-stats', 'active');
        $this->view->newBlock('stats-block');

        $this->model('models/quests');

        $stats = $this->quests->fetch_stats($force);        
        if (!$stats) {
            $this->view->newBlock('no-stats-found');
            return;
        }

        $this->view->newBlock('stats-found');
        foreach ($stats as $key => $value) {
            // apakšmasīvs, kur pie katra gada norādīts iznākušo kvestu skaits
            if ($key === 'years') {
                foreach ($value as $single_year => $quest_count) {
                    $this->view->newBlock('stats-single-year');
                    $this->view->assignAll(array(
                        'short-year' => $single_year,
                        'quest-count' => $quest_count
                    ));
                    if ($single_year == date('Y')) {
                        $this->view->assign('row-class', 'space');
                    }
                }
            } else { // citas vērtības
                $this->view->assign($key, $value);
            }
        }   
    }
    
    /**
     *  Prasību cilne
     *
     *  Tabula ar augstākajām prasībām visās prasmēs,
     *  kādas nepieciešamas kādam no kvestiem
     */
    private function reqs_tab() {
        
        $this->view->assign('tab-reqs', 'active');
        $this->view->newBlock('skills-block');
        
        $this->model('models/quests');
        $skills = $this->quests->fetch_skills();
        
        if (!$skills) {
            $this->view->newBlock('no-skills-found');
        } else {
            $this->view->newBlock('skills-found');
            foreach ($skills as $skill) {
                
                if ($skill->pages_id != '0') {
                    $skill->page_title = '<a href="/read/'.$skill->strid.'">'.
                        $skill->pages_title.'</a>';
                }
                $this->view->newBlock('skill-requirement');
                $this->view->assignAll($skill);
                
                // combat, tasks, total u.tml.
                if ($skill->is_special) {
                    $this->view->assign('style', 'color:#3576E9');
                }
            }
        }
    }
}

