<?php
/**
 *  Darbības ar datiem, kas saistīti ar jaunu minispēļu pievienošanu vai
 *  esošu ierakstu rediģēšanu
 */

class Model_List_Minigames extends Model {

    // lokālas references globālajiem mainīgajiem
    private $cat_activities;
    private $cat_minigames;
    private $cat_distractions;
    
    public function __construct() {
        global $cat_activities, $cat_minigames, $cat_distractions;

        $this->cat_activities =& $cat_activities;
        $this->cat_minigames =& $cat_minigames;
        $this->cat_distractions =& $cat_distractions;

        parent::__construct();
    }
    
    /**
     *  Atgriež norādīto ierakstu no `pages` tabulas
     */
    private function fetch_page($strid = null) {
    
        if ($strid === null) return false;
        
        $query = $this->db->get_row("
            SELECT `id` FROM `pages` 
            WHERE 
                `strid` = '" . sanitize($strid) . "' AND 
                `category` IN(".implode(', ', $this->cat_activities).")
        ");
        
        return $query;
    }
    
    /**
     *  Atgriež norādīto ierakstu no `rs_pages` tabulas
     */
    public function fetch_entry($entry_id = 0) {
    
        $entry_id = (int)$entry_id;
        if ($entry_id < 1) return false;
    
        $query = $this->db->get_row("
            SELECT           
                `rs_pages`.*,
                `pages`.`strid` AS `strid`
            FROM `rs_pages`
                LEFT JOIN `pages` ON (
                    `rs_pages`.`page_id` = `pages`.`id` AND
                    `pages`.`lang` = 9 AND
                    `pages`.`category` IN(".implode(', ', $this->cat_activities).")
                )
            WHERE 
                `rs_pages`.`id` = $entry_id AND
                `rs_pages`.`deleted_by` = 0
        ");
        
        return $query;
    }
    
    /**
     *  Pievieno jaunu ierakstu
     */
    public function post_new($post_arr = null) {
    
        if (empty($post_arr)) {
            set_flash('Kļūdaini iesniegti dati');
            redirect('/'.$_GET['viewcat']);
        }
        
        // pēc (iespējams) norādītā `strid` nolasa raksta id
        $strid = '';
        $page_id = 0;
        if (isset($post_arr['strid'])) {
            $strid = substr(h(trim(strip_tags(
                            $post_arr['strid']))), 0, 255);
        }
        // lauks drīkst būt tukšs, kas nozīmē, ka ieraksts būs placeholderis
        if ($strid !== '') {
            $if_exists = $this->fetch_page($strid);
            if ($if_exists) {
                $page_id = (int)$if_exists->id;
            }
        }

        $title = (isset($post_arr['title']) && !empty($post_arr['title'])) ?
            input2db($post_arr['title'], 255) : '--';
        $starting_point = (isset($post_arr['starting_point'])) ? 
            input2db($post_arr['starting_point'], 100) : '';
        $extra = (isset($post_arr['extra'])) ? 
            input2db($post_arr['extra'], 1024) : '';
        $description = (isset($post_arr['description'])) ? 
            input2db($post_arr['description'], 1024) : '';        
        $members_only = (isset($post_arr['members_only'])) ? 
            (int)((bool)$post_arr['members_only']) : 0;
        $safe = (isset($post_arr['safe'])) ? 
            (int)((bool)$post_arr['safe']) : 0;

        $cat = 0;
        if (isset($_GET['viewcat']) && $_GET['viewcat'] === 'all-minigames') {
            $cat = $this->cat_minigames;
        } else {
            $cat = $this->cat_distractions;
        }
        
        $values = [
            'page_id'           => $page_id,
            'cat_id'            => (int)$cat,
            'title'             => $title,
            'members_only'      => $members_only,
            'starting_point'    => $starting_point,
            'safe'              => $safe,
            'extra'             => $extra,
            'description'       => $description,
            'created_by'        => (int)$this->auth->id,
            'created_at'        => time()
        ];
        
        $this->db->insert('rs_pages', $values);
    }
    
    /**
     *  Atjauno esošu ierakstu
     */
    public function update_entry($entry_id = 0, $post_arr = null) {
    
        if (empty($post_arr)) {
            set_flash('Kļūdaini iesniegti dati');
            redirect('/'.$_GET['viewcat']);
        }
        $entry_id = (int)$entry_id;
        $entry = $this->fetch_entry($entry_id);
        if (!$entry) {
            set_flash('Kļūdaini iesniegti dati');
            redirect('/'.$_GET['viewcat']);
        }
    
        // pārbauda, vai raksts ar norādīto strid eksistē       
        if (isset($post_arr['strid'])) {
            $entry->strid = substr(h(trim(strip_tags(
                                   $post_arr['strid']))), 0, 255);
            if ($entry->strid !== '') {
                $if_exists = $this->fetch_page($entry->strid);
                if ($if_exists) {
                    $entry->page_id = (int)$if_exists->id;
                } else {
                    $entry->page_id = 0;
                }
            }
        } else {
            $entry->page_id = 0;
        }
        
        $entry->title = (isset($post_arr['title']) && !empty($post_arr['title'])) ?
            input2db($post_arr['title'], 255) : '--';

        // citi parametri, kas nav obligāti norādāmi
        $entry->starting_point = (isset($post_arr['starting_point'])) ? 
            input2db($post_arr['starting_point'], 256) : '';
        $entry->extra = (isset($post_arr['extra'])) ? 
            input2db($post_arr['extra'], 1024) : '';
        $entry->description = (isset($post_arr['description'])) ? 
            input2db($post_arr['description'], 1024) : '';
        $entry->members_only = (isset($post_arr['members_only'])) ? 
            (bool)$post_arr['members_only'] : false;
        $entry->safe = (isset($post_arr['safe'])) ? 
            (bool)$post_arr['safe'] : false;

        $values = [
            'page_id'           => $entry->page_id,
            'title'             => $entry->title,
            'members_only'      => (int)$entry->members_only,
            'safe'              => (int)$entry->safe,
            'starting_point'    => $entry->starting_point,
            'extra'             => $entry->extra,
            'description'       => $entry->description,
            'updated_by'        => (int)$this->auth->id,
            'updated_at'        => time()
        ];
        $params = [
            'id' => (int)$entry_id,
            'deleted_by' => 0
        ];
        $this->db->update('rs_pages', $params, $values);
    }
}
