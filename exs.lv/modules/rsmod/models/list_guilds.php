<?php
/**
 *  Darbības ar datiem, kas saistīti ar jaunu ģilžu pievienošanu vai
 *  esošu ierakstu rediģēšanu
 */

class Model_List_Guilds extends Model {

    // lokālas references globālajiem mainīgajiem
    private $cat_guilds;
    
    public function __construct() {
        global $cat_guilds;

        $this->cat_guilds =& $cat_guilds;

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
                `category` = ".(int)$this->cat_guilds."
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
                    `pages`.`category` = ".(int)$this->cat_guilds."
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
            $strid = substr(htmlspecialchars(trim(strip_tags(
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
        $members_only = (isset($post_arr['members_only'])) ? 
            (int)((bool)$post_arr['members_only']) : 0;
        $starting_point = (isset($post_arr['starting_point'])) ? 
            input2db($post_arr['starting_point'], 100) : '';
        $extra = (isset($post_arr['extra'])) ? 
            input2db($post_arr['extra'], 1024) : '';

        $values = array(
            'page_id'           => $page_id,
            'cat_id'            => (int)$this->cat_guilds,
            'title'             => $title,
            'members_only'      => $members_only,
            'starting_point'    => $starting_point,
            'extra'             => $extra,
            'created_by'        => (int)$this->auth->id,
            'created_at'        => time()
        );
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
            $entry->strid = substr(htmlspecialchars(trim(strip_tags(
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
        $entry->members_only = (isset($post_arr['members_only'])) ? 
            (bool)$post_arr['members_only'] : false;
        $entry->starting_point = (isset($post_arr['starting_point'])) ? 
            input2db($post_arr['starting_point'], 256) : '';
        $entry->extra = (isset($post_arr['extra'])) ? 
            input2db($post_arr['extra'], 1024) : '';

        $values = array(
            'page_id'           => $entry->page_id,
            'title'             => $entry->title,
            'members_only'      => (int)$entry->members_only,
            'starting_point'    => $entry->starting_point,
            'extra'             => $entry->extra,
            'updated_by'        => (int)$this->auth->id,
            'updated_at'        => time()
        );
        $params = array(
            'id'            => (int)$entry->id,
            'deleted_by'    => 0
        );
        
        $this->db->update('rs_pages', $params, $values);
    }
}
