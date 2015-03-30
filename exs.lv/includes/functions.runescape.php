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
function get_tpl($file = '', $add_path = true) {
	global $category;
	
	if ($file == '') {
		return false;
	}

	if ($add_path) {
		$file = CORE_PATH.'/modules/'.$category->module.'/'.$file.'.tpl';
	}
	
	if (!file_exists($file)) {
		return false;
	}

	$tpl = new TemplatePower($file);
	$tpl->prepare();
	
	return $tpl;
}


/**
 *  Nolasa runescape ziņas no cache faila
 *
 *  (Tiek izsaukta RuneScape apakšprojekta sākumlapā.)
 *
 *  @param  bool    norāde, vai atjaunot cache
 */
function read_runescape_news($force = false) {
	global $m;

	// memcache pārbauda un glabā tikai pēdējo parsēšanas laiku
	if ($force || $m->get('runescape-news') === false) {
		read_runescape_rss($force);
		$m->set('runescape-news', time(), false, 600);
	}

	$output = '<p class="simple-note">Neizdevās nolasīt jaunumus</p>';

	$file = @fopen(CORE_PATH.'/cache/runescape/official-news.html', 'r');
	if ($file !== false) {
		$output = fread($file, 
			filesize(CORE_PATH.'/cache/runescape/official-news.html'));
		fclose($file);
	}
	
	return $output;
}


/**
 *  RuneScape.com RSS feed lasītājs
 *
 *  Nolasa jaunākās ziņas un saglabā tās datubāzē.
 *  Jauniem ierakstiem izveido miniblogus.
 *
 *  Ja rakstiem ir pievienota arī logo adrese,
 *  logo tiek saglabāts lokāli un tā izmērs pielāgots vajadzībām.
 */
function read_runescape_rss($force = false) {
	global $db, $auth, $rsbot_id, $lang;   

	$news = curl_get('http://services.runescape.com/m=news/latest_news.rss');
	if ($news === false) {    
		// labāk, lai no esošiem db ierakstiem cache pārģenerē šā vai tā
		generate_news_cache();
		return;
	}

	$data = new SimpleXmlElement($news);

	foreach ($data->channel->item as $single) {                

		// pārbaude, vai datubāzē šāds jaunums jau neeksistē
		$hash_val = sanitize(md5($single->pubDate.$single->title));
		$val = $db->get_var("
			SELECT count(*) FROM `rs_news`
				JOIN `miniblog` ON `rs_news`.`mb_id` = `miniblog`.`id`
			WHERE 
				`rs_news`.`hash_value` = '$hash_val'
		");
		if ($val > 0) continue; // dublikātus nevajag

		// izveido ierakstu `miniblog` tabulā
		$mb_text  = '<p class="rsmb-title">'.$single->title.'</p>'.
					'<p class="rsmb-text">'.$single->description.'<br>'.
					'Oriģinālraksts: <a href="'.$single->link.'" '.
					'rel="nofollow" target="_blank">'.$single->link.'</a></p>'.
					'<p class="rsmb-fade">Ieraksts izveidots automātiski.</p>';

		$values = array(
			'author'    => (int)$rsbot_id,
			'date'      => date("Y-m-d H:i:s", time()),
			'text'      => sanitize($mb_text),
			'lang'      => (int)$lang,
			'bump'      => time()
		);
		$insert = $db->insert('miniblog', $values);

		if (!$insert) continue;

		// izveido ierakstu `rs_news` tabulā
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
		
		$values = array(
			'hash_value'    => input2db($hash_val, 256),
			'mb_id'         => $mb_id,
			'news_title'    => input2db($single->title, 256),
			'news_category' => input2db($single->category, 256),
			'news_description' => input2db($single->description, 1000),
			'news_date'     => input2db($single->pubDate, 256),
			'news_link'     => input2db($single->link, 400),
			'has_image'     => $has_image,
			'created_by'    => $rsbot_id,
			'created_at'    => time()
		);
		$db->insert('rs_news', $values);
	}
	
	generate_news_cache();
}


/**
 *  Izveido cache failu ar jaunākajām RuneScape ziņām
 */
function generate_news_cache() {
	global $db, $rsbot_id, $img_server;
	
	$news_count = 12;

	$news = $db->get_results("
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
		LIMIT 0, $news_count
	");    
	if (!$news) return; // slikti, ka tā :(
	
	$out = '<ul class="official-news">';
	   
	foreach ($news as $single) {
		
		// attēls
		$image = '';
		if ($single->has_image &&
			file_exists(CORE_PATH.'/bildes/runescape/news/thumb-news-'.$single->mb_id.'.jpg') ) {
			$image = '<img class="vc-item" src="'.$img_server.'/bildes/runescape/news/thumb-news-'.$single->mb_id.'.jpg" title="'.$single->title.'" alt="Logo">';
		} else {
			$image = '<img class="vc-item" src="'.$img_server.'/bildes/runescape/fallback/'.get_fallback_image($single->category).'" title="'.$single->title.'" alt="Logo">';
		}
		
		$date = date("d.m.Y", strtotime($single->date));
		$cat = translate_category((string)$single->category);
		$description = (empty($image) ? 
			textlimit($single->description, 90, '...') : 
			textlimit($single->description, 65, '...'));

		 // rakstu, kuriem nav logo, laukumiem ir lielākas atstarpes
		$style = (empty($image) ? 
			' style="padding:0 10px 5px;width:90%"' : '');
		
		$out .= '<li><a href="/say/'.$rsbot_id.'/'.$single->mb_id.'-'.
			mb_get_strid($single->text, $single->mb_id).'">'.
			'<span class="vc-ghost-item"></span>'.$image.
			'<p class="vc-item"'.$style.'>'.
				'<span>'.$single->title.'</span>'.$description.
				'<span>'.$date.' &middot; '.$cat.'</span>'.
			'</p>'.
			'</a></li>';
		
	}
	$out .= '<li class="link">'.
		'<a href="http://services.runescape.com/m=news/" rel="nofollow" '.
		'target="_blank">Skatīt senākus rakstus</a></li>';
	$out .= '</ul>';

	$file = fopen(CORE_PATH.'/cache/runescape/official-news.html', 'w');
	fwrite($file, $out);
	fclose($file);
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
