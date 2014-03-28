<?php
/**
 *  android.exs.lv izmantotās funkcijas
 */
 
 
/**
 *  Atgriež JSON sarakstu ar jaunākajiem exs.lv rakstiem
 *
 *  Atbalsta pārvietošanos pa lapām un apakšprojektus.
 *
 *  @param  int     skaits, cik rakstu rādīt vienā lapā
 */
function get_news($in_page = 20) {
	global $auth, $db, $lang, $android_lang;
    
    // rakstu skaits, cik izlaist
	$skip = 0;
	if (isset($_GET['page'])) {
		$skip = $in_page * (intval($_GET['page']) - 1);
	}
    
    // tiek pievienoti kritēriji rakstu atlasei
	$conditions = array();
    
    // redzami izvēlētā apakšprojekta vai $lang=0 raksti
    $conditions[] = '(`pages`.`lang` = ' . (int)$android_lang . ' || `pages`.`lang` = 0)';

    // atlasa sadaļas, kuras lietotājs vēlas ignorēt
	if ($auth->ok) {
		$ignores = $db->get_col("SELECT `category_id` FROM `cat_ignore` WHERE `user_id` = '$auth->id'");
		if (!empty($ignores)) {
			foreach ($ignores as $ignore) {
				$conditions[] = "`category` != $ignore";
			}
		}
	}

    // moderatoru sadaļu pārbaude
	$mods_only = '';
	if (!im_mod()) {
		$mods_only = " AND `cat`.`mods_only` = 0";
	}

    // tiek atlasīti izvēlētie raksti
	$latest = $db->get_results("
        SELECT
            `pages`.`id`,
            `pages`.`strid`,
            `pages`.`title`,
            `pages`.`category`,
            `pages`.`posts`,
            `pages`.`readby`,
            `pages`.`bump`,
            `cat`.`mods_only`,
            `cat`.`title` AS `cat_title`
        FROM `pages`
            JOIN `cat` ON `pages`.`category` = `cat`.`id`
        WHERE
            " . implode(' AND ', $conditions) . $mods_only . "            
        ORDER BY
            `pages`.`bump` DESC LIMIT $skip, $in_page
    ");

    // masīvs, kas tiks atgriezts
    $arr_news = array();
    
	if ( !$latest ) {
        return $arr_news;
    }
    
    foreach ($latest as $late) {
    
        // statuss, kas norādīs, vai lietotājs rakstu ir lasījis
        $is_read = false;
        if (!empty($late->readby) && in_array($auth->id, unserialize($late->readby))) {
           $is_read = true;
        }        
    
        $arr_news[] = array(
            $late->id, 
            $late->title, 
            $late->cat_title,
            $late->posts,
            $late->mods_only,
            $late->bump,
            $is_read
        );
    }
    
    return $arr_news;
}