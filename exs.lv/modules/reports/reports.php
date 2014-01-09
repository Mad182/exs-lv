<?php

/** 	
 * 	Moderatoru sadaļa, kurā aplūkojamas un pārvaldāmas
 * 	visas lietotāju iesniegtās sūdzības.
 *
 * 	Moduļa adrese: 	exs.lv/reports
 */
$allowed_reports = array('miniblogs', 'articles', 'gallery-comments');

/**
 *  Projekti un apakšprojekti, kuros sūdzības ir iespējotas:
 *    1 - exs.lv; 
 *    7 - lol.exs.lv
 *    9 - runescape.exs.lv
 *
 *  Citi apakšprojekti jāpieraksta klāt katrā vietā, 
 *  kur ziņošanas podziņa tiek vispār izdrukāta lapā.
 */
$allowed_sites = array(1, 7, 9);


// apakšprojekti, kuros ir slēgtās grupas;
// citos projektos nav nepieciešams atsevišķi aplūkot nosūdzētā ieraksta saturu
$has_groups = array(1, 9);


/**
 *  Turpmāk izmantotie apzīmējumi:
 *
 * 	0 - miniblogs (pats mb, mb komentārs, junk komentārs, ieraksti grupā)
 * 	1 - raksta komentārs (arī komentāru atbildes; /read sadaļā)
 * 	2 - galerijas attēla komentārs
 */
//	sūtām prom ne-moderatorus un tos, kas sadaļu atver caur neiespējotiem projektiem
if (!im_mod() || !in_array($lang, $allowed_sites)) {
	set_flash('Pieeja liegta!');
	redirect();
}



//	atvērs fancybox, kurā tiks parādīts nosūdzētā komentāra saturs;
//	adreses forma: /reports/show_content/{entry_id}?_=1
if (isset($_GET['var1']) && $_GET['var1'] == 'show_content' && isset($_GET['var2']) && isset($_GET['_'])) {

	// šāda iespēja nepieciešama tikai tajos apakšprojektos,
	// kuros ir slēgtās grupas un kur kāds komentārs var nebūt redzams
	if (!in_array($lang, $has_groups)) {
		redirect('/reports');
		exit;
	}

	// pēc padotā ID meklē datubāzē ierakstu no konkrētā apakšprojekta
	$data = $db->get_row("
		SELECT 
			`reports`.`reported_content`,
			`reports`.`type`,
			`reports`.`entry_id`
		FROM `reports` 
		WHERE 
			`reports`.`removed` = 0 AND
			`reports`.`site_id` = " . $lang . " AND
			`reports`.`id` 		= '" . (int) $_GET['var2'] . "'
	");

	if (!$data) {
		echo json_encode(array('state' => 'error', 'message' => 'Kļūdaini iesniegts pieprasījums!'));
		exit;
	} else {

		$data->reported_content = ( empty($data->reported_content) ) ? '<p class="report-notice"><strong>Nav saglabāts!</strong></p>' : $data->reported_content;
		$data->reported_content = add_smile($data->reported_content);

		$templ = new TemplatePower(CORE_PATH . '/modules/reports/reported-content.tpl');
		$templ->prepare();
		$templ->newBlock('reported-content');
		$templ->assignAll($data);

		// lai varētu salīdzināt nosūdzēto ierakstu ar tagadējo, ja tas labots,
		// atlasa no datubāzes esošo saturu;
		// šeit nepārbauda, vai, piemēram, moderatoram ir pieeja ierakstam, kas atrodas
		// slēgtā grupā, jo no moderatora ierakstus slēpt nav jēgas
		// rakstu komentāri
		if ($data->type == 1) {
			$original_data = $db->get_row("SELECT `text`, `edit_time`, `removed` FROM `comments` WHERE `id` = '" . (int) $data->entry_id . "' ");
		}
		// galeriju komentāri
		else if ($data->type == 2) {
			$original_data = $db->get_row("SELECT `text`, `edit_time`, `removed` FROM `galcom` WHERE `id` = '" . (int) $data->entry_id . "' ");
		}
		// miniblogi
		else {
			$original_data = $db->get_row("SELECT `text`, `edit_time`, `removed` FROM `miniblog` WHERE `id` = '" . (int) $data->entry_id . "' ");
		}

		if ($original_data) {

			// nosūdzētis ieraksts var būt dzēsts un lapā vairs nebūt redzams;
			// to moderatoram pieklātos redzēt
			if ($original_data->removed == 1) {
				$original_data->text = '<p class="report-notice"><strong>Ieraksts ir dzēsts!</strong></p>' . $original_data->text;
			}
			$templ->assign('original-post', add_smile($original_data->text));
		}

		echo json_encode(array('state' => 'success', 'message' => $templ->getOutputContent()));
	}
	exit;
}



// šo bloku izsauc jquery getJSON, kad nospiesta arhivēšanas/aktualizēšanas poga;
// pieprasītā adrese ir formā /reports/{remove|activate}/{report-id}?_=1
if (isset($_GET['var1']) && ($_GET['var1'] == 'remove' || $_GET['var1'] == 'activate') &&
		isset($_GET['var2']) && is_numeric($_GET['var2']) && isset($_GET['_'])) {

	$swap_to = ($_GET['var1'] == 'remove') ? 1 : 0;

	// drošības labad arhivēt/aktualizēt ļauts tikai attiecīgā apakšprojekta ierakstus
	$query_update = $db->query("UPDATE `reports` SET `archived` = '$swap_to', `deleted_by` = '" . $auth->id . "', `deleted_at` = '" . time() . "' WHERE `id` = '" . (int) $_GET['var2'] . "' AND `site_id` = " . $lang . " LIMIT 1");

	if (!$query_update) {
		echo json_encode(array('state' => 'error', 'href' => '', 'text' => ''));
	} else {
		// atkarībā no tā, kāds statuss ziņojumam tika pielikts, atgriež atbilstošo pogu
		if ($swap_to == 1) {

			echo json_encode(array(
				'state' => 'success',
				'href' => '/reports/activate/' . (int) $_GET['var2'],
				'text' => 'Aktualizēt'
			));
		} else {

			echo json_encode(array(
				'state' => 'success',
				'href' => '/reports/remove/' . (int) $_GET['var2'],
				'text' => 'Arhivēt'
			));
		}
	}
	exit;
}




// aktīvās cilnes izcelšana
$active_tab = 'miniblogs';
if (isset($_GET['var1']) && in_array($_GET['var1'], $allowed_reports)) {
	$active_tab = $_GET['var1'];
}
$tpl->assign('tab-' . $active_tab, ' class="active"');


// vai skatīt arhivētās sūdzības?
$query_limit = ( isset($_GET['archive']) ) ? ' LIMIT 0, 30' : '';
$get_archived = ( isset($_GET['archive']) ) ? 1 : 0;

// SELECT lauki, kas izmantoti visos pieprasījumos
$includable_selects = '
	`reports`.`id` 				AS `report_id`,
	`reports`.`type`			AS `report_type`,
	`reports`.`entry_id`		AS `report_entry_id`,
	`reports`.`comment`			AS `report_comment`,
	`reports`.`reported_content` AS `reported_content`,
	`reports`.`created_at`		AS `report_created_at`,
	`reports`.`archived`		AS `report_archived`,
	
	`reporter`.`id`				AS `reporter_id`,
	`reporter`.`nick`			AS `reporter_nick`,
	`reporter`.`level`			AS `reporter_level`,
	
	`rule_breaker`.`id`			AS `rule_breaker_id`,
	`rule_breaker`.`nick`		AS `rule_breaker_nick`,
	`rule_breaker`.`level`		AS `rule_breaker_level`
';
$includable_join = '';
if (isset($_GET['archive'])) {
	$includable_selects .= ',
		`archived_by`.`id`		AS `archivator_id`,
		`archived_by`.`nick`	AS `archivator_nick`,
		`archived_by`.`level`	AS `archivator_level`
	';
	$includable_join = " JOIN `users` AS `archived_by` ON `reports`.`deleted_by` = `archived_by`.`id` ";
}
// uzskaita tikai konkrētā apakšprojekta brīdinājumus
$includable_subquery = " 
	(SELECT count(*) FROM `warns` WHERE `user_id` = `rule_breaker`.`id` AND `active` = '1' AND `site_id` = $lang) AS `warn_count`
";

// miniblogi
if ($active_tab == 'miniblogs') {
	$query_reports = $db->get_results("
		SELECT			
			$includable_selects,
			$includable_subquery,
			
			`miniblog`.`id`				AS `miniblog_id`,			
			`miniblog`.`author`			AS `miniblog_author`,
			`miniblog`.`text` 			AS `miniblog_text`,
			`miniblog`.`parent`			AS `miniblog_parent`,
			`miniblog`.`groupid`		AS `miniblog_groupid`,
			`miniblog`.`type`			AS `miniblog_type`,
			
			`parent_mb`.`id` 			AS `parentmb_id`,
			`parent_mb`.`author` 		AS `parentmb_author`,
			`parent_mb`.`text` 			AS `parentmb_text`,
			`parent_mb`.`groupid`		AS `parentmb_groupid`,
			
			`clans`.`title`				AS `group_title`

		FROM `reports` 
			$includable_join 
			JOIN `miniblog` 							ON `reports`.`entry_id` 	= `miniblog`.`id`
			JOIN `users` 			AS `reporter` 		ON `reports`.`created_by` 	= `reporter`.`id`				
			JOIN `users` 			AS `rule_breaker` 	ON `miniblog`.`author` 		= `rule_breaker`.`id`
			LEFT JOIN `miniblog` 	AS `parent_mb` 		ON `miniblog`.`parent` 		= `parent_mb`.`id`
			LEFT JOIN `clans`							ON `miniblog`.`groupid`		= `clans`.`id`
		WHERE
			`reports`.`archived` 	= '$get_archived' 	AND
			`reports`.`type` 		= '0'				AND
			`reports`.`removed` 	= '0'				AND
			`reports`.`site_id`		= $lang
		ORDER BY 
			`reports`.`created_at` DESC
		$query_limit
	");
}
// rakstu komentāri
else if ($active_tab == 'articles') {
	$query_reports = $db->get_results("
		SELECT			
			$includable_selects,
			$includable_subquery,
			
			`comments`.`text` 	AS `comment_text`,
			`comments`.`id`		AS `comment_id`,			
			`pages`.`strid`		AS `comment_page_strid`,
			`pages`.`title`		AS `comment_page_title`

		FROM `reports`	
			$includable_join 
			JOIN `users` AS `reporter` 		ON `reports`.`created_by` 	= `reporter`.`id`
			JOIN `comments` 				ON `reports`.`entry_id` 	= `comments`.`id`
			JOIN `users` AS `rule_breaker` 	ON `comments`.`author` 		= `rule_breaker`.`id`
			JOIN `pages` 					ON `comments`.`pid` 		= `pages`.`id`
		WHERE	
			`reports`.`archived` 	= '$get_archived' 	AND
			`reports`.`type` 		= '1'				AND
			`reports`.`removed` 	= '0'				AND
			`reports`.`site_id`		= '$lang'
		ORDER BY 
			`reports`.`created_at` DESC
	");
}
// galeriju komentāri
else if ($active_tab == 'gallery-comments') {
	$query_reports = $db->get_results("
		SELECT			
			$includable_selects,
			$includable_subquery,
			
			`galcom`.`text` 			AS `galcom_text`,
			`galcom`.`id`				AS `galcom_id`,
			`galcom`.`author`			AS `galcom_author`,
			`galcom`.`bid`				AS `galcom_bid`,
			`gallery_author`.`id`		AS `gallery_author`

		FROM `reports`
			$includable_join 
			JOIN `galcom` 						ON `reports`.`entry_id` 	= `galcom`.`id`
			JOIN `users` 	AS `reporter` 		ON `reports`.`created_by` 	= `reporter`.`id`				
			JOIN `users` 	AS `rule_breaker` 	ON `galcom`.`author` 		= `rule_breaker`.`id`
			JOIN `images`						ON `galcom`.`bid`			= `images`.`id`
			JOIN `users`	AS `gallery_author`	ON `images`.`uid`			= `gallery_author`.`id`
		WHERE
			`reports`.`archived` 	= '$get_archived' 	AND
			`reports`.`type` 		= '2'				AND
			`reports`.`removed` 	= '0'				AND
			`reports`.`site_id`		= '$lang'
		ORDER BY 
			`reports`.`created_at` DESC
		$query_limit
	");
} else {
	redirect('/reports');
}


// vēl neskatītu iesniegumu skaits, kas tiek norādīts cilnēs;
// atlasa tikai aktīvā apakšprojekta ziņojumu skaitu
$new_mblogs = $db->get_var("SELECT count(*) FROM `reports` WHERE `type` = '0' AND `archived` = '0' AND `removed` = '0' AND `site_id` = $lang ");
$new_articles = $db->get_var("SELECT count(*) FROM `reports` WHERE `type` = '1' AND `archived` = '0' AND `removed` = '0' AND `site_id` = $lang ");
$new_gcomments = $db->get_var("SELECT count(*) FROM `reports` WHERE `type` = '2' AND `archived` = '0' AND `removed` = '0' AND `site_id` = $lang ");

$tpl->assign(array(
	'count-mblogs' => ' (<span class="red">' . $new_mblogs . '</span>)',
	'count-articles' => ' (<span class="red">' . $new_articles . '</span>)',
	'count-gcomments' => ' (<span class="red">' . $new_gcomments . '</span>)'
));

// sadaļas virsrakstā to norāda, ja skatīti tiek arhivētie ziņojumi
if (isset($_GET['archive'])) {
	$tpl->assign('is_archive', ' (arhīvs)');
}

// arhivēto ziņojumu pogu parāda tikai tad, ja skatītas tiek aktīvās sūdzības
if (!isset($_GET['archive'])) {
	$tpl->newBlock('view-archived-reports');
	$tpl->assign(array(
		'archive-active' => '',
		'archive-addr' => str_replace('/?archive', '', $_SERVER['REQUEST_URI']) . '/?archive'
	));
}


// jauni ziņojumi nav atrasti
if (!$query_reports) {
	$tpl->newBlock('no-reports-found');
	if (isset($_GET['archive'])) {
		$tpl->assign('report-type', 'arhivētas sūdzības');
	} else {
		$tpl->assign('report-type', 'iesniegtas sūdzības');
	}
}
// atrasti jauni ziņojumi
else {

	$tpl->newBlock('list-reports');

	foreach ($query_reports as $report) {

		$report->report_created_at = display_time_simple($report->report_created_at);

		// sūdzības iesūtītājs
		$report->reporter_nick = usercolor($report->reporter_nick, $report->reporter_level);
		$report->reporter_nick = '<a href="' . mkurl('user', $report->reporter_id, $report->reporter_nick) . '">' . $report->reporter_nick . '</a>';

		// pārkāpuma veicējs
		$report->rule_breaker_nick = usercolor($report->rule_breaker_nick, $report->rule_breaker_level);
		$report->rule_breaker_nick = '<a href="' . mkurl('user', $report->rule_breaker_id, $report->rule_breaker_nick) . '">' . $report->rule_breaker_nick . '</a>';

		// ziņojuma arhivētājs
		if (isset($_GET['archive'])) {
			$report->archivator_nick = usercolor($report->archivator_nick, $report->archivator_level);
			$report->archivator_nick = '<a href="' . mkurl('user', $report->archivator_id, $report->archivator_nick) . '">' . $report->archivator_nick . '</a>';
		}


		//	adrese uz pārkāpuma izdarīšanas vietu;
		//	tiek norādīta tabulā pie katra ieraksta
		switch ($report->report_type) {

			// raksta komentārs (vai komentāra atbilde)
			case 1:
				$report_place = '<strong>Komentārs: </strong>';
				$report_place .= '<a href="/read/' . $report->comment_page_strid . '#c' . $report->comment_id . '">' . $report->comment_page_title . '</a>';
				break;

			// galerijas komentārs
			case 2:
				$report_place = '<strong>Komentārs: </strong> ';
				$report_place .= '<a href="/gallery/' . $report->gallery_author . '/' . $report->galcom_bid . '#c' . $report->galcom_id . '">' . $report->galcom_id . '</a>';
				break;

			// minibloga tipa ieraksts (var būt arī grupā)
			default:

				// junk komentārs
				if ($report->miniblog_type == 'junk' && $report->miniblog_parent != 0) {
					$report_place = '<strong>Junk komentārs: </strong>';
					$report_place .= '<a href="/junk/' . $report->miniblog_parent . '#m' . $report->miniblog_id . '">#m' . $report->miniblog_id . '</a>';
				}
				// minibloga komentārs
				else if ($report->miniblog_parent != '0') {

					$mb_strid = mb_get_strid($report->parentmb_text, $report->parentmb_id);

					// grupā esošs komentārs
					if ($report->miniblog_groupid != '0') {
						$report_place = '<strong>Grupas mb komentārs: </strong>';
						$report_place .= '<a href="/group/' . $report->parentmb_groupid . '/forum/' . base_convert($report->parentmb_id, 10, 36) . '#m' . $report->miniblog_id . '">' . $report->group_title . '</a>';
					}
					// ārpus grupām esošs komentārs
					else {
						$report_place = '<strong>Minibloga komentārs: </strong>';
						$report_place .= '<a href="/say/' . $report->parentmb_author . '/' . $report->parentmb_id . '-' . $mb_strid . '#m' . $report->miniblog_id . '">' . $mb_strid . '</a>';
					}
				}
				// pats miniblogs
				else {

					$mb_strid = mb_get_strid($report->miniblog_text, $report->miniblog_id);

					// grupā esošs miniblogs
					if ($report->miniblog_groupid != '0') {
						$report_place = '<strong>Grupas miniblogs: </strong>';
						$report_place .= '<a href="/group/' . $report->miniblog_groupid . '/forum/' . base_convert($report->miniblog_id, 10, 36) . '">' . $report->group_title . '</a>';
					}
					// ārpus grupām esošs miniblogs
					else {
						$report_place = '<strong>Miniblogs: </strong>';
						$report_place .= '<a href="/say/' . $report->miniblog_author . '/' . $report->miniblog_id . '-' . $mb_strid . '">' . $mb_strid . '</a>';
					}
				}
				break;
		};

		// izvade lapā		
		$tpl->newBlock('single-report');
		$tpl->assignAll($report);
		$tpl->assign(array(
			'report-place' => $report_place,
			'report-comment' => textlimit($report->report_comment, 600),
			'full-content' => $report->report_comment
		));

		// parādīs podziņu, kas ļaus apskatīt pilno ziņojuma saturu
		if (mb_strlen($report->report_comment) > 600) {
			$tpl->newBlock('show-full-content');
		}

		// skatīt saturu nepieciešams tikai tajos apakšprojektos,
		// kuros nav slēgto grupu
		if (in_array($lang, $has_groups)) {
			$tpl->newBlock('display-original-content');
			$tpl->assign('report_id', $report->report_id);
		}

		// arhivēšanas/aktualizēšanas poga
		if (!isset($_GET['archive'])) {
			$tpl->newBlock('archive-button');
		} else {
			$tpl->newBlock('activation-button');
		}
		$tpl->assign('report_id', $report->report_id);
		$tpl->assign('addr', $active_tab . ( isset($_GET['archive']) ? '/?archive' : ''));

		// skatot ziņojumu arhīvu, parādīs lietotāju, kurš ziņojumu arhivējis
		if (isset($_GET['archive'])) {
			$tpl->newBlock('archived-by');
			$tpl->assign('archivator_nick', $report->archivator_nick);
		}
	}
}

