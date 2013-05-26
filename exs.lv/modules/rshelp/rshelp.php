<?php

$tpl->assignInclude('module-head', CORE_PATH . '/modules/' . $category->module . '/rshelp-head.tpl');
$tpl->prepare();
$skinid = ($auth->skin == '1') ? 'dark' : 'light';
$tpl->assign('skinid', $skinid);


/* ----------------------------------------------
 *		RuneScape kvestu galvenā izvēlne
/*/
if (isset($_GET['viewcat']) && $_GET['viewcat'] == 'kvestu-pamacibas') {

	// quests, kuriem lapā iztrūkst pamācība, kā arī tie, kurus jāparāda vairākās sērijās
	// array(sērijas id, vieta sarakstā, nosaukums
	$bonus = array(
		array(26, 7, '<a href="/read/missing-my-mummy">Missing My Mummy</a>'),
		array(25, 4, '<a href="/read/temple-at-senntisten-the">Temple at Senntisten, the</a>'),
		array(21, 20, '<a href="/read/legends-quest-2">Legends\' Quest</a>')
	);
	
	$tpl_options = 'no-right';	
	$tpl->newBlock('quests-intro');
		$tpl->assign('intro-image', '/bildes/rs/intro/khazard.png');	
	$tpl->newBlock('quests-additional');
	
	$storylines = $db->get_results("SELECT * FROM `rs_classes` WHERE `cat` = 'series' ORDER BY `order` ASC ");
	if ($storylines) {	
	
		$line = 0;
		
		foreach ($storylines as $storyline) {		
			$quests = $db->get_results("
				SELECT
					`pages`.`id` AS `id`,
					`pages`.`strid` AS `strid`,
					`pages`.`title` AS `title`
				FROM
					`rs_help`,`pages`
				WHERE
					`rs_help`.`cat` IN ('99','100') AND
					`rs_help`.`page_id` = `pages`.`id` AND
					`rs_help`.`storyline` = '".(int)$storyline->id."' 
				ORDER BY ABS(`rs_help`.`order`) ASC
			");
			if ($quests) {

				$counter = 1;
				$line++;
				
				$tpl->newBlock('storyline');
				$tpl->assignAll($storyline);
				
				if ($line % 5 == 0) {
					$tpl->assign('newline', ' newline');
					$line = 1;
				}							
				
				foreach ($quests as $quest) {

					$tpl->newBlock('storyline-quest');
					$tpl->assign('title', '<a href="/read/' . $quest->strid . '" title="'.$quest->title.'">' . $quest->title . '</a>');
					
					// pārbauda, vai pievienot kādu kvestu no augšējā masīva
					foreach ($bonus as $single) {
						if ($single[0] == $storyline->id && ($single[1] == $counter + 1 || ($single[1] >= $counter + 1 && $counter == count($quests)))) {
							$tpl->newBlock('storyline-quest');
							$tpl->assign('title', $single[2]);
						}
					}
					$counter++;
				}
			}
		}
	}
	/* statistikas dati */
	$tpl->newBlock('quests-outro');
	$tpl->assign(array(
		'2012' => $db->get_var("SELECT count(*) FROM `rs_help` WHERE `year` = '12' AND `cat` IN ('99','100') "),
		'2011' => $db->get_var("SELECT count(*) FROM `rs_help` WHERE `year` = '11' AND `cat` IN ('99','100') "),
		'2010' => $db->get_var("SELECT count(*) FROM `rs_help` WHERE `year` = '10' AND `cat` IN ('99','100') "),
		'2009' => $db->get_var("SELECT count(*) FROM `rs_help` WHERE `year` = '09' AND `cat` IN ('99','100') "),
		'2008' => $db->get_var("SELECT count(*) FROM `rs_help` WHERE `year` = '08' AND `cat` IN ('99','100') "),
		'older' => $db->get_var("SELECT count(*) FROM `rs_help` WHERE `year` NOT IN ('12','11','10','09','08') AND `cat` IN ('99','100') ")
	));
	$tpl->assign(array(
		'p2p' => $db->get_var("SELECT count(*) FROM `pages` WHERE `category` = '100' "),
		'f2p' => $db->get_var("SELECT count(*) FROM `pages` WHERE `category` = '99' "),
		'miniquests' => $db->get_var("SELECT count(*) FROM `pages` WHERE `category` = '193' "),
		'special' => $db->get_var("SELECT count(*) FROM `rs_help` WHERE `difficulty` = '6' AND `cat` IN ('99','100') "),
		'grandmaster' => $db->get_var("SELECT count(*) FROM `rs_help` WHERE `difficulty` = '5' AND `cat` IN ('99','100') "),
		'master' => $db->get_var("SELECT count(*) FROM `rs_help` WHERE `difficulty` = '4' AND `cat` IN ('99','100') "),
		'interm' => $db->get_var("SELECT count(*) FROM `rs_help` WHERE `difficulty` = '3' AND `cat` IN ('99','100') "),
		'easy' => $db->get_var("SELECT count(*) FROM `rs_help` WHERE `difficulty` = '2' AND `cat` IN ('99','100') "),
		'novice' => $db->get_var("SELECT count(*) FROM `rs_help` WHERE `difficulty` = '1' AND `cat` IN ('99','100') ")
	));
	/* statistikas END */
	$skills = $db->get_results("SELECT * FROM `rs_qskills` ORDER BY `skill` ASC");
	foreach ($skills as $skill) {
		$tpl->newBlock('skill-req');
		$tpl->assignAll($skill);
	}
}


/* ----------------------------------------------
 *		RuneScape P2P kvesti
/*/
else if (isset($_GET['viewcat']) && $_GET['viewcat'] == 'p2p-kvesti') {

	$tpl_options = 'no-right';
	$tpl->newBlock('quests-intro');
	$tpl->assign('intro-image', '/bildes/rs/intro/vampyre-juvinate.png');

	$quests = $db->get_results("
		SELECT 
			`pages`.`id`,
			`pages`.`strid`,
			`pages`.`title`,
			`pages`.`author`,
			`rs_help`.`page_id` AS `pid`,
			`rs_help`.`old` AS `old`
		FROM 
			`pages`
		LEFT JOIN `rs_help` ON `pages`.`id` = `rs_help`.`page_id`
		WHERE 
			`category` = '100' 
		ORDER BY `title` ASC 
	");
	
	if ($quests) {
		$tpl->newBlock('questlist');
		$letter = '';
		
		foreach ($quests as $quest => $data) {
		
			if ($data->id != $data->pid && $auth->id == 115) {
				// $insert = $db->query("INSERT INTO `rs_help` (cat,title,page_id) VALUES ('100','" . title2db($data->title) . "','" . $data->id . "') ");
				//echo $data->title.'<br />';
			}
		
			$tpl->newBlock('single-quest');
			$tpl->newBlock('quest-normal');
			$tpl->assignAll($data);
			
			$author = '';
			if ($user = get_user($data->author)) {
				$author = '<a style="font-size:11px;" href="' . mkurl('user', $user->id, $user->nick) . '">' . usercolor($user->nick, $user->level) . '</a>';
			}
			$tpl->assign('author', $author);
			
			$letter2 = substr($data->title, 0, 1);
			if ($letter2 != $letter) {
				$letter = $letter2;
				$tpl->assign(array(
					'letter' => '<b>' . $letter . '</b>',
					'border' => ' class="border"',
				));
			}
			
			if ($data->old != 0) {
				$title = ($data->old == 1) ? 'Pamācībai nepieciešamas jaunākas, labākas kvalitātes bildes!' : 'Pamācību nepieciešams atjaunināt!';
				$pic = ($data->old == 1) ? 'info_yellow_sm' : 'info_red_sm';
				$tpl->assign('warning', '<img class="warning_small" src="/bildes/rs/' . $pic . '.png" title="' . $title . '" alt="" />');
			}
		}
	}
	/* -- */
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
	}
	/* -- */
	// pārbauda skaitu, ievieto `rshelp`
	
	/* -- */
}

/* ----------------------------------------------
 *		RuneScape F2P un minikvesti
/*/ 
else if (isset($_GET['viewcat']) && ($_GET['viewcat'] == 'f2p-kvesti' || $_GET['viewcat'] == 'mini-kvesti')) {

	$tpl_options = 'no-right';
	$tpl->newBlock('quests-intro');
	$intro_img = ($_GET['viewcat'] == 'mini-kvesti') ? 'citharede-sister.png' : 'hazelmere.png';
	$tpl->assign('intro-image', '/bildes/rs/intro/' . $intro_img);

	$id = ($_GET['viewcat'] == 'f2p-kvesti') ? 99 : 193;
	$folder = ($id == 99) ? 'free' : 'miniquests';
	$title = ($id == 99) ? 'RuneScape visiem spēlētājiem pieejamie kvesti' : 'RuneScape minikvesti';
	
	$all_quests = $db->get_results("
		SELECT 
			`pages`.`id`,
			`pages`.`strid`,
			`pages`.`title`,
			`pages`.`date`,
			`pages`.`author`,
			`pages`.`category`,
			`rs_help`.`page_id` AS `pid`,
			`rs_help`.`old` AS `old`,
			`rs_help`.`img` AS `img`,
			`rs_help`.`description` AS `description` 
		FROM `pages` 
		LEFT JOIN `rs_help` ON `pages`.`id` = `rs_help`.`page_id`
		WHERE 
			`pages`.`category` = '$id' 
		ORDER BY 
			`pages`.`title` ASC
		");

	if ($all_quests) {
		$tpl->newBlock('questlist-extended');
		$tpl->assign('extended-title', $title);

		foreach ($all_quests as $quest) {
		
			if ($quest->id != $quest->pid) {
				$check = $db->get_row("SELECT * FROM `rs_help` WHERE `page_id` = '".$quest->id."' ");
				if (!$check) {
					//$quest->description .= ' - 2';
					$ins = $db->query("INSERT INTO `rs_help` (page_id,cat,title,strid,auth) VALUES (
						'".$quest->id."',
						'".$quest->category."',
						'".sanitize($quest->title)."',
						'".sanitize($quest->strid)."',
						'".$quest->author."'						
					) ");
				}
			}
		
			$author = '';
			if ($user = get_user($quest->author)) {
				$quest->author = '<a href="' . mkurl('user', $user->id, $user->nick) . '">' . usercolor($user->nick, $user->level) . '</a>';
			}
			$quest->date = date('d.m.Y', strtotime($quest->date));
			$tpl->newBlock('extended-left');
			$tpl->assignAll($quest);

			if ($quest->img != '') { // banerītis pie minikvestiem/prastajiem kvestiem
				$tpl->assign('image', '<img src="/bildes/rs/' . $folder . '/' . $quest->img . '" title="' . $quest->title . '" alt="' . $quest->title . '" />');
			}
			
			if ($quest->old != 0) { // pamācība novecojusi vai nepieciešamas HD bildes
				$title 	= ($quest->old == 1) ? 'Pamācībai nepieciešamas jaunākas, labākas kvalitātes bildes!' : 'Pamācību nepieciešams atjaunināt!';
				$pic 	= ($quest->old == 1) ? 'info_yellow' : 'info_red';
				$tpl->assign('warning', '<img class="warning" style="max-width:16px;max-height:16px;" src="/bildes/rs/' . $pic . '.png" title="' . $title . '" alt="" />');
			}
			
		}
	}
	/* placeholders */
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
	}
	/* -- */
}

/* -----------------------------------------------------------
 *		RuneScape minispēles un Distractions&Diversions
/*/  
else if (isset($_GET['viewcat']) && ($_GET['viewcat'] == 'minispeles' || $_GET['viewcat'] == 'distractions-diversions')) {

	$catid = ($_GET['viewcat'] == 'minispeles') ? 160 : 792;
	$type = ($catid == 160) ? 'minispēļu' : 'Distractions & Diversions';
	$type2 = ($catid == 160) ? 'minispēles' : 'Distractions & Diversions';

	$tpl_options = 'no-right';
	$tpl->newBlock('minigames');
	$tpl->assign('type-top', $type2);
	if ($catid == 160) {
		$tpl->newBlock('minigames-intro');
	} else {
		$tpl->newBlock('diversions-intro');
	}
	$minigames = $db->get_results("SELECT `id`,`strid`,`title`,`author`,`date`,`avatar` FROM `pages` WHERE `category` = '$catid' ORDER BY `title` ASC");
	if ($minigames) {
		foreach ($minigames as $game) {
			$tpl->newBlock('minigame');
			if ($user = get_user($game->author)) {
				$game->author = '<a href="' . mkurl('user', $user->id, $user->nick) . '">' . usercolor($user->nick, $user->level) . '</a>';
			}
			$game->avatar = ($game->avatar != '') ? '<a href="/read/' . $game->strid . '"><img class="mg-av" src="/' . $game->avatar . '" title="' . $game->title . '" alt="" /></a>' : '';
			$game->date = date("d.m.Y", strtotime($game->date));
			$game->title = str_replace('[D&amp;D] ', '', $game->title);
			$tpl->assignAll($game);

			$info = $db->get_row("SELECT `old`,`description`,`location`,`p2p` FROM `rs_help` WHERE `page_id` = '" . $game->id . "' LIMIT 1");
			if ($info) {
				$info->p2p = ($info->p2p == '1') ? 'Jā' : 'Nē';
				$tpl->assignAll($info);
				if ($info->old != '0') {
					$title = ($info->old == 1) ? 'Pamācībai nepieciešamas jaunākas, labākas kvalitātes bildes!' : 'Pamācību nepieciešams atjaunināt!';
					$pic = ($info->old == 1) ? 'info_yellow_sm' : 'info_red_sm';
					$tpl->assign('warning', '<img class="mg-old" src="/bildes/rs/' . $pic . '.png" title="' . $title . '" alt="" />');
				}
			} else {
				$insert = $db->query("INSERT INTO `rs_help` (page_id,cat,title) VALUES ('" . $game->id . "','$catid','" . title2db($game->title) . "') ");
			}
		}
	}
	// placeholderi neuzrakstītajām pamācībām...
	$get_ph = $db->get_results("SELECT * FROM `rs_placeholders` WHERE `cat` = '$catid' ORDER BY `title` ASC");
	if ($get_ph) {
		$tpl->newBlock('minigames-placeholders');
		$tpl->assign('type', $type);
		foreach ($get_ph as $ph) {
			$tpl->newBlock('minigame-ph');
			//$ph->img = ($ph->img != '') ? '<a href="/write"><img class="mg-av" src="/bildes/rs/temp/'.$ph->img.'" title="Pamācība vēl nav uzrakstīta!" alt="" /></a>' : '';
			$tpl->assignAll($ph);
			$link2 = ($ph->url2 == '') ? '' : ' un <a href="' . $ph->url2 . '">šis raksts</a>';
			$link1 = ($link2 == '') ? '<a href="' . $ph->url . '">šis raksts</a>' : '<a href="' . $ph->url . '">šis</a>';
			if ($ph->url != '' || $ph->url2 != '') {
				$tpl->assign('link', '<p>Pamācības veidošanas procesā Tev var noderēt ' . $link1 . $link2 . '.</p>');
			}
		}
	}
}
/* ----------------------------------------------
 *		RuneScape prasmes
/*/ 
else if (isset($_GET['viewcat']) && $_GET['viewcat'] == 'prasmes'/* && $auth->id == '115' */) {
	$tpl_options = 'no-right';
	$skills = $db->get_results("SELECT `id`,`title` FROM `cat` WHERE `parent` = '4' ORDER BY `title` ASC");
	if ($skills) {
		$skaits = 0;
		$tpl->newBlock('skills');
		foreach ($skills as $skill) {
			$pages = $db->get_results("SELECT `title`,`strid` FROM `pages` WHERE `category` = '" . $skill->id . "' ORDER BY `title` ASC LIMIT 5");
			if ($pages) {
				$skaits++;
				$img = $db->get_row("SELECT `img`,`info`,`members` FROM `rs_classes` WHERE `title` = '" . sanitize($skill->title) . "' AND `cat` = 'skills' ");
				if ($img) {
					$tpl->newBlock('skill');
					$img->members = ($img->members == '1') ? ' <img src="/bildes/rs/p2p_small.png" title="members only" />' : '';
					// pārmet jaunā rindā katru nepāra prasmi
					if ($skaits % 2 != 0) {
						$tpl->assign('linebreak', ' style="clear:left;"');
					} else {
						$px = ($auth->skin == 2) ? 13 : 25;
						$tpl->assign('linebreak', ' style="margin-left:' . $px . 'px;"');
					}
					// Linux fontu dēļ Linux lietotājiem uzliek citu klasi ar citiem bloku izmēriem
					if (strpos($_SERVER['HTTP_USER_AGENT'], 'inux') !== false) {
						$tpl->assign('forlinux', '-2');
					}
					$tpl->assign(array(
						'title' => $skill->title,
						'img' => $img->img,
						'info' => $img->info,
						'members' => $img->members
					));
					// ja vairāk par 5 linkiem, izvada pogu uz nākamo lapu
					$page_count = $db->get_var("SELECT count(*) FROM `pages` WHERE `category` = '$skill->id'");
					if ($page_count > 5) {
						$tpl->assign('next', '<a class="skill-pager" href="/rs-skills/?skill=' . $skill->id . '&page=2">Tālāk &rsaquo;&rsaquo;</a>');
					}
					// izvada ar prasmes kategoriju saistītās lapas
					foreach ($pages as $page) {
						$tpl->newBlock('skill-link');
						$tpl->assignAll($page);
						$short_title = textlimit($page->title, 30);
						$tpl->assign('short-title', $short_title);
					}
				}
			}
		}
	}
}
/* --------------------------------------- */
//	 RuneScape padomu sadaļa
/* --------------------------------------- */ 
else if (isset($_GET['viewcat']) && $_GET['viewcat'] == 'padomi') {

	$get_blocks = $db->get_results("SELECT `id`,`title`,`klase` FROM `rs_classes` WHERE `cat` = 'other' ORDER BY `order` ASC");
	if ($get_blocks) {
		$tpl->newBlock('rshelp-blocklist-outer');
		foreach ($get_blocks as $data) {
			$get_guides = $db->get_results("SELECT `id`,`strid`,`title`,`author` FROM `pages` WHERE `category` = '5' AND `rsclass` = '" . $data->id . "' ORDER BY `title` ASC");
			// AND `rsclass` != '0'
			// AND `title` like '%naudas peln%'
			if ($get_guides) {

				$class = ($data->klase != '') ? ' class="rshelp-' . $data->klase . '"' : '';
				$tpl->newBlock('rshelp-blocklist');
				$tpl->assign('blocklist-title', $data->title);
				foreach ($get_guides as $guidedata) {
					// $db->query("UPDATE `pages` SET `rsclass` = '14' WHERE `id` = '".$guidedata->id."' AND `title` like '%naudas peln%' ");
					if ($user = get_user($guidedata->author)) {
						$addedby = '<a style="font-size:11px;" href="' . mkurl('user', $user->id, $user->nick) . '">' . usercolor($user->nick, $user->level) . '</a>';
					}
					$tpl->newBlock('rshelp-blocklistitem');
					$tpl->assign(array(
						'guide-strid' => $guidedata->strid,
						'guide-title' => $guidedata->title,
						'guide-author' => $addedby,
						'rshelp-class' => $class
					));
				}
			}
		}
	}


	$all_items = $db->get_results("SELECT `strid`,`title`,`author` FROM `pages` WHERE `category` = '5' AND `rsclass` = '0' ORDER BY `title` ASC");
	// AND `rsclass` = '0'
	if ($all_items) {
		$tpl->newBlock('rshelp-list');
		$tpl->assign(array(
			'articles-catid' => '5',
			'articles-title' => 'Padomi'
		));
		foreach ($all_items as $item => $data) {
			// $db->query("UPDATE `pages` SET `rsclass` = '18' WHERE `strid` = '".$data->strid."' AND `title` like '%RuneScape legend%' ");
			if ($user = get_user($data->author)) {
				$data->author = '<a style="font-size:11px;" href="' . mkurl('user', $user->id, $user->nick) . '">' . usercolor($user->nick, $user->level) . '</a>';
			}

			$tpl->newBlock('rshelp-listitem');
			$tpl->assignAll($data);
		}
	}
}
/* --------------------------------------- */
//	 Tasks/achievement diaries
/* --------------------------------------- */ 
else if (isset($_GET['viewcat']) && $_GET['viewcat'] == 'achievement-diary'/* && in_array($auth->id,array(115,140,21018)) */) {
	$tpl_options = 'no-right';
	$tpl->newBlock('tasks');
	$skaits = 0;
	$diaries = $db->get_results("SELECT `id`,`title`,`img` FROM `rs_classes` WHERE `cat` = 'tasks' ORDER BY `order` ASC");
	if ($diaries) {
		foreach ($diaries as $tasks) {
			$pages = $db->get_results("SELECT `strid`,`title` FROM `pages` WHERE `rsclass` = '$tasks->id' AND `category` = '194' ORDER BY `title` ASC");
			if ($pages) {
				$tpl->newBlock('tasks-block');
				$tpl->assignAll($tasks);
				if ($skaits % 3 == 0) {
					$tpl->assign('newline', ' newline');
				}  // pārmet blokus jaunā rindā
				$skaits++;
				foreach ($pages as $page) {
					$tpl->newBlock('task');
					$tpl->assignAll($page);
				}
			} else if ($tasks->id != '112') {
				$tpl->newBlock('tasks-not');
				$tpl->assignAll($tasks);
			} else {

			}
		}
	}
	// citi raksti
	$others = $db->get_results("SELECT `strid`,`title` FROM `pages` WHERE `category` = '194' AND `rsclass` = '0' ORDER BY `title` ASC");
	if ($others) {
		$tpl->newBlock('tasks-block');
		$tpl->assign(array(
			'img' => 'uncategorised.png',
			'title' => 'Nekategorizēti raksti'
		));
		foreach ($others as $other) {
			$tpl->newBlock('task');
			$tpl->assignAll($other);
		}
	}
}
/* --------------------------------------- */
//	 Ģildes
/* --------------------------------------- */ 
else if (isset($_GET['viewcat']) && $_GET['viewcat'] == 'gildes'/* && in_array($auth->id,array(115,140,21018)) */) {
	$tpl_options = 'no-right';
	$guilds = $db->get_results("SELECT `id`,`strid`,`title`,`author` FROM `pages` WHERE `category` = '791' ORDER BY `title` ASC");
	if ($guilds) {
		$tpl->newBlock('guilds');
		$tpl->newBlock('guilds-not');
		foreach ($guilds as $guild) {
			$info = $db->get_row("SELECT `img`,`p2p`,`location`,`extra`,`old` FROM `rs_help` WHERE `page_id` = '$guild->id' LIMIT 1");
			if ($info) {
				if ($info->img != '') {
					$info->p2p = ($info->p2p == 1) ? '<img class="guild-icon" src="/bildes/rs/p2p_small.png" title="Maksājošo spēlētāju ģilde" />' : '';
					if ($info->old != '0') {
						$info->old = ($info->old == '1') ?
								'<img class="old" src="/bildes/rs/info_yellow_sm.png" title="Pamācībai nepieciešamas jaunākas, labākas kvalitātes bildes!" alt="" />' : '<img class="old" src="/bildes/rs/info_red_sm.png" title="Pamācību nepieciešams atjaunināt!" alt="" />';
					} else {
						$info->old = '';
					}
					$tpl->newBlock('guild');
					$tpl->assignAll($guild);
					$tpl->assignAll($info);
				} else {
					$tpl->newBlock('guild-page');
					$tpl->assignAll($guild);
				}
			} else {
				$insert = $db->query("INSERT INTO `rs_help` (page_id,cat,title,auth) VALUES ('" . $guild->id . "','791','" . title2db($guild->title) . "','" . (int) $guild->author . "') ");
				$tpl->newBlock('guild-page');
				$tpl->assignAll($guild);
			}
		}
	}
}
/* --------------------------------------- */
//	 Ceļveži
/* --------------------------------------- */
else if (isset($_GET['viewcat']) && $_GET['viewcat'] == 'celvezi'/* && in_array($auth->id,array(115,140)) */) {
	$tpl_options = 'no-right';
	$tpl->newBlock('areas');

	// pēc kategorijām
	$cats = $db->get_results("SELECT `id`,`title` FROM `rs_classes` WHERE `cat` = 'areas' ORDER BY `order` ASC ");
	if ($cats) {
		foreach ($cats as $cat) {
			$pages = $db->get_results("SELECT `id`,`strid`,`title`,`author` FROM `pages` WHERE `rsclass` = '$cat->id' AND `category` = '195' ORDER BY `title` ASC ");
			if ($pages) {
				$tpl->newBlock('areas-category');
				$tpl->assignAll($cat);
				foreach ($pages as $page) {
					//$count++;
					$help = $db->get_row("SELECT `img`,`large_img` FROM `rs_help` WHERE `page_id` = '" . $page->id . "' LIMIT 1");
					if ($help) {
						$tpl->newBlock('area-choice');
						if (in_array($page->title, array('Bedabin Camp', 'Brimhaven Dungeon', 'Baxtorian Falls'))) {
							$tpl->newBlock('area-left');
						} else if (in_array($page->title, array(''))) {
							$tpl->newBlock('area-right');
							if ($auth->skin == '0') {
								$tpl->assign('oldskin-margin', 'margin-right:56px !important');
							} else if ($auth->skin == '3') {
								$tpl->assign('oldskin-margin', 'margin-right:63px !important');
							}
						} else {
							$tpl->newBlock('area');
						}
						$help->img = ($help->img != '') ? '<img class="area-ico" src="bildes/rs/areas/' . $help->img . '" />' : '';
						$help->large_img = ($help->large_img != '') ? '<img class="large-img" src="bildes/rs/areas/large/' . $help->large_img . '" title="' . $page->title . '" />' : '';
						$tpl->assignAll($page);
						$tpl->assignAll($help);
						//$skaits++;
					} else {
						$insert = $db->query("INSERT INTO `rs_help` (page_id,cat,title,auth) VALUES ('" . (int) $page->id . "','195','" . title2db($page->title) . "','" . (int) $page->author . "') ");
						$tpl->newBlock('area-choice');
						$tpl->newBlock('area');
						$tpl->assignAll($page);
					}
				}
			}
			// placeholders
			$phs = $db->get_results("SELECT `title` FROM `rs_placeholders` WHERE `cat` = '$cat->id' ORDER BY `title` ASC");
			if ($phs) {
				$tpl->newBlock('area-choice');
				$tpl->newBlock('area-more');
				$sk = 0;
				foreach ($phs as $ph) {
					$sk++;
					$tpl->newBlock('area-choice');
					$tpl->newBlock('area-placeholder');
					$tpl->assign('title', $ph->title);
					if ($sk == 1) {
						$tpl->assign(array(
							'area-break' => 'clear:left;',
							'placeholder-start' => '<div class="ph-hidden" style="display:none">'
						));
					}
					if ($sk >= count($phs)) {
						$tpl->assign('placeholder-end', '</div>');
					}
				}
			}
		}
	}
	// nekategorizēti raksti
	$pages = $db->get_results("SELECT `id`,`strid`,`title` FROM `pages` WHERE `rsclass` = '0' AND `category` = '195' ORDER BY `title` ASC ");
	if ($pages) {
		$tpl->newBlock('areas-category');
		$tpl->assign('title', 'Nekategorizēti raksti');
		foreach ($pages as $page) {
			$tpl->newBlock('area-choice');
			$tpl->newBlock('area');
			$tpl->assignAll($page);
		}
	}
}
/* --------------------------------------- */
//	 Sākumlapa
/* --------------------------------------- */ 
else if (isset($_GET['viewcat']) && $_GET['viewcat'] == 'runescape'/* && in_array($auth->id,array(21018,115)) */) {

	$tpl_options = 'no-right';
	$tpl->newBlock('runescape-mainpage');

	$total = $db->get_var("SELECT count(*) FROM `pages` WHERE category = ('" . $category->id . "')");
	$page_count = ceil($total / 15);

	$current_page = (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 && $_GET['page'] <= $page_count) ? (int) $_GET['page'] : 1;
	$skip = 15 * ($current_page - 1);
	$end = 15;

	$articles = $db->get_results("SELECT
  		`pages`.`id` AS `id`,
  		`pages`.`strid` AS `strid`,
  		`pages`.`title` AS `title`,
  		`pages`.`date` AS `date`,
  		`pages`.`author` AS `author`,
  		`pages`.`text` AS `text`,
  		`pages`.`posts` AS `posts`,
  		`pages`.`avatar` AS `avatar`,
  		`pages`.`views` AS `views`,
  		`pages`.`intro` AS `intro`,
  		`users`.`nick` AS `nick`,
  		`users`.`level` AS `level`
  	FROM `pages`,`users` WHERE `pages`.`category` IN (599) AND `users`.`id` = `pages`.`author`
    ORDER BY `pages`.`date` DESC LIMIT $skip,$end ");

	//if($skip) {$page_title =	$page_title . ' - lapa ' . ($skip/$end+1);}

	if ($articles) {
		$tpl->newBlock('rsarticles');

		foreach ($articles as $article) {
			if (!$article->nick) {
				$article->nick = 'Nezināms';
				$article->level = 0;
			}

			$article->date = display_time(strtotime($article->date));
			if (empty($article->intro)) {
				$article->intro = sanitize($article->text);
				$db->query("UPDATE pages SET intro = '$article->intro' WHERE id = '$article->id' LIMIT 1");
			}
			$article->intro = textlimit($article->intro, 500, $replacer = '...');
			$article->title = str_replace(array('[RuneScape] ', '[Runescape] ', '[rs] ', '[RS] ', '[runescape] '), '', $article->title);
			$author = mkurl('user', $article->author, $article->nick);
			$article->author = usercolor($article->nick, $article->level);

			$tpl->newBlock('rsarticle');
			$tpl->assignAll($article);
			$tpl->assign('aurl', $author);
			if ($article->avatar) {
				$tpl->newBlock('rsarticle-avatar');
				$tpl->assign(array(
					'image' => trim($article->avatar),
					'alt' => trim(htmlspecialchars($article->title))
				));
			}
		}
	}

	/* $pager = pager($category->stat_topics, $skip, $end, '/' . $category->textid . '/?skip=');
	  $tpl->assignGlobal(array(
	  'pager-next' => $pager['next'],
	  'pager-prev' => $pager['prev'],
	  'pager-numeric' => $pager['pages']
	  )); */

// lapošana
	if ($current_page <= $page_count/* && $current_page > 0 */) {

		$toLeft = -2;
		$toRight = 2;
		$difference = $page_count - $current_page;
		if ($page_count < 5) {
			$diff = $current_page - 1;
			$toLeft = ($diff > 2) ? -2 : -$diff;
			$toRight = ($difference > 2) ? 2 : $difference;
		} else if ($page_count >= 5) {
			if ($current_page < 4) {
				$toLeft = 1 - $current_page;
				$toRight = 5 - $current_page;
			} else if ($current_page > $page_count - 2) {
				$toLeft = $difference - 4;
				$toRight = $difference;
			}
		}
		$all_pages = '';
		if ($current_page > 3) { // izvada pirmo lapu pirms bultiņas
			$all_pages .= '<li class="start"><a href="/runescape/?page=1">1</a></li>';
		}
		if ($current_page > 1) { // izvada bultiņu uz iepriekšējo lapu
			$all_pages .= '<li class="arrows"><a href="/runescape/?page=' . ($current_page - 1) . '">««</a></li>';
		}
		for ($a = ($current_page + $toLeft); $a <= ($current_page + $toRight); $a++) { // izvada lapas pa vidu starp bultiņām
			if ($a == $current_page) {
				$all_pages .= '<li class="active">' . $a . '</li>';
			} else {
				$all_pages .= '<li><a href="/runescape/?page=' . $a . '">' . $a . '</a></li>';
			}
		}
		if ($current_page < $page_count) { // bultiņa uz nākamo lapu
			$all_pages .= '<li class="arrows"><a href="/runescape/?page=' . ($current_page + 1) . '">»»</a></li>';
		}

		if ($current_page < $page_count - 2) { // pēdējā lapa aiz labās bultiņas
			$all_pages .= '<li class="end"><a href="/runescape/?page=' . $page_count . '">' . $page_count . '</a></li>';
		}
		$tpl->newBlock('all-rs-pages');
		$tpl->assign('all-pages', $all_pages);
	}
	// lapošanas END
}

/* --------------------------------------- */
//	 OSS
/* --------------------------------------- */ 
/*else if (isset($_GET['viewcat']) && $_GET['viewcat'] == 'oss-guides' && $auth->id == 115) { 
	$pages = $db->get_results("SELECT `id`,`strid`,`title` FROM `pages` WHERE `category` IN(6,244) ORDER BY `title` ASC LIMIT 500,5000");
	if ($pages) {
		$counter = 1;
		foreach ($pages as $page) {
			echo '<strong>'.$counter.'.</strong> <a href="/read/'.$page->strid.'">'.$page->title.'</a><br />';
			$counter++;
		}
	}
}*/
/* --------------------------------------- */
//	 Citas RuneScape pamācību izvēlnes
/* --------------------------------------- */ 
else {
	$all_items = $db->get_results("SELECT `strid`,`title`,`author` FROM `pages` WHERE `category` = '" . $category->id . "' ORDER BY `title` ASC");
	if ($all_items) {
		$tpl->newBlock('rshelp-list');
		$tpl->assign('category-title', $category->title);
		foreach ($all_items as $item => $data) {
			if ($user = get_user($data->author)) {
				$data->author = '<a style="font-size:11px;" href="' . mkurl('user', $user->id, $user->nick) . '">' . usercolor($user->nick, $user->level) . '</a>';
			}
			$tpl->newBlock('rshelp-listitem');
			$tpl->assignAll($data);
			/* $tpl->assign(array(
			  'guide-strid' => $data->strid,
			  'guide-title' => $data->title,
			  'guide-author' => $addedby
			  )); */
		}
	}
}
?>
