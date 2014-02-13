<?php

/**
 * 	Publiskās RuneScape kvestu pamācību sadaļas
 */
!isset($sub_include) and die('No hacking, pls.');


/**
 *  Kvestu sākumlapa ar to sērijām un statistikas datiem.
 */
if ($category->textid == 'kvestu-pamacibas') {

	// izdrukā lapā ievadtekstu par kvestiem kā tādiem
	$tpl->newBlock('quests-intro');

	// bildes adrese nav ielikta templeitā,
	// jo citās kvestu sadaļās tajā pašā vietā būs jau cits attēls
	$tpl->assign('intro-image', '/bildes/runescape/intro/khazard.png');

	// no datubāzes atlasa visas pievienotās kvestu sērijas un
	// katrai no tām piesaistītos rakstus;
	// pieprasījumā 99 un 100 ir attiecīgi kvestu kategoriju id
	$series = $db->get_results("
        SELECT
            `rs_classes`.`id`           AS `series_id`,
            `rs_classes`.`title`        AS `series_title`,
            `rs_classes`.`img`          AS `series_img`,
            IFNULL(`pages`.`category`, 0) AS `category_id`,
            `pages`.`title`             AS `page_title`,
            `pages`.`strid`             AS `page_strid`,
            `rs_pages`.`is_placeholder` AS `is_placeholder`
        FROM `rs_classes`
            LEFT JOIN `rs_pages` ON (
                `rs_classes`.`id`       = `rs_pages`.`class_id` AND
                `rs_pages`.`deleted_by` = 0               
            )
            LEFT JOIN `pages` ON (
                `rs_pages`.`is_placeholder` = 0 AND
                `rs_pages`.`page_id`        = `pages`.`id` AND
                `pages`.`category` IN (99, 100)
            )
        WHERE
            `rs_classes`.`category` = 'series'
        ORDER BY
            ABS(`rs_classes`.`ordered`) ASC,
            `rs_pages`.`ordered` ASC
    ");
	if ($series) {

		$tpl->newBlock('quests-series');
		$temp_series = 0; // ciklā fiksē ejošo sērijas id
		$series_count = 0;

		foreach ($series as $single) {

			// izveido jaunu sēriju, ja nesakrīt pieglabātais id
			if ($single->series_id != $temp_series) {
				$tpl->newBlock('single-series');
				$tpl->assignAll($single);

				$series_count++;
				$temp_series = $single->series_id;

				// ik pēc 4 sērijām pārlec uz jaunu rindu
				if ($series_count > 1 && ($series_count - 1) % 4 == 0) {
					$tpl->assign('newline', ' style="clear:left"');
				}
			}

			// pievieno sērijai visus tai piesaistītos kvestus,
			// ja tādi ir atrasti
			// eksistējošs raksts `pages` tabulā
			if ($single->category_id != '0') {

				$quest_addr = '<a href="/read/' . $single->page_strid . '" title="' . $single->page_title . '">' . $single->page_title . '</a>';
			}
			// raksts vēl neeksistē, bet tam ir izveidots placeholderis @ `rs_pages`
			elseif ($single->is_placeholder == 1) {
				$quest_addr = '<a href="#">' . $single->page_title . '</a>';
			}

			$tpl->newBlock('series-quest');
			$tpl->assign('page_title', $quest_addr);
		}
	}

	// kvestu statistika, fakti un nepieciešamās prasmes
	$tpl->newBlock('quests-outro');

	// kvestu statistika
	$stats = get_quests_stats();
	if ($stats) {
		$tpl->newBlock('quests-stats');
		$tpl->assign(array(
			'2014' => $stats[14],
			'2013' => $stats[13],
			'2012' => $stats[12],
			'2011' => $stats[11],
			'2010' => $stats[10],
			'older' => $stats['older'],
			'p2p' => $stats['p2p'],
			'f2p' => $stats['f2p'],
			'miniquests' => $stats['miniquests'],
			'special' => $stats['special'],
			'grandmaster' => $stats['grandmaster'],
			'master' => $stats['master'],
			'intermediate' => $stats['intermediate'],
			'easy' => $stats['easy'],
			'novice' => $stats['novice']
		));
	}

	// kvestu fakti
	$tpl->newBlock('quests-facts');

	// nepieciešamās prasmes, lai izietu visus kvestus
	$skills = $db->get_results("SELECT * FROM `rs_qskills` ORDER BY `skill` ASC");
	if ($skills) {
		$tpl->newBlock('max-skills');
		foreach ($skills as $skill) {
			$tpl->newBlock('skill-requirement');
			$tpl->assignAll($skill);
		}
	}
}


/**
 *  Pay-to-play kvesti
 */ 
elseif ($category->textid == 'p2p-kvesti') {

	// izdrukā lapā ievadtekstu par kvestiem kā tādiem
	$tpl->newBlock('quests-intro');

	// bildes adrese nav ielikta templeitā,
	// jo citās kvestu sadaļās tajā pašā vietā būs jau cits attēls
	$tpl->assign('intro-image', '/bildes/runescape/intro/vampyre-juvinate.png');

	$p2p_quests = $db->get_results("
		SELECT 
			`pages`.`id`            AS `page_id`,
			`pages`.`strid`         AS `page_strid`,
			`pages`.`title`         AS `page_title`,
			`pages`.`author`        AS `page_author`,
            IFNULL(`rs_pages`.`is_old`, 0) AS `rspages_old`
		FROM `pages`
            LEFT JOIN `rs_pages` ON (
                `pages`.`id`                = `rs_pages`.`page_id` AND
                `rs_pages`.`deleted_by`     = 0 AND
                `rs_pages`.`is_placeholder` = 0
            )
		WHERE 
			`pages`.`category` = '100'
		ORDER BY `pages`.`title` ASC 
	");

	if ($p2p_quests) {

		$tpl->newBlock('p2p-quests');

		// kvesti tiek kategorizēti pēc alfabēta burtiem;
		// mainīgais fiksē ejošo burtu
		$letter = '';

		foreach ($p2p_quests as $data) {

			$tpl->newBlock('p2p-quest');
			$tpl->assignAll($data);

			// atlasa datus par raksta autoru
			$author = '';
			if ($user = get_user($data->page_author)) {
				$author = '<a style="font-size:11px;" href="' . mkurl('user', $user->id, $user->nick) . '">' . usercolor($user->nick, $user->level) . '</a>';
			}
			$tpl->assign('page-author', $author);

			// ja nepieciešams, pārmaina fiksēto burtu
			if (substr($data->page_title, 0, 1) != $letter) {
				$letter = substr($data->page_title, 0, 1);
				$tpl->assign(array(
					'letter' => '<b>' . $letter . '</b>',
					'border' => ' class="border"',
				));
			}

			// ja raksts ir novecojis, parāda info, ka to būtu vēlams atjaunot
			if ($data->rspages_old != '0') {

				$title = ($data->rspages_old == 1) ?
						'Pamācībai nepieciešamas jaunākas, labākas kvalitātes bildes!' :
						'Pamācību nepieciešams atjaunināt!';
				$picture = ($data->rspages_old == 1) ? 'info_yellow_sm.png' : 'info_red_sm.png';

				$tpl->assign('warning', '<img class="warning_small" src="/bildes/runescape/' . $picture . '" title="' . $title . '" alt="">');
			}
		}
	}

	/*
	  $placeholders = $db->get_results("SELECT `title` FROM `rs_placeholders` WHERE `cat` = '100' ORDER BY `title` ASC");
	  if ($placeholders) {
	  $tpl->newBlock('questlist-placeholders');
	  foreach ($placeholders as $ph) {
	  $tpl->newBlock('quest-ph');
	  $tpl->assignAll($ph);
	  $tpl->assign(array(
	  'title' => $ph->title,
	  'info' => 'Šāda pamācība lapā iztrūkst. Lai tādu izveidotu, dodies uz <a href="/write">šo sadaļu</a>.'
	  ));
	  }
	  } */
}

/**
 *  Free-to-play- vai mini-kvesti
 */
elseif ($category->textid == 'f2p-kvesti' || $category->textid == 'mini-kvesti') {

	// izdrukā lapā ievadtekstu par kvestiem kā tādiem
	$tpl->newBlock('quests-intro');

	// atkarībā no atvērtās sadaļas pamaina intro attēlu
	$intro_img = ($category->textid == 'mini-kvesti') ? 'citharede-sister.png' : 'hazelmere.png';
	$tpl->assign('intro-image', '/bildes/runescape/intro/' . $intro_img);

	$cat_id = ($category->textid == 'f2p-kvesti') ? 99 : 193;
	$folder = ($cat_id == 99) ? 'freequests' : 'miniquests';
	$title = ($cat_id == 99) ?
			'RuneScape visiem spēlētājiem pieejamie kvesti' : 'RuneScape minikvesti';

	$other_quests = $db->get_results("
		SELECT 
			`pages`.`id`            AS `page_id`,
			`pages`.`strid`         AS `page_strid`,
			`pages`.`title`         AS `page_title`,
			`pages`.`date`          AS `page_date`,
			`pages`.`author`        AS `page_author`,
			`pages`.`category`      AS `page_catid`,
            
            IFNULL(`rs_pages`.`is_old`, 0) AS `rspage_old`,
			`rs_pages`.`page_id`        AS `rspage_pageid`,			
			`rs_pages`.`img`            AS `rspage_img`,
			`rs_pages`.`description`    AS `rspage_description` 
		FROM `pages` 
            LEFT JOIN `rs_pages` ON (
                `pages`.`id`                = `rs_pages`.`page_id` AND
                `rs_pages`.`deleted_by`     = 0     AND
                `rs_pages`.`is_placeholder` = 0
            )
		WHERE 
			`pages`.`category` = $cat_id 
		ORDER BY 
			`pages`.`title` ASC
		");

	if ($other_quests) {

		$tpl->newBlock('other-quests');
		$tpl->assign('extended-title', $title);

		foreach ($other_quests as $quest) {

			$author = '';
			if ($user = get_user($quest->page_author)) {
				$quest->page_author = '<a href="' . mkurl('user', $user->id, $user->nick) . '">';
				$quest->page_author .= usercolor($user->nick, $user->level) . '</a>';
			}
			$quest->page_date = date('d.m.Y', strtotime($quest->page_date));

			$tpl->newBlock('other-quest');
			$tpl->assignAll($quest);

			// banerītis pie minikvestiem/prastajiem kvestiem
			if ($quest->rspage_img != '') {
				$quest->rspage_img = '<img src="/bildes/runescape/' . $folder . '/' . $quest->rspage_img . '" title="' . $quest->page_title . '" alt="">';
				$tpl->assign('page_image', $quest->rspage_img);
			}

			// pamācība novecojusi vai nepieciešamas HD bildes
			if ($quest->rspage_old != 0) {

				$title = ($quest->rspage_old == 1) ?
						'Pamācībai nepieciešamas jaunākas, labākas kvalitātes bildes!' :
						'Pamācību nepieciešams atjaunināt!';

				$picture = ($quest->rspage_old == 1) ? 'info_yellow.png' : 'info_red.png';
				$picture = '<img class="warning" src="/bildes/runescape/' . $picture . '" title="' . $title . '" alt="">';

				$tpl->assign('warning', $picture);
			}
		}
	}

	// placeholders
	/*
	  $placeholders = $db->get_results("SELECT * FROM `rs_placeholders` WHERE `cat` = '$id' ORDER BY `title` ASC");
	  if ($placeholders) {
	  $needed = ($id == 99) ? 'visiem spēlētājiem pieejamo kvestu' : 'minikvestu';
	  $tpl->newBlock('extended-placeholders');
	  $tpl->assign('needed', $needed);
	  foreach ($placeholders as $ph) {
	  $tpl->newBlock('extended-ph');
	  $tpl->assignAll($ph);
	  $link2 = ($ph->url2 == '') ? '' : ' un <a href="' . $ph->url2 . '">šis raksts</a>';
	  $link1 = ($link2 == '') ? '<a href="' . $ph->url . '">šis raksts</a>' : '<a href="' . $ph->url . '">šis</a>';
	  if ($ph->url != '' || $ph->url2 != '') {
	  $tpl->assign('link', '<br />Pamācības veidošanas procesā Tev var noderēt ' . $link1 . $link2 . '.');
	  }
	  }
	  } */
}