<?php
/**
 *  Satur tās funkcijas, kurām ir saistība ar RuneScape jaunumu ielasīšanu
 *  no runescape.com RSS feeds un to parādīšanu sākumlapā.
 *
 *  Šīs funkcijas tiek izmantotas tikai šajā modulī!
 */

/**
 *  Atgriež HTML ar RuneScape jaunākajām ziņām, kas
 *  no runescape.com RSS saglabāts cache failā.
 */
function fetch_news($type = 'rs3') {
	
	$filename = 'official-news.html';
	if ($type === 'oldschool') {
		$filename = 'oldschool-news.html';
	}
	
	$output = '<p class="simple-note">Neizdevās nolasīt jaunumus. :(<br><br>Pāris minūšu laikā saraksts tiks atjaunots.</p>';

	$file = @fopen(CORE_PATH.'/cache/runescape/'.$filename, 'r');
	if ($file !== false) {
		$output = fread($file, 
			filesize(CORE_PATH.'/cache/runescape/'.$filename));
		fclose($file);
	}
	
	return $output;
}

/**
 *  RuneScape.com jaunumu RSS feed lasītājs.
 *
 *  Veiks jaunāko ziņu nolasīšanu no mājaslapas (gan RS3, gan OSRS) un
 *  tās saglabās datubāzē. Katram jaunumam izveidos attiecīgu miniblogu.
 *  Jaunumu logo tiek pārglabāti lokāli vai aizvietoti ar placeholderiem.
 *
 *  RSS feed lasīšana tiek veikta tikai reizi 10 minūtēs.
 */
function read_rss($force = false) {
	global $m, $db, $auth, $rsbot_id, $lang;
	
	$read_every = 600; // sekundes

	$urls = array(
		'rs3' => 'http://services.runescape.com/m=news/latest_news.rss',
		'oldschool' => 'http://services.runescape.com/m=news/latest_news.rss?oldschool=true'
	);
	
	foreach ($urls as $key => $link) {
	
		// memcached glabās tikai pēdējās parsēšanas laiku
		if (!$force && $m->get('rs-rssfeed-'.$key.'-lastread')) continue;        
		$m->set('rs-rssfeed-'.$key.'-lastread', time(), false, $read_every);

		$news = curl_get($link);
		if ($news === false) continue; // ignorēs un neko nedarīs
		
		// ciklā katru jauno ierakstu saglabās masīvā, kura vērtības pēc tam
		// apgriezīs pretēji, lai pievienotu ierakstus pareizā secībā
		$reversed_objects = array();
		$data = new SimpleXmlElement($news);
		foreach ($data->channel->item as $single) {

			$single->is_oldschool = ($key === 'oldschool') ? 1 : 0;
			
			// pārbaude, vai datubāzē šāds jaunums jau neeksistē
			$single->hashval = sanitize(md5($single->pubDate.$single->title));        
			$val = $db->get_var("
				SELECT count(*) FROM `rs_news`
					JOIN `miniblog` ON `rs_news`.`mb_id` = `miniblog`.`id`
				WHERE 
					`rs_news`.`hash_value` = '".$single->hashval."' AND
					`rs_news`.`is_oldschool` = ".$single->is_oldschool
			);
			if ($val > 0) continue; // dublikātus nevajag
			
			$reversed_objects[] = $single;
		}
		
		// varbūt jaunu ierakstu nebija
		if (empty($reversed_objects)) continue;

		// izies cauri jaunajiem ierakstiem pretējā secībā un tos pievienos
		foreach (array_reverse($reversed_objects) as $single) {

			// izveidos ierakstu `miniblog` tabulā
            $append = '';
            if ((int)$single->is_oldschool) {
                $append = '&nbsp;<span class="rsmb-oldschool">(Oldschool)</span>';
            }            
			$mb_text  = '<p class="rsmb-title">'.$single->title.$append.'</p>'.
						'<p class="rsmb-text">'.$single->description.'<br><br>'.
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

			// izveidos ierakstu `rs_news` tabulā
			$mb_id = $db->insert_id;
			$has_image = (isset($single->enclosure['url'])) ? 1 : 0;
			$os_prefix = ((int)$single->is_oldschool) ? 'os-' : 'rs3-';

			// ne visiem rakstiem ir pieejams logo
			if ($has_image) {
				// attēls tiks saglabāts uz lokālā servera
				$img_path = CORE_PATH.'/bildes/runescape/news/';
				$save = save_rs_image(
					$single->enclosure['url'], // source_path
					$img_path, // target_path
					$os_prefix.$mb_id.'.jpg' // img_title
				);
				// ja lokāli attēlu saglabāt neizdodas, tā arī jāatzīmē,
				// lai pēcāk varētu rādīt fallback attēlus
				if ($save === false) $has_image = 0;
			}
			
			$values = array(
				'hash_value'    => input2db((string)$single->hashval, 256),
				'is_oldschool'  => (int)$single->is_oldschool,
				'mb_id'         => $mb_id,
				'news_title'    => input2db($single->title, 256),
				'news_category' => input2db($single->category, 256),
				'news_description' => input2db($single->description, 1000),
				'news_date'     => input2db($single->pubDate, 256),
				'news_link'     => input2db($single->link, 400),
				'has_image'     => $has_image,
				'created_by'    => (int)$rsbot_id,
				'created_at'    => time()
			);
			$db->insert('rs_news', $values);
		}
	}

	// izsauks HTML cache failu saģenerēšanu pēc datiem no datubāzes
	create_news('rs3');
	create_news('oldschool');
}

/**
 *  Uzģenerēs HTML formāta cache failu ar RuneScape ziņām,
 *  par kurām dati jau saglabāti datubāzē.
 */
function create_news($type = 'rs3') {
	global $db, $rsbot_id, $img_server;
	
	$news_count = 12; // ierakstu skaits, kas būs redzams lapā    
	$is_oldschool = ($type === 'oldschool') ? 1 : 0;

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
			`miniblog`.`removed`,
			`miniblog`.`text`
		FROM `rs_news`
			JOIN `miniblog` ON `rs_news`.`mb_id` = `miniblog`.`id`
		WHERE
			`rs_news`.`deleted_by` = 0 AND
			`rs_news`.`is_oldschool` = ".$is_oldschool."
		ORDER BY
			`rs_news`.`id` DESC
		LIMIT 0, ".$news_count."
	");    
	if (!$news) return; // slikti, ka tā :(
	
	$img_prefix = ($is_oldschool) ? 'os-' : 'rs3-';
	$out = '<ul class="official-news">';

	foreach ($news as $single) { // izies cauri atlasītajiem ierakstiem
	
		// ja ierakstam dzēsts miniblogs, to šeit neiekļaus
		if ($single->removed) continue;
		
		$img_path = '/bildes/runescape/news/'.$img_prefix.$single->mb_id.'.jpg';
		if ($type === 'oldschool') {
			$fallback_path = '/bildes/runescape/fallback/os-fallback.png';
		} else {
			$fallback_path = '/bildes/runescape/fallback/'.get_fallback_image($single->category);
		}
		
		// attēls
		$image = '';
		if ($single->has_image && file_exists(CORE_PATH.$img_path)) {
			$image = '<img class="vc-item" src="'.$img_server.$img_path.'" title="'.$single->title.'" alt="Logo">';
		} else {
			$image = '<img class="vc-item" src="'.$fallback_path.'" title="'.$single->title.'" alt="Logo">';
		}
		
		$date = date('d.m.Y', strtotime($single->date));
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
	
	// oldschool jaunumos senāki raksti nav apskatāmi
	if (!$is_oldschool) {
			$out .= '<li class="link">'.
				'<a href="http://services.runescape.com/m=news/" rel="nofollow" '.
			'target="_blank">Skatīt senākus rakstus</a></li>';
	}
	$out .= '</ul>';
	
	$filename = ($is_oldschool) ? 'oldschool-news.html' : 'official-news.html';
	$file = fopen(CORE_PATH.'/cache/runescape/'.$filename, 'w');
	fwrite($file, $out);
	fclose($file);
}

/**
 *  Saglabās lokāli RuneScape ziņu raksta logo.
 *
 *  @param  string  vieta, no kurienes attēls jālejuplādē
 *  @param  string  vieta, kur attēls uz servera jāsaglabā
 *  @param  string  attēla nosaukums
 */
function save_rs_image($source_path, $target_path, $target_name = 'empty') {

	if ($target_name == 'empty' || empty($target_name)) return false;

	$curl = curl_init($source_path);
	$file = fopen($target_path.$target_name, 'wb');
	curl_setopt($curl, CURLOPT_FILE, $file);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($curl, CURLOPT_TIMEOUT, 4);
	$exec = curl_exec($curl);
	curl_close($curl);
	fclose($file);

	// pārveidos attēlu uz pieļaujamu izmēru
	require_once(CORE_PATH . '/includes/class.upload.php');

	$foo = new Upload($target_path.$target_name);
	$foo->image_max_pixels = 200000000;
	if ($foo->uploaded) {
		$foo->file_new_name_body = str_replace(array('.png','.gif','.jpg', '.jpeg'), '', $target_name);
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
 *  Atgriezīs fallback bildi RuneScape jaunumu ierakstam.
 */
function get_fallback_image($string = '') {

	$categories = array(
		'Game Update News'          => 'gameupdates.jpg',
		'Future Updates'            => 'gameupdates.jpg',
		'Behind the Scenes News'    => 'behindthescenes.jpg',
		'Your Feedback'             => 'yourfeedback.jpg',
		'Website News'              => 'website.jpg',
		'Events'                    => 'community.jpg',
		'Technical News'            => 'technical.jpg',
		'Support'                   => 'support.jpg',
		'Customer Support News'     => 'yourfeedback.jpg',
		'Community'                 => 'community.jpg',
		'Solomon&apos;s Store'      => 'solomons.jpg',
		'Treasure Hunter'           => 'treasurehunter.jpg'
	);

	if ($string !== '' && array_key_exists($string, $categories)) {
		return 'fallback-' . $categories[$string];
	}
	return 'fallback-technical.jpg';
}

/**
 *  Atgriezīs tulkojumu RuneScape jaunumu ieraksta sadaļai.
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

	if ($string !== '' && array_key_exists($string, $categories)) {
		return $categories[$string];
	}
	return $string;
}
