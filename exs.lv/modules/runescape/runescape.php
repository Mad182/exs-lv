<?php
/** 	
 * 	RuneScape apakšprojekta sākumlapas modulis
 */
if ($auth->ok) {
	set_action('sākumlapu');
}

// vērtības lappušu saraksta veidošanai
$pages_limit 	= 8; // rakstu skaits vienā lappusē
$pages_count 	= ceil($db->get_var("SELECT count(*) FROM `pages` WHERE `pages`.`category` = 599 AND `lang` = $lang") / $pages_limit);

$current_page 	= (isset($_GET['page'])) ? (int)$_GET['page'] : 0;
$current_page 	= ($current_page > $pages_count || $current_page < 1) ? 1 : $current_page;
$pages_start 	= ($current_page - 1) * $pages_limit;



// runescape oficiālo jaunumu virsraksti no RSS feed
$tpl->assign('runescape-news', get_runescape_news());
$tpl->newBlock('main-runescape-header');
    

if (!isset($_GET['_']) || !isset($_GET['type']) || $_GET['type'] == 'newest') {
    // izdrukā lapā svaigākos RuneScape jaunumu rakstus
    $newest_pages = $db->get_results("
        SELECT
            `pages`.*,    
            `users`.`id`      AS `user_id`,
            `users`.`nick`    AS `user_nick`,
            `users`.`level`   AS `user_level`,
            `cat`.`title`     AS `cat_title`
        FROM `pages`
            JOIN `users` ON `pages`.`author` = `users`.`id`
            JOIN `cat` ON `pages`.`category` = `cat`.`id`
        WHERE
            `pages`.`lang`      = '$lang' AND
            `cat`.`isforum`     = 0 AND
            `cat`.`isblog`      = 0
        ORDER BY `pages`.`date` DESC 
        LIMIT $pages_start , $pages_limit 
    ");

    if ($newest_pages) {
    
        /*
            $tpl = new TemplatePower(CORE_PATH . '/modules/' . $category->module . '/' . $category->module . '.tpl');
            $templ->prepare();
        */
    
        $article_counter = 1;
        
        $tpl->newBlock('recent-page-list');
        $tpl->assign(array(
            'list-title' => 'Jaunākie raksti lapā',
            'column-style' => ' style="margin-right:20px"'
        ));

        foreach ($newest_pages as $article) {
        
            $article->title 	= str_replace(array('[RuneScape] ','[RS] '),'',$article->title);
            $article->author 	= usercolor($article->user_nick, $article->user_level);
            $article->author 	= '<a href="'.mkurl('user', $article->user_id, $article->user_nick).'">'.$article->author.'</a>';
            
            $tpl->newBlock('list-page');
            $tpl->assignAll($article);
            
            $additional_info  = $article->author . ' &middot; pirms ' . time_ago(strtotime($article->date));       
            $tpl->assign('additional-info', $additional_info);
                    
            $article_counter++;
        }

        //  atvērtajai lappusei katrā pusē būs vēl trīs iepriekšējās/nākamās lappuses
        $page_view = pagelist(ceil($pages_count), $current_page, '/?type=newest&amp;page=', 'news-col-newest', 1, 1);
        $tpl->gotoBlock('recent-page-list');
        $tpl->assign('pages', $page_view);
        
        /*if (isset($_GET['_']) && isset($_GET['type']) && $_GET['type'] == 'newest') {
            $templ->newBlock('pagelist-runtime-bind');
            echo json_encode(array('status' => 'success', 'content' => $templ->getOutputContent()));
            exit;
        }
        else {*/
            //$tpl->newBlock('recent-page-list-default');
           // $tpl->assign('content', $tpl->getOutputContent());
        //}
    }
}

if (!isset($_GET['_']) || !isset($_GET['type']) || $_GET['type'] == 'recent') {
    // izdrukā lapā svaigākos RuneScape jaunumu rakstus
    $recent_pages = $db->get_results("
        SELECT
            `pages`.*,    
            `users`.`id`      AS `user_id`,
            `users`.`nick`    AS `user_nick`,
            `users`.`level`   AS `user_level`,
            `cat`.`title`     AS `cat_title`
        FROM `pages`
            JOIN `users` ON `pages`.`author` = `users`.`id`
            JOIN `cat` ON `pages`.`category` = `cat`.`id`
        WHERE
            `pages`.`lang`      = '$lang' AND
            `cat`.`isforum`     = 0 AND
            `cat`.`isblog`      = 0
        ORDER BY `pages`.`bump` DESC
        LIMIT $pages_start , $pages_limit 
    ");

    if ($recent_pages) {
    
        /*
            $tpl = new TemplatePower(CORE_PATH . '/modules/' . $category->module . '/' . $category->module . '.tpl');
            $templ->prepare();
        */

        $article_counter = 1;
        
        $tpl->newBlock('recent-page-list');
        $tpl->assign('list-title', 'Pēdējie komentētie raksti');

        foreach ($recent_pages as $article) {
        
            $article->title 	= str_replace(array('[RuneScape] ','[RS] '),'',$article->title);
            $article->author 	= usercolor($article->user_nick, $article->user_level);
            $article->author 	= '<a href="'.mkurl('user', $article->user_id, $article->user_nick).'">'.$article->author.'</a>';

            $tpl->newBlock('list-page');
            $tpl->assignAll($article);

            $additional_info = 'pirms ' . time_ago(strtotime($article->bump));
            
            if (!empty($article->readby) && in_array($auth->id, unserialize($article->readby))) {
                $additional_info .= ' &middot; (' . $article->posts . ')';
            } else {
                $additional_info .= ' &middot; (<span class="r">' . $article->posts . '</span>)';
            }
            
            $additional_info .= ' &middot; ' . $article->cat_title;
            
            $tpl->assign('additional-info', $additional_info);
                    
            $article_counter++;
        }
        
        //  atvērtajai lappusei katrā pusē būs vēl trīs iepriekšējās/nākamās lappuses
        $page_view = pagelist(ceil($pages_count), $current_page, '/?page=', 'news-col-recent', 1, 1);
        $tpl->gotoBlock('recent-page-list');
        $tpl->assign('pages', $page_view);
        
        /*if (isset($_GET['_']) && isset($_GET['type']) && $_GET['type'] == 'newest') {
            echo json_encode(array('status' => 'success', 'content' => $templ->getOutputContent()));
            exit;
        }
        else {
            $tpl->newBlock('recent-page-list-default');
            $tpl->assign('content', $templ->getOutputContent());
        }*/
    }
}