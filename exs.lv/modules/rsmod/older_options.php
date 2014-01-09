<?php

/**
 * 	??? Kaut kāds garais, apjomīgais datubāzes pieprasījums rakstu salīdzināšanai divās tabulās.
 * 	TODO: uzlabot, izveidojot vienu pieprasījumu.
 *
 * 	+ vēl citas dev opts, kurām kuram katram nav jāpiekļūst
 */
if ($_GET['var1'] == 'dev' && $auth->id == 115) {

	$count_pages = $db->get_var("SELECT count(*) FROM `pages` WHERE `category` = '100' ");
	$count_rshelp = $db->get_var("SELECT count(*) FROM `rs_help` WHERE `cat` = '100' ");

	echo $count_pages . ' : ' . $count_rshelp . '<br>';

	$quests = $db->get_results("
		SELECT 
			`pages`.`id`,
			`pages`.`strid`,
			`pages`.`title`,
			`pages`.`author`
		FROM 
			`pages`
		WHERE 
			`category` IN(100,102,99,193) 
		ORDER BY `date` DESC 
		LIMIT 0,20
	");
	if ($quests) {
		$counter = 1;
		foreach ($quests as $quest) {

			$get = $db->get_row("SELECT `id` FROM `rs_help` WHERE `page_id` = '" . $quest->id . "' ");
			if (!$get) {

				echo '<strong>' . $counter . '.</strong> <a href="/read/' . $quest->strid . '">' . $quest->title . '</a><br>';
				$counter++;
			}
		}
		echo '<br><br>';
	}
} else if ($_GET['var1'] == 'ready' && $auth->id == 115) {
	$q = $db->query("UPDATE `rs_help` SET `ready` = '0' ");
} else if ($_GET['var1'] == 'questlist' && $auth->id == 115) {

	$pages = $db->get_results("SELECT `id`,`title`,`strid`,`author` FROM `pages` WHERE `category` in ('99','100') ORDER BY `title` ASC");
	foreach ($pages as $page) {
		echo $page->title . '<br>';
	}
	exit;
} else if (isset($_GET['insert']) && $auth->id == 115) {

	$pages = $db->get_results("SELECT `id`,`title`,`strid`,`author` FROM `pages` WHERE `category` = '195' ");
	foreach ($pages as $page) {
		$ins = $db->query("INSERT INTO `rs_help` (cat,page_id,title,strid,auth) VALUES (
		  '195',
		  '" . $page->id . "',
		  '" . sanitize($page->title) . "',
		  '" . sanitize($page->strid) . "',
		  '" . (int) $page->author . "'
		) ");
	}
}