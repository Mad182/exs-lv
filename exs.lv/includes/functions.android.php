<?php
/**
 *  Globālas funkcijas Android lietotnes pieprasījumiem.
 *
 *  Funkciju nosaukumiem izmantots "a_" prefix, lai citos failos tās
 *  varētu atšķirt.
 */

/**
 *  Pievieno kļūdas paziņojumu nākamajai atbildei.
 *
 *  @param string   kļūdas paziņojums
 */
function a_error($string = '') {
	global $json_state, $json_message;
	
	$json_state     = 'error';
	$json_message   = $string;
} 
 
/**
 *  Atgriež JSON sarakstu ar jaunākajiem exs.lv rakstiem.
 *
 *  Atbalsta pārvietošanos pa lapām un apakšprojektus.
 */
function a_get_news() {
	global $auth, $db, $lang, $android_lang;
	
	// vienā lappusē redzamo rakstu skaits;
	// lappušu saraksta lietotnē nav, tā vietā nākamās lapas ieraksti 
	// pievienojas aiz iepriekšējiem    
	$news_in_page = 20;
	
	// nosaka, cik rakstus SQL pieprasījumā izlaist
	if (isset($_GET['page'])) {
		$skip = $news_in_page * intval($_GET['page']);
	} else {
		$skip = 0;
	}
	
	// tiek pievienoti kritēriji rakstu atlasei
	$conditions = array();
	
	// redzami izvēlētā apakšprojekta vai $lang=0 raksti
	$conditions[] = '(`pages`.`lang` = ' . (int)$android_lang . ' || `pages`.`lang` = 0)';

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
 *  Jaunāko miniblogu saraksts.
 *
 *  Atlasa un atgriež JSON sarakstu ar jaunākajiem miniblogiem,
 *  kas pievienoti vai nu grupās, kurām lietotājs ir pieteicies, vai ārpus tām.
 *
 *  Šobrīd apakšprojektu miniblogi netiek ņemti vērā un ir izlaisti.
 */
function a_fetch_miniblogs() {
	global $auth, $db, $android_lang;      
	
	// vienā lappusē redzamo miniblogu skaits;
	// lappušu saraksta lietotnē nav, tā vietā nākamās lapas ieraksti 
	// pievienojas aiz iepriekšējiem    
	$mbs_in_page = 10;
	
	// nosaka, cik mbs SQL pieprasījumā izlaist
	if (isset($_GET['page'])) {
		$skip = $mbs_in_page * intval($_GET['page']);
	} else {
		$skip = 0;
	}

	// atlasa grupas, kurās lietotājs ir pieteicies;    
	// iedomājos tās šausmas, ja būtu jāredz visi miniblogi :(
	if ($auth->level == 1) {
		$groupquery = '1 = 1';
	} else {
	
		// visi ieraksti, kas atrodas ārpus grupām
		$usergroups = array("`miniblog`.`groupid` = '0'");
		
		if ($auth->ok === true) {
			// grupas, kurās lietotājs ir administrators
			$g_owners = $db->get_col("SELECT id FROM clans WHERE owner = '$auth->id'");
			if ($g_owners) {
				foreach ($g_owners as $g_owner) {
					$usergroups[] = "`miniblog`.`groupid` = '" . $g_owner . "'";
				}
			}
			// grupas, kurās lietotājam ir parasts statuss
			$g_members = $db->get_col("SELECT clan FROM clans_members WHERE user = '$auth->id' AND approve = '1'");
			if ($g_members) {
				foreach ($g_members as $g_member) {
					$usergroups[] = "`miniblog`.`groupid` = '" . $g_member . "'";
				}
			}
		}
		
		// sakonkatenē visus kritērijus vienā stringā, lai ievietotu query
		$groupquery = implode(' OR ', $usergroups);
	}

	// atlasa pašus miniblogus
	$mbs = $db->get_results("
		SELECT
			`miniblog`.`id`         AS `id`,
			`miniblog`.`text`       AS `text`,
			`miniblog`.`date`       AS `date`,
			`miniblog`.`author`     AS `author`,
			`miniblog`.`posts`      AS `posts`,
			`miniblog`.`groupid`    AS `groupid`,
			`miniblog`.`closed`     AS `closed`,
			`users`.`avatar`        AS `avatar`,
			`users`.`deleted`       AS `deleted`,
			`users`.`av_alt`        AS `av_alt`,
			`users`.`id`            AS `user_id`,
			`users`.`nick`          AS `nick`,
			`users`.`level`         AS `level`
		FROM
			`miniblog`  USE INDEX(`parent_2`),
			`users`     USE INDEX(`PRIMARY`)
		WHERE
			`miniblog`.`removed`    = '0' AND
			`miniblog`.`parent`     = '0' AND
			`miniblog`.`type`       = 'miniblog' AND
			`miniblog`.`lang`       = '$android_lang' AND
			(" . $groupquery . ") AND
			`users`.`id`            = `miniblog`.`author`
		ORDER BY
			`miniblog`.`bump`
		DESC LIMIT $skip, $mbs_in_page
	");

	// masīvs, kas tiks atgriezts
	$arr_mbs = array();
	
	if (!$mbs) {
		return $arr_mbs;
	}
	
	foreach ($mbs as $mb) {

		// kaut kas šeit tiek eskeipots
		$mb->text = mb_get_title($mb->text);

		// paslēpj spoilerus
		if (strpos($mb->text, 'spoiler') !== false) {
			$mb->text = preg_replace('/\[spoiler\](.*)\[\/spoiler\]/is', "(spoiler)", $mb->text);
		}
		
		// atkarībā no ekrāna orientācijas jāatgriež atšķirīgs garums
		if (isset($_GET['length'])) {
			$mb->text = textlimit($mb->text, 250, '...');
		} else {
			$mb->text = textlimit($mb->text, 100, '...');
		}

		// aizstāj dzēsto profilu lietotājvārdus
		if (!empty($mb->deleted)) {
			$mb->nick = 'dzēsts';
		}
		
		// iegūst pareizu avatara adresi un grupas nosaukumu
		$avatar = '';
		$group_title = '';
		// grupu miniblogiem rādīs grupas avatarus
		if ($mb->groupid != 0) {
			$group = $db->get_row("SELECT `title`,`avatar`,`strid` FROM `clans` WHERE `id` = '$mb->groupid'");
			if ($group->avatar) {
				$group->av_alt = 1; // jo funkcija pārbaudīs av_alt vērtību
				$avatar = a_get_user_avatar($group, 's');
			}
			if ($group) {
				$group_title = ' @ ' . $group->title;
			}
		// pārējiem miniblogiem - to autoru avatarus
		} else {
			$avatar = a_get_user_avatar($mb, 's');
		}        
		
		// atgriežamais masīvs
		$arr_mbs[] = array(
			'mb-id'             => $mb->id,
			'mb-author'         => a_fetch_user($mb->user_id, $mb->nick, $mb->level),
			'mb-text'           => $mb->text,
			'mb-date'           => 'pirms ' . time_ago(strtotime($mb->date)),
			'mb-avatar'         => $avatar,
			'mb-posts'          => $mb->posts,
			'mb-closed'         => (bool)$mb->closed,
			'mb-group-id'       => $mb->groupid,
			'mb-group-title'    => $group_title
		);
	}
	
	return $arr_mbs;
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
 *  Atgriež Android lietotnei nepieciešamos lietotāja datus.
 *
 *  @return array   masīvs ar lietotāja datiem
 */
function a_fetch_user($user_id = 0, $nick = '-', $level = 0) {
	global $auth, $online_users, $busers;
	
	// atgriežamais masīvs
	$data = array();
	
	// ja parametri nav norādīti, jāatgriež dati par autorizēto lietotāju
	if ($user_id == 0) {
		$user_id    = $auth->id;
		$user_nick  = $auth->nick;
		$user_level = $auth->level;  
	// pārējos gadījumos meklē datus par norādīto lietotāju
	} else {
		$user_id    = (int)$user_id;
		$user_nick  = $nick;
		$user_level = (int)$level;
	}

	$online_status  = false;
	$online_type    = 0;
	
	
	// vai lietotājs ir tiešsaistē?
	if ( (!empty($user_id) && !empty($online_users['onlineusers'][$user_id])) || (!empty($online_users['onlineusers']) && in_array($user_nick, $online_users['onlineusers'])) ) {
	
		$online_status = true;
		
		// mob
		if (!empty($online_users['mobileusers']) && in_array($user_nick, $online_users['mobileusers'])) {
			$online_type = 1;
		// cits
		} else {
			$online_type = 0;
		}
	}
	
	// bloķētie lietotāji
	if (!empty($busers) && !empty($busers[$user_id])) {
		$level = -1;
	}
	
	$data = array(
		'id'        => (int)$user_id, 
		'nick'      => $user_nick,
		'level'     => (int)$user_level,
		'online'    => (bool)$online_status,
		'type'      => (int)$online_type
	);

	return $data;
}

/**
 *  Komentāra vērtēšana.
 *
 *  Strādā rakstos, miniblogos un attēlos
 *
 *  @param int      vērtējamā komentāra id
 *  @param string   'article'/'miniblog'/'image'
 *  @param bool     vai vērtēt pozitīvi?
 */
function a_rate_comment($comment_id = 0, $type = 'article', $positive = true) {
	global $db, $auth, $remote_salt, $json_page;
	
	if ($comment_id == 0) {
		a_error('Kļūda'); 
		return;
	}
	
	$comment_id  = (int)$comment_id;
	$positive   = ($positive) ? 'plus' : 'minus';
	
	// vērtēt neļauj pārāk bieži
	if (isset($_SESSION['antiflood_rate']) && 
		microtime(true) - $_SESSION['antiflood_rate'] < 0.5) {
		
		$_SESSION['antiflood_rate'] = microtime(true);
		$db->query("
			UPDATE `users` 
			SET `vote_today` = (`vote_today` + 3)
			WHERE `id` = " . (int)$auth->id . "
		");
		
		a_error('Hold your horses!'); 
		return;
	}
	$_SESSION['antiflood_rate'] = microtime(true);
	
	// vērtēšanas dienas limita pārbaude
	$limit = (5 + $auth->karma / 30);
	if (im_mod()) {
		$limit += 50;
	}
	if ($auth->vote_today >= $limit) {
		a_error('Sasniegts dienas limits'); 
		return;
	}
	
	// nosaka datubāzes tabulu, kuras ieraksts jāvērtē
	$table = 'comments';
	if ($type === 'miniblog') {
		$table = 'miniblog';
	} else if ($type === 'image') {
		$table = 'galcom';
	}
	
	// parent ieraksta esamības pārbaude
	$comment = $db->get_row("
		SELECT `id`, `vote_users`, `vote_value`, `author` 
		FROM `" . $table . "` 
		WHERE `id` = " . (int)$comment_id . "
	");
	if (!$comment || empty($comment)) {
		a_error('Vērtēts neeksistējošs ieraksts'); 
		return;
	}
	
	// sevi plusot/mīnusot nav ļauts
	if ($comment->author == $auth->id) {
		a_error('Savu ierakstu nevar vērtēt'); 
		return;
	}
	
	// drošības atslēgas pārbaude xsrf tipa uzbrukumiem
	$key = substr(md5($comment->id . $remote_salt . $auth->id), 0, 5);
	if (!isset($_GET['safe']) || $_GET['safe'] != $key) {
		a_error('no hacking, pls'); 
		return;
	}

	// pārbauda, vai šis lietotājs komentāru jau nav vērtējis
	$voters = array();
	if (!empty($comment->vote_users)) {
		$voters = unserialize($comment->vote_users);
	}   
	if (in_array($auth->id, $voters)) {
		a_error('Ieraksts jau novērtēts'); 
		return;
	}
	
	// pievieno šo lietotāju komentāra vērtētājiem
	$voters[] = $auth->id;
	$comment->vote_users = serialize($voters);

	// plusiņš!
	if ($positive === 'plus') {
		$db->query("
			UPDATE `" . $table . "` 
			SET
				`vote_value` = (`vote_value` + 1), 
				`vote_users` = '" . $comment->vote_users . "' 
			WHERE `id` = " . (int)$comment_id . "
		");
		$db->query("
			UPDATE `users` 
			SET 
				`vote_others` = (`vote_others` + 1), 
				`vote_total` = (`vote_total` + 1), 
				`vote_today` = (`vote_today` + 1) 
			WHERE `id` = " . (int)$auth->id . "
		");
		$comment->vote_value++;
		get_user($auth->id, true);
	}
	// mīnusiņš!
	else {
		$db->query("
			UPDATE `" . $table . "` 
			SET 
				`vote_value` = (`vote_value` - 1), 
				`vote_users` = '" . $comment->vote_users . "' 
			WHERE `id` = " . (int)$comment_id . "
		");
		$db->query("
			UPDATE `users` 
			SET 
				`vote_others` = (`vote_others` - 1), 
				`vote_total` = (`vote_total` + 1), 
				`vote_today` = (`vote_today` + 1) 
			WHERE `id` = " . (int)$auth->id . "
		");
		$comment->vote_value--;
		get_user($auth->id, true);
	}
	
	// atgriezīs lietotnei jauno vērtējumu
	$json_page = array(
		'vote_value' => (int)$comment->vote_value
	);
}

/**
 *  Minibloga komentāra pievienošana.
 *
 *  @param object   dati par lietotāju, kurš pievienojis parent miniblogu
 */
function a_add_mb_comment($inprofile, $android = false) {
	global $db, $auth, $remote_salt;
 
	if (!isset($_POST['comment_id']) || !isset($_POST['comment'])) {
		a_error('Kļūdains pieprasījums!'); return;
	}
	$to = intval($_POST['comment_id']);
	
	/*if (!isset($_POST['token']) || $_POST['token'] != md5('mb' . intval($_GET['single']) . $remote_salt . $auth->nick)) {
		a_error('Hacking around?'); return;
	}*/

	if (get_mb_level($to) > 1 && $auth->level != 1) {
		a_error('Too deep ;('); return;
	}

	// parent komentāra dati
	$reply_to = $db->get_row("SELECT * FROM `miniblog` WHERE `id` = '$to' AND `removed` = '0' AND `groupid` = '0' ");

	$reply_to_id = 0;
	$mainid = $to;
	
	if ($reply_to->parent != 0) {
		$mainid = $reply_to->parent;
		$reply_to_id = $reply_to->id;
	}

	$body = post2db($_POST['comment']);

	// vai parents eksistē? vai tēma nav slēgta?
	$check = $db->get_var("SELECT `author` FROM `miniblog` WHERE `id` = '" . $mainid . "' AND `removed` = '0' AND `groupid` = '0' ");
	if (!$check || $check != $inprofile['id']) {
		a_error('Kļūdains parent id!'); return;
	}
	$check2 = $db->get_var("SELECT `author` FROM `miniblog` WHERE `id` = '" . $mainid . "' AND `closed` = '1' ");
	if ($check2) {
		a_error('Miniblogs slēgts'); return;
	}
	
	// viss kārtībā, var pievienot
	if ($mainid) {
	
		// flood kontrole
		if (isset($_SESSION['antiflood']) && $_SESSION['antiflood'] > time() - 4) {
			a_error('no flood, pls'); return;
		}        
		$_SESSION["antiflood"] = time();

		// pievieno komentāru
		$newid = post_mb(array(
			'text' => $body,
			'parent' => $mainid,
			'reply_to' => $reply_to_id
		));

		if ($check == $auth->id) {
			$str = 'savā';
		} else {
			$str = $inprofile['nick'];
		}
		$body = $db->get_var("SELECT `text` FROM `miniblog` WHERE `id` = '$mainid' ");

		$title = mb_get_title(stripslashes($body));
		$strid = mb_get_strid($title, $mainid);
		$url = '/say/' . $check . '/' . $mainid . '-' . $strid;

		// bump, notifikācijas
		if (!isset($_POST['no-bump'])) {
			push('Atbildēja <a href="' . $url . '#m' . $newid . '">' . $str . ' miniblogā &quot;' . textlimit(hide_spoilers($title), 32, '...') . '&quot;</a>', '', 'mb-answ-' . $mainid);

			$newpost = $db->get_row("SELECT * FROM `miniblog` WHERE id = '$newid'");
			$newpost->text = mention($newpost->text, $url, 'mb', $mainid);
			$db->query("UPDATE `miniblog` SET `text` = '" . sanitize($newpost->text) . "' WHERE id = '$newpost->id'");

			notify($inprofile['id'], 3, $mainid, $url, textlimit(hide_spoilers($title), 64));
			if (!empty($reply_to_id) && $inprofile['id'] != $reply_to->author) {
				notify($reply_to->author, 3, $mainid, $url, textlimit(hide_spoilers($title), 64));
			}
		}

		// ja miniblogā ir vismaz 500 komentāri, to aizver un izveido jaunu miniblogu,
		// kurā viss turpinās
		$topic = $db->get_row("SELECT * FROM `miniblog` WHERE `id` = '$mainid'");
		
		if ($topic->posts >= 500) {
		
			$body = sanitize($topic->text . '<p>(<a href="' . $url . '">Tēmas</a> turpinājums)</p>');
			$db->query("INSERT INTO miniblog (`author`,`date`,`text`,`ip`,`bump`,`lang`) VALUES ('$topic->author',NOW(),'$body','$topic->ip','" . time() . "','$topic->lang')");
			
			$new = $db->insert_id;
			
			$newtopic = $db->get_row("SELECT * FROM miniblog WHERE id = '$new'");
			$newtitle = mb_get_title($newtopic->text);
			$newstrid = mb_get_strid($newtitle, $new);
			$newurl = '/say/' . $topic->author . '/' . $newtopic->id . '-' . $newstrid;
			
			$reason = sanitize('Sasniegts 500 atbilžu limits, slēgts automātiski. Tēmas tupinājums: <a href="' . $newurl . '">http://' . $_SERVER['HTTP_HOST'] . $newurl . '</a>.');
			$db->query("UPDATE `miniblog` SET `closed` = '1', `close_reason` = '$reason', `closed_by` = '17077' WHERE `id` = '$mainid'");
			
			if (!$android) {
				redirect($newurl);
			}
		}
	} else {
		a_error('Kļūdains pieprasījums');
	}
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
 *  Atgriezīs sarakstu masīva veidā ar lietotāja pēdējām notifikācijām.
 */
function a_fetch_notifications() {
    global $db, $auth, $config_domains;
    global $android_lang;

	$arr_notifs = array(); // atgriežamais notifikāciju masīvs
    $notif_limit = 15; // cik pēdējos jaunumus atgriezt

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
    
    $user_notifications = $db->get_results("
        SELECT * FROM `notify` 
        WHERE 
            `user_id` = ".(int)$auth->id." AND
            `lang` = ".(int)$android_lang."
        ORDER BY `bump` DESC 
        LIMIT 0, $notif_limit
    ");
    
    if (!$user_notifications) {
        a_error('Notikumu nav!');
        return false;
    }
    
    $inner_counter = 0;
    foreach ($user_notifications as $notify) {    
        $arr_notifs[] = [
            'type' => $texts[$notify->type],
            'text' => textlimit(trim($notify->info), 45, ''),
            'date' => 'pirms ' . time_ago(strtotime($notify->bump)),
            'project' => $config_domains[$notify->lang]['domain']
        ];
    }

    return $arr_notifs;
}

/**
 *  Tiešsaistē esošie lietotāji.
 *
 *  Atgriež sarakstu ar tiešsaistē esošajiem lietotājiem 
 *  atvērtajā apakšprojektā pēdējās x sekundēs.
 *
 *  Klāt pievieno arī informāciju par tiešsaistē esošiem lietotājiem
 *  katrā klasē.
 */
function a_fetch_online($force = false) {
	global $db, $m, $android_lang;
    global $online_users, $busers;
    
    // laiks sekundēs, kurā lietotāju uzskata par tiešsaistē esošu
    $online_seconds = 360;
    
	$data = array();
    
    // satura nolasīšana no memcached
	if ($force || !($data = $m->get('android-online-'.$android_lang))) {

        $online = null;
        $classes = null;
        
		$lastseen = $db->get_results("
            SELECT
                DISTINCT(`visits`.`user_id`) AS `user_id`,
                `users`.`nick`,
                `users`.`level`
            FROM `visits`
                JOIN `users` ON `visits`.`user_id` = `users`.`id`
            WHERE
                `visits`.`site_id` = ".(int)$android_lang." AND
                `visits`.`lastseen` > '".date('Y-m-d H:i:s', time() - $online_seconds)."'
            ORDER BY
                `users`.`level` ASC,
                `users`.`nick` ASC
        ");

        if (!$lastseen) {
            a_error('Šobrīd neviena lietotāja nav tiešsaistē');
            return false;
        }
        
        // visi tiešsaistes lietotāji tiek pievienoti masīvam
        foreach ($lastseen as $user) {       

            // nosaka ierīci, no kādas lietotājs pieslēdzies
            $device = 0;
            if (!empty($online_users['mobileusers']) && 
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
                'is_banned' => (bool)$is_banned,
                'device' => (int)$device
            );
            
            // palielinās lietotāju skaitu šī lietotāja klasē
            if (isset($classes[$user->level])) {
                $classes[$user->level] += 1;
            } else {
                $classes[$user->level] = 1;
            }
        }

        $data = array(
            'online_users' => $online,
            'by_classes' => $classes
        );
        
		$m->set('android-online-'.$android_lang, $data, false, 30);
	}
    
	return $data;
}
