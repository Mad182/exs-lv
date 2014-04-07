<?php
/**
 *  android.exs.lv izmantotās funkcijas
 */
 
 
/**
 *  Atgriež JSON sarakstu ar jaunākajiem exs.lv rakstiem
 *
 *  Atbalsta pārvietošanos pa lapām un apakšprojektus.
 *
 *  @param  int     skaits, cik rakstu rādīt vienā lapā
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
    $mbs_in_page = 15;
    
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
    $mbs = $db->get_results("SELECT
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
    DESC LIMIT $skip, 15");

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
