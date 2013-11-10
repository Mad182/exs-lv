<?php
/**	
 *	Moderatoru sadaļa, kurā aplūkojamas un pārvaldāmas
 *	visas lietotāju iesniegtās sūdzības.
 *
 *	Moduļa adrese: 		exs.lv/reports
 *	Pēdējās izmaiņas: 	10.10.2013 ( Edgars )
 */
 
//	ne-moderatorus sūtām prom
if ( !im_mod() ) {
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
	
	if ( isset($_GET['url']) )
		redirect('/reports/'.$_GET['url']);
	else
		redirect('/reports');
	exit;
}


// aktīvā cilne
$active_tab 	= 'miniblogs';
if ( isset($_GET['var1']) ) {
	if ($_GET['var1'] == 'articles') 				$active_tab = 'articles';
	else if ($_GET['var1'] == 'gallery-comments') 	$active_tab = 'gallery-comments';
	else if ($_GET['var1'] == 'gallery-images') 	$active_tab = 'gallery-images';
	else $active_tab = 'miniblogs';
}
$tpl->assign('tab-' . $active_tab, ' class="active"');

// vai skatīt arhivētās sūdzības?
$query_where_field = "`reports`.`archived` = '0' AND ";
$query_limit = '';
if ( isset($_GET['archive']) ) {
	$query_where_field = "`reports`.`archived` = '1' AND ";
	$query_limit = ' LIMIT 0, 50';
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
	`rule_breaker`.`level`		AS `rule_breaker_level`,
	`rule_breaker`.`warn_count`	AS `warn_count`
';
$includable_subquery = '
	(SELECT count(*) FROM `warns` WHERE `user_id` = `rule_breaker`.`id` AND `active` = \'1\') AS `warn_count`
';

//	arhivātora (moderatora) datu lauki
/*if ( isset($_GET['archive']) ) {
	$includable_sel = ',
		`archived_by`.`id`		AS `archivator_id`,
		`archived_by`.`nick`	AS `archivator_nick`,
		`archived_by`.`level`	AS `archivator_level`
	';
	$includable_join = ' JOIN `users` AS `archived_by` ON `reports`.`deleted_by` = `users`.`id` ';
} else {
	$includable_sel 	= '';
	$includable_join 	= '';
}*/
$includable_sel 	= '';
$includable_join 	= '';

// miniblogi
if ( $active_tab == 'miniblogs' ) {
	$query_reports = $db->get_results("
		SELECT			
			$includable_selects,
			
			`miniblog`.`text` 			AS `miniblog_text`,
			`miniblog`.`id`				AS `miniblog_id`,
			`miniblog`.`author`			AS `miniblog_author`,
			`miniblog`.`parent`			AS `miniblog_parent`,
			
			`parent_mb`.`id` 			AS `parentmb_id`,
			`parent_mb`.`author` 		AS `parentmb_author`,
			`parent_mb`.`text` 			AS `parentmb_text`,
			`parent_mb`.`groupid`		AS `parentmb_groupid`,
			
			$includable_subquery
			$includable_sel

		FROM `reports`	
			JOIN `users` 	AS `reporter` 		ON `reports`.`created_by` 	= `reporter`.`id`
			JOIN `miniblog` 					ON `reports`.`entry_id` 	= `miniblog`.`id`	
			JOIN `users` 	AS `rule_breaker` 	ON `miniblog`.`author` 		= `rule_breaker`.`id`
			JOIN `miniblog` AS `parent_mb` 		ON `miniblog`.`parent` 		= `parent_mb`.`id`
			$includable_join
		WHERE
			$query_where_field
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
			`comments`.`text` 	AS `comment_text`,
			`comments`.`id`		AS `comment_id`,			
			`pages`.`strid`		AS `comment_page_strid`,
			`pages`.`title`		AS `comment_page_title`,
			$includable_subquery
			$includable_sel

		FROM `reports`	
			JOIN `users` AS `reporter` 		ON `reports`.`created_by` 	= `reporter`.`id`
			JOIN `comments` 				ON `reports`.`entry_id` 	= `comments`.`id`
			JOIN `users` AS `rule_breaker` 	ON `comments`.`author` 		= `rule_breaker`.`id`
			JOIN `pages` 					ON `comments`.`pid` 		= `pages`.`id`
			$includable_join
		WHERE	
			$query_where_field
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
			
			`galcom`.`text` 			AS `galcom_text`,
			`galcom`.`id`				AS `galcom_id`,
			`galcom`.`author`			AS `galcom_author`,
			
			$includable_subquery
			$includable_sel

		FROM `reports`	
			JOIN `users` 	AS `reporter` 		ON `reports`.`created_by` 	= `reporter`.`id`
			JOIN `galcom` 						ON `reports`.`entry_id` 	= `galcom`.`id`	
			JOIN `users` 	AS `rule_breaker` 	ON `galcom`.`author` 		= `rule_breaker`.`id`
			$includable_join
		WHERE
			$query_where_field
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
				$report_place 	.= '<a href="/gallery/'.$report->rule_breaker_id.'#c'.$report->galcom_id.'">'.$report->galcom_id.'</a>';
				break;
			// galerijas attēls kā tāds
			case 3:
				$report_place = '<strong>Attēls: </strong> ';
				break;
			// miniblogs (var būt arī grupā)
			default:
				$mb_strid = mb_get_strid($report->parentmb_text,$report->parentmb_id);			
				
				if ( $report->parentmb_groupid != 0 ) {
					$report_place 	 = '<strong>Grupas miniblogs: </strong>';
					$report_place 	.= '<a href="/group/'.$report->parentmb_groupid.'/forum/'.base_convert($report->parentmb_id, 10, 36).'#m'.$report->miniblog_id.'">'.$mb_strid.'</a>';
				} else {
					$report_place 	 = '<strong>Miniblogs: </strong>';
					$report_place 	.= '<a href="/say/'.$report->parentmb_author.'/'.$report->miniblog_parent.'-'.$mb_strid.'#m'.$report->miniblog_id.'">'.$mb_strid.'</a>';
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
			/*$tpl->newBlock('archived-by');
			$tpl->assign('archived-by', $report->archivator_nick);*/
			$tpl->newBlock('activation-button');
		}
		$tpl->assign('report_id', $report->report_id);
		$tpl->assign('addr', $active_tab . ( isset($_GET['archive']) ? '/?archive' : '') );
	}
	
}

