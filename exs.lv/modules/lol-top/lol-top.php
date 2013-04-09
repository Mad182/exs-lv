<?php

$top_players = $db->get_results("
	SELECT
		DISTINCT(`lol_tracking`.`player_id`) as `player_id`,
		`lol_players`.`lol_nick` as `lol_nick`,
		`lol_players`.`server` as `server`,
		`lol_players`.`user_id` as `user_id`,
		`lol_tracking`.`lks` as `lks`
	FROM
		`lol_players`,
		`lol_tracking`
	WHERE
		`lol_players`.`id` = `lol_tracking`.`player_id` AND
		`lol_tracking`.`date` = (SELECT MAX(`date`) FROM `lol_tracking`)
	ORDER BY
		`lol_tracking`.`lks` DESC
");

if(!empty($top_players)) {
	$tpl->newBlock('lol-top-full');
	$i = 1;
	foreach($top_players as $plr) {
	
		$usr = get_user($plr->user_id);
		$plr->nick = usercolor($usr->nick, $usr->level, false, $usr->id);
		$plr->id = $usr->id;
		$plr->i = $i++;
	
		$tpl->newBlock('lol-top-full-node');
		$tpl->assignAll($plr);
	}
}
