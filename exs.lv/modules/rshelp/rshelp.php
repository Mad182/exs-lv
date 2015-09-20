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

require_once(CORE_PATH.'/modules/runescape/class.controller.php');

class Rshelp extends Controller {
    
    // skaits attiecas uz rakstiem
    private $max_per_page = 30;

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
        'achievements'              => 'tasks'
    );
    
    public function __construct() {
        $this->globals(array('cat_padomi', 'cat_rsnews'));
        parent::__construct();
    }
    
    /**
     *  Ielādēs pareizo apakšmoduli
     */
    public function index() {

        $this->tpl_options = 'no-right';
        
        if (array_key_exists($this->category->textid, $this->submodules)) {
        
            $file_name = $this->submodules[$this->category->textid];
            $this->subview('submodules/'.$file_name, 'sub-template');
            $this->submodule('submodules/'.$file_name);

        // RuneScape ziņas (nedaudz smukākas par parastu sarakstu)
        } else if ($this->category->id == $this->cat_rsnews) {
            $this->show_news_list();
        
        // pārējo sadaļu raksti tiks izdrukāti vienkāršā tabulas sarakstā
        } else {
            $this->show_default_list();
        }
    }
    
    /**
     *  Parādīs RuneScape ziņu rakstus
     *  
     *  (Nedaudz glītāk par parasto sarakstu, kāds ir citām sadaļām.)
     *  @see $this->show_default_list()
     */
    private function show_news_list() {
    
        $this->tpl_options = '';
        $this->model('models/rshelp');
        
        $items = $this->rshelp->fetch_news($this->category->id);
        if (!$items) {
            $this->view->newBlock('no-pages-found');
            return;
        }
        
        $this->view->newBlock('rs-articles');
        
        foreach ($items as $article) {
        
            if (!$article->nick) {
                $article->nick = 'Nezināms';
                $article->level = 0;
            }

            $date = display_time(strtotime($article->date));

            // saīsina tekstu, lai pie katra raksta būtu tikai fragments
            if (!empty($article->intro) && strlen($article->intro) < 400) {
                $article->text = $article->intro;
            } else {
                $article->text = str_replace('<li>', ' • ', 
                    str_replace(array('&nbsp;', '<br />'), ' ', 
                                youtube_title($article->text))
                );
                $article->text = textlimit(strip_tags(trim(
                    $article->text)), 400
                );
                $article->intro = sanitize($article->text);
                $this->db->query("
                    UPDATE `pages` SET `intro` = '$article->intro' 
                    WHERE `id` = '$article->id' LIMIT 1
                ");
            }

            // dati
            $this->view->newBlock('rs-article');
            $this->view->assign(array(
                'id'        => $article->id,
                'url'       => '/read/' . $article->strid,
                'aurl'      => mkurl('user', $article->author, $article->nick),
                'title'     => $article->title,
                'views'     => $article->views,
                'date'      => $date,
                'author'    => usercolor($article->nick, $article->level),
                'posts'     => $article->posts,
                'intro'     => $article->text
            ));
            
            // avatars
            if ($article->avatar) {
                $this->view->newBlock('article-avatar');
                $this->view->assign('image', trim($article->avatar));
            }
            
            // lappuses
            $lim_start = (isset($_GET['skip']) && (int)$_GET['skip'] > 0) ? 
                (int)$_GET['skip'] : 0;

            $pager = pager($this->category->stat_topics, $lim_start, 
                           20, '/runescape?skip=');

            $this->view->assignGlobal(array(
                'pager-next'    => $pager['next'],
                'pager-prev'    => $pager['prev'],
                'pager-numeric' => $pager['pages']
            ));
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
            $this->view->newBlock('no-pages-found');
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
        
        // lappuses visām sadaļām, atskaitot /padomi, 
        // kur uzreiz redzami visi raksti
        if ($this->category->id != $this->cat_padomi) {
        
            $lim_start = (isset($_GET['skip']) && (int)$_GET['skip'] > 0) ? 
                (int)$_GET['skip'] : 0;
            
            $pager = pager($this->category->stat_topics, $lim_start, 
                           $this->max_per_page, '/runescape?skip=');
            
            $this->view->newBlock('show-pager');
            $this->view->assignGlobal(array(
                'pager-next' => $pager['next'],
                'pager-prev' => $pager['prev'],
                'pager-numeric' => $pager['pages']
            ));
        }      
    }
}

init_mvc();
