<?php
/**
 *  Android miniblogu apakšmodulis
 */

// pa tiešo šeit nebūs nekādas skatīšanās
!isset($sub_include) and die('Error loading page!');




// izvēlēts konkrēts miniblogs (der arī no grupām);
// var1 - minibloga ID
// TODO: pārbaudīt, vai attiecīgajam grupas mb lietotājam ir piekļuve
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
    
        // minibloga vērtēšana
        if (isset($_GET['var2']) && in_array($_GET['var2'], array('plus', 'minus'))) {
        
            // miniblogā esoša komentāra vērtēšana
            if (isset($_GET['var3'])) {
                if ($_GET['var2'] == 'plus') {
                    a_rate_comment((int)$_GET['var3'], 'miniblog', true);
                } else {
                    a_rate_comment((int)$_GET['var3'], 'miniblog', false);
                }
            }
            // galvenā minibloga vērtēšana
            else if ($_GET['var2'] == 'plus') {
                a_rate_comment($record->id, 'miniblog', true);
            }
            else if ($_GET['var2'] == 'minus') {
                a_rate_comment($record->id, 'miniblog', false);
            }
        }
        
        // atbildes pievienošana
        else if (isset($_POST['comment'])) {
            if (!empty($_POST['comment']) && isset($_POST['comment_id'])) {
                a_add_mb_comment(array('id' => $record->user_id, 'nick' => $record->user_nick), true);
            } else {
                a_error('Kļūdaini komentāra dati!');
            }
        }
        
        // atgriež minibloga saturu
        else {

            if ($record->user_deleted) {
                $record->user_nick = 'dzēsts';
            }
            
            // paredzēts avataru funkcijai
            $record->av_alt = 1;       
            
            // galvenā minibloga informācija
            $array_miniblog = array(
                'mb-id'         => $record->id,
                'mb-text'       => strip_tags(add_smile($record->text), '<img><p><strong><b><i><em>'),
                'mb-date'       => display_time(strtotime($record->date)),
                'mb-author'     => a_fetch_user($record->user_id, $record->user_nick, $record->user_level),
                'mb-vote'       => $record->vote_value,
                'avatar'        => a_get_user_avatar($record, 's'),
                'safeguard'     => substr(md5($record->id . $remote_salt . $auth->id), 0, 5)
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
                        `miniblog`.`vote_value` AS `mb_vote`,
                        `users`.`id`            AS `user_id`,
                        `users`.`nick`          AS `user_nick`,
                        `users`.`level`         AS `user_level`,
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
                        if ($response->user_deleted) {
                            $response->user_nick = 'dzēsts';
                        }
                        $response->user_data = a_fetch_user($response->user_id, $response->user_nick, $response->user_level);
                        
                        // drošības atslēga, ko lietotnē pievienos adreses galā,
                        // lai novērstu xsrf-tipa uzbrukumus, ja android lapu skatās caur pārlūku
                        $response->safeguard = substr(md5($response->mb_id . $remote_salt . $auth->id), 0, 5);
                        
                        // dzēstiem ierakstiem aizstāj saturu ar kaut ko citu
                        if ($response->mb_removed) {
                            $response->mb_text = '<em>Ieraksts dzēsts!</em>';
                        }
                        // smaidiņi, embbeds u.c. saturs;
                        // būtībā ir jēga, lai pievieno smaidiņu un attēlu adreses,
                        // kuras lietotnē varētu ielādēt
                        else {
                            $response->mb_text = strip_tags(add_smile($response->mb_text, 0, 0, 1), '<img><p><strong><b><i><em>');
                        }
                        
                        $response->mb_date  = display_time(strtotime($response->mb_date));
                        $response->avatar   = a_get_user_avatar($response, 's');
                    
                        $json[$response->mb_reply_to][] = $response;
                    }                
                    
                    // ja miniblogā ir tikai viens komentārs, no objekta tas tiek pārveidots uz masīvu;
                    // tā kā lietotne vienmēr gaida objektu, jāpievieno kāds papildobjekts,
                    // kas lietotnē netiks uztverts kā komentārs
                    $json[-1][] = 'safeguard';
                    
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
    } else {
        a_error('Miniblogs nav atrasts');
    }
}


// jaunāko miniblogu saraksts
else {

    $json_page = a_fetch_miniblogs();

}
