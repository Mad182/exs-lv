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
        SELECT `title`, `text` FROM `pages` 
        WHERE 
            `id` = '".(int)$_GET['var1']."' AND
            (`lang` = '$android_lang' OR `lang` = 0)
    ");
    // raksta komentāri
    $page_comments = $db->get_results("
        SELECT * FROM comments 
        WHERE 
            `pid` = '" . (int)$_GET['var1'] . "' AND 
            `parent` = 0 AND `removed` = 0 
        ORDER BY `id` ASC
    ");
    
    // masīvi, kas tiks pievienoti $json_page 
    $about_news = array();
    $comments   = array();
    
    // informācija par rakstu atrasta
    if ($news_data) {
        $about_news = array(
            'title' => $news_data->title,
            'text'  => $news_data->text
        );
    }
    
    // nebūtu jēdzīgi rādīt komentārus, ja neatrastu rakstu,
    // tāpēc arī raksta pārbaude
    if ($news_data && $page_comments) {   
        foreach ($page_comments as $single_comment) {
            $comments[] = array(
                'id'        => $single_comment->id,
                'author'    => $single_comment->author,
                'text'      => $single_comment->text,
                'date'      => $single_comment->date,
                'replies'   => $single_comment->replies
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