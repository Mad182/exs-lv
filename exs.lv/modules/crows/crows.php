<?php
/**	
 *	Pārskatāms pēdējo likto brīdinājumu saraksts
 *	un ar tiem saistīta informācija.
 *
 *	Moduļa adrese: 	exs.lv/crows
 */

// ne-moderatorus sūtām prom
if ( !im_mod() ) {
	set_flash('Error 403: Permission denied!');
	redirect();
}

$tpl_options = 'no-right';
if ( $lang == 9 ) {
    $tpl_options = 'no-left';
}

// ārpus galvenā exa sarakstā rādīsies tikai atvērtā apakšprojekta brīdinājumi
$where = '';
if ( $lang != 1 ) {
    $where = " WHERE `warns`.`site_id` = $lang ";
}


$warns = $db->get_results("
	SELECT
		`warns`.`reason`		AS `warn_reason`,
		`warns`.`active`		AS `warn_active`,
		`warns`.`remove_reason`	AS `warn_removal_reason`,
		`warns`.`removed_by`	AS `warn_removed_by`,
		`warns`.`created`		AS `warn_created_at`,
		
		`offender`.`id`			AS `offender_id`,
		`offender`.`nick`		AS `offender_nick`,
		`offender`.`level`		AS `offender_level`,
		
		`warned_by`.`id`		AS `creator_id`,
		`warned_by`.`nick`		AS `creator_nick`,
		`warned_by`.`level`		AS `creator_level`,
		
		IFNULL(`removed_by`.`id`,0) AS `removed_id`,
		`removed_by`.`nick` 	AS `removed_nick`,
		`removed_by`.`level` 	AS `removed_level`
		
	FROM `warns`
		JOIN `users` AS `offender` 			ON `warns`.`user_id` 	= `offender`.`id`
		JOIN `users` AS `warned_by` 		ON `warns`.`created_by` = `warned_by`.`id`
		LEFT JOIN `users` AS `removed_by` 	ON `warns`.`removed_by` = `removed_by`.`id`
    $where
	ORDER BY 
		`warns`.`created` DESC 
	LIMIT 0,100
");
if ( !$warns ) {
	$tpl->newBlock('no-warns-found');
}
else {

	$counter = 1;

	$tpl->newBlock('warns-list');
	
	foreach ($warns as $warn) {
	
		$warn->warn_created_at = display_time_simple( strtotime($warn->warn_created_at) );
	
		// sodītais lietotājs
		$warn->offender_nick = usercolor($warn->offender_nick, $warn->offender_level);
		$warn->offender_nick = '<a href="'.mkurl('user', $warn->offender_id, $warn->offender_nick).'">'.$warn->offender_nick.'</a>';

		// soda uzlicējs
		$warn->creator_nick = usercolor($warn->creator_nick, $warn->creator_level);
		$warn->creator_nick = '<a href="'.mkurl('user', $warn->creator_id, $warn->creator_nick).'">'.$warn->creator_nick.'</a>';
		
		// ja kāds brīdinājumu noņēmis, 
		// tiek apstrādāts noņēmēja niks
		if ( $warn->removed_id != '0' ) {
			$warn->removed_nick = usercolor($warn->removed_nick, $warn->removed_level);
			$warn->removed_nick = '<a href="'.mkurl('user', $warn->removed_id, $warn->removed_nick).'">'.$warn->removed_nick.'</a>';
		}
		else $warn->removed_nick = '';
		
		// pārveido iemeslos norādītās adreses, ja tās nāk no apakšprojekta
		if ( strpos($warn->warn_reason, 'http://rp.exs.lv') !== false ) {
			$warn->warn_reason = str_replace('href="/', 'href="http://rp.exs.lv/', $warn->warn_reason);
		}
		else if ( strpos($warn->warn_reason, 'http://runescape.exs.lv') !== false ) {
			$warn->warn_reason = str_replace('href="/', 'href="http://runescape.exs.lv/', $warn->warn_reason);
		}
		
		$tpl->newBlock('single-warn');	
		$tpl->assignAll($warn);
		$tpl->assign('row_counter', $counter);
		
		// ja brīdinājums ticis noņemts... parāda noņēmēju un noņemšanas iemeslu
		if ( $warn->removed_id != '0' ) {
		
			$warn->warn_removal_reason = '<strong>Noņemšanas iemesls:</strong> ( '.$warn->removed_nick.' ) '.$warn->warn_removal_reason;
		
			$tpl->assign('removal-reason', $warn->warn_removal_reason);
			$tpl->assign('removed-warn', ' class="removed_warn" title="Brīdinājums noņemts!"');
		}
		
		$counter++;		
	}
}
