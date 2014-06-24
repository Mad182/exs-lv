<?php
/**
 *  RuneScape apakšprojektā izmantotās funkcijas
 */

/**
 *  Testēšanas nolūkiem, lai pārbaudītu Models (un ne tikai) rezultātus kā json
 *  @see https://github.com/callumlocke/json-formatter
 */
function as_json($content) {
    header('Content-Type: application/json');
    
    if (is_string($content) || is_integer($content)) {
        $content = array($content);
    }
    
    echo json_encode($content);
    exit;
}


/**
 *  Atgriež objektu ar template faila saturu
 *
 *  @param string $file     faila nosaukums
 *  @param bool $add_path   vai pievienot pilno ceļu uz atvērto moduli
 *  @return bool            "false", ja fails neeksistē
 *  @return TemplatePower   template objekts
 */
function get_template($file = '', $add_path = true) {
    global $category;
    
    if ($file == '') {
        return false;
    }

    if ($add_path) {
        $file = CORE_PATH.'/modules/'.$category->module.'/'.$file;
    }
    
    if (!file_exists($file)) {
        return false;
    }

    $tpl = new TemplatePower($file);
    $tpl->prepare();
    
    return $tpl;
}


/**
 *  RuneScape.com RSS feed lasītājs
 *
 *  (Tiek izsaukta RuneScape apakšprojekta sākumlapā.)
 *
 *  Nolasa jaunāko ziņu virsrakstus un no tiem izveido HTML,
 *  kuru saglabā cache failā un vēlāk izvada kā saturu sākumlapā.
 *
 *  Ja rakstiem ir pievienota arī logo adrese,
 *  logo tiek saglabāts lokāli un tā izmērs pielāgots vajadzībām.
 *
 *  @param  bool    norāde, vai atjaunot cache
 */
function get_runescape_news($force = false) {
    global $m, $db, $auth, $rsbot_id, $lang;

    $list_news = 12;     // rakstu skaits, cik rādīt sarakstā

    // memcache glabā tikai laiku, kad jaunumi pēdējoreiz saglabāti,
    // citādi tiek izmantots .html cache fails
    if ($force || $m->get('runescape-news') === false) {

        // atjaunosies reizi 10 minūtēs;
        // jāuzstāda uzreiz, lai, ieilgstot parsēšanai,
        // vairāki lietotāji neizsauktu atjaunošanos vienlaicīgi
        $m->set('runescape-news', time(), false, 600);

        // nolasa jaunākās ziņas no runescape.com
        $news = curl_get('http://services.runescape.com/m=news/latest_news.rss');

        // ja izdevās ielasīt saturu no RSS, iet cauri visiem rakstiem un
        // salīdzina ar ierakstiem datubāzē
        if ($news !== false) {
            $data = new SimpleXmlElement($news);
            
            foreach ($data->channel->item as $single) {                

                // pārbaude, vai datubāzē šāds jaunums jau neeksistē
                $hash_val = sanitize(md5($single->pubDate.$single->title));
                $val = $db->get_row("
                    SELECT
                        `rs_news`.`news_title`
                    FROM `rs_news`
                        JOIN `miniblog` ON `rs_news`.`mb_id` = `miniblog`.`id`
                    WHERE 
                        `rs_news`.`hash_value` = '$hash_val'
                ");
                
                // ieraksta datubāzē vēl nav; tāds tiek izveidots
                if (!$val) {         

                    // minibloga saturs
                    $mb_text  = '<p class="rsmb-title">'.$single->title.'</p>';
                    $mb_text .= '<p class="rsmb-text">'.$single->description.'<br>';
                    $mb_text .= 'Oriģinālraksts: <a href="'.$single->link.'" rel="nofollow" target="_blank">';
                    $mb_text .= $single->link.'</a></p>';
                    $mb_text .= '<p class="rsmb-fade">Šis ieraksts ir izveidots automātiski šī jaunuma apspriešanai.</p>';

                    // izveido jaunu miniblogu
                    $insert = $db->query("INSERT INTO `miniblog`
                        (author, date, text, lang, bump)
                        VALUES (
                            ".(int)$rsbot_id.",
                            '".date("Y-m-d H:i:s", time())."',
                            '".sanitize($mb_text)."',
                            ".(int)$lang.",
                            '".time()."'
                        )
                    ");

                    // izveido ierakstu `rs_news` tabulā
                    if ($insert) {

                        $mb_id = $db->insert_id;
                        $has_image = (isset($single->enclosure['url'])) ? 1 : 0;

                        // ne visiem rakstiem ir pieejams logo
                        if ($has_image) {

                            // attēls tiek saglabāts uz lokālā servera
                            $img_path = CORE_PATH.'/bildes/runescape/news/';
                            $save = save_rs_image(
                                $single->enclosure['url'], // source_path
                                $img_path, // target_path
                                'news-'.$mb_id.'.jpg' // img_title
                            );

                            // ja lokāli attēlu saglabāt neizdodas, tā arī jāatzīmē,
                            // lai pēcāk varētu rādīt fallback attēlus
                            if ($save === false)
                                $has_image = 0;
                        }

                        $news_insert = $db->query("INSERT INTO `rs_news`
                            (hash_value, mb_id, news_title, news_category, news_description, news_date, news_link, has_image, created_by, created_at)
                            VALUES (
                                '".sanitize($hash_val)."',
                                '".$mb_id."',
                                '".sanitize(trim(strip_tags($single->title)))."',
                                '".sanitize(trim(strip_tags($single->category)))."',
                                '".sanitize(trim(strip_tags($single->description)))."',
                                '".sanitize(trim(strip_tags($single->pubDate)))."',
                                '".sanitize(trim(strip_tags($single->link)))."',
                                $has_image,
                                $rsbot_id,
                                '".time()."'
                            )
                        ");                        
                    }
                }
                // `rs_news` tabulā ierakstam nav virsraksta; atjaunosies
                elseif (empty($val->news_title)) {
                    $db->query("
                        UPDATE `rs_news` 
                        SET 
                            `news_title`        = '".sanitize(trim(strip_tags($single->title)))."', 
                            `news_category`     = '".sanitize(trim(strip_tags($single->category)))."', 
                            `news_description`  = '".sanitize(trim(strip_tags($single->description)))."', 
                            `news_date`         = '".sanitize(trim(strip_tags($single->pubDate)))."', 
                            `news_link`         = '".sanitize(trim(strip_tags($single->link)))."'
                        WHERE 
                            `hash_value` = '".sanitize($hash_val)."' 
                        LIMIT 1
                    ");
                }
            }            
        }
        
        
        // no datubāzes atlasa jaunākos x rakstus,
        // no tiem uzģenerē HTML saturu un to saglabā cache failā
        $all_news = $db->get_results("
            SELECT
                `rs_news`.`id`,
                `rs_news`.`mb_id`,
                `rs_news`.`has_image`,
                `rs_news`.`news_title`          AS `title`,
                `rs_news`.`news_description`    AS `description`,
                `rs_news`.`news_date`           AS `date`,
                `rs_news`.`news_category`       AS `category`,
                `rs_news`.`news_link`           AS `link`,
                `miniblog`.`text`
            FROM `rs_news`
                JOIN `miniblog` ON `rs_news`.`mb_id` = `miniblog`.`id`
            WHERE
                `rs_news`.`deleted_by` = 0
            ORDER BY `rs_news`.`id` DESC
            LIMIT 0, $list_news
        ");
        
        $out = '<ul class="official-news">';
        if ( $all_news ) {
           
            foreach ($all_news as $single_news) {

                // bultiņa sānā uz miniblogu
                $mb_arrow  = '<p class="goto-mb">';
                $mb_arrow .= '<a href="/say/'.$rsbot_id.'/'.$single_news->mb_id.'-'.mb_get_strid($single_news->text, $single_news->mb_id). '">';
                $mb_arrow .= '&rsaquo;&rsaquo;</a></p>';
                
                // attēls
                $image = '';
                if ($single_news->has_image &&
                    file_exists(CORE_PATH . '/bildes/runescape/news/thumb-news-'.$single_news->mb_id.'.jpg') ) {
                    $image = '<img src="/bildes/runescape/news/thumb-news-'.$single_news->mb_id.'.jpg" title="'.$single_news->title.'" alt="Logo">';
                } else {
                    $image = '<img src="/bildes/runescape/fallback/'.get_fallback_image($single_news->category).'" title="'.$single_news->title.'" alt="Logo">';
                }
                
                $news_date          = date("d.m.Y", strtotime($single_news->date));
                $news_category      = translate_category((string)$single_news->category);
                $news_description   = (empty($image) ? textlimit($single_news->description, 95, '...') :
                                      textlimit($single_news->description, 70, '...'));
                 // rakstu, kuriem nav logo, laukumiem ir lielākas atstarpes
                $news_style = (empty($image) ? ' style="padding:0 10px 5px;width:90%"' : '');
                
                $out .= '<li>';
                $out .= '<a class="news-image" href="'.$single_news->link.'" rel="nofollow" target="_blank">'.$image.'</a>';
                $out .= '<p'.$news_style.'>';
                $out .= '<a class="news-title" href="'.$single_news->link.'" rel="nofollow" target="_blank">'.$single_news->title.'</a>';
                //$out .=
                $out .= $news_description;
                $out .= '<span class="news-date">'.$news_date.' &middot; '.$news_category.'</span>';
                $out .= '</p>' . $mb_arrow . '</li>';
                
            }
        }
        $out .= '<li class="news-link"><a href="http://services.runescape.com/m=news/" rel="nofollow" target="_blank">Skatīt senākus rakstus</a></li>';
        $out .= '</ul>';

        // izveido cache failu
        $cache_file = fopen(CORE_PATH . '/cache/runescape/official-news.html', 'w');
        fwrite($cache_file, $out);
        fclose($cache_file);
    }    

    // nolasa runescape ziņas no cache faila
    $output_file = fopen(CORE_PATH . '/cache/runescape/official-news.html', 'r');
    if ($output_file === false) {
        $output = ''; // ja nu tomēr kāds misēklis
    }
    else {
        $output = fread($output_file, filesize(CORE_PATH . '/cache/runescape/official-news.html'));
    }
    fclose($output_file);

    return $output;
}


/**
 *  Saglabā lokāli RuneScape ziņu raksta logo.
 *
 *  (Tiek izsaukta get_runescape_news() funkcijā.)
 *
 *  @param  string  vieta, no kurienes attēls jālejuplādē
 *  @param  string  vieta, kur attēls uz servera jāsaglabā
 *  @param  string  attēla nosaukums
 */
function save_rs_image($source_path, $target_path, $target_name = 'empty') {

    if ($target_name == 'empty' || empty($target_name))
        return false;

    // lejuplādē attēlu un saglabā lokāli
    $curl = curl_init($source_path);
    $file = fopen($target_path.$target_name, 'wb');

    curl_setopt($curl, CURLOPT_FILE, $file);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($curl, CURLOPT_TIMEOUT, 4);
    $exec = curl_exec($curl);
    curl_close($curl);
    fclose($file);

    // pārveido attēlu uz thumbnail izmēru
    require_once(CORE_PATH . '/includes/class.upload.php');

    $foo = new Upload($target_path.$target_name);
    if ($foo->uploaded) {
        $foo->file_new_name_body = 'thumb-'.str_replace(array('.png','.gif','.jpg'), '', $target_name);
        $foo->image_resize = true;
        $foo->image_convert = 'jpg';
        $foo->image_x = 125;
        $foo->image_ratio_y = true;
        $foo->allowed = array('image/*');
        $foo->Process(CORE_PATH . '/bildes/runescape/news/');
    }
    if ($foo->processed) {
        $foo->Clean();
    }

    if ($exec === false) {
        return false;
    }
    return true;
}


/**
 *  Atgriež fallback bildi RuneScape rakstiem.
 *
 *  (Tiek izsaukta get_runescape_news() funkcijā.)
 *
 *  @param string kategorijas nosaukums
 */
function get_fallback_image($string = '') {

    $categories = array(
        'Game Update News'          => 'fallback-gameupdates.jpg',
        'Future Updates'            => 'fallback-gameupdates.jpg',
        'Behind the Scenes News'    => 'fallback-behindthescenes.jpg',
        'Your Feedback'             => 'fallback-yourfeedback.jpg',
        'Website News'              => 'fallback-website.jpg',
        'Events'                    => 'fallback-community.jpg',
        'Technical News'            => 'fallback-technical.jpg',
        'Support'                   => 'fallback-support.jpg',
        'Customer Support News'     => 'fallback-yourfeedback.jpg',
        'Community'                 => 'fallback-community.jpg',
        'Solomon&apos;s Store'      => 'fallback-solomons.jpg',
        'Treasure Hunter'           => 'fallback-treasurehunter.jpg'
    );

    if ($string != '' && array_key_exists($string, $categories)) {
        return $categories[$string];
    }
    return 'fallback-technical.jpg';

}


/**
 *  Iztulko jaunumu kategoriju.
 *
 *  (Tiek izsaukta get_runescape_news() funkcijā.)
 *
 *  @param  string  pārtulkojamās kategorijas nosaukums
 */
function translate_category($string = '') {

    /*
        'Community'                 => 'Community',
        'Squeal Of Fortune'         => 'Squeal Of Fortune',
        'Treasure Hunter'           => 'Treasure Hunter',
        'Solomon&apos;s Store'      => 'Solomon&apos;s Store',
    */
    $categories = array(
        'Game Update News'          => 'Spēles jaunumi',
        'Future Updates'            => 'Gaidāmie uzlabojumi',
        'Behind the Scenes News'    => 'Behind the Scenes',
        'Your Feedback'             => 'Spēlētāju ieteikumi',
        'Website News'              => 'Mājaslapas jaunumi',
        'Events'                    => 'Pasākumi',
        'Technical News'            => 'Tehniskie jaunumi',
        'Support'                   => 'Atbalsts',
        'Customer Support News'     => 'Klientu atbalsta ziņas'
    );

    if ($string != '' && array_key_exists($string, $categories)) {
        return $categories[$string];
    }
    return $string;
}


/**
 *  Ar Memcache saglabā un atgriež kvestu statistikas datus.
 *
 *  Funkcija tiek izsaukta, caur MOD sadaļu rediģējot rs pamācības.
 *
 *  @param  bool  norāde, vai atjaunināt memcache glabāto saturu
 */
function get_quests_stats($force = false) {
	global $db, $m;
    global $cats_quests, $cat_p2p_quests, $cat_f2p_quests, $cat_miniquests;

	$stats = false;

	if ($force || ($stats = $m->get('quests-stats')) === false) {

		// izlaisto kvestu skaits noteiktos gados
		$stats[14] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `year` = 14 AND `cat_id` IN (".implode(',', $cats_quests).") ");
		$stats[13] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `year` = 13 AND `cat_id` IN (".implode(',', $cats_quests).") ");
		$stats[12] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `year` = 12 AND `cat_id` IN (".implode(',', $cats_quests).") ");
		$stats[11] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `year` = 11 AND `cat_id` IN (".implode(',', $cats_quests).") ");
		$stats[10] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `year` = 10 AND `cat_id` IN (".implode(',', $cats_quests).") ");
		$stats['older'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `year` NOT IN (12,11,10,9,8) AND `cat_id` IN (".implode(',', $cats_quests).") ");

		// kvestu tips
		$stats['p2p'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `members_only` = 1 AND `cat_id` = ".(int)$cat_p2p_quests);
		$stats['f2p'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `members_only` = 0 AND `cat_id` = ".(int)$cat_f2p_quests);
		$stats['miniquests'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `cat_id` = ".(int)$cat_miniquests);

		// kvestu sarežģītība
		$stats['special'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `difficulty` = 6 AND `cat_id` IN (".implode(',', $cats_quests).") ");
		$stats['grandmaster'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `difficulty` = 5 AND `cat_id` IN (".implode(',', $cats_quests).") ");
		$stats['master'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `difficulty` = 4 AND `cat_id` IN (".implode(',', $cats_quests).") ");
		$stats['experienced'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `difficulty` = 3 AND `cat_id` IN (".implode(',', $cats_quests).") ");
		$stats['intermediate'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `difficulty` = 2 AND `cat_id` IN (".implode(',', $cats_quests).") ");
		$stats['novice'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `difficulty` = 1 AND `cat_id` IN (".implode(',', $cats_quests).") ");

		$m->set('quests-stats', $stats, false, 3600);
	}

	return $stats;
}


/**
 *  RuneScape kategoriju saraksts
 *
 *  Funkcija tiek izsaukta sadaļās, kurās iespējams mainīt raksta kategoriju.
 *  (read, write, blogadmin)
 */
function get_rs_page_categories($current = null, $force = false) {
	global $db, $m, $lang, $debug;

	if ($debug || $force || !($cats = $m->get('cat_list_' . $lang))) {
		$cats = $db->get_results("SELECT `lang`,`parent`,`module`,`persona`,`isblog`,`isforum`,`id`,`title`,`status` FROM `cat` WHERE `module` IN('list','index','rshelp') AND `lang` = '$lang' ORDER BY `title` ASC");
		$m->set('cat_list_' . $lang, $cats, false, 900);
	}

	$return = array();
	foreach ($cats as $cat) {

        if ( in_array($cat->id, array(102, 4)) ) { // kvestu parent, prasmju parent
            continue;
        }
 
        // pāris sadaļu kategorijas redzamas vienmēr
        if ($cat->parent == 4) {
            $return['Prasmes'][$cat->id] = $cat->title;
        } elseif ($cat->parent == 1903) {
            $return['Arhīvs'][$cat->id] = $cat->title;
        } elseif ($cat->parent == 102) {
            $return['Kvesti'][$cat->id] = $cat->title;
        } elseif ($cat->module == 'rshelp') {
            $return['Runescape'][$cat->id] = $cat->title;
        } elseif ($cat->id == 599) { // rs ziņas
            $return['Main'][$cat->id] = $cat->title;
        }         
        // blogi, atkritne un vēl atsevišķas sadaļas redzamas tikai moderatoriem
        else if ( im_mod() || im_cat_mod($cat->id) ) {
            if (!$cat->isblog && $cat->isforum) {
                $return['Main'][$cat->id] = $cat->title . ' forums';
            } elseif ($cat->isblog) {
                $return['Blogi'][$cat->id] = $cat->title;
            } else {
                $return['Main'][$cat->id] = $cat->title;
            }
        }
	}
	return $return;
}
