<?php
/**
 *  Datu apstrāde vairākām RuneScape pamācību sadaļām,
 *  tai skaitā:
 *
 *      - minispēlēm un D&D
 *      - tasks
 *      - ģildēm
 */

class Model_Guides extends Model {

    private $cat_guilds;
    private $cat_achievements;
    
    public function __construct() {
        global $cat_guilds, $cat_achievements;

        $this->cat_guilds =& $cat_guilds;
        $this->cat_achievements =& $cat_achievements;
        
        parent::__construct();
    }
    
    /**
     *  Atgriež datus par ģildēm
     */
    public function fetch_guilds() {
    
        $query = $this->db->get_results("
            SELECT                 
                `rs_pages`.`id`,
                `rs_pages`.`title`,
                `rs_pages`.`image`,
                `rs_pages`.`members_only`,
                `rs_pages`.`starting_point`,
                `rs_pages`.`extra`,
                IFNULL(`pages`.`id`, 0) AS `page_id`,
                `pages`.`strid`
            FROM `rs_pages` 
                LEFT JOIN `pages` ON (
                    `rs_pages`.`page_id` = `pages`.`id` AND
                    `pages`.`category`   = ".(int)$this->cat_guilds."
                )
            WHERE 
                `rs_pages`.`cat_id` = ".(int)$this->cat_guilds." 
            ORDER BY 
                `rs_pages`.`title` ASC
        ");
        
        return $query;
    }
    
    /**
     *  Atgriež datus par Tasks/Achievements
     */
    public function fetch_tasks() {
    
        $query = $this->db->get_results("
            SELECT 
                `rs_series`.`id`, 
                `rs_series`.`title` AS `series_title`, 
                `rs_series`.`img`,
                IFNULL(`pages`.`id`, 0) AS `page_id`, 
                `pages`.`strid`,
                `pages`.`title`
            FROM `rs_series`
                LEFT JOIN `rs_series_quests` ON (
                    `rs_series`.`id` = `rs_series_quests`.`series_id` AND 
                    `rs_series_quests`.`deleted_by` = 0
                )
                LEFT JOIN `rs_pages` ON (
                    `rs_series_quests`.`rspages_id` = `rs_pages`.`id` AND
                    `rs_pages`.`is_hidden` = 0  AND
                    `rs_pages`.`deleted_by` = 0
                )
                LEFT JOIN `pages` ON (
                    `rs_pages`.`page_id` = `pages`.`id` AND
                    `pages`.`category` = $this->cat_achievements
                )
            WHERE 
                `rs_series`.`category` = 'tasks' AND
                `rs_series`.`id` != 112
            ORDER BY 
                `rs_series`.`title` ASC,
                `rs_pages`.`title` ASC
        ");

        return $query;
    }
    
    /**
     *  Atgriež datus par rakstiem, kuri atrodas Tasks sadaļā,
     *  bet nav piesaistīti nevienam reģionam no `rs_series`
     */
    public function fetch_uncategorized_tasks() {
    
        $query = $this->db->get_results("
            SELECT
                `pages`.`id`, 
                `pages`.`strid`,
                `pages`.`title`,
                `rs_pages`.`id` AS `rspage_id`
            FROM `pages`
                LEFT JOIN `rs_pages` ON (
                    `pages`.`id` = `rs_pages`.`page_id` AND
                    `rs_pages`.`is_hidden` = 0  AND
                    `rs_pages`.`deleted_by` = 0 AND
                    `rs_pages`.`cat_id` = $this->cat_achievements
                )
            WHERE 
                `pages`.`category` = $this->cat_achievements AND
                `rs_pages`.`id` IS NULL
            ORDER BY 
                `pages`.`title` ASC
        ");

        return $query;
    }
    
    /**
     *  Atgriež datus par prasmēm un tām piesaistītos rakstus
     */
    public function fetch_skills($page_nr = 0) {

        $query = $this->db->get_results("
            SELECT 
                `cat`.`id`              AS `cat_id`,
                `cat`.`title`           AS `cat_title`,                
                IFNULL(`pages`.`id`, 0) AS `page_id`,
                `pages`.`title`         AS `page_title`,
                `pages`.`strid`         AS `strid`,                
                IFNULL(`rs_series`.`id`, 0)    AS `class_id`,
                `rs_series`.`img`              AS `img`,
                `rs_series`.`info`             AS `info`,
                `rs_series`.`members_only`     AS `members_only`
            FROM `cat` 
                JOIN `rs_series` ON (
                    `cat`.`title` = `rs_series`.`title` AND
                    `rs_series`.`category` = 'skills'
                )
                LEFT JOIN `pages` ON `cat`.`id` = `pages`.`category`
            WHERE 
                `cat`.`parent` = 4  
            ORDER BY 
                `cat`.`title` ASC,
                `pages`.`title` ASC
        ");
        
        return $query;
    }
    
    /**
     *  Atgriež norādītās prasmes rakstus kādā no lappusēm
     */
    public function fetch_skill_pages($max_per_page = 5) {
    
        $start = 0;
        $skill_id = 0;

        $max_per_page = (int)$max_per_page;
        if ($max_per_page < 1) {
            $max_per_page = 5;
        }
        
        if (isset($_GET['skill'])) $skill_id = (int)$_GET['skill'];
        if ($skill_id < 1) return false;
        
        if (isset($_GET['page'])) {
            $page_nr = (int)$_GET['page'];
            if ($page_nr > 0) {
                $start = ($page_nr - 1) * $max_per_page;
            }
        }
        
        $query = $this->db->get_results("
            SELECT `title`, `strid` FROM `pages` 
            WHERE `category` = $skill_id 
            ORDER BY `title` ASC 
            LIMIT $start, $max_per_page
        ");
        
        return $query;
    }
    
    /**
     *  Atgriež datus par minispēlēm vai distractions & diversions
     *
     *  @param int $cat_id  attiecīgās sadaļas id
     */
    public function fetch_minigames($cat_id = 0) {
    
        $cat_id = (int)$cat_id;
        if ($cat_id < 1) return false;
    
        $query = $this->db->get_results("
            SELECT
                `rs_pages`.`id`,
                `rs_pages`.`title`,
                `rs_pages`.`starting_point`,
                `rs_pages`.`members_only`,
                `rs_pages`.`safe`,
                `rs_pages`.`description`,
                IFNULL(`pages`.`id`, 0) AS `page_id`,
                `pages`.`strid`,
                `pages`.`avatar`                
            FROM `rs_pages`
                LEFT JOIN `pages` ON (
                    `rs_pages`.`page_id` = `pages`.`id` AND
                    `pages`.`category` = $cat_id
                )
            WHERE 
                `rs_pages`.`deleted_by` = 0 AND
                `rs_pages`.`is_hidden` = 0 AND
                `rs_pages`.`cat_id` = $cat_id
            ORDER BY 
                `rs_pages`.`title` ASC
        ");
        
        return $query;
    }
}
