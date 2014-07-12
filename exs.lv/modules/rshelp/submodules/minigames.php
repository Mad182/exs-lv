<?php
/**
 *  RuneScape minispēļu un D&D pamācību sadaļa
 *
 *  Adreses:
 *
 *      /minispeles
 *      /distractions-diversions
 */

class Minigames extends Controller {

    // sadaļu id
    private $cat_minigames;
    private $cat_distractions;
    
    // minispēļu īpašības
    private $count_f2p;
    private $count_p2p;
    private $count_safe;
    private $count_unsafe;
    
    public function __construct() {

        global $cat_minigames, $cat_distractions;
        $this->cat_minigames =& $cat_minigames;
        $this->cat_distractions =& $cat_distractions;
        
        $this->count_f2p = 0;
        $this->count_p2p = 0;
        $this->count_safe = 0;
        $this->count_unsafe = 0;
        
        parent::__construct();
    }

    /**
     *  Atkarībā no sadaļas nosaka parādāmo saturu
     */
    public function index() {

        $this->view->newBlock('minigames');

        if ($this->category->textid === 'minispeles') {

            $this->view->newBlock('mg-intro-text');
            $this->show_list($this->cat_minigames);

        } else {
            $this->view->newBlock('dd-intro-text');
            $this->show_list($this->cat_distractions);
        }
    }
    
    /**
     *  Saraksts ar minispēlēm vai D&D aktivitātēm
     *
     *  Katram ierakstam kreisajā pusē ir neliels attēls, bet blakus -
     *  īss apraksts.
     */
    private function show_list($cat_id = 0) {
    
        $this->model('models/guides');
    
        $minigames = $this->guides->fetch_minigames($cat_id);
        if (!$minigames) {
            $this->view->newBlock('no-guides-found');
            return;
        }

        $this->view->newBlock('minigames-list');
        
        foreach ($minigames as $game) {

            if (!empty($game->avatar)) {
                $avatar  = '<a href="/read/'.$game->strid.'">';
                $avatar .= '<img src="http://img.exs.lv/'.$game->avatar.'" ';
                $avatar .= 'title="'.$game->title.'" alt=""></a>';
                $game->avatar = $avatar;
            } else {
                $avatar  = '<a href="javascript:void(0)">';
                $avatar .= '<img src="/bildes/runescape/fallback.png" ';
                $avatar .= 'title="'.$game->title.'" alt=""></a>';
                $game->avatar = $avatar;
            }

            // D&D rakstiem vēl mēdz būt šāds prefix
            $game->title = str_replace('[D&amp;D] ', '', $game->title);
            
            // placeholderiem adresi nevar norādīt...
            if ($game->page_id != '0') {
                $game->title = 
                    '<a href="/read/'.$game->strid.'">'.$game->title.'</a>';
            } else {
                $game->title = 
                    '<a class="placeholder">'.$game->title.'</a>';
            }

            $this->view->newBlock('minigame');
            $this->view->assignAll($game);

            if ($game->page_id == '0') {
                $this->view->assign('cluetip', 
                    ' class="cluetip" title="Pamācība iztrūkst"');
            }

            if ($game->members_only) {
                $this->view->newBlock('p2p-only');
                $this->count_p2p++;
            } else {
                $this->count_f2p++;
            }
           
            // vai bīstama minispēle ar iespēju mirt?
            if (!$game->safe) {
                $this->view->newBlock('unsafe-minigame');
                $this->count_unsafe++;
            } else {
                $this->count_safe++;
            }
        }

        $this->show_stats();
    }
    
    /**
     *  Ievieto skatā bloku ar statistikas datiem par minispēlēm/d&d
     */
    private function show_stats() {

        $this->view->newBlock('minigames-statistics');
        $this->view->assign(array(
            'f2p-only'  => $this->count_f2p,
            'p2p-only'  => $this->count_p2p,
            'safe'      => $this->count_safe,
            'unsafe'    => $this->count_unsafe
        ));
    }
}
