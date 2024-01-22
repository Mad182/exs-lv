<?php

/**
 *  Metodes, kas norādītajā tekstā apstrādā adreses, smaidiņus, widgets u.c.
 *
 *  Pagaidām atbalstītie ārējo lapu widgets:
 *
 *      YouTube, Twitter, Deezer,
 *      Vine, Soundcloud, Instagram, Vimeo,
 *		imgur gifv video, gfycat
 */

/**
 *  Pēc vajadzības izsauc funkcijas, kas aizstāj noteiktu saturu tekstā
 *
 *  Nosaukums jāsaglabā, jo funkcija izmantota ļoti daudzās vietās
 *
 *  @param $txt                 apstrādājamais teksts
 *  @param $wide                vai rādīt platos YouTube video
 *  @param $disable_emotions    vai aizstāt smaidiņus
 *  @param $disable_embed       vai ievietot widgets
 *  @param $hide_spoilers       vai paslēpt spoilerus
 *  @return $txt
 */
function add_smile($txt, $wide = 0, $disable_emotions = 0, $disable_embed = 0, $hide_spoilers = true) {
	global $lang;

	// @coding.lv nerādīs smaidiņus, ja ierakstā ir koda gabals
	if (strpos($txt, 'prettyprint') !== false && $lang == 3) {
		$disable_emotions = true;
	}

	// smaidiņi
	if (!$disable_emotions) {
		$txt = insert_smilies($txt);
	}

	// pārveido vecās avataru adreses uz jaunajām
	$txt = str_replace('="/dati/bildes/useravatar/', '="//img.exs.lv/userpic/medium/', $txt);
	$txt = str_replace('="/dati/bildes/u_small/', '="//img.exs.lv/userpic/small/', $txt);
	$txt = str_replace('="/dati/bildes/u_large/', '="//img.exs.lv/userpic/large/', $txt);

	// visu iekš /dati/bildes lādē caur img.exs.lv cache
	$txt = str_replace('="/dati/bildes', '="//img.exs.lv/dati/bildes', $txt);

	// absolūtie ceļi, lai viss no /upload un /bildes rādītos arī m.exs.lv
	$txt = str_replace('="/upload/', '="//exs.lv/upload/', $txt);
	$txt = str_replace('="/bildes', '="//exs.lv/bildes', $txt);

	// saturā esošām adresēm pievieno "nofollow" atribūtu
	$txt = str_replace(' rel="nofollow"', '', $txt);
	$txt = str_replace(' href="http', ' rel="nofollow noopener noreferrer" href="http', $txt);

	// draudzīgajām un atbalstāmajām adresēm noņem "nofollow" atribūtu
	$dofollow_sites = get_sitelist('dofollow');

	foreach ($dofollow_sites as $site) {
		if (strpos($txt, $site) !== false) {
			$find = [
				' rel="nofollow noopener noreferrer" href="http://' . $site,
				' rel="nofollow noopener noreferrer" href="https://' . $site,
				' rel="nofollow noopener noreferrer" href="http://www.' . $site,
				' rel="nofollow noopener noreferrer" href="https://www.' . $site
			];
			$replace = [
				' href="http://' . $site,
				' href="https://' . $site,
				' href="http://www.' . $site,
				' href="https://www.' . $site
			];
			$txt = str_ireplace($find, $replace, $txt);
		}
	}

	// adreses, kas atrodas blacklistē, tiek aizstātas ar "/ES_SPAMOJU_SUDUS"
	$blacklisted_sites = get_sitelist('blacklisted');
	foreach ($blacklisted_sites as $site) {
		if (stripos($txt, $site) !== false) {
			$replace = [
				'http://' . $site,
				'http://www.' . $site,
				'https://' . $site,
				'https://www.' . $site
			];
			$txt = str_ireplace($replace, '/ES_SPAMOJU_SUDUS', $txt);
		}
	}

	$txt = str_replace([
		'.space.lv',
		'CoxFr2Kobuw',
		'MOBM1ODD',
		's.exs.lv/63',
		'?ref=',
		'servics-',
		'servces-',
		'.org/lan.',
		'4f200c32f12e7.jpg'
			], 'ES_SPAMOJU_SUDUS', $txt);

	$txt = str_replace([
		'/ref.php',
		'/referrer/'
			], '/ES_SPAMOJU_SUDUS/', $txt);


	// paslēps spoilerus
	if ($hide_spoilers && strpos($txt, 'spoiler') !== false) {
		$txt = preg_replace_callback(
				"/\[spoiler](.*?)\[\/spoiler]/isS", 'replace_spoiler', $txt
		);
	}

	// ievietos ārējo mājaslapu widgets
	if (!$disable_embed) {
		$txt = embed_widgets($txt, $wide);
	}

	if(stripos($txt, 'http://') !== false) {

		//force https for known supported sites
		$https_sites = get_sitelist('https');
		foreach ($https_sites as $site) {
			if (strpos($txt, $site) !== false) {
				$txt = str_ireplace('http://' . $site, 'https://' . $site, $txt);
			} elseif(stripos($site, '*') !== false) {

				//wildcard
				$check = str_replace('*', '', $site);
				if (strpos($txt, $check) !== false) {
					$txt = preg_replace('/http:\/\/([a-zA-Z0-9]+)'.$check.'/', 'https://$1'.$check, $txt);
				} 

			}
		}

		//auto add proxy to all jpg/png images over http
		if($pos = stripos($txt, 'src="http://')) {
			$data = substr($txt, $pos, 200);
			$data = explode('"', $data);
			if(stripos($data[1], '.jpg') || stripos($data[1], '.jpeg') || stripos($data[1], '.png')) {
				$find = str_ireplace('http://', '', $data[1]);
				$txt = str_ireplace('src="http://' . $find, 'src="https://images.weserv.nl/?url=' . $find, $txt);
				//error_log($find . "\n", 3, "/home/www/exs.lv/tmp/http-img.log");
			}
		}
	}

	$txt = str_replace(['https://m.ss.lv/', 'https://www.ss.lv/', 'https://m.ss.com/'], 'https://www.ss.com/', $txt);
	$txt = str_replace(['https://www.imgur.com', 'https://m.imgur.com'], 'https://imgur.com', $txt);

	return $txt;
}

/**
 *  Aizstās simbolu kombinācijas tekstā ar smaidiņiem
 *
 *  Šobrīd tiek izsaukta tikai no add_smile()
 *
 *  @param $txt apstrādājamais teksts
 *  @param $just_return TRUE - tikai atgriež smaidiņu masīvu
 *  @return $txt
 */
function insert_smilies($txt, $just_return = false) {
	global $img_server;

	$smilies = [
		':sweat:' => 'smiley-sweat.png',
		':o:' => 'smiley-surprise.png',
		':eek:' => 'smiley-eek.png',
		':roll:' => 'smiley-roll.png',
		':confused:' => 'smiley-confuse.png',
		':nerd:' => 'smiley-nerd.png',
		':sleep:' => 'smiley-sleep.png',
		':fat:' => 'smiley-fat.png',
		':twist:' => 'smiley-twist.png',
		':slim:' => 'smiley-slim.png',
		':money:' => 'smiley-money.png',
		':android:' => 'android.png',
		':dog:' => 'animal-dog.png',
		':monkey:' => 'animal-monkey.png',
		':pingvins:' => 'animal-penguin.png',
		':linux:' => 'animal-penguin.png',
		':windows:' => 'windows.png',
		':mac:' => 'mac-os.png',
		':applefag:' => 'mac-os.png',
		':bug:' => 'bug.png',
		':star:' => 'star.png',
		':zvaigzne:' => 'star.png',
		':cookie:' => 'cookie.png',
		':cookies:' => 'cookies.png',
		':burger:' => 'hamburger.png',
		':burgers:' => 'hamburger.png',
		':game:' => 'game.png',
		':apple:' => 'fruit.png',
		':candle:' => 'candle.png',
		':candle-white:' => 'candle-white.png',
		':latvija:' => 'latvija.gif',
		':audi:' => 'kissmyrings.gif',
		':shura:' => 'shura.gif',
		':geek:' => 'icon_geek.gif',
		':tease:' => 'tease.gif',
		':slims:' => 'ill.gif',
		':zzz:' => 'lazy.gif',
		':shock:' => 'shok.gif',
		':beer:' => 'beer.gif',
		':alus:' => 'beer.gif',
		':pohas:' => 'pohas.gif',
		':cepure:' => 'cepure.gif',
		':crazy:' => 'crazy.gif',
		':rokas:' => 'rokas.gif',
		':facepalm:' => 'facepalm.gif',
		':hihi:' => 'hihi.gif',
		':ile:' => 'loveexs.gif',
		':ban:' => 'ban.gif',
		':mjau:' => 'mjau.gif',
		':rock:' => 'rock.gif',
		//kolobok
		':drink:' => 'drink_mini.gif',
		':lol:' => 'lol_mini.gif',
		':happy:' => 'happy_mini.gif',
		':greeting:' => 'greeting_mini.gif',
		':cry:' => 'cray_mini.gif',
		':dance:' => 'dance_mini.gif',
		';(' => 'cray_mini2.gif',
		':acute:' => 'acute_mini.gif',
		':thumb:' => 'good_mini.gif',
		':aggressive:' => 'aggressive_mini.gif',
		':agresivs:' => 'aggressive_mini.gif',
		':beee:' => 'beee_mini.gif',
		':bomb:' => 'bomb_mini.gif',
		':puke:' => 'bo_mini.gif',
		':mrgreen:' => 'biggrin_mini.gif',
		':D' => 'biggrin_mini2.gif',
		':P' => 'blum_mini.gif',
		':blush:' => 'blush_mini.gif',
		':kiss:' => 'air_kiss_mini.gif',
		':angel:' => 'angel_mini.gif',
		':bored:' => 'boredom_mini.gif',
		':bye:' => 'bye_mini.gif',
		':chok:' => 'chok_mini.gif',
		':clap:' => 'clapping_mini.gif',
		':headbang:' => 'dash_mini.gif',
		':evil:' => 'diablo_mini.gif',
		'8=)' => 'dirol_mini.gif',
		':cool:' => 'dirol_mini.gif',
		':fool:' => 'fool_mini.gif',
		':heart:' => 'heart_mini.gif',
		':sirds:' => 'heart_mini.gif',
		':help:' => 'help_mini.gif',
		':laugh:' => 'laugh_mini.gif',
		':mad:' => 'mad_mini.gif',
		':mail:' => 'mail1_mini.gif',
		':mamba:' => 'mamba_mini.gif',
		':inlove:' => 'man_in_love_mini.gif',
		':mocking:' => 'mocking_mini.gif',
		':music:' => 'music_mini.gif',
		':nea:' => 'nea_mini.gif',
		':fingers:' => 'new_russian_mini.gif',
		':ok:' => 'ok_mini.gif',
		':pardon:' => 'pardon_mini.gif',
		':rofl:' => 'rofl_mini.gif',
		':rolleyes' => 'rolleyes_mini.gif',
		':rose:' => 'rose_mini.gif',
		':(' => 'sad_mini.gif',
		':sad:' => 'sad_mini2.gif',
		':think:' => 'scratch_one-s_head_mini.gif',
		':secret:' => 'secret_mini.gif',
		':shout:' => 'shout_mini.gif',
		':)' => 'smile_mini.gif',
		':sorry:' => 'sorry_mini.gif',
		':|' => 'connie_mini_huh.gif',
		':stop:' => 'stop_mini.gif',
		':dunno:' => 'unknw_mini.gif',
		':unsure:' => 'unsure_mini.gif',
		':vava:' => 'vava_mini.gif',
		':wacko:' => 'wacko_mini.gif',
		';)' => 'wink_mini.gif',
		':wink:' => 'wink_mini.gif',
		':yahoo:' => 'yahoo_mini.gif',
		':yes:' => 'yes_mini.gif',
		':yell:' => 'shout_mini.gif',
		':cat:' => 'connie_mini_kitty.gif',
		':minka:' => 'connie_mini_kitty.gif',
		':buck:' => 'connie_mini_buck.gif',
		':bump:' => 'connie_mini_bump.gif',
		':shifty:' => 'shifty.gif',
		':bulduris:' => 'bulduris.png',
		':agility:' => 'agility.png',
		':11:' => '11.png',
		':zagis:' => 'chainsaw.gif',
		':dickbutt:' => 'dickbutt.gif',
		':hektors:' => 'hektors.png'
	];
    
    // izmanto exs API moduļos, lai vienkārši atgrieztu smaidiņu sarakstu
    if ($just_return) {
        return $smilies;
    }

	foreach ($smilies as $key => $val) {
		if (strpos($txt, $key) !== false) { // speeds things up
			$txt = str_ireplace($key, ' <img src="' . $img_server . '/bildes/fugue-icons/' . $val . '" alt="' . $val . '" /> ', $txt);
		}
	}

	return $txt;
}

/**
 *  Aizstās noteiktas adreses tekstā ar ārēju lapu logrīkiem.
 *
 *  Atbalsta:
 *      - YouTube, Twitter
 *      - Deezer
 *      - Soundcloud, Vine, Instagram, Vimeo
 *		- imgur gif video, gfycat
 *
 *  @param $txt     apstrādājamais saturs
 *  @param $wide    vai rādīt platu video
 *  @return $txt
 */
function embed_widgets($txt, $wide = 0) {

	global $auth, $has_yt, $post_bump;

	// lai nebūtu problēmu, ja vienā HTML paragrāfā uzreiz aiz, piemēram,
	// YouTube saites ir kāda cita saite, .* vietā jābūt [^>]*,
	// kas attiecīgajās vietās neļaus atrasties ">", citādi regex
	// var vienā piegājienā paķert abas saites

	//don't embed old videos
	$embed = true;
	//if(!empty($post_bump) && $post_bump < strtotime('-2 weeks')) {
	//	$embed = false;
	//}

	// youtube videos
	if (strpos($txt, 'youtu') !== false && $embed) {
		if ($wide) {
			$fn = 'get_youtube_video';
		} else {
			$fn = 'get_youtube_video_small';
		}
		$txt = preg_replace_callback(
				"#(<code class=\"prettyprint\">(.*?)</code>)(*SKIP)(*F)|(^|[\n ]|<a([^>]*?)>)https?://(www\.)?youtube\.com/watch\?v=([a-zA-Z0-9\-_]+)(([^<]*?)</a>)?#im", $fn, $txt
		);
		$txt = preg_replace_callback(
				"#(<code class=\"prettyprint\">(.*?)</code>)(*SKIP)(*F)|(^|[\n ]|<a([^>]*?)>)https?://(www\.)?youtu\.be/([a-zA-Z0-9\-_]+)(([^<]*?)</a>)?#im", $fn, $txt
		);
	} elseif(strpos($txt, 'youtu') !== false) {
		$has_yt = true;
	}

	// twitter posts
	if (strpos($txt, 'twitter') !== false) {
		$txt = preg_replace_callback(
				"#(<code class=\"prettyprint\">(.*?)</code>)(*SKIP)(*F)|(^|[\n ]|<a([^>]*?)>)https?://(www\.)?twitter\.com/.+?/status(es)?/([a-zA-Z0-9]+)(([^<]*?)</a>)?#im", 'embed_twitter', $txt
		);

		if (strpos($txt, 'mobile.twitter') !== false) {
			$txt = preg_replace_callback(
					"#(<code class=\"prettyprint\">(.*?)</code>)(*SKIP)(*F)|(^|[\n ]|<a([^>]*?)>)https?://(mobile\.)?twitter\.com/.+?/status(es)?/([a-zA-Z0-9]+)(([^<]*?)</a>)?#im", 'embed_twitter', $txt
			);
		}

	}

	// deezer track or album, or even playlist
	if ($auth->ok && strpos($txt, 'deezer') !== false && $embed) {
		$txt = preg_replace_callback(
				"#(^|[\n ]|<a[^>]*?>)https?://(www\.)?deezer\.com/(track|album|playlist)/([0-9]+)(</a>)?#im", 'embed_deezer', $txt
		);
	}

	// vine videos
	if ($auth->ok && strpos($txt, 'vine') !== false && $embed) {
		$txt = preg_replace_callback(
				"#(^|[\n ]|<a[^>]*?>)(https?:\/\/vine\.co\/v\/([a-z0-9]+)\/?)(</a>)?#im", 'embed_vine', $txt
		);
	}

	// soundcloud tracks, users, and playlists
	if (($auth->ok && strpos($txt, 'soundcloud') !== false ||
			strpos($txt, 'snd.sc') !== false) && $embed) {
		// tā kā soundcloud saites mēdz būt ļoti garas, htmlpurifier tās var
		// saīsināt, tāpēc īstā saite jānolasa no "href" atribūta;
		$txt = preg_replace_callback(
			"#(^|[\n ]|<a[^>]*?href=\"(https?:\/\/(soundcloud\.com|snd\.sc)\/([a-z0-9_\/\-]+))\"[^>]*?>)(https?:\/\/(soundcloud\.com|snd\.sc)\/([a-z0-9_\.\/\-	]+))(</a>)?#im", 'embed_soundcloud', $txt
		);
	}

	// instagram images
	if (strpos($txt, 'instagram') !== false) {
		$txt = preg_replace_callback(
				"#(^|[\n ]|<a[^>]*?>)(https?:\/\/(www\.)?instagram.com\/p\/([a-z0-9_\-]+))([^<]*?</a>)#im", 'embed_instagram', $txt
		);
	}

	// vimeo video
	if ($auth->ok && strpos($txt, 'vimeo') !== false && $embed) {
		$txt = preg_replace_callback(
				"#(^|[\n ]|<a[^>]*?>)(https?:\/\/vimeo\.com\/([a-z0-9]+)\/?)(</a>)?#im", 'embed_vimeo', $txt
		);
	}

	// gifv video
	if (strpos($txt, 'gifv') !== false || strpos($txt, 'webm') !== false && $embed) {
		$txt = preg_replace_callback(
				"#(^|[\n ]|<a(.*?)>)https?:\/\/i\.imgur\.com\/([A-Za-z0-9]+)\.(gifv|webm)\/?#im", 'embed_gifv_imgur', $txt
		);
	}

	// gfycat
	if (strpos($txt, 'gfycat') !== false && $embed) {

		$txt = preg_replace_callback(
				"#(^|[\n ]|<a([^>]*?)>)https?:\/\/([a-z0-9]+)\.gfycat\.com\/([A-Za-z0-9]+)\.(gifv|webm|mp4)(</a>)?#im", 'embed_gifv_gfycat', $txt
		);
		$txt = preg_replace_callback(
				"#(^|[\n ]|<a([^>]*?)>)https?:\/\/([a-z0-9]+)\.gfycat\.com\/([A-Za-z0-9]+)\/(</a>)?#im", 'embed_gifv_gfycat', $txt
		);
		$txt = preg_replace_callback(
				"#(^|[\n ]|<a([^>]*?)>)https?:\/\/([g])fycat\.com\/([A-Za-z0-9]+)\/(</a>)?#im", 'embed_gifv_gfycat', $txt
		);
	}

	return $txt;
}

/**
 *  Atgriezīs iframe ar YouTube video
 *
 *  @param $matches video parametri
 */
function get_youtube_video($matches) {
	return embed_youtube($matches, 1);
}

/**
 *  Atgriezīs iframe ar mazāka izmēra YouTube video
 *
 *  @param $matches video parametri
 */
function get_youtube_video_small($matches) {
	return embed_youtube($matches, 0);
}

/**
 *  Atgriezīs iframe ar YouTube video
 *
 *  @param $matches video parametri
 *  @param $wide    vai atgriezt plata izmēra video
 */
function embed_youtube($matches, $wide = 0) {

	global $has_yt;
	$has_yt = true;

	$safe = mkslug($matches[6], false, false);

	$width = 380;
	$height = 240;
	$vq = 'large';
	if ($wide) {
		$width = 520;
		$height = 290;
		$vq = 'hd720';
	}

	// izmanto h, lai norādītu kā parametru javascriptā
	$videocode = '<div class="c"></div><div class="auto-embed" ';
	$videocode .= 'style="width:' . $width . 'px;">';
	$videocode .= '<iframe class="youtube-player" type="text/html" ';
	$videocode .= 'width="' . $width . '" height="' . $height . '" ';
	$videocode .= 'src="https://www.youtube.com/embed/' . $safe;
	$videocode .= '?wmode=transparent&autoplay=1&autohide=1&hl=lv_LV&vq=' . $vq . '&origin=';
	$videocode .= urlencode('http://exs.lv') . '" frameborder="0"';
	$videocode .= ' webkitallowfullscreen mozallowfullscreen allowfullscreen>';
	$videocode .= '</iframe><br><a title="Atvērt video mājas lapā" ';
	$videocode .= 'href="https://www.youtube.com/watch?v=' . $safe . '" ';
	$videocode .= 'target="_blank" rel="nofollow">YouTube video</a> ';
	$videocode .= '<div class="c"></div></div>';
	$videocode = h($videocode);

	// saturs, uz kura nospiežot, caur javascript ielādēs $videocode
	$return = '<div><div class="auto-embed-placeholder">';
	$return .= '<img style="width:240px;height:180px" ';
	$return .= 'src="https://i4.ytimg.com/vi/' . $safe . '/0.jpg" ';
	$return .= 'alt="" /><a class="play-button" ';
	$return .= 'onclick="$(this).parent().parent().html(\'' . $videocode . '\');';
	$return .= 'return false;" title="Atskaņot video" ';
	$return .= 'rel="nofollow" ';
	$return .= 'href="https://www.youtube.com/watch?v=' . $safe . '"><span>';
	$return .= '</span></a></div></div>';

	return $return;
}

/**
 *  Atgriež YouTube video informāciju
 *
 *  @param $videoid
 *  @param $force   vai saglabāt informāciju atkārtoti
 *  @return $data   objekts ar video informāciju
 */
function get_youtube($videoid, $force = false) {
    return false;
}

/**
 *  Aizstāj YouTube video adreses ar video nosaukumiem
 *
 *  @param $text apstrādājamais saturs
 *  @return $text
 */
function youtube_title($text) {
	return $text;
}

/**
 *  Callback metode Twitter ierakstu iekļaušanai tekstā
 *
 *  Izveidoto HTML iekešo Memcached (30 min)
 *
 *  @param $params        ieraksta parametri
 *  @return $tweet_html   iframe ar tvītu
 */
function embed_twitter($params) {
	global $m, $tpl_options;

	$maxwidth = 400;
	if ($tpl_options === 'no-left') {
		$maxwidth = 520;
	}

	// nolasa no Memcached vai izveido iframe saturu
	$tweet_unique = 'tweet_' . $params[7] . '_' . $maxwidth;
	if (($tweet_html = $m->get($tweet_unique)) === false) {
		$tweet_html = $params[0];

		$response = curl_get('https://api.twitter.com/1/statuses/oembed.json?id=' . $params[7] . '&align=center&maxwidth=' . $maxwidth);
		if (!empty($response)) {
			$tweet = json_decode($response);
			if (empty($tweet->error) && !empty($tweet->html)) {
				$tweet_html = $tweet->html;
			}
		}
		$m->set($tweet_unique, $tweet_html, 7200);
	}

	return $tweet_html;
}

/**
 *  Callback metode Deezer logrīka iekļaušanai tekstā.
 *
 *	@see https://developers.deezer.com/musicplugins/player
 */
function embed_deezer($params) {

	// [0] pilns "notvertais" saturs
	// [1] <a...>
	// [2] www.
	// [3] playlist|album|track
	// [4] ieraksts ID
	// [5] </a>

	$type = 'tracks';
	$format = 'classic';
	$width = 400;
	$height = 100;
	
	if ($params[3] === 'playlist' || $params[3] === 'album') {
		$type = $params[3];
		$format = 'square';
		$width = 200;
		$height = 200;
	}
	
	return	'<p><iframe class="embedded-iframe" scrolling="no" '.
			'frameborder="0" style="margin:5px 0" allowTransparency="true" '.
			'src="https://www.deezer.com/plugins/player?format='.$format.
			'&autoplay=false&playlist=false&width='.$width.'&height='.$height.
			'&color=007FEB&layout=dark&size=medium&type='.$type.
			'&id='.(int)$params[4].'&title=&app_id=1" '.
			'width="'.$width.'" height="'.$height.'"></iframe></p>';
}

/**
 *  Callback metode Vine video iekļaušanai tekstā.
 *
 *	@see http://blog.vine.co/post/55514921892/embed-vine-posts
 *		 https://dev.twitter.com/web/vine/oembed
 */
function embed_vine($params) {
	global $m;

	// [0] pilns "notvertais" saturs
	// [1] <a...>
	// [2] adrese uz vine video
	// [3] video parametrs
	// [4] </a>

	$encoded_url = urlencode(strip_tags($params[3]));

	return	'<iframe class="embedded-iframe vine-embed" '.
			'src="https://vine.co/v/'.$encoded_url.'/embed/simple"'.
			'width="320" height="320" frameborder="0">'.
			'</iframe><script async src="'.
			'//platform.vine.co/static/scripts/embed.js"'.
			'charset="utf-8"></script>';
}

/**
 *  Callback metode Soundcloud dziesmu iekļaušanai tekstā.
 *
 *  Izveidoto HTML iekešo Memcached (30 min).
 */
function embed_soundcloud($params) {
	global $m;

	// tā kā soundcloud saites mēdz būt garas, htmlpurifier var saīsināt
	// starp <a> un </a> esošo tekstu, tāpēc to nevajadzētu izmantot
	
	// [0] pilns "notvertais" saturs
	// [1] <a...>
	// [2] adrese nesaīsinātā formā no "href" atribūta
	// [3] "soundcloud.com" vai "snd.sc" (no "href" adreses)
	// [4] adreses parametri pēc / (no "href" adreses)
	// [5] adrese no <a></a> tagu vidus (var būt saīsināta)
	// [6] "soundcloud.com" vai "snd.sc" (no "<a>...</a>" adreses)
	// [7] adreses parametri pēc / (no "<a>...</a>" adreses)
	// [8] </a>

	$max_height = 320;
	$max_width = 450;

	// ja norādīta specifiska dziesma, augstums nepieciešams visai neliels
	if (isset($params[4]) && !empty($params[4])) {
		$max_height = 130;
	}

	// nolasa no Memcached vai arī tajā ieraksta iframe saturu
	if (($scloud_html = $m->get('scloud_' . md5($params[4]))) === false) {

		// izveido adresi, kas atgriež JSON formāta datus par ierakstā
		// iekļauto adresi; no JSON var atlasīt iframe saturu
		$url  = 'https://soundcloud.com/oembed?format=json';
		$url .= '&maxwidth=' . $max_width . '&maxheight=' . $max_height;
		$url .= '&url=' . urlencode(strip_tags($params[2]));

		$data = '';
		$response = curl_get($url);
		
		if (!empty($response)) {			
			$data = json_decode($response);		

			if ($data !== '' && !empty($data->html)) {
				
				// šis paslēpj kvadrātformas attēlu dziesmas sānā
				/* $data->html = str_replace('show_artwork=true',
				  'show_artwork=false',
				  $data->html); */
				
				// šis paslēpj fona attēlu
				$scloud_html = str_replace(
					'visual=true', 'visual=false', $data->html);
			}
		}

		// ja nekas nav izdevies, iekešo sākotnējo HTML saturu
		if (empty($scloud_html)) {
			$scloud_html = $params[0];
		}
		$m->set('scloud_' . md5($params[4]), $scloud_html, 7200);
	}

	return $scloud_html;
}

/**
 *  Callback metode Instagram attēlu iekļaušanai tekstā.
 */
function embed_instagram($params) {
	
	// [0] pilns "notvertais" saturs
	// [1] <a...>
	// [2] adrese uz instagram attēlu
	// [3] www.
	// [4] attēla parametrs
	// [5] </a> vai /</a>

	return	'<iframe class="embedded-iframe" src="//instagram.com/p/'.
			urlencode($params[4]) . '/embed/" '.
			'width="350" height="450" frameborder="0" '.
			'scrolling="no" allowtransparency="true"></iframe>';
}

/**
 *  Callback metode Vimeo video iekļaušanai tekstā.
 */
function embed_vimeo($params) {

	global $has_yt;
	$has_yt = true;
	
	// [0] pilns "notvertais" saturs
	// [1] <a...>
	// [2] adrese uz vimeo video
	// [3] video parametrs
	// [4] </a>

	return	'<iframe class="embedded-iframe" src="//player.vimeo.com/video/'.
			urlencode($params[3]) . '?badge=0&byline=0" '.
			'width="520" height="300" frameborder="0" '.
			'webkitallowfullscreen mozallowfullscreen '.
			'allowfullscreen></iframe>';
}

/**
 *  Callback metode gfycat gifv/mp4/webm failu embedošanai
 *
 *  @param $params  video parametri
 *  @return $html   iframe ar video
 */
function embed_gifv_gfycat($params) {

	global $m;
	
	$cache_key = 'gify_' . md5($params[4]);
	
	if (($html = $m->get($cache_key)) === false) {

		$width = 560;
		$height = 400;

		$json = curl_get('https://gfycat.com/cajax/get/' . $params[4]);
		if(!empty($json)) {
			$jparams = json_decode($json);
	
			if(!empty($jparams->gfyItem->width) && !empty($jparams->gfyItem->height)) {
	
				if($jparams->gfyItem->width > 560) {
					$height = floor($jparams->gfyItem->height*(560/$jparams->gfyItem->width));
					$width = 560;
				} else {
					$height = $jparams->gfyItem->height;
					$width = $jparams->gfyItem->width;
				}
	
			}
		}

		$html = '<iframe class="embedded-iframe" src="//gfycat.com/ifr/'.h($params[4]).'" ';
		$html .= 'allowfullscreen="" frameborder="0" scrolling="no" ';
		$html .= 'style="-webkit-backface-visibility: hidden;-webkit-transform: scale(1);" ';
		$html .= 'width="' . (int)$width . '" height="' . (int)$height . '"></iframe>';
		
		$m->set($cache_key, $html, 7200);
	
	}

	return $html;
}

/**
 *  Callback metode imgur gifv failu embedošanai
 *
 *  @param $params        video parametri
 *  @return $html   iframe ar video
 */
function embed_gifv_imgur($params) {

	$html = '<div style="text-align:center">';
	$html .= '<blockquote class="imgur-embed-pub" data-context="false" lang="en" data-id="'.h($params[3]).'"></blockquote>';
	$html .= '<script async src="//s.imgur.com/min/embed.js" charset="utf-8"></script>';
	$html .= '</div>';

	return $html;
}

/**
 *  Paslēpj [spoiler] tagos ievietoto saturu
 *
 *  @param $text    apstrādājamais saturs
 *  @return $text
 */
function replace_spoiler($text) {

	$text = str_replace(['<p>', '</p>', '%5B/spoiler%5D'], ['<br>', '<br>', ''], $text[1]);

	$content = '<span class="spoiler"><a href="javascript:void(0);" ';
	$content .= 'class="spoiler-title" title="Slēpt/rādīt spoilera saturu">';
	$content .= 'Rādīt spoileri</a><br><span style="display:none" ';
	$content .= 'class="spoiler-content">' . $text . '</span></span>';

	return $content;
}

/**
 *  Dzēš [spoiler] tagos ievietoto saturu
 *
 *  Izmantojams vietās, kur teksts tiek apgriezts, piemēram,
 *  lapas sānā esošajos miniblogu fragmentos
 *
 *  @param $text    apstrādājamais saturs
 *  @return $text
 */
function hide_spoilers($text) {
	if (strpos($text, 'spoiler') !== false) {
		$text = preg_replace('/\[spoiler\](.*)\[\/spoiler\]/is', "(spoiler) ", $text);
		$text = str_replace('  ', ' ', $text);
	}
	return $text;
}

