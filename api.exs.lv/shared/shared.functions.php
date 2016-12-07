<?php
/**
 *  Globālas funkcijas mobilo lietotņu pieprasījumiem.
 */

/**
 *  Uzstāda atgriežamā JSON objekta statusu.
 */
function api_status($status) { // iOS only pagaidām
    global $json_arr;
    $json_arr['status'] = $status;
}

/**
 *  Pieprasījuma atbildei pievieno kļūdas tekstu,
 *  kuru lietotnē var attiecīgi parādīt.
 */
function api_error($string = '') {
	global $lang;
	
    // androīdam
    if ($lang === 2) {
        global $json_state, $json_message;
        $json_state = 'error';
        $json_message = $string;
    }
    
    // ios
    if ($lang === 4) {
        api_status(400);
        api_append(array('error_message' => $string));
    }
}

/**
 *  Kļūdas teksta parametrā pievieno informatīvu tekstu,
 *  ko lietotnes pusē tā arī jāuztver kā informatīvu, nevis kļūdu.
 */
function api_info($string = '') {
    global $lang;

    if ($lang === 2) {
        // androīdam
        global $json_state, $json_message;
        $json_state = 'success';
        $json_message = $string;
    }
    
    if ($lang === 4) {
        // ios
        global $json_arr;
        $json_arr['response']['info_message'] = $string;
    }
}

/**
 *  Pieprasījuma atbildei galā pievieno norādītā masīva vērtības.
 */
function api_append($obj, $value = '') {
	global $lang, $json_page;
    
    if ($lang === 2) { // androīdam
        if (!is_array($obj)) return;
        foreach ($obj as $key => $value) {
            $json_page[$key] = $value;
        }
    } else { // ios
        global $json_arr;
        if (is_array($obj)) {
            if (array_key_exists('response', $json_arr)) {
                $json_arr['response'] += $obj;
            } else {
                $json_arr['response'] = $obj;
            }
            return;
        }
        $json_arr[$obj] = $value;
    }
}

function api_append_root($obj) {
    global $json_arr;
    
    if (!is_array($obj)) return;
    
    foreach ($obj as $key => $value) {
        $json_arr[$key] = $value;
    }
}

/**
 *  Saglabā žurnālierakstu ar norādīto tekstu datubāzē.
 *  Papildu tiek fiksēta adrese, kādu lietotājs centies ielādēt.
 */
function api_log($text) {
	global $db, $auth, $lang;
	
	if (empty($text)) return;
	
	$uri = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
	
	return $db->insert('api_logs', array(
        'api_type' => ($lang === 2 ? 0 : 1),
		'url' => sanitize($uri),
		'message' => sanitize($text),
		'created_by' => (int)$auth->id,
		'created_at' => date('Y-m-d H:i:s', time()),
		'created_ip' => sanitize($auth->ip)
	));
}

/**
 *  Atgriež XSRF atslēgu, kādu nosūtīt tālāk pieprasījuma atbildē.
 *  No lietotnes nākošajiem pieprasījumiem adrešu galā jābūt šai atslēgai.
 */
function api_make_xsrf() {
	global $auth;
	// nav jēgas izmantot MD5 hashu visā garumā
	return substr($auth->xsrf, 0, 10);
}

/**
 *  Pārbauda, vai pieprasījumā saņemtā XSRF atslēga sakrīt ar to,
 *  kāda atbilst lietotājam, kas pieprasījumu veicis.
 */
function api_check_xsrf($token = '') {
	global $auth, $lang;
    $key = ($lang === 2) ? 'xsrf' : 'token';
	if (empty($token)) {
		if (!empty($_GET[$key])) {
			return (substr($auth->xsrf, 0, 10) === $_GET[$key]);
		}
		return false;
	}
	return (substr($auth->xsrf, 0, 10) === $token);
}

/**
 *  Saformatēs tekstu atbilstoši tam, kādu to spēj uztvert Androīds.
 *
 *  Aizstās tekstā esošos attēlus (HTML <img/> tagos) ar informāciju, ka
 *  attiecīgajā vietā bija attēls, un atgriezīs masīvu ar visu atrasto
 *  attēlu adresēm secībā, kādā tās tika atrastas.
 *
 *  Izmantojams miniblogiem, lai lietotnei attēlu saites aizsūtītu atsevišķi un
 *  tā tos ielādētu atsevišķi AIZ teksta lauka kā pielikumus, nevis pašā
 *  teksta laukā, kas ir tehniski sarežģīti un arī nesniedz patīkamu rezultātu.
 *
 *  Papildu pareizi tiks noformētas arī adreses, lai lietotnē tās
 *  atpazītu un būtu nospiežamas, tiks paslēpti spoileri un veikti
 *  vēl citi pārveidojumi.
 */
function api_format_text(&$mb_text, $return_img = true) {

	// paslēps spoilerus, kurus lietotnē pagaidām īsti labi parādīt nevar
	if (strpos($mb_text, 'spoiler') !== false) {
		$mb_text = preg_replace('/\[spoiler\](.*)\[\/spoiler\]/is', 
			"(spoileris slēpts)", $mb_text);
	}

	// widgetus atstās kā saites un arī smaidiņu vietā neieliks attēlus,
	// jo to prot darīt jau pati lietotne
	$mb_text = add_smile($mb_text, false, true, true);
	$mb_text = strip_tags($mb_text, '<a><img><br><p><strong><b><i><em>');

	$arr_images = array();
	
	// ja tekstā nav ne adrešu, ne attēlu, to var atgriezt esošajā formā
	if (strpos($mb_text, '<img') === false && strpos($mb_text, 'href') === false) {
		return $arr_images;
	}
	
	// lai novērstu kodējumu kļūdas
	// (https://stackoverflow.com/questions/11309194/php-domdocument-failing-to-handle-utf-8-characters-%E2%98%86)
	$mb_text = mb_convert_encoding($mb_text, 'HTML-ENTITIES', 'UTF-8');

	$dom = new DOMDocument();    
	$dom->loadHTML($mb_text, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
	
	// attēlu aizstāšana
	if (strpos($mb_text, '<img') !== false) {
	
		$images = $dom->getElementsByTagName('img');
		
		// aizstājot elementu ar citu, mainās kaut kas ar indeksiem un foreach
		// vienā brīdī pārtrauc strādāt, tāpēc jāizmanto regressive loop
		for ($i = $images->length; $i > 0; $i--) {    
			$image = $images->item($i - 1);
			$image_link = api_fill_link($image->getAttribute('src'));
			$arr_images[] = $image_link;            
			$element = $dom->createElement('span', '(attēls #'.$i.')');
			$element->setAttribute('class', 'img_replacement');
			$image->parentNode->replaceChild($element, $image);
		}
	}
	
	// saformatēs adreses uz tādām, kādas Androīdā atpazīst
	// "<a href="https://exs.lv">spied šeit</a>" -> "https://exs.lv"
	if (strpos($mb_text, 'href') !== false) {    
		$links = $dom->getElementsByTagName('a');        
		for ($i = $links->length; $i > 0; $i--) {        
			$link = $links->item($i - 1);  

			$do_replace = true;
			
			// ja adrese ir ap pieminētu lietotāju, labāk lietotājvārdu atstāsim
			if ($link->getAttribute('class') == 'post-mention') {
				$do_replace = false;

			// ja šīs adreses iekšienē iepriekš tika aizstāts attēls,
			// tad adresi nevajag pārveidot, lai nepazaudētu info par attēlu
			} else {
				$imgs = $link->getElementsByTagName('span');
				if ($imgs->length > 0) {
					$img = $imgs->item(0);
					if ($img->getAttribute('class') === 'img_replacement') {
						$do_replace = false;
					}
				}
			}            
			
			if ($do_replace) {
				$addr = api_fill_link($link->getAttribute('href'));
				$element = $dom->createTextNode($addr);
				$link->parentNode->replaceChild($element, $link);
			}
		}
	}
	
	$mb_text = $dom->saveHTML();
	
	// aizvāks lieki pievienoto "\n" no rindas beigām
	$mb_text = mb_substr($mb_text, 0, -1);
	
	// regressive loop masīvā attēlus saglabāja pretēja secībā
	if ($return_img) {
		return array_reverse($arr_images);
	}
}

/**
 *  Pēc vajadzības pievienos adresei priekšā pareizo protokolu un
 *  apakšprojekta adresi.
 */
function api_fill_link($string) {
	global $config_domains, $api_lang;

	if (strlen($string) < 2) {
		return $string;
	}
	
	$project = $config_domains[$api_lang]['domain'];

	$first_sym = substr($string, 0, 1);
	$second_sym = substr($string, 1, 1);
	
	$before = 'http:';
	if (!empty($_SERVER['HTTPS'])) {
		$before = 'https:';
	}
	
	// ja adrese sākas ar "//"
	if ($first_sym === '/' && $second_sym === '/') {
		$string = $before.$string;
		
	// ja adrese ir formā, piemēram, "/user/115"
	} else if ($first_sym === '/' && $second_sym !== '/') {
		$string = $before.'//'.$project.$string;
	}
	
	return $string;
}

/**
 *  Ieraksta avatara adreses iegūšana.
 *
 *  Atkarībā no tā, vai norādīts bilžu serveris un attēls kā tāds,
 *  kā arī pēc citiem parametriem izveido bildes adresi.
 *
 *  Tai vienmēr jābūt ar "http" pilno adresi, lai lietotnē zinātu,
 *  no kurienes lejuplādēt.
 *
 *  @param object   satur vērtības, pēc kurām var izveidot adresi
 *  @param string   s|m|l   norāda nepieciešamā avatara izmēru
 */
function api_get_user_avatar($user, $size = 'm') {
	global $auth, $img_server;
	
	if (!$user || empty($user->av_alt) || empty($user->avatar)) {
		return '';
	}
	
	// pēc noklusējuma izveido vidēja izmēra attēla adresi
	$path       = 'medium';
	$real_path  = 'useravatar';
	
	// nepieciešamības gadījumā izmēru nomaina
	if (($user->av_alt || !$user->avatar) && $size == 's') {
		$path       = 'small';
		$real_path  = 'u_small';
	} elseif (($user->av_alt || !$user->avatar) && $size == 'l') {
		$path       = 'large';
		$real_path  = 'u_large';
	}
	
	// rādīs silueta avataru, ja cits nebūs norādīts
	if (empty($user->avatar)) {
		$user->avatar = 'none.png';
	}

	// localhost avataru fix
	if (empty($img_server)) {

		if (file_exists(CORE_PATH . '/dati/bildes/' . $real_path . '/' . $user->avatar)) {
			//lokālais avatars
			return 'http://img.exs.lv/dati/bildes/' . $real_path . '/' . $user->avatar;
		} else {
			// tāpat mēģina nolasīt no img.exs.lv
			return 'http://img.exs.lv/userpic/' . $path . '/' . $user->avatar;
		}
	} else {    
		return $img_server . '/userpic/' . $path . '/' . $user->avatar;
	}
}

/**
 *  Atgriezīs x jaunākos apbalvojumus lietotāja profilā.
 */
function api_fetch_awards($user_id, $award_count = 4) {
	global $db, $m, $img_server, $lang;
	
	$user_id = (int)$user_id;
	$award_count = (int)$award_count;
	
	if ($user_id < 0 || $award_count < 0 || $award_count > 10) {
		api_error('Kļūdaini apbalvojumu ielādes parametri');
		return;
	}
	
	$memcached_key = 'api_'.$lang.'_awards_'.$user_id.'-'.$award_count;
	
	if (($data = $m->get($memcached_key)) === false) {
		$awards = array();
		$awards_list = $db->get_results("
			SELECT `id`, `title`, `award` FROM `autoawards` 
			WHERE `user_id` = ".$user_id."
			ORDER BY `importance` DESC
			LIMIT ".$award_count
		);
		if ($awards_list) {
			foreach ($awards_list as $award) {
				$awards[] = array(
					'img_url' => $img_server.'/dati/bildes/awards/'.$award->award.'.png',
					'title' => strip_tags($award->title)
				);
			}
		}
		// kopējais apbalvojumu skaits šim lietotājam
		$total = $db->get_var(
			"SELECT count(*) FROM `autoawards` WHERE `user_id` = ".$user_id
		);

        $data = null;
        if ($lang === 2) {
            $data = array(
                'count' => (int)$total,
                'list' => $awards
            );
        } else {
            $data = array(
                'total_count' => (int)$total,
                'top_count' => 6,
                'list' => $awards
            );
        }
		$m->set($memcached_key, $data, false, 900);
	}
	
	api_append(array('awards' => $data));
}
