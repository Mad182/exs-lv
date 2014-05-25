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
    $miniblog = $db->get_row("
        SELECT 
            `miniblog`.*
        FROM `miniblog`
        WHERE 
            `id`        = " . $parent_mb_id . " AND
            `removed`   = '0' AND 
            `parent`    = '0' AND 
            `lang`      = '$android_lang' 
        ORDER BY `bump` DESC
    ");
    
    if ($miniblog) {
    
        // minibloga vērtēšana
        if (isset($_GET['var2']) && 
            in_array($_GET['var2'], array('plus', 'minus'))) {
        
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
                a_rate_comment($miniblog->id, 'miniblog', true);
            }
            else if ($_GET['var2'] == 'minus') {
                a_rate_comment($miniblog->id, 'miniblog', false);
            }
        }
        
        // atbildes pievienošana
        else if (isset($_POST['comment'])) {
            if (!empty($_POST['comment']) && isset($_POST['comment_id'])) {
                a_add_mb_comment(array('id' => $miniblog->user_id, 
                                       'nick' => $miniblog->user_nick),
                                 true);
            } else {
                a_error('Kļūdaini komentāra dati!');
            }
        }
        
        // atgriež minibloga saturu
        else {          
            
            // paredzēts avataru funkcijai
            $miniblog->av_alt = 1;
            
            $author = get_user($miniblog->author);
            if ($author->deleted) {
                $author->nick = 'dzēsts';
            }            
            $key = substr(md5($miniblog->id . $remote_salt . $auth->id),
                                  0, 5);
            
            // galvenā minibloga informācija
            $array_miniblog = array(
                'id'        => (int)$miniblog->id,
                'text'      => strip_tags(add_smile($miniblog->text), 
                                          '<img><p><strong><b><i><em>'),
                'date'      => display_time(strtotime($miniblog->date)),
                'author'    => a_fetch_user($author->id, 
                                            $author->nick, 
                                            $author->level),
                'vote'      => (int)$miniblog->vote_value,
                'avatar'    => a_get_user_avatar($author, 's'),
                'closed'    => (bool)$miniblog->closed,
                'safe'      => substr(md5($miniblog->id . $remote_salt . $auth->id), 0, 5)
            );

            // galvenā minibloga komentāri
            $array_comments = array();
            $json = array();
            
            if ($miniblog->posts) {

                // atlasa visus ierakstus, kuriem parent ir galvenais miniblogs
                $responses = $db->get_results("
                    SELECT
                        `id`, `text`, `author`, `date`, `groupid`, `reply_to`,
                        `removed`, `vote_value` AS `vote`
                    FROM `miniblog`
                    WHERE
                        `parent` = '" . $miniblog->id . "' AND
                        `type`   = 'miniblog'
                    ORDER BY `id` ASC
                ");

                // ja miniblogam ir komentāri, tos visus ievieto masīvā
                if ($responses) {
                    
                    foreach ($responses as $response) {
                    
                        $author = get_user($response->author);
                    
                        // aizstāj dzēstu autora lietotājvārdu
                        if ($author->deleted) {
                            $response->nick = 'dzēsts';
                        }
                        $response->author = a_fetch_user($author->id, $author->nick, $author->level);
                        
                        // dzēstiem ierakstiem aizstāj saturu ar kaut ko citu
                        if ($response->removed) {
                            $response->text = '<em>Ieraksts dzēsts!</em>';
                        }
                        // smaidiņi, embbeds u.c. saturs;
                        // būtībā ir jēga, lai pievieno smaidiņu un attēlu adreses,
                        // kuras lietotnē varētu ielādēt
                        else {
                            $response->text = strip_tags(add_smile($response->text, 0, 0, 1), '<img><p><strong><b><i><em>');
                        }
                        
                        $response->date     = display_time(strtotime($response->date));
                        $response->avatar   = a_get_user_avatar($author, 's');
                        
                        // drošības atslēga, ko lietotnē pievienos adreses galā,
                        // lai novērstu xsrf-tipa uzbrukumus, ja android lapu skatās caur pārlūku
                        $response->safe = substr(md5($response->id . $remote_salt . $auth->id), 0, 5);
                    
                        $json[$response->reply_to][] = $response;
                    }  
                }
            }  
            
            // ja miniblogā ir tikai viens komentārs, no objekta tas tiek pārveidots uz masīvu;
            // tā kā lietotne vienmēr gaida objektu, jāpievieno kāds papildobjekts,
            // kas lietotnē netiks uztverts kā komentārs
            $json[-1][] = 'safe';
            
            // šis tiek pievienots atgriežamajam saturam
            $array_comments = $json;
                    
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
