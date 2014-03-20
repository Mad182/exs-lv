<?php
/**
 *  android.exs.lv izmantotās funkcijas
 */
 
 
/**
 *  Atgriež sarakstu ar jaunākajiem exs.lv rakstiem
 */
function get_news() {
	global $auth, $db, $lang;
	$out = '';

	$skip = 0;
	if (isset($_GET['page'])) {
		$skip = 8 * intval($_GET['page']);
	}

	$conditions = array();

    // raksti no dažādiem apakšprojektiem,
    // kas attiecīgajam lietotājam interesē
    $add_langs = array("`pages`.`lang` = '1'");
    if (!empty($auth->show_code)) {
        $add_langs[] = "`pages`.`lang` = '3'";
    }
    if (!empty($auth->show_rp)) {
        $add_langs[] = "`pages`.`lang` = '5'";
    }
    if (!empty($auth->show_lol)) {
        $add_langs[] = "`pages`.`lang` = '7'";
    }
    if (!empty($auth->show_rs)) {
        $add_langs[] = "`pages`.`lang` = '9'";
    }
    $conditions[] = '(' . implode(' OR ', $add_langs) . ')';


    // atlasa sadaļas, kuras lietotājs vēlas ignorēt,
    // un pievieno kritērijiem
	if ($auth->ok) {
		$ignores = $db->get_col("SELECT `category_id` FROM `cat_ignore` WHERE `user_id` = '$auth->id'");
		if (!empty($ignores)) {
			foreach ($ignores as $ignore) {
				$conditions[] = "`category` != $ignore";
			}
		}
	}

    // moderatoru sadaļas
	$mods_only = '';
	if (!im_mod()) {
		$mods_only = " AND `cat`.`mods_only` = 0";
	}

    // pēc noteiktajiem kritērijiem atlasa visus rakstus
	$latest = $db->get_results("
        SELECT
            `pages`.`title`,
            `pages`.`id`,
            `pages`.`posts`,
            `pages`.`readby`,
            `pages`.`strid`,
            `pages`.`category`,
            `pages`.`lang`,
            `pages`.`bump`,
            `cat`.`mods_only`,
            `cat`.`title` AS `cat_title`
        FROM `pages`
            JOIN `cat` ON `pages`.`category` = `cat`.`id`
        WHERE
            " . implode(' AND ', $conditions) . $mods_only . "            
        ORDER BY
            `pages`.`bump` DESC LIMIT $skip, 8
    ");

    // masīvs, kas tiks atgriezts
    $arr_news = array();
    
	if ( !$latest ) {
        return $arr_news;
    }
    

    foreach ($latest as $late) {
    
        $arr_news[] = array(
            $late->id, 
            $late->title, 
            $late->cat_title
        );
        
        /*$skip = '';
        if ($late->posts > $comments_per_page) {
            $posts = $db->get_var("SELECT count(*) FROM `comments` WHERE `pid` = $late->id AND `parent` = 0 AND `removed` = 0");
            if ($posts > $comments_per_page) {
                $skip = '/com_page/' . floor(($posts - 1) / $comments_per_page);
            }
        }

        if ($late->mods_only == 1) {
            $late->title = '<em>' . $late->title . '</em>';
        }

        $out .= '<li><a href="' . $url . $skip . '"><img src="http://exs.lv/dati/bildes/topic-av/' . $late->id . '.jpg" class="av" alt="" />';

        $out .= '<span>pirms ' . time_ago(strtotime($late->bump)) . '</span>';

        if (!empty($late->readby) && in_array($auth->id, unserialize($late->readby))) {
            $out .= $prefix . $late->title . '&nbsp;[' . $late->posts . ']</a></li>';
        } else {
            $out .= $prefix . $late->title . '&nbsp;[<span class="r">' . $late->posts . '</span>]</a></li>';
        }
        */
    }
    
    return $arr_news;
}