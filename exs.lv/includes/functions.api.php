<?php
/**
 *  Globālas funkcijas Android/iOS lietotnes pieprasījumiem.
 *  Tiek izmantotas Android/iOS modulī (modules/android | modules/ios).
 *
 *  Funkciju nosaukumiem izmantots "a_" prefix, lai citos failos tās
 *  varētu atšķirt.
 */

/**
 *  Pievienos kļūdas paziņojumu nākamajai atbildei,
 *  kuru lietotne izmetīs kā Toast ziņu.
 */
function a_error($string = '') {
	global $json_state, $json_message;
	
	$json_state   = 'error';
	$json_message = $string;
}

/**
 *  Atgriezīs XSRF tokenu, kāds izmantojams Android adresēs,
 *  lai identificētu konkrēto lietotāju.
 */
function a_make_xsrf() {
	global $auth;
	// nav jēgas izmantot MD5 hashu visā garumā
	return substr($auth->xsrf, 0, 7);
}

/**
 *  Pārbaudīs, vai norādītā xsrf atslēga sakrīt ar 
 *  konkrētajam lietotājam izveidoto.
 */
function a_check_xsrf($key = '') {
	global $auth;
	if (empty($key)) {
		if (!empty($_GET['xsrf'])) {
			return (substr($auth->xsrf, 0, 7) === $_GET['xsrf']);
		}
		return false;
	}
	return (substr($auth->xsrf, 0, 7) === $key);
}

/**
 *  Pievienos atbildei tekstu, kas netiks uztverts kā kļūda,
 *  bet kuru lietotne pēc vajadzības varēs kaut kur izvadīt,
 *  paskaidrojot situāciju.
 */
function a_message($string = '') {
	global $json_page;
	
	$json_page['message'] = $string;
}

/**
 *  Pievienos atbildei masīvā norādītās vērtības.
 */
function a_append($values) {
	global $json_page;
	
	if (!is_array($values)) {
		return;
	}

	foreach ($values as $key => $value) {
		$json_page[$key] = $value;
	}
}

/**
 *  Saglabās ziņojumu datubāzes `android_logs` tabulā,
 *  piefiksējot atvērto adresi, lai zinātu, ko lietotājs centās atvērt.
 */
function a_log($text) {
	global $db, $auth;
	
	if (empty($text)) {
		return;
	}
	
	$uri = (isset($_SERVER['REQUEST_URI'])) ? 
		$_SERVER['REQUEST_URI'] : '';
	
	return $db->insert('android_logs', array(
		'message' => sanitize($text),
		'url' => sanitize($uri),
		'created_by' => (int)$auth->id,
		'created_at' => date('Y-m-d H:i:s', time()),
		'created_ip' => sanitize($auth->ip)
	));
}

/**
 *  Ielādēs informāciju, kādu lietotne vēlas saņemt pie veiksmīgas autorizācijas.
 */
function a_load_profile() {
	global $db, $auth, $img_server;
	
	// nelasīto vēstuļu skaits
	$msgs = $db->get_var("
		SELECT count(*) FROM `pm` WHERE `to_uid` = ".$auth->id." AND `is_read` = 0
	");

	a_append(array('profile' => array(
		'id' => (int)$auth->id,
		'nick' => $auth->nick,
		'level' => (int)$auth->level,
		'av_url' => $img_server.'/userpic/medium/'.$auth->avatar,
		'usertitle' => $auth->custom_title,
		'users_online' => (int)$auth->hosts_online,
		'inbox_unread' => (int)$msgs
	)));
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
function a_format_text(&$mb_text, $return_img = true) {

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
			$image_link = a_fill_link($image->getAttribute('src'));
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
				$addr = a_fill_link($link->getAttribute('href'));          
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
function a_fill_link($string) {
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
 *  Atgriezīs datus par norādīto lietotāju.
 *
 *  Android lietotnē tie ir nepieciešami specifiskā formātā (ne-HTML), lai
 *  lietotne pēc tam spētu lietotājvārdu atbilstoši "izdaiļot" ar krāsām.
 *
 *  Ja tiek izmantoti noklusētie parametri, atgriezīs datus par to lietotāju,
 *  kas šo funkciju izsauc. Norādot parametrus, dati atbildīs norādītajam
 *  lietotājam.
 */
function a_fetch_user($user_id = 0, $nick = '-', $level = 0) {
	global $auth, $online_users, $busers;

	// dati par autorizēto lietotāju
	if ($user_id == 0) {
		$user_id    = $auth->id;
		$user_nick  = $auth->nick;
		$user_level = $auth->level;

	// dati par norādīto lietotāju
	} else {
		$user_id    = (int)$user_id;
		$user_nick  = $nick;
		$user_level = $level;
	}

	$is_online = false;
	$is_banned = false;
	$device = 0; // 0 - dators, 1 - mob. tel., 2 - androīda app

	// vai lietotājs ir tiešsaistē?
	if ((!empty($online_users['onlineusers'][$user_id])) || 
		(!empty($online_users['onlineusers']) && 
		in_array($user_nick, $online_users['onlineusers']))) {
	
		$is_online = true;
	}

	// caur kādu ierīci lietotājs ielādējis saturu?
	if (!empty($online_users['androidusers']) && 
		in_array($user_nick, $online_users['androidusers'])) {
		$device = 2;
	} else if (!empty($online_users['mobileusers']) && 
		in_array($user_nick, $online_users['mobileusers'])) {
		$device = 1;
	}

	// vai lietotājs ir bloķēts un tā lietotājvārds jāpārsvītro?
	if (!empty($busers) && !empty($busers[$user_id])) {
		$is_banned = true;
	}

	$data = array(
		'id'        => (int)$user_id, 
		'nick'      => (string)$user_nick,
		'level'     => (int)$user_level,
		'is_online' => (bool)$is_online,
		'is_banned' => (bool)$is_banned,
		'device'    => (int)$device
	);

	return $data;
}

/**
 *  Pievienos atbildei datus par lietotājam piemēroto liegumu, ja tāds ir.
 *
 *  @param $type    1 - ip liegums, 2 - profila liegums
 *  @param query    ja $type = 1, datus ņem no šī query
 */
function a_fetch_ban($type = 1, $ip_banned = null) {
	global $db, $auth;
	global $json_page, $json_banned;

	$type = (int)$type;
	if ($type !== 1 && $type !== 2) {
		return false;
	}
	
	$json_banned = $type;
	
	// profila liegums
	if ($type === 2) {
	
		$prof_banned = $db->get_row("
			SELECT * FROM `banned` 
			WHERE 
				`active` = 1 AND 
				`user_id` = ".(int)$auth->id."
			LIMIT 1
		");
		
		if (!$prof_banned) {
			a_error('Neizdevās atlasīt lieguma info');
		} else {
			$from_user = get_user($prof_banned->author);
			$to_user = get_user($prof_banned->user_id);
			
			if ($from_user && $to_user) {
				a_append(array(
					'ip' => $prof_banned->ip,
					'to_user' => a_fetch_user($to_user->id, 
						$to_user->nick, $to_user->level),
					'reason' => $prof_banned->reason,
					'from_user' => a_fetch_user($from_user->id, 
						$from_user->nick, $from_user->level),
					'date_from' => date('d.m.Y, H:i', $prof_banned->time),
					'date_to' => date('d.m.Y, H:i', $prof_banned->time + 
						$prof_banned->length),
					'remaining' => strTime($prof_banned->time + 
						$prof_banned->length - time())
				));
			}
		}
		
	// ip liegums
	} else if ($type === 1 && $ip_banned != null) {
	
		$from_user = get_user($ip_banned->author);
		$to_user = get_user($ip_banned->user_id);
		
		if ($from_user && $to_user) {
			a_append(array(
				'ip' => $ip_banned->ip,
				'to_user' => a_fetch_user($to_user->id, 
					$to_user->nick, $to_user->level),
				'reason' => $ip_banned->reason,
				'from_user' => a_fetch_user($from_user->id, 
					$from_user->nick, $from_user->level),
				'date_from' => date('d.m.Y, H:i', $ip_banned->time),
				'date_to' => date('d.m.Y, H:i', $ip_banned->time + 
						$ip_banned->length),
				'remaining' => strTime($ip_banned->time + 
					$ip_banned->length - time())
			));
		}
	}
}

/**
 *  Tiešsaistē esošie lietotāji.
 *
 *  Atgriezīs sarakstu ar tiešsaistē esošajiem lietotājiem 
 *  atvērtajā apakšprojektā pēdējās x sekundēs.
 */
function a_fetch_online($force = false) {
	global $db, $m, $auth, $api_lang;
	global $online_users, $busers;
	
	// laiks sekundēs, kurā lietotāju uzskata par tiešsaistē esošu
	$online_seconds = 300;
	
	$data = array();
	
	// satura nolasīšana no memcached
	if ($force || !($data = $m->get('api-online-'.$api_lang))) {

		$online = null;
		$classes = null;
		
		$last_seen = date('Y-m-d H:i:s', time() - $online_seconds);
		
		$lastseen = $db->get_results("
			SELECT
				DISTINCT(`visits`.`user_id`) AS `user_id`,
				`users`.`nick`,
				`users`.`level`
			FROM `visits`
				JOIN `users` ON `visits`.`user_id` = `users`.`id`
			WHERE
				`visits`.`site_id` = ".$api_lang." AND
				`visits`.`lastseen` > '".$last_seen."'
			ORDER BY
				`users`.`nick` ASC
		");

		if (!$lastseen) {
			a_append(array(
				'online' => 0,
				'registered' => 0,
				'users' => array()
			));
			return false;
		}
		
		$cnt_registered = 0;

		foreach ($lastseen as $user) {       

			// noteiks ierīci, no kādas lietotājs pieslēdzies
			$device = 0;
			if (!empty($online_users['androidusers']) && 
				in_array($user->nick, $online_users['androidusers'])) {
				$device = 2; // androīda apps
			} else if (!empty($online_users['iosusers']) && 
				in_array($user->nick, $online_users['iosusers'])) {
				$device = 3; // ios apps
			} else if (!empty($online_users['mobileusers']) && 
				in_array($user->nick, $online_users['mobileusers'])) {
				$device = 1; // mob. tel.
			} else {
				$device = 0; // dators
			}
			
			// pārbauda, vai lietotājs ir bloķēts
			$is_banned = false;
			if (!empty($busers) && !empty($busers[$user->user_id])) {
				$is_banned = true;
			}
		
			$online[] = array(
				'id' => (int)$user->user_id,
				'nick' => (string)$user->nick,
				'level' => (int)$user->level,
				'is_online' => true,
				'is_banned' => (bool)$is_banned,
				'device' => (int)$device
			);
			
			$cnt_registered++;
		}

		$data = array(
			'online' => (int)$auth->hosts_online,
			'registered' => (int)$cnt_registered,
			'users' => $online
		);
		
		$m->set('api-online-'.$api_lang, $data, false, 15);
	}
	
	a_append($data);
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
function a_get_user_avatar($user, $size = 'm') {
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
 *  Atgriež JSON sarakstu ar jaunākajiem exs.lv rakstiem.
 *
 *  Atbalsta pārvietošanos pa lapām un apakšprojektus.
 */
function a_get_news() {
	global $auth, $db, $lang, $api_lang;
	
	// vienā lappusē redzamo rakstu skaits
	$news_in_page = 20;

	if (isset($_GET['page'])) {
		$skip = $news_in_page * intval($_GET['page']);
	} else {
		$skip = 0;
	}
	
	// tiek pievienoti kritēriji rakstu atlasei
	$conditions = array();
	
	// redzami izvēlētā apakšprojekta vai $lang=0 raksti
	$conditions[] = '(`pages`.`lang` = ' . (int)$api_lang . ' || `pages`.`lang` = 0)';

	// atlasa sadaļas, kuras lietotājs vēlas ignorēt
	if ($auth->ok) {
		$ignores = $db->get_col("SELECT `category_id` FROM `cat_ignore` WHERE `user_id` = '$auth->id'");
		if (!empty($ignores)) {
			foreach ($ignores as $ignore) {
				$conditions[] = "`category` != $ignore";
			}
		}
	}

	// moderatoru sadaļu pārbaude
	$mods_only = '';
	if (!im_mod()) {
		$mods_only = " AND `cat`.`mods_only` = 0";
	}

	// tiek atlasīti izvēlētie raksti
	$latest = $db->get_results("
		SELECT
			`pages`.`id`,
			`pages`.`strid`,
			`pages`.`title`,
			`pages`.`category`,
			`pages`.`posts`,
			`pages`.`readby`,
			`pages`.`bump`,
			`cat`.`mods_only`,
			`cat`.`title` AS `cat_title`
		FROM `pages`
			JOIN `cat` ON `pages`.`category` = `cat`.`id`
		WHERE
			" . implode(' AND ', $conditions) . $mods_only . "            
		ORDER BY
			`pages`.`bump` DESC 
		LIMIT $skip, $news_in_page
	");

	// masīvs, kas tiks atgriezts
	$arr_news = array();
	
	if ( !$latest ) {
		return $arr_news;
	}
	
	foreach ($latest as $late) {
	
		// statuss, kas norādīs, vai lietotājs rakstu ir lasījis
		$is_read = false;
		if (!empty($late->readby) && in_array($auth->id, unserialize($late->readby))) {
		   $is_read = true;
		}        
	
		$arr_news[] = array(
			$late->id, 
			$late->title, 
			$late->cat_title,
			$late->posts,
			$late->mods_only,
			$late->bump,
			$is_read
		);
	}
	
	return $arr_news;
}

/**
 *  Raksta komentāra vai tā atbildes pievienošana.
 *
 *  @param object   raksta dati no datubāzes
 */
function a_add_article_comment($article = null) {
	global $db, $auth, $remote_salt, $comments_per_page;
	
	if ($article == null || !isset($_POST['comment'])) {
		a_error('Pievienot neizdevās'); 
		return;
	}
	
	// drošības atslēga xsrf tipa uzbrukumiem
	$article_salt = substr(md5($article->id . $remote_salt . $auth->id), 0, 5);
	
	// pārbaudes
	if ($article->closed) {    
		a_error('Raksta komentēšana slēgta'); 
		return;        
	} else if (empty($_POST['comment'])) {    
		a_error('Tukšu komentāru nevar pievienot'); 
		return;        
	} 
	// drošības atslēgas pārbaude
	else if (!isset($_POST['safe']) || $_POST['safe'] != $article_salt) {
		a_error('no hacking, pls');
		return;
	} else {
	
		// pārbaude, vai tiek atbildēts kādam esošam komentāram
		$parent_id = 0;
		$comment = null;
		if (isset($_POST['parent_comment'])) {
			$parent_id = (int)$_POST['parent_comment'];
			$comment = $db->get_row("
				SELECT * FROM `comments` 
				WHERE 
					`id` = ".$parent_id." AND 
					`pid` = ".(int)$article->id." AND 
					`parent` = 0
			");
			if (!$comment) {
				a_error('Atbildāmais komentārs neeksistē'); 
				return;
			}
		}

		// komentāru saglabā datubāzē
		require(CORE_PATH . '/includes/class.comment.php');
		$addcom = new Comment();
		$addcom->add_comment($article->id, $auth->id, $_POST['comment'], 
							 0, $parent_id);
		
		// izveido adresi notifikācijai raksta autoram
		$total = $db->get_var("
			SELECT count(*) FROM `comments` 
			WHERE 
				`pid` = " . (int)$article->id . " AND 
				`parent` = 0 AND 
				`removed` = 0
		");
		if ($total > $comments_per_page) {
			$skip = '/com_page/' . floor($total / $comments_per_page);
		} else {
			$skip = '';
		}
		$url = '/read/' . $article->strid . $skip;
		
		// pievieno notifikāciju raksta autoram, ja tiek atbildēts
		if ($comment != null && $comment->author != $article->author) {
			notify($comment->author, 0, $comment->id, $url, 
				   textlimit(hide_spoilers($article->title), 64));
		}

		// atjauno raksta skaitliskos datus
		update_stats($article->category);
		$category = get_cat($article->category);
		if (!empty($category->parent)) {
			update_stats($category->parent);
		}
	}
}

/**
 *  Atgriezīs x jaunākos apbalvojumus lietotāja profilā.
 */
function a_fetch_awards($user_id, $award_count = 4) {
	global $db, $m, $img_server;
	
	$user_id = (int)$user_id;
	$award_count = (int)$award_count;
	
	if ($user_id < 0 || $award_count < 0 || $award_count > 10) {
		a_error('Kļūdaini apbalvojumu ielādes parametri');
		return;
	}
	
	$memcached_key = 'api_awards_'.$user_id.'-'.$award_count;
	
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
		$data = array(
			'count' => (int)$total,
			'list' => $awards
		);
		$m->set($memcached_key, $data, false, 900);
	}
	
	a_append(array('awards' => $data));
}
