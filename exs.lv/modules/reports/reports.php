<?php
/**
 * 	Moderatoru sadaļa, kurā aplūkojamas un pārvaldāmas
 * 	visas lietotāju iesniegtās sūdzības.
 *
 * 	Moduļa adrese: 	exs.lv/reports
 *
 *  Projekti un apakšprojekti, kuros sūdzības ir iespējotas:
 *    1 - exs.lv;
 *    7 - lol.exs.lv
 *    9 - runescape.exs.lv
 *
 *  Citi apakšprojekti jāpieraksta klāt katrā vietā,
 *  kur ziņošanas podziņa tiek vispār izdrukāta lapā.
 *  Tie ir visu veidu miniblogi, rakstu komentāri un galeriju komentāri.
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


/**
 *  Fancybox ar nosūdzētā komentāra saturu.
 *
 *  Adreses forma: /reports/show_content/{entry_id}?_=1
 */
if (isset($_GET['var1']) && $_GET['var1'] == 'show_content' &&
	isset($_GET['var2']) && isset($_GET['_'])) {

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
		echo json_encode(array(
			'state' => 'error',
			'message' => 'Kļūdaini iesniegts pieprasījums!'
		));
		exit;
	} else {

		$data->reported_content = ( empty($data->reported_content) ) ?
				'<p class="report-notice stronger">Nav saglabāts!</p>' :
				$data->reported_content;
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
			$original_data = $db->get_row("
                SELECT `text`, `edit_time`, `removed`
                FROM `comments`
                WHERE `id` = '" . (int) $data->entry_id . "'
            ");

		// galeriju komentāri
		} else if ($data->type == 2) {
			$original_data = $db->get_row("
                SELECT `text`, `edit_time`, `removed`
                FROM `galcom`
                WHERE `id` = '" . (int) $data->entry_id . "'
            ");

		// miniblogi
		} else {
			$original_data = $db->get_row("
                SELECT `text`, `edit_time`, `removed`
                FROM `miniblog`
                WHERE `id` = '" . (int) $data->entry_id . "'
            ");
		}

		if ($original_data) {

			// nosūdzētis ieraksts var būt dzēsts un lapā vairs nebūt redzams;
			// to moderatoram pieklātos redzēt
			if ($original_data->removed == 1) {
				$original = $original_data->text;
				$original_data->text = '<p class="report-notice stronger">';
				$original_data->text .= 'Ieraksts ir dzēsts!</p>';
				$original_data->text .= $original;
			}
			$templ->assign('original-post', add_smile($original_data->text));
		}

		echo json_encode(array(
			'state' => 'success',
			'message' => $templ->getOutputContent()
		));
	}
	exit;
}


/**
 *  Sūdzības arhivēšana.
 *
 *  Šo bloku izsauc jquery getJSON, kad nospiesta arhivēšanas/aktualizēšanas poga.
 *  Pieprasītā adrese ir formā /reports/remove/{report-id}?_=1
 */
if (isset($_GET['var1']) && $_GET['var1'] == 'remove' &&
	isset($_GET['var2']) && is_numeric($_GET['var2']) && isset($_GET['_'])) {

	// drošības labad arhivēt ļauts tikai attiecīgā apakšprojekta ierakstus
	$query_update = $db->query("
        UPDATE `reports`
        SET
            `archived`   = 1,
            `deleted_by` = '" . $auth->id . "',
            `deleted_at` = '" . time() . "'
        WHERE
            `id` = '" . (int) $_GET['var2'] . "' AND
            `site_id` = " . $lang . "
        LIMIT 1
    ");

	if (!$query_update) {
		echo json_encode(array('state' => 'error', 'text' => ''));
	} else {

		echo json_encode(array(
			'state' => 'success',
			'text' => 'Arhivēts'
		));
	}
	exit;
}


/**
 *  Saraksts ar iesniegtajām sūdzībām.
 */
// aktīvās cilnes izcelšana
$active_tab = 'miniblogs';
if (isset($_GET['var1']) && 
    in_array($_GET['var1'], array('miniblogs', 'articles', 'gallery-comments'))) {
	$active_tab = $_GET['var1'];
}
$tpl->assign('tab-' . $active_tab, ' class="active"');



/**
 *  Milzīgie SQL pieprasījumi.
 *
 *  Atkarīgi no tā, kura cilne tiek skatīta.
 */
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

// uzskaita tikai konkrētā apakšprojekta brīdinājumus
$includable_subquery = "(
    SELECT count(*) FROM `warns`
    WHERE
        `user_id`   = `rule_breaker`.`id` AND
        `active`    = 1 AND
        `site_id`   = $lang
    ) AS `warn_count`
";

// nosūdzēto miniblogu pieprasījums
if ($active_tab == 'miniblogs') {

	$report_types = array(0, 1);
	$query_reports_0 = false;
	$query_reports_1 = false;

	foreach ($report_types as $get_archive) {

		// arhivētajiem ierakstiem klāt nāk lietotājs-arhivētājs,
		// kā arī atgriežamo sūdzību skaits ir ierobežots;
		// iesniegto sūdzību nav tik daudz, lai vajadzētu limitēt
		$includable_join = '';
		$query_limit = '';

		if ($get_archive == 1) {
			$includable_selects .= ',
                `archived_by`.`id`		AS `archivator_id`,
                `archived_by`.`nick`	AS `archivator_nick`,
                `archived_by`.`level`	AS `archivator_level`
            ';
			$includable_join = " JOIN `users` AS `archived_by` ON `reports`.`deleted_by` = `archived_by`.`id` ";
			$query_limit = 'LIMIT 0, 30';
		}

		$query_reports_{$get_archive} = $db->get_results("
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
                `reports`.`archived` 	= $get_archive 	AND
                `reports`.`type` 		= 0				AND
                `reports`.`removed` 	= 0				AND
                `reports`.`site_id`		= $lang
            ORDER BY
                `reports`.`created_at` DESC
            $query_limit
        ");
	}

// nosūdzēto rakstu komentāru pieprasījums
} else if ($active_tab == 'articles') {

	$report_types = array(0, 1);
	$query_reports_0 = false;
	$query_reports_1 = false;

	foreach ($report_types as $get_archive) {

		// arhivētajiem ierakstiem klāt nāk lietotājs-arhivētājs,
		// kā arī atgriežamo sūdzību skaits ir ierobežots;
		// iesniegto sūdzību nav tik daudz, lai vajadzētu limitēt
		$includable_join = '';
		$query_limit = '';

		if ($get_archive == 1) {
			$includable_selects .= ',
                `archived_by`.`id`		AS `archivator_id`,
                `archived_by`.`nick`	AS `archivator_nick`,
                `archived_by`.`level`	AS `archivator_level`
            ';
			$includable_join = " JOIN `users` AS `archived_by` ON `reports`.`deleted_by` = `archived_by`.`id` ";
			$query_limit = 'LIMIT 0, 30';
		}

		$query_reports_{$get_archive} = $db->get_results("
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
                `reports`.`archived` 	= $get_archive 	AND
                `reports`.`type` 		= 1				AND
                `reports`.`removed` 	= 0				AND
                `reports`.`site_id`		= $lang
            ORDER BY
                `reports`.`created_at` DESC
            $query_limit
        ");
	}

// nosūdzēto galeriju komentāru pieprasījums
} else if ($active_tab == 'gallery-comments') {

	$report_types = array(0, 1);
	$query_reports_0 = false;
	$query_reports_1 = false;

	foreach ($report_types as $get_archive) {

		// arhivētajiem ierakstiem klāt nāk lietotājs-arhivētājs,
		// kā arī atgriežamo sūdzību skaits ir ierobežots;
		// iesniegto sūdzību nav tik daudz, lai vajadzētu limitēt
		$includable_join = '';
		$query_limit = '';

		if ($get_archive == 1) {
			$includable_selects .= ',
                `archived_by`.`id`		AS `archivator_id`,
                `archived_by`.`nick`	AS `archivator_nick`,
                `archived_by`.`level`	AS `archivator_level`
            ';
			$includable_join = " JOIN `users` AS `archived_by` ON `reports`.`deleted_by` = `archived_by`.`id` ";
			$query_limit = 'LIMIT 0, 30';
		}

		$query_reports_{$get_archive} = $db->get_results("
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
                `reports`.`archived` 	= $get_archive 	AND
                `reports`.`type` 		= 2				AND
                `reports`.`removed` 	= 0				AND
                `reports`.`site_id`		= $lang
            ORDER BY
                `reports`.`created_at` DESC
            $query_limit
        ");
	}
} else {
	redirect('/reports');
}



// vēl neskatītu iesniegumu skaits, kas tiek norādīts cilnēs;
// atlasa tikai aktīvā apakšprojekta ziņojumu skaitu
$new_mblogs = $db->get_var("SELECT count(*) FROM `reports` WHERE `type` = 0 AND `archived` = 0 AND `removed` = 0 AND `site_id` = $lang");
$new_articles = $db->get_var("SELECT count(*) FROM `reports` WHERE `type` = 1 AND `archived` = 0 AND `removed` = 0 AND `site_id` = $lang");
// tā kā bildes iespējams brutāli izdzēst no db, 
// tad šeit nepieciešama papildu pārbaude ar JOIN
$new_gcomments = $db->get_var("
    SELECT count(*) FROM `reports`
    JOIN `galcom` ON `reports`.`entry_id` = `galcom`.`id`
    WHERE `reports`.`type` = 2 AND `reports`.`archived` = 0 AND `reports`.`removed` = 0 AND `reports`.`site_id` = $lang
");

$tpl->assign(array(
	'count-mblogs' => ' (<span class="red">' . $new_mblogs . '</span>)',
	'count-articles' => ' (<span class="red">' . $new_articles . '</span>)',
	'count-gcomments' => ' (<span class="red">' . $new_gcomments . '</span>)'
));



// lapā viens virs otra drukājas divi saraksti;
// augšējais - nearhivētās sūdzības (0)
// apakšējais - arhivētās sūdzības (1)
$report_types = array(0, 1);

foreach ($report_types as $report_type) {

	$tpl->newBlock('report-list-container');

	// ja konkrētā veida sūdzības nav atrastas...
	if (!$query_reports_{$report_type}) {
		$tpl->newBlock('no-reports-found');
		if ($report_type == 1) {
			$tpl->assign('report-type', 'arhivētas sūdzības');
		} else {
			$tpl->assign('report-type', 'jaunas sūdzības');
		}

	// konkrētā veida sūdzības atrastas
	} else {

		// tabulas virsraksts
		$tpl->newBlock('list-reports');
		if ($report_type == 0) {
			$tpl->assign('report-title', 'Jaunākās sūdzības');
			$tpl->newBlock('archive-button-header');
		} else {
			$tpl->assign('report-title', 'Sūdzību arhīvs');
		}

		// saraksts ar sūdzībām
		foreach ($query_reports_{$report_type} as $report) {

			$report->report_created_at = display_time($report->report_created_at);

			// sūdzības iesūtītājs
			$report->reporter_nick = usercolor($report->reporter_nick, $report->reporter_level);
			$report->reporter_nick = '<a href="/user/' . $report->reporter_id . '">' . $report->reporter_nick . '</a>';

			// pārkāpuma veicējs
			$report->rule_breaker_nick = usercolor($report->rule_breaker_nick, $report->rule_breaker_level);
			$report->rule_breaker_nick = '<a href="/user/' . $report->rule_breaker_id . '">' . $report->rule_breaker_nick . '</a>';

			// ziņojuma arhivētājs
			if ($report_type == 1) {
				$report->archivator_nick = usercolor($report->archivator_nick, $report->archivator_level);
				$report->archivator_nick = '<a href="/user/' . $report->archivator_id . '">' . $report->archivator_nick . '</a>';
			}


			//	adrese uz pārkāpuma izdarīšanas vietu;
			//	tiek norādīta tabulā pie katra ieraksta
			switch ($report->report_type) {

				// raksta komentārs (vai komentāra atbilde)
				case 1:
					$report_place = '<span class="stronger">Komentārs: </span>';
					$report_place .= '<a href="/read/' . $report->comment_page_strid . '#c' . $report->comment_id . '">' . $report->comment_page_title . '</a>';
					break;

				// galerijas komentārs
				case 2:
					$report_place = '<span class="stronger">Komentārs: </span> ';
					$report_place .= '<a href="/gallery/' . $report->gallery_author . '/' . $report->galcom_bid . '#c' . $report->galcom_id . '">' . $report->galcom_id . '</a>';
					break;

				// minibloga tipa ieraksts (var būt arī grupā)
				default:

					// junk komentārs
					if ($report->miniblog_type == 'junk' && $report->miniblog_parent != 0) {
						$report_place = '<span class="stronger">Junk komentārs: </span>';
						$report_place .= '<a href="/junk/' . $report->miniblog_parent . '#m' . $report->miniblog_id . '">#m' . $report->miniblog_id . '</a>';

					// minibloga komentārs
					} else if ($report->miniblog_parent != '0') {

						$mb_strid = mb_get_strid($report->parentmb_text, $report->parentmb_id);

						// grupā esošs komentārs
						if ($report->miniblog_groupid != '0') {
							$report_place = '<span class="stronger">Grupas mb komentārs: </span>';
							$report_place .= '<a href="/group/' . $report->parentmb_groupid . '/forum/' . base_convert($report->parentmb_id, 10, 36) . '#m' . $report->miniblog_id . '">' . $report->group_title . '</a>';

						// ārpus grupām esošs komentārs
						} else {
							$report_place = '<span class="stronger">Minibloga komentārs: </span>';
							$report_place .= '<a href="/say/' . $report->parentmb_author . '/' . $report->parentmb_id . '-' . $mb_strid . '#m' . $report->miniblog_id . '">' . $mb_strid . '</a>';
						}

					// pats miniblogs
					} else {

						$mb_strid = mb_get_strid($report->miniblog_text, $report->miniblog_id);

						// grupā esošs miniblogs
						if ($report->miniblog_groupid != '0') {
							$report_place = '<span class="stronger">Grupas miniblogs: </span>';
							$report_place .= '<a href="/group/' . $report->miniblog_groupid . '/forum/' . base_convert($report->miniblog_id, 10, 36) . '">' . $report->group_title . '</a>';

						// ārpus grupām esošs miniblogs
						} else {
							$report_place = '<span class="stronger">Miniblogs: </span>';
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

			// arhivēšanas poga
			if ($report_type == 0) {
				$tpl->newBlock('archive-button');
			}

			$tpl->assign('report_id', $report->report_id);

			// ziņojumu arhīva tabulā parādīs lietotāju, kurš ziņojumu arhivējis
			if ($report_type == 1) {
				$tpl->newBlock('archived-by');
				$tpl->assign('archivator_nick', $report->archivator_nick);
			}
		}
	}
}
