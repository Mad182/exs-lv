<?php
/**
 *  RuneScape kvestu pamācību sadaļas, tai skaitā sākumlapa,
 *  p2p/f2p kvesti un minikvesti
 */

class Quests extends Controller {

    private $cat_f2p_quests;
    private $cat_miniquests;
    
    public function __construct() {
        global $cat_f2p_quests, $cat_miniquests;

        $this->cat_f2p_quests =& $cat_f2p_quests;
        $this->cat_miniquests =& $cat_miniquests;
        
        parent::__construct();
    }

    /**
     *  
     */
    public function index() {
        
        $this->view->newBlock('quests-intro');
        
        // moderatoriem redzama poga, kas aizved uz sadaļu, 
        // kur pamācību rakstiem var pievienot dažādu papildinformāciju
        if (im_mod()) {
            $this->view->newBlock('quests-info-button');
        }
        
        if ($this->category->textid === 'kvestu-pamacibas') {
            $this->show_index();

        } else if ($this->category->textid === 'p2p-kvesti') {
            $this->show_p2p();
            
        } else if ($this->category->textid === 'f2p-kvesti') {
            $this->show_other($this->cat_f2p_quests);

        } else if ($this->category->textid === 'mini-kvesti') {
            $this->show_other($this->cat_miniquests);

        // hackz
        } else {
            set_flash('No hacking, pls');
            redirect();
        }
    }
    
    /**
     *  Kvestu sēriju cilne
     */
    private function show_index() {

        // bildes adrese nav ielikta templeitā,
        // jo citās kvestu sadaļās tajā pašā vietā būs jau cits attēls
        $this->view->assign('intro-image', '/bildes/runescape/intro/khazard.png');
       
        $this->model('models/quests');
       
        $series = $this->quests->fetch_series();    
        if (!$series) {
            $his->view->newBlock('..');
        } else {

            $this->view->newBlock('quests-series');
            
            $temp_series    = 0; // ciklā fiksē ejošo sērijas id
            $series_count   = 0;

            foreach ($series as $single) {

                // izveido jaunu sēriju, ja nesakrīt pieglabātais id
                if ($single->series_id != $temp_series) {
                
                    $this->view->newBlock('single-series');
                    $this->view->assignAll($single);

                    $series_count++;
                    $temp_series = $single->series_id;

                    // ik pēc 4 sērijām pārlec uz jaunu rindu
                    if ($series_count > 1 && ($series_count - 1) % 4 == 0) {
                        $this->view->assign('newline', ' style="clear:left"');
                    }
                }

                $quest_addr = $single->title;
                if ($single->pages_id != '0') {
                    $quest_addr = '<a href="/read/'.$single->pages_strid.'">'.$quest_addr.'</a>';
                } else {
                    $quest_addr = '<a href="javascript:void(0);">'.$quest_addr.'</a>';
                }

                $this->view->newBlock('series-quest');
                $this->view->assign('page_title', $quest_addr);
            }

            // kvestu statistika, fakti un nepieciešamās prasmes
            $this->view->newBlock('quests-outro');

            // kvestu statistika
            $stats = get_quests_stats();
            if ($stats) {
                $this->view->newBlock('quests-stats');
                $this->view->assign(array(
                    '2014'          => $stats[14],
                    '2013'          => $stats[13],
                    '2012'          => $stats[12],
                    '2011'          => $stats[11],
                    '2010'          => $stats[10],
                    'older'         => $stats['older'],
                    'p2p'           => $stats['p2p'],
                    'f2p'           => $stats['f2p'],
                    'miniquests'    => $stats['miniquests'],
                    'special'       => $stats['special'],
                    'grandmaster'   => $stats['grandmaster'],
                    'master'        => $stats['master'],
                    'experienced'   => $stats['experienced'],
                    'intermediate'  => $stats['intermediate'],
                    'novice'        => $stats['novice']
                ));
            }

            $this->view->newBlock('quests-facts');

            // nepieciešamās prasmes, lai izietu visus kvestus
            $skills = $this->quests->fetch_skills();
            if ($skills) {
                $this->view->newBlock('max-skills');
                foreach ($skills as $skill) {
                    $this->view->newBlock('skill-requirement');
                    $this->view->assignAll($skill);
                }
            }
        }
    }
    
    /**
     *  Maksas kvestu cilne
     */
    private function show_p2p() {

        // bildes adrese nav ielikta templeitā,
        // jo citās kvestu sadaļās tajā pašā vietā būs jau cits attēls
        $this->view->assign('intro-image', '/bildes/runescape/intro/vampyre-juvinate.png');
        
        $this->model('models/quests');
        
        $quests = $this->quests->fetch_p2p_quests();
        if (!$quests) {
            $this->view->newBlock('..');
            return;
        }

        $this->view->newBlock('p2p-quests');

        // kvesti tiek kategorizēti pēc alfabēta burtiem;
        // mainīgais fiksē ejošo burtu
        $letter = '';

        foreach ($quests as $data) {

            $this->view->newBlock('p2p-quest');
            $this->view->assignAll($data);

            // atlasa datus par raksta autoru
            $author = '';
            if ($user = get_user($data->page_author)) {
                $author = '<a style="font-size:11px;" href="' . mkurl('user', $user->id, $user->nick) . '">' . usercolor($user->nick, $user->level) . '</a>';
            }
            $this->view->assign('page-author', $author);

            // ja nepieciešams, pārmaina fiksēto burtu
            if (substr($data->page_title, 0, 1) != $letter) {
                $letter = substr($data->page_title, 0, 1);
                $this->view->assign(array(
                    'letter' => '<b>' . $letter . '</b>',
                    'border' => ' class="border"',
                ));
            }

            // ja raksts ir novecojis, parāda info, ka to būtu vēlams atjaunot
            /*if ($data->rspages_old != '0') {

                $title = ($data->rspages_old == 1) ?
                        'Pamācībai nepieciešamas jaunākas, labākas kvalitātes bildes!' :
                        'Pamācību nepieciešams atjaunināt!';
                $picture = ($data->rspages_old == 1) ? 'info_yellow_sm.png' : 'info_red_sm.png';

                $this->view->assign('warning', '<img class="warning_small" src="/bildes/runescape/' . $picture . '" title="' . $title . '" alt="">');
            }*/
        }
    }
    
    /**
     *  F2P/minikvestu cilne
     */
    private function show_other($category_id = 0) {
    
        $intro_img  = 'citharede-sister.png';
        $folder     = 'miniquests';
        $title      = 'RuneScape minikvesti';

        if ((int)$category_id === $this->cat_f2p_quests) {
            $intro_img  = 'hazelmere.png';
            $folder     = 'freequests';
            $title      = 'RuneScape visiem spēlētājiem pieejamie kvesti';
        }
        
        $this->view->assign('intro-image', 
                            '/bildes/runescape/intro/'.$intro_img);

        $this->model('models/quests');

        $pages = $this->quests->fetch_simple_quests($category_id);
        if (!$pages) {
            $this->view->newBlock('..');
            return;
        }

        $this->view->newBlock('other-quests');
        $this->view->assign('extended-title', $title);

        foreach ($pages as $quest) {

            $author = '';
            if ($user = get_user($quest->page_author)) {
                $quest->page_author = '<a href="' . mkurl('user', $user->id, $user->nick) . '">';
                $quest->page_author .= usercolor($user->nick, $user->level) . '</a>';
            }
            $quest->page_date = date('d.m.Y', strtotime($quest->page_date));

            $this->view->newBlock('other-quest');
            $this->view->assignAll($quest);

            // banerītis pie minikvestiem/prastajiem kvestiem
            /*if ($quest->rspage_img != '') {
                $quest->rspage_img = '<img src="/bildes/runescape/' . $folder . '/' . $quest->rspage_img . '" title="' . $quest->page_title . '" alt="">';
                $this->view->assign('page_image', $quest->rspage_img);
            }*/

            // pamācība novecojusi vai nepieciešamas HD bildes
            /*if ($quest->rspage_old != 0) {

                $title = ($quest->rspage_old == 1) ?
                        'Pamācībai nepieciešamas jaunākas, labākas kvalitātes bildes!' :
                        'Pamācību nepieciešams atjaunināt!';

                $picture = ($quest->rspage_old == 1) ? 'info_yellow.png' : 'info_red.png';
                $picture = '<img class="warning" src="/bildes/runescape/' . $picture . '" title="' . $title . '" alt="">';

                $this->view->assign('warning', $picture);
            }*/
        }
    }
}

