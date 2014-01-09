<?php

/**
 *  Sinhronizē RuneScape rakstu informāciju
 *  starp `pages` un `rs_pages` tabulām.
 *
 *  Funkcija tiek izsaukta pie raksta atjaunošanas
 *  vai dzēšanas RuneScape apakšprojektā.
 *
 *  @param  bool  norāda, vai uz ekrāna drukāt info par saistītajiem rakstiem
 */
function update_rspages($update = true, $print = false) {
	global $db, $auth;

	// pieprasījumos 102 - /kvestu-pamacibas, 599 - /runescape
	// atlasa rakstus, kuri ir iekš `rs_pages`, bet nav iekš `pages`;
	// šie raksti ir dzēsti/pārvietoti
	$select_old = $db->get_results("
        SELECT
            `rs_pages`.`id`             AS `rspages_id`,
            `rs_pages`.`category_id`    AS `rspages_catid`,
            IFNULL(`pages`.`id`, 0)     AS `pages_id`,
            `pages`.`strid`             AS `pages_strid`,
            `pages`.`title`             AS `pages_title`,
            `pages`.`category`          AS `pages_catid`,
            `cat`.`parent`              AS `category_parent`
        FROM `rs_pages`
            LEFT JOIN `pages` ON `rs_pages`.`page_id` = `pages`.`id`
            LEFT JOIN `cat` ON `pages`.`category` = `cat`.`id`
        WHERE
            `rs_pages`.`deleted_by` = 0 AND
            (`pages`.`id` IS NULL OR
            `cat`.`parent` NOT IN(599, 102) OR
            `rs_pages`.`category_id` != `pages`.`category`)
        ORDER BY `pages`.`title` ASC
    ");
	if ($select_old) {

		$counter = 1;

		foreach ($select_old as $old) {

			// raksts `pages` tabulā neeksistē
			if ($old->pages_id == '0') {
				if ($print) {
					$msg = $counter . '. Raksts neeksistē! (rspages.id: ' . $old->rspages_id . '';
					$msg .= ', rspages.cat: ' . $old->rspages_catid . ')<br>';
					echo $msg;
				}
				if ($update) {
					$db->query("UPDATE `rs_pages` SET `deleted_by` = '" . $auth->id . "', `deleted_at` = '" . time() . "' WHERE `id` = '" . $old->rspages_id . "' LIMIT 1");
				}
			}
			// raksts `pages` tabulā vairs nav derīgā kategorijā
			// (piemēram, ir dzēsts)
			elseif ($old->category_parent != 599 && $old->category_parent != 102) {
				if ($print) {
					$msg = $counter . '. Raksts nelāgā kategorijā! (pages.id: ' . $old->pages_id . '';
					$msg .= ', pages.cat: ' . $old->pages_catid . ') - ' . $old->pages_title . '<br>';
					echo $msg;
				}
				if ($update) {
					$db->query("UPDATE `rs_pages` SET `deleted_by` = '" . $auth->id . "', `deleted_at` = '" . time() . "' WHERE `id` = '" . $old->rspages_id . "' LIMIT 1");
				}
			}
			// rakstam `pages` tabulā ir mainījusies kategorija
			// (tomēr derīga rs kategorija)
			elseif ($old->rspages_catid != $old->pages_catid) {
				if ($print) {
					$msg = $counter . '. Rakstam mainīta kategorija! (pages.id: ' . $old->pages_id;
					$msg .= ', rspages.cat: ' . $old->rspages_catid;
					$msg .= ', pages.cat: ' . $old->pages_catid . ') - ' . $old->pages_title . '<br>';
					echo $msg;
				}
				if ($update) {
					$db->query("UPDATE `rs_pages` SET `category_id` = '" . (int) $old->pages_catid . "', `updated_by` = '" . $auth->id . "', `updated_at` = '" . time() . "' WHERE `id` = '" . $old->rspages_id . "' LIMIT 1");
				}
			}
			$counter++;
		}
	}
	if ($print) {
		echo '<br><br><br>';
	}

	// atlasa rakstus, kuri ir iekš `pages`, bet nav iekš `rs_pages`;
	// šos rakstus ieraksta arī `rs_pages`;
	$select_new = $db->get_results("
        SELECT
            `pages`.`id`            AS `pages_id`,
            `pages`.`category`      AS `pages_catid`,
            `pages`.`strid`         AS `pages_strid`,
            `pages`.`title`         AS `pages_title`,
            IFNULL(`rs_pages`.`id`, 0)  AS `rspages_id`,            
            `rs_pages`.`category_id`    AS `rspages_catid`,
            `cat`.`parent`              AS `category_parent`
        FROM `pages`
            JOIN `cat` ON `pages`.`category` = `cat`.`id`
            LEFT JOIN `rs_pages` ON (
                `pages`.`id`            = `rs_pages`.`page_id` AND
                `pages`.`category`      = `rs_pages`.`category_id` AND
                `rs_pages`.`deleted_by` = 0
            )
        WHERE
            `cat`.`parent` IN(599, 102) AND
            `rs_pages`.`id` IS NULL
        ORDER BY `pages`.`title` ASC
    ");
	if ($select_new) {

		$counter = 1;

		foreach ($select_new as $old) {

			// šeit vairs nav jēgas pārbaudīt, vai `rs_pages` sadaļā ir mainīta kategorija vai kas tāds,
			// kategoriju salīdzināšana jau notikusi iepriekšējā pieprasījumā
			// raksts `rs_pages` tabulā neeksistē
			if ($old->rspages_id == '0') {
				if ($print) {
					$msg = $counter . '. Raksts neeksistē! (pages.id: ' . $old->pages_id . '';
					$msg .= ', pages.cat: ' . $old->pages_catid . ') - ' . $old->pages_title . '<br>';
					echo $msg;
				}
				if ($update) {
					$db->query("INSERT INTO `rs_pages` (page_id, category_id, created_by, created_at) VALUES ('" . (int) $old->pages_id . "', '" . (int) $old->pages_catid . "', '" . $auth->id . "', '" . time() . "') ");
				}
			}
			$counter++;
		}
	}
	if ($print) {
		exit;
	}
}

/**
 *  Ar Memcache saglabā un atgriež kvestu statistikas datus.
 *
 *  Funkcija tiek izsaukta, caur MOD sadaļu rediģējot rs pamācības.
 *
 *  @param  bool  norāde, vai atjaunināt memcache glabāto saturu
 */
function get_quests_stats($force = false) {
	global $db, $m;

	$stats = false;

	if ($force || ($stats = $m->get('quests-stats')) === false) {

		// izlaisto kvestu skaits noteiktos gados
		$stats[14] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `year` = '14' AND `category_id` IN (99,100) ");
		$stats[13] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `year` = '13' AND `category_id` IN (99,100) ");
		$stats[12] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `year` = '12' AND `category_id` IN (99,100) ");
		$stats[11] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `year` = '11' AND `category_id` IN (99,100) ");
		$stats[10] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `year` = '10' AND `category_id` IN (99,100) ");
		$stats['older'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `year` NOT IN ('12','11','10','09','08') AND `category_id` IN (99,100) ");

		// kvestu tips
		$stats['p2p'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `category_id` = 100 ");
		$stats['f2p'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `category_id` = 99 ");
		$stats['miniquests'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `category_id` = 193 ");

		// kvestu sarežģītība
		$stats['special'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `difficulty` = 6 AND `category_id` IN (99,100) ");
		$stats['grandmaster'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `difficulty` = 5 AND `category_id` IN (99,100) ");
		$stats['master'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `difficulty` = 4 AND `category_id` IN (99,100) ");
		$stats['intermediate'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `difficulty` = 3 AND `category_id` IN (99,100) ");
		$stats['easy'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `difficulty` = 2 AND `category_id` IN (99,100) ");
		$stats['novice'] = $db->get_var("SELECT count(*) FROM `rs_pages` WHERE `deleted_by` = 0 AND `difficulty` = 1 AND `category_id` IN (99,100) ");

		$m->set('quests-stats', $stats, false, 3600);
	}

	return $stats;
}
