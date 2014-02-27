<?php
/** 	
 * 	RuneScape apakšprojekta sākumlapas modulis
 */
if ($auth->ok) {
	set_action('sākumlapu');
}

// runescape oficiālo jaunumu virsraksti no RSS feed
$tpl->assign('runescape-news', get_runescape_news());    
    
// sākumlapā 2 kolonnās parāda jaunākos un pēdējos komentētos rakstus;
// pēc tā arī attiecīgi SQL pieprasījumā rakstus atlasa
//$types_of_pages = array('date', 'bump');
$types_of_pages = array();


/*foreach ($types_of_pages as $type) {

    $about_blogs = ($type == 'date') ? 
        '`cat`.`parent` IN(1903,4,102,194,160,599,791,791)' :
        '`cat`.`isblog` != 0';

    $runescape_pages = $db->get_results("
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
            ".$about_blogs."
        ORDER BY `pages`.`bump` DESC 
        LIMIT 8
    ");

    if ($runescape_pages) {
        
        $tpl->newBlock('recent-page-list');        
        if ($type == 'date') {
            $tpl->assign(array(
                'column-style' => ' style="margin-right:20px"',
                'column-title' => 'Pēdējie komentētie raksti'
            ));
        } else {
            $tpl->assign('column-title', 'Jaunākais blogos');
        }

        $article_counter = 1;
        foreach ($runescape_pages as $article) {
        
            $article->title 	= str_replace(array('[RuneScape] ','[RS] '),'',$article->title);
            $article->author 	= usercolor($article->user_nick, $article->user_level);
            $article->author 	= '<a href="'.mkurl('user', $article->user_id, $article->user_nick).'">'.$article->author.'</a>';
            
            $tpl->newBlock('list-page');
            $tpl->assignAll($article);
            
            if ($type == 'date') {            
                $additional_info  = $article->author . ' &middot; pirms ' . time_ago(strtotime($article->date));       
            }
            else {
                $additional_info = 'pirms ' . time_ago(strtotime($article->bump));
        
                if (!empty($article->readby) && in_array($auth->id, unserialize($article->readby))) {
                    $additional_info .= ' &middot; (' . $article->posts . ')';
                } else {
                    $additional_info .= ' &middot; (<span class="r">' . $article->posts . '</span>)';
                }                
                $additional_info .= ' &middot; ' . $article->cat_title;
            }
            $tpl->assign('additional-info', $additional_info);
                    
            $article_counter++;
        }
    }
}*/