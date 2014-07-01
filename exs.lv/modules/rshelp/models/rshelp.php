<?php
/**
 *  Darbībām, kas saistītas ar RuneScape noklusēto pamācību sarakstu
 *  (rshelp.php)
 */

class Model_Rshelp extends Model {

    private $cat_rsnews = 599;
    private $cat_padomi = 5;

    /**
     *  Atgriež rakstus, kas pievienoti norādītajai sadaļai
     */
    public function fetch_items($category_id = 0) {
    
        // skaits, cik rakstu rādīt vienā lapā
        $lim_end = 30;
        $lim_start = (isset($_GET['skip']) && (int)$_GET['skip'] > 0) ? 
            (int)$_GET['skip'] : 0;
        
        // parastās sadaļās vienā lapā būs redzams $lim_end skaits rakstu,
        // savukārt /padomi sadaļā - visi raksti    
        $limit = ($this->category->id == $this->cat_padomi) ? 
            '' : 'LIMIT '.$lim_start.', '.$lim_end;
        
        // parastās sadaļās kārtos pēc raksta nosaukuma;
        // savukārt rs ziņu sadaļā - pēc datuma
        $order_by   = 'ORDER BY `title` ASC';    
        if ($this->category->id == $this->cat_rsnews) { // rs jaunumu raksti
            $order_by   = 'ORDER BY `date` DESC ';
        }
        
        $category_id = (int)$category_id;
        
        $query = $this->db->get_results("
            SELECT `strid`, `title`, `author` FROM `pages` 
            WHERE `category` = $category_id
            $order_by 
            $limit
        ");

        return $query;
    }
}
