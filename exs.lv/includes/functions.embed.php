<?php

/**
 *  Metodes, kas norādītajā tekstā apstrādā adreses, smaidiņus, widgets u.c.
 *
 *  Pagaidām atbalstītie ārējo lapu widgets:
 *
 *      YouTube, Twitter, Spotify, Deezer,
 *      Vine, Soundcloud, Instagram, Vimeo
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
 *  @return $txt
 */
function add_smile($txt, $wide = 0, $disable_emotions = 0, $disable_embed = 0) {
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
	$txt = str_replace(' href="http', ' rel="nofollow" href="http', $txt);

	// draudzīgajām un atbalstāmajām adresēm noņem "nofollow" atribūtu
	$dofollow_sites = get_sitelist('dofollow');
	foreach ($dofollow_sites as $site) {
		if (strpos($txt, $site) !== false) {
			$find = array(
				' rel="nofollow" href="http://' . $site,
				' rel="nofollow" href="https://' . $site,
				' rel="nofollow" href="http://www.' . $site,
				' rel="nofollow" href="https://www.' . $site
			);
			$replace = array(
				' href="http://' . $site,
				' href="https://' . $site,
				' href="http://www.' . $site,
				' href="https://www.' . $site
			);
			$txt = str_ireplace($find, $replace, $txt);
		}
	}

	// adreses, kas atrodas blacklistē, tiek aizstātas ar "/ES_SPAMOJU_SUDUS"
	$blacklisted_sites = get_sitelist('blacklisted');
	foreach ($blacklisted_sites as $site) {
		if (stripos($txt, $site) !== false) {
			$replace = array(
				'http://' . $site,
				'http://www.' . $site,
				'https://' . $site,
				'https://www.' . $site
			);
			$txt = str_ireplace($replace, '/ES_SPAMOJU_SUDUS', $txt);
		}
	}

	// bilžu hostingi, kas neatbalsta HTTPS
	$image_sites = get_sitelist('image');
	foreach ($image_sites as $site) {
		if (stripos($txt, $site) !== false) {
			$find = array(
				'src="http://' . $site,
				'src="http://www.' . $site
			);
			$replace = array(
				'src="https://images.weserv.nl/?url=' . $site,
				'src="https://images.weserv.nl/?url=www.' . $site
			);
			$txt = str_ireplace($find, $replace, $txt);
		}
	}

	$txt = str_replace(array(
		'.space.lv',
		'CoxFr2Kobuw',
		'MOBM1ODD',
		's.exs.lv/63',
		'?ref=',
		'servics-',
		'servces-',
		'.org/lan.',
		'4f200c32f12e7.jpg'
			), 'ES_SPAMOJU_SUDUS', $txt);

	$txt = str_replace(array(
		'/ref.php',
		'/referrer/'
			), '/ES_SPAMOJU_SUDUS/', $txt);


	// paslēps spoilerus
	if (strpos($txt, 'spoiler') !== false) {
		$txt = preg_replace_callback(
				"/\[spoiler](.*?)\[\/spoiler]/isS", 'replace_spoiler', $txt
		);
	}

	// ievietos ārējo mājaslapu widgets
	if (!$disable_embed) {
		$txt = embed_widgets($txt, $wide);
	}

	//http/https support
	$https_sites = get_sitelist('https');
	foreach ($https_sites as $site) {
		if (strpos($txt, $site) !== false) {
			$txt = str_ireplace('http://' . $site, 'https://' . $site, $txt);
		}
	}

	return $txt;
}

/**
 *  Aizstās simbolu kombinācijas tekstā ar smaidiņiem
 *
 *  Šobrīd tiek izsaukta tikai no add_smile()
 *
 *  @param $txt apstrādājamais teksts
 *  @return $txt
 */
function insert_smilies($txt) {
	global $img_server;

	$smilies = array(
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
	);

	foreach ($smilies as $key => $val) {
		if (strpos($txt, $key) !== false) { // speeds things up
			$txt = str_replace($key, ' <img src="' . $img_server . '/bildes/fugue-icons/' . $val . '" alt="' . $val . '" /> ', $txt);
		}
	}

	return $txt;
}

/**
 *  Aizstās noteiktas adreses tekstā ar ārēju lapu widgetiem
 *
 *  Šobrīd tiek izsaukta tikai no add_smile()
 *
 *  Atbalsta:
 *      - YouTube
 *      - Twitter
 *      - Spotify
 *      - Deezer
 *      - Vine
 *      - Soundcloud
 *      - Instagram
 *      - Vimeo
 *
 *  @param $txt     apstrādājamais saturs
 *  @param $wide    vai rādīt platu video
 *  @return $txt
 */
function embed_widgets($txt, $wide = 0) {

	// youtube videos
	if (strpos($txt, 'youtu') !== false) {
		if ($wide) {
			$fn = 'get_youtube_video';
		} else {
			$fn = 'get_youtube_video_small';
		}
		$txt = preg_replace_callback(
				"#(<code class=\"prettyprint\">(.*?)</code>)(*SKIP)(*F)|(^|[\n ]|<a(.*?)>)https?://(www\.)?youtube\.com/watch\?v=([a-zA-Z0-9\-_]+)((.*?)</a>)?#im", $fn, $txt
		);
		$txt = preg_replace_callback(
				"#(<code class=\"prettyprint\">(.*?)</code>)(*SKIP)(*F)|(^|[\n ]|<a(.*?)>)https?://(www\.)?youtu\.be/([a-zA-Z0-9\-_]+)((.*?)</a>)?#im", $fn, $txt
		);
	}

	// twitter posts
	if (strpos($txt, 'twitter') !== false) {
		$txt = preg_replace_callback(
				"#(<code class=\"prettyprint\">(.*?)</code>)(*SKIP)(*F)|(^|[\n ]|<a(.*?)>)https?://(www\.)?twitter\.com/.+?/status(es)?/([a-zA-Z0-9]+)((.*?)</a>)?#im", 'embed_twitter', $txt
		);
	}

	// spotify
	if (strpos($txt, 'spotify') !== false) {
		$txt = preg_replace_callback(
				"#(<code class=\"prettyprint\">(.*?)</code>)(*SKIP)(*F)|(^|[\n ]|<a(.*?)>)https?://(open|play)\.spotify\.com/([a-zA-Z0-9]+)/([a-zA-Z0-9]+)((.*?)</a>)?#im", 'embed_spotify', $txt
		);
	}

	// deezer track or album, or even playlist
	if (strpos($txt, 'deezer') !== false) {
		$txt = preg_replace_callback(
				"#(^|[\n ]|<a(.*?)>)https?://(www\.)?deezer\.com/(track|album|playlist)/([0-9]+)((.*?)</a>)?#im", 'embed_deezer', $txt
		);
	}

	// vine videos
	if (strpos($txt, 'vine') !== false) {
		$txt = preg_replace_callback(
				"#(^|[\n ]|<a(.*?)>)https?:\/\/vine\.co\/v\/([a-z0-9]+)\/?#im", 'embed_vine', $txt
		);
	}

	// soundcloud tracks, users, and playlists
	if (strpos($txt, 'soundcloud') !== false ||
			strpos($txt, 'snd.sc') !== false) {
		$txt = preg_replace_callback(
				"#(^|[\n ]|<a.*?href=\"(.*?)\".*?>)(https?:\/\/(soundcloud\.com|snd\.sc)\/([a-z0-9]+)(.*?))</a>#im", 'embed_soundcloud', $txt
		);
	}

	// instagram images
	if (strpos($txt, 'instagram') !== false) {
		$txt = preg_replace_callback(
				"#(^|[\n ]|<a.*?href=\"(.*?)\".*?>)(https?:\/\/instagram.com\/p\/([a-z0-9_\-]+)(.*?))</a>#im", 'embed_instagram', $txt
		);
	}

	// vimeo video
	if (strpos($txt, 'vimeo') !== false) {
		$txt = preg_replace_callback(
				"#(^|[\n ]|<a(.*?)>)https?:\/\/vimeo\.com\/([a-z0-9]+)\/?#im", 'embed_vimeo', $txt
		);
	}

	// gifv video
	if (strpos($txt, 'gifv') !== false || strpos($txt, 'webm') !== false) {
		$txt = preg_replace_callback(
				"#(^|[\n ]|<a(.*?)>)https?:\/\/i\.imgur\.com\/([a-z0-9]+)\.(gifv|webm)\/?#im", 'embed_gifv_imgur', $txt
		);
	}
	
	//gfycat
	if (strpos($txt, 'gfycat') !== false) {
		$txt = preg_replace_callback(
				"#(^|[\n ]|<a(.*?)>)https?:\/\/fat\.gfycat\.com\/([A-Za-z0-9]+)\.(gifv|webm|mp4)\/?#im", 'embed_gifv_gfycat', $txt
		);
		$txt = preg_replace_callback(
				"#(^|[\n ]|<a(.*?)>)https?:\/\/www\.gfycat\.com\/([A-Za-z0-9]+)\/?#im", 'embed_gifv_gfycat', $txt
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

	$safe = mkslug($matches[6], false, false);
	$video = get_youtube($safe);

	$title = str_replace("'", "&#39;", h(textlimit(
							stripslashes($video->yt_title), 100)));
	$title = str_replace("&amp;amp;", "&amp;", $title);

	$width = 380;
	$height = 240;
	if ($wide) {
		$width = 520;
		$height = 290;
	}

	// izmanto h, lai norādītu kā parametru javascriptā
	$videocode = '<div class="c"></div><div class="auto-embed" ';
	$videocode .= 'style="width:' . $width . 'px;">';
	$videocode .= '<iframe class="youtube-player" type="text/html" ';
	$videocode .= 'width="' . $width . '" height="' . $height . '" ';
	$videocode .= 'src="https://www.youtube.com/embed/' . $safe;
	$videocode .= '?wmode=transparent&autoplay=1&origin=';
	$videocode .= urlencode('http://exs.lv') . '" frameborder="0"';
	$videocode .= ' webkitallowfullscreen mozallowfullscreen allowfullscreen>';
	$videocode .= '</iframe><br /><a title="Atvērt video mājas lapā" ';
	$videocode .= 'href="https://www.youtube.com/watch?v=' . $safe . '" ';
	$videocode .= 'target="_blank" rel="nofollow">YouTube video</a> ';
	$videocode .= '<strong>' . $title . '</strong><div class="c"></div></div>';
	$videocode = h($videocode);

	// saturs, uz kura nospiežot, caur javascript ielādēs $videocode
	$return = '<div><div class="auto-embed-placeholder">';
	$return .= '<img width="240" height="180" ';
	$return .= 'src="https://i4.ytimg.com/vi/' . $safe . '/0.jpg" ';
	$return .= 'alt="' . $title . '" /><a class="play-button" ';
	$return .= 'onclick="$(this).parent().parent().html(\'' . $videocode . '\');';
	$return .= 'return false;" title="Atskaņot ' . $title . '" ';
	$return .= 'rel="nofollow" ';
	$return .= 'href="https://www.youtube.com/watch?v=' . $safe . '"><span>';
	$return .= '<span>' . $title . '</span></span></a></div></div>';

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
	global $db, $m;

	// saglabā informāciju Memcached uz stundu
	if ($force || !($data = $m->get('yt_' . $videoid))) {

		$data = $db->get_row("
            SELECT * FROM `ytlocal`
            WHERE `yt_id` = '" . sanitize($videoid) . "'
        ");

		// saglabā info arī datubāzē, ja tādas tur nav
		if (empty($data)) {

			require_once(LIB_PATH . '/youtube/youtube.lib.php');
			$yt = new Youtube(array('user' => 'google', 'limit' => 5));
			$video = $yt->getSingleVideo($videoid);

			$data = new Stdclass;
			$data->yt_title = esr($video['title'], 'youtube.com');
			$data->yt_description = esr($video['description'], '');
			$data->yt_time = esr($video['duration'], '0:00');
			$data->yt_restricted = 0;
			$data->yt_id = $videoid;

			$db->query("
                INSERT INTO `ytlocal`
                    (yt_id, yt_title, yt_description, yt_restricted, yt_time)
                VALUES(
                    '" . sanitize($videoid) . "',
                    '" . sanitize($data->yt_title) . "',
                    '" . sanitize($data->yt_description) . "',
                    '" . $data->yt_restricted . "',
                    '" . sanitize($data->yt_time) . "'
                )
            ");
		}

		$m->set('yt_' . $videoid, $data, false, 3600);
	}
	return $data;
}

/**
 *  Aizstāj YouTube video adreses ar video nosaukumiem
 *
 *  @param $text apstrādājamais saturs
 *  @return $text
 */
function youtube_title($text) {
	if (strpos($text, 'youtu') !== false) {
		$text = preg_replace_callback("#(^|[\n ]|<a(.*?)>)http://(www\.)?youtube\.com/watch\?v=([a-zA-Z0-9\-_]+)((.*?)</a>)?#im", 'youtube_title_callback', $text);
		$text = preg_replace_callback("#(^|[\n ]|<a(.*?)>)http://(www\.)?youtu\.be/([a-zA-Z0-9\-_]+)((.*?)</a>)?#im", 'youtube_title_callback', $text);
	}
	return $text;
}

/**
 *  Callback metode YouTube video nosaukumu iekļaušanai tekstā
 *
 *  @param $matches YouTube video parametri
 *  @return string  video nosaukums
 */
function youtube_title_callback($matches) {
	$safe = mkslug($matches[4], false, false);
	$video = get_youtube($safe);
	return ' Video: ' . $video->yt_title . ' ';
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
		$m->set($tweet_unique, $tweet_html, false, 1800);
	}

	return $tweet_html;
}

/**
 *  Callback metode Spotify ierakstu iekļaušanai tekstā
 *
 *  Izveidoto HTML iekešo Memcached (30 min)
 *
 *  @param $params          ieraksta parametri
 *  @return $spotify_html   iframe ar dziesmām
 */
function embed_spotify($params) {
	global $m;

	// $matches[0] - ieraksta adrese
	// nolasa no Memcached vai izveido iframe saturu
	if (($spotify_html = $m->get('spotify_' . md5($params[0]))) === false) {
		$spotify_html = $params[0];

		$response = curl_get('http://api.embed.ly/1/oembed?url='
				. urlencode(strip_tags($params[0])));
		if (!empty($response)) {
			$spotify = json_decode($response);
			if (empty($spotify->error) && !empty($spotify->html)) {
				$spotify_html = $spotify->html;
			}
		}
		$m->set('spotify_' . md5($params[0]), $spotify_html, false, 1800);
	}

	return $spotify_html;
}

/**
 *  Callback metode Deezer ierakstu iekļaušanai tekstā
 *
 *  @param $params          ieraksta parametri
 *  @return $deezer_html    iframe ar dziesmām
 */
function embed_deezer($params) {

	// $matches[4] - ieraksta veids
	// $matches[5] - ieraksta id

	$type = 'tracks';
	$height = 180; // izmērs pietiek, lai redzētu vienu dziesmu
	// izmērs atbilst 6 dziesmām sarakstā
	if ($params[4] === 'album') {
		$type = 'album';
		$height = 375;
	} elseif ($params[4] === 'playlist') {
		$type = 'playlist';
		$height = 375;
	}

	$deezer_html = '<p><iframe class="embedded-iframe" scrolling="no" frameborder="0" ';
	$deezer_html .= 'allowTransparency="true" ';
	$deezer_html .= 'src="https://www.deezer.com/plugins/player?';
	$deezer_html .= 'autoplay=false&playlist=true&width=300';
	$deezer_html .= '&height=' . (int) $height . '&cover=false&type=' . $type;
	$deezer_html .= '&id=' . (int) $params[5] . '&title=&format=vertical';
	$deezer_html .= '&app_id=undefined" width="300" ';
	$deezer_html .= 'height="' . (int) $height . '"></iframe></p>';

	return $deezer_html;
}

/**
 *  Callback metode Vine video iekļaušanai tekstā
 *
 *  Izveidoto HTML iekešo Memcached (30 min)
 *
 *  @param $params       video parametri
 *  @return $vine_html   iframe ar video
 */
function embed_vine($params) {
	global $m;

	// $params[0] - <a..href=".."..>http://vine.co/v/..
	// $params[1] - <a..href="http://vine.co/v/..">
	// $params[2] - rel=".." href="http://vine.co/v/.."
	// $params[3] - video ID (tas, kas seko aiz /v/)
	// nolasa no Memcached vai arī tajā ieraksta iframe saturu
	if (($vine_html = $m->get('vine_' . md5($params[3]))) === false) {

		$encoded_url = urlencode(strip_tags(
						'https://vine.co/v/' . $params[3]));
		$url = 'https://api.embed.ly/1/oembed?url=' .
				$encoded_url . '&maxwidth=320&maxheight=320';

		$response = curl_get($url);
		if (!empty($response)) {
			$vine = json_decode($response);
			if (isset($vine->html)) {
				$vine_html = $vine->html;
				// imho glītāk, ja iframe nav centrēts
				$vine_html = str_replace(
						'></iframe>', ' style="margin-left:0"></iframe>', $vine_html);
			}
		}

		/*
		  Jaukāks variants, kur redzama arī video info,
		  bet pagaidām nemāku noņemt autoplay
		  (šķiet, ka tāda iespēja netiek piedāvāta)

		  $encoded_url = urlencode(strip_tags($params[3]));

		  $vine_html  = '<iframe class="vine-embed" ';
		  $vine_html .= 'src="https://vine.co/v/'.$encoded_url.'/embed/simple"';
		  $vine_html .= 'width="320" height="320" frameborder="0">';
		  $vine_html .= '</iframe><script async src="';
		  $vine_html .= '//platform.vine.co/static/scripts/embed.js"';
		  $vine_html .= 'charset="utf-8"></script>';
		 */

		$m->set('vine_' . md5($params[3]), $vine_html, false, 1800);
	}

	return $vine_html;
}

/**
 *  Callback metode Soundcloud dziesmu iekļaušanai tekstā
 *
 *  Izveidoto HTML iekešo Memcached (30 min)
 *
 *  @param $params          dziesmas parametri
 *  @return $scloud_html    iframe ar dziesmām
 */
function embed_soundcloud($params) {
	global $m;

	// [2] adrese ir nesaīsinātā formā, jo atrodas iekš href=""
	// $params[2] - https://../..
	// [3] adrese var būt saīsināta, jo atrodas starp <a></a>
	// $params[3] - https://../..
	// $params[5] - lietotājvārds
	// $params[6] - parametri aiz lietotājvārda (ņemti no saīsinātās adreses)

	$max_height = 320;
	$max_width = 450;

	// ja norādīta specifiska dziesma, augstums nepieciešams visai neliels
	if (isset($params[6]) && !empty($params[6])) {
		$max_height = 130;
	}

	// nolasa no Memcached vai arī tajā ieraksta iframe saturu
	if (($scloud_html = $m->get('scloud_' . md5($params[2]))) === false) {

		// izveido adresi, kas atgriež JSON formāta datus par ierakstā
		// iekļauto adresi; no JSON var atlasīt iframe saturu
		$url = 'https://soundcloud.com/oembed?format=json';
		$url .= '&maxwidth=' . $max_width . '&maxheight=' . $max_height;
		$url .= '&url=' . urlencode(strip_tags($params[2]));

		$data = '';
		$response = curl_get($url);
		if (!empty($response)) {
			$data = json_decode($response);
		}

		// šis paslēpj kvadrātformas attēlu dziesmas sānā
		/* $data->html = str_replace('show_artwork=true',
		  'show_artwork=false',
		  $data->html); */

		// šis paslēpj fona attēlu
		if ($data !== '') {
			$scloud_html = str_replace(
					'visual=true', 'visual=false', $data->html);
		}

		$m->set('scloud_' . md5($params[2]), $scloud_html, false, 1800);
	}

	return $scloud_html;
}

/**
 *  Callback metode Instagram attēlu iekļaušanai tekstā
 *
 *  @param $params       attēla parametri
 *  @return $inst_html   iframe ar attēlu
 */
function embed_instagram($params) {

	// $params[4] - attēla ID

	$inst_html = '<iframe class="embedded-iframe" src="//instagram.com/p/';
	$inst_html .= urlencode($params[4]) . '/embed/" ';
	$inst_html .= 'width="350" height="450" frameborder="0" ';
	$inst_html .= 'scrolling="no" allowtransparency="true"></iframe>';

	return $inst_html;
}

/**
 *  Callback metode Vimeo video iekļaušanai tekstā
 *
 *  @param $params        video parametri
 *  @return $vimeo_html   iframe ar video
 */
function embed_vimeo($params) {

	// $params[3] - video id

	$vimeo_html = '<iframe class="embedded-iframe" src="//player.vimeo.com/video/';
	$vimeo_html .= urlencode($params[3]) . '?badge=0&byline=0" ';
	$vimeo_html .= 'width="520" height="300" frameborder="0" ';
	$vimeo_html .= 'webkitallowfullscreen mozallowfullscreen ';
	$vimeo_html .= 'allowfullscreen></iframe>';

	return $vimeo_html;
}

/**
 *  Callback metode imgur gifv failu embedošanai
 *
 *  @param $params        video parametri
 *  @return $html   iframe ar video
 */
function embed_gifv_gfycat($params) {

	$html = '<iframe class="embedded-iframe" src="//gfycat.com/ifr/'.h($params[3]).'" ';
	$html .= 'allowfullscreen="" frameborder="0" scrolling="no" ';
	$html .= 'style="-webkit-backface-visibility: hidden;-webkit-transform: scale(1);" ';
	$html .= 'width="520" height="300"></iframe>';

	return $html;
}

/**
 *  Callback metode imgur gifv failu embedošanai
 *
 *  @param $params        video parametri
 *  @return $html   iframe ar video
 */
function embed_gifv_imgur($params) {

	$html = '<iframe class="embedded-iframe" src="//i.imgur.com/' . h($params[3]) . '.gifv#embed" ';
	$html .= 'allowfullscreen="" frameborder="0" scrolling="no" ';
	$html .= 'width="520" height="300"></iframe>';

	return $html;
}

/**
 *  Paslēpj [spoiler] tagos ievietoto saturu
 *
 *  @param $text    apstrādājamais saturs
 *  @return $text
 */
function replace_spoiler($text) {

	$text = str_replace(array('<p>', '</p>', '%5B/spoiler%5D'), array('<br />', '<br />', ''), $text[1]);

	$content = '<span class="spoiler"><a href="javascript:void(0);" ';
	$content .= 'class="spoiler-title" title="Slēpt/rādīt spoilera saturu">';
	$content .= 'Rādīt spoileri</a><br /><span style="display:none" ';
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
