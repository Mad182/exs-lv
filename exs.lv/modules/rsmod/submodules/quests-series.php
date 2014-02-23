<?php

/**
 * 	RuneScape questu sēriju pārvaldība.
 *
 *  Sēriju izmaiņas, kvestu piesaiste, to secība;
 *  nepieciešamo prasmju uzskaite.
 *
 * 	Moduļa adrese: runescape.exs.lv/series
 */

// mainīgais definēts parent failā, kas šo iekļauj (rsmod.php)
if ( !isset($sub_include) ) {
	die('No hacking, pls.');
}


/**
 *  jQuery pieprasījumi
 */
if (isset($_GET['_'])) {


    // atgriež sarakstu ar visiem kvestiem, kas nav piesaistīti norādītajai sērijai;
    // saraksts parādīsies atvērtā fancybox
    
    // var2 - sērijas ID no `rs_classes`
    if (isset($_GET['var1']) && $_GET['var1'] === 'list-quests' && isset($_GET['var2'])) {

        if ( !is_numeric($_GET['var2']) || !ctype_digit($_GET['var2']) || (int)$_GET['var2'] == 0) {
            echo json_encode(array('state' => 'parameter error'));
            exit;
        }
        
        // pārbauda, vai kvestu sērija ar šādu ID vispār eksistē
        $check_class = $db->get_row("
            SELECT `id` FROM `rs_classes` 
            WHERE `id` = ".(int)$_GET['var2']." AND `category` = 'series'
        ");        
        if ( !$check_class ) {
            echo json_encode(array('state' => 'series error'));
            exit;
        }
        
        // atlasa sērijai piesaistītos kvestus;
        // 99 un 100 ir kvestu kategorijas no `cat`
        $get_quests = $db->get_results("
            SELECT 
                `rs_pages`.`id`         AS `rspages_id`,
                `rs_pages`.`class_id`   AS `classes_id`,
                `pages`.`strid`         AS `pages_strid`,
                `pages`.`title`         AS `pages_title`
            FROM `rs_pages` 
                JOIN `pages` ON `rs_pages`.`page_id` = `pages`.`id`
            WHERE 
                `rs_pages`.`category_id` IN (99, 100) AND 
                `rs_pages`.`is_placeholder` = 0 AND
                `rs_pages`.`deleted_by`     = 0 AND
                `rs_pages`.`class_id` IN(0, ".(int)$_GET['var2'].")
            ORDER BY `pages`.`title` ASC
        ");
        if ( !$get_quests ) {
            echo json_encode(array('state' => 'quest list error'));
            exit;
        }

        $content  = '<div class="fancy-questlist">';
        $content .= '<h2>Piesaistāmie RuneScape kvesti</h2>';
        $content .= '<p style="color:#4A84B1">Sarakstā redzami tie kvesti, ';
        $content .= 'kas vai nu nav piesaistīti nevienai sērijai, ';
        $content .= 'vai arī ir piesaistīti atvērtajai sērijai.';
        $content .= '</p><ul>';
        
        foreach ($get_quests as $quest) {
        
            // iekrāso tos questus, kas izvēlētajai sērijai ir piesaistīti
            if ($quest->classes_id != 0) {
                $content .= '<li class="mark-added">';
                $content .= '<a href="/read/'.$quest->pages_strid.'">';
                $content .= $quest->pages_title.'</a> ';
                $content .= '<a class="related-quest" href="/series/remove-related/';
                $content .= (int)$check_class->id.'/'.(int)$quest->rspages_id;
                $content .= '">[ - ]</a></li>';
            }
            // vai arī neiekrāso...
            else {
                $content .= '<li>';
                $content .= '<a href="/read/'.$quest->pages_strid.'">';
                $content .= $quest->pages_title.'</a> ';
                $content .= '<a class="related-quest" href="/series/add-related/';
                $content .= (int)$check_class->id.'/'.(int)$quest->rspages_id;
                $content .= '">[ + ]</a></li>';
            }
        }
        
        $content .= '</ul></div>';
        
        echo json_encode(array('state' => 'success', 'content' => $content));
        exit;
    } 
    

    
    
    
    // pieprasījums piesaistīt/atsaistīt sērijai kvestu
    
    // var2 - `rs_classes`.`id`, var3 - `rs_pages`.`id`
    if ( isset($_GET['var1']) && ($_GET['var1'] == 'add-related' || $_GET['var1'] == 'remove-related') &&
         isset($_GET['var2']) && isset($_GET['var3'])) {
         
        if ( !is_numeric($_GET['var2']) || !ctype_digit($_GET['var2']) || (int)$_GET['var2'] == 0) {
            echo json_encode(array('state' => 'parameter 1 error'));
            exit;
        }
        if ( !is_numeric($_GET['var3']) || !ctype_digit($_GET['var3']) || (int)$_GET['var3'] == 0) {
            echo json_encode(array('state' => 'parameter 2 error'));
            exit;
        }
        
        // pārbaude, ka eksistē norādītā sērija
        $check_class = $db->get_row("
            SELECT `id` FROM `rs_classes` WHERE `id` = ".(int)$_GET['var2']." AND `category` = 'series'
        ");
        if ( !$check_class ) {
            echo json_encode(array('state' => 'series error'));
            exit;
        }
        
        $class_to_check = ($_GET['var1'] == 'add-related') ? 0 : (int)$check_class->id;
        
        // pārbaude, ka eksistē šāda pamācība
        $check_page = $db->get_row("
            SELECT 
                `rs_pages`.`id`, 
                `rs_pages`.`class_id`,
                `pages`.`strid` AS `pages_strid`,
                `pages`.`title` AS `pages_title`
            FROM `rs_pages`
                JOIN `pages` ON `rs_pages`.`page_id` = `pages`.`id`
            WHERE 
                `rs_pages`.`id`                = ".(int)$_GET['var3']." AND 
                `rs_pages`.`class_id`          = $class_to_check AND
                `rs_pages`.`deleted_by`        = 0 AND 
                `rs_pages`.`is_placeholder`    = 0
        ");
        if ( !$check_page ) {
            echo json_encode(array('state' => 'quest error'));
            exit;
        }
        
        // vēloties kvestu piesaistīt...
        if ($_GET['var1'] == 'add-related') {
            
            $db->query("
                UPDATE `rs_pages` 
                SET 
                    `class_id` = ".(int)$check_class->id.",
                    `updated_by` = ".(int)$auth->id.",
                    `updated_at` = '".(int)time()."'
                WHERE `id` = ".(int)$check_page->id." 
                LIMIT 1
            ");
            
            $content  = '<li>';
            $content .=     '<a id="quest-'.(int)$check_page->id.'"';
            $content .=     ' href="/read/'.$check_page->pages_strid.'">';
            $content .=     $check_page->pages_title.'</a>';        
            $content .= '</li>';
            
            $quest_addr     = '/series/remove-related/'.(int)$check_class->id.'/'.(int)$check_page->id;
            $url_inner      = '[ - ]';
            $action_type    = 'added';
        }
        // vēloties kvestu atsaistīt...
        else {         
            
            $db->query("
                UPDATE `rs_pages` 
                SET 
                    `class_id` = 0,
                    `updated_by` = ".(int)$auth->id.",
                    `updated_at` = '".(int)time()."'
                WHERE `id` = ".(int)$check_page->id." 
                LIMIT 1
            ");
            
            $content        = '';
            $quest_addr     = '/series/add-related/'.(int)$check_class->id.'/'.(int)$check_page->id;
            $url_inner      = '[ + ]';
            $action_type    = 'removed';
        }

        // lai HTML elementiem paliktu piesaistītas jQuery darbības,
        // saistītajiem elementiem tiek mainītas tikai atribūtu vērtības
        echo json_encode(array(
            'state'     => 'success',
            'type'      => $action_type,    // added/removed
            'content'   => $content,
            'series_id' => $check_class->id,
            'quest_id'  => $check_page->id,
            'url'       => $quest_addr,
            'url_inner' => $url_inner
        ));
        exit;
    }

    exit;
}



/**
 *  Pārējās iespējas
 */



// sēriju secības atjaunošana
if (isset($_GET['var1']) && $_GET['var1'] == 'update' && isset($_POST['submit'])) {

	$series = $db->get_results("SELECT `id` FROM `rs_classes` WHERE `category` = 'series'");

	if ($series) {
		foreach ($series as $single) {
			if (isset($_POST['order_' . $single->id]) && isset($_POST['title_' . $single->id])) {

				$order = (int) $_POST['order_' . $single->id];
				$title = strip_tags(trim($_POST['title_' . $single->id]));
				$title = sanitize(substr($title, 0, 50));

				$db->query("
                    UPDATE `rs_classes` 
                    SET 
                        `ordered` = $order, 
                        `title` = '$title'
                    WHERE `id` = '".(int)$single->id."' 
                    LIMIT 1
                ");
			}
		}
	}
	set_flash('Sēriju secība un nosaukumi veiksmīgi atjaunināti!');
	redirect('/' . $_GET['viewcat']);
}





// sērijas kvestu secības atjaunošana;
// var2 - sērijas ID no `rs_classes`
if (isset($_GET['var1']) && $_GET['var1'] == 'change-order' && isset($_GET['var2']) && isset($_POST['submit'])) {

    $series = $db->get_results("SELECT `id` FROM `rs_classes` WHERE `category` = 'series'");

    if ( !is_numeric($_GET['var2']) || !ctype_digit($_GET['var2']) || (int)$_GET['var2'] == 0) {
        set_flash('Kļūdaini norādīta sērija!');
        redirect('/' . $_GET['viewcat']);
    }
    else if ( !$series ) {
        set_flash('Kļūdaini norādīta sērija!');
        redirect('/' . $_GET['viewcat']);
    }

    // pārbaude, ka eksistē šāda pamācība
    $series_quests = $db->get_results("
        SELECT 
            `rs_pages`.`id`, 
            `rs_pages`.`class_id`
        FROM `rs_pages`
            JOIN `rs_classes` ON (
                `rs_pages`.`class_id` = `rs_classes`.`id` AND
                `rs_classes`.`category` = 'series'
            )
        WHERE
            `rs_pages`.`class_id`        = ".(int)$_GET['var2']." AND
            `rs_pages`.`deleted_by`      = 0 AND 
            `rs_pages`.`is_placeholder`  = 0
    ");
    if ( !$series_quests ) {
        set_flash('Sērijai nav neviena piesaistīta raksta!');
        redirect('/' . $_GET['viewcat']);
    }

    foreach ($series_quests as $single) {
    
        if (isset($_POST['related-' . $single->id])) {

            $order = (int) $_POST['related-' . $single->id];

            $db->query("
                UPDATE `rs_pages` SET 
                    `ordered` = $order, 
                    `updated_by` = ".(int)$auth->id.",
                    `updated_at` = '".(int)time()."'
                WHERE `id` = ".(int)$single->id."
                LIMIT 1
            ");
        }        
    }
        
	set_flash('Sēriju secība un nosaukumi atjaunināti!');
	redirect('/' . $_GET['viewcat']);
}





else {

	/**
	 *  izdrukās visas pievienotās storylines
	 */
	$series = $db->get_results("
        SELECT 
            `id`        AS `class_id`,
            `title`     AS `class_title`, 
            `ordered`   AS `class_order`
        FROM `rs_classes` 
        WHERE 
            `category` = 'series' 
        ORDER BY `ordered` ASC 
    ");
	if ($series) {        

		$counter = 0;
		$series_count = count($series);

		// skaits, aiz kura sarakstu pārdalīt uz pusēm
		$col_split = floor($series_count / 2);

		$tpl->newBlock('series-form');

		foreach ($series as $single) {
        
			// izveido jaunu saraksta kolonnu
			if ($counter == 0 || $counter == $col_split) {
				$tpl->newBlock('series-column');
			}

			$tpl->newBlock('single-series');
			$tpl->assignAll($single);

			// katrai sērijai ir izvēlne ar kārtas numuriem
			for ($i = 1; $i <= $series_count; $i++) {
				$selected = ($i == $single->class_order) ? ' selected="selected"' : '';
				$tpl->newBlock('selection-option');
				$tpl->assign(array(
					'ordered' => $i,
					'selected' => $selected
				));
			}            
            
            // katrai sērijai slēptā HTML elementā 
            // izdrukā sarakstu ar piesaistītajiem kvestiem
            $tpl->gotoBlock('single-series');            
            
            $related_quests = $db->get_results("
                SELECT
                    `pages`.`id`            AS `pages_id`,
                    `pages`.`strid`         AS `pages_strid`,
                    `pages`.`title`         AS `pages_title`,
                    `rs_pages`.`id`         AS `rspages_id`,
                    `rs_pages`.`ordered`    AS `rspages_order`
                FROM `rs_pages`
                    JOIN `pages` ON (
                        `rs_pages`.`page_id` = `pages`.`id`
                    )
                    JOIN `rs_classes` ON (
                        `rs_pages`.`class_id` = `rs_classes`.`id`
                    )
                WHERE 
                    `rs_pages`.`deleted_by`     = 0 AND
                    `rs_pages`.`is_placeholder` = 0 AND 
                    `rs_pages`.`class_id`       = ".(int)$single->class_id."
                ORDER BY `rs_pages`.`ordered` ASC
            ");
            
            // related quests-order
            if ($related_quests) {
            
                $tpl->newBlock('related-quests');
                $tpl->assign('class_id', $single->class_id);
                
                $related_count = count($related_quests);
                
                foreach ($related_quests as $related_quest) {
                
                    $tpl->newBlock('related-quest');
                    $tpl->assignAll($related_quest);                    
                    $tpl->assign('rspages_id', (int)$related_quest->rspages_id);
                    
                    // saistītajiem kvestiem iespējams mainīt secību
                    for ($i = 0; $i < $related_count; $i++) {
                        $tpl->newBlock('option-param');
                        $tpl->assign('value', $i + 1);
                        if ($i + 1 == $related_quest->rspages_order) {
                            $tpl->assign('selected', ' selected="selected"');
                        }
                    }
                    
                }
            }
            else {
                $tpl->newBlock('no-related-quests');
            }
            
			$counter++;
		}
	}
    
    
    
    
    /**
	 *  izdrukās kvestiem nepieciešamās prasmes un rediģēšanas formu
	 */
    $skills = $db->get_results("SELECT * FROM `rs_qskills` ORDER BY `skill` ASC");
    
    // info atjaunošana
    if ($skills && isset($_GET['var1']) && $_GET['var1'] == 'skills' && isset($_POST['submit'])) {
        
        foreach ($skills as $skill) {
        
            if (isset($_POST['level-'.$skill->id]) && isset($_POST['quest-'.$skill->id])) {
                $db->query("
                    UPDATE `rs_qskills` 
                    SET 
                        `level` = ".(int)$_POST['level-'.$skill->id].", 
                        `page_title` = '".sanitize($_POST['quest-'.$skill->id])."'
                    WHERE `id` = ".(int)$skill->id."
                    LIMIT 1
                ");
            }
        }
        set_flash('Informācija atjaunināta!');
        redirect('/'.$_GET['viewcat']);
    } 

    // info izdrukāšana tabulas veidā
    if ($skills) {
    
        $tpl->newBlock('quests-skills');
        $tpl->newBlock('skills-column');
        
        $counter = 0;
        $split_by = floor(count($skills) / 2);
        
        foreach ($skills as $data) {
        
            $tpl->newBlock('single-skill');
            $tpl->assignAll($data);
            
            if (++$counter == $split_by) {
                $tpl->newBlock('skills-column');
            }
        }
    }
}