<?php
/**
 * 	RuneScape minispēļu un D&D sadaļa
 *
 *  /minispeles
 *  /distractions-diversions
 */

class Minigames extends Controller {

    private $cat_minigames;
    private $cat_distractions;
    
    public function __construct() {
        global $cat_minigames, $cat_distractions;

        $this->cat_minigames =& $cat_minigames;
        $this->cat_distractions =& $cat_distractions;
        
        parent::__construct();
    }

    public function index() {

        $this->model('models/other');
        
        $cat_id = ($this->category->id === 'minispeles') ? 160 : 792;

        $this->view->newBlock('minigames');
        
        /*
        // augšējais sadaļas intro teksts
        if ($cat_id == 160) {
            $this->view->newBlock('minigames-intro');
        } else {
            $this->view->newBlock('diversions-intro');
        }
        */
        
        // nosaka atvērto cilni
        if ($this->category->textid === 'minispeles') {
            $this->view->assign('top-content-title', 'RuneScape minispēles');
            $this->show_list($this->cat_minigames);
        } else {
            $this->view->assign('top-content-title', 'Distractions & Diversions');
            $this->show_list($this->cat_distractions);
        }
        
        /*
        // moderatoriem redzama poga, kas aizved uz sadaļu, 
        // kur pamācību rakstiem var pievienot dažādu papildinformāciju
        if (im_mod()) {
            $this->view->newBlock('mg-info-button');
        }
        */
    }
    
    private function show_list($cat_id = 0) {
    
        $minigames = $this->other->fetch_minigames($cat_id);
        if (!$minigames) {
            $this->view->newBlock('..');
            return;
        }
        
        foreach ($minigames as $game) {

            // mainīgo raksturiezīmju pārveidošana
            if ($user = get_user($game->page_author)) {
                $game->page_author = '<a href="' . mkurl('user', $user->id, $user->nick) . '">' . usercolor($user->nick, $user->level) . '</a>';
            }

            $game->avatar = ($game->avatar != '') ?
                    '<a href="/read/' . $game->page_strid . '">
                    <img class="mg-av" src="http://img.exs.lv/' . $game->avatar . '" title="' . $game->page_title . '" alt="">
                </a>' : '';

            $game->page_date = date('d.m.Y', strtotime($game->page_date));
            $game->page_title = str_replace('[D&amp;D] ', '', $game->page_title);

            // ja izdevies atlasīt papildinfo par rakstu no `rs_pages` tabulas...
            if ($game->rspage_id != '0') {

                if ($game->members_only == 1) {
                    $game->members_only = 'Jā';
                } else $game->members_only = 'Nē';

            } else {
                $game->members_only = 'Nē';
            }

            $this->view->newBlock('minigame');
            $this->view->assignAll($game);
        }        
    }
}
