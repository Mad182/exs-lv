<?php
/**
 *  Darbības ar datiem, kas saistīti ar pamācību sarakstiem
 */

class Model_Lists extends Model {
    
    // lokālas references globālajiem mainīgajiem
    private $cat_quests;
    private $cats_quests;
    private $cat_miniquests;
    private $cat_p2p_quests;
    private $cat_f2p_quests;

    public function __construct() {
        global $cat_quests, $cats_quests, $cat_miniquests;
        global $cat_p2p_quests, $cat_f2p_quests;

        $this->cat_quests     =& $cat_quests;
        $this->cats_quests    =& $cats_quests;
        $this->cat_miniquests =& $cat_miniquests;
        $this->cat_p2p_quests =& $cat_p2p_quests;
        $this->cat_f2p_quests =& $cat_f2p_quests;

        parent::__construct();
    }

    /**
     *  Atgriež atvērtās cilnes nosaukumu
     */
    private function get_opened_tab() {    
        $response = 'quests';

        $arr_links = array('all-miniquests', 'all-minigames', 
                           'all-distractions', 'all-guilds');

        if (in_array($_GET['viewcat'], $arr_links)) {
            $response = str_replace('all-', '', mkslug($_GET['viewcat']));
        }
        
        return $response;
    }
    
    /**
     *  Atgriež virkni ar kategoriju id, kādās jāmeklē db ieraksti 
     */
    private function get_categories_list() {
        global $cat_miniquests, $cat_minigames;
        global $cat_distractions, $cat_guilds;
        
        $response = '`pages`.`category` IN('.implode(', ', $this->cats_quests).')'.
                    ' OR `rs_pages`.`cat_id` IN('.implode(', ', $this->cats_quests).')';
        
        $tab = $this->get_opened_tab();

        if ($tab == 'miniquests') {
            $response = '`pages`.`category` = '.(int)$cat_miniquests.
                ' OR `rs_pages`.`cat_id` = '.(int)$cat_miniquests;
        } else if ($tab == 'minigames') {
            $response = '`pages`.`category` = '.(int)$cat_minigames.
                ' OR `rs_pages`.`cat_id` = '.(int)$cat_minigames;
        } else if ($tab == 'distractions') {
            $response = '`pages`.`category` = '.(int)$cat_distractions.
                ' OR `rs_pages`.`cat_id` = '.(int)$cat_distractions;
        } else if ($tab == 'guilds') {
            $response = '`pages`.`category` = '.(int)$cat_guilds.
                ' OR `rs_pages`.`cat_id` = '.(int)$cat_guilds;
        }
        
        return $response;
    }

    /**
     *  Atgriež kategorijas ierakstus no `rs_pages` tabulas
     */
    public function fetch_pages() {
    
        $query = $this->db->get_results("
            SELECT
                `rs_pages`.`id`         AS `rspage_id`,
                `rs_pages`.`is_hidden`,
                `rs_pages`.`title`      AS `rspage_title`,

                `rs_series`.`id`        AS `rsseries_id`,
                `rs_series`.`title`     AS `rsseries_title`,
                
                IFNULL(`pages`.`id`, 0) AS `page_id`,
                `pages`.`strid`         AS `page_strid`,
                `pages`.`title`         AS `page_title`
                
            FROM `rs_pages`
                LEFT JOIN `rs_series_quests` ON (
                    `rs_pages`.`id` = `rs_series_quests`.`rspages_id` AND 
                    `rs_series_quests`.`deleted_by` = 0
                )
                LEFT JOIN `rs_series` ON 
                    `rs_series_quests`.`series_id` = `rs_series`.`id`
                LEFT JOIN `pages` ON 
                    `rs_pages`.`page_id` = `pages`.`id`
            WHERE
                `rs_pages`.`deleted_by` = 0 AND
                (" . $this->get_categories_list() . ")
            ORDER BY `rs_pages`.`title` ASC
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
            SELECT * FROM `rs_pages` 
            WHERE 
                `id` = $entry_id AND 
                `deleted_by` = 0
        ");
        
        return $query;
    }
    
    /**
     *  Dzēš ierakstu `rs_pages` tabulā
     */
    public function delete_entry($entry_id = 0) {
    
        $entry_id = (int)$entry_id;
        if ($entry_id < 1) return false;
        
        $values = array(
            'deleted_by' => (int)$this->auth->id,
            'deleted_at' => time()
        );
        
        $this->db->update('rs_pages', $entry_id, $values);
        
        return true;
    }
    
    /**
     *  "Paslēpj/parāda" `rs_pages` ierakstu
     */
    public function toggle_entry($entry_id = 0, $swap_to = 0) {

        $entry_id = (int)$entry_id;
        if ($entry_id < 1) return false;
        
        $swap_to = ($swap_to === 1) ? 1 : 0;
        
        $values = array(
            'updated_by' => (int)$this->auth->id,
            'updated_at' => time(),
            'is_hidden' => $swap_to
        );
        $params = array(
            'id' => $entry_id,
            'deleted_by' => 0
        );
        
        $this->db->update('rs_pages', $params, $values);

        return true;
    }
}
