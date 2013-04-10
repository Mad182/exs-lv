<?php

/**
 * functions.core.php
 * satur pmata funkcijas, kas vajadzīgas praktiski jebkurā lapas pieprasījumā
 * */
if (!function_exists('mb_ucfirst') && function_exists('mb_substr')) {

	function mb_ucfirst($string) {
		$string = mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
		return $string;
	}

}

/* aprekina un updato lietotja karmu */

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
		$voteval =
				$db->get_var("SELECT sum(vote_value) FROM comments WHERE author = '$user->id' AND removed = '0'") +
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

function build_latest() {
	@unlink(CORE_PATH . '/cache/blogs.html');
	destroy_cdir(CORE_PATH . '/cache/index/');
}

//saīsinājums prieks userlog aktuālajam lietotājam
function push($action, $avatar = '', $multi = '') {
	global $auth;
	if ($auth->ok === true) {
		return userlog($auth->id, $action, $avatar, $multi);
	} else {
		return false;
	}
}

//veic ierakstu leitotāja pēdējās darbībās
function userlog($user, $action, $avatar = '', $multi = '') {
	global $db, $lang;
	if (!empty($multi)) {
		$db->query("DELETE FROM `userlogs` WHERE `user` = '$user' AND `multi` = '$multi' AND `lang` = '$lang' LIMIT 2");
	}
	$db->query("INSERT INTO `userlogs` (time,user,avatar,action,multi,lang) VALUES ('" . time() . "','" . intval($user) . "','" . sanitize($avatar) . "','" . sanitize($action) . "','$multi','$lang')");
	return true;
}

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
			return 2;
		} else {
			$db->query("INSERT INTO `notify` (`user_id`,`type`,`foreign_key`,`bump`,`url`,`info`,`lang`) VALUES ('$user_id','$type','$place',NOW(),'$url','$info','$nlang')");
			return 1;
		}
	}
	return 0;
}

function get_notify($user_id, $base = '/events-pager?events-page=') {
	global $db, $lang, $new_msg_html, $auth; //man kauns :(
	$user_id = intval($user_id);
	$out = '';
	$texts = array(
		0 => 'atbilde komentāram',
		1 => 'komentārs galerijā',
		2 => 'komentārs rakstam',
		3 => 'atbilde miniblogā',
		4 => 'jauns biedrs tavā grupā',
		5 => 'tevi aicina draudzēties',
		6 => 'tev ir jauns draugs',
		7 => 'tu saņēmi medaļu',
		8 => 'tev atbildēja grupā',
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

		if ($notify = $db->get_results("SELECT * FROM `notify` WHERE `user_id` = '$user_id' ORDER BY `bump` DESC LIMIT $skip,$end")) {

			$out = '<ul id="user-notify">';
			foreach ($notify as $notify) {
				$add = '';

				$site = '';
				$addt = '';
				if ($auth->ok && $lang != $notify->lang) {
					$addt = $auth->transfer;
				}
				if ($notify->lang == $lang || in_array($notify->type, array(5, 6, 7, 9, 10, 11))) {
					$domain = '';
				} elseif ($notify->lang == 3) {
					$domain = 'http://coding.lv';
					$site = '&nbsp;<span class="site-name">coding.lv</span>';
				} elseif ($notify->lang == 5) {
					$domain = 'http://rp.exs.lv';
					$site = '&nbsp;<span class="site-name">rp.exs.lv</span>';
				} elseif ($notify->lang == 6) {
					$domain = 'http://lfs.lv';
					$site = '&nbsp;<span class="site-name">lfs.lv</span>';
				} elseif ($notify->lang == 7) {
					$domain = 'http://lol.exs.lv';
					$site = '&nbsp;<span class="site-name">lol.exs.lv</span>';
				} else {
					$domain = 'http://exs.lv';
					$site = '&nbsp;<span class="site-name">exs.lv</span>';
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
				$out .= 'href="' . $domain . $notify->url . $addt . '"><span class="notification-date">pirms ' . time_ago(strtotime($notify->bump)) . $site . '</span>' . $texts[$notify->type] . $add . '</a></li>';
			}
			$out .= '</ul>';

			$total = $db->get_var("SELECT count(*) FROM `notify` WHERE `user_id` = '$user_id' ORDER BY `bump` DESC LIMIT 25");
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

function usercolor($nick, $level = 0, $online = false, $userid = 0) {
	global $busers, $online_users, $site_admins, $site_mods, $auth, $cday_users;
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
	if(!empty($cday_users)) {
		if(!empty($cday_users[$userid]) || in_array($nick, $cday_users)) {
			$cakeday = '<img src="http://exs.lv/bildes/cakeday.png" alt="" title="Cake Day!" style="display:inline-block;width:16px;height:16px;" />';
		}
	}

	$nick = $star . htmlspecialchars($nick);

	if ($level == 1 || $userid != 0 && in_array($userid, $site_admins)) {
		$nick = '<span class="admins">' . $nick . '</span>';
	}
	if ($level == 2 || $userid != 0 && in_array($userid, $site_mods)) {
		$nick = '<span class="mods">' . $nick . '</span>';
	}
	if ($level == 3) {
		$nick = '<span class="rautors">' . $nick . '</span>';
	}
	if ($level == 5) {
		$nick = '<span class="bot">' . $nick . '</span>';
	}
	// SELĪNA :D
	if ($userid == 24668 && $auth->id == 24668) {
		$nick = '<span style="color:#ffc6f5;">S<span style="color:#ea89d9;">E</span>L<span style="color:#ea89d9;">Ī</span>N<span style="color:#ea89d9;">A</span></span>';
	}

	if ($online !== 'disable' && $userid && !empty($busers)) {
		if (!empty($busers[$userid])) {
			$nick = '<span class="banned">' . $nick . '</span>';
		}
	}

	return $nick . $cakeday;
}

/* parbauda vai aktīvais lietotājs ir moderators (vai admins) */

function im_mod() {
	global $auth;
	if ($auth->ok === true && ($auth->level == 1 || $auth->level == 2)) {
		return true;
	} else {
		return false;
	}
}

/* parbauda vai aktīvais lietotājs ir atvērtās sadaļas moderators */

function im_cat_mod($id = null) {
	global $auth, $category;
	if(!empty($id)) {
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

function utf8_substr($str, $from, $len) {
	return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $from . '}' .
					'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $len . '}).*#s', '$1', $str);
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
	}else
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

function mkslug($string) {
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
	$string = str_replace(array('----', '---', '--'), '-', str_replace(array('----', '---', '--'), '-', $string));
	$string = str_replace(array('----', '---', '--'), '-', str_replace(array('----', '---', '--'), '-', $string));
	$string = str_replace(array('----', '---', '--'), '-', str_replace(array('----', '---', '--'), '-', $string));
	if (substr($string, -1) == '-') {
		$string = substr($string, 0, -1);
	}
	if (substr($string, 0, 1) == '-') {
		$string = substr($string, 1);
	}
	$string = substr($string, 0, 100);
	if (empty($string)) {
		$string = 'page';
	}
	return strtolower($string);
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

function mkslug_itemsdb($string) {
	$bads = array('+', '/', ' ', 'ā', 'č', 'ē', 'ģ', 'ī', 'ķ', 'ļ', 'ņ', 'ŗ', 'š', 'ū', 'ž', 'Ā', 'Č', 'Ē', 'Ģ', 'Ī', 'Ķ', 'Ļ', 'Ņ', 'Ŗ', 'Š', 'Ū', 'Ž', '$', '&', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'ЫЬ', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'шщ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
	$good = array('-', '-', '-', 'a', 'c', 'e', 'g', 'i', 'k', 'l', 'n', 'r', 's', 'u', 'z', 'A', 'C', 'E', 'G', 'I', 'K', 'L', 'N', 'R', 'S', 'U', 'Z', 's', 'and', 'A', 'B', 'V', 'G', 'D', 'E', 'J', 'Z', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'C', 'S', 'S', 'T', 'T', 'E', 'Ju', 'Ja', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'z', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'c', 's', 't', 't', 'y', 'z', 'e', 'ju', 'ja');
	$string = str_replace($bads, $good, trim($string));
	$allowed = "/[^a-z0-9\\-\\_\\\\]/i";
	$string = preg_replace($allowed, '', $string);
	//$string = str_replace(array('---','--'),'-',$string);
	return $string;
}

function mkurl($type, $id, $title, $add = '') {
	if ($type === 'user') {
		return '/user/' . $id;
	}
	return '/' . $type . '/' . $id . '-' . mkslug($title) . $add;
}

function get_youtube_title_mb($videoid) {
	global $pagedesc;
	$safe = mkslug($videoid);
	$cach = get_youtube($safe);
	if (!$cach) {
		$contents = file_get_contents('http://gdata.youtube.com/feeds/api/videos/' . $videoid);
		if (stristr($contents, "Syndication of this video was restricted by its owner")) {
			$restricted = 1;
		} else {
			$restricted = 0;
		}
		$title = sanitize(get_between($contents, "<media:title type='plain'>", '</media:title>'));
		$description = sanitize(get_between($contents, "<media:description type='plain'>", '</media:description>'));
	} else {
		$title = $cach->yt_title;
		$description = $cach->yt_description;
	}
	if (!$pagedesc) {
		$pagedesc = stripslashes(str_replace(array('\n', '\r'), ' ', $description));
	}
	return ' Video: ' . $title . ' ';
}

function get_youtube_video_small($videoid) {
	global $db, $is_miniblog, $force_tag_update, $auth;
	$safe = mkslug($videoid);
	$video = get_youtube($safe);
	if (!$video || !empty($force_tag_update)) {

		$contents = file_get_contents('http://gdata.youtube.com/feeds/api/videos/' . $videoid);
		if ($contents) {
			if (stristr($contents, "Syndication of this video was restricted by its owner")) {
				$restricted = 1;
			} else {
				$restricted = 0;
			}
			$title = sanitize(stripslashes(get_between($contents, "<media:title type='plain'>", '</media:title>')));
			$description = sanitize(stripslashes(str_replace(array('\\n', '\n'), ' ', get_between($contents, "<media:description type='plain'>", '</media:description>'))));
			if (!$video) {
				$db->query("INSERT INTO ytlocal (yt_id,yt_title,yt_description,yt_restricted) VALUES ('$safe','$title','$description','$restricted')");
			}

			//automatiski pieliek tagus rakstam
			$data = simplexml_load_string($contents);
			if (!empty($is_miniblog)) {
				include_once(CORE_PATH . '/includes/class.tags.php');
				$tags = new tags;
				$i = 0;
				foreach ($data->category as $cat) {
					if (strlen($cat['term']) < 30 && strlen($cat['term']) > 2 && $i < 6 && substr($cat['term'], 0, 3) != 'Yt:') {
						$newtag = sanitize(mb_ucfirst(strtolower(trim($cat['term']))));
						$nslug = mkslug($cat['term']);
						if (!empty($newtag)) {
							$tagid = $db->get_var("SELECT id FROM tags WHERE slug = '$nslug'");
							if ($tagid) {
								$tags->add_tag($is_miniblog, $tagid, 2);
							} else {
								$db->query("INSERT INTO tags (name,slug) VALUES ('$newtag','$nslug')");
								$tagid = $db->get_var("SELECT id FROM tags WHERE slug = '$nslug'");
								$tags->add_tag($is_miniblog, $tagid, 2);
							}
						}
						$i++;
					}
				}
			}
		} else {
			$title = 'Video';
			if (!$video) {
				$db->query("INSERT INTO ytlocal (yt_id,yt_title) VALUES ('$safe','Youtube video')");
			}
		}
	} else {
		$title = $video->yt_title;
		$description = $video->yt_description;
		$restricted = $video->yt_restricted;
	}
	$title = str_replace("'", "&#39;", htmlspecialchars(textlimit(stripslashes($title), 100)));
	$title = str_replace("&amp;amp;", "&amp;", $title);
	if ($auth->ok === true) {
		$videocode = htmlspecialchars('<div class="auto-embed" style="width:380px;"><iframe class="youtube-player" type="text/html" width="380" height="240" src="http://www.youtube.com/embed/' . $videoid . '?wmode=transparent&autoplay=1&origin=' . urlencode('http://exs.lv') . '" frameborder="0"></iframe><br /><a title="Atvērt video mājas lapā" href="http://www.youtube.com/watch?v=' . $videoid . '" target="_blank" rel="nofollow">YouTube video</a> <strong>' . $title . '</strong><div class="c"></div></div>');
		return '<div><div class="auto-embed-placeholder"><img width="240" height="180" src="http://i4.ytimg.com/vi/' . $videoid . '/0.jpg" alt="' . $title . '" /><a class="play-button" onclick="$(this).parent().parent().html(\'' . $videocode . '\');return false;" rel="nofollow" title="Atskaņot ' . $title . '" href="http://www.youtube.com/watch?v=' . $videoid . '"><span><span>' . $title . '</span></span></a></div></div>';
	} else {
		return '<div><div class="auto-embed-placeholder"><img width="240" height="180" src="http://i4.ytimg.com/vi/' . $videoid . '/0.jpg" alt="' . $title . '" /><a class="play-button" rel="nofollow" title="Atskaņot ' . $title . '" href="http://www.youtube.com/watch?v=' . $videoid . '"><span><span>' . $title . '</span></span></a></div></div>';
	}
}

function get_youtube_video($videoid) {
	global $db, $article, $auth;
	$safe = mkslug($videoid);
	$video = get_youtube($safe);
	if (!$video) {
		$contents = file_get_contents('http://gdata.youtube.com/feeds/api/videos/' . $videoid);
		if ($contents) {
			if (stristr($contents, "Syndication of this video was restricted by its owner")) {
				$restricted = 1;
			} else {
				$restricted = 0;
			}
			$title = sanitize(stripslashes(get_between($contents, "<media:title type='plain'>", '</media:title>')));
			$description = sanitize(stripslashes(get_between($contents, "<media:description type='plain'>", '</media:description>')));
			$db->query("INSERT INTO ytlocal (yt_id,yt_title,yt_description,yt_restricted) VALUES ('$safe','$title','$description','$restricted')");

			//automatiski pieliek tagus rakstam
			$data = simplexml_load_string($contents);
			if (!empty($article)) {
				include_once(CORE_PATH . '/includes/class.tags.php');
				$tags = new tags;
				$i = 0;
				foreach ($data->category as $cat) {
					if (strlen($cat['term']) < 30 && strlen($cat['term']) > 2 && $i < 10 && substr($cat['term'], 0, 3) != 'Yt:') {
						$newtag = sanitize(mb_ucfirst(strtolower(trim($cat['term']))));
						$nslug = mkslug($cat['term']);
						if (!empty($newtag)) {
							$tagid = $db->get_var("SELECT id FROM tags WHERE slug = '$nslug'");
							if ($tagid) {
								$tags->add_tag($article->id, $tagid);
							} else {
								$db->query("INSERT INTO tags (name,slug) VALUES ('$newtag','$nslug')");
								$tagid = $db->get_var("SELECT id FROM tags WHERE slug = '$nslug'");
								$tags->add_tag($article->id, $tagid);
							}
						}
						$i++;
					}
				}
			}
		} else {
			$title = 'Video';
		}
	} else {
		$title = $video->yt_title;
		$description = $video->yt_description;
		$restricted = $video->yt_restricted;
	}
	$title = str_replace("'", "&#39;", htmlspecialchars(textlimit(stripslashes($title), 100)));
	$title = str_replace("&amp;amp;", "&amp;", $title);

	if ($auth->ok === true) {
		$videocode = htmlspecialchars('<div class="c"></div><div class="auto-embed" style="width:520px;"><iframe class="youtube-player" type="text/html" width="520" height="290" src="http://www.youtube.com/embed/' . $videoid . '?wmode=transparent&autoplay=1&origin=' . urlencode('http://exs.lv') . '" frameborder="0"></iframe><br /><a title="Atvērt video mājas lapā" href="http://www.youtube.com/watch?v=' . $videoid . '" target="_blank" rel="nofollow">YouTube video</a> <strong>' . $title . '</strong><div class="c"></div></div> ');
		return '<div><div class="auto-embed-placeholder"><img width="240" height="180" src="http://i4.ytimg.com/vi/' . $videoid . '/0.jpg" alt="' . $title . '" /><a class="play-button" onclick="$(this).parent().parent().html(\'' . $videocode . '\');return false;" title="Atskaņot ' . $title . '" rel="nofollow" href="http://www.youtube.com/watch?v=' . $videoid . '"><span><span>' . $title . '</span></span></a></div></div>';
	} else {
		return '<div><div class="auto-embed-placeholder"><img width="240" height="180" src="http://i4.ytimg.com/vi/' . $videoid . '/0.jpg" alt="' . $title . '" /><a class="play-button" title="Atskaņot ' . $title . '" rel="nofollow" href="http://www.youtube.com/watch?v=' . $videoid . '"><span><span>' . $title . '</span></span></a></div></div>';
	}
}

function add_smile($txt, $wide = 0, $disable_emotions = 0) {

	if (!$disable_emotions) {
		$smilies = array(
			':D' => 'smiley-grin.png',
			':)' => 'smiley.png',
			':(' => 'smiley-sad.png',
			';)' => 'smiley-wink.png',
			';(' => 'smiley-cry.png',
			'8=)' => 'smiley-cool.png',
			':cool:' => 'smiley-cool.png',
			':sweat:' => 'smiley-sweat.png',
			':P' => 'smiley-razz.png',
			':o:' => 'smiley-surprise.png',
			':|' => 'smiley-neutral.png',
			':lol:' => 'smiley-lol.png',
			':mrgreen:' => 'mrgreen.gif',
			':eek:' => 'smiley-eek.png',
			':roll:' => 'smiley-roll.png',
			':cat:' => 'smiley-kitty.png',
			':kitty:' => 'smiley-kitty.png',
			':minka:' => 'smiley-kitty.png',
			':confused:' => 'smiley-confuse.png',
			':nerd:' => 'smiley-nerd.png',
			':sleep:' => 'smiley-sleep.png',
			':fat:' => 'smiley-fat.png',
			':evil:' => 'smiley-evil.png',
			':twist:' => 'smiley-twist.png',
			':red:' => 'smiley-red.png',
			':blush:' => 'smiley-red.png',
			':yell:' => 'smiley-yell.png',
			':slim:' => 'smiley-slim.png',
			':money:' => 'smiley-money.png',
			':cry:' => 'smiley-cry.png',
			':kiss:' => 'smiley-kiss.png',
			':sad:' => 'smiley-sad.png',
			':angel:' => 'smiley-angel.png',
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
			':heart:' => 'heart.png',
			':sirds:' => 'heart.png',
			':game:' => 'game.png',
			':apple:' => 'fruit.png',
			':candle:' => 'candle.png',
			':candle-white:' => 'candle-white.png',
			':beer:' => 'beer.gif',
			':alus:' => 'beer.gif',
			':rofl:' => 'rofl.gif',
			':latvija:' => 'latvija.gif',
			':audi:' => 'kissmyrings.gif',
			':shura:' => 'shura.gif',
			':rock:' => 'rock.gif',
			':geek:' => 'icon_geek.gif',
			':mjau:' => 'mjau.gif',
			':hihi:' => 'hihi.gif',
			':thumb:' => 'icon_thumleft.gif',
			':crazy:' => 'crazy.gif',
			':rokas:' => 'rokas.gif',
			':facepalm:' => 'facepalm.gif',
			':ile:' => 'loveexs.gif',
			':pohas:' => 'pohas.gif',
			':cepure:' => 'cepure.gif',
			':ban:' => 'ban.gif',
			':tease:' => 'tease.gif',
			':agresivs:' => 'agresivs.gif',
			':slims:' => 'ill.gif',
			':zzz:' => 'lazy.gif',
			':yahoo:' => 'yahoo.gif',
			':shock:' => 'shok.gif',
			':fool:' => 'fool.gif'
		);

		foreach ($smilies as $key => $val) {
			if (strpos($txt, $key) !== false) { //speeds things up
				$txt = str_replace($key, ' <img src="http://exs.lv/bildes/fugue-icons/' . $val . '" alt="' . $val . '" /> ', $txt);
			}
		}
	}

	$txt = str_replace('="/bildes', '="http://exs.lv/bildes', $txt);
	$txt = str_replace('="/dati/bildes', '="http://exs.lv/dati/bildes', $txt);
	$txt = str_replace('="/upload/', '="http://exs.lv/upload/', $txt);

	//friendly domains, no nofollow
	$txt = str_replace(' rel="nofollow" href="http://akredits.lv', ' href="http://akredits.lv', $txt);
	
	$txt = str_replace(' rel="nofollow" href="http://lfs.lv', ' href="http://lfs.lv', $txt);
	$txt = str_replace(' rel="nofollow" href="http://www.lfs.lv', ' href="http://lfs.lv', $txt);
	
	$txt = str_replace(' rel="nofollow" href="http://ezgif.com', ' href="http://ezgif.com', $txt);
	$txt = str_replace(' rel="nofollow" href="http://gif-avatars.com', ' href="http://gif-avatars.com', $txt);
	
	$txt = str_replace(' rel="nofollow" href="http://www.coding.lv', ' href="http://coding.lv', $txt);
	$txt = str_replace(' rel="nofollow" href="http://coding.lv', ' href="http://coding.lv', $txt);
	
	$txt = str_replace(' rel="nofollow" href="http://exs.lv', ' href="http://exs.lv', $txt);
	$txt = str_replace(' rel="nofollow" href="http://www.exs.lv', ' href="http://exs.lv', $txt);
	$txt = str_replace(' rel="nofollow" href="http://img.exs.lv', ' href="http://img.exs.lv', $txt);
	$txt = str_replace(' rel="nofollow" href="http://rp.exs.lv', ' href="http://rp.exs.lv', $txt);
	
	$txt = str_replace(' rel="nofollow" href="http://openidea.lv', ' href="http://openidea.lv', $txt);
	$txt = str_replace(' rel="nofollow" href="http://www.openidea.lv', ' href="http://openidea.lv', $txt);
	
	$txt = str_replace(' rel="nofollow" href="http://nvsk.lv', ' href="http://nvsk.lv', $txt);
	
	$txt = str_replace(' rel="nofollow" href="http://grab.lv', ' href="http://grab.lv', $txt);
	$txt = str_replace(' rel="nofollow" href="http://www.grab.lv', ' href="http://www.grab.lv', $txt);
	
	$txt = str_replace(' rel="nofollow" href="http://irdarbs.lv', ' href="http://irdarbs.lv', $txt);
	$txt = str_replace(' rel="nofollow" href="http://www.irdarbs.lv', ' href="http://irdarbs.lv', $txt);
	
	$txt = str_replace(' rel="nofollow" href="http://otrapuse.lv', ' href="http://otrapuse.lv', $txt);
	$txt = str_replace(' rel="nofollow" href="http://www.otrapuse.lv', ' href="http://otrapuse.lv', $txt);

	
	//aizvieto lapas kas ievietotas `blacklisted_sites` ar linku uz /ES_SPAMOJU_SUDUS
	$blacklisted_sites = get_blacklisted_sites();
	foreach($blacklisted_sites as $site) {
		if (strpos($txt, $site) !== false) {
		
			$replace = array(
				'http://'.$site,
				'https://'.$site,
				'http://www.'.$site,
				'https://www.'.$site,
			);

			$txt = str_ireplace($replace, '/ES_SPAMOJU_SUDUS', $txt);
		}
	}

	$txt = str_replace(array(
		'.space.lv',
		'CoxFr2Kobuw',
		'T_bn77at0zA',
		'Vy1zWWGzL0Q',
		'playpro.lv',
		'MOBM1ODD',
		's.exs.lv/63',
		'MODAPPLICATIONRUNE.TK',
		'?ref=',
		'91.135.84.135',
		'servics-',
		'servces-',
		'.org/lan.',
		'4f200c32f12e7.jpg'
			), 'ES_SPAMOJU_SUDUS', $txt);


	$txt = str_replace(array(
		'/ref.php',
		'/referrer/'
			), '/ES_SPAMOJU_SUDUS/', $txt);


	if (strpos($txt, 'spoiler') !== false) {
		$txt = preg_replace('/\[spoiler\](.*)\[\/spoiler\]/iseU', 'replace_spoiler("\\1")', $txt);
	}

	if ($wide) {
		$txt = preg_replace("#(^|[\n ]|<a(.*?)>)http://(www\.)?youtube\.com/watch\?v=([a-zA-Z0-9\-_]+)((.*?)</a>)?#ime", 'get_youtube_video("\\4")', $txt);
		$txt = preg_replace("#(^|[\n ]|<a(.*?)>)http://(www\.)?youtu\.be/([a-zA-Z0-9\-_]+)((.*?)</a>)?#ime", 'get_youtube_video("\\4")', $txt);
	} else {
		$txt = preg_replace("#(^|[\n ]|<a(.*?)>)http://(www\.)?youtube\.com/watch\?v=([a-zA-Z0-9\-_]+)((.*?)</a>)?#ime", 'get_youtube_video_small("\\4")', $txt);
		$txt = preg_replace("#(^|[\n ]|<a(.*?)>)http://(www\.)?youtu\.be/([a-zA-Z0-9\-_]+)((.*?)</a>)?#ime", 'get_youtube_video_small("\\4")', $txt);
	}

	return $txt;
}


/* atgriež masīvu ar bloķētiem mājas lapu domēniem */
function get_blacklisted_sites() {
	global $db, $m, $blacklisted_sites;
	if(empty($blacklisted_sites)) {
		if (($blacklisted_sites = $m->get('blacklisted_sites')) === false) {
			$blacklisted_sites = $db->get_col("SELECT `url` FROM `blacklisted_sites`");
			$m->set('blacklisted_sites', $blacklisted_sites, false, 3600);
		}
	}
	return $blacklisted_sites;
}


function replace_spoiler($text) {
	$text = str_replace(array('<p>', '</p>'), array('<br />', '<br />'), $text);
	return '<span class="spoiler"><a href="javascript:void(0);" class="spoiler-title" title="Slēpt/rādīt spoilera saturu">Rādīt spoileri</a><br /><span style="display:none" class="spoiler-content">'.$text.'</span></span>';
}


function hide_spoilers($text) {
	if (strpos($text, 'spoiler') !== false) {
		$text = preg_replace('/\[spoiler\](.*)\[\/spoiler\]/is', "(spoiler) ", $text);
		$text = str_replace('  ', ' ', $text);
	}
	return $text;
}


function mention($text, $url = '#', $type = 'notype', $uniq = 0) {

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

	if (!empty($usr) && !in_array($nick, array('exs', 'inbox', 'gmail', 'mail')) && $mention_counter <= 6) {
		$mention_counter++;

		if ($type == 'mb') {
			if (!empty($uniq)) {
				$mb = $db->get_row("SELECT * FROM `miniblog` WHERE `id` = '" . intval($uniq) . "'");
				$title = mb_get_title($mb->text);
				$strid = mb_get_strid($title, $mb->id);
				$url = '/say/' . $mb->author . '/' . $mb->id . '-' . $strid;
				if ($mb->author != $usr->id && $usr->id != $auth->id) {
					notify($usr->id, 14, $mb->id, $url);
				}
			}
		}

		if ($type == 'group') {
			if (!empty($uniq)) {
				$mb = $db->get_row("SELECT * FROM `miniblog` WHERE `id` = '" . intval($uniq) . "'");
				$title = mb_get_title($mb->text);
				$url = '/group/' . $mb->groupid . '/forum/' . base_convert($mb->id, 10, 36);
				if ($mb->author != $usr->id && $usr->id != $auth->id) {
					notify($usr->id, 13, $mb->id, $url);
				}
			}
		}

		if ($type == 'page') {
			if (!empty($uniq)) {
				$mb = $db->get_row("SELECT * FROM `pages` WHERE `id` = '" . intval($uniq) . "'");
				if ($mb->author != $usr->id && $usr->id != $auth->id) {
					notify($usr->id, 15, $mb->id, $url);
				}
			}
		}

		if ($type == 'junk') {
			if (!empty($uniq)) {
				$url = '/junk/' . $uniq;
				if ($mb->author != $usr->id && $usr->id != $auth->id) {
					notify($usr->id, 15, $uniq, $url);
				}
			}
		}

		return '<a class="post-mention" href="/user/' . $usr->id . '"><span class="at-sign">@</span>' . usercolor($usr->nick, $usr->level, 'disable', $usr->id) . '</a>';
	} else {
		return '@' . $nick;
	}
}

function get_youtube_title($videoid) {
	$safe = mkslug($videoid);
	$video = get_youtube($safe);
	if (!$video) {
		$contents = file_get_contents('http://gdata.youtube.com/feeds/api/videos/' . $videoid);
		$title = get_between($contents, "<media:title type='plain'>", '</media:title>');
	} else {
		$title = $video->yt_title;
	}
	return ' Video: ' . $title . ' ';
}

function createPassword($length) {
	$chars = "1234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()-+_=|/.,";
	$i = 0;
	$password = "";
	while ($i <= $length) {
		$password .= $chars{mt_rand(0, strlen($chars))};
		$i++;
	}
	return $password;
}

function tla_ads($config_cache = "local_307458.xml", $config_key = "BCMVOQLYMIYCAINOCKZ1") {
	$CONNECTION_TIMEOUT = 10;
	$LOCAL_XML_FILENAME = "cache/" . $config_cache;
	if (!file_exists($LOCAL_XML_FILENAME))
		pr("Script error: $LOCAL_XML_FILENAME does not exist. Please create a blank file named $LOCAL_XML_FILENAME.");
	if (!is_writable($LOCAL_XML_FILENAME))
		pr("Script error: $LOCAL_XML_FILENAME is not writable. Please set write permissions on $LOCAL_XML_FILENAME.");

	if (filemtime($LOCAL_XML_FILENAME) < (time() - 3600) || filesize($LOCAL_XML_FILENAME) < 20) {
		$request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "";
		$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
		tla_updateLocalXML("http://www.text-link-ads.com/xml.php?inventory_key=".$config_key."&referer=" . urlencode($request_uri) . "&user_agent=" . urlencode($user_agent), $LOCAL_XML_FILENAME, $CONNECTION_TIMEOUT);
	}
	$xml = tla_getLocalXML($LOCAL_XML_FILENAME);
	$arr_xml = tla_decodeXML($xml);
	$ret = '';
	if (is_array($arr_xml)) {
		for ($i = 0; $i < count($arr_xml['URL']); $i++) {
			if (isset($arr_xml['PostID'][$i]) && $arr_xml['PostID'][$i] > 0)
				continue;
			$ret .= '<li>' . $arr_xml['BeforeText'][$i] . ' <a href="' . $arr_xml['URL'][$i] . '">' . $arr_xml['Text'][$i] . '</a> ' . $arr_xml['AfterText'][$i] . '</li>';
		}
	}
	return $ret;
}

function tla_updateLocalXML($url, $file, $time_out) {
	if ($handle = fopen($file, "a")) {
		fwrite($handle, "\n");
		fclose($handle);
	}
	if ($xml = file_get_contents_tla($url, $time_out)) {
		$xml = substr($xml, strpos($xml, '<?'));

		if ($handle = fopen($file, "w")) {
			fwrite($handle, $xml);
			fclose($handle);
		}
	}
}

function tla_getLocalXML($file) {
	$contents = "";
	if ($handle = fopen($file, "r")) {
		$contents = fread($handle, filesize($file) + 1);
		fclose($handle);
	}
	return $contents;
}

function file_get_contents_tla($url, $time_out) {
	$result = "";
	$url = parse_url($url);
	if ($handle = @fsockopen($url["host"], 80)) {
		if (function_exists("socket_set_timeout")) {
			socket_set_timeout($handle, $time_out, 0);
		} else if (function_exists("stream_set_timeout")) {
			stream_set_timeout($handle, $time_out, 0);
		}
		fwrite($handle, "GET $url[path]?$url[query] HTTP/1.0\r\nHost: $url[host]\r\nConnection: Close\r\n\r\n");
		while (!feof($handle)) {
			$result .= @fread($handle, 40960);
		}
		fclose($handle);
	}
	return $result;
}

function tla_decodeXML($xmlstg) {
	if (!function_exists('html_entity_decode')) {

		function html_entity_decode($string) {
			$string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\1"))', $string);
			$string = preg_replace('~&#([0-9]+);~e', 'chr(\1)', $string);
			$trans_tbl = get_html_translation_table(HTML_ENTITIES);
			$trans_tbl = array_flip($trans_tbl);
			return strtr($string, $trans_tbl);
		}

	}
	$out = "";
	$retarr = "";
	preg_match_all("/<(.*?)>(.*?)</", $xmlstg, $out, PREG_SET_ORDER);
	$search_ar = array('&#60;', '&#62;', '&#34;');
	$replace_ar = array('<', '>', '"');
	$n = 0;
	while (isset($out[$n])) {
		$retarr[$out[$n][1]][] = str_replace($search_ar, $replace_ar, html_entity_decode(strip_tags($out[$n][0])));
		$n++;
	}
	return $retarr;
}

function utf8_strtolower($string) {
	$string = str_replace(array('Ā', 'Č', 'Ē', 'Ģ', 'Ī', 'Ķ', 'Ļ', 'Ņ', 'Õ', 'Š', 'Ū', 'Ž'), array('ā', 'č', 'ē', 'ģ', 'ī', 'ķ', 'ļ', 'ņ', 'õ', 'š', 'ū', 'ž'), $string);
	return strtolower($string);
}

function strBytes($str) {
	$strlen_var = strlen($str);
	$d = 0;
	for ($c = 0; $c < $strlen_var; ++$c) {
		$ord_var_c = ord($str{$d});
		switch (true) {
			case (($ord_var_c >= 0x20) && ($ord_var_c <= 0x7F)):
				$d++;
				break;
			case (($ord_var_c & 0xE0) == 0xC0):
				$d+=2;
				break;
			case (($ord_var_c & 0xF0) == 0xE0):
				$d+=3;
				break;
			case (($ord_var_c & 0xF8) == 0xF0):
				$d+=4;
				break;
			case (($ord_var_c & 0xFC) == 0xF8):
				$d+=5;
				break;
			case (($ord_var_c & 0xFE) == 0xFC):
				$d+=6;
				break;
			default:
				$d++;
		}
	}
	return $d;
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

function display_time($time) {
	if ($time >= strtotime('today')) {
		$out = 'Šodien, ' . date('G:i', $time);
	} elseif ($time >= strtotime('yesterday')) {
		$out = 'Vakar, ' . date('G:i', $time);
	} else {
		$out = date_lv('j. F, Y G:i', $time);
	}
	return $out;
}

function display_time_simple($time) {
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

function display_date_simple($time) {
	if (!$time) {
		$out = '';
	} elseif ($time >= strtotime('today')) {
		$out = 'Šodien';
	} elseif ($time >= strtotime('yesterday')) {
		$out = 'Vakar';
	} else {
		$out = date('d.m.Y.', $time);
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

function destroy_cdir($dir = 'cache/index/') {
	$mydir = opendir($dir);
	while (false !== ($file = readdir($mydir))) {
		if ($file != "." && $file != "..") {
			chmod($dir . $file, 0777);
			if (is_dir($dir . $file)) {
				chdir('.');
				destroy($dir . $file . '/');
				rmdir($dir . $file) or DIE("couldn't delete $dir$file<br />");
			}
			else
				unlink($dir . $file) or DIE("couldn't delete $dir$file<br />");
		}
	}
	closedir($mydir);
}

function pr($data) {
	global $debug;
	if ($debug) {
		echo '<pre style="color: #eee;background: #123;border: 1px solid #111;padding: 5px 10px;font-size:10px;line-height: 13px; margin: 0 0 10px;">';
		var_dump($data);
		echo '</pre>';
	}
}

function file_force_contents($dir, $contents) {
	$parts = explode('/', $dir);
	$file = array_pop($parts);
	$dir = '';
	foreach ($parts as $part) {
		if (!is_dir($dir .= "$part/")) {
			mkdir($dir);
		}
	}
	file_put_contents("$dir/$file", $contents);
}

function strTime($s) {
	$d = intval($s / 86400);
	$s -= $d * 86400;
	$h = intval($s / 3600);
	$s -= $h * 3600;
	$m = intval($s / 60);
	$s -= $m * 60;
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
	$body = preg_replace("#(^|[\n ]|<a(.*?)>)http://(www\.)?youtube\.com/watch\?v=([a-zA-Z0-9\-_]+)((.*?)</a>)?#ime", 'get_youtube_title("\\4") ', $body);
	$body = preg_replace("#(^|[\n ]|<a(.*?)>)http://(www\.)?youtu\.be/([a-zA-Z0-9\-_]+)((.*?)</a>)?#ime", 'get_youtube_title("\\4")', $body);
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

function get_top_awards($user) {
	global $db, $m;
	$user = (int) $user;
	if (($data = $m->get('aw_' . $user)) === false) {
		$data = '';
		$res = $db->get_results("SELECT `id`,`award`,`title` FROM `autoawards` WHERE `user_id` = '$user' ORDER BY `importance` DESC LIMIT 4");
		if ($res) {
			$data .= '<p style="margin:0;padding:4px 0 10px">';
			foreach ($res as $award) {
				$data .= '<img width="32" height="32" src="http://exs.lv/dati/bildes/awards/' . $award->award . '.png" alt="' . $award->award . '" title="' . htmlspecialchars(strip_tags($award->title)) . '" />&nbsp;';
			}
			$total = $db->get_var("SELECT count(*) FROM `autoawards` WHERE `user_id` = '$user'");
			if ($total > 4) {
				$data .= '<a style="color:#777" title="Visas ' . $total . ' medaļas" href="/awards/' . $user . '">(' . $total . ')</a>';
			}
			$data .= '</p>';
		}
		$m->set('aw_' . $user, $data, false, 3600);
	}
	return $data;
}

/*
  awardu saraksts,
  secībā sākot no mazākajiem un mazāk nozīmīgajiem līdz svarīgākajiem,
  piem 20 draugi jāliek pirms 50.
  lietotājs pats pēc tam varēs pārkārtot, ja gribēs.
  profilā rādīs tikai svarīgākos.

  speciālos - piem veterāns vai ala te neliksim, tos varēs piešķirt manuāli,
  ievietojot ierakstu db tabulā, šeit tikai tos, kurus updato automātiski
 */

function list_awards() {
	return array(
		'first-post' => array(
			'title' => 'Pirmie 5 posti ;)',
			'state' => 'inactive'
		),
		'avatar-have' => array(
			'title' => 'Uzlika sev avataru',
			'state' => 'inactive'
		),
		'group-created' => array(
			'title' => 'Izveidoja grupu',
			'state' => 'inactive'
		),
		'popular' => array(
			'title' => 'Populārs (apskatīja 100 biedri)',
			'state' => 'inactive'
		),
		'ingroup-5' => array(
			'title' => '5 grupu biedrs',
			'state' => 'inactive'
		),
		'group-100' => array(
			'title' => 'Izveidoja grupu ar 100 biedriem',
			'state' => 'inactive'
		),
		'friends-20' => array(
			'title' => 'Sadraudzējās ar 20 lietotājiem',
			'state' => 'inactive'
		),
		'friends-50' => array(
			'title' => 'Sadraudzējās ar 50 lietotājiem',
			'state' => 'inactive'
		),
		'gallery' => array(
			'title' => 'Ievietoja bildi galerijā',
			'state' => 'inactive'
		),
		'blog-have' => array(
			'title' => 'Ieguva blogu',
			'state' => 'inactive'
		),
		'messages-100' => array(
			'title' => 'Nosūtīja 100 vēstules',
			'state' => 'inactive'
		),
		'topics-20' => array(
			'title' => 'Izveidoja 20 diskusijas',
			'state' => 'inactive'
		),
		'blogcom-100' => array(
			'title' => '100 komentāri tavā blogā',
			'state' => 'inactive'
		),
		'game-pages-1' => array(
			'title' => 'Uzrakstīja vienas spēles apskatu',
			'state' => 'inactive'
		),
		'game-pages-5' => array(
			'title' => 'Uzrakstīja 5 spēļu apskatus',
			'state' => 'inactive'
		),
		'rs-pages-1' => array(
			'title' => 'Uzrakstīja 1 rakstu <a href="http://exs.lv/runescape" title="RuneScape">RS</a> sadaļā',
			'state' => 'inactive'
		),
		'rs-pages-5' => array(
			'title' => 'Uzrakstīja 5 rakstus <a href="http://exs.lv/runescape" title="RuneScape">RS</a> sadaļā',
			'state' => 'inactive'
		),
		'film-pages-1' => array(
			'title' => 'Uzrakstīja vienu filmas apskatu',
			'state' => 'inactive'
		),
		'film-pages-5' => array(
			'title' => 'Uzrakstīja 5 filmu apskatus',
			'state' => 'inactive'
		),
		'music-pages-1' => array(
			'title' => 'Raksts <a href="http://exs.lv/muzika">mūzikas</a> sadaļā',
			'state' => 'inactive'
		),
		'music-pages-5' => array(
			'title' => '5 raksti <a href="http://exs.lv/muzika">mūzikas</a> sadaļā',
			'state' => 'inactive'
		),
		'news-1' => array(
			'title' => 'Uzrakstīja vienu jaunumu rakstu',
			'state' => 'inactive'
		),
		'news-5' => array(
			'title' => 'Uzrakstīja 5 jaunumu rakstus',
			'state' => 'inactive'
		),
		'news-15' => array(
			'title' => 'Uzrakstīja 15 jaunumu rakstus',
			'state' => 'inactive'
		),
		'daily-first' => array(
			'title' => 'Dienas aktīvākais postotājs',
			'state' => 'inactive'
		),
		'daily-first-5' => array(
			'title' => 'Dienas aktīvākais 5 reizes',
			'state' => 'inactive'
		),
		'miniblog-10' => array(
			'title' => '10 ieraksti miniblogā',
			'state' => 'inactive'
		),
		'miniblog-100' => array(
			'title' => '100 ieraksti miniblogā',
			'state' => 'inactive'
		),
		'miniblog-1000' => array(
			'title' => '1000 ieraksti miniblogā',
			'state' => 'inactive'
		),
		'miniblog-10000' => array(
			'title' => '10000 ieraksti miniblogā',
			'state' => 'inactive'
		),
		'miniblog-r-100' => array(
			'title' => 'Izveidoja MB ar 100 atbildēm',
			'state' => 'inactive'
		),
		'best-pages' => array(
			'title' => 'Augsti novērtēti autora raksti',
			'state' => 'inactive'
		),
		'exs-cup' => array(
			'title' => 'Uzvarēja rakstu konkursā',
			'state' => 'inactive'
		),
		'desas' => array(
			'title' => 'Uzvarēja 25 <a href="http://exs.lv/desas">desu</a> partijas',
			'state' => 'inactive'
		),
		'mc-exs' => array(
			'title' => '<a href="http://exs.lv/mc-award">mc.exs.lv spēlētājs</a>',
			'state' => 'inactive'
		),
		'mta-user' => array(
			'title' => '<a href="http://rp.exs.lv/">rp.exs.lv lietotājs</a>',
			'state' => 'inactive'
		),
		'coding-user' => array(
			'title' => '<a href="http://coding.lv/">coding.lv lietotājs</a>',
			'state' => 'inactive'
		),
		'lol-exs-lv' => array(
			'title' => '<a href="http://lol.exs.lv/">lol.exs.lv lietotājs</a>',
			'state' => 'inactive'
		),
		'mobile' => array(
			'title' => 'Apmeklēja m.exs.lv',
			'state' => 'inactive'
		),
		'draugiem-follow' => array(
			'title' => 'Draugiem.lv sekotājs',
			'state' => 'inactive'
		),
		'facebook-like' => array(
			'title' => 'Facebook.com <a href="https://www.facebook.com/exs.lv">like</a>',
			'state' => 'inactive'
		),
		'blogs-50' => array(
			'title' => 'Veica 50 bloga ierakstus',
			'state' => 'inactive'
		),
		'polls-50' => array(
			'title' => 'Atbildēja 50 aptaujās',
			'state' => 'inactive'
		),
		'karma-20' => array(
			'title' => 'Karmas zaķis (20)',
			'state' => 'inactive'
		),
		'karma-100' => array(
			'title' => 'Karmena (100)',
			'state' => 'inactive'
		),
		'karma-500' => array(
			'title' => 'Karmas iemiesojums (500)',
			'state' => 'inactive'
		),
		'karma-1000' => array(
			'title' => 'Karma Whore (1000)',
			'state' => 'inactive'
		),
		'karma-2000' => array(
			'title' => 'How about a nice cup of karma? (2000)',
			'state' => 'inactive'
		),
		'karma-5000' => array(
			'title' => 'Alus no Maadinsh (Karma 5000)',
			'state' => 'inactive'
		),
		'online-7days' => array(
			'title' => '7 dienas online',
			'state' => 'inactive'
		),
		'online-30days' => array(
			'title' => '30 dienas online',
			'state' => 'inactive'
		),
		'online-100days' => array(
			'title' => '100 dienas online',
			'state' => 'inactive'
		),
		'thumbs-up-100' => array(
			'title' => 'Atzītais (saņēma 100 plusiņus)',
			'state' => 'inactive'
		),
		'thumbs-up' => array(
			'title' => 'Ievērotais (saņēma 1000 plusiņus)',
			'state' => 'inactive'
		),
		'plus' => array(
			'title' => '10 plusi vienam komentāram',
			'state' => 'inactive'
		),
		'mentioned' => array(
			'title' => '@pieminēts 10 reizes',
			'state' => 'inactive'
		),
		'positive' => array(
			'title' => 'Pozitīvais (vērtēja citus +100)',
			'state' => 'inactive'
		),
		'active-poster' => array(
			'title' => 'Aktīvais postotājs (5 posti dienā)',
			'state' => 'inactive'
		),
		'savejais' => array(
			'title' => 'Savējais (aktīvs 1000 dienas)',
			'state' => 'inactive'
		),
		'hangman' => array(
			'title' => '<a href="http://exs.lv/karatavas">Karātavu</a> dienas uzvarētājs',
			'state' => 'inactive'
		)
	);
}

function get_awards($user) {
	global $db;
	$user = (int) $user;
	$ret = $db->get_results("SELECT `id`,`award`,`title`,`created`,`importance` FROM `autoawards` WHERE `user_id` = $user ORDER BY `importance` DESC", 0);
	if ($ret) {
		return $ret;
	} else {
		return array();
	}
}

function get_awards_list($user) {
	global $db;
	$user = (int) $user;
	$ret = (array) $db->get_col("SELECT `award` FROM `autoawards` WHERE `user_id` = $user ORDER BY `importance` DESC");
	if ($ret) {
		return $ret;
	} else {
		return array();
	}
}

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

function update_awards($user) {

	global $db, $m;
	$user = (int) $user;
	$awards_list = list_awards();
	$existing_awards = get_awards_list($user);

	$userr = get_user($user, true);
	if (!$userr) {
		return false;
	}

	//ja lietotajs nav redzets 6 menesus, nemaz necensamies vinjam updatot medaļas, ienāks - saņems
	if ($userr->lastseen > date('Y-m-d H:i:s', time() - 15778463)) {
		$karma = $userr->karma;
		if ($karma >= 20) {
			$awards_list['karma-20']['state'] = 'active';
		}
		if ($karma >= 100) {
			$awards_list['karma-100']['state'] = 'active';
		}
		if ($karma >= 500) {
			$awards_list['karma-500']['state'] = 'active';
		}
		if ($karma >= 1000) {
			$awards_list['karma-1000']['state'] = 'active';
		}
		if ($karma >= 2000) {
			$awards_list['karma-2000']['state'] = 'active';
		}
		if ($karma >= 5000) {
			$awards_list['karma-5000']['state'] = 'active';
		}

		if (!in_array('draugiem-follow', $existing_awards) && !empty($userr->draugiem_id)) {
			if ($db->get_var("SELECT count(*) FROM `draugiem_followers` WHERE `id` = '$userr->draugiem_id'")) {
				$awards_list['draugiem-follow']['state'] = 'active';
			}
		}

		if ($userr->posts >= 5) {
			$awards_list['first-post']['state'] = 'active';
		}

		if (!empty($userr->avatar) && $userr->avatar != 'none.png') {
			$awards_list['avatar-have']['state'] = 'active';
		}

		if (!in_array('news-15', $existing_awards) && $userr->posts > 5) {
			$news = $db->get_var("SELECT count(*) FROM pages WHERE author = '$user' AND category = '1'");
			if ($news >= 1) {
				$awards_list['news-1']['state'] = 'active';
			}
			if ($news >= 5) {
				$awards_list['news-5']['state'] = 'active';
			}
			if ($news >= 15) {
				$awards_list['news-15']['state'] = 'active';
			}
		}

		if (!in_array('miniblog-10000', $existing_awards) && $userr->posts > 5) {
			$miniblog = $db->get_var("SELECT count(*) FROM `miniblog` WHERE `author` = '$user' AND `removed` = '0'");
			if ($miniblog >= 10) {
				$awards_list['miniblog-10']['state'] = 'active';
			}
			if ($miniblog >= 100) {
				$awards_list['miniblog-100']['state'] = 'active';
			}
			if ($miniblog >= 1000) {
				$awards_list['miniblog-1000']['state'] = 'active';
			}
			if ($miniblog >= 10000) {
				$awards_list['miniblog-10000']['state'] = 'active';
			}
		}

		if (!in_array('miniblog-r-100', $existing_awards) && $userr->posts > 5) {
			if ($db->get_var("SELECT count(*) FROM `miniblog` WHERE `author` = '$user' AND `removed` = '0' AND `posts` >= 100")) {
				$awards_list['miniblog-r-100']['state'] = 'active';
			}
		}

		if (!in_array('mc-exs', $existing_awards)) {
			if ($db->get_var("SELECT count(*) FROM `mc_users` WHERE `id` = '$user'")) {
				$awards_list['mc-exs']['state'] = 'active';
			}
		}

		if (!in_array('mta-user', $existing_awards)) {
			if ($db->get_var("SELECT count(*) FROM `userlogs` WHERE `user` = '$user' AND `lang` = 5") >= 10) {
				$awards_list['mta-user']['state'] = 'active';
			}
		}

		if (!in_array('lol-exs-lv', $existing_awards)) {
			if ($db->get_var("SELECT count(*) FROM `userlogs` WHERE `user` = '$user' AND `lang` = 7") >= 10) {
				$awards_list['lol-exs-lv']['state'] = 'active';
			}
		}

		if (!in_array('coding-user', $existing_awards)) {
			if ($db->get_var("SELECT count(*) FROM `userlogs` WHERE `user` = '$user' AND `lang` = 3") >= 10) {
				$awards_list['coding-user']['state'] = 'active';
			}
		}

		if (!in_array('group-created', $existing_awards)) {
			$group_created = $db->get_var("SELECT count(*) FROM clans WHERE owner = '$user'");
			if ($group_created) {
				$awards_list['group-created']['state'] = 'active';
			}
		} else {
			$group_created = 1;
		}

		if (!in_array('ingroup-5', $existing_awards)) {
			$ingroups = $db->get_var("SELECT count(*) FROM clans_members WHERE user = '$user' AND approve = '1'");
			if ($ingroups + $group_created >= 5) {
				$awards_list['ingroup-5']['state'] = 'active';
			}
		}

		if (!in_array('group-100', $existing_awards)) {
			$group_100 = $db->get_var("SELECT count(*) FROM clans WHERE owner = '$user' AND members >= 99");
			if ($group_100) {
				$awards_list['group-100']['state'] = 'active';
			}
		}

		//draugi
		if (!in_array('friends-50', $existing_awards)) {
			$fcount = $db->get_var("SELECT count(*) FROM friends WHERE (friend1 = '$user' OR friend2 = '$user') AND confirmed = 1");
			if ($fcount >= 20) {
				$awards_list['friends-20']['state'] = 'active';
			}
			if ($fcount >= 50) {
				$awards_list['friends-50']['state'] = 'active';
			}
		}

		if (!in_array('gallery', $existing_awards) && $userr->posts > 0) {
			$gallery = $db->get_var("SELECT count(*) FROM images WHERE uid = '$user'");
			if ($gallery) {
				$awards_list['gallery']['state'] = 'active';
			}
		}

		if (!in_array('popular', $existing_awards) && $userr->posts > 1) {
			$views = $db->get_var("SELECT count(*) FROM `viewprofile` WHERE `profile` = '$user'");
			if ($views >= 100) {
				$awards_list['popular']['state'] = 'active';
			}
		}

		if (!in_array('messages-100', $existing_awards)) {
			$messages_100 = $db->get_var("SELECT count(*) FROM `pm` WHERE from_uid = '$user'");
			if ($messages_100 >= 100) {
				$awards_list['messages-100']['state'] = 'active';
			}
		}

		if (!in_array('mentioned', $existing_awards)) {
			if ($db->get_var("SELECT count(*) FROM `notify` WHERE `user_id` = '$user' AND `type` IN(13,14,15,16)") >= 10) {
				$awards_list['mentioned']['state'] = 'active';
			}
		}

		if (!in_array('topics-20', $existing_awards) && $userr->posts > 5) {
			$topics_20 = $db->get_var("SELECT count(*) FROM `pages` WHERE author = '$user'");
			if ($topics_20 >= 20) {
				$awards_list['topics-20']['state'] = 'active';
			}
		}

		if (!in_array('game-pages-5', $existing_awards) && $userr->posts > 5) {
			$game_pages = $db->get_var("SELECT count(*) FROM pages WHERE author = '$user' AND category = '81'");
			if ($game_pages >= 1) {
				$awards_list['game-pages-1']['state'] = 'active';
			}
			if ($game_pages >= 5) {
				$awards_list['game-pages-5']['state'] = 'active';
			}
		}

		if (!in_array('music-pages-5', $existing_awards) && $userr->posts > 5) {
			$music_pages = $db->get_var("SELECT count(*) FROM pages WHERE author = '$user' AND category = '323'");
			if ($music_pages >= 1) {
				$awards_list['music-pages-1']['state'] = 'active';
			}
			if ($music_pages >= 5) {
				$awards_list['music-pages-5']['state'] = 'active';
			}
		}

		if (!in_array('film-pages-5', $existing_awards) && $userr->posts > 5) {
			$film_pages = $db->get_var("SELECT count(*) FROM pages WHERE author = '$user' AND category = '80'");
			if ($film_pages >= 1) {
				$awards_list['film-pages-1']['state'] = 'active';
			}
			if ($film_pages >= 5) {
				$awards_list['film-pages-5']['state'] = 'active';
			}
		}

		if (!in_array('rs-pages-5', $existing_awards) && $userr->posts > 5) {
			$rs_pages = $db->get_var("SELECT count(*) FROM `pages` WHERE `author` = '$user' AND `category` IN(599,4,5,99,100,102,160,193,195,194,792,787,788,789,790,791,793)");
			if ($rs_pages >= 1) {
				$awards_list['rs-pages-1']['state'] = 'active';
			}
			if ($rs_pages >= 5) {
				$awards_list['rs-pages-5']['state'] = 'active';
			}
		}

		if (!in_array('thumbs-up', $existing_awards) && $userr->karma > 10) {
			$pcom = $db->get_var("SELECT SUM(`vote_value`) FROM `comments` WHERE `author` = '$user'");
			$gcom = $db->get_var("SELECT SUM(`vote_value`) FROM `galcom` WHERE `author` = '$user'");
			$mbvt = $db->get_var("SELECT SUM(`vote_value`) FROM `miniblog` WHERE `author` = '$user'");
			if (($pcom + $gcom + $mbvt) > 99) {
				$awards_list['thumbs-up-100']['state'] = 'active';
			}
			if (($pcom + $gcom + $mbvt) > 999) {
				$awards_list['thumbs-up']['state'] = 'active';
			}
		}

		if (!in_array('plus', $existing_awards) && $userr->posts > 1) {
			$pcom = $db->get_var("SELECT `id` FROM `comments` WHERE `author` = '$user' AND `vote_value` >= 10 LIMIT 1");
			$gcom = $db->get_var("SELECT `id` FROM `galcom` WHERE `author` = '$user' AND `vote_value` >= 10 LIMIT 1");
			$mbvt = $db->get_var("SELECT `id` FROM `miniblog` WHERE `author` = '$user' AND `vote_value` >= 10 LIMIT 1");
			if ($pcom || $gcom || $mbvt) {
				$awards_list['plus']['state'] = 'active';
			}
		}

		if ($userr->days_in_row >= 7 || ($userr->days_in_row >= 6 && $userr->seen_today == 1)) {
			$awards_list['online-7days']['state'] = 'active';

			if ($userr->days_in_row >= 30 || ($userr->days_in_row >= 29 && $userr->seen_today == 1)) {
				$awards_list['online-30days']['state'] = 'active';

				if ($userr->days_in_row >= 100 || ($userr->days_in_row >= 99 && $userr->seen_today == 1)) {
					$awards_list['online-100days']['state'] = 'active';
				}
			}
		}

		if ($userr->vote_others > 99) {
			$awards_list['positive']['state'] = 'active';
		}

		if ($userr->mobile_seen == 1) {
			$awards_list['mobile']['state'] = 'active';
		}

		if ($userr->year_first == 1) {
			$awards_list['year-first']['state'] = 'active';
			$awards_list['year-first']['title'] = 'Iepostoja gada 1. minūtē';
		}

		if (!in_array('best-pages', $existing_awards) && $userr->karma > 10) {
			//augsti novērtēti raksti
			$ratings = $db->get_var("SELECT count(*) FROM `pages` WHERE rating_count > 15 AND (rating/rating_count) > 4 AND author = '$user'");
			if ($ratings >= 3) {
				$awards_list['best-pages']['state'] = 'active';
			}
		}

		//blog
		if ($blog_have = get_blog_by_user($user)) {
			$awards_list['blog-have']['state'] = 'active';
			$blogs = $db->get_var("SELECT count(*) FROM `pages` WHERE `category` = '$blog_have'");
			if ($blogs >= 50) {
				$awards_list['blogs-50']['state'] = 'active';
			}
			//blogcom-100
			$blogcom = $db->get_var("SELECT SUM(`posts`) FROM `pages` WHERE `author` = '$user' AND `category` = '$blog_have'");
			if ($blogcom > 99) {
				$awards_list['blogcom-100']['state'] = 'active';
			}
		}

		if (!in_array('active-poster', $existing_awards)) {
			$days = ceil((time() - strtotime($userr->date)) / 60 / 60 / 24);
			$posts = $userr->posts / $days;
			if ($days >= 30 && $posts >= 5) {
				$awards_list['active-poster']['state'] = 'active';
			}
		}

		//50 aptaujas
		if (!in_array('polls-50', $existing_awards)) {
			if ($db->get_var("SELECT count(*) FROM responses WHERE user_id = '$user'") >= 50) {
				$awards_list['polls-50']['state'] = 'active';
			}
		}

		//dienas spameris
		if (!in_array('daily-first-5', $existing_awards)) {
			$first = $db->get_var("SELECT `daily_first` FROM `users` WHERE `id` = '$user'");
			if ($first >= 1) {
				$awards_list['daily-first']['state'] = 'active';
			}
			if ($first >= 5) {
				$awards_list['daily-first-5']['state'] = 'active';
			}
		}

		//dienas hangman
		if (!in_array('hangman', $existing_awards)) {
			if ($db->get_var("SELECT count(*) FROM users WHERE id = '$user' AND daily_hangman > 0")) {
				$awards_list['hangman']['state'] = 'active';
			}
		}

		//desas
		if (!in_array('desas', $existing_awards)) {
			if ($db->get_var("SELECT count(*) FROM desas WHERE (user_1 = '$user' AND winner = '1') OR (user_2 = '$user' AND winner = '2')") >= 25) {
				$awards_list['desas']['state'] = 'active';
			}
		}

		//savējais
		if (!in_array('savejais', $existing_awards)) {
			if ($db->get_var("SELECT count(*) FROM users WHERE id = '$user' AND karma > 99 AND DATEDIFF(lastseen,date) > 999 AND lastseen != '0000-00-00 00:00:00'")) {
				$awards_list['savejais']['state'] = 'active';
			}
		}

		$user_age = user_age($userr->date);
		for ($i = 1; $i < 8; $i++) {
			if ($user_age >= $i) {
				$awards_list['cake-' . $i] = array(
					'title' => $i . ' ' . lv_dsk($i, 'gads', 'gadi') . ' exā ;)',
					'state' => 'active'
				);
			}
		}
	}

	//custom medalas. atkomentēt pēc vajadzības un updatot tiem lietotajiem. ja masīvā nav izmaiņu, nav ko lieki trenkāt procesoru :crazy:
	/*
	  if(in_array($user,array(2,140,325,543,1822,2324,2339,3650,4711,6001,8531,8872,9048,9247,12605,14911,16267,21600))) {
	  $awards_list['exs-cup']['state'] = 'active';
	  } */


	/* gada balva */
	/*if (in_array($user, array(16433))) {
		$awards_list['gada-balva-2012'] = array(
			'title' => 'Exs gada balva 2012',
			'state' => 'active'
		);
	}*/

	if (in_array($user, array(
24587,
25778,
11807,
18773,
10734,
13004,
10065,
1822,
1473,
2037,
273,
858,
14836,
4845,
23678,
15960,
11722,
6001,
5356,
7825,
4226,
16817,
6671,
21450,
1621,
12108,
8516,
15390,
23512,
23282,
21704,
703,
655,
1003,
20858,
11525,
5965,
1385,
22051,
8601,
10433,
25254,
24706,
25709,
1220,
16433,
21230,
1,
16261,
27098,
27089,
1306,
3217,
13388
			))) {
		$awards_list['futzals-06-04-2013'] = array(
			'title' => 'Exs futzāla turnīrs 06.04.2013',
			'state' => 'active'
		);
	}


	/* 	if(in_array($user,array(13004))) {
	  $awards_list['db-1'] = array(
	  'title' => 'aktīva dalība datubāzes tulkošanā',
	  'state' => 'active'
	  );
	  } */

	/* CS
	  if(in_array($user,array(13004,8707,3906,4088,16395,10880,1322,23622,5547,17341,24437,
	  24049,
	  17532,
	  24853,
	  22518,
	  1611,
	  10081,
	  23107,
	  1621,
	  1280))) {
	  $awards_list['counter-strike'] = array(
	  'title' => '<a href="/group/150" title="Counter Strike">CS</a> mēneša top 15',
	  'state' => 'active'
	  );
	  } */


	/* if(in_array($user,array(19162,15394,19308,22469,5356,1306,18558,21649,10869,19203,18557,7808,4137,18702,3605,13004))) {
	  $awards_list['nhl-stars'] = array(
	  'title' => 'Zvaigžņu spēles NHL turnīrā',
	  'state' => 'active'
	  );
	  } */

	foreach ($awards_list as $key => $val) {
		if ($val['state'] === 'active') {
			//ja lietotājam jau ir šāds awards, neko nedaram
			if (!in_array($key, $existing_awards)) {
				$db->query("INSERT INTO autoawards (user_id,award,title,created) VALUES ('$user','$key','" . $val['title'] . "',NOW())");
				$db->update('autoawards', $db->insert_id, array('importance' => $db->insert_id));
				userlog($user, 'Ieguva medaļu &quot;' . $val['title'] . '&quot;', '/dati/bildes/awards/' . $key . '.png');
				notify($user, 7);
				$m->delete('aw_' . $user);
			}
		}
	}
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

function get_user($user_id, $force = false) {
	global $db, $m, $users_cache, $debug;
	$user_id = (int) $user_id;
	if (!$force && !empty($users_cache[$user_id])) {
		return $users_cache[$user_id];
	}
	if ($debug || $force === true || ($data = $m->get('u_' . $user_id)) === false) {
		$data = $db->get_row("SELECT 
		`lastseen`,`mail`,`gender`,`persona`,`maximg`,`yt_name`,`twitter`,`show_code`,`show_lol`,
		`show_rp`,`vote_today`,`rte`,`showsig`,`id`,`nick`,`level`,`skin`,`posts`,
		`karma`,`custom_title`,`signature`,`avatar`,`av_alt`,`vote_others`,`warn_count`,
		`date`,`mobile_seen`,`decos`,`draugiem_id`,`days_in_row`,`seen_today`,
		`token`,`year_first`,`rating` FROM `users` WHERE `id` = '$user_id'");
		$m->set('u_' . $user_id, $data, false, 3600);
	}
	$users_cache[$user_id] = $data;
	return $data;
}

function get_latest_groups($force = false) {
	global $db, $m, $lang;
	if ($force || !($data = $m->get('l_groups_' . $lang))) {
		$data = $db->get_results("SELECT `id`,`title` FROM `clans` WHERE `list` = 1 AND `lang` = '$lang' ORDER BY `id` DESC LIMIT 5");
		$m->set('l_groups_' . $lang, $data, false, 3600);
	}
	return $data;
}

function get_youtube($videoid, $force = false) {
	global $db, $m;
	if ($force || !($data = $m->get('yt_' . $videoid))) {
		$data = $db->get_row("SELECT * FROM `ytlocal` WHERE `yt_id` = '" . sanitize($videoid) . "'");
		if (!empty($data)) {
			$m->set('yt_' . $videoid, $data, false, 3600);
		}
	}
	return $data;
}

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
	if ($force || !($busers = $m->get('banlist_'.$lang))) {
		$data = $db->get_results("SELECT `user_id` FROM `banned` WHERE `user_id` != 0 AND `time`+`length` > " . time() . " AND (`lang` = 0 OR `lang` = '$lang') ORDER BY `time` DESC");
		$busers = array();
		if ($data) {
			foreach ($data as $banned) {
				$busers[$banned->user_id] = $banned->user_id;
			}
		}
		$m->set('banlist_'.$lang, $busers, false, 100);
	}
	return $busers;
}

function get_blog_latest($category_id, $force = false) {
	global $auth, $db, $m;
	if ($force || !($html = $m->get('blog_latest_' . $category_id . '_' . $auth->ok))) {
		if ($bloglatest = $db->get_results("SELECT strid,title,posts FROM pages WHERE category = '" . $category_id . "' ORDER BY bump DESC LIMIT 5")) {
			$html = '<h3>Jaunākais blogā</h3><div class="box"><p>';
			foreach ($bloglatest as $bloglate) {
				$html .= '<a href="/read/' . $bloglate->strid . '">' . $bloglate->title . '&nbsp;[' . $bloglate->posts . ']</a><br />';
			}
			$html .= '</p></div>';
		} else {
			$html = '';
		}
		if ($auth->ok === true) {
			if ($sidelinks = $db->get_results("SELECT title,url FROM sidelinks WHERE category = '" . $category_id . "' ORDER BY id DESC")) {
				$html .= '<h3>Manas saites</h3><div class="box"><p>';
				foreach ($sidelinks as $sidelink) {
					$html .= '<a href="' . $sidelink->url . '" rel="nofollow">' . $sidelink->title . '</a><br />';
				}
				$html .= '</p></div>';
			}
		}
		$m->set('blog_latest_' . $category_id . '_' . $auth->ok, $html, false, 100);
	}
	return $html;
}

function get_footer_mb($force = false) {
	global $db, $m, $lang;
	if ($force || !($html = $m->get('footer_mb_' . $lang))) {
		$html = '';
		$latest = $db->get_results("SELECT `text`,`id`,`author` FROM `miniblog` WHERE `groupid` = 0 AND `parent` = 0 AND `removed` = 0 AND `lang` = '$lang' ORDER BY `id` DESC LIMIT 6");
		if ($latest) {
			$html .= '<ul class="internal-links">';
			foreach ($latest as $late) {
				$late->text = preg_replace("#(^|[\n ]|<a(.*?)>)http://(www\.)?youtube\.com/watch\?v=([a-zA-Z0-9\-_]+)((.*?)</a>)?#ime", 'get_youtube_title("\\4") ', strip_tags(str_replace(array('<br/>', '<br>', '<br />', '<p>', '</p>', '&nbsp;', "\n", "\r"), ' ', $late->text)));
				$late->text = preg_replace("#(^|[\n ]|<a(.*?)>)http://(www\.)?youtu\.be/([a-zA-Z0-9\-_]+)((.*?)</a>)?#ime", 'get_youtube_title("\\4")', $late->text);
				$url_title = mkslug(textlimit($late->text, 36, ''));
				$html .= '<li><a href="/say/' . $late->author . '/' . $late->id . '-' . $url_title . '">' . textlimit($late->text, 36, '') . '</a></li>';
			}
			$html .= '</ul>';
		}
		$m->set('footer_mb_' . $lang, $html, false, 30);
	}
	return $html;
}

function get_footer_topics($force = false) {
	global $db, $m, $lang;
	if ($force || !($html = $m->get('f_topics_' . $lang))) {
		$html = '';
		if ($lang == 1) {
			$latest = $db->get_results("SELECT `lang`,`title`,`strid` FROM `pages` WHERE `category` != '83' AND `category` != '6' AND (`lang` = '1' OR `lang` = '3') ORDER BY `id` DESC LIMIT 6");
		} else {
			$latest = $db->get_results("SELECT `lang`,`title`,`strid` FROM `pages` WHERE `category` != '83' AND `category` != '6' AND `lang` = '$lang' ORDER BY `id` DESC LIMIT 6");
		}
		if ($latest) {
			$html .= '<ul class="internal-links">';
			foreach ($latest as $late) {

				$domain = '';
				$prefix = '';
				if ($late->lang == 3 && $lang != $late->lang) {
					$domain = 'http://coding.lv';
					if ($lang == 1) {
						$prefix = '<span class="lp-prefix">code</span> ';
					}
				}
				if ($late->lang == 5 && $lang != $late->lang) {
					$domain = 'http://rp.exs.lv';
					if ($lang == 1) {
						$prefix = '<span class="lp-prefix">mta</span> ';
					}
				}
				if ($late->lang == 7 && $lang != $late->lang) {
					$domain = 'http://lol.exs.lv';
					if ($lang == 1) {
						$prefix = '<span class="lp-prefix">lol</span> ';
					}
				}
				if ($late->lang == 6 && $lang != $late->lang) {
					$domain = 'http://lfs.lv';
					if ($lang == 1) {
						$prefix = '<span class="lp-prefix">lfs</span> ';
					}
				}
				$url = $domain . '/read/' . $late->strid;

				$html .= '<li><a href="' . $url . '" title="' . htmlspecialchars($late->title) . '">' . $prefix . textlimit($late->title, 36) . '</a></li>';
			}
			$html .= '</ul>';
		}
		$m->set('f_topics_' . $lang, $html, false, 60);
	}
	return $html;
}

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

function get_blog_by_user($user_id, $force = false) {
	global $db, $m;
	if ($force || !($data = $m->get('isb_' . $user_id))) {
		$data = $db->get_var("SELECT `id` FROM `cat` WHERE `isblog` = '$user_id' LIMIT 1");
		if (!$data) {
			$data = 'no';
		}
		$m->set('isb_' . $user_id, $data, false, 7200);
	}
	if ($data > 0 && $data != 'no') {
		return $data;
	} else {
		return false;
	}
}

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
		if($ajax) {
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
				$class = ' class="'.$ajax_class.'selected"';
			} elseif($ajax) {
				$class = ' class="'.$ajax_class.'"';
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
	$text = str_replace('......', '...', $text);
	$text = str_replace('.....', '...', $text);
	$text = str_replace('....', '...', $text);
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
	require_once(LIB_PATH . '/htmlpurifier/library/HTMLPurifier.auto.php');
	$config = HTMLPurifier_Config::createDefault();
	$config->set('Cache.SerializerPath', CORE_PATH . '/cache/htmlpurifier');
	$config->set('AutoFormat.Linkify', true);
	$config->set('AutoFormat.AutoParagraph', true);
	$config->set('AutoFormat.RemoveSpansWithoutAttributes', true);
	$config->set('AutoFormat.RemoveEmpty', true);
	$purifier = new HTMLPurifier($config);
	$text = $purifier->purify($text);
	$text = str_replace('href="http://' . $_SERVER['SERVER_NAME'] . '/', 'href="/', $text);
	$text = str_replace(' rel="nofollow"', '', $text);
	$text = str_replace(' href="http://', ' rel="nofollow" href="http://', $text);
	$text = str_replace(' href="https://', ' rel="nofollow" href="https://', $text);
	$text = str_replace(' href="ftp://', ' rel="nofollow" href="ftp://', $text);
	$text = str_replace(' dateks.lv ', ' <a href="http://www.dateks.lv/ref/view.html">dateks.lv</a> ', $text);
	$text = str_replace(' dateks ', ' <a href="http://www.dateks.lv/ref/view.html">Dateks</a> ', $text);
	$text = str_replace(' dateksā ', ' <a href="http://www.dateks.lv/ref/view.html">dateksā</a> ', $text);
	$text = str_replace(' dateksaa ', ' <a href="http://www.dateks.lv/ref/view.html">dateksā</a> ', $text);
	$text = str_replace('dateks.lv/cenas', 'dateks.lv/p/view/cenas', $text);
	$text = str_replace('<code>', '<code class="prettyprint">', $text);
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
	return sanitize($text);
}

function input2db($text, $len = 30) {
	$text = filterb4db($text);
	$text = substr(htmlspecialchars(trim($text)), 0, $len);
	return sanitize($text);
}

function email2db($email) {
	return sanitize(filter_var($email, FILTER_SANITIZE_EMAIL));
}

//redirect back to miniblog
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
function mb_recursive($data, $key = 0, $level = 0, $intro = 0, $answer_limit = 3, $closed = 0) {
	global $auth, $min_post_edit;
	$out = '<ul class="responses-' . $key . ' level-' . $level . '">';
	if (!empty($data[$key])) {
		$level++;
		foreach ($data[$key] as $val) {
			$out .= '<li>';
			$avatar = get_avatar($val, 's');
			$val->date = strtotime($val->date);
			if (!$auth->mobile) {
				$out .= '<div class="mb-av"><a id="m' . $val->id . '" href="/user/' . $val->author . '">';
				$out .= '<img width="45" height="45" src="' . $avatar . '" alt="' . htmlspecialchars($val->nick) . '" /></a>';
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
				$out .= '<a class="mb-av" id="m' . $val->id . '" href="/user/' . $val->author . '"><img class="av" width="40" height="40" src="' . $avatar . '" alt="" /></a>';
			}
			$out .= '<div class="response-content">';
			if (!$intro && $auth->ok === true && $level < $answer_limit) {
				$out .= '<a href="' . $val->id . '" class="mb-reply-to mb-icon">Atbildēt</a>';
			}
			if (!$auth->mobile) {
				$out .= '<div class="mb-rater">' . mb_rater($val) . '</div>';
			}
			$out .= '<p class="post-info"><a href="/user/' . $val->author . '">' . usercolor($val->nick, $val->level, false, $val->author) . '</a> <span class="comment-date-time" title="' . date('d.m.Y. H:i', $val->date) . '">' . display_time_simple($val->date) . '</span>';
			if (!$auth->mobile && !$intro) {
				$out .= ' <a href="#m' . $val->id . '" class="comment-permalink">#</a>';
			}
			if (!$auth->mobile && !$intro && $auth->ok === true && ((!$closed && $auth->id == $val->author && $auth->level == 3) || im_mod()) && $val->date > time() - 600) {
				$out .= ' [<a href="/delete/' . $val->id . '" class="confirm r">dzēst</a>]';
			}
			if (
					!$auth->mobile && !$intro && ($val->date > time() - 1800 || $auth->level == 1) &&
					(im_mod() || (!$closed && $auth->karma >= $min_post_edit && $val->author == $auth->id))) {
				$out .= ' [<a href="/edit/' . $val->id . '">labot</a>]';
			}
			$out .= '</p><div class="post-content">' . add_smile($val->text) . '</div>';
			if ($auth->ok === true || $val->posts) {
				$out .= mb_recursive($data, $val->id, $level, $intro, $answer_limit);
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

//rekursivi atrod minibloga atbildes limeni, pec id
function get_mb_level($mbid, $level = 0) {
	global $db;
	$mb = $db->get_var("SELECT `reply_to` FROM `miniblog` WHERE `id` = '" . intval($mbid) . "'");
	if ($mb > 0 && $level < 30) {
		$level++;
		return get_mb_level($mb, $level);
	}
	return $level;
}

function lv_dsk($num = 0, $single = 'atbilde', $multi = 'atbildes') {
	if ($num == 1 || (substr($num, -1) == '1' && substr($num, -2) != '11')) {
		return $single;
	} else {
		return $multi;
	}
}

function get_page_categories($current = null, $force = false) {
	global $db, $m, $lang, $debug;

	if ($debug || $force || !($cats = $m->get('cat_list_' . $lang))) {
		$cats = $db->get_results("SELECT `persona`,`isblog`,`isforum`,`id`,`title` FROM `cat` WHERE `module` IN('list','index','rshelp','movies') AND `lang` = '$lang' ORDER BY `title` ASC");
		$m->set('cat_list_' . $lang, $cats, false, 900);
	}

	$return = array();
	foreach ($cats as $cat) {
		if (im_mod() || im_cat_mod($cat->id) || $cat->id == $current || $current == 'all') {
			if ($cat->isforum) {
				$return['Forums'][$cat->id] = $cat->title . ' forums';
			} elseif ($cat->isblog) {
				$return['Blogi'][$cat->id] = $cat->title;
			} elseif ($cat->persona == 'runescape.jpg') {
				$return['Runescape'][$cat->id] = $cat->title;
			} else {
				$return['Main'][$cat->id] = $cat->title;
			}
		}
	}
	return $return;
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
	global $auth, $db, $lang, $comments_per_page;
	$out = '';

	$skip = 0;
	if (isset($_GET['pg'])) {
		$skip = 8 * intval($_GET['pg']);
	}

	$conditions = array(
		"`category` != '6'"
	);

	if ($lang == 1) {
		$add_langs = array("`pages`.`lang` = '1'");
		if(!empty($auth->show_code)) {
			$add_langs[] = "`pages`.`lang` = '3'";
		}
		if(!empty($auth->show_rp)) {
			$add_langs[] = "`pages`.`lang` = '5'";
		}
		if(!empty($auth->show_lol)) {
			$add_langs[] = "`pages`.`lang` = '7'";
		}
		$conditions[] = '(' . implode(' OR ', $add_langs) . ')';
		
	} else {
		$conditions[] = "`pages`.`lang` = '$lang'";
	}


	if (!im_mod()) {
		$conditions[] = "`category` != 83";
		$conditions[] = "`category` != 954";
	}

	if ($auth->ok) {
		$ignores = $db->get_col("SELECT `category_id` FROM `cat_ignore` WHERE `user_id` = '$auth->id'");
		if (!empty($ignores)) {
			foreach ($ignores as $ignore) {
				$conditions[] = "`category` != $ignore";
			}
		}
	}

	$latest = $db->get_results("SELECT `title`,`id`,`posts`,`readby`,`strid`,`category`,`lang`,`bump` FROM `pages` WHERE " . implode(' AND ', $conditions) . " ORDER BY `bump` DESC LIMIT $skip,8");

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
			if ($late->lang == 1) {
				$domain = 'http://exs.lv';
			} elseif ($late->lang == 3) {
				$domain = 'http://coding.lv';
				if ($lang == 1) {
					$prefix = '<span class="lp-prefix">code</span> ';
				}
			} elseif ($late->lang == 5) {
				$domain = 'http://rp.exs.lv';
				if ($lang == 1) {
					$prefix = '<span class="lp-prefix">mta</span> ';
				}
			} elseif ($late->lang == 7) {
				$domain = 'http://lol.exs.lv';
				if ($lang == 1) {
					$prefix = '<span class="lp-prefix">lol</span> ';
				}
			} elseif ($late->lang == 6) {
				$domain = 'http://lfs.lv';
				if ($lang == 1) {
					$prefix = '<span class="lp-prefix">lfs</span> ';
				}
			}
			$add = '';
			if ($auth->ok && $lang != $late->lang) {
				$add = $auth->transfer;
			}
			$url = $domain . '/read/' . $late->strid;
			if ($late->category == 83 || $late->category == 954) {
				$late->title = '<em>' . $late->title . '</em>';
			}
			if ($lang == 1) {
				$out .= '<li><a href="' . $url . $skip . $add . '">';

				if (!empty($late->readby) && in_array($auth->id, unserialize($late->readby))) {
					$out .= $prefix . $late->title . '&nbsp;[' . $late->posts . ']<span>pirms ' . time_ago(strtotime($late->bump)) . '</span></a></li>';
				} else {
					$out .= $prefix . $late->title . '&nbsp;[<span class="r">' . $late->posts . '</span>]<span>pirms ' . time_ago(strtotime($late->bump)) . '</span></a></li>';
				}
			} else {
				$out .= '<li><a href="' . $url . $skip . $add . '"><img src="http://exs.lv/dati/bildes/topic-av/' . $late->id . '.jpg" class="av" alt="" />';

				$out .= '<span>pirms ' . time_ago(strtotime($late->bump)) . '</span>';

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
	global $db, $lang;
	$out = '';
	$actions = $db->get_results("SELECT
		`userlogs`.`action`,
		`userlogs`.`time`,
		`userlogs`.`avatar`,
		`userlogs`.`id`,
		`userlogs`.`user`,
		`users`.`avatar` AS `uavatar`,
		`users`.`av_alt`,
		`users`.`nick`
	FROM
		`userlogs`,
		`users`
	WHERE
		`users`.`id` = `userlogs`.`user` AND
		`userlogs`.`lang` = '$lang'
	ORDER BY
		`userlogs`.`time` DESC
	LIMIT 5");

	if ($actions) {
		$out .= '<ul class="user-actions">';
		foreach ($actions as $action) {
			if (!$action->avatar) {
				if ($action->av_alt) {
					$action->avatar = 'http://img.exs.lv/userpic/small/' . $action->uavatar;
				} elseif ($action->uavatar) {
					$action->avatar = 'http://img.exs.lv/userpic/medium/' . $action->uavatar;
				} else {
					$action->avatar = 'http://img.exs.lv/userpic/small/none.png';
				}
			}
			
			$out .= '<li><img class="av" src="' . $action->avatar . '" alt="" /><div class="event-content"><span>' . $action->nick . ' pirms ' . time_ago($action->time) . '</span><br />' . $action->action . '</div><div class="c"></div></li>';
		}
		$out .= '</ul>';
	}
	return $out;
}

function get_latest_images() {
	global $db, $auth, $lang;

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

	$out = '<p class="imgs">';
	if ($latest) {
		foreach ($latest as $late) {

			//remake_thb($late->url, $late->thb);

			$out .= '<a title="' . htmlspecialchars($late->nick) . '" href="/gallery/' . $late->uid . '/' . $late->id . '"><img src="http://exs.lv/' . $late->thb . '" alt="" />';

			if (!empty($late->readby) && in_array($auth->id, unserialize($late->readby))) {
				$out .= '<span>' . $late->posts . '</span>';
			} else {
				$out .= '<span class="r">' . $late->posts . '</span>';
			}

			$out .= '</a> ';
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
	global $auth, $db, $lang;

	$out = '<ul id="friendssay-list" class="blockhref mb-col">';

	if (isset($_GET['pg'])) {
		$skip = 7 * intval($_GET['pg']);
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
		if(!empty($auth->show_code)) {
			$add_langs[] = "`miniblog`.`lang` = '3'";
		}
		if(!empty($auth->show_rp)) {
			$add_langs[] = "`miniblog`.`lang` = '5'";
		}
		if(!empty($auth->show_lol)) {
			$add_langs[] = "`miniblog`.`lang` = '7'";
		}
		$addlang = '(' . implode(' OR ', $add_langs) . ')';
		
	} else {
		$addlang = "`miniblog`.`lang` = '$lang'";
	}

	$friendsquery = '';
	if($auth->ok && $friends) {
		$myfriends = get_friends($auth->id);
		$myfriends[] = $auth->id;
		$friendsquery = 'AND `miniblog`.`author` IN('.implode(',', $myfriends).')';
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
		`miniblog`.`twitterid` AS `twitterid`,
		`users`.`avatar` AS `avatar`,
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
	ORDER BY
		`miniblog`.`bump`
	DESC LIMIT $skip,7");

	if ($mbs) {
		foreach ($mbs as $mb) {
			$spec = '';

			$avatar = get_avatar($mb, 's');

			$mb->text = mb_get_title($mb->text);
			$add = '';
			if ($auth->ok && $lang != $mb->lang) {
				$add = $auth->transfer;
			}
			if ($lang == 1 && $mb->lang == 3) {
				$spec = ' class="linkcode"';
				$domain = 'http://coding.lv';
			} elseif ($lang == 1 && $mb->lang == 5) {
				$spec = ' class="linkcode"';
				$domain = 'http://rp.exs.lv';
			} elseif ($lang == 1 && $mb->lang == 6) {
				$spec = ' class="linkcode"';
				$domain = 'http://lfs.lv';
			} elseif ($lang == 1 && $mb->lang == 7) {
				$spec = ' class="linkcode"';
				$domain = 'http://lol.exs.lv';
			} else {
				$domain = '';
			}

			if ($mb->groupid != 0) {
				$spec = ' class="group"';
				$group = $db->get_row("SELECT `title`,`avatar` FROM `clans` WHERE `id` = '$mb->groupid'");
				if ($group->avatar) {
					$avatar = 'http://exs.lv/dati/bildes/u_small/' . $group->avatar;
				}
				$url = $domain . '/group/' . $mb->groupid . '/forum/' . base_convert($mb->id, 10, 36);
			} else {
				$url = $domain . '/say/' . $mb->author . '/' . $mb->id . '-' . mb_get_strid($mb->text, $mb->id);
			}

			if (strpos($mb->text, 'spoiler') !== false) {
				$mb->text= preg_replace('/\[spoiler\](.*)\[\/spoiler\]/is', "(spoiler)", $mb->text);
			}

			$mb->text = wordwrap($mb->text, 36, "\n", 1);
			$mb->text = str_replace('/', "/&#8203;", $mb->text);
			$mb->text = str_replace('.', ".&#8203;", $mb->text);
			$mb->text = str_replace('-', "-&#8203;", $mb->text);
			
			if ($mb->groupid != 0) {
				$mb->text = '<em><span>@' . $group->title . '</span></em>' . textlimit($mb->text, 88, '...');
			} elseif ($lang == 1 && $mb->lang == 7) {
				$mb->text = '<em><span>lol.exs.lv</span></em>' . textlimit($mb->text, 88, '...');
			} elseif ($lang == 1 && $mb->lang == 5) {
				$mb->text = '<em><span>rp.exs.lv</span></em>' . textlimit($mb->text, 88, '...');
			} elseif ($lang == 1 && $mb->lang == 6) {
				$mb->text = '<em><span>lfs.lv</span></em>' . textlimit($mb->text, 88, '...');
			} elseif ($lang == 1 && $mb->lang == 3) {
				$mb->text = '<em><span>coding.lv</span></em>' . textlimit($mb->text, 88, '...');
			} else {
				$mb->text = textlimit($mb->text, 98, '...');
			}
			if ($lang == 1) {
				$time = time_ago(strtotime($mb->date));
			} else {
				$time = time_ago($mb->bump);
			}
			$tw = '';
			if (!empty($mb->twitterid)) {
				$tw = '<span style="background: url(\'http://exs.lv/bildes/i.png\') no-repeat 0 -280px;width:16px;height:16px;position:absolute;right:2px;bottom:3px"></span>';
			}

			$out .= '<li' . $spec . '><a href="' . $url . $add . '"><span class="av"><img width="45" height="45" src="' . $avatar . '" alt="' . htmlspecialchars($mb->nick) . '" />' . $tw . '</span><span class="author">' . htmlspecialchars($mb->nick) . '</span> <span>pirms ' . $time . '</span> ' . $mb->text . '&nbsp;[' . $mb->posts . ']</a></li>';
		}
	}
	$out .= '</ul><p class="core-pager ajax-pager">';

	$pager_add = '';
	if($friends) {
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
	if ($auth->ok === true && empty($_SESSION['admin_simulate'])) {
		$db->update('users', $auth->id, array('last_action' => sanitize($action)));
	}
}

function get_itemsdb_action($force = false) {
	global $db, $m;
	$idb_count = '';
	if ($force || ($idb_count = $m->get('itdb_coun')) === false) {
		$queue = $db->get_var("SELECT count(*) FROM `items_db_queue` WHERE `action_by` = '0'");
		$white = $db->get_var("SELECT count(*) FROM `items_db_whitelist` WHERE `action_by` = '0' AND `work_time` != '0000-00-00 00:00:00' AND `user` != '21018'");
		if ($queue > 0 || $white > 0) {
			$idb_count = '&nbsp;(<span class="r">' . ($queue + $white) . '</span>)';
		}
		$m->set('itdb_coun', "$idb_count", false, 25);
	}
	return $idb_count;
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

	//1000000
	/* if ($return == 3000000) {
	  $db->query("INSERT INTO autoawards (user_id,award,title,created) VALUES ('$auth->id','mb-1000000','Miljonā posta autors (3)',NOW())");
	  $db->update('autoawards', $db->insert_id, array('importance' => $db->insert_id));
	  push('Ieguva medaļu &quot;Miljonā posta autors (3)&quot;', '/dati/bildes/awards/mb-1000000.png');
	  } */

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

function get_avatar($user, $size = 'm') {
	global $auth;
	if (empty($auth->mobile)) {
		$path = 'medium';
		if (($user->av_alt || !$user->avatar) && $size == 's') {
			$path = 'small';
		} elseif (($user->av_alt || !$user->avatar) && $size == 'l') {
			$path = 'large';
		}
		if (empty($user->avatar)) {
			$user->avatar = 'none.png';
		}
		return 'http://img.exs.lv/userpic/' . $path . '/' . $user->avatar;
	} else {
		if (empty($user->avatar)) {
			$user->avatar = 'none.png';
		}
		return '/av/' . $user->avatar;
	}
}

function upload_user_avatar($post, $old_filename, $text) {
	$return = $old_filename;

	if (isset($post)) {
		require_once(CORE_PATH . '/includes/class.upload.php');
		$text = 'group_' . time() . '_' . $group->id;
		$foo = new Upload($post);
		$foo->file_new_name_body = $text;
		$foo->image_resize = true;
		$foo->image_convert = 'jpg';
		$foo->image_x = 90;
		$foo->image_y = 90;
		$foo->allowed = array('image/*');
		$foo->image_ratio_crop = true;
		$foo->jpeg_quality = 90;
		$foo->file_auto_rename = false;
		$foo->file_overwrite = true;
		$foo->process('dati/bildes/useravatar/');
		if ($foo->processed) {

			$foo = new Upload($_FILES['edit-avatar']);
			$foo->file_new_name_body = $text;
			$foo->image_resize = true;
			$foo->image_convert = 'jpg';
			$foo->image_x = 45;
			$foo->image_y = 45;
			$foo->allowed = array('image/*');
			$foo->image_ratio_crop = true;
			$foo->jpeg_quality = 90;
			$foo->file_auto_rename = false;
			$foo->file_overwrite = true;
			$foo->process('dati/bildes/u_small/');

			$foo = new Upload($_FILES['edit-avatar']);
			$foo->file_new_name_body = $text;
			$foo->image_resize = true;
			$foo->image_convert = 'jpg';
			$foo->image_x = 170;
			$foo->image_y = 220;
			$foo->allowed = array('image/*');
			$foo->image_ratio_crop = false;
			$foo->image_ratio_no_zoom_in = true;
			$foo->jpeg_quality = 94;
			$foo->file_auto_rename = false;
			$foo->file_overwrite = true;
			$foo->process('dati/bildes/u_large/');

			if (file_exists('dati/bildes/useravatar/' . $text . '.jpg')) {
				$return = $text . '.jpg';
			}
			$foo->clean();
		}
	}
	return $return;
}

function remake_thb($large, $thb) {
	$thb = CORE_PATH . '/' . $thb;
	$large = CORE_PATH . '/' . $large;
	$thb_size = getimagesize($thb);
	if ($thb_size[0] != 56) {
		$c1 = `convert $large -resize '56x56^' -gravity center -crop 56x56+0+0 +repage -strip $thb`;
	}
}

function translate_genres($en) {
	$genres = array(
		'Action' => 'Asa sižeta',
		'Adventure' => 'Piedzīvojumi',
		'Animation' => 'Animācijas',
		'Biography' => 'Biogrāfija',
		'Comedy' => 'Komēdija',
		'Crime' => 'Noziegumu',
		'Drama' => 'Drāma',
		'Documentary' => 'Dokumentāla',
		'Family' => 'Ģimenes',
		'Fantasy' => 'Fantāzija',
		'History' => 'Vēsturiskas',
		'Horror' => 'Šausmu',
		'Music' => 'Muzikāla',
		'Mystery' => 'Mistērija',
		'Reality-TV' => 'Realitātes TV',
		'Romance' => 'Romantika',
		'Sci-Fi' => 'Zinātniskā fantastika',
		'Sport' => 'Sports',
		'Thriller' => 'Trilleris',
		'War' => 'Karš',
		'Western' => 'Vesterns'
	);

	if(!empty($genres[$en])) {
		return $genres[$en];
	}
	return $en;

}

function get_cakeday() {
	global $db, $m;

	$out = array();

	if (($out = $m->get('cday_'.date('Y-m-d'))) === false) {

		$users = $db->get_results("SELECT `id`, `nick` FROM `users` WHERE `date` < '".date('Y')."-01-01 00:00:00' AND `date` LIKE '%-".date('m')."-".date('d')." %' ORDER BY `users`.`lastseen` DESC");
		if(!empty($users)) {
			foreach($users as $user) {
				$out[$user->id] = $user->nick;
			}
		}
		$m->set('cday_'.date('Y-m-d'), $out, false, 3600);
	}

	return $out;
}
