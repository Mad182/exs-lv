<?php
/**
 *  RuneScape ģilžu pamācību saraksta sadaļa
 */

class Guilds extends Controller {

    /**
     *  Parādīs lapā visas pievienotās ģildes
     *
     *  Katrai no tām būs attēls, bet zem tā - atrašanās vieta un prasības
     */
    public function index() {
    
        $this->view->newBlock('guilds-intro-text');
    
        $this->model('models/guides');
        
        $guilds = $this->guides->fetch_guilds();
        if (!$guilds) {
            $this->view->newBlock('no-guilds-found');
            return;
        }

        $this->view->newBlock('guilds-block');
        $this->view->newBlock('not-a-guild');

        foreach ($guilds as $page) {
        
            if ((int)$page->members_only === 1) {
                $page->members_only = 
                    '<img class="guide-p2p" src="/bildes/runescape/star-p2p-small.png"'.
                    ' title="Pieejama tikai maksājošajiem spēlētājiem">';
            } else {
                $page->members_only = '';
            }
            
            // placeholder ģildēm papildikona
            if ($page->page_id == '0') {
                $page->cluetip = ' class="cluetip"';
                $page->cluetip_title = '|Ģildei nav pamācības';
            } else {
                $page->placeholder = '';
                $page->cluetip_title = $page->title;
            }
            
            // adreses placeholderiem arī nav
            if ($page->page_id != '0') {
                $page->strid = '/read/'.$page->strid;
            } else {
                $page->strid = 'javascript:void(0);';
            }

            // ja rakstam ir pievienots attēls, to uzskata par ģildes rakstu
            if (!empty($page->image)) {
                $this->view->newBlock('guild');
                $this->view->assignAll($page);

            // pretējā gadījumā rakstu pievieno nekategorizētajiem rakstiem
            } else {
                $this->view->newBlock('not-guild-page');
                $this->view->assignAll($page);
            }
        }
    }
}
