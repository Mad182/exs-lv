<?php
/**
 *  Darbības, kas saistītas ar RuneScape kvestu pamācību sadaļām
 */

class Model_Quests extends Model {

    private $cat_quests;
    
    public function __construct() {
        global $cat_quests;
        $this->cat_quests =& $cat_quests;        
        parent::__construct();
    }
    
    /**
     *  Atgriež kvestu sērijas un tām piesaistītus kvestus
     */
    public function fetch_series() {
        
        $query = $this->db->get_results("
            SELECT
                IFNULL(`pages`.`id`, 0) AS `pages_id`,
                `pages`.`category`      AS `pages_category`,
                `pages`.`title`         AS `pages_title`,
                `pages`.`strid`         AS `pages_strid`,
                
                `rs_pages`.`id`         AS `rspages_id`,
                `rs_pages`.`title`      AS `title`,
                
                `rs_series`.`id`        AS `series_id`,
                `rs_series`.`title`     AS `series_title`,
                `rs_series`.`img`       AS `series_img`

            FROM `rs_series_quests`
                JOIN `rs_series` ON (
                    `rs_series_quests`.`series_id`  = `rs_series`.`id` AND
                    `rs_series`.`category`          = 'series'
                )
                JOIN `rs_pages` ON (
                    `rs_series_quests`.`rspages_id`  = `rs_pages`.`id` AND
                    `rs_pages`.`deleted_by`          = 0 AND
                    `rs_pages`.`is_hidden`           = 0 AND
                    `rs_pages`.`cat_id` IN(".implode(', ', $this->cat_quests).")
                )
                LEFT JOIN `pages` ON (
                    `pages`.`id`    = `rs_pages`.`page_id` AND
                    `pages`.`lang`  = ".(int)$this->lang." AND
                    `pages`.`category` IN(".implode(', ', $this->cat_quests).")
                )            
            WHERE
                `rs_series_quests`.`deleted_by` = 0
            ORDER BY            
                ABS(`rs_series`.`ordered_by`) ASC,
                `rs_series_quests`.`ordered_by` ASC,
                `rs_pages`.`title` ASC
        ");
        
        return $query;
    }
    
    /**
     *  Atgriež datus par F2P/minikvestiem
     */
    public function fetch_simple_quests($category_id = 0) {
    
        $category_id = (int)$category_id;
        if ($category_id < 1) return false;
    
        $query = $this->db->get_results("
            SELECT 
                `pages`.`id`            AS `page_id`,
                `pages`.`strid`         AS `page_strid`,
                `pages`.`title`         AS `page_title`,
                `pages`.`date`          AS `page_date`,
                `pages`.`author`        AS `page_author`,
                `pages`.`category`      AS `page_catid`,
                
                IFNULL(`rs_pages`.`is_old`, 0) AS `rspage_old`,
                `rs_pages`.`page_id`        AS `rspage_pageid`,			
                `rs_pages`.`img`            AS `rspage_img`,
                `rs_pages`.`description`    AS `rspage_description`

            FROM `rs_pages` 
                LEFT JOIN `pages` ON (
                    `pages`.`id`                = `rs_pages`.`page_id` AND
                    `rs_pages`.`deleted_by`     = 0     AND
                    `rs_pages`.`is_placeholder` = 0
                )
            WHERE 
                `pages`.`category` = $category_id 
            ORDER BY 
                `pages`.`title` ASC
        ");
        
        return $query;
    }
    
    /**
     *  Atgriež datus par P2P kvestiem
     */
    public function fetch_p2p_quests() {
        
        $query = $this->db->get_results("
            SELECT 
                `pages`.`id`            AS `page_id`,
                `pages`.`strid`         AS `page_strid`,
                `pages`.`title`         AS `page_title`,
                `pages`.`author`        AS `page_author`,
                IFNULL(`rs_pages`.`is_old`, 0) AS `rspages_old`
            FROM `pages`
                LEFT JOIN `rs_pages` ON (
                    `pages`.`id`                = `rs_pages`.`page_id` AND
                    `rs_pages`.`deleted_by`     = 0 AND
                    `rs_pages`.`is_placeholder` = 0
                )
            WHERE 
                `pages`.`category` = '100'
            ORDER BY `pages`.`title` ASC 
        ");
        
        return $query;
    }
    
    /**
     *  Atgriež informāciju par prasmēm
     */
    public function fetch_skills() {
        
        $query = $this->db->get_results("
            SELECT * FROM `rs_skills` ORDER BY `skill` ASC
        ");
        
        return $query;
    }
}
