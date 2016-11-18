<?php
/**
 *  Nekategorizēto pamācību saraksts
 *
 *  Tabulas formā aplūkojams saraksts ar tām pamācībām kādā no
 *  galvenajām rakstu sadaļām, kuras nav piesaistītas nevienam
 *  ierakstam `rs_pages` tabulā. 
 *
 *  Noder, kad kāds lietotājs uzraksta jaunu rakstu.
 *
 *  Starp pārbaudāmajām sadaļām ietilpst:
 *
 *      - kvesti (f2p, p2p, minikvesti)
 *      - minispēles
 *      - distractions & diversions
 *      - ģildes
 */

class Unlisted extends Controller {

    /**
     *  Parāda sarakstu ar rakstiem tabulas formā
     */
    public function index() {
    
        $this->model('models/unlisted');
        
        $this->view->newBlock('list-tabs');
        $this->view->assign('tab-unlisted', 'active');
        $this->view->newBlock('list-intro-unlisted');
        
        // atlasa rakstus
        $pages = $this->unlisted->fetch_pages();
        if (!$pages) {
            $this->view->newBlock('list-no-pages');
            return;
        }
        
        $this->view->newBlock('list-all-unlisted');
        
        $saved_letter = '';

        foreach ($pages as $guide) {

            $this->view->newBlock('unlisted-page');
            
            $this->view->assign([
                'page_id'   => $guide->id,
                'strid'     => $guide->strid,
                'title'     => $guide->title
            ]);
        }
    }
}
