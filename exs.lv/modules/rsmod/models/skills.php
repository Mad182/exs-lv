<?php
/**
 *  
 */

class Model_Skills extends Model {

    private $cat_quests;

    function __construct() {
        global $cat_quests;
        $this->cat_quests = $cat_quests;
        parent::__construct();
    }    
    
    /**
     *  Atlasa informāciju par RuneScape kvestiem
     */
    public function fetch_skills() {
    
        $query = $this->db->get_results("
            SELECT * FROM `rs_skills` ORDER BY `title` ASC
        ");
        
        return $query;
    }    
    
    /**
     *  Atlasa informāciju par rakstu ar norādīto adreses nosaukumu
     */
    public function fetch_page($strid = '') {
    
        $strid = trim($strid);
        if ($strid === '') return false;
    
        $query = $this->db->get_row("
            SELECT `id`, `strid` FROM `pages` 
            WHERE
                `strid` = '".sanitize($strid)."' AND
                `lang` = 9 AND
                `category` IN(".implode(', ', $this->cat_quests).")
        ");

        return $query;
    }
}
