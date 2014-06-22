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
 *  Funkcija lappušu saraksta atgriešanai.
 *
 *  (Šobrīd nav izmantota.)
 *
 *  Atgriež sarakstu ar lapām tādā veidā, ka atvērtā lapa ir pa vidu, bet
 *  katrā pusē tai ir norādītais skaits iepriekšējo/nākamo lappušu.
 *
 *  Atkarībā no tā, kura lappuse ir atvērta,
 *  izdrukā arī bultiņas un pirmo/pēdējo lapu.
 *
 *  @param  int     kopējais lappušu skaits
 *  @param  int     atvērtās lappuses numurs
 *  @param  string  teksts, kāds adresē rakstāms pirms lappuses numura
 *  @param  string  klases nosaukums, kādu piemērot "ul" elementam
 *  @param  int     skaits, cik lappuses rādīt atvērtās lapas kreisajā pusē
 *  @param  int     skaits, cik lappuses rādīt atvērtās lapas labajā pusē
 */
function pagelist($page_count = 1, $current_page = 1, $addr_prefix = '', $spec_class = '', $page_left = 0, $page_right = 0) {

	// cik daudz lappušu rādīt katrā pašreizējās lappuses sānā
	$max_left   = ((int) $page_left < 1) ? 3 : (int) $page_left;
	$max_right  = ((int) $page_right < 1) ? 3 : (int) $page_right;

	$pages_to_left  = ($current_page - $max_left < 1) ? 1 : $current_page - $max_left;
	$pages_to_right = ($current_page + $max_right > $page_count) ? $page_count : $current_page + $max_right;

	$view = '<ul class="pagelist '.$spec_class.'">';

	// saraksts tiek atgriezts tikai tad, ja esošās lapas nr ir lapu skaita robežās;
	// pretējā gadījumā tikai pirmā lappuse
	if ($current_page <= $page_count && $current_page > 0) {

		// pirmā lappuse
		if ($current_page > $max_left + 1)
			$view .= '<li><a href="' . $addr_prefix . '1">1</a></li>';
		// bultiņa pa kreisi
		if ($current_page > 1)
			$view .= '<li class="arrows">
				<a href="' . $addr_prefix . ($current_page - 1) . '">&laquo;</a>
			</li>';
		// vidusdaļa ar kreisās puses lappusēm, atvērto lapu, labās puses lappusēm
		for ($i = $pages_to_left; $i <= $pages_to_right; $i++) {
			$view .= ($i == $current_page) ?
					'<li class="current-page"><a href="javascript:return false;">' . $i . '</a></li>' :
					'<li><a href="' . $addr_prefix . $i . '">' . $i . '</a></li>';
		}
		// bultiņa pa labi
		if ($current_page < $page_count)
			$view .= '<li class="arrows">
				<a href="' . $addr_prefix . ($current_page + 1) . '">&raquo;</a>
			</li>';
		// pēdējā lappuse
		if ($current_page < $page_count - $max_right)
			$view .= '<li><a href="' . $addr_prefix . $page_count . '">' . $page_count . '</a></li>';

		return $view . '</ul>';
	}
	return $view . '<li><a href="' . $addr_prefix . '1' . '">1</a></li></ul>';
}



/**
 *  Sinhronizē RuneScape rakstu informāciju
 *  starp `pages` un `rs_pages` tabulām.
 *
 *  Funkcija tiek izsaukta pie raksta atjaunošanas
 *  vai dzēšanas RuneScape apakšprojektā.
 *
 *  @param  bool  norāda, vai uz ekrāna drukāt info par saistītajiem rakstiem
 */
function update_rspages($update = true, $print = false) {
	global $db, $auth;

	// pieprasījumos 102 - /kvestu-pamacibas, 599 - /runescape
	// atlasa rakstus, kuri ir iekš `rs_pages`, bet nav iekš `pages`;
	// šie raksti ir dzēsti/pārvietoti
	$select_old = $db->get_results("
        SELECT
            `rs_pages`.`id`             AS `rspages_id`,
            `rs_pages`.`category_id`    AS `rspages_catid`,
            IFNULL(`pages`.`id`, 0)     AS `pages_id`,
            `pages`.`strid`             AS `pages_strid`,
            `pages`.`title`             AS `pages_title`,
            `pages`.`category`          AS `pages_catid`,
            `cat`.`parent`              AS `category_parent`
        FROM `rs_pages`
            LEFT JOIN `pages` ON `rs_pages`.`page_id` = `pages`.`id`
            LEFT JOIN `cat` ON `pages`.`category` = `cat`.`id`
        WHERE
            `rs_pages`.`deleted_by` = 0 AND
            (`pages`.`id` IS NULL OR
            `cat`.`parent` NOT IN(4, 102, 1863) OR
            `rs_pages`.`category_id` != `pages`.`category`)
        ORDER BY `pages`.`title` ASC
    ");
	if ($select_old) {

		$counter = 1;

		foreach ($select_old as $old) {

			// raksts `pages` tabulā neeksistē
			if ($old->pages_id == '0') {
				if ($print) {
					$msg = $counter . '. Raksts neeksistē! (rspages.id: ' . $old->rspages_id . '';
					$msg .= ', rspages.cat: ' . $old->rspages_catid . ')<br>';
					echo $msg;
				}
				if ($update) {
					$db->query("UPDATE `rs_pages` SET `deleted_by` = '" . $auth->id . "', `deleted_at` = '" . time() . "' WHERE `id` = '" . $old->rspages_id . "' LIMIT 1");
				}
			}
			// raksts `pages` tabulā vairs nav derīgā kategorijā
			// (piemēram, ir dzēsts)
			elseif ( !in_array($old->category_parent, array(4, 102, 1863)) ) {
				if ($print) {
					$msg = $counter . '. Raksts nelāgā kategorijā! (pages.id: ' . $old->pages_id . '';
					$msg .= ', pages.cat: ' . $old->pages_catid . ') - ' . $old->pages_title . '<br>';
					echo $msg;
				}
				if ($update) {
					$db->query("UPDATE `rs_pages` SET `deleted_by` = '" . $auth->id . "', `deleted_at` = '" . time() . "' WHERE `id` = '" . $old->rspages_id . "' LIMIT 1");
				}
			}
			// rakstam `pages` tabulā ir mainījusies kategorija
			// (tomēr derīga rs kategorija)
			elseif ($old->rspages_catid != $old->pages_catid) {
				if ($print) {
					$msg = $counter . '. Rakstam mainīta kategorija! (pages.id: ' . $old->pages_id;
					$msg .= ', rspages.cat: ' . $old->rspages_catid;
					$msg .= ', pages.cat: ' . $old->pages_catid . ') - ' . $old->pages_title . '<br>';
					echo $msg;
				}
				if ($update) {
					$db->query("UPDATE `rs_pages` SET `category_id` = '" . (int) $old->pages_catid . "', `updated_by` = '" . $auth->id . "', `updated_at` = '" . time() . "' WHERE `id` = '" . $old->rspages_id . "' LIMIT 1");
				}
			}
			$counter++;
		}
	}
	if ($print) {
		echo '<br><br><br>';
	}

	// atlasa rakstus, kuri ir iekš `pages`, bet nav iekš `rs_pages`;
	// šos rakstus ieraksta arī `rs_pages`;
	/*$select_new = $db->get_results("
        SELECT
            `pages`.`id`            AS `pages_id`,
            `pages`.`category`      AS `pages_catid`,
            `pages`.`strid`         AS `pages_strid`,
            `pages`.`title`         AS `pages_title`,
            IFNULL(`rs_pages`.`id`, 0)  AS `rspages_id`,
            `rs_pages`.`category_id`    AS `rspages_catid`,
            `cat`.`parent`              AS `category_parent`
        FROM `pages`
            JOIN `cat` ON `pages`.`category` = `cat`.`id`
            LEFT JOIN `rs_pages` ON (
                `pages`.`id`            = `rs_pages`.`page_id` AND
                `pages`.`category`      = `rs_pages`.`category_id` AND
                `rs_pages`.`deleted_by` = 0
            )
        WHERE
            `cat`.`parent` IN(4, 102, 1863) AND
            `rs_pages`.`id` IS NULL
        ORDER BY `pages`.`title` ASC
    ");
	if ($select_new) {

		$counter = 1;

		foreach ($select_new as $old) {

			// šeit vairs nav jēgas pārbaudīt, vai `rs_pages` sadaļā ir mainīta kategorija vai kas tāds,
			// kategoriju salīdzināšana jau notikusi iepriekšējā pieprasījumā;
			// raksts `rs_pages` tabulā neeksistē
			if ($old->rspages_id == '0') {
				if ($print) {
					$msg = $counter . '. Raksts neeksistē! (pages.id: ' . $old->pages_id . '';
					$msg .= ', pages.cat: ' . $old->pages_catid . ') - ' . $old->pages_title . '<br>';
					echo $msg;
				}
				if ($update) {
					$db->query("INSERT INTO `rs_pages` (page_id, category_id, created_by, created_at) VALUES ('" . (int) $old->pages_id . "', '" . (int) $old->pages_catid . "', '" . $auth->id . "', '" . time() . "') ");
				}
			}
			$counter++;
		}
	}
	if ($print) {
		exit;
	}*/
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

	$stats = false;

	if ($force || ($stats = $m->get('quests-stats')) === false) {

		// izlaisto kvestu skaits noteiktos gados
		$stats[14] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `year` = '14' AND `category_id` IN (99,100) ");
		$stats[13] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `year` = '13' AND `category_id` IN (99,100) ");
		$stats[12] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `year` = '12' AND `category_id` IN (99,100) ");
		$stats[11] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `year` = '11' AND `category_id` IN (99,100) ");
		$stats[10] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `year` = '10' AND `category_id` IN (99,100) ");
		$stats['older'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `year` NOT IN ('12','11','10','09','08') AND `category_id` IN (99,100) ");

		// kvestu tips
		$stats['p2p'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `category_id` = 100 ");
		$stats['f2p'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `category_id` = 99 ");
		$stats['miniquests'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `category_id` = 193 ");

		// kvestu sarežģītība
		$stats['special'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `difficulty` = 6 AND `category_id` IN (99,100) ");
		$stats['grandmaster'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `difficulty` = 5 AND `category_id` IN (99,100) ");
		$stats['master'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `difficulty` = 4 AND `category_id` IN (99,100) ");
		$stats['experienced'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `difficulty` = 3 AND `category_id` IN (99,100) ");
		$stats['intermediate'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `difficulty` = 2 AND `category_id` IN (99,100) ");
		$stats['novice'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `difficulty` = 1 AND `category_id` IN (99,100) ");

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
