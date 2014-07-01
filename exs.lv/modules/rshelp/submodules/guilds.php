<?php
/**
 *  RuneScape ģilžu pamācību saraksta sadaļa
 */

class Guilds extends Controller {

    public function index() {
    
        $this->model('models/other');
        
        $guilds = $this->other->fetch_guilds();

        if (!$guilds) {
            $this->view->newBlock('..');
            return;
        }

        $this->view->newBlock('guilds');
        $this->view->newBlock('guilds-not');

        foreach ($guilds as $page) {

            // ja rakstam ir pievienots attēls, to uzskata par ģildes rakstu
            if ($page->rspage_img != '') {
                $this->view->newBlock('guild');
                $this->view->assignAll($page);
            }
            // pretējā gadījumā rakstu pievieno nekategorizētajiem rakstiem
            else {
                $this->view->newBlock('guild-page');
                $this->view->assignAll($page);
            }
        }
    }
}
