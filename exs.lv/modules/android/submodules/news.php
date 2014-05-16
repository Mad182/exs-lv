<?php
/**
 *  Android rakstu apakšmodulis
 *
 *  Kaut ko šeit darīs saistībā ar rakstiem.
 */

// pa tiešo šeit nebūs nekādas skatīšanās
!isset($sub_include) and die('Error loading page!');




// izvēlēts konkrēts raksts;
// parādīs tā saturu un komentārus
if (isset($_GET['var1'])) {

    // raksta informācija
    $news_data = $db->get_row("
        SELECT 
            `pages`.`id`        AS `page_id`,
            `pages`.`title`     AS `page_title`, 
            `pages`.`text`      AS `page_text`,
            `pages`.`date`      AS `page_date`,
            `cat`.`title`       AS `category`,
            `users`.`id`        AS `user_id`,
            `users`.`nick`      AS `user_nick`,
            `users`.`level`     AS `user_level`
        FROM `pages` 
            JOIN `users` ON `pages`.`author` = `users`.`id`
            JOIN `cat` ON `pages`.`category` = `cat`.`id`
        WHERE 
            `pages`.`id` = '".(int)$_GET['var1']."' AND
            (`pages`.`lang` = '$android_lang' OR `pages`.`lang` = 0)
    ");
    // raksta komentāri
    $page_comments = $db->get_results("
        SELECT 
            `comments`.`id`         AS `comment_id`,
            `comments`.`text`       AS `comment_text`,
            `comments`.`date`       AS `comment_date`,
            `comments`.`replies`    AS `comment_replies`,
            `users`.`id`            AS `user_id`,
            `users`.`nick`          AS `user_nick`,
            `users`.`level`         AS `user_level`,
            `users`.`avatar`        AS `avatar`,
            `users`.`av_alt`        AS `av_alt`
        FROM `comments`
            JOIN `users` ON `comments`.`author` = `users`.`id`
        WHERE 
            `comments`.`pid` = '" . (int)$_GET['var1'] . "' AND 
            `comments`.`parent` = 0 AND 
            `comments`.`removed` = 0 
        ORDER BY `comments`.`id` ASC
    ");
    
    // masīvi, kas tiks pievienoti $json_page 
    $about_news = array();
    $comments   = array();
    
    // informācija par rakstu atrasta
    if ($news_data) {
        $about_news = array(
            'article_id'    => (int)$news_data->page_id,    
            'article_title' => $news_data->page_title,
            'article_text'  => $news_data->page_text,
            'article_date'  => display_time(strtotime($news_data->page_date)),
            'category'      => $news_data->category,
            'user_data'     => a_fetch_user($news_data->user_id, 
                                            $news_data->user_nick, 
                                            $news_data->user_level)
        );
    }
    
    // nebūtu jēdzīgi rādīt komentārus, ja neatrastu rakstu,
    // tāpēc arī raksta pārbaude
    if ($news_data && $page_comments) {   
        foreach ($page_comments as $single_comment) {
            $comments[] = array(
                'comment_id'      => (int)$single_comment->comment_id,
                'comment_text'    => $single_comment->comment_text,
                'comment_date'    => display_time(strtotime(
                                        $single_comment->comment_date)),
                'comment_replies' => (int)$single_comment->comment_replies,
                'user_data'       => a_fetch_user($single_comment->user_id, 
                                                  $single_comment->user_nick, 
                                                  $single_comment->user_level),
                'avatar'          => a_get_user_avatar($single_comment, 's'),
            );
        }
    }
    
    // atgriežamais saturs
    $json_page = array(
        'content'   => $about_news,
        'comments'  => $comments
    );
}




// visos pārējos gadījumos atgriezīs sarakstu ar jaunākajiem rakstiem
else {    
    $json_page = a_get_news();
}

/*
    TODO:
    
        - rakstu rediģēšana (vai ļaut?)
        - komentāru rediģēšana
        - rakstu komentāru slēgšana
        - rakstu vērtēšana
        - komentāru vērtēšana
        - daudz kas cits
        - ...
*/