<?php
/**
 *  Darbībām, kas saistītas ar RuneScape noklusēto pamācību sarakstu
 *  (rshelp.php)
 */

class Model_Rshelp extends Model {
    
    // skaits attiecas uz rakstiem
    private $max_per_page = 30;
    
    public function __construct() {
        $this->globals(array('cat_rsnews', 'cat_padomi'));
        parent::__construct();
    }

    /**
     *  Atgriež rakstus, kas pievienoti norādītajai sadaļai
     */
    public function fetch_items($category_id = 0) {

        $category_id = (int)$category_id;

        $start = (isset($_GET['skip']) && (int)$_GET['skip'] > 0) ? 
            (int)$_GET['skip'] : 0;
        
        // parastās sadaļās vienā lapā būs redzams $max_per_page skaits rakstu,
        // savukārt /padomi sadaļā - visi raksti    
        $limit = ($this->category->id == $this->cat_padomi) ? 
            "" : "LIMIT $start, $this->max_per_page";

        $order_by   = 'ORDER BY `title` ASC';    
        if ($this->category->id == $this->cat_rsnews) {
            $order_by   = 'ORDER BY `date` DESC ';
        }
        
        $query = $this->db->get_results("
            SELECT * FROM `pages` 
            WHERE `category` = $category_id
            $order_by $limit
        ");

        return $query;
    }
    
    /**
     *  Atlasa informāciju par RuneScape ziņām
     */
    public function fetch_news($category_id = 0) {
    
        $category_id = (int)$category_id;
        if ($category_id < 1) return false;

        $skip = 0;
        if (isset($_GET['skip'])) {
            $skip = (int)$_GET['skip'];
        } else {
            $skip = 0;
        }        
        $end = 20;
    
        $query = $this->db->get_results("
            SELECT
                `pages`.*,
                `users`.`nick`,
                `users`.`level`
            FROM `pages`
                JOIN `users` ON `pages`.`author` = `users`.`id`
            WHERE
                `pages`.`category` = $category_id
            ORDER BY
                `pages`.`date` DESC
            LIMIT
                $skip, $end
        ");
                
        return $query;
    }
}
