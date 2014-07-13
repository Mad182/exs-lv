<?php
/**
 *  Darbības ar pamācībām, kuras nav pievienotas `rs_pages` tabulā
 */

class Model_Unlisted extends Model {

    private $cats;

    public function __construct() {
        global $cat_f2p_quests, $cat_p2p_quests, $cat_miniquests;
        global $cat_minigames, $cat_distractions, $cat_guilds;

        $this->cats = array($cat_f2p_quests, $cat_p2p_quests, $cat_miniquests, 
            $cat_minigames, $cat_distractions, $cat_guilds);

        parent::__construct();
    }
    
    /**
     *  Atgriež RuneScape pamācības, kuras `rs_pages` tabulā nav
     */
    public function fetch_pages() {
        
        $query = $this->db->get_results("
            SELECT
                `pages`.`id`     AS `id`,
                `pages`.`strid`  AS `strid`,
                `pages`.`title`  AS `title`
            FROM `pages`
                LEFT JOIN `rs_pages` ON ( 
                    `pages`.`id` = `rs_pages`.`page_id` AND
                    `rs_pages`.`deleted_by` = 0
                )
            WHERE
                `pages`.`lang` = 9 AND
                `pages`.`category` IN(".implode(',', $this->cats).") AND
                `rs_pages`.`id` IS NULL
            ORDER BY `pages`.`title` ASC
        ");
        
        return $query;
    }
}
