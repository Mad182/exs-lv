<?php
/**
 *  android.exs.lv izmantotās funkcijas
 */



/**
 *  Pievieno kļūdas paziņojumu nākamajai atbildei
 *
 *  @param string   kļūdas paziņojums
 */
function a_error($string = '') {
    global $json_state, $json_message;
    
    $json_state     = 'error';
    $json_message   = $string;
}
 
 
 
/**
 *  Atgriež JSON sarakstu ar jaunākajiem exs.lv rakstiem
 *
 *  Atbalsta pārvietošanos pa lapām un apakšprojektus.
 *
 *  @param int     skaits, cik rakstu rādīt vienā lapā
 */
function get_news($in_page = 20) {
	global $auth, $db, $lang, $android_lang;
    
    // rakstu skaits, cik izlaist
	$skip = 0;
	if (isset($_GET['page'])) {
		$skip = $in_page * (intval($_GET['page']) - 1);
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
            `pages`.`bump` DESC LIMIT $skip, $in_page
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
 *  Jaunāko miniblogu saraksts
 *
 *  Atlasa un atgriež JSON sarakstu ar jaunākajiem miniblogiem,
 *  kas pievienoti vai nu grupās, kurām lietotājs ir pieteicies, vai ārpus tām.
 *
 *  Šobrīd apakšprojektu miniblogi netiek ņemti vērā un ir izlaisti.
 */
function fetch_miniblogs() {
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
            `users`.`nick`          AS `nick`
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
                $avatar = get_user_avatar($group, 's');
            }
            if ($group) {
                $group_title = ' @ ' . $group->title;
            }
        // pārējiem miniblogiem - to autoru avatarus
        } else {
            $avatar = get_user_avatar($mb, 's');
        }        
        
        // atgriežamais masīvs
        $arr_mbs[] = array(
            'mb-id'             => $mb->id, 
            'mb-author'         => $mb->nick, 
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
 *  Ieraksta avatara adreses iegūšana
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
function get_user_avatar($user, $size = 'm') {
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
 *  Atgriež Android lietotnei nepieciešamos lietotāja datus
 *
 *  @return array   masīvs ar lietotāja datiem
 */
function fetch_user_data() {
    global $auth;
    
    $colored_nick = $auth->nick;
    if ($auth->ok) {
        $colored_nick = stylize_nick($auth->nick, $auth->level, false, $auth->id);
    }
    
    $data = array(
        'id'        => $auth->id, 
        'nick'      => $auth->nick, 
        'colorful'  => $colored_nick, 
        'level'     => $auth->level
    );
    
    return $data;
}


/**
 *  Atgriež lietotājvārdu ar pareizām krāsām un zvaigznīti
 *
 *  @param string   lietotājvārds
 *  @param int      lietotāja līmenis
 *  @param bool     ??
 *  @param int      lietotāja ID
 *  @return string  krāsains lietotājvārds HTML formā
 */
function stylize_nick($nick, $level = 0, $online = false, $userid = 0) {
	global $online_users, $busers, $site_access, $auth, $img_server;
    
	$star = '';

    // vai lietotājs ir tiešsaistē?
    // atšķiras zvaigznīte, ja izmanto mobilo versiju
	if ($online !== 'disable') {
		if ($online || (!empty($userid) && !empty($online_users['onlineusers'][$userid])) || (!empty($online_users['onlineusers']) && in_array($nick, $online_users['onlineusers']))) {
			if (!empty($online_users['mobileusers']) && in_array($nick, $online_users['mobileusers'])) {
				$star = '<span style="color: #60ef00">*</span>';
			} else {
				$star = '<span style="color: #ef6000">*</span>';
			}
		}
	}
	$nick = $star . htmlspecialchars($nick);

    // īpašo lietotāju klašu krāsas
    // (1 - admins, 2 - mods, 3 - rakstu autors, 5 - bots)
	$user_classes = array(1 => '#700', 2 => '#00b', 3 => '#070', 5 => '#777');

	foreach ($user_classes as $key => $color) {
		if ($level == $key || ($userid != 0 && !empty($site_access[$key]) && in_array($userid, $site_access[$key]))) {
			$nick = '<span style="color:' . $color . '">' . $nick . '</span>';
		}
	}

    // bloķēto lietotāju vārdi pārsvītroti
	if ($online !== 'disable' && $userid && !empty($busers)) {
		if (!empty($busers[$userid])) {
			$nick = '<span style="text-decoration:line-through;color:#000;">' . $nick . '</span>';
		}
	}

	return $nick;
}



/**
 *  Minibloga vērtēšana
 *
 *  @param id   minibloga id
 *  @param bool vai vērtēt pozitīvi?
 */
function a_rate_mb($id = 0, $type = true) {
    global $auth, $db, $json_page;
    
    if ($id == 0) {
        a_error('Kļūda!'); return;
    } 
    
    $id     = (int)$id;
    $action = ($type) ? 'plus' : 'minus';
    
    // neļauj vērtēt pārāk bieži
    if (isset($_SESSION['antiflood_rate']) && microtime(true) - $_SESSION['antiflood_rate'] < 0.5) {
		$_SESSION["antiflood_rate"] = microtime(true);
		$db->query("UPDATE `users` SET `vote_today` = `vote_today`+3 WHERE `id` = '$auth->id'");
        
        a_error('Hold your horses!'); return;
	}
	$_SESSION["antiflood_rate"] = microtime(true);
    
    // vērtējamā minibloga dati
    $comment = $db->get_row("
        SELECT `id`, `vote_users`, `vote_value`, `author` 
        FROM `miniblog` 
        WHERE `id` = '$id'
    ");
    
    if (empty($comment)) {
        a_error('Vērtēts neeksistējošs komentārs!'); return;
    }

    if ($comment->author == $auth->id) {
        a_error('Savu komentāru nevar vērtēt!'); return;
    }
    
    // pārbauda masīvu ar lietotājiem, kas miniblogu jau vērtējuši
    $check = substr(md5($comment->id . $remote_salt . $auth->id), 0, 5);

    if (!empty($comment->vote_users)) {
        $voters = unserialize($comment->vote_users);
    } else {
        $voters = array();
    }

    $voted = in_array($auth->id, $voters);    
    
    if (isset($_GET['check']) && !$voted && $_GET['check'] == $check && isset($_GET['action'])) {
        
        $voters[] = $auth->id;
        $comment->vote_users = serialize($voters);

        // vērtējumu limits
        $limit = (5 + $auth->karma / 30);
        if(im_mod()) {
            $limit += 50;
        }

        if ($auth->vote_today >= $limit) {
            a_error('Sasniegts dienas limits!'); return;
        } 
        else if ($action == 'plus') {

            $db->query("UPDATE `miniblog` SET vote_value = vote_value+1, vote_users = '" . $comment->vote_users . "' WHERE id = '$id'");
            $db->query("UPDATE users SET vote_others = vote_others+1, vote_total = vote_total+1, vote_today = vote_today+1 WHERE id = '$auth->id'");
            $comment->vote_value++;
            get_user($auth->id, true);
            
        } else {
            $db->query("UPDATE `miniblog` SET vote_value = vote_value-1, vote_users = '" . $comment->vote_users . "' WHERE id = '$id'");
            $db->query("UPDATE users SET vote_others = vote_others-1, vote_total = vote_total+1, vote_today = vote_today+1 WHERE id = '$auth->id'");
            $comment->vote_value = $comment->vote_value - 1;
            get_user($auth->id, true);
        }
        
        // atgriezīs lietotnei jauno vērtējumu
        $json_page = array(
            'vote-value' => $comment->vote_value
        );
        
    } else {
        a_error('Komentārs jau novērtēts!'); return;
    }
}
