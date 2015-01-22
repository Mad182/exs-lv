<?php
/**
 *  Android miniblogu apakšmodulis.
 *
 *  Apstrādā pieprasījumus saistībā ar darbībām miniblogos, tai skaitā
 *  jauna minibloga pievienošanu vai esoša komentēšanu, vērtēšanu u.c.
 */

// nebūs iespējams skatīt failu pa tiešo
!isset($sub_include) and die('Error loading page!');

// piegriezies rakstīt isset pārbaudi un neērto $_GET
$var1 = (!empty($_GET['var1'])) ? $_GET['var1'] : '';
$var2 = (!empty($_GET['var2'])) ? $_GET['var2'] : '';
$var3 = (!empty($_GET['var3'])) ? $_GET['var3'] : '';


/**
 *  Atgriezīs jaunāko miniblogu sarakstu.
 */
if ($var1 === 'getlist') {

    $json_page = a_fetch_miniblogs();

/**
 *  Iesniegti jauna minibloga pievienošanas dati.
 */
} else if ($var1 === 'new') {

    // dažādas drošības pārbaudes
    if (!isset($_GET['xsrf']) || !a_check_xsrf($_GET['xsrf'])) {
        a_error('Hacking around?');
        a_log('Pievienojot jaunu miniblogu, nenorādīja XSRF atslēgu');
    } else if (!isset($_POST['mb_content'])) {
        a_error('Pievienošanas kļūda');
    } else if (empty($_POST['mb_content'])) {
        a_error('Nevar pievienot tukšu miniblogu');
        
    // plūdu kontrole
    } else if (isset($_SESSION['antiflood']) && 
               $_SESSION['antiflood'] >= time() - 15) {
        a_error('Pārāk bieža pievienošana, brīdi uzgaidi');
    
    // minibloga pievienošana
    } else {
        $_SESSION['antiflood'] = time();
        
        $mb_content = post2db($_POST['mb_content']);
        
        // vai miniblogs ir slēpjams no botiem un crawleriem?
        $is_private = (isset($_POST['private'])) ? 1 : 0;
        
        // ja viss kārtībā, drīkst pievienot
        $insert_id = post_mb(array(
            'text' => $mb_content,
            'private' => $is_private,
            'lang' => $android_lang
        ));
        
        // vēl tik jāpievieno pāris notifikācijas...
        $inserted_mb = $db->get_row('
            SELECT `id`, `text` FROM `miniblog` WHERE `id` = '.(int)$insert_id
        );
        $mb_title = mb_get_title($inserted_mb->text);
		$mb_strid = mb_get_strid($mb_title, $inserted_mb->id);
        
        push('Izveidoja <a href="/say/'.$auth->id.'/'.$inserted_mb->id.'-'.$mb_strid.'">minibloga ierakstu &quot;'.textlimit(hide_spoilers($mb_title), 32, '...') . '&quot;</a>');
        
        // pieminēto lietotāju lietotājvārdu apstrāde
        $inserted_mb->text = mention($inserted_mb->text, 
                                     '/say/'.$auth->id.'/'.$inserted_mb->id.'-'.$mb_strid,
                                     'mb', $inserted_mb->id);
        $db->update('miniblog', $inserted_mb->id, array(
            'text' => sanitize($inserted_mb->text)
        ));
        
        a_message('Miniblogs pievienots');
        a_append(array('miniblog_id' => $insert_id));
    }

/**
 *  Izvēlēts konkrēts miniblogs (der arī tie no grupām).
 *  $var1 - minibloga ID
 */
} else if (!empty($var1)) {

    $parent_mb_id = (int)$var1;

    // atlasīs minibloga informāciju, pie reizes arī pārbaudot,
    // vai tas ir kādā no grupām un vai lietotājam ir tam piekļuve
    $miniblog = $db->get_row("
        SELECT 
            `miniblog`.*,
            IFNULL(`clans`.`id`, 0) AS `clan_id`,
            `clans`.`title` AS `clan_title`,
            `clans`.`avatar` AS `clan_avatar`,
            `clans`.`public` AS `clan_public`,
            `clans`.`owner` AS `clan_owner`,
            IFNULL(`clans_members`.`id`, 0) AS `is_member`
        FROM `miniblog`
            LEFT JOIN `clans` ON `miniblog`.`groupid` = `clans`.`id`
            LEFT JOIN `clans_members` ON (
                `clans`.`id` = `clans_members`.`clan` AND
                `clans_members`.`user` = ".$auth->id." AND
                `clans_members`.`approve` = 1
            )
        WHERE 
            `miniblog`.`id`        = ".$parent_mb_id." AND
            `miniblog`.`removed`   = 0 AND 
            `miniblog`.`parent`    = 0 AND 
            `miniblog`.`lang`      = ".(int)$android_lang."
        ORDER BY `miniblog`.`bump` DESC
    ");
    
    if (!$miniblog) {
        a_error('Miniblogs nav atrasts');
    } else {
    
        // miniblogs var būt un var nebūt no kādas grupas
        $clan_id = 0;
        $clan_title = $clan_av_url = '';
        if (!empty($miniblog->clan_id)) {
            $clan_id = (int)$miniblog->clan_id;
            $clan_title = $miniblog->clan_title;
            $clan_av_url = $img_server.'/userpic/large/'.$miniblog->clan_avatar;
        }
        
        // liedz darboties miniblogā, kas ir slēgtā grupā,
        // kurai lietotājam nav piekļuves
        if ($clan_id !== 0 && $miniblog->is_member === 0) {
            a_error('Nav pieejas');
            a_log('Mēģināja darboties miniblogā, kuram nav pieejas');           
        } else {    
    
            $author = get_user($miniblog->author);
            if ($author->deleted) {
                $author->nick = 'dzēsts';
            }
        
            // minibloga vērtēšana
            if (in_array($var2, array('plus', 'minus'))) {
            
                // miniblogā esoša komentāra vērtēšana
                if (!empty($var3)) {
                    if ($var2 === 'plus') {
                        a_rate_comment((int)$var3, 'miniblog', true);
                    } else {
                        a_rate_comment((int)$var3, 'miniblog', false);
                    }

                // galvenā minibloga vērtēšana
                } else if ($var2 == 'plus') {
                    a_rate_comment($miniblog->id, 'miniblog', true);
                } else if ($var2 == 'minus') {
                    a_rate_comment($miniblog->id, 'miniblog', false);
                }
            
            // atbildes pievienošana
            } else if (!empty($_POST['comment'])) {
                if (isset($_POST['comment_id'])) {
                    a_add_mb_comment(array('id' => $author->id, 
                                           'nick' => $author->nick), true);
                } else {
                    a_error('Kļūdaini komentāra dati');
                }
            
            // atgriež minibloga saturu
            } else {          
                
                // paredzēts avataru funkcijai
                $miniblog->av_alt = 1;
                
                // galvenā minibloga informācija
                $array_miniblog = array(
                    'id' => (int)$miniblog->id,
                    'text' => strip_tags(add_smile($miniblog->text), '<img><p><strong><b><i><em>'),
                    'date' => display_time(strtotime($miniblog->date)),
                    'author' => a_fetch_user($author->id, $author->nick, $author->level),
                    'author_av_url' => a_get_user_avatar($author, 's'),
                    'vote' => (int)$miniblog->vote_value,
                    'is_closed' => (bool)$miniblog->closed,
                    'group_id' => $clan_id,
                    'group_title' => $clan_title,
                    'group_av_url' => $clan_av_url
                );

                $json = array();
                
                if ($miniblog->posts) {

                    // atlasa visus ierakstus, kuriem parent ir galvenais miniblogs
                    $responses = $db->get_results("
                        SELECT
                            `id`, `text`, `author`, `date`, `groupid`, `reply_to`,
                            `removed`, `vote_value` AS `vote`
                        FROM `miniblog`
                        WHERE
                            `parent` = " . (int)$miniblog->id . " AND
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

                            // smaidiņi, embbeds u.c. saturs;
                            // būtībā ir jēga, lai pievieno smaidiņu un attēlu adreses,
                            // kuras lietotnē varētu ielādēt
                            } else {
                                $response->text = strip_tags(add_smile($response->text, 0, 0, 1), '<img><p><strong><b><i><em>');
                            }
                            
                            $response->date = display_time(strtotime($response->date));
                            $response->avatar = a_get_user_avatar($author, 's');
                        
                            $json[$response->reply_to][] = $response;
                        }  
                    }
                }  
                
                // ja miniblogā ir tikai viens komentārs, no objekta tas tiek pārveidots uz masīvu;
                // tā kā lietotne vienmēr gaida objektu, jāpievieno kāds papildobjekts,
                // kas lietotnē netiks uztverts kā komentārs
                $json[-1][] = 'safe';

                $json_page = array(
                    'content'   => $array_miniblog,
                    'comments'  => $json
                );
            }
        }
    }

} else {
    a_error('Kļūdaini veikts pieprasījums');
}
