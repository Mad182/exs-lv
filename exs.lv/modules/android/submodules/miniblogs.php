<?php
/**
 *  Android miniblogu apakšmodulis
 *
 *  Kaut ko šeit darīs saistībā ar miniblogiem.
 */

// pa tiešo šeit nebūs nekādas skatīšanās
!isset($sub_include) and die('Error loading page!');




// izvēlēts konkrēts miniblogs (der arī no grupām);
// var1 - minibloga ID
if (isset($_GET['var1'])) {

    $parent_mb_id = (int)$_GET['var1'];

    // atlasa minibloga informāciju
    $record = $db->get_row("
        SELECT 
            `miniblog`.*,
            `users`.`id`        AS `user_id`,
            `users`.`nick`      AS `user_nick`,
            `users`.`level`     AS `user_level`,
            `users`.`deleted`   AS `user_deleted`,
            `users`.`avatar`    AS `avatar`,
            `users`.`av_alt`    AS `av_alt`
        FROM `miniblog` 
            JOIN `users` ON `miniblog`.`author` = `users`.`id`
        WHERE 
            `miniblog`.`id`        = " . $parent_mb_id . " AND
            `miniblog`.`removed`   = '0' AND 
            `miniblog`.`parent`    = '0' AND 
            `miniblog`.`lang`      = '$android_lang' 
        ORDER BY `miniblog`.`bump` DESC
    ");
    
    if ($record) {
            
        // aizstāj/neaizstāj dzēstu autora lietotājvārdu
        if ( ! $record->user_deleted ) {
            $author = $record->user_nick;
        } else {
            $author = 'dzēsts';
        }
        
        // paredzēts avataru funkcijai
        $record->av_alt = 1;       
        
        // galvenā minibloga informācija
        $array_miniblog = array(
            'mb-id'         => $record->id,
            'mb-text'       => strip_tags(add_smile($record->text), '<img><p><strong><b><i><em>'),
            'mb-date'       => display_time(strtotime($record->date)),
            'mb-author'     => $author,
            'mb-author-id'  => $record->user_id,
            'avatar'        => get_user_avatar($record, 's')
        );
        
        // galvenā minibloga komentāri
        $array_comments = array();
        
        if ($record->posts) {

            // atlasa visus ierakstus, kuriem parent ir galvenais miniblogs
            $responses = $db->get_results("
                SELECT
                    `miniblog`.`id`         AS `mb_id`,
                    `miniblog`.`text`       AS `mb_text`,
                    `miniblog`.`date`       AS `mb_date`,
                    `miniblog`.`groupid`    AS `mb_groupid`,
                    `miniblog`.`reply_to`   AS `mb_reply_to`,
                    `miniblog`.`removed`    AS `mb_removed`,
                    `users`.`id`            AS `user_id`,
                    `users`.`nick`          AS `user_nick`,
                    `users`.`deleted`       AS `user_deleted`,
                    `users`.`avatar`        AS `avatar`,
                    `users`.`av_alt`        AS `av_alt`
                FROM `miniblog`
                    JOIN `users` ON `miniblog`.`author` = `users`.`id`
                WHERE
                    `miniblog`.`parent`     = '" . $record->id . "' AND
                    `miniblog`.`type`       = 'miniblog'
                ORDER BY `miniblog`.`id` ASC"
            );

            // ja miniblogam ir komentāri, tos visus ievieto masīvā
            if ($responses) {
            
                $json = array();
                
                foreach ($responses as $response) {
                
                    // aizstāj dzēstu autora lietotājvārdu
                    if ( $response->user_deleted ) {
                        $response->user_nick = 'dzēsts';
                    }
                    
                    // dzēstiem ierakstiem aizstāj saturu ar kaut ko citu
                    if ( $response->mb_removed ) {
                        $response->mb_text = 'Dzēsts!';
                    }
                    // smaidiņi, embbeds u.c. saturs;
                    // būtībā ir jēga, lai pievieno smaidiņu adreses,
                    // kuras lietotnē varētu ielādēt
                    else {
                        $response->mb_text = strip_tags(add_smile($response->mb_text, 0, 0, 1), '<img><p><strong><b><i><em>');
                    }
                    
                    $response->mb_date  = display_time(strtotime($response->mb_date));
                    $response->avatar   = get_user_avatar($response, 's');
                
                    $json[$response->mb_reply_to][] = $response;
                }
                
                // šis tiek pievienots atgriežamajam saturam
                $array_comments = $json;
            }
        }  
        
        
        // atgriežamais rezultāts
        $json_page = array(
            'content'   => $array_miniblog,
            'comments'  => $array_comments
        );
        
    }
}


// jaunāko miniblogu saraksts
else {

    $json_page = fetch_miniblogs();

}
/*
    TODO:

        - minibloga komentāru atjaunošana
        - komentāra pievienošana
        - vērtēšana
        - rediģēšana
        - cits
        - cits
        - ...
*/