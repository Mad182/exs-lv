<?php
/**
 * 	RuneScape pamācību sadaļas
 *
 *  Starp tām ietilpst:
 *
 *      - kvesti (f2p, p2p, mini-)
 *      - minispēles
 *      - distractions & diversions
 *      - tasks
 *      - u.c.
 */

class Rshelp extends Controller {

    // [category->textid] => [file name]
    private $submodules = array(
        'kvestu-pamacibas'          => 'quests',
        'f2p-kvesti'                => 'quests',
        'p2p-kvesti'                => 'quests',
        'mini-kvesti'               => 'quests',
        'minispeles'                => 'minigames',
        'distractions-diversions'   => 'minigames',
        'prasmes'                   => 'skills',
        'gildes'                    => 'guilds',
        'tasks'                     => 'tasks'
    );
    
    /**
     *  Ielādēs pareizo apakšmoduli
     */
    public function index() {
    
        $this->check_permission('mod');
        $this->tpl_options = 'no-right';
        
        if (array_key_exists($this->category->textid, $this->submodules)) {
        
            $file_name = $this->submodules[$this->category->textid];
            $this->subview('submodules/'.$file_name, 'sub-template');
            $this->submodule('submodules/'.$file_name);

        // pārējo sadaļu raksti tiks izdrukāti vienkāršā tabulas sarakstā
        } else {
            $this->show_default_list();
        }
    }
    
    /**
     *  Parādīs sadaļas rakstus tabulas formāta sarakstā
     */
    private function show_default_list() {

        $this->tpl_options = '';        

        $this->model('models/rshelp');
        
        $items = $this->rshelp->fetch_items($this->category->id);
        if (!$items) {
            $this->view->newBlock('..');
            return;
        }
        
        $this->view->newBlock('rshelp-list');
        $this->view->assign('category-title', $this->category->title);
        
        foreach ($items as $item => $data) {
            
            if ($user = get_user($data->author)) {
                $data->author  = '<a style="font-size:11px;"';
                $data->author .= ' href="'.mkurl('user', $user->id, $user->nick);
                $data->author .= '">'.usercolor($user->nick, $user->level).'</a>';
            }
            
            // rs rakstu virsrakstiem nodzēš kādreizējos prefixus
            $replaceable = array(
                '[Runescape] ', '[RuneScape] ', '[runescape] ', 
                '[RS] ', '[rs] '
            );
            $data->title = str_replace($replaceable, '', $data->title);
            
            $this->view->newBlock('rshelp-listitem');
            $this->view->assignAll($data);
        }
        
        // visām sadaļām, atskaitot /padomi, kur uzreiz redzami visi raksti
        /*if ($this->category->id != $this->cat_padomi) {
        
            $lim_end = 30;
            $lim_start = (isset($_GET['skip']) && (int)$_GET['skip'] > 0) ? (int)$_GET['skip'] : 0;
            
            $pager = pager($this->category->stat_topics, $lim_start, 
                           $lim_end, '/runescape?skip=');
            
            $this->view->newBlock('show-pager');
            $this->view->assignGlobal(array(
                'pager-next' => $pager['next'],
                'pager-prev' => $pager['prev'],
                'pager-numeric' => $pager['pages']
            ));
        } */       
    }
}
