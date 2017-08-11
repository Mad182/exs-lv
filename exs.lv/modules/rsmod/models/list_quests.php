<?php
/**
 *  Darbības ar datiem, kas saistīti ar jaunu kvestu pievienošanu vai
 *  esošu ierakstu rediģēšanu
 */

class Model_List_Quests extends Model {

    // lokālas references globālajiem mainīgajiem
    private $cat_quests;
    private $cat_miniquests;
    private $cat_p2p_quests;
    private $cat_f2p_quests;
    
    // kvestu sarežģītība
    private $arr_levels = [
        1 => 'Novice', 
        2 => 'Intermediate', 
        3 => 'Experienced', 
        4 => 'Master', 
        5 => 'Grandmaster', 
        6 => 'Special'
    ];
    
    // kvestu ilgums
    private $arr_length = [
        1 => 'Īss', 
        2 => 'Vidējs', 
        3 => 'Ilgs', 
        4 => 'Ļoti ilgs', 
        5 => 'Ļoti, ļoti ilgs'
    ];
    
    public function __construct() {
        global $cat_quests, $cat_miniquests;
        global $cat_p2p_quests, $cat_f2p_quests;

        $this->cat_quests     =& $cat_quests;
        $this->cat_miniquests =& $cat_miniquests;
        $this->cat_p2p_quests =& $cat_p2p_quests;
        $this->cat_f2p_quests =& $cat_f2p_quests;

        parent::__construct();
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
                    `pages`.`category` IN(".implode(', ', $this->cat_quests).")
                )
            WHERE 
                `rs_pages`.`id` = $entry_id AND
                `rs_pages`.`deleted_by` = 0
        ");
        
        return $query;
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
                `category` IN(".implode(', ', $this->cat_quests).")
        ");
        
        return $query;
    }
    
    /**
     *  Pievieno jaunu kvesta ierakstu
     */
    public function post_new_quest($post_arr = null) {
    
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
        $skills = (isset($post_arr['skills'])) ? 
            input2db($post_arr['skills'], 512) : '';
        $quests = (isset($post_arr['quests'])) ? 
            input2db($post_arr['quests'], 1024) : '';
        $extra = (isset($post_arr['extra'])) ? 
            input2db($post_arr['extra'], 1024) : '';
        $description = (isset($post_arr['description'])) ? 
            input2db($post_arr['description'], 1024) : '';                
        $members_only = (isset($post_arr['members_only'])) ? 
            (int)((bool)$post_arr['members_only']) : 0;

        $date = '01/01/2001';
        if (isset($post_arr['date']) && !empty($post_arr['date'])) {
            $date = str_replace('/', '-', trim($post_arr['date']));
            $date = date('d/m/Y', strtotime($date));
        }

        $cat = 0;
        if (isset($_GET['viewcat']) && $_GET['viewcat'] === 'all-miniquests') {
            $cat = $this->cat_miniquests;
        } else {
            $cat = ($members_only) ? 
                $this->cat_p2p_quests : $this->cat_f2p_quests;
        }
            
        $difficulty = 0;
        if (isset($post_arr['difficulty']) && 
            array_key_exists((int)$post_arr['difficulty'], $this->arr_levels)) {            
            $difficulty = (int)$post_arr['difficulty'];
        }
        
        $length = 0;
        if (isset($post_arr['length']) && 
            array_key_exists((int)$post_arr['length'], $this->arr_length)) {            
            $length = (int)$post_arr['length'];
        }

        $age = 0;
        if (isset($post_arr['age']) && (int)$post_arr['age'] === 1) {
            $age = 1;
        }
        
        $voice_acted = 0;
        if (isset($post_arr['voice_acted']) && 
            (int)$post_arr['voice_acted'] === 1) {
            $voice_acted = 1;
        }

        $values = [
            'page_id'           => $page_id,
            'cat_id'            => (int)$cat,
            'title'             => $title,
            'members_only'      => $members_only,
            'difficulty'        => $difficulty,
            'length'            => $length,
            'age'               => $age,
            'voice_acted'       => $voice_acted,
            'starting_point'    => $starting_point,
            'skills'            => $skills,
            'quests'            => $quests,
            'extra'             => $extra,
            'description'       => $description,
            'date'              => sanitize($date),
            'year'              => (int)substr($date, -2),
            'created_by'        => (int)$this->auth->id,
            'created_at'        => time()
        ];

        $this->db->insert('rs_pages', $values);
    }
    
    /**
     *  Rediģē esošu kvesta ierakstu
     */
    public function update_quest($entry_id = 0, $post_arr = null) {
    
        if (empty($post_arr)) return false;
        
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
        $entry->starting_point = (isset($post_arr['starting_point'])) ? 
            input2db($post_arr['starting_point'], 256) : '';
        $entry->skills = (isset($post_arr['skills'])) ? 
            input2db($post_arr['skills'], 512) : '';
        $entry->quests = (isset($post_arr['quests'])) ? 
            input2db($post_arr['quests'], 1024) : '';
        $entry->extra = (isset($post_arr['extra'])) ? 
            input2db($post_arr['extra'], 1024) : '';
        $entry->description = (isset($post_arr['description'])) ? 
            input2db($post_arr['description'], 1024) : '';
        $entry->members_only = (isset($post_arr['members_only'])) ? 
            (bool)$post_arr['members_only'] : false;

        if (isset($_GET['viewcat']) && $_GET['viewcat'] === 'all-miniquests') {
            $entry->cat_id = $this->cat_miniquests;
        } else {
            $entry->cat_id = ($entry->members_only) ? 
                $this->cat_p2p_quests : $this->cat_f2p_quests;
        }

        $entry->date = '01/01/2001';
        if (isset($post_arr['date']) && !empty($post_arr['date'])) {
            $entry->date = str_replace('/', '-', trim($post_arr['date']));
            $entry->date = date('d/m/Y', strtotime($entry->date));
        }
        
        $entry->difficulty = 0;
        if (isset($post_arr['difficulty']) && 
            array_key_exists((int)$post_arr['difficulty'], $this->arr_levels)) {            
            $entry->difficulty = (int)$post_arr['difficulty'];
        }
        
        $entry->length = 0;
        if (isset($post_arr['length']) && 
            array_key_exists((int)$post_arr['length'], $this->arr_length)) {            
            $entry->length = (int)$post_arr['length'];
        }
        
        $entry->age = 0;
        if (isset($post_arr['age']) && (int)$post_arr['age'] === 1) {
            $entry->age = 1;
        }
        
        $entry->voice_acted = 0;
        if (isset($post_arr['voice_acted']) && 
            (int)$post_arr['voice_acted'] === 1) {
            $entry->voice_acted = 1;
        }
        
        $values = [
            'page_id'           => $entry->page_id,
            'cat_id'            => $entry->cat_id,
            'title'             => $entry->title,
            'members_only'      => (int)$entry->members_only,
            'difficulty'        => $entry->difficulty,
            'length'            => $entry->length,
            'age'               => $entry->age,
            'voice_acted'       => $entry->voice_acted,
            'starting_point'    => $entry->starting_point,
            'skills'            => $entry->skills,
            'quests'            => $entry->quests,
            'extra'             => $entry->extra,
            'description'       => $entry->description,
            'date'              => sanitize($entry->date),
            'year'              => (int)substr($entry->date, -2),
            'updated_by'        => (int)$this->auth->id,
            'updated_at'        => time()
        ];
        $params = [
            'id' => (int)$entry->id,
            'deleted_by' => 0
        ];
        
        $this->db->update('rs_pages', $params, $values);
    }
}
