<?php
/**
 *
 */

class Model_Other extends Model {

    private $cat_guilds;
    
    public function __construct() {
        global $cat_guilds;

        $this->cat_guilds =& $cat_guilds;
        
        parent::__construct();
    }
    
    /**
     *  Atgriež datus par ģildēm
     */
    public function fetch_guilds() {
    
        $query = $this->db->get_results("
            SELECT 
                `pages`.`id`                AS `page_id`,
                `pages`.`strid`             AS `page_strid`,
                `pages`.`title`             AS `page_title`,
                `pages`.`author`            AS `page_author`,
                `pages`.`category`          AS `page_category`,
                
                IFNULL(`rs_pages`.`id`,0)   AS `rspage_id`,
                `rs_pages`.`img`            AS `rspage_img`,
                `rs_pages`.`members_only`   AS `rspage_members_only`,
                `rs_pages`.`location`       AS `rspage_location`,
                `rs_pages`.`extra`          AS `rspage_extra`,
                `rs_pages`.`is_old`         AS `rspage_is_old`
            FROM `pages` 
                LEFT JOIN `rs_pages` ON (
                    `pages`.`id`                = `rs_pages`.`page_id` AND
                    `rs_pages`.`deleted_by`     = 0 AND
                    `rs_pages`.`is_placeholder` = 0
                )
            WHERE 
                `pages`.`category` = ".(int)$this->cat_guilds." 
            ORDER BY 
                `pages`.`title` ASC
        ");
        
        return $query;
    }
    
    /**
     *  Atgriež datus par Tasks
     *
     *  @param bool $categorised    vai atlasīt kategorizētos Tasks?
     */
    public function fetch_tasks($categorised = true) {
    
        $categorised = (bool)$categorised;
    
        $query = $this->db->get_results("
            SELECT 
                `rs_classes`.`id`       AS `class_id`, 
                `rs_classes`.`title`    AS `class_title`, 
                `rs_classes`.`img`      AS `class_img`,
                IFNULL(`pages`.`id`, 0) AS `page_id`, 
                `pages`.`strid`         AS `page_strid`,
                `pages`.`title`         AS `page_title`
            FROM `rs_classes`
                LEFT JOIN `rs_pages` ON (
                    `rs_classes`.`id` = `rs_pages`.`class_id` AND
                    `rs_pages`.`is_placeholder` = 0  AND
                    `rs_pages`.`deleted_by` = 0
                )
                LEFT JOIN `pages` ON `rs_pages`.`page_id` = `pages`.`id`
            WHERE 
                `rs_classes`.`category` = 'tasks' AND
                `rs_classes`.`id` != 112
            ORDER BY 
                `rs_classes`.`ordered` ASC
        ");

        return $query;
    }
    
    /**
     *
     */
    public function fetch_skill_pages() {
    
        $query = $this->db->get_results("
            SELECT 
                `cat`.`id`              AS `cat_id`,
                `cat`.`title`           AS `cat_title`,
                
                IFNULL(`pages`.`id`, 0) AS `page_id`,
                `pages`.`title`         AS `page_title`,
                `pages`.`strid`         AS `page_strid`,
                
                IFNULL(`rs_classes`.`id`, 0)    AS `class_id`,
                `rs_classes`.`img`              AS `class_img`,
                `rs_classes`.`info`             AS `class_info`,
                `rs_classes`.`members_only`     AS `members_only`
            FROM `cat` 
                LEFT JOIN `pages` ON `cat`.`id` = `pages`.`category`
                LEFT JOIN `rs_classes` ON (
                    `cat`.`title`           = `rs_classes`.`title` AND
                    `rs_classes`.`category` = 'skills'
                )
            WHERE 
                `cat`.`parent` = 4  
            ORDER BY 
                `cat`.`title` ASC
        ");
        
        return $query;
    }
    
    /**
     *  Atgriež datus par minispēlēm/distractions & diversions
     */
    public function fetch_minigames($cat_id = 0) {
    
        $cat_id = (int)$cat_id;
        if ($cat_id < 1) return false;
    
        $query = $this->db->get_results("
            SELECT 
                `pages`.`id`,
                `pages`.`strid`     AS `page_strid`, 
                `pages`.`title`     AS `page_title`,
                `pages`.`author`    AS `page_author`, 
                `pages`.`date`      AS `page_date`,
                `pages`.`avatar`,
                IFNULL(`rs_pages`.`id`, 0)      AS `rspage_id`,
                `rs_pages`.`description`        AS `rspage_description`,
                `rs_pages`.`location`           AS `rspage_location`,
                `rs_pages`.`members_only`       AS `members_only`
            FROM `pages`
                LEFT JOIN `rs_pages` ON (
                    `pages`.`id`                = `rs_pages`.`page_id` AND
                    `rs_pages`.`deleted_by`     = 0 AND
                    `rs_pages`.`is_placeholder` = 0
                )
            WHERE 
                `pages`.`category` = $cat_id
            ORDER BY 
                `pages`.`title` ASC
        ");
        
        return $query;
    }
}
