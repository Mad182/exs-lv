<?php
/**
 *  Darbības ar RuneScape kvestu sērijām
 */

class Model_Series extends Model {

    private $cat_quests;

    function __construct() {
        global $cat_quests;
        $this->cat_quests = $cat_quests;
        parent::__construct();
    }    
    
    /**
     *  Atlasa visas kvestu sērijas
     */
    public function fetch_series() {
        
        $query = $this->db->get_results("
            SELECT `id`, `title`, `ordered_by` FROM `rs_series`
            WHERE `category` = 'series'
            ORDER BY `ordered_by` ASC 
        ");
        
        return $query;
    }    
    
    /**
     *  Atgriež kvestu sēriju skaitu
     */
    public function count_series() {
    
        $query = $this->db->get_var("
            SELECT count(*) FROM `rs_series` WHERE `category` = 'series'
        ");
        
        return $query;
    }    

    /**
     *  Atlasa visus kvestus
     */
    public function fetch_quests($series_id = 0) {
        
        $series_id = (int)$series_id;

        $query = $this->db->get_results("
            SELECT 
                `rs_pages`.`id`,
                `rs_pages`.`title`,
                IFNULL(`pages`.`id`, 0) AS `pages_id`,
                `pages`.`strid` AS `pages_strid`,
                IFNULL(`rs_series_quests`.`id`, 0) AS `quests_id`
            FROM `rs_pages` 
                LEFT JOIN `pages` ON (
                    `rs_pages`.`page_id` = `pages`.`id` AND
                    `pages`.`category` IN(".implode(', ', $this->cat_quests).")
                )
                LEFT JOIN `rs_series_quests` ON (
                    `rs_series_quests`.`rspages_id` = `rs_pages`.`id` AND
                    `rs_series_quests`.`series_id` = $series_id AND
                    `rs_series_quests`.`deleted_by` = 0
                )
            WHERE
                `rs_pages`.`deleted_by` = 0 AND
                `rs_pages`.`cat_id` IN(".implode(', ', $this->cat_quests).")
            ORDER BY `rs_pages`.`title` ASC
        ");
        
        return $query;
    }    
    
    /**
     *  Atlasa norādītajā sērijā ietilpstošos kvestus
     */
    public function fetch_series_quests($series_id = 0) {
    
        $series_id = (int)$series_id;
        if ($series_id === 0) return false;
    
        $query = $this->db->get_results("
            SELECT
                `rs_series_quests`.`id`,
                `rs_series_quests`.`ordered_by`,
                `rs_pages`.`title`,
                IFNULL(`pages`.`strid`, 0) AS `strid`
            FROM `rs_series_quests`
                JOIN `rs_pages` ON (
                    `rs_series_quests`.`rspages_id` = `rs_pages`.`id` AND
                    `rs_pages`.`deleted_by` = 0
                )
                LEFT JOIN `pages` ON (
                    `rs_pages`.`page_id` = `pages`.`id` AND
                    `pages`.`category` IN(".implode(',', $this->cat_quests).")
                )
            WHERE
                `rs_series_quests`.`deleted_by` = 0 AND
                `rs_series_quests`.`series_id` = ".$series_id."
            ORDER BY 
                `rs_series_quests`.`ordered_by` ASC,
                `rs_pages`.`title` ASC
        ");
        
        return $query;
    }    
    
    /**
     *  Atgriež datus par norādīto sēriju
     */
    public function fetch_single_series($series_id = 0) {
    
        $series_id = (int)$series_id;
        if ($series_id === 0) return false;
        
        $query = $this->db->get_row("
            SELECT * FROM `rs_series` 
            WHERE 
                `id` = ".$series_id." AND
                `category` = 'series'
            LIMIT 0,1
        ");
        
        return $query;
    }    
    
    /**
     *  Veic pārbaudi, vai eksistē norādītā sērija
     */
    public function check_series($series_id = 0) {
    
        $series_id = (int)$series_id;
        if ($series_id < 1) return false;
        
        $query = $this->db->get_var("
            SELECT count(*) FROM `rs_series` 
            WHERE 
                `id` = ".$series_id." AND
                `category` = 'series'
        ");
        
        return ($query == 1);
    }    
    
    /**
     *  Veic pārbaudi, vai norādītais kvests eksistē
     */
    public function check_quest($quest_id = 0) {
    
        $quest_id = (int)$quest_id;
        if ($quest_id < 1) return false;
    
        $query = $this->db->get_var("
            SELECT count(*) FROM `rs_pages`
            WHERE 
                `id` = ".$quest_id." AND
                `deleted_by`  = 0 AND
                `cat_id` IN(".implode(',', $this->cat_quests).")
        ");
        
        return ($query == 1);
    }

    /**
     *  Atgriež norādītās sērijas un kvesta `rs_series_quests` ierakstu
     */
    public function get_series_quest($series_id = 0, $quest_id = 0) {
        
        $series_id = (int)$series_id;
        if ($series_id < 1) return false;
        
        $quest_id = (int)$quest_id;
        if ($quest_id < 1) return false;
    
        $query = $this->db->get_row("
            SELECT `id` FROM `rs_series_quests`
            WHERE
                `series_id` = ".$series_id." AND
                `rspages_id` = ".$quest_id."
        ");
        
        return $query;
    }    
    
    /**
     *  Dzēš kvestu no norādītās sērijas
     */
    public function remove_series_quest($row_id = 0) {
    
        $row_id = (int)$row_id;
        if ($row_id < 1) return false;

        $fields = [
            'deleted_by' => (int)$this->auth->id,
            'deleted_at' => time()
        ];
        $this->db->update('rs_series_quests', $row_id, $fields);
        
        return true;
    }    
    
    /**
     *  Piesaista sērijai kvestu
     */
    public function set_series_quest($series_id = 0, $quest_id = 0, 
                                     $row_id = 0) {
    
        $series_id = (int)$series_id;
        if ($series_id < 1) return false;
        
        $quest_id = (int)$quest_id;
        if ($quest_id < 1) return false;
    
        $row_id = (int)$row_id;
        
        // ja rinda ir norādīta, ieraksts jau iepriekš tabulā ierakstīts,
        // tikai ticis dzēsts, tāpēc jāatjauno
        $query = true;
        if ($row_id > 0) {
        
            $fields = [
                'deleted_by' => 0,
                'updated_by' => (int)$this->auth->id,
                'updated_at' => time()
            ];
            $this->db->update('rs_series_quests', $row_id, $fields);

        // ieraksta tabulā vēl nav
        } else {
            $query = $this->db->query("
                INSERT INTO `rs_series_quests`
                    (series_id, rspages_id, created_by, created_at)
                VALUES(
                    ".$series_id.",
                    ".$quest_id.",
                    ".(int)$this->auth->id.",
                    '".time()."'
                )
            ");
        }
    
        return $query;
    }
}
