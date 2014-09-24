<?php

/**
 * functions.core.php
 * satur pamata funkcijas, kas vajadzīgas praktiski jebkurā lapas pieprasījumā
 */
/**
 * Functions related to awards
 */
include_once(CORE_PATH . '/includes/functions.awards.php');
/**
 *  Functions related to widgets
 */
include_once(CORE_PATH . '/includes/functions.embed.php');

/**
 * utf-8 ucfirst
 */
if (!function_exists('mb_ucfirst') && function_exists('mb_substr')) {

	function mb_ucfirst($string) {
		$string = mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
		return $string;
	}

}

/**
 *  Pārveido stringu par pieļaujamu klases nosaukumu
 */
function as_class_name($name = '') {

	$name = trim($name);
	if (empty($name))
		return '';

	// klašu nosaukumos nevar būt "-", tāpēc aizstājam ar pieņemamu atdalītāju
	$name = str_replace(array('-', ' '), '_', $name);

	$allowed = "/[^a-z0-9_]/i";
	$name = preg_replace($allowed, '', $name);
	if (empty($name))
		return '';

	// katra daļa sāksies ar lielo sākumburtu, piemēram, "class Model_Users"
	$name = str_replace('_', ' ', $name);
	$name = ucwords($name);
	$name = str_replace(' ', '_', $name);

	return $name;
}

/**
 * Aprēķina un updeito lietotāja karmu
 *
 * @param int $userid
 * @param bool $force_award
 */
function update_karma($userid, $force_award = false) {
	global $db; //I feel your pain
	$userid = intval($userid);
	$user = $db->get_row("SELECT id,karma,date,today,posts,rating FROM users WHERE id = '" . $userid . "'");
	if ($user) {
		$images = (int) $db->get_var("SELECT count(*) FROM images WHERE uid = '" . $user->id . "'");
		$topics = (int) $db->get_var("SELECT count(*) FROM pages WHERE author = '" . $user->id . "'");
		$tvotes = $db->get_row("SELECT sum(rating)/sum(rating_count) AS avg, sum(rating_count) AS count FROM pages WHERE author = '" . $user->id . "'");
		$ivotes = $db->get_row("SELECT sum(rating)/sum(rating_count) AS avg, sum(rating_count) AS count FROM images WHERE uid = '" . $user->id . "'");
		$pms = (int) $db->get_var("SELECT count(*) FROM `pm` WHERE `to_uid` = '" . $user->id . "' OR `from_uid` = '" . $user->id . "'");
		$votes = $db->get_var("SELECT count(*) FROM `responses` WHERE `user_id` = '" . $user->id . "'");
		$posts = ($db->get_var("SELECT count(*) FROM comments WHERE author = '$user->id' AND removed = '0'") + $db->get_var("SELECT count(*) FROM galcom WHERE author = '$user->id' AND removed = '0'"));
		$miniblog = $db->get_var("SELECT count(*) FROM miniblog WHERE author = '" . $user->id . "' AND removed = '0'");
		$awards = $db->get_var("SELECT count(*) FROM `autoawards` WHERE `user_id` = '$user->id'");
		$days = ceil((time() - strtotime($user->date)) / 60 / 60 / 24);
		$voteval = $db->get_var("SELECT sum(vote_value) FROM comments WHERE author = '$user->id' AND removed = '0'") +
				$db->get_var("SELECT sum(vote_value) FROM galcom WHERE author = '$user->id' AND removed = '0'") +
				$db->get_var("SELECT sum(vote_value) FROM miniblog WHERE author = '$user->id'");
		$rating = ceil(($tvotes->avg - 3) * $tvotes->count / 10) + ceil(($ivotes->avg - 3) * $ivotes->count / 20);
		if ($rating > 100) {
			$rating = 100;
		}
		if ($rating < -100) {
			$rating = -100;
		}
		$karma = floor($pms / 100) + ($awards * 5) + ($posts + ($voteval / 2) + $topics + $images + $miniblog + ($days / 10) + $votes) / 10 + 1 + $rating;
		if ($karma != $user->karma) {
			$db->query("UPDATE users SET karma = $karma+karma_bonus WHERE id = '$user->id' LIMIT 1");
			get_user($userid, true);
		}
		if ($karma != $user->karma || $force_award) {
			update_awards($userid);
		}
		$posts = ($posts + $miniblog);
		if ($posts != $user->posts || $voteval != $user->rating) {
			$db->update('users', $user->id, array('posts' => $posts, 'rating' => $voteval));
		}
		$topics = $db->get_var("SELECT count(*) FROM pages WHERE author = '" . $user->id . "' AND date > '" . date('Y-m-d') . " 00:00:00'");
		$images = $db->get_var("SELECT count(*) FROM images WHERE uid = '" . $user->id . "' AND date > '" . date('Y-m-d') . " 00:00:00'");
		$posts = ($db->get_var("SELECT count(*) FROM comments WHERE author = '$user->id' AND removed = '0' AND date > '" . date('Y-m-d') . " 00:00:00'") + $db->get_var("SELECT count(*) FROM galcom WHERE author = '$user->id' AND removed = '0' AND date > '" . date('Y-m-d') . " 00:00:00'"));
		$miniblog = $db->get_var("SELECT count(*) FROM `miniblog` WHERE `author` = '" . $user->id . "' AND removed = '0' AND date > '" . date('Y-m-d') . " 00:00:00'");
		$today = $posts + $miniblog + $topics + $images;
		if ($today != $user->today) {
			$db->update('users', $user->id, array('today' => $today));
		}
	}
}

/**
 * Saīsinājums prieks userlog aktuālajam lietotājam
 */
function push($action, $avatar = '', $multi = '') {
	global $auth;
	if ($auth->ok === true) {
		return userlog($auth->id, $action, $avatar, $multi);
	} else {
		return false;
	}
}

/**
 * Veic ierakstu leitotāja pēdējās darbībās
 */
function userlog($user, $action, $avatar = '', $multi = '') {
	global $db, $lang;
	if (!empty($multi)) {
		$db->query("DELETE FROM `userlogs` WHERE `user` = '$user' AND `multi` = '$multi' AND `lang` = '$lang' LIMIT 2");
	}
	$db->query("INSERT INTO `userlogs` (time,user,avatar,action,multi,lang) VALUES ('" . time() . "','" . intval($user) . "','" . sanitize($avatar) . "','" . sanitize($action) . "','$multi','$lang')");
	return true;
}

/**
 * Pievieno lietotāja notifikāciju
 */
function notify($user_id, $type, $place = 0, $url = '', $info = '') {
	global $db, $lang;
	/*
	  tipi:
	  0 - atbilde komentaram
	  1 - komentars bildei
	  2 - komentars rakstam
	  3 - komentars minibloga
	  4 - lietotajs grupaa
	  5 - uzaicinaja draudzeties
	  6 - apstiprināja draudzības aizcinājumu
	  7 - jauna medaļa
	  8 - atbilde grupā
	  9 - jauna vēstule
	  10 - warns pielikts
	  11 - warns noņemts
	  12 - exs.lv update
	  13 - @mention grupā
	  14 - @mention miniblogā
	  15 - @mention topikā
	  16 - @mention attēla komentos
	 */
	$user_id = intval($user_id);
	$type = intval($type);
	$place = intval($place);
	$url = sanitize($url);
	$info = sanitize($info);

	$nlang = $lang;
	if (in_array($type, array(5, 6, 7, 9, 10, 11))) {
		$nlang = 1;
	}

	if (!empty($user_id)) {
		if ($id = $db->get_var("SELECT `id` FROM `notify` WHERE `user_id` = '$user_id' AND `type` = '$type' AND `foreign_key` = '$place' AND `lang` = '$nlang'")) {
			$db->update('notify', $id, array('bump' => 'NOW()'));
			if (!empty($info)) {
				$db->update('notify', $id, array('info' => $info));
			}
			return 2;
		} else {
			$db->query("INSERT INTO `notify` (`user_id`,`type`,`foreign_key`,`bump`,`url`,`info`,`lang`) VALUES ('$user_id','$type','$place',NOW(),'$url','$info','$nlang')");
			return 1;
		}
	}
	return 0;
}

/**
 * Atgriež lietotāja notifikāciju HTML sarakstu
 */
function get_notify($user_id, $base = '/events-pager?events-page=') {
	global $db, $lang, $new_msg_html, $auth, $config_domains; //man kauns :(
	$user_id = intval($user_id);
	$out = '';
	$texts = array(
		0 => 'atbilde komentāram',
		1 => 'komentārs galerijā',
		2 => 'komentārs rakstam',
		3 => 'atbilde mb',
		4 => 'jauns biedrs tavā grupā',
		5 => 'tevi aicina draudzēties',
		6 => 'tev ir jauns draugs',
		7 => 'tu saņēmi medaļu',
		8 => 'atbilde grupā',
		9 => 'saņemta vēstule',
		10 => 'brīdinājums!',
		11 => 'noņemts brīdinājums',
		12 => 'jaunumi no exs.lv',
		13 => 'tevi pieminēja grupā',
		14 => 'tevi pieminēja mb',
		15 => 'tevi pieminēja',
		16 => 'tevi pieminēja galerijā'
	);
	if (!empty($user_id)) {

		$end = 5;
		if (isset($_GET['events-page'])) {
			$skip = (int) $_GET['events-page'] * $end;
		} else {
			$skip = 0;
		}

		// rs.exs.lv nerādīs citu projektu notifikācijas
		$lang_var = ($lang == 9) ? ' AND `lang` = 9 ' : '';

		if ($notify = $db->get_results("SELECT * FROM `notify` WHERE `user_id` = '$user_id' $lang_var ORDER BY `bump` DESC LIMIT $skip,$end")) {

			$out = '<ul id="user-notify">';
			foreach ($notify as $notify) {
				$add = '';

				$site = '';

				$domain = '';
				if ($notify->lang != $lang && !in_array($notify->type, array(5, 6, 7, 9, 10, 11))) {

					$domain = '//' . $config_domains[$notify->lang]['domain'];
					if (empty($config_domains[$notify->lang]['ssl'])) {
						$domain = 'http:' . $domain;
					}

					$site = '&nbsp;<span class="site-name">' . $config_domains[$notify->lang]['domain'] . '</span>';
				}

				if ($notify->type == 5 || $notify->type == 6) {
					$notify->url = '/friends/' . $notify->user_id;
				}
				if ($notify->type == 7) {
					$notify->url = '/awards/' . $notify->user_id;
				}
				if ($notify->type == 9) {
					$notify->url = '/pm';
					$add = $new_msg_html;
				}
				if ($notify->type == 10 || $notify->type == 11) {
					$notify->url = '/warns/' . $notify->user_id;
				}
				if (empty($notify->url)) {
					$notify->url = 'javascript:void(0);';
					$domain = '';
				}
				$class = $notify->type;
				if ($notify->type == 8) {
					$class = 3;
				}
				$out .= '<li class="notification-' . $class . '"><a ';
				if (!empty($notify->info) && $notify->info != 'twitter') {
					$out .= 'title="' . htmlspecialchars($notify->info) . '" ';
				}
				$out .= 'href="' . $domain . $notify->url . '"><span class="notification-icon"></span><span class="notification-date">pirms ' . time_ago(strtotime($notify->bump)) . $site . '</span>' . $texts[$notify->type] . $add;

				if (!empty($notify->info) && $notify->info != 'twitter') {
					$out .= ' - <span class="info-content">' . strip_tags(textlimit($notify->info, 45, '')) . '...</span>';
				}

				$out .= '</a></li>';
			}
			$out .= '</ul>';

			$total = $db->get_var("SELECT count(*) FROM `notify` WHERE `user_id` = '$user_id' $lang_var ORDER BY `bump` DESC LIMIT 25");
			if ($total > 25) {
				$total = 25;
			}
			if ($total > $end) {
				$out .= '<p class="core-pager ajax-pager">';
				$startnext = 0;
				$page_number = 0;
				while ($total - $startnext > 0) {
					$page_number++;
					$class = '';
					if ($skip === $startnext) {
						$class = ' class="selected"';
					}
					$out .= ' <a href="' . $base . $startnext / $end . '"' . $class . '>' . $page_number . '</a> ';
					$startnext = $startnext + $end;
				}
				$out .= '</p>';
			}
		}
	}
	return $out;
}

/**
 * Apakšprojektam specifiskas lietotāju tiesības
 */
function get_site_access() {
	global $db, $m, $lang;

	$site_access = array(
		1 => array(),
		2 => array(),
		3 => array(),
		4 => array(),
		5 => array()
	);

	$site_access_data = $db->get_results("SELECT `user_id`, `level` FROM `site_admins` WHERE `site_id` = '$lang'");
	if (!empty($site_access_data)) {
		foreach ($site_access_data as $usr) {
			$site_access[$usr->level][] = $usr->user_id;
		}
	}

	return $site_access;
}

/**
 * Atgriež niku ar tam atbilstošo krāsu pēc lietotāja tiesībām
 */
function usercolor($nick, $level = 0, $online = false, $userid = 0) {
	global $busers, $online_users, $site_access, $auth, $cday_users, $img_server;
	$star = '';

	if ($online !== 'disable') {
		if ($online || (!empty($userid) && !empty($online_users['onlineusers'][$userid])) || (!empty($online_users['onlineusers']) && in_array($nick, $online_users['onlineusers']))) {
			if (!empty($online_users['mobileusers']) && in_array($nick, $online_users['mobileusers'])) {
				$star = '<span class="g">*</span>';
			} else {
				$star = '<span class="r">*</span>';
			}
		}
	}

	$cakeday = '';
	if (!empty($cday_users)) {
		if (!empty($cday_users[$userid]) || in_array($nick, $cday_users)) {
			$cakeday = '<img src="' . $img_server . '/bildes/cakeday.png" alt="" title="Cake Day!" style="display:inline-block;width:16px;height:16px;" />';
		}
	}

	$nick = $star . htmlspecialchars($nick);

	$user_classes = array(1 => 'admins', 2 => 'mods', 3 => 'rautors', 5 => 'bot');

	foreach ($user_classes as $key => $class) {
		if ($level == $key || ($userid != 0 && !empty($site_access[$key]) && in_array($userid, $site_access[$key]))) {
			$nick = '<span class="' . $class . '">' . $nick . '</span>';
		}
	}

	if ($online !== 'disable' && $userid && !empty($busers)) {
		if (!empty($busers[$userid])) {
			$nick = '<span class="banned">' . $nick . '</span>';
		}
	}

	return $nick . $cakeday;
}

/**
 * Parbauda vai aktīvais lietotājs ir moderators (vai admins)
 */
function im_mod() {
	global $auth;
	if ($auth->ok === true && ($auth->level == 1 || $auth->level == 2)) {
		return true;
	} else {
		return false;
	}
}

/**
 *  RuneScape satura rediģēšanas moderatoru pārbaude
 *
 *  Atlasa no datubāzes tos lietotājus,
 *  kuriem ļauts rediģēt ar rs saistītu saturu un ir vēl pāris privilēģu.
 *
 *  @param  bool    norāde, vai atjaunot memcache vērtību ar moderatoru sarakstu
 */
function im_rs_mod($force = true) {
	global $auth, $db, $m, $lang;

	if (!$auth->ok || $lang != 9)
		return false;

	$rs_mods = array();

	if ($force || $m->get('runescape-mods') === false) {

		$get_mods = $db->get_col("SELECT `user_id` FROM `rs_mods` WHERE `is_deleted` = 0");
		if ($get_mods) {
			$rs_mods = $get_mods;
		}

		// ik pēc 15 min pārbaudīs datubāzi,
		// 15 min tādēļ, lai pēc izmaiņām tabulā ilgi nebūtu jāgaida
		$m->set('runescape-mods', $rs_mods, false, 900);
	}

	$rs_mods = $m->get('runescape-mods');

	return (im_mod() || in_array($auth->id, $rs_mods));
}

/**
 * Parbauda vai aktīvais lietotājs ir atvērtās sadaļas moderators
 */
function im_cat_mod($id = null) {
	global $auth, $category;
	if (!empty($id)) {
		$ct = get_cat($id);
	} else {
		$ct = $category;
	}
	if (in_array($auth->id, $ct->mods)) {
		return true;
	} else {
		return false;
	}
}

function textlimit($string, $setlength, $replacer = '...') {
	$string = trim(strip_tags(str_replace(array('<li>', '</li>', '<br />', '<p>', '</p>', '&nbsp;', "\n", "\r"), ' ', $string)));
	$string = str_replace('	 ', ' ', $string);
	$length = $setlength;
	if ($length < strlen($string)) {
		while (($string{$length} != " ") AND ($length > 0)) {
			$length--;
		}
		if ($length == 0)
			return substr($string, 0, $setlength);
		else
			return substr($string, 0, $length) . $replacer;
	} else
		return $string;
}

function sanitize($input) {
	global $db;
	if (is_array($input)) {
		$output = array();
		foreach ($input as $k => $i) {
			$output[$k] = sanitize($i);
		}
	} else {
		$output = $db->real_escape_string($input);
	}
	return $output;
}

function mkslug($string, $lower = true, $remove_dashes = true) {
	$translit = array(
		'/ä|æ|ǽ/' => 'ae',
		'/ö|œ/' => 'oe',
		'/ü/' => 'ue',
		'/Ä/' => 'Ae',
		'/Ü/' => 'Ue',
		'/Ö/' => 'Oe',
		'/À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ|А/' => 'A',
		'/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª|а/' => 'a',
		'/б/' => 'Б',
		'/б/' => 'b',
		'/Ç|Ć|Ĉ|Ċ|Č|Ц|Ч/' => 'C',
		'/ç|ć|ĉ|ċ|č|ц|ч/' => 'c',
		'/Ð|Ď|Đ|Д/' => 'D',
		'/ð|ď|đ|д/' => 'd',
		'/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě|Ё|Е|З|Э/' => 'E',
		'/è|é|ê|ë|ē|ĕ|ė|ę|ě|ё|е|з|э/' => 'e',
		'/Ф/' => 'F',
		'/ф/' => 'f',
		'/Ĝ|Ğ|Ġ|Ģ|Г/' => 'G',
		'/ĝ|ğ|ġ|ģ|г/' => 'g',
		'/Ĥ|Ħ|Х/' => 'H',
		'/ĥ|ħ|х/' => 'h',
		'/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ|И|Й|Ы/' => 'I',
		'/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı|и|й|ы/' => 'i',
		'/Ĵ|Ъ/' => 'J',
		'/ĵ|ъ/' => 'j',
		'/Ķ|К/' => 'K',
		'/ķ|к/' => 'k',
		'/Ĺ|Ļ|Ľ|Ŀ|Ł|Л/' => 'L',
		'/ĺ|ļ|ľ|ŀ|ł|л/' => 'l',
		'/М/' => 'M',
		'/м/' => 'm',
		'/Ñ|Ń|Ņ|Ň|Н/' => 'N',
		'/ñ|ń|ņ|ň|ŉ|н/' => 'n',
		'/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ|О/' => 'O',
		'/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º|о/' => 'o',
		'/П/' => 'P',
		'/п/' => 'p',
		'/Ŕ|Ŗ|Ř|Р/' => 'R',
		'/ŕ|ŗ|ř|р/' => 'r',
		'/Ś|Ŝ|Ş|Š|С|Ш|Щ/' => 'S',
		'/ś|ŝ|ş|š|ſ|с|ш|щ/' => 's',
		'/Ţ|Ť|Ŧ|Т/' => 'T',
		'/ţ|ť|ŧ|т/' => 't',
		'/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ|У/' => 'U',
		'/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ|у/' => 'u',
		'/В/' => 'V',
		'/в/' => 'v',
		'/Ý|Ÿ|Ŷ/' => 'Y',
		'/ý|ÿ|ŷ/' => 'y',
		'/Ŵ/' => 'W',
		'/ŵ/' => 'w',
		'/Ź|Ż|Ž|Ж/' => 'Z',
		'/ź|ż|ž|ж/' => 'z',
		'/Æ|Ǽ/' => 'AE',
		'/ß/' => 'ss',
		'/Ĳ/' => 'IJ',
		'/ĳ/' => 'ij',
		'/Œ/' => 'OE',
		'/ƒ/' => 'f',
		'/Ю/' => 'Ju',
		'/ю/' => 'ju',
		'/Я/' => 'Ja',
		'/я/' => 'ja'
	);

	$string = trim($string);
	$string = preg_replace(array_keys($translit), array_values($translit), $string);
	$string = str_replace('&amp;', '-un-', $string);
	$string = str_replace(array(' ', '.', ',', '"', '=', '`', ']', '[', '|', ':', '+', '&quot;', '!', '/', "\\"), '-', $string);
	$allowed = "/[^a-z0-9\\-\\_\\\\]/i";
	$string = preg_replace($allowed, '', $string);

	//remove repeated dashes
	$string = preg_replace('/-+/', '-', $string);

	//remove dashes from ends of string
	if ($remove_dashes) {
		$string = trim($string, '-');
	}

	$string = substr($string, 0, 100);
	if (empty($string)) {
		$string = 'page';
	}
	if ($lower) {
		$string = strtolower($string);
	}
	return $string;
}

function mkslug_newpage($title) {
	global $db;
	$strid = mkslug($title);
	$exists = $db->get_var("SELECT count(*) FROM `pages` WHERE `strid` = '$strid'");
	if (!$exists) {
		return $strid;
	} else {
		for ($i = 2; $i < 999; $i++) {
			$nstrid = $strid . '-' . $i;
			if (!$db->get_var("SELECT count(*) FROM `pages` WHERE `strid` = '$nstrid'")) {
				return $nstrid;
			}
		}
	}
}

function mkurl($type, $id, $title, $add = '') {
	if ($type === 'user') {
		return '/user/' . $id;
	}
	return '/' . $type . '/' . $id . '-' . mkslug($title) . $add;
}

/**
 * Atrod sarakstu ar domēniem no dofollow_sites,
 * https_sites un blacklisted_sites tabulām
 */
function get_sitelist($table) {

	//variable name
	$storage = $table . "_sites";

	global $db, $m, $$storage;
	if (empty($$storage)) {
		if (($$storage = $m->get($storage)) === false) {
			$$storage = $db->get_col("SELECT `url` FROM `" . $storage . "`");
			$m->set($storage, $$storage, false, 3600);
		}
	}
	return $$storage;
}

function mention($text, $url = '#', $type = 'notype', $uniq = 0) {

	/* repleisojam bieži pieminētu lietotāju vārdus, lai būtu mazāk kļūdu */
	$underscore_names = array(
		'@Avril Lavigne' => '@Avril_Lavigne',
		'@Hidden driver' => '@Hidden_driver',
		'@Maadinsh' => '@mad',
		'@S J' => '@S_J'
	);

	foreach ($underscore_names as $key => $val) {
		$text = str_ireplace($key, $val, $text);
	}

	$text = str_replace('eval(', 'ev<span>a</span>l(', $text);
	$text = preg_replace('/@([0-\x{003b}\x{003d}-\x{024f}]+)/uime', 'get_mentions("\\1","' . $url . '","' . $type . '","' . $uniq . '")', $text);

	if ($type == 'mb') {
		$text = preg_replace('/\B#([0-\x{003b}\x{003d}-\x{024f}\-_]+)/uime', 'get_tags_mb("\\1", "' . $uniq . '")', $text);
	}

	return $text;
}

function get_tags_mb($tag, $mbid) {
	global $db;

	if ($mb = $db->get_row("SELECT * FROM `miniblog` WHERE `id` = '$mbid' AND `parent` = '0' AND `removed` = '0'")) {
		include_once(CORE_PATH . '/includes/class.tags.php');
		$tags = new tags;

		if (strlen($tag) < 30 && strlen($tag) > 2) {
			$newtag = sanitize(mb_ucfirst(strtolower(trim($tag))));
			$nslug = mkslug($tag);
			if (!empty($newtag)) {
				$tagid = $db->get_var("SELECT id FROM tags WHERE slug = '$nslug'");
				if ($tagid) {
					$tags->add_tag($mb->id, $tagid, 2);
				} else {
					$db->query("INSERT INTO tags (name,slug) VALUES ('$newtag','$nslug')");
					$tagid = $db->get_var("SELECT id FROM tags WHERE slug = '$nslug'");
					$tags->add_tag($mb->id, $tagid, 2);
				}
			}
			return '<a class="post-tag" href="/tag/' . $nslug . '" title="' . $newtag . '"><span class="hash-sign">#</span>' . $tag . '</a>';
		}
	}
	return '#' . $tag;
}

function get_mentions($nick, $url = '#', $type = "notype", $uniq = 0) {
	global $db, $auth, $mention_counter;

	$usr = $db->get_row("SELECT * FROM `users` WHERE `nick` = '" . sanitize($nick) . "'");

	if (empty($usr) && stristr($nick, '_')) {
		$nick = str_replace('_', ' ', $nick);
		$usr = $db->get_row("SELECT * FROM `users` WHERE `nick` = '" . sanitize($nick) . "'");
	}

	if (empty($usr) && stristr($nick, '-')) {
		$nick = str_replace('-', ' ', $nick);
		$usr = $db->get_row("SELECT * FROM `users` WHERE `nick` = '" . sanitize($nick) . "'");
	}

	if (!empty($usr) && !in_array($nick, array('exs', 'inbox', 'gmail', 'mail', 'twitter', 'hotmail')) && $mention_counter <= 6) {
		$mention_counter++;

		if ($type == 'mb') {
			if (!empty($uniq)) {
				$mb = $db->get_row("SELECT `text`, `id`, `author` FROM `miniblog` WHERE `id` = '" . intval($uniq) . "'");
				$title = mb_get_title($mb->text);
				$strid = mb_get_strid($title, $mb->id);
				$url = '/say/' . $mb->author . '/' . $mb->id . '-' . $strid;
				if ($mb->author != $usr->id && $usr->id != $auth->id) {
					notify($usr->id, 14, $mb->id, $url, $title);
				}
			}
		}

		if ($type == 'group') {
			if (!empty($uniq)) {
				$mb = $db->get_row("SELECT `id`, `groupid`, `text`, `author` FROM `miniblog` WHERE `id` = '" . intval($uniq) . "'");
				$group = $db->get_row("SELECT `title`, `strid`, `id` FROM `clans` WHERE `id` = '$mb->groupid'");
				$title = mb_get_title($mb->text);
				if (!empty($group->strid)) {
					$url = '/' . $group->strid . '/forum/' . base_convert($mb->id, 10, 36);
				} else {
					$url = '/group/' . $group->id . '/forum/' . base_convert($mb->id, 10, 36);
				}
				if ($mb->author != $usr->id && $usr->id != $auth->id) {
					notify($usr->id, 13, $mb->id, $url, $group->title . ': ' . $title);
				}
			}
		}

		if ($type == 'page') {
			if (!empty($uniq)) {
				$page = $db->get_row("SELECT `id`, `title`, `author` FROM `pages` WHERE `id` = '" . intval($uniq) . "'");
				if ($page->author != $usr->id && $usr->id != $auth->id) {
					notify($usr->id, 15, $page->id, $url, $page->title);
				}
			}
		}

		if ($type == 'image') {
			if (!empty($uniq)) {
				$image = $db->get_row("SELECT `id`, `uid`, `text` FROM `images` WHERE `id` = '" . intval($uniq) . "'");
				$url = '/gallery/' . $image->uid . '/' . $image->id;
				if ($usr->id != $auth->id) {
					notify($usr->id, 16, $image->id, $url, strip_tags($image->text));
				}
			}
		}

		if ($type == 'junk') {
			if (!empty($uniq)) {
				$junk = $db->get_row("SELECT `id`, `title` FROM `junk` WHERE `id` = '" . intval($uniq) . "'");
				$url = '/junk/' . $junk->id;
				if ($usr->id != $auth->id) {
					notify($usr->id, 15, $junk->id, $url, strip_tags($junk->title));
				}
			}
		}

		return '<a class="post-mention" href="/user/' . $usr->id . '"><span class="at-sign">@</span>' . usercolor($usr->nick, $usr->level, 'disable', $usr->id) . '</a>';
	} else {
		return '@' . $nick;
	}
}

function createPassword($length) {
	$chars = "1234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%&";
	$i = 0;
	$password = "";
	while ($i <= $length) {
		$password .= $chars{mt_rand(0, strlen($chars))};
		$i++;
	}
	return $password;
}

function get_between($text, $s1, $s2) {
	$mid_url = "";
	$pos_s = strpos($text, $s1);
	$pos_e = strpos($text, $s2);
	for ($i = $pos_s + strlen($s1); ( ( $i < ($pos_e)) && $i < strlen($text)); $i++) {
		$mid_url .= $text[$i];
	}
	return $mid_url;
}

function time_ago($tm) {
	$cur_tm = time();
	$dif = $cur_tm - $tm;
	$pds = array('sekundēm', 'minūtēm', 'stundām', 'dienām', 'nedēļām', 'mēnešiem', 'gadiem');
	$pd = array('sekundes', 'minūtes', 'stundas', 'dienas', 'nedēļas', 'mēneša', 'gada');
	$lngh = array(1, 60, 3600, 86400, 604800, 2630880, 31570560);
	for ($v = sizeof($lngh) - 1; ($v >= 0) && (($no = $dif / $lngh[$v]) <= 1); $v--)
		;
	if ($v < 0) {
		$v = 0;
		$_tm = $cur_tm - ($dif % $lngh[$v]);
	}
	$no = floor($no);
	if (substr($no, -1) == '1' && substr($no, -2) != '11') {
		$x = sprintf("%d %s", $no, $pd[$v]);
	} else {
		$x = sprintf("%d %s", $no, $pds[$v]);
	}
	if ($v == 0 && $no < 3) {
		$x = 'mirkļa';
	}
	return $x;
}

function time_ago_m($tm) {
	$cur_tm = time();
	$dif = $cur_tm - $tm;
	$pd = array('s', 'm', 'h', 'd', 'n', 'mēn', 'g');
	$lngh = array(1, 60, 3600, 86400, 604800, 2630880, 31570560);
	for ($v = sizeof($lngh) - 1; ($v >= 0) && (($no = $dif / $lngh[$v]) <= 1); $v--)
		;
	if ($v < 0) {
		$v = 0;
		$_tm = $cur_tm - ($dif % $lngh[$v]);
	}
	$no = floor($no);
	$x = sprintf("%d%s", $no, $pd[$v]);
	return $x;
}

function date_lv($date, $time = '') {
	$en = array('/Monday/', '/Tuesday/', '/Wednesday/', '/Thursday/', '/Friday/', '/Saturday/', '/Sunday/', '/January/', '/February/', '/March/', '/April/', '/May/', '/June/', '/July/', '/August/', '/September/', '/October/', '/November/', '/December/');
	$lv = array('Pirmdiena', 'Otrdiena', 'Trešdiena', 'Ceturtdiena', 'Piektdiena', 'Sestdiena', 'Svētdiena', 'janvāris', 'februāris', 'marts', 'aprīlis', 'maijs', 'jūnijs', 'jūlijs', 'augusts', 'septembris', 'oktobris', 'novembris', 'decembris');
	$date = date($date, $time);
	$date = preg_replace($en, $lv, $date);
	return $date;
}

/**
 * Datuma attēlošanas funkcija
 *
 * @param int $time Unix timestamp
 * @return string Human readable datetime
 */
function display_time($time) {
	if (!$time) {
		$out = '';
	} elseif ($time >= strtotime('today')) {
		$out = 'Šodien, ' . date('G:i', $time);
	} elseif ($time >= strtotime('yesterday')) {
		$out = 'Vakar, ' . date('G:i', $time);
	} else {
		$out = date('d.m.Y. H:i', $time);
	}
	return $out;
}

function alternator() {
	static $i;
	if (func_num_args() === 0) {
		$i = 0;
		return '';
	}
	$args = func_get_args();
	return $args[($i++ % count($args))];
}

function pr($data) {
	global $debug;
	if ($debug) {
		echo '<pre style="color: #eee;background: #123;border: 1px solid #111;padding: 5px 10px;font-size:10px;line-height: 13px; margin: 0 0 10px;white-space:pre;">';
		var_dump($data);
		echo '</pre>';
	}
}

function strTime($s) {
	$d = intval($s / 86400);
	$s -= $d * 86400;
	$h = intval($s / 3600);
	$s -= $h * 3600;
	$m = intval($s / 60);
	$s -= $m * 60;
	$str = '';
	if ($d)
		$str = $d . 'd ';
	if ($h)
		$str .= $h . 'h ';
	if ($m)
		$str .= $m . 'm ';
	if ($s)
		$str .= $s . 's';
	return $str;
}

function mb_get_title($body = 'Bez nosaukuma') {
	$body = youtube_title($body);
	$body = strip_tags(str_replace(array('<br/>', '<br>', '<br />', '<p>', '</p>', '&nbsp;', "\n", "\r"), ' ', $body));
	return $body;
}

function mb_get_strid($title = 'Bez nosaukuma', $id = null) {
	global $m;
	if (!empty($id)) {
		if (($data = $m->get('strid_' . $id)) === false) {
			$data = mkslug(textlimit(mb_get_title($title), 36, ''));
			$m->set('strid_' . $id, $data, false, 300);
		}
		return $data;
	} else {
		return mkslug(textlimit($title, 36, ''));
	}
}

/**
 * Parāda lietotāja vecumu pēc datuma
 */
function user_age($date) {
	$year_diff = date("Y") - date('Y', strtotime($date));
	$month_diff = date("m") - date('m', strtotime($date));
	$day_diff = date("d") - date('d', strtotime($date));
	if ($month_diff < 0) {
		$year_diff--;
	} elseif (($month_diff == 0) && ($day_diff < 0)) {
		$year_diff--;
	}
	return $year_diff;
}

function get_top($id, $depth = 0) {
	global $m, $debug, $lang;
	$id = (int) $id;
	if ($debug || ($data = $m->get('ctop_' . $id)) === false) {
		$data = get_top_rec($id, $depth);
		$m->set('ctop_' . $id . '_' . $lang, $data, false, 43200);
	}
	return $data;
}

function get_top_rec($id, $depth = 0) {
	global $db;
	$id = (int) $id;
	$depth++;
	if ($depth > 6) {
		return false;
	}
	if ($id != 1) {
		$parent = $db->get_var("SELECT `parent` FROM `cat` WHERE `id` = '$id'");
		if ($parent != 0) {
			$id = get_top_rec($parent, $depth);
		}
	}
	return $id;
}

/**
 * Returns all user data as object by id
 *
 * @param int $user_id
 * @param bool $force
 * @return object
 */
function get_user($user_id, $force = false) {
	global $db, $m, $users_cache, $debug;
	$user_id = (int) $user_id;
	if (!$force && !empty($users_cache[$user_id])) {
		return $users_cache[$user_id];
	}
	if ($debug || $force === true || ($data = $m->get('u_' . $user_id)) === false) {
		$data = $db->get_row("SELECT * FROM `users` WHERE `id` = '$user_id'");
		$m->set('u_' . $user_id, $data, false, 3600);
	}
	$users_cache[$user_id] = $data;
	return $data;
}

/**
 * Returns 5 newest groups in current domain
 *
 * @param bool $force
 * @return array
 */
function get_latest_groups($force = false) {
	global $db, $m, $lang;
	if ($force || !($data = $m->get('latest_groups_' . $lang))) {
		$data = $db->get_results("SELECT `id`,`title`,`strid`,`avatar` FROM `clans` WHERE `list` = 1 AND `lang` = '$lang' ORDER BY `id` DESC LIMIT 5");
		$m->set('latest_groups_' . $lang, $data, false, 3600);
	}
	return $data;
}

/**
 * Replacement for file_get_contents with timeout
 */
function curl_get($url, $connect_timeout = 2, $timeout = 4) {

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$contents = curl_exec($ch);
	curl_close($ch);

	return $contents;
}

/**
 * Returns category object by either id or strid
 */
function get_cat($id, $force = false) {
	global $db, $m, $debug, $lang;
	if ($debug || $force || !($data = $m->get('cat_' . $lang . '_' . $id))) {
		if (is_numeric($id)) {
			$data = $db->get_row("SELECT * FROM `cat` WHERE `id` = '" . intval($id) . "' AND (`lang` = $lang OR `lang` = 0)");
		} else {
			$data = $db->get_row("SELECT * FROM `cat` WHERE `textid` = '" . sanitize(trim($id)) . "' AND (`lang` = $lang OR `lang` = 0)");
		}
		$data->mods = array();
		if ($mods = $db->get_results("SELECT `user_id` FROM `cat_moderators` WHERE `category_id` = '$data->id'")) {
			foreach ($mods as $mod) {
				$data->mods[] = $mod->user_id;
			}
		}
		$m->set('cat_' . $lang . '_' . $id, $data, false, 7200);
	}
	return $data;
}

/**
 * Pārvieto sadaļu secībā uz augšu vai leju
 */
function move_cat($id, $direction = 'down') {
	global $auth, $db, $lang;

	$order = 'ASC';
	$sign = '>';
	if ($direction === 'up') {
		$order = 'DESC';
		$sign = '<';
	}

	if ($auth->level == 1) {
		$move = $db->get_row("SELECT * FROM `cat` WHERE `id` = '" . intval($id) . "'");
		$swap = $db->get_row("SELECT * FROM `cat` WHERE `parent` = '$move->parent' AND (`lang` = '$lang' OR `lang` = 0) AND `ordered` $sign '$move->ordered' ORDER BY `ordered` $order LIMIT 1");
		if ($move && $swap) {
			$db->query("UPDATE `cat` SET `ordered` = '$move->ordered' WHERE `id` = '$swap->id' LIMIT 1");
			$db->query("UPDATE `cat` SET `ordered` = '$swap->ordered' WHERE `id` = '$move->id' LIMIT 1");
		}
	}
}

/**
 * Find page id by strid
 */
function get_page_strid($id = null) {
	global $db, $m;
	if (($data = $m->get('strid_' . $id)) === false) {
		$data = $db->get_var("SELECT `strid` FROM `pages` WHERE `id` = '$id'");
		$m->set('strid_' . $id, $data, false, 10800);
	}
	return $data;
}

function get_banlist($force = false) {
	global $db, $m, $lang;
	if ($force || !($busers = $m->get('banlist_' . $lang))) {
		$data = $db->get_results("SELECT `user_id` FROM `banned` WHERE `user_id` != 0 AND `time`+`length` > " . time() . " AND (`lang` = 0 OR `lang` = '$lang') ORDER BY `time` DESC");
		$busers = array();
		if ($data) {
			foreach ($data as $banned) {
				$busers[$banned->user_id] = $banned->user_id;
			}
		}
		$m->set('banlist_' . $lang, $busers, false, 100);
	}
	return $busers;
}

/**
 * Linki uz jaunākajiem miniblogiem lapas footerī
 */
function get_footer_mb($force = false) {
	global $db, $m, $lang, $auth;
	if ($force || !($html = $m->get('f_mb_' . $lang))) {
		$html = '';

		//miniblogi kas nav publiski pieejami
		$priv = '';
		if (!$auth->ok) {
			$priv = ' AND `miniblog`.`private` = 0 ';
		}

		$latest = $db->get_results("
			SELECT `text`,`id`,`author`
			FROM `miniblog`
			WHERE `date` > '" . date('Y-m-d H:i:s', time() - 1209600) . "' AND `parent` = 0 AND `groupid` = 0 AND `removed` = 0 AND `lang` = $lang $priv
			ORDER BY `id` DESC
			LIMIT 5
		");

		if ($latest) {
			$html .= '<ul class="internal-links">';
			foreach ($latest as $late) {
				$late->text = mb_get_title($late->text);
				$url_title = mkslug(textlimit($late->text, 36, ''));
				$html .= '<li><a href="/say/' . $late->author . '/' . $late->id . '-' . $url_title . '">' . textlimit($late->text, 36, '') . '</a></li>';
			}
			$html .= '</ul>';
		}
		$m->set('f_mb_' . $lang, $html, false, 120);
	}
	return $html;
}

/**
 * Linki uz jaunākajiem rakstiem footerī
 */
function get_footer_topics($force = false) {
	global $db, $m, $lang;
	if ($force || !($html = $m->get('f_topics_' . $lang))) {
		$html = '';
		$latest = $db->get_results("SELECT `lang`,`title`,`strid` FROM `pages` WHERE `category` != '83' AND `category` != '6' AND `lang` = '$lang' ORDER BY `id` DESC LIMIT 5");
		if ($latest) {
			$html .= '<ul class="internal-links">';
			foreach ($latest as $late) {
				$html .= '<li><a href="/read/' . $late->strid . '" title="' . htmlspecialchars($late->title) . '">' . textlimit($late->title, 36) . '</a></li>';
			}
			$html .= '</ul>';
		}
		$m->set('f_topics_' . $lang, $html, false, 120);
	}
	return $html;
}

/**
 * Creates online users array
 */
function get_online($force = false) {
	global $db, $m;
	if ($force || !($data = $m->get('onlineusers'))) {
		$lastseen = $db->get_results("SELECT
			`users`.`id`,
			`users`.`nick`,
			`users`.`mobile`
		FROM
			`users`,
			`visits`
		WHERE
			`visits`.`lastseen` > '" . date('Y-m-d H:i:s', time() - 360) . "' AND
			`users`.`id` = `visits`.`user_id`
		");

		$data = array(
			'onlineusers' => array(),
			'mobileusers' => array()
		);
		if ($lastseen) {
			foreach ($lastseen as $usr) {
				$data['onlineusers'][$usr->id] = $usr->nick;
				if ($usr->mobile) {
					$data['mobileusers'][$usr->id] = $usr->nick;
				}
			}
		}
		$m->set('onlineusers', $data, false, 10);
	}
	return $data;
}

/**
 * Creates online users list in HTML
 */
function get_online_list($force = false) {
	global $db, $m, $lang;
	$data = '';
	if ($force || !($data = $m->get('onlinelist-' . $lang))) {
		$lastseen = $db->get_results("SELECT
			DISTINCT(`visits`.`user_id`) AS `id`,
			`users`.`nick`,
			`users`.`level`
		FROM
			`visits`,
			`users`
		WHERE
			`visits`.`site_id` = $lang AND
			`visits`.`lastseen` > '" . date('Y-m-d H:i:s', time() - 360) . "' AND
			`users`.`id` = `visits`.`user_id`
		ORDER BY
			`users`.`nick` ASC");
		if ($lastseen) {
			foreach ($lastseen as $usr) {
				$data .= '<a href="/user/' . $usr->id . '">' . usercolor($usr->nick, $usr->level, true, $usr->id) . '</a> ';
			}
		}
		$m->set('onlinelist-' . $lang, $data, false, 10);
	}
	return $data;
}

/**
 * Find category marked as given users blog
 */
function get_blog_by_user($user_id, $force = false) {
	global $db, $m, $lang;
	if ($force || !($data = $m->get('isb_' . $user_id . '_' . $lang))) {
		$data = $db->get_var("SELECT `id` FROM `cat` WHERE `isblog` = '$user_id' AND `lang` = '$lang' LIMIT 1");
		if (!$data) {
			$data = 'no';
		}
		$m->set('isb_' . $user_id . '_' . $lang, $data, false, 7200);
	}
	if ($data > 0 && $data != 'no') {
		return $data;
	} else {
		return false;
	}
}

/**
 * Recursive mkdir
 *
 * @return boolean
 */
function rmkdir($path, $mode = 0777) {
	$path = rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", $path), "/");
	$e = explode("/", ltrim($path, "/"));
	if (substr($path, 0, 1) === "/") {
		$e[0] = "/" . $e[0];
	}
	$c = count($e);
	$cp = $e[0];
	for ($i = 1; $i < $c; $i++) {
		if (!is_dir($cp) && !@mkdir($cp, $mode)) {
			return false;
		}
		$cp .= "/" . $e[$i];
	}
	return @mkdir($path, $mode);
}

function pager($total = 0, $skip = 20, $end = 20, $url = '?skip=', $ajax = false) {
	$pager_prev = '';
	$pager_next = '';
	$pager_numeric = '';
	if ($total > $end) {

		$ajax_class = '';
		if ($ajax) {
			$ajax_class = 'ajax-module ';
		}

		if ($skip > 0) {
			if ($skip > $end) {
				$iepriekseja = $skip - $end;
			} else {
				$iepriekseja = 0;
			}
			$pager_next = '<a class="pager-next" title="Iepriekšējā lapa" href="' . $url . $iepriekseja . '">&laquo;</a> <span>-</span>';
		} else {
			$pager_next = '';
		}
		$pager_prev = '';
		if ($total > $skip + $end) {
			$pager_prev = '<span>-</span> <a class="pager-prev" title="Nākamā lapa" href="' . $url . ($skip + $end) . '">&raquo;</a>';
		}
		$startnext = 0;
		$page_number = 0;
		$pager_numeric = '';
		while ($total - $startnext > 0) {
			$page_number++;
			$class = '';
			if ($skip === $startnext) {
				$class = ' class="' . $ajax_class . 'selected"';
			} elseif ($ajax) {
				$class = ' class="' . $ajax_class . '"';
			}
			if ($total / $end < 10 || $page_number < 4 || $page_number > $total / $end - 2 || $startnext === $skip || $startnext === $skip + $end || $startnext === $skip - $end) {
				if ($page_number != 1) {
					$pager_numeric .='<span>-</span> ';
				}
				$pager_numeric .= '<a href="' . $url . $startnext . '"' . $class . '>' . $page_number . '</a> ';
			} elseif ($startnext === $skip + $end * 2 || $startnext === $skip - $end * 2) {
				if ($page_number !== 1) {
					$pager_numeric .='<span>-</span> ';
				}
				$pager_numeric .= ' ... ';
			} elseif ($page_number === 4 && $skip / $end < 5) {
				if ($page_number !== 1) {
					$pager_numeric .='<span>-</span> ';
				}
				$pager_numeric .= ' ... ';
			}

			$startnext = $startnext + $end;
		}
	}
	return array(
		'prev' => $pager_prev,
		'next' => $pager_next,
		'pages' => $pager_numeric
	);
}

function mb_rater($mb) {
	global $auth, $remote_salt;

	$pluslnk = '<span class="voted1"></span>';
	$minuslnk = '<span class="voted2"></span>';

	if ($auth->ok === true) {
		$check = substr(md5($mb->id . $remote_salt . $auth->id), 0, 5);
		if (!empty($mb->vote_users)) {
			$voters = unserialize($mb->vote_users);
		} else {
			$voters = array();
		}
		$voted = in_array($auth->id, $voters);

		if (!$voted && $auth->id != $mb->author) {
			$pluslnk = '<a href="/rate-comment/?vc=' . $mb->id . '&amp;type=mb&amp;check=' . $check . '&amp;action=plus" class="plus">plus</a>';
			$minuslnk = '<a href="/rate-comment/?vc=' . $mb->id . '&amp;type=mb&amp;check=' . $check . '&amp;action=minus" class="minus">minus</a>';
		}
	}

	if ($mb->vote_value > 0) {
		$mb->vote_value = '+' . $mb->vote_value;
		$vclass = 'positive';
	} elseif ($mb->vote_value < 0) {
		$vclass = 'negative';
	} else {
		$vclass = 'zero';
	}

	$html = '<span class="c-rate"><span class="r-val ' . $vclass . '">' . $mb->vote_value . '</span>' . $pluslnk . $minuslnk . '</span>';
	return $html;
}

function filterb4db($text) {
	$shit = array(
		'&feature=youtu.be',
		'&feature=player_embedded',
		'&feature=video_response',
		'&feature=player_profilepage'
	);

	if (strpos($text, 'code') === false) {
		$text = str_replace('<br /><br />', "\n\n", $text);
	}

	$text = str_replace($shit, '', $text);
	$text = str_replace('??????', '???', $text);
	$text = str_replace('?????', '???', $text);
	$text = str_replace('????', '???', $text);
	$text = str_replace('!!!!!!', '!!!', $text);
	$text = str_replace('!!!!!', '!!!', $text);
	$text = str_replace('!!!!', '!!!', $text);
	return $text;
}

function post2db($text, $type = "notype", $mbid = "0") {
	global $auth, $db;
	require_once(CORE_PATH . '/includes/class.bbcode.php');
	$bbcode = new BBCode();
	$text = $bbcode->parse($text);
	$text = mb_ucfirst($text);
	return htmlpost2db($text);
}

function htmlpost2db($text) {
	$text = filterb4db($text);
	require_once(LIB_PATH . '/htmlpurifier/library/HTMLPurifier.includes.php');
	$config = HTMLPurifier_Config::createDefault();
	$config->set('Cache.SerializerPath', CORE_PATH . '/cache/htmlpurifier');
	$config->set('AutoFormat.Linkify', true);
	$config->set('AutoFormat.AutoParagraph', true);
	$config->set('AutoFormat.RemoveSpansWithoutAttributes', true);
	$config->set('AutoFormat.RemoveEmpty', true);
	$purifier = new HTMLPurifier($config);
	$text = $purifier->purify($text);
	$text = str_replace('href="http://' . $_SERVER['SERVER_NAME'] . '/', 'href="/', $text);
	$text = str_replace('href="https://' . $_SERVER['SERVER_NAME'] . '/', 'href="/', $text);
	$text = str_replace(' rel="nofollow"', '', $text);
	$text = str_replace(' dateks.lv ', ' <a href="http://www.dateks.lv/ref/view.html">dateks.lv</a> ', $text);
	$text = str_replace(' dateks ', ' <a href="http://www.dateks.lv/ref/view.html">Dateks</a> ', $text);
	$text = str_replace(' dateksā ', ' <a href="http://www.dateks.lv/ref/view.html">dateksā</a> ', $text);
	$text = str_replace(' dateksaa ', ' <a href="http://www.dateks.lv/ref/view.html">dateksā</a> ', $text);
	$text = str_replace('dateks.lv/cenas', 'dateks.lv/p/view/cenas', $text);
	$text = str_replace('<code>', '<code class="prettyprint">', $text);
	$text = str_replace('<pre>', '<pre class="prettyprint">', $text);
	return sanitize($text);
}

function title2db($text) {
	$text = filterb4db($text);
	$text = str_replace(')', ') ', $text);
	$text = str_replace('(', ' (', $text);
	$text = str_replace(',', ', ', $text);
	$text = str_replace(' ,', ',', $text);
	$text = mb_ucfirst(substr(htmlspecialchars(strip_tags(trim($text))), 0, 80));
	if (substr($text, -1) == '.' && substr($text, -3) != '...') {
		$text = substr($text, 0, -1);
	}
	if (in_array(substr($text, -1), array('.', ',', ';', ':')) && substr($text, -3) != '...') {
		$text = substr($text, 0, -1);
	}
	if (empty($text)) {
		$text = 'Bez nosaukuma';
	}
	$text = str_replace('  ', ' ', $text);
	$text = str_replace('  ', ' ', $text);
	return sanitize($text);
}

function input2db($text, $len = 30) {
	$text = filterb4db($text);
	$text = substr(htmlspecialchars(trim($text)), 0, $len);
	return sanitize($text);
}

/**
 * Returns safe and valid email address for storing in mysql
 *
 * @param string $email
 * @return string
 */
function email2db($email) {
	return sanitize(filter_var($email, FILTER_SANITIZE_EMAIL));
}

/**
 * Redirect user to given miniblog/group post/junk image
 *
 * @param object $mb
 */
function return2mb($mb) {
	global $db;
	if ($mb->type == 'junk') {
		redirect('/junk/' . $mb->parent);
	}
	if (!empty($mb->parent)) {
		$mb = $db->get_row("SELECT * FROM `miniblog` WHERE `id` = $mb->parent");
	}
	if (!empty($mb->groupid)) {
		redirect('/group/' . $mb->groupid . '/forum/' . base_convert($mb->id, 10, 36));
	} else {
		redirect('/?m=' . $mb->author . '&single=' . $mb->id);
	}
}

//atgriez visas minibloga atbildes html formā, rekursīvi
function mb_recursive($data, $key = 0, $level = 0, $intro = 0, $answer_limit = 3, $closed = 0, $disable_vote = 0) {
	global $auth, $min_post_edit, $lang;
	$out = '<ul class="responses-' . $key . ' level-' . $level . '">';
	if (!empty($data[$key])) {
		$level++;
		foreach ($data[$key] as $val) {
			$out .= '<li>';
			$val->date = strtotime($val->date);
			if (!$auth->mobile) {
				$out .= '<div class="mb-av"><a id="m' . $val->id . '" href="/user/' . $val->author . '">';
				$out .= '<img width="45" height="45" class="av" src="' . get_avatar($val, 's') . '" alt="' . htmlspecialchars($val->nick) . '" /></a>';
				if (!empty($val->decos)) {
					$decos = unserialize($val->decos);
					if (!empty($decos)) {
						$di = 0;
						foreach ($decos as $deco) {
							$out .= '<img src="' . $deco['icon'] . '" alt="' . $deco['title'] . '" title="' . $deco['title'] . '" class="user-deco deco-pos-' . $di . '" />';
							$di++;
						}
					}
				}
				$out .= '</div>';
			} else {
				$out .= '<a class="mb-av" id="m' . $val->id . '" href="/user/' . $val->author . '"><img class="av" width="40" height="40" src="' . get_avatar($val, 's') . '" alt="" /></a>';
			}
			$out .= '<div class="response-content">';
			if (!$intro && $auth->ok === true && $level < $answer_limit) {
				$out .= '<a href="' . $val->id . '" class="mb-reply-to mb-icon">Atbilde</a>';
			}

			// atslēdz ierakstu vērtēšanu
			if (empty($disable_vote)) {
				$out .= '<div class="mb-rater">' . mb_rater($val) . '</div>';
			}

			$out .= '<p class="post-info">';
			if (!$val->user_deleted) {
				$out .= '<a href="/user/' . $val->author . '">' . usercolor($val->nick, $val->level, false, $val->author) . '</a>';
			} else {
				$out .= '<em>dzēsts</em>';
			}
			$out .= ' <span class="comment-date-time" title="' . date('d.m.Y. H:i', $val->date) . '">' . display_time($val->date) . '</span>';

			//permalink
			if (!$auth->mobile && !$intro) {
				$out .= ' <a href="#m' . $val->id . '" class="post-button comment-permalink" title="Saite uz komentāru">#</a>';
			}

			//podziņa lietotāja pārkāpuma noziņošanai (exs.lv; lol.exs.lv; rs.exs.lv) (ja ieraksts jau nav dzēsts)
			if ($val->mb_removed == 0 && $auth->ok && !$auth->mobile && in_array($lang, array(1, 7, 9))) {
				$out .= ' <a class="post-button report-user" href="/report/miniblog/' . $val->id . '" title="Ziņot par pārkāpumu">ziņot</a>';
			}

			//labot (ja ieraksts jau nav dzēsts)
			if ($val->mb_removed == 0 && !$auth->mobile && !$intro && ($val->date > time() - 1800 || ($auth->level == 2 && $val->author == $auth->id && $val->date > time() - 86400) || $auth->level == 1 || $auth->id == 115) &&
					(im_mod() || (!$closed && $auth->karma >= $min_post_edit && $val->author == $auth->id))) {
				$out .= ' <a href="/edit/' . $val->id . '" class="post-button post-edit" title="Labot komentāru">labot</a>';
			}

			//dzēst (ja ieraksts jau nav dzēsts)
			if ($val->mb_removed == 0 && !$auth->mobile && !$intro && $auth->ok === true && ( (!$closed && $auth->id == $val->author && $auth->level == 3 && $val->date > time() - 1800) || (im_mod() && $val->date > time() - 86400) )) {
				$out .= ' <a href="/delete/' . $val->id . '" class="post-button post-delete delete-fast" title="Dzēst komentāru">dzēst</a>';
			}

			$out .= '</p>';
			if ($val->mb_removed == 1) {
				$out .= '<p class="deleted-entry">Saturs dzēsts!';
				// moderatoriem apskatāms dzēstā ieraksta saturs
				if (im_mod() && !$auth->mobile) {
					$out .= '<a style="float:right" class="deleted-content" href="/mbview/' . $val->id . '">skatīt saturu</a>';
				}
				$out .= '</p>';
			} else {
				$out .= '<div class="post-content">' . add_smile($val->text) . '</div>';
			}
			if ($auth->ok === true || $val->posts) {
				$out .= mb_recursive($data, $val->id, $level, $intro, $answer_limit, $closed, $disable_vote);
				$out .= '<div class="c"></div>';
			}
			if ($auth->ok === true && !$closed) {
				$out .= '<div class="reply-ph"></div>';
			}
			$out .= '</div></li>';
		}
	} elseif ($auth->ok === true) {
		$out .= '<li style="display:none"></li>';
	}
	$out .= '</ul>';
	return $out;
}

/**
 * Rekursīvi atrod minibloga atbildes limeni, pec id
 */
function get_mb_level($mbid, $level = 0) {
	global $db;
	$mb = $db->get_var("SELECT `reply_to` FROM `miniblog` WHERE `id` = '" . intval($mbid) . "'");
	if ($mb > 0 && $level < 30) {
		$level++;
		return get_mb_level($mb, $level);
	}
	return $level;
}

/**
 * Vienskaitļa/daudzskaitļa vārdi latviešu valodā
 */
function lv_dsk($num = 0, $single = 'atbilde', $multi = 'atbildes') {
	if ($num == 1 || (substr($num, -1) == '1' && substr($num, -2) != '11')) {
		return $single;
	} else {
		return $multi;
	}
}

function get_friends($user_id, $force = false) {
	global $db, $m;

	if ($force || !($friends = $m->get('friends_' . $user_id))) {
		$f1 = $db->get_col("SELECT `friend1` FROM `friends` WHERE `friend2` = $user_id AND `confirmed` = 1");
		$f2 = $db->get_col("SELECT `friend2` FROM `friends` WHERE `friend1` = $user_id AND `confirmed` = 1");
		$friends = (array) array_merge($f1, $f2);
		$m->set('friends_' . $user_id, $friends, false, 600);
	}

	return $friends;
}

/**
 * Uzlēcošais paziņojums, parādās lietotājam vienu reizi
 *
 * @param string $message
 * @param string $class (success/error)
 */
function set_flash($message, $class = 'error') {
	$_SESSION['flash_message'] = array(
		'message' => $message,
		'class' => $class
	);
}

function redirect($location = '/', $perm = false) {
	if ($perm) {
		header("HTTP/1.1 301 Moved Permanently");
	}
	header('Location: ' . $location);
	exit;
}

function get_latest_posts() {
	global $auth, $db, $lang, $comments_per_page, $config_domains;
	$out = '';

	$skip = 0;
	if (isset($_GET['pg'])) {
		$skip = 8 * intval($_GET['pg']);
	}

	$conditions = array();

	if ($lang == 1) {
		$add_langs = array("`pages`.`lang` = '1'");
		if (!empty($auth->show_code)) {
			$add_langs[] = "`pages`.`lang` = '3'";
		}
		if (!empty($auth->show_rp)) {
			$add_langs[] = "`pages`.`lang` = '5'";
		}
		if (!empty($auth->show_lol)) {
			$add_langs[] = "`pages`.`lang` = '7'";
		}
		if (!empty($auth->show_rs)) {
			$add_langs[] = "`pages`.`lang` = '9'";
		}
		$conditions[] = '(' . implode(' OR ', $add_langs) . ')';
	} else {
		$conditions[] = "`pages`.`lang` = '$lang'";
	}

	if ($auth->ok) {
		$ignores = $db->get_col("SELECT `category_id` FROM `cat_ignore` WHERE `user_id` = '$auth->id'");
		if (!empty($ignores)) {
			foreach ($ignores as $ignore) {
				$conditions[] = "`category` != $ignore";
			}
		}
	}

	$mods_only = '';
	if (!im_mod()) {
		$mods_only = " AND `cat`.`mods_only` = 0";
	}

	$latest = $db->get_results("SELECT
					`pages`.`title`,
					`pages`.`id`,`posts`,
					`pages`.`readby`,
					`pages`.`strid`,
					`pages`.`category`,
					`pages`.`lang`,
					`pages`.`bump`,
					`cat`.`mods_only`
				FROM
					`pages`,
					`cat`
				WHERE
					" . implode(' AND ', $conditions) . $mods_only . "
					AND `cat`.`id` = `pages`.`category`
				ORDER BY
					`pages`.`bump` DESC LIMIT $skip,8");

	if ($latest) {
		$out = '<ul id="latest-topics" class="blockhref">';
		foreach ($latest as $late) {
			$skip = '';
			if ($late->posts > $comments_per_page) {
				$posts = $db->get_var("SELECT count(*) FROM `comments` WHERE `pid` = $late->id AND `parent` = 0 AND `removed` = 0");
				if ($posts > $comments_per_page) {
					$skip = '/com_page/' . floor(($posts - 1) / $comments_per_page);
				}
			}

			$domain = '';
			$prefix = '';
			if ($late->lang != $lang) {
				$domain = '//' . $config_domains[$late->lang]['domain'];
				$prefix = '<span class="lp-prefix">' . $config_domains[$late->lang]['prefix'] . '</span> ';
			}
			$url = $domain . '/read/' . $late->strid;

			if (empty($config_domains[$late->lang]['ssl'])) {
				$url = 'http:' . $url;
			}

			if ($late->mods_only == 1) {
				$late->title = '<em>' . $late->title . '</em>';
			}

			if ($lang == 1) {
				$out .= '<li><a href="' . $url . $skip . '">';

				if (!empty($late->readby) && in_array($auth->id, unserialize($late->readby))) {
					$out .= $prefix . $late->title . '&nbsp;[' . $late->posts . ']<span class="post-time">pirms ' . time_ago(strtotime($late->bump)) . '</span></a></li>';
				} else {
					$out .= $prefix . $late->title . '&nbsp;[<span class="r">' . $late->posts . '</span>]<span class="post-time">pirms ' . time_ago(strtotime($late->bump)) . '</span></a></li>';
				}
			} else {
				$out .= '<li><a href="' . $url . $skip . '"><img src="//exs.lv/dati/bildes/topic-av/' . $late->id . '.jpg" class="av" alt="" />';

				$out .= '<span class="post-time">pirms ' . time_ago(strtotime($late->bump)) . '</span>';

				if (!empty($late->readby) && in_array($auth->id, unserialize($late->readby))) {
					$out .= $prefix . $late->title . '&nbsp;[' . $late->posts . ']</a></li>';
				} else {
					$out .= $prefix . $late->title . '&nbsp;[<span class="r">' . $late->posts . '</span>]</a></li>';
				}
			}
		}

		$out .= '</ul><p class="core-pager ajax-pager">';

		for ($i = 1; $i <= 5; $i++) {
			$out .= ' <a class="';
			if ($i == 1) {
				$out .= 'default-posts-tab ';
			}
			if ((isset($_GET['pg']) && $_GET['pg'] == ($i - 1)) || (!isset($_GET['pg']) && $i == 1)) {
				$out .= 'selected';
			}
			$out .= '" href="/latest.php?pg=' . ($i - 1) . '">' . $i . '</a>';
			if ($i != 5) {
				$out .= ' <span>-</span>';
			}
		}
		$out .= '</p>';
	}
	return $out;
}

function get_index_events() {
	global $db, $lang, $img_server;
	$out = '';
	$actions = $db->get_results("SELECT `user`, `action`, `avatar`, `time` FROM `userlogs` WHERE `lang` = '$lang' ORDER BY `time` DESC LIMIT 5");

	if ($actions) {
		$out .= '<ul class="user-actions">';
		foreach ($actions as $action) {

			$user = get_user($action->user);
			if (!$action->avatar) {
				$action->avatar = get_avatar($user, 's');
			}

			$action->avatar = str_replace('//img.exs.lv/dati', $img_server . '/dati', $action->avatar);

			$out .= '<li><img class="av" width="45" height="45" src="' . $action->avatar . '" alt="" /><div class="event-content"><span class="post-time">' . $user->nick . ' pirms ' . time_ago($action->time) . '</span><br />' . $action->action . '</div><div class="c"></div></li>';
		}
		$out .= '</ul>';
	}
	return $out;
}

function get_latest_images() {
	global $db, $auth, $lang, $img_server;

	if (isset($_GET['pg'])) {
		$skip = 15 * intval($_GET['pg']);
	} else {
		$skip = 0;
	}

	$int = "WHERE `images`.`lang` = '$lang'";
	if ($auth->ok && $lang == 1) {
		$interests = $auth->interests;
		$interests[] = 0;
		$int_q = implode(',', $interests);
		$int .= " AND `images`.`interest_id` IN(" . $int_q . ")";
	}

	$latest = $db->get_results("SELECT
		`images`.`uid` AS `uid`,
		`images`.`id` AS `id`,
		`images`.`posts` AS `posts`,
		`images`.`thb` AS `thb`,
		`images`.`url` AS `url`,
		`images`.`readby` AS `readby`,
		`users`.`nick` AS `nick`
	FROM
		`images`
	LEFT JOIN
		`users` ON  `images`.`uid` =  `users`.`id`
		" . $int . "
	ORDER BY
		`images`.`bump`
	DESC LIMIT $skip,15");

	$out = '<p class="latest-imgs">';
	if ($latest) {
		foreach ($latest as $late) {

			//fix for localhost
			if (empty($img_server)) {
				if (file_exists(CORE_PATH . '/' . $late->thb)) {
					$img = '/' . $late->thb;
				} else {
					$img = '//img.exs.lv/' . $late->thb;
				}
			} else {
				$img = $img_server . '/' . $late->thb;
			}

			$out .= '<a title="' . htmlspecialchars($late->nick) . '" href="/gallery/' . $late->uid . '/' . $late->id . '"><span class="container"><img src="' . $img . '" alt="" />';

			if (!empty($late->readby) && in_array($auth->id, unserialize($late->readby))) {
				$out .= '<span>' . $late->posts . '</span>';
			} else {
				$out .= '<span class="r">' . $late->posts . '</span>';
			}

			$out .= '</span></a> ';
		}
	}

	$out .= '</p><div class="clear"></div><p class="core-pager ajax-pager">';

	for ($i = 1; $i <= 5; $i++) {
		$out .= ' <a class="';
		if ($i == 1) {
			$out .= 'default-gallery-tab ';
		}
		if ((isset($_GET['pg']) && $_GET['pg'] == ($i - 1)) || (!isset($_GET['pg']) && $i == 1)) {
			$out .= 'selected';
		}
		$out .= '" href="/latest.php?type=images&pg=' . ($i - 1) . '">' . $i . '</a>';
		if ($i != 5) {
			$out .= ' <span>-</span>';
		}
	}
	$out .= '</p>';

	return $out;
}

function get_latest_mbs($friends = false) {
	global $auth, $db, $lang, $config_domains, $img_server;

	$out = '<ul id="friendssay-list" class="blockhref mb-col">';

	if (isset($_GET['pg'])) {
		$skip = 6 * intval($_GET['pg']);
	} else {
		$skip = 0;
	}

	if ($auth->level == 1) {
		$groupquery = '1 = 1';
	} else {
		$usergroups = array("`miniblog`.`groupid` = '0'");
		if ($auth->ok === true) {
			$g_owners = $db->get_col("SELECT id FROM clans WHERE owner = '$auth->id'");
			if ($g_owners) {
				foreach ($g_owners as $g_owner) {
					$usergroups[] = "`miniblog`.`groupid` = '" . $g_owner . "'";
				}
			}
			$g_members = $db->get_col("SELECT clan FROM clans_members WHERE user = '$auth->id' AND approve = '1'");
			if ($g_members) {
				foreach ($g_members as $g_member) {
					$usergroups[] = "`miniblog`.`groupid` = '" . $g_member . "'";
				}
			}
		}
		$groupquery = implode(' OR ', $usergroups);
	}

	if ($lang == 1) {
		$add_langs = array("`miniblog`.`lang` = '1'");
		if (!empty($auth->show_code)) {
			$add_langs[] = "`miniblog`.`lang` = '3'";
		}
		if (!empty($auth->show_rp)) {
			$add_langs[] = "`miniblog`.`lang` = '5'";
		}
		if (!empty($auth->show_lol)) {
			$add_langs[] = "`miniblog`.`lang` = '7'";
		}
		if (!empty($auth->show_rs)) {
			$add_langs[] = "`miniblog`.`lang` = '9'";
		}
		$addlang = '(' . implode(' OR ', $add_langs) . ')';
	} else {
		$addlang = "`miniblog`.`lang` = '$lang'";
	}

	$friendsquery = '';
	if ($lang == 9) { // rs projektā cilnes sadalās: mb ārpus grupām un grupās
		if ($friends) {
			$friendsquery = 'AND `miniblog`.`groupid` != 0';
		} else {
			$friendsquery = 'AND `miniblog`.`groupid` = 0';
		}
	} else if ($auth->ok && $friends) {
		$myfriends = get_friends($auth->id);
		$myfriends[] = $auth->id;
		$friendsquery = 'AND `miniblog`.`author` IN(' . implode(',', $myfriends) . ')';
	}

	//miniblogi kas nav publiski pieejami
	$priv = '';
	if (!$auth->ok) {
		$priv = ' AND `miniblog`.`private` = 0 ';
	}

	$mbs = $db->get_results("SELECT
		`miniblog`.`id` AS `id`,
		`miniblog`.`text` AS `text`,
		`miniblog`.`bump` AS `bump`,
		`miniblog`.`date` AS `date`,
		`miniblog`.`lang` AS `lang`,
		`miniblog`.`author` AS `author`,
		`miniblog`.`posts` AS `posts`,
		`miniblog`.`groupid` AS `groupid`,
		`users`.`avatar` AS `avatar`,
		`users`.`deleted` AS `deleted`,
		`users`.`av_alt` AS `av_alt`,
		`users`.`nick` AS `nick`
	FROM
		`miniblog` USE INDEX(`parent_2`),
		`users` USE INDEX(`PRIMARY`)
	WHERE
		`miniblog`.`removed` = '0' AND
		`miniblog`.`parent` = '0' AND
		`miniblog`.`type` = 'miniblog' AND
		" . $addlang . " AND
		(" . $groupquery . ") AND
		`users`.`id` = `miniblog`.`author`
		$friendsquery
		$priv
	ORDER BY
		`miniblog`.`bump`
	DESC LIMIT $skip, 6");

	if ($mbs) {
		foreach ($mbs as $mb) {
			$spec = '';

			$mb->text = mb_get_title($mb->text);
			$domain = '';
			$prefix = '';
			if ($mb->lang != $lang) {

				$domain = '//' . $config_domains[$mb->lang]['domain'];
				if (empty($config_domains[$mb->lang]['ssl'])) {
					$domain = 'http:' . $domain;
				}

				$spec = ' class="linkcode"';
				if ($mb->lang == 9) {
					$spec = ' class="rs-linkcode"';
				}
			}

			if ($mb->groupid != 0) {
				$spec = ' class="group"';
				$group = $db->get_row("SELECT `title`,`avatar`,`strid` FROM `clans` WHERE `id` = '$mb->groupid'");

				if ($group->avatar) {
					$group->av_alt = 1;
					$avatar = get_avatar($group, 's');
				}

				if (!empty($group->strid)) {
					$url = $domain . '/' . $group->strid . '/forum/' . base_convert($mb->id, 10, 36);
				} else {
					$url = $domain . '/group/' . $mb->groupid . '/forum/' . base_convert($mb->id, 10, 36);
				}
			} else {

				$avatar = get_avatar($mb, 's');

				$url = $domain . '/say/' . $mb->author . '/' . $mb->id . '-' . mb_get_strid($mb->text, $mb->id);
			}

			if (strpos($mb->text, 'spoiler') !== false) {
				$mb->text = preg_replace('/\[spoiler\](.*)\[\/spoiler\]/is', "(spoiler)", $mb->text);
			}

			$mb->text = wordwrap($mb->text, 36, "\n", 1);
			$mb->text = str_replace('/', "/<wbr />", $mb->text);

			if ($mb->groupid != 0) {
				$mb->text = '<em><span>@' . $group->title . '</span></em>' . textlimit($mb->text, 88, '...');
			} elseif ($mb->lang != $lang) {
				$mb->text = '<em><span>' . $config_domains[$mb->lang]['domain'] . '</span></em>' . textlimit($mb->text, 88, '...');
			} else {
				$mb->text = textlimit($mb->text, 98, '...');
			}

			$mb->text = str_replace('.', ".<wbr />", $mb->text);
			$mb->text = str_replace('-', "-<wbr />", $mb->text);

			if ($lang == 1) {
				$time = time_ago(strtotime($mb->date));
			} else {
				$time = time_ago($mb->bump);
			}

			if (!empty($mb->deleted)) {
				$mb->nick = 'dzēsts';
			}

			$out .= '<li' . $spec . '><a href="' . $url . '"><img class="av" width="45" height="45" src="' . $avatar . '" alt="' . htmlspecialchars($mb->nick) . '" /><span class="author">' . htmlspecialchars($mb->nick) . '</span> <span class="post-time">pirms ' . $time . '</span> ' . $mb->text . '&nbsp;[' . $mb->posts . ']</a></li>';
		}
	}
	$out .= '</ul><p class="core-pager ajax-pager">';

	$pager_add = '';
	if ($friends) {
		$pager_add .= '&amp;friendmb=true';
	}

	for ($i = 1; $i <= 5; $i++) {
		$out .= ' <a class="';
		if ($i == 1) {
			$out .= 'default-minibog-tab ';
		}
		if ((isset($_GET['pg']) && $_GET['pg'] == ($i - 1)) || (!isset($_GET['pg']) && $i == 1)) {
			$out .= 'selected';
		}
		$out .= '" href="/mb-latest?pg=' . ($i - 1) . $pager_add . '">' . $i . '</a>';
		if ($i != 5) {
			$out .= ' <span>-</span>';
		}
	}
	$out .= '</p>';
	return $out;
}

function set_action($action = '') {
	global $db, $auth;
	if ($auth->ok === true) {
		$db->query("UPDATE `users` SET `last_action` = '" . sanitize($action) . "' WHERE `id` = $auth->id LIMIT 1");
	}
}

function update_stats($category_id) {
	global $db;
	$stats = $db->get_row("SELECT COUNT(`pages`.`id`) AS `topics`, SUM(`pages`.`posts`) AS `posts`, SUM(`pages`.`views`) AS `views` FROM `pages` WHERE `category` = '$category_id'");
	$parent = $db->get_row("SELECT SUM(`stat_topics`) AS `topics`, SUM(`stat_com`) AS `posts`, SUM(`stat_views`) AS `views` FROM `cat` WHERE `parent` = '$category_id'");
	if (!empty($parent)) {
		$stats->topics += $parent->topics;
		$stats->posts += $parent->posts;
		$stats->views += $parent->views;
	}
	$db->query("UPDATE `cat` SET `stat_topics` = '$stats->topics', `stat_com` = '$stats->posts', `stat_views` = '$stats->views' WHERE id = '$category_id'");
	$db->update('cat', $category_id, array(
		'stat_topics' => $stats->topics,
		'stat_com' => $stats->posts,
		'stat_views' => $stats->views
	));
}

function post_mb($post) {
	global $db, $auth, $lang;

	$default = array(
		'groupid' => 0,
		'author' => $auth->id,
		'date' => 'NOW()',
		'text' => '',
		'private' => 0,
		'parent' => 0,
		'reply_to' => 0,
		'ip' => $auth->ip,
		'bump' => 0,
		'type' => 'miniblog',
		'lang' => $lang
	);

	$post = array_merge($default, $post);

	if (empty($post['parent']) && empty($post['bump'])) {
		$post['bump'] = time();
	}

	$fields = array();
	$data = array();
	foreach ($post as $title => $field) {
		$fields[] = '`' . $title . '`';
		if ($field == 'NOW()') {
			$data[] = $field;
		} else {
			$data[] = "'" . $field . "'";
		}
	}

	$db->query('INSERT INTO `miniblog` (' . implode(',', $fields) . ') VALUES (' . implode(',', $data) . ')');
	$return = $db->insert_id;

	if (substr($return, -6) === '000000') {
		$db->query("INSERT INTO autoawards (user_id,award,title,created) VALUES ('$auth->id','mb-1000000','Miljonā posta autors (" . substr($return, 0, -6) . ")',NOW())");
		$db->update('autoawards', $db->insert_id, array('importance' => $db->insert_id));
		push('Ieguva medaļu &quot;Miljonā posta autors (' . substr($return, 0, -6) . ')&quot;', '/dati/bildes/awards/mb-1000000.png');
	}

	if (!empty($post['parent'])) {
		if (isset($_POST['no-bump'])) {
			$db->query("UPDATE `" . $post['type'] . "` SET `posts` = `posts`+1 WHERE `id` = '" . $post['parent'] . "'");
			if (!empty($post['reply_to'])) {
				$db->query("UPDATE `miniblog` SET `posts` = `posts`+1 WHERE `id` = '" . $post['reply_to'] . "'");
			}
		} else {
			$db->query("UPDATE `" . $post['type'] . "` SET `bump` = '" . time() . "', `posts` = `posts`+1 WHERE `id` = '" . $post['parent'] . "'");
			if (!empty($post['reply_to'])) {
				$db->query("UPDATE `miniblog` SET `bump` = '" . time() . "', `posts` = `posts`+1 WHERE `id` = '" . $post['reply_to'] . "'");
			}
		}
	}

	update_karma($post['author']);

	return $return;
}

function pwd($pwd) {
	return hash('sha256', $pwd . 'hLaYVQ7TjapIBS8QWxf7jAn8eDKksq5LuCUrJ');
}

function human_filesize($bytes, $decimals = 2) {
	$sz = 'BKMGTP';
	$factor = floor((strlen($bytes) - 1) / 3);
	return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

/**
 * Atgriež lietotāja avataru pēc pieprasītā izmēra
 */
function get_avatar($user, $size = 'm', $ignore_mobile = false) {
	global $auth, $img_server;
	if (empty($auth->mobile) || $ignore_mobile) {
		$path = 'medium';
		$real_path = 'useravatar';
		if (($user->av_alt || !$user->avatar) && $size == 's') {
			$path = 'small';
			$real_path = 'u_small';
		} elseif (($user->av_alt || !$user->avatar) && $size == 'l') {
			$path = 'large';
			$real_path = 'u_large';
		}
		if (empty($user->avatar)) {
			$user->avatar = 'none.png';
		}

		//fix for avatars on localhost
		if (empty($img_server)) {

			if (file_exists(CORE_PATH . '/dati/bildes/' . $real_path . '/' . $user->avatar)) {
				//local avatar
				return '/dati/bildes/' . $real_path . '/' . $user->avatar;
			} else {
				//try to load from img.exs.lv anyway
				return '//img.exs.lv/userpic/' . $path . '/' . $user->avatar;
			}
		} else {
			return $img_server . '/userpic/' . $path . '/' . $user->avatar;
		}
	} else {
		if (empty($user->avatar)) {
			$user->avatar = 'none.png';
		}
		return '/av/' . $user->avatar;
	}
}

/**
 * Ja galerijas thumbnail izmērs ir nepareizs,
 * uzģenerē jaunu no lielā attēla
 */
function remake_thb($large, $thb) {
	$thb = CORE_PATH . '/' . $thb;
	$large = CORE_PATH . '/' . $large;
	$thb_size = getimagesize($thb);
	$size = 72;
	if ($thb_size[0] != $size) {
		exec("convert " . $large . " -resize '" . $size . "x" . $size . "^' -gravity center -crop " . $size . "x" . $size . "+0+0 +repage -strip " . $thb);
	}
}

/**
 * Masīvs ar lietotājiem, kuriem sodien ir cakeday
 * id => nick
 */
function get_cakeday() {
	global $db, $m;

	$out = array();

	if (($out = $m->get('cday_' . date('Y-m-d'))) === false) {

		$users = $db->get_results("SELECT `id`, `nick` FROM `users` WHERE `date` < '" . date('Y') . "-01-01 00:00:00' AND `date` LIKE '%-" . date('m') . "-" . date('d') . " %' ORDER BY `users`.`lastseen` DESC");
		if (!empty($users)) {
			foreach ($users as $user) {
				$out[$user->id] = $user->nick;
			}
		}
		$m->set('cday_' . date('Y-m-d'), $out, false, 3600);
	}

	return $out;
}

/**
 * Atgriež vērtību vai $empty, ja $val nav definēts
 */
function esr(&$val, $empty = '') {
	if (!empty($val)) {
		return $val;
	} else {
		return $empty;
	}
}

/**
 * Get users title based on karma level
 */
function custom_user_title($user) {
	if (empty($user->custom_title)) {
		if ($user->karma >= 500) {
			return 'Guru';
		} elseif ($user->karma >= 400) {
			return 'Dzīvo exā';
		} elseif ($user->karma >= 300) {
			return 'Atkarībnieks';
		} elseif ($user->karma >= 200) {
			return 'Profiņš';
		} elseif ($user->karma >= 100) {
			return 'Lietpratējs';
		} elseif ($user->karma >= 50) {
			return 'Savējais';
		} elseif ($user->karma >= 20) {
			return 'Biežais viesis';
		} else {
			return 'Jauniņais';
		}
	} else {
		return $user->custom_title;
	}
}

/**
 * Lietotāja profila izvēlne (tabi)
 */
function profile_menu($user, $active, $title, $action = null) {
	global $auth, $tpl, $page_title;

	if ($auth->ok) {
		if (empty($action)) {
			$action = $title;
		}
		set_action($user->nick . ' ' . $action);
	}

	$tpl->newBlock('profile-menu');
	$tpl->assign('user-menu-add', ' ' . $title);

	$tpl->assignGlobal(array(
		'user-id' => $user->id,
		'user-nick' => htmlspecialchars($user->nick),
		'active-tab-' . $active => 'active'
	));

	$page_title = $user->nick . ' ' . $title;
}
