<?php
/**
 *  Profilu meklētājs pēc IP adresēm, lietotājvārdiem, e-pastiem u.c.
 *
 *  Pie katra no atrastajiem profiliem redzama noderīga papildinformācija,
 *  piemēram, lietotāja visi bijušie lietotājvārdi u.c.
 *
 * 	Moduļa adrese: /findby
 */

$add_css[] = 'grouped-profiles.css';
 
// ne-moderatorus sūtām prom
if (!im_mod()) {
	set_flash('no hacking, pls');
	redirect();
}


/*
|--------------------------------------------------------------------------
|   Globālie moduļa mainīgie.
|--------------------------------------------------------------------------
*/

// maksimālais lappušu skaits meklētāja rezultātiem
$max_result_pages = 10;

// rādāmo rezultātu skaits vienā lappusē
$max_in_result_page = 50;


// maksimālais skaits, cik pēdējās IP var apskatīt vienā stabiņā,
// nospiežot "rādīt vairāk" pogu
$limit_total_ips = 50;

// IP skaits, cik parādīt pirms "rādīt vairāk" pogas
$limit_shown_ips = 10;

// profilu skaits, cik parādīt pirms "rādīt vairāk" pogas;
// dažiem indivīdiem ir desmitiem fake profilu!
$limit_shown_profiles = 10;


/*
|--------------------------------------------------------------------------
|   jQuery AJAX: atgriezīs saturu ar profilu meklētāju.
|--------------------------------------------------------------------------
|   Tiek izsaukta, nospiežot uz sadaļas atvēršanas cilnes,
|   lai to ielādētu satura blokā zem ciļņu izvēlnes.
*/

if (isset($_GET['_']) && isset($_GET['load'])) {
    
    $new_tpl = fetch_tpl();
    if (empty($new_tpl)) { die('error'); }
    
    $new_tpl->newBlock('mcp-find-profiles');
    $new_tpl->assignAll([
        'category-url' => $category->textid
    ]);
    echo json_encode([
        'content' => $new_tpl->getOutputContent()
    ]);
    exit;
}


/*
|--------------------------------------------------------------------------
|   jQuery AJAX: atgriezīs norādītā lietotāja e-pasta adresi.
|--------------------------------------------------------------------------
|   (not sure, vai vēl kaut kur tiek izsaukta)
*/

if (isset($_GET['email']) && is_numeric($_GET['email'])) {

	$user = $db->get_row("
        SELECT `mail` FROM `users`
        WHERE `id` = " . (int) $_GET['email']
    );

	echo ($user) ? $user->mail : 'Nav norādīts!';
	exit;
}


/*
|--------------------------------------------------------------------------
|   jQuery AJAX: atgriezīs lappusi meklētāja rezultātiem.
|--------------------------------------------------------------------------
*/
if (isset($_GET['var1']) && $_GET['var1'] == 'page' &&
    isset($_GET['var2']) && isset($_POST['vip'])) {
    
    $page_nr = (int)$_GET['var2'];
    if ($page_nr < 1) $page_nr = 1;
    
    $new_tpl = fetch_tpl();
    if (empty($new_tpl)) { die('error'); }
    
    $new_tpl->newBlock('mcp-search-page');
    
    $total = $db->get_var("
        SELECT count(*) AS `total_count`
        FROM (
            SELECT count(*) AS `total` FROM `visits`
            JOIN `users` ON `visits`.`user_id` = `users`.`id`
            WHERE `visits`.`ip` LIKE '" . sanitize(trim($_POST['vip'])) . "' AND
            `users`.`deleted` = 0
            GROUP BY `user_id`
        ) AS `tbl`
    ");
    
    $page_count = ceil($total / $max_in_result_page);
    if ($page_count > $max_result_pages) $page_count = $max_result_pages;
    if ($page_nr > $page_count) $page_nr = $max_result_pages;
    $offset = ($page_nr - 1) * $max_in_result_page;
    
    $results = $db->get_results("
        SELECT
            `users`.`id`,
            `users`.`nick`,
            `users`.`level`,
            `users`.`lastip`,
            `users`.`mail`,
            `users`.`karma`,
            `users`.`date`,
            `visits`.`ip`
        FROM `visits`
            JOIN `users` ON `visits`.`user_id` = `users`.`id`
        WHERE `visits`.`ip` LIKE '" . sanitize(trim($_POST['vip'])) . "' AND
        `users`.`deleted` = 0
        GROUP BY `user_id`
        ORDER BY `users`.`level` DESC, `users`.`nick`
        LIMIT ".$offset.", ".$max_in_result_page."
    ");
    
    if ($results) {

		foreach ($results as $res) {

			$res->date = ceil((time() - strtotime($res->date)) / 60 / 60 / 24);			
			
			// ja laukā ļauts ievadīt "%", tos šeit vispirms izvāc,
			// lai varētu veikt pareizu aizstāšanu
            $escaped = str_replace('%', '', trim($_POST['vip']));
            $res->lastip = $res->ip;
            $tmp_ip = $res->lastip;
            $res->lastip = str_replace($escaped, '<strong>' . h($escaped) . '</strong>', h($res->lastip));
            $res->lastip = '<a href="https://whois.domaintools.com/'.h($tmp_ip).'" rel="nofollow">'.$res->lastip.'</a>';           
			
			$res->nick = usercolor($res->nick, $res->level, false, $res->id);
			$new_tpl->newBlock('search-page-result');
			$new_tpl->assignAll($res);
		}

        if ($page_count > 1) {
            $new_tpl->newBlock('search-page-list');
            for ($i = 1; $i <= $page_count; $i++) {
                $new_tpl->newBlock('search-list-page');
                $new_tpl->assign([
                    'page-nr' => $i,
                    'displayed' =>
                        ($i == $page_nr) ? '<strong>'.$i.'</strong>' : $i
                ]);
            }
        }
	}

    echo json_encode([
        'content' => $new_tpl->getOutputContent()
    ]);
    exit;
}


/*
|--------------------------------------------------------------------------
|   jQuery AJAX: atgriezīs datus par norādīto lietotāju.
|--------------------------------------------------------------------------
|
|   Ielādēta tiks šāda informācija:
|
| 	  - lietotāja vecie lietotājvārdi;
| 	  - profili, ar kuriem sakrīt paroles hash;
| 	  - banu termiņi;
|     - iepriekš izmantotās IP (noteikts skaits).
*/

if (isset($_GET['display']) && is_numeric($_GET['display'])) {

	$userid = (int) $_GET['display'];
	$content = '';

	$user = $db->get_row("
        SELECT `id`, `nick`, `level` FROM `users` WHERE `id` = ".$userid
    );
	if (!$user) {
		echo ''; exit;
	}

	// pārbauda, vai lietotājam ir aktīvs bans
	$ban = $db->get_row("
		SELECT
			`banned`.`reason`,
			`banned`.`length`,
			`banned`.`time`,
			`users`.`nick`
		FROM `banned`
			JOIN `users` ON `banned`.`author` = `users`.`id`
		WHERE
			`banned`.`user_id` = ".$user->id."
		ORDER BY
			`banned`.`time` DESC
	");
	if ($ban && (time() - $ban->time < $ban->length)) {
		$content .= '<p class="note note-findby">Bloķēts ar iemeslu: ' . $ban->reason . ' (' . $ban->nick . ')</p>';
	}

	// atrod pēdējās x lietotās IP
	$all_ips = $db->get_results("
		SELECT `visits`.`ip`, `visits`.`lastseen` FROM `visits`
		WHERE
			`visits`.`user_id` 	= '$user->id' AND
			`visits`.`ip`		!= ''
		ORDER BY
			`visits`.`lastseen` DESC
		LIMIT 0, $limit_total_ips
	");

	$unique_ips = $db->get_results("
		SELECT `visits`.`ip`, MAX(`visits`.`lastseen`) AS `lasttime` FROM `visits`
		WHERE
			`visits`.`user_id` 	= '$user->id' AND
			`visits`.`ip` 		!= ''
		GROUP BY `visits`.`ip`
		ORDER BY `lasttime` DESC
		LIMIT 0, $limit_total_ips
	");

	if ($all_ips || $unique_ips) {

		$counter = 1;

		$content .= '<div id="ip_block">';
		$content .= '<p class="note note-findby">Izmantotās IP:</p>';

		// visu atrasto IP stabiņš ar laiku, kad šī IP izmantota
		if ($all_ips) {
			$content .= '<table id="all_ips" class="ip-table">';
			$content .= '<tr><td>Pēdējās IP</td></tr>';
			foreach ($all_ips as $ip) {
			
				if (!empty($ip->ip) && $ip->ip != '--') {
					$ip->ip = '<a href="https://whois.domaintools.com/'.$ip->ip.'" rel="nofollow">'.$ip->ip.'</a>';
				}
				$row_class = ( $counter > $limit_shown_ips ) ? ' class="is-hidden"' : '';
								
				$content .= '<tr' . $row_class . '><td>' . $ip->ip . ' (pirms ' . time_ago(strtotime($ip->lastseen)) . ')</td></tr>';
				$counter++;
			}
			if ($counter - 1 > $limit_shown_ips) {
				$content .= '<tr><td>
					<a id="show_more_all" href="javascript:void();">Rādīt <span class="toggle-text">vairāk</span></a>
				</td></tr>';
			}
			$content .= '</table>';
		}

		$row_class = '';
		$counter = 1;

		// unikālo izmantoto IP stabiņš
		if ($unique_ips) {
			$content .= '<table id="unique_ips" class="ip-table">';
			$content .= '<tr><td>Unikālās IP</td></tr>';
			foreach ($unique_ips as $ip) {
			
				if (!empty($ip->ip) && $ip->ip != '--') {
					$ip->ip = '<a href="https://whois.domaintools.com/'.$ip->ip.'" rel="nofollow">'.$ip->ip.'</a>';
				}
				$row_class = ( $counter > $limit_shown_ips ) ? ' class="is-hidden"' : '';
				
				$content .= '<tr' . $row_class . '><td>' . $ip->ip . '</td></tr>';
				$counter++;
			}
			if ($counter - 1 > $limit_shown_ips) {
				$content .= '<tr><td>
					<a id="show_more_unique" href="javascript:void();">Rādīt <span class="toggle-text">vairāk</span></a>
				</td></tr>';
			}
			$content .= '</table>';
		}

		$content .= '</div>';
	} else {
		$content .= '<p class="note note-findby">Izmantotās IP:<br>Nav fiksētas!</p>';
	}

	// atradīs vecos lietotājvārdus
	$usernames = $db->get_results("
		SELECT
			`user_id` AS `id`,
			`nick`,
			`changed`
		FROM `nick_history`
		WHERE
			`user_id` = '" . $user->id . "'
		ORDER BY
			`changed` DESC
	");

	if ($usernames) {
		$content .= '<p class="note note-findby">Iepriekšējie lietotājvārdi:</p>';
        $content .= '<table id="mgmt_usernames">';
		foreach ($usernames as $uname) {
			$uname->changed = date("d.m.Y, H:i", strtotime($uname->changed));
            $content .= '<tr>';
            $content .=     '<td><a href="/user/'.$uname->id.'">'.$uname->nick.'</a></td>';
            $content .=     '<td>līdz: '.$uname->changed.'</td>';
            $content .= '</tr>';
		}
		$content .= '</table>';
	} else {
		$content .= '<p class="note note-findby" style="margin-bottom:10px">Šis lietotājs savu lietotājvārdu nav mainījis.</p>';
	}

	echo $content;
	exit;
}


/*
|--------------------------------------------------------------------------
|   Saturs ar ievades formām un meklēšanas rezultātiem.
|--------------------------------------------------------------------------
*/

$tpl->assignInclude('module-head', CORE_PATH . '/modules/' . $category->module . '/head.tpl');
$tpl->prepare();
$tpl->newBlock('mcp-profiles-tabs');
// otra sadaļa pieejama tikai pāris projektos
if ($lang == 0 || $lang == 1) {
    $tpl->newBlock('grouped-enabled');
}
$tpl->newBlock('mcp-find-outer-start');
$tpl->newBlock('mcp-find-profiles');

// kāda no formām aizpildīta un iesūtīta
if (isset($_POST['submit']) || isset($_GET['ip'])) {

	// pēc lietotājvārda
	if (isset($_POST['nick']) && strlen(trim($_POST['nick'])) >= 2) {
		$field = 'nick';
		$criteria = '`nick` LIKE \'%' . sanitize(trim($_POST['nick'])) . '%\'';
		$tpl->assign('nick', h(trim($_POST['nick'])));
	}    
	// pēc e-pasta
	else if (isset($_POST['mail']) && strlen(trim($_POST['mail'])) >= 3) {
		$field = 'mail';
		$criteria = '`mail` LIKE \'%' . sanitize(trim($_POST['mail'])) . '%\'';
		$tpl->assign('mail', h(trim($_POST['mail'])));
	}    
	// pēc pēdējās lietotās IP adreses
	else if (isset($_REQUEST['ip']) && strlen(trim($_REQUEST['ip'])) >= 3) {
		$field = 'ip';
		// šeit neder % zīme, jo POST['ip'] laukā to ir ļauts pielietot
		$criteria = '`lastip` LIKE \'' . sanitize(trim($_REQUEST['ip'])) . '\'';
		$tpl->assign('ip', h(trim($_REQUEST['ip'])));
	}    
	// pēc IP iekš 'visits'
	else if (isset($_POST['vip']) && strlen(trim($_POST['vip'])) >= 3) {
		$field = 'vip';
		// šeit neder % zīme, jo POST['vip'] laukā tā var tikt izmantota
		$criteria = '`visits`.`ip` LIKE \'' . sanitize(trim($_POST['vip'])) . '\'';
		$tpl->assign('vip', h(trim($_POST['vip'])));
	}
	// pēc user-agent
	else if (isset($_POST['useragent']) && strlen(trim($_POST['useragent'])) >= 5) {
		$field = 'useragent';
		// šeit neder % zīme, jo POST['useragent'] laukā tā var tikt izmantota
		$criteria = '`users`.`user_agent` LIKE \'' . sanitize(trim($_POST['useragent'])) . '\'';
		$tpl->assign('useragent', h(trim($_POST['useragent'])));
	}
	// kļūdu gadījumā
	else {
		$criteria = '1';
		$field = '';
	}
 
    $page_count = 1;

	// meklējot pēc IP, jāpiesaista `visits` tabula
	if ($field == 'vip') {

        $total = $db->get_var("
            SELECT count(*) AS `total_count`
            FROM (
                SELECT count(*) AS `total` FROM `visits`
                JOIN `users` ON `visits`.`user_id` = `users`.`id`
                WHERE " . $criteria . " AND
                `users`.`deleted` = 0
                GROUP BY `user_id`
            ) AS `tbl`
		");
        
        $page_count = ceil($total / $max_in_result_page);
        if ($page_count > $max_result_pages) $page_count = $max_result_pages;
        
		$results = $db->get_results("
			SELECT
				`users`.`id`,
				`users`.`nick`,
				`users`.`level`,
				`users`.`lastip`,
				`users`.`mail`,
				`users`.`karma`,
				`users`.`date`,
				`visits`.`ip`
			FROM `visits`
				JOIN `users` ON `visits`.`user_id` = `users`.`id`
			WHERE " . $criteria . " AND
            `users`.`deleted` = 0
			GROUP BY `user_id`
			ORDER BY `users`.`level` DESC, `users`.`nick`
			LIMIT 0, ".$max_in_result_page."
		");
	// pārējos gadījumos pietiek ar meklēšanu `users` tabulā
	} else {
		$results = $db->get_results("
			SELECT
				`id`, `nick`, `mail`, `lastip`, `karma`, `date`, `level`
			FROM `users`
			WHERE " . $criteria . " AND
            `users`.`deleted` = 0
			ORDER BY `level` DESC, `nick`
			LIMIT 0, ".$max_in_result_page
		);
	}

	if ($results) {

		$tpl->newBlock('search-results');
		if ($field == 'vip') {
			$tpl->assign('ip-type', 'Lietotā IP');
		} else {
			$tpl->assign('ip-type', 'Pēdējā IP');
		}

		foreach ($results as $res) {

			$res->date = ceil((time() - strtotime($res->date)) / 60 / 60 / 24);			
			
			// izceļ kādu no laukiem, ja pēc tāda tika veikta meklēšana;
			// ja laukā ļauts ievadīt "%", tos šeit vispirms izvāc,
			// lai varētu veikt pareizu aizstāšanu
			if ($field == 'vip') {
				$escaped = str_replace('%', '', trim($_POST['vip']));
				$res->lastip = $res->ip;
				$tmp_ip = $res->lastip;
				$res->lastip = str_replace($escaped, '<strong>' . h($escaped) . '</strong>', h($res->lastip));
				$res->lastip = '<a href="https://whois.domaintools.com/'.h($tmp_ip).'" rel="nofollow">'.$res->lastip.'</a>';
			} 
			else if ($field == 'mail') {
				$escaped = trim($_POST['mail']);
				$res->mail = str_replace($escaped, '<strong>' . h($escaped) . '</strong>', h($res->mail));
			} 
			else if ($field == 'ip') {
				$escaped = str_replace('%', '', trim($_REQUEST['ip']));
				$tmp_ip = $res->lastip;
				$res->lastip = str_replace($escaped, '<strong>' . h($escaped) . '</strong>', h($res->lastip));
				$res->lastip = '<a href="https://whois.domaintools.com/'.h($tmp_ip).'" rel="nofollow">'.$res->lastip.'</a>';
			}             
			
			$res->nick = usercolor($res->nick, $res->level, false, $res->id);
			$tpl->newBlock('search-result');
			$tpl->assignAll($res);
		}

        if ($field === 'vip' && $page_count > 1) {
            $tpl->newBlock('search-pages');
            for ($i = 1; $i <= $page_count; $i++) {
                $tpl->newBlock('search-page');
                $tpl->assign([
                    'page-nr' => $i,
                    'displayed' => ($i == 1) ? '<strong>'.$i.'</strong>' : $i
                ]);
            }
        }
	}
}

$tpl->newBlock('mcp-find-outer-end');
