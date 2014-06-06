<?php

/**
 * Saskaita tagus, vajadzīgs lai atlasītu noteiktu skaitu sākot no randomas pozīcijas
 * order by rand() strādā ļoti lēni
 *
 * @param type $db
 * @param type $lang
 * @return int Total tag count
 */
function tags_total($db, $lang) {

	return $db->get_var("SELECT count(*) FROM `tags`, `taged` WHERE `taged`.`tag_id` = `tags`.`id` AND `taged`.`lang` = '$lang' GROUP BY `tags`.`id`");

}

/**
 * Atgriež $count random tagus, kas ir izmantoti $lang lapās
 *
 * @param type $db
 * @param type $lang
 * @param type $count
 * @return array Random tagu saraksts
 */
function tags_random($db, $lang, $count) {

	return $db->get_results("
		SELECT
			`tags`.*
		FROM
			`tags`,
			`taged`
		WHERE
			`taged`.`tag_id` = `tags`.`id` AND
			`taged`.`lang` = '$lang'
		GROUP BY
		  `tags`.`id`
		LIMIT ".rand(0,tags_total($db, $lang)-$count).",".$count);

}

