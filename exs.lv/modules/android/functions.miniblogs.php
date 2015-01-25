<?php
/**
 *  Android miniblogiem paredzētas funkcijas.
 *
 *  Šīs funkcijas rakstītas daudz maz universālā veidā, lai katra atbalstītu
 *  gan parastos miniblogus, gan grupās esošos, gan nākotnē arī citus.
 */
 
/**
 *  Atgriezīs sarakstu ar jaunākajiem miniblogiem.
 *
 *  Norādot grupas ID, atgriezti tiks tikai šīs grupas miniblogi.
 */
function a_fetch_miniblogs($group_id = 0) {
	global $auth, $db, $android_lang;      
	
    $group_id = (int)$group_id;
    
    $max_pages = 10;
    $mbs_per_page = 20;
    $current_page = 1;
    
    // noteiks, vai lietotājam maz ir piekļuve norādītajai grupai
    if ($group_id != 0 && !a_member_of($group_id)) {
        return;
    }    
    
    // lappušu iestatījumi
    if (isset($_GET['page'])) {
        $_GET['page'] = (int)$_GET['page'];
        if ($_GET['page'] < 1 || $_GET['page'] > $max_pages) {
            $_GET['page'] = 1;
        }
        $current_page = $_GET['page'];
    }
    $lim_start = ($current_page - 1) * $mbs_per_page;

    // visi ieraksti, kas atrodas norādītajā grupā
    if ($group_id > 0) {
        $groups = array($group_id);   
        
    // ieraksti gan ārpus grupām, gan grupās, kurās lietotājs ir biedrs
    } else {

        // ieraksti ārpus grupām
        $groups = array(0);
        
        // grupas, kurās lietotājs ir administrators
        $g_owners = $db->get_col("
            SELECT `id` FROM `clans` WHERE `owner` = ".$auth->id
        );
        if ($g_owners) {
            foreach ($g_owners as $g_owner) {
                $groups[] = (int)$g_owner;
            }
        }
        
        // grupas, kurās lietotājam ir parasts statuss
        $g_members = $db->get_col("
            SELECT `clan` FROM `clans_members` 
            WHERE `user` = ".$auth->id." AND `approve` = 1
        ");
        if ($g_members) {
            foreach ($g_members as $g_member) {
                $groups[] = (int)$g_member;
            }
        }
    }
    $groups = implode(',', $groups);

    // atlasīs miniblogus, kas atbilst noteiktajiem kritērijiem
	$mbs = $db->get_results("
		SELECT
			`miniblog`.`id` AS `mb_id`,
			`miniblog`.`text`,
			`miniblog`.`date`,
			`miniblog`.`author`,
			`miniblog`.`posts`,
			`miniblog`.`groupid`,
			`miniblog`.`closed`,
			`users`.`avatar`,
			`users`.`deleted`,
			`users`.`av_alt`,
			`users`.`id` AS `user_id`,
			`users`.`nick`,
			`users`.`level`
		FROM
			`miniblog`  USE INDEX(`parent_2`),
			`users`     USE INDEX(`PRIMARY`)
		WHERE
			`miniblog`.`removed` = 0 AND
			`miniblog`.`parent` = 0 AND
			`miniblog`.`type` = 'miniblog' AND
			`miniblog`.`lang` = ".(int)$android_lang." AND
            `miniblog`.`groupid` IN(".$groups.") AND
			`users`.`id` = `miniblog`.`author`
		ORDER BY
			`miniblog`.`bump` DESC
		LIMIT ".$lim_start.", ".$mbs_per_page."
	");

	if (!$mbs) {
        if ($group_id > 0) {
            a_error('Grupā vēl nav neviena minibloga');
        } else {
            a_error('Neizdevās atlasīt jaunākos miniblogus');
        }
        return;
	}
	
	$arr_mbs = array();	
    
	foreach ($mbs as $mb) {

		// kaut kas šeit tiek eskeipots
		$mb->text = mb_get_title($mb->text);
		$mb->text = textlimit($mb->text, 300, '...');

		// paslēps spoilerus
		if (strpos($mb->text, 'spoiler') !== false) {
			$mb->text = preg_replace('/\[spoiler\](.*)\[\/spoiler\]/is', 
                "(spoiler)", $mb->text);
		}		

        // dzēstie lietotājvārdi
		if (!empty($mb->deleted)) {
			$mb->nick = 'dzēsts';
		}

		$avatar = '';
		$group_title = '';
        
        // grupu miniblogiem rādīs grupas avatarus
        if ($group_id == 0 && $mb->groupid != 0) {
            $group = $db->get_row("
                SELECT `title`, `avatar`, `strid` FROM `clans` 
                WHERE `id` = ".(int)$mb->groupid
            );
            if ($group->avatar) {
                $group->av_alt = 1; // jo funkcija pārbaudīs av_alt vērtību
                $avatar = a_get_user_avatar($group, 's');
            }
            $group_title = ' @ ' . $group->title;

        // pārējiem miniblogiem - to autoru avatarus
        } else {
            $avatar = a_get_user_avatar($mb, 's');
        }

		$arr_mbs[] = array(
			'id' => (int)$mb->mb_id,
			'text' => $mb->text,
			'author' => a_fetch_user($mb->user_id, $mb->nick, $mb->level),
			'date' => 'pirms ' . time_ago(strtotime($mb->date)),
			'av_url' => $avatar,
			'posts' => (int)$mb->posts,
			'is_closed' => (bool)$mb->closed,
			'group_id' => (int)$mb->groupid,
			'group_title' => $group_title
		);
	}
    
    a_append(array('miniblogs' => $arr_mbs));
}

/**
 *  Jauna minibloga pievienošana.
 *
 *  Ar miniblogu tiek saprasts ieraksts `miniblog` tabulā (t.i., gan miniblogs,
 *  gan tā komentāri). Pagaidām neatbalsta junk sadaļu.
 */
function a_add_miniblog($data) {
    global $db, $auth;
    global $android_lang;
    
    // iesniegto datu esamības pārbaudes
    if (empty($data) || !isset($data['group_id']) || 
        !isset($data['parent_id']) || !isset($data['content']) ||
        !isset($data['is_private'])) {
        a_error('Pieprasījuma kļūda');
        return;
    } else if (empty(trim($data['content']))) {
        a_error('Nevar pievienot tukšu miniblogu');
        return;
    }
    
    // dažādi mainīgie
    $group_id = (int)$data['group_id'];
    $group_data = '';    
    $parent_id = (int)$data['parent_id'];
    $parent_user_id = $auth->id;
    $outer_parent_id = $parent_id;    
    $mb_level = 1; // kaut kāds dziļuma parametrs miniblogiem
    
    // anti-xsrf aizsardzība
    if (!a_check_xsrf()) {
        a_error('no hacking, pls');
        a_log('Pievienojot miniblogu, nenorādīja pareizu XSRF atslēgu');        
    // plūdu kontrole
    } else if (isset($_SESSION['antiflood']) && 
        $_SESSION['antiflood'] >= time() - 15) {
        a_error('Pārāk bieža pievienošana, brīdi uzgaidi');
        return;
    }
    $_SESSION['antiflood'] = time();
    
    // noteiks, vai lietotājam maz ir piekļuve norādītajai grupai,
    // ja ieraksts tiešām tiek pievienots grupai
    if ($group_id != 0) {
        $group_data = $db->get_row("
            SELECT
                `clans`.*,
                IFNULL(`clans_members`.`approve`, 0) AS `approved`
            FROM `clans`
            LEFT JOIN `clans_members` ON (
                `clans`.`id` = `clans_members`.`clan` AND
                `clans_members`.`user` = ".$auth->id." AND
                `clans_members`.`approve` = 1
            )
            WHERE
                `clans`.`id` = ".$group_id." AND
                `clans`.`lang` = ".$android_lang."
        ");
        if (!$group_data) {
            a_error('Grupa neeksistē');
            a_log('Vēlējās pievienot ierakstu neeksistējošai grupai ('.$group_id.')');
            return;
        } else if ($group_data->owner !== $auth->id && $group_data->approved == '0') {
            a_error('Pieeja grupai liegta');
            a_log('Vēlējās pievienot ierakstu grupai ('.$group_id.'), kurai nav piekļuves');
            return;
        } else if ($group_data->archived == 1) {
            a_error('Arhivētās grupās nevar veikt ierakstu');
            a_log('Vēlējās pievienot ierakstu arhivētai grupai ('.$group_id.')');
            return;
        }
        $mb_level = 3; // kaut kāds dziļuma parametrs grupām
    }
    
    // vai norādītais parent miniblogs vispār eksistē? nav slēgts? nav dzēsts?
    if ($parent_id !== 0) {
    
        $parent_data = $db->get_row("
            SELECT `author`, `parent`, `closed`, `reply_to` FROM `miniblog` 
            WHERE 
                `id` = ".$parent_id." AND
                `removed` = 0 AND
                `groupid` = ".$group_id."
        ");
        if (!$parent_data) {
            a_error('Atbildāmais ieraksts neeksistē vai ir dzēsts');
            a_log('Centās pievienot atbildi neeksistējošam vai dzēstam miniblogam ('.$parent_id.')');
            return;
        } else if ($parent_data->reply_to == 0 && $parent_data->closed == 1) {
            a_error('Miniblogs slēgts komentēšanai');
            a_log('Centās pievienot ierakstu slēgtam miniblogam ('.$parent_id.') #1');
            return;
        }
        $parent_user_id = $parent_data->author;
        
        // ja parent miniblogs ir komentārs, jāpārbauda paša minibloga flagi
        if ($parent_data->parent != 0) {
            $outer_parent_id = $parent_data->parent;
            
            // atlasīs datus par pašu miniblogu
            $miniblog = $db->get_row("
                SELECT `miniblog`.`closed` FROM `miniblog`                
                WHERE
                    `miniblog`.`id` = ".(int)$parent_data->parent." AND
                    `miniblog`.`removed` = 0 AND
                    `miniblog`.`groupid` = ".$group_id."
            ");
            if (!$miniblog) {
                a_error('Miniblogs neeksistē');
                a_log('Vēlējās pievienot ierakstu neeksistējošam miniblogam ('.$parent_data->parent.')');
                return;
            } else if ($miniblog->closed == 1) {
                a_error('Miniblogs slēgts komentēšanai');
                a_log('Centās pievienot ierakstu slēgtam miniblogam ('.$parent_data->parent.') #2');
                return;
            }
        }  
    
        // minibloga "dziļumam" ir sava robeža
        $current_level = get_mb_level($parent_id);
        if ($current_level > $mb_level) {
            a_error('Too deep ;(');
            a_log('Vēlējās pievienot minibloga ierakstu pārāk dziļā rekursivitātes līmenī ('.$current_level.')');
            return;
        }
    }
    
    // ja ieraksts ir pirmā līmeņa komentārs, tam nav jānorāda `reply_to`
    $reply_to = ($parent_id == $outer_parent_id) ? 0 : $parent_id;

    // viss kārtībā, tā ka ierakstu drīkst pievienot
    $insert_id = post_mb(array(
        'groupid' => $group_id,
        'parent' => $outer_parent_id,
        'reply_to' => $reply_to,
        'text' => post2db($data['content']),
        'private' => (bool)$data['is_private'],
        'lang' => $android_lang
    ));
    
    // ārējā minibloga dati, kas nepieciešami notifikācijām
    if ($outer_parent_id == 0) {
        $outer_parent_id = $insert_id;
    }
    $main_mb = $db->get_row("
        SELECT
            `miniblog`.`id` AS `mb_id`,
            `miniblog`.`text`,
            `users`.`id` AS `user_id`, 
            `users`.`nick`
        FROM `miniblog`
        JOIN `users` ON `miniblog`.`author` = `users`.`id`
        WHERE `miniblog`.`id` = ".(int)$outer_parent_id."
    ");    
    if (empty($main_mb)) {
        a_log('Neizdevās pievienot notifikācijas jaunam ierakstam');
        a_append(array('miniblog_id' => (int)$main_mb->mb_id));
        return;
    }
    $mb_title = mb_get_title($main_mb->text);
    $mb_strid = mb_get_strid($mb_title, $main_mb->mb_id); 
    
    // @mentions apstrāde pievienotajam ierakstam
    $inserted_mb = $db->get_row('
        SELECT `id`, `text` FROM `miniblog` WHERE `id` = '.(int)$insert_id
    );
    $url = '/say/'.$main_mb->user_id.'/'.$main_mb->mb_id.'-'.$mb_strid;
    if ($group_id > 0) { // grupās adrese ir cita, tāpēc jāpārraksta
        if (!empty($group_data->strid)) {
            $url = '/'.$group_data->strid.'/forum/'.base_convert($outer_parent_id, 10, 36);
        } else {
            $url = '/group/'.$group_id.'/forum/'.base_convert($outer_parent_id, 10, 36);
        }
    }
    $type = ($group_id > 0) ? 'group' : 'mb';

    $inserted_mb->text = mention($inserted_mb->text, $url, $type, $main_mb->mb_id);
    $db->update('miniblog', $inserted_mb->id, array(
        'text' => sanitize($inserted_mb->text)
    ));
    
    // žurnālieraksts lietotāja profilā    
    if ($group_id !== 0) { // ierakstiem grupās

        $db->query("UPDATE `clans` SET `posts` = '".$db->get_var("SELECT count(*) FROM `miniblog` WHERE `groupid` = ".$group_id)."' WHERE id = ".$group_id);

        $avatar_data = new stdClass;
        $avatar_data->avatar = $group_data->avatar;
        $avatar_data->av_alt = 0;
    
        if ($outer_parent_id === $insert_id) { // temats
            push('Izveidoja tematu grupā <a href="'.$url.'">'.$group_data->title.'</a>', get_avatar($avatar_data, 's', true), 'g'.$outer_parent_id);
        } else { // komentārs/atbilde
            if (!$group_data->hide_intro) {
                push('Atbildēja <a href="'.$url.'#m'.$insert_id.'">'.$group_data->title.' grupā &quot;'.textlimit($mb_title, 32, '...') . '&quot;</a>', get_avatar($avatar_data, 's', true), 'g-'.$outer_parent_id);
            } else {
                push('Atbildēja '.$group_data->title.' grupā', get_avatar($avatar_data, 's', true), 'g-'.$outer_parent_id);
            }
        }
    } else { // ierakstiem ārpus grupām
        if ($outer_parent_id === $insert_id) { // temats
            push('Izveidoja <a href="/say/'.$auth->id.'/'.$inserted_mb->id.'-'.$mb_strid.'">minibloga ierakstu &quot;'.textlimit(hide_spoilers($mb_title), 32, '...') . '&quot;</a>');
        } else { // komentārs/atbilde
            $location = 'savā';
            if ($main_mb->user_id != $auth->id) {
                $location = $main_mb->nick;
            }
            push('Atbildēja <a href="'.$url.'#m'.$inserted_mb->id.'">'.$location.' miniblogā &quot;'.textlimit(hide_spoilers($mb_title), 32, '...').'&quot;</a>', '', 'mb-answ-'.$outer_parent_id);
        }
    }
    
    // notifikācijas, ja ieraksts ir komentārs vai atbilde
    if ($outer_parent_id !== $insert_id) {
        
        $mid = 3;
        if (!empty($group_id)) { // ja ieraksts tika veikts grupā...
            $mb_title = $group_data->title.' - '.$mb_title;
            $mid = 8;
        }
   
        // notifikācija ārējā minibloga autoram
        notify($main_mb->user_id, $mid, $main_mb->mb_id, $url, textlimit(hide_spoilers($mb_title), 64));
        
        // notifikācija atbildāmā ieraksta autoram, 
        // ja tiek atbildēts citam lietotājam
        if ($parent_id != $main_mb->mb_id && $parent_user_id != $main_mb->user_id) {
            notify($parent_user_id, $mid, $main_mb->mb_id, $url, textlimit(hide_spoilers($mb_title), 64));
        }
    }
     
    // ja miniblogā ir vismaz 500 komentāri, to aizvērs un izveidos jaunu
    $topic = $db->get_row("
        SELECT `posts`, `text`, `author`, `lang`, `ip`
        FROM `miniblog` WHERE `id` = ".$main_mb->mb_id
    );
    if (!empty($topic) && $topic->posts >= 500) {
    
        $body = sanitize($topic->text.'<p>(<a href="'.$url.'">Tēmas</a> turpinājums)</p>');
        $db->insert('miniblog', array(
            'groupid' => $group_id,
            'author' => $topic->author,
            'date' => 'NOW()',
            'text' => $body,
            'ip' => $topic->ip,
            'bump' => time(),
            'lang' => $topic->lang
        ));
        $insert_id = $db->insert_id;
        
        $topic = $db->get_row("
            SELECT * FROM `miniblog` WHERE `id` = ".$insert_id
        );
        if (!empty($topic)) {
            $title = mb_get_title($topic->text);
            $strid = mb_get_strid($title, $topic->id);

            $url = '/say/'.$topic->author.'/'.$topic->id.'-'.$strid;
            if (!empty($group_id)) {
                if (!empty($group_data->strid)) {
                    $url = '/'.$group_data->strid.'/forum/'.base_convert($insert_id, 10, 36);
                } else {
                    $url = '/group/'.$group_id.'/forum/'.base_convert($insert_id, 10, 36);
                }
            }
            
            $reason = sanitize('Sasniegts 500 atbilžu limits, slēgts automātiski. Tēmas tupinājums <a href="'.$url.'">šeit</a>.');
            $db->query("UPDATE `miniblog` SET `closed` = 1, `close_reason` = '".$reason."', `closed_by` = 17077 WHERE `id` = ".$main_mb->mb_id);
        }
    }
    
    a_append(array('miniblog_id' => (int)$main_mb->mb_id));
}

/**
 *  Novērtēs norādīto komentāru ar plusu vai mīnusu.
 *
 *  Strādā rakstos, miniblogos un attēlos.
 *
 *  @param int      vērtējamā komentāra id
 *  @param string   'article'/'miniblog'/'image'
 *  @param bool     vai vērtēt pozitīvi?
 */
function a_rate_comment($comment_id = 0, $type = 'article', $positive = true) {
	global $db, $auth, $remote_salt, $json_page;
	
    $comment_id = (int)$comment_id;
	$positive = ($positive) ? 'plus' : 'minus';
    
    // dažādas drošības pārbaudes
	if ($comment_id == 0) {
		a_error('Kļūda'); 
		return;
	} else if (!a_check_xsrf()) {
        a_error('no hacking, pls');
        return;
        
    // vērtēt pārāk bieži nav atļauts
    } else if (isset($_SESSION['antiflood_rate']) && 
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
	
	// noteiks datubāzes tabulu, kuras ieraksts jāvērtē
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
		WHERE `id` = " . $comment_id . "
	");
	if (empty($comment)) {
		a_error('Vērtēts neeksistējošs ieraksts'); 
		return;
	}
	
	// sevi plusot/mīnusot nav ļauts
	if ($comment->author == $auth->id) {
		a_error('Savu ierakstu nevar vērtēt'); 
		return;
	}

	// pārbaudīs, vai šis lietotājs komentāru jau nav vērtējis
	$voters = array();
	if (!empty($comment->vote_users)) {
		$voters = unserialize($comment->vote_users);
	}   
	if (in_array($auth->id, $voters)) {
		a_error('Ieraksts jau novērtēts'); 
		return;
	}
	
	// pievienos šo lietotāju komentāra vērtētājiem
	$voters[] = $auth->id;
	$comment->vote_users = serialize($voters);

	// plusiņš!
	if ($positive === 'plus') {
		$db->query("
			UPDATE `" . $table . "` 
			SET
				`vote_value` = (`vote_value` + 1), 
				`vote_users` = '" . $comment->vote_users . "' 
			WHERE `id` = " . $comment_id . "
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
	
    // mīnusiņš!
    } else {
		$db->query("
			UPDATE `" . $table . "` 
			SET 
				`vote_value` = (`vote_value` - 1), 
				`vote_users` = '" . $comment->vote_users . "' 
			WHERE `id` = " . $comment_id . "
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
    a_append(array('vote_value' => (int)$comment->vote_value));
}

/**
 *  Noteiks, vai lietotājam ir piekļuve norādītajam grupai.
 *
 *  @param $allow_archived  - vai arhivēta grupa ir pieļaujama
 */
function a_member_of($group_id = 0, $allow_archived = true) {
    global $db, $auth, $android_lang;
    
    $group_id = (int)$group_id;
    if ($group_id < 1) {
        return false;
    }

    $group_data = $db->get_row("
        SELECT
            `clans`.*,
            IFNULL(`clans_members`.`approve`, 0) AS `approved`
        FROM `clans`
        LEFT JOIN `clans_members` ON (
            `clans`.`id` = `clans_members`.`clan` AND
            `clans_members`.`user` = ".$auth->id." AND
            `clans_members`.`approve` = 1
        )
        WHERE
            `clans`.`id` = ".$group_id." AND
            `clans`.`lang` = ".$android_lang."
    ");
    
    if (!$group_data) {
        a_error('Grupa neeksistē');
        a_log('a_member_of('.$group_id.'): norādītā grupa neeksistē');
        return false;
    } else if ($group_data->owner !== $auth->id &&
               $group_data->approved == '0') {
        a_error('Pieeja grupai liegta');
        a_log('a_member_of('.$group_id.'): lietotājam grupai nav piekļuves');
        return false;
    } else if (!$allow_archived && $group_data->archived == 1) {
        a_error('Grupa ir arhivēta');
        a_log('a_member_of('.$group_id.'): norādītā grupa ir arhivēta');
        return false;
    }
    
    return true;
}
