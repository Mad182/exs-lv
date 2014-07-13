<?php
/**
 *  Darbības, kas saistītas ar RuneScape kvestu pamācību sadaļām
 */

class Model_Quests extends Model {
    
    public function __construct() { 
        $globals = array(
            'cat_quests', 
            'cats_quests', 
            'cat_p2p_quests', 
            'cat_f2p_quests', 
            'cat_miniquests'
        );
        $this->globals($globals);
        parent::__construct();
    }
    
    /**
     *  Atlasa izveidotās kvestu sērijas, katrai no tām piesaista kvestus
     *  no `rs_pages` tabulas (tai skaitā minikvestus un placeholders),
     *  savukārt tiem piesaista rakstus no `pages` (ja tādi ir).
     */
    public function fetch_series() {
        
        $query = $this->db->get_results("
            SELECT
                IFNULL(`pages`.`id`, 0) AS `pages_id`,
                `pages`.`category`      AS `category`,
                `pages`.`title`         AS `pages_title`,
                `pages`.`strid`         AS `strid`,
                
                `rs_pages`.`id`         AS `rspages_id`,
                `rs_pages`.`title`      AS `title`,
                
                `rs_series`.`id`        AS `series_id`,
                `rs_series`.`title`     AS `series_title`,
                `rs_series`.`img`       AS `img`

            FROM `rs_series_quests`
                JOIN `rs_series` ON (
                    `rs_series_quests`.`series_id`  = `rs_series`.`id` AND
                    `rs_series`.`category`          = 'series'
                )
                JOIN `rs_pages` ON (
                    `rs_series_quests`.`rspages_id`  = `rs_pages`.`id` AND
                    `rs_pages`.`deleted_by`          = 0 AND
                    `rs_pages`.`is_hidden`           = 0 AND
                    `rs_pages`.`cat_id` IN(".implode(',', $this->cat_quests).")
                )
                LEFT JOIN `pages` ON (
                    `pages`.`id`    = `rs_pages`.`page_id` AND
                    `pages`.`lang`  = ".(int)$this->lang." AND
                    `pages`.`category` IN(".implode(',', $this->cat_quests).")
                )            
            WHERE
                `rs_series_quests`.`deleted_by` = 0
            ORDER BY            
                ABS(`rs_series`.`ordered_by`) ASC,
                ABS(`rs_series`.`id`) ASC,
                `rs_series_quests`.`ordered_by` ASC,
                `rs_pages`.`title` ASC
        ");
        
        return $query;
    }
    
    /**
     *  Atgriež datus par F2P/minikvestiem
     */
    public function fetch_common_quests($category_id = 0) {
    
        $category_id = (int)$category_id;
        if ($category_id < 1) return false;
    
        $query = $this->db->get_results("
            SELECT              
                `rs_pages`.`title`,
                `rs_pages`.`image`,
                `rs_pages`.`description`,

                IFNULL(`pages`.`id`, 0) AS `page_id`,
                `pages`.`strid`,
                `pages`.`date`,
                `pages`.`author`

            FROM `rs_pages` 
                LEFT JOIN `pages` ON (
                    `rs_pages`.`page_id` = `pages`.`id` AND
                    `pages`.`category` = $category_id
                )
            WHERE 
                `rs_pages`.`is_hidden` = 0 AND
                `rs_pages`.`deleted_by` = 0 AND
                `rs_pages`.`cat_id` = $category_id
            ORDER BY 
                `rs_pages`.`title` ASC
        ");
        
        return $query;
    }
    
    /**
     *  Atgriež datus par P2P kvestiem
     */
    public function fetch_p2p_quests() {
        
        $query = $this->db->get_results("
            SELECT
                `rs_pages`.`title`,

                IFNULL(`pages`.`id`,0 ) AS `page_id`,
                `pages`.`strid`,
                `pages`.`author`

            FROM `rs_pages`
                LEFT JOIN `pages` ON (
                    `rs_pages`.`page_id` = `pages`.`id` AND
                    `pages`.`category` = ".(int)$this->cat_p2p_quests."
                )
            WHERE
                `rs_pages`.`is_hidden` = 0 AND
                `rs_pages`.`deleted_by` = 0 AND
                `rs_pages`.`cat_id` = ".(int)$this->cat_p2p_quests."
            ORDER BY `rs_pages`.`title` ASC 
        ");
        
        return $query;
    }
    
    /**
     *  Atgriež informāciju par prasmēm
     */
    public function fetch_skills() {
        
        $query = $this->db->get_results("
            SELECT 
                `rs_skills`.*,

                IFNULL(`pages`.`id`, 0) AS `pages_id`,
                `pages`.`title` AS `pages_title`,
                `pages`.`strid`

            FROM `rs_skills`
                LEFT JOIN `pages` ON (
                    `rs_skills`.`page_id` = `pages`.`id`
                )
            ORDER BY `title` ASC
        ");
        
        return $query;
    }
    
    /**
     *  Ar Memcache saglabā un atgriež kvestu statistikas datus
     */
    public function fetch_stats($force = false) {

        $stats = false;

        if ($force || ($stats = $this->m->get('quests-stats')) === false) {

            // izlaisto kvestu skaits noteiktos gados
            $stats['14'] = $this->db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `is_hidden` = 0 AND `year` = 14 AND `cat_id` IN (".implode(',', $this->cats_quests).") ");
            $stats['13'] = $this->db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `is_hidden` = 0 AND `year` = 13 AND `cat_id` IN (".implode(',', $this->cats_quests).") ");
            $stats['12'] = $this->db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `is_hidden` = 0 AND `year` = 12 AND `cat_id` IN (".implode(',', $this->cats_quests).") ");
            $stats['11'] = $this->db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `is_hidden` = 0 AND `year` = 11 AND `cat_id` IN (".implode(',', $this->cats_quests).") ");
            $stats['10'] = $this->db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `is_hidden` = 0 AND `year` = 10 AND `cat_id` IN (".implode(',', $this->cats_quests).") ");
            $stats['older'] = $this->db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `is_hidden` = 0 AND `year` NOT IN (14, 13, 12, 11, 10) AND `cat_id` IN (".implode(',', $this->cats_quests).") ");

            // kvestu tips
            $stats['p2p'] = $this->db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `is_hidden` = 0 AND `members_only` = 1 AND `cat_id` = ".(int)$this->cat_p2p_quests);
            $stats['f2p'] = $this->db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `is_hidden` = 0 AND `members_only` = 0 AND `cat_id` = ".(int)$this->cat_f2p_quests);
            $stats['miniquests'] = $this->db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `is_hidden` = 0 AND `cat_id` = ".(int)$this->cat_miniquests);

            // kvestu sarežģītība
            $stats['special'] = $this->db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `is_hidden` = 0 AND `difficulty` = 6 AND `cat_id` IN (".implode(',', $this->cats_quests).") ");
            $stats['grandmaster'] = $this->db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `is_hidden` = 0 AND `difficulty` = 5 AND `cat_id` IN (".implode(',', $this->cats_quests).") ");
            $stats['master'] = $this->db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `is_hidden` = 0 AND `difficulty` = 4 AND `cat_id` IN (".implode(',', $this->cats_quests).") ");
            $stats['experienced'] = $this->db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `is_hidden` = 0 AND `difficulty` = 3 AND `cat_id` IN (".implode(',', $this->cats_quests).") ");
            $stats['intermediate'] = $this->db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `is_hidden` = 0 AND `difficulty` = 2 AND `cat_id` IN (".implode(',', $this->cats_quests).") ");
            $stats['novice'] = $this->db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `is_hidden` = 0 AND `difficulty` = 1 AND `cat_id` IN (".implode(',', $this->cats_quests).") ");

            $this->m->set('quests-stats', $stats, false, 1800);
        }

        return $stats;
    }
}
