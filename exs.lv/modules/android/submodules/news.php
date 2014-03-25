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
            `pages`.`title`     AS `page_title`, 
            `pages`.`text`      AS `page_text`,
            `users`.`id`        AS `user_id`,
            `users`.`nick`      AS `user_nick`
        FROM `pages` 
            JOIN `users` ON `pages`.`author` = `users`.`id`
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
            `users`.`nick`          AS `user_nick`
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
            'page_title'    => $news_data->page_title,
            'page_text'     => $news_data->page_text,
            'user_id'       => $news_data->user_id,
            'user_nick'     => $news_data->user_nick
        );
    }
    
    // nebūtu jēdzīgi rādīt komentārus, ja neatrastu rakstu,
    // tāpēc arī raksta pārbaude
    if ($news_data && $page_comments) {   
        foreach ($page_comments as $single_comment) {
            $comments[] = array(
                'comment_id'        => $single_comment->comment_id,
                'comment_text'      => $single_comment->comment_text,
                'comment_date'      => $single_comment->comment_date,
                'comment_replies'   => $single_comment->comment_replies,
                'user_id'           => $single_comment->user_id,
                'user_nick'         => $single_comment->user_nick
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
    $json_page = get_news();
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