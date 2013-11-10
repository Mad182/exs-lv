<?php
/**	
 *	Moderatoru sadaļa, kurā aplūkojamas un pārvaldāmas
 *	visas lietotāju iesniegtās sūdzības.
 *
 *	Moduļa adrese: 		exs.lv/reports
 *	Pēdējās izmaiņas: 	10.10.2013 ( Edgars )
 */

/**
 *	0 - miniblogs (gan komentārs, gan pats mb; arī grupā)
 *	1 - raksta komentārs (/read sadaļā)
 *	2 - galerijas attēla komentārs
 *
 *	--- zemāk esošie vēl nav ieviesti
 *	3 - galerijas attēls
 *	4 - raksts kā tāds
 *	5 - junk bilde
 */
$allowed_reports = array('miniblogs', 'articles', 'gallery-comments');

 
//	ne-moderatorus sūtām prom;
//	sadaļai piekļuve ļauta tikai exs.lv lietotājiem
if ( !im_mod() || $lang != 1 ) {
	set_flash('Pieeja liegta!');
	redirect();
	exit;
}
$tpl_options = 'no-right';


//	esošas sūdzības arhivēšana/aktualizēšana
if ( isset($_GET['var1']) && ($_GET['var1'] == 'remove' || $_GET['var1'] == 'activate') && 
	 isset($_GET['var2']) && is_numeric($_GET['var2']) ) {

	$swap_to = ($_GET['var1'] == 'remove') ? 1 : 0;		
	
	$query_update = $db->query("UPDATE `reports` SET `archived` = '$swap_to', `deleted_by` = '".$auth->id."', `deleted_at` = '".time()."' WHERE `id` = '".(int)$_GET['var2']."' LIMIT 1");
	
	if ( !$query_update ) {
		if ($swap_to == 1)
			set_flash('Sūdzību neizdevās arhivēt!');
		else
			set_flash('Sūdzību neizdevās aktualizēt!');
	}
	else {
		if ($swap_to == 1)
			set_flash('Iesniegtā sūdzība arhivēta!');
		else
			set_flash('Iesniegtā sūdzība aktualizēta!');
	}
	
	//	"url" parametrs nebūs padots vienīgi tad,
	//	ja kāds to speciāli adresē nodzēsīs
	if ( isset($_GET['url']) )
		redirect('/reports/'.$_GET['url']);
	else
		redirect('/reports');
	exit;
}


// aktīvā cilne
$active_tab = 'miniblogs';
if ( isset($_GET['var1']) && in_array($_GET['var1'], $allowed_reports) ) {
	$active_tab = $_GET['var1'];
}
$tpl->assign('tab-' . $active_tab, ' class="active"');


// vai skatīt arhivētās sūdzības?
$query_limit 	= ( isset($_GET['archive']) ) ? ' LIMIT 0, 50' : '';
$get_archived 	= ( isset($_GET['archive']) ) ? 1 : 0;
if ( isset($_GET['archive']) ){
	$tpl->assign('archive-active', 'active');
}


// SELECT lauki, kas izmantoti visos pieprasījumos
$includable_selects = '
	`reports`.`id` 				AS `report_id`,
	`reports`.`type`			AS `report_type`,
	`reports`.`entry_id`		AS `report_entry_id`,
	`reports`.`comment`			AS `report_comment`,
	`reports`.`created_at`		AS `report_created_at`,
	
	`reporter`.`id`				AS `reporter_id`,
	`reporter`.`nick`			AS `reporter_nick`,
	`reporter`.`level`			AS `reporter_level`,
	
	`rule_breaker`.`id`			AS `rule_breaker_id`,
	`rule_breaker`.`nick`		AS `rule_breaker_nick`,
	`rule_breaker`.`level`		AS `rule_breaker_level`
';
$includable_subquery = " 
	(SELECT count(*) FROM `warns` WHERE `user_id` = `rule_breaker`.`id` AND `active` = '1' AND `site_id` = '1') AS `warn_count`
";

// miniblogi
if ( $active_tab == 'miniblogs' ) {
	$query_reports = $db->get_results("
		SELECT			
			$includable_selects,
			$includable_subquery,
			
			`miniblog`.`id`				AS `miniblog_id`,			
			`miniblog`.`author`			AS `miniblog_author`,
			`miniblog`.`text` 			AS `miniblog_text`,
			`miniblog`.`parent`			AS `miniblog_parent`,
			`miniblog`.`groupid`		AS `miniblog_groupid`,
			
			`parent_mb`.`id` 			AS `parentmb_id`,
			`parent_mb`.`author` 		AS `parentmb_author`,
			`parent_mb`.`text` 			AS `parentmb_text`,
			`parent_mb`.`groupid`		AS `parentmb_groupid`,
			
			`clans`.`title`				AS `group_title`

		FROM `reports` 
			JOIN `miniblog` 							ON `reports`.`entry_id` 	= `miniblog`.`id`
			JOIN `users` 			AS `reporter` 		ON `reports`.`created_by` 	= `reporter`.`id`				
			JOIN `users` 			AS `rule_breaker` 	ON `miniblog`.`author` 		= `rule_breaker`.`id`
			LEFT JOIN `miniblog` 	AS `parent_mb` 		ON `miniblog`.`parent` 		= `parent_mb`.`id`
			LEFT JOIN `clans`							ON `miniblog`.`groupid`		= `clans`.`id`
		WHERE
			`reports`.`archived` 	= '$get_archived' AND
			`reports`.`type` 		= '0'	
		ORDER BY 
			`reports`.`created_at` DESC
		$query_limit
	");
}
// rakstu komentāri
else if ( $active_tab == 'articles' ) {
	$query_reports = $db->get_results("
		SELECT			
			$includable_selects,
			$includable_subquery,
			
			`comments`.`text` 	AS `comment_text`,
			`comments`.`id`		AS `comment_id`,			
			`pages`.`strid`		AS `comment_page_strid`,
			`pages`.`title`		AS `comment_page_title`

		FROM `reports`	
			JOIN `users` AS `reporter` 		ON `reports`.`created_by` 	= `reporter`.`id`
			JOIN `comments` 				ON `reports`.`entry_id` 	= `comments`.`id`
			JOIN `users` AS `rule_breaker` 	ON `comments`.`author` 		= `rule_breaker`.`id`
			JOIN `pages` 					ON `comments`.`pid` 		= `pages`.`id`
		WHERE	
			`reports`.`archived` 	= '$get_archived' AND
			`reports`.`type` 		= '1'		
		ORDER BY 
			`reports`.`created_at` DESC
	");
}
// galeriju komentāri
else if ( $active_tab == 'gallery-comments' ) {
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
			JOIN `galcom` 						ON `reports`.`entry_id` 	= `galcom`.`id`
			JOIN `users` 	AS `reporter` 		ON `reports`.`created_by` 	= `reporter`.`id`				
			JOIN `users` 	AS `rule_breaker` 	ON `galcom`.`author` 		= `rule_breaker`.`id`
			JOIN `images`						ON `galcom`.`bid`			= `images`.`id`
			JOIN `users`	AS `gallery_author`	ON `images`.`uid`			= `gallery_author`.`id`
		WHERE
			`reports`.`archived` 	= '$get_archived' AND
			`reports`.`type` 		= '2'	
		ORDER BY 
			`reports`.`created_at` DESC
		$query_limit
	");
}
else {
	redirect('/reports');
}


// vēl neskatītu iesniegumu skaits, kas tiek norādīts cilnēs
$new_mblogs		= $db->get_var("SELECT count(*) FROM `reports` WHERE `type` = '0' AND `archived` = '0' ");
$new_articles	= $db->get_var("SELECT count(*) FROM `reports` WHERE `type` = '1' AND `archived` = '0' ");
$new_gcomments	= $db->get_var("SELECT count(*) FROM `reports` WHERE `type` = '2' AND `archived` = '0' ");

$tpl->assign(array(
	'count-mblogs' 		=> ' (<span class="red">'.$new_mblogs.'</span>)',
	'count-articles' 	=> ' (<span class="red">'.$new_articles.'</span>)',
	'count-gcomments' 	=> ' (<span class="red">'.$new_gcomments.'</span>)'
));

//	sadaļas virsrakstā to norāda, ja skatīti tiek arhivētie ziņojumi
$tpl->assign('archive-addr', str_replace('/?archive', '', $_SERVER['REQUEST_URI']) . '/?archive');
if ( isset($_GET['archive']) ) {
	$tpl->assign('is_archive', ' (arhīvs)');
}

if ( !$query_reports ){
	$tpl->newBlock('no-reports-found');
}
else {
	
	$tpl->newBlock('list-reports');
	
	foreach ($query_reports as $report) {
	
		$report->report_created_at = display_time_simple( $report->report_created_at );
	
		// sūdzības iesūtītājs
		$report->reporter_nick = usercolor($report->reporter_nick, $report->reporter_level);		
		$report->reporter_nick = '<a href="'.mkurl('user', $report->reporter_id, $report->reporter_nick).'">'.$report->reporter_nick.'</a>';
		
		// pārkāpuma veicējs
		$report->rule_breaker_nick = usercolor($report->rule_breaker_nick, $report->rule_breaker_level);
		$report->rule_breaker_nick = '<a href="'.mkurl('user', $report->rule_breaker_id, $report->rule_breaker_nick).'">'.$report->rule_breaker_nick.'</a>';
		
		// ziņojuma arhivētājs
		/*if ( isset($_GET['archive']) ) {
			$report->archivator_nick = usercolor($report->archivator_nick, $report->archivator_level);
			$report->archivator_nick = '<a href="'.mkurl('user', $report->archivator_id, $report->archivator_nick).'">'.$report->archivator_nick.'</a>';
		}*/
		
		
		//	adrese uz pārkāpuma izdarīšanas vietu;
		//	tiek norādīta tabulā pie katra ieraksta
		switch ($report->report_type) {
		
			// raksta komentārs
			case 1:
				$report_place 	 = '<strong>Komentārs: </strong>';
				$report_place 	.= '<a href="/read/'.$report->comment_page_strid.'#c'.$report->comment_id.'">'.$report->comment_page_title.'</a>';
				break;
				
			// galerijas komentārs
			case 2:
				$report_place = '<strong>Komentārs: </strong> ';
				$report_place 	.= '<a href="/gallery/'.$report->gallery_author.'/'.$report->galcom_bid.'#c'.$report->galcom_id.'">'.$report->galcom_id.'</a>';
				break;

			// miniblogs (var būt arī grupā)
			default:
			
				// minibloga komentārs
				if ( $report->miniblog_parent != '0' ) {
				
					$mb_strid = mb_get_strid($report->parentmb_text,$report->parentmb_id);			
				
					// grupā esošs komentārs
					if ( $report->miniblog_groupid != '0' ) {
						$report_place 	 = '<strong>Grupas mb komentārs: </strong>';
						$report_place 	.= '<a href="/group/'.$report->parentmb_groupid.'/forum/'.base_convert($report->parentmb_id, 10, 36).'#m'.$report->miniblog_id.'">'.$report->group_title.'</a>';
					} 
					// ārpus grupām esošs komentārs
					else {
						$report_place 	 = '<strong>Minibloga komentārs: </strong>';
						$report_place 	.= '<a href="/say/'.$report->parentmb_author.'/'.$report->parentmb_id.'-'.$mb_strid.'#m'.$report->miniblog_id.'">'.$mb_strid.'</a>';
					}
				}
				// pats miniblogs
				else {
				
					$mb_strid = mb_get_strid($report->miniblog_text,$report->miniblog_id);
					
					// grupā esošs miniblogs
					if ( $report->miniblog_groupid != '0' ) {
						$report_place 	 = '<strong>Grupas miniblogs: </strong>';
						$report_place 	.= '<a href="/group/'.$report->miniblog_groupid.'/forum/'.base_convert($report->miniblog_id, 10, 36).'">'.$report->group_title.'</a>';
					} 
					// ārpus grupām esošs miniblogs
					else {
						$report_place 	 = '<strong>Miniblogs: </strong>';
						$report_place 	.= '<a href="/say/'.$report->miniblog_author.'/'.$report->miniblog_id.'-'.$mb_strid.'">'.$mb_strid.'</a>';
					}
				}
				break;
		};
	
		// izvade lapā
		$tpl->newBlock('single-report');
		$tpl->assignAll($report);
		$tpl->assign('report-place', $report_place);
		
		// arhivēšanas/aktualizēšanas poga
		if ( !isset($_GET['archive']) ) {
			$tpl->newBlock('archive-button');			
		} else {
			$tpl->newBlock('activation-button');
		}
		$tpl->assign('report_id', $report->report_id);
		$tpl->assign('addr', $active_tab . ( isset($_GET['archive']) ? '/?archive' : '') );
	}
	
}

