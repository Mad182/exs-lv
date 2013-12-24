<?php
/**	
 *	runescape apakšprojekta sākumlapas modulis
 */
if ($auth->ok) {
	set_action('sākumlapu');
}

// vērtības lappušu saraksta veidošanai
$pages_limit 	= 10;	// rakstu skaits vienā lappusē
$pages_count 	= ceil($db->get_var("SELECT count(*) FROM `pages` WHERE `pages`.`category`  = 599 AND `lang` = $lang") / $pages_limit);

$current_page   = ( isset($_GET['page']) ) ? (int)$_GET['page'] : 0;
$current_page	= ( $current_page > $pages_count || $current_page < 1 ) ? 1 : $current_page;
$pages_start 	= ( $current_page - 1 ) * $pages_limit;


$tpl_options = '';
$tpl->assign('page-content-title', 'RuneScape jaunumi');


// izdrukā lapā svaigākos RuneScape jaunumu rakstus
$articles = $db->get_results("
    SELECT
        `pages`.*,    
        `users`.`id`      AS `user_id`,
        `users`.`nick`    AS `user_nick`,
        `users`.`level`   AS `user_level`
  	FROM `pages`
        JOIN `users` ON `pages`.`author` = `users`.`id`
    WHERE 
        `pages`.`category`  = 599       AND
        `pages`.`lang`      = '$lang'
    ORDER BY `pages`.`date` DESC 
    LIMIT $pages_start , $pages_limit 
");

if ($articles) {
    
    $counter = 0;
    $tpl->newBlock('rs-articles');
    
    foreach ($articles as $article) {
    
        //  ja rakstam nav norādīts intro, to izveido
        if ( !empty($article->intro) ) {
			$article->text = $article->intro;
		} else {
			$article->text = textlimit(strip_tags(trim(str_replace(array('&nbsp;', '<br />', '<li>'), ' ', youtube_title($article->text)))), 680);
			$article->intro = sanitize($article->text);
			$db->query("UPDATE `pages` SET `intro` = '$article->intro' WHERE `id` = '$article->id' LIMIT 1");
		}
        $article->text = textlimit($article->text, 500);
        
        if ($article->sm_avatar == '') {
			$article->sm_avatar = '/dati/bildes/useravatar/none.png';
		} else {
            $article->sm_avatar = 'http://img.exs.lv/'.trim($article->sm_avatar);
        }
    
        $article->title     = str_replace(array('[RuneScape] ', '[Runescape] ', '[rs] ', '[RS] ', '[runescape] '), '', $article->title);
        $article->date      = display_time(strtotime($article->date));
        
        $article->user_nick = usercolor($article->user_nick, $article->user_level);
        $article->user_nick = '<a href="'.mkurl('user', $article->user_id, $article->user_nick).'">'.$article->user_nick.'</a>';
    
        $tpl->newBlock('rs-article');
        $tpl->assignAll($article);
        
        $tpl->newBlock('article-image');
        $tpl->assign('sm_avatar', $article->sm_avatar);
        
        $counter++;
    }
    
    //  atvērtajai lappusei katrā pusē būs vēl trīs iepriekšējās/nākamās lappuses
    $page_view = pagelist( ceil($pages_count), $current_page, '/?page=', 3, 3);
    $tpl->gotoBlock('rs-articles');
    $tpl->assign('pages', $page_view);
}
