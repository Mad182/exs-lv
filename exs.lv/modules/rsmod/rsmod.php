<?php

/* ----------------------------------------------------------------------------------- */
//	 RuneScape sadaļu administrēšanas stūrītis
/* ---------------------------------------------------------------------------------- */
if (in_array($auth->id, array(21018)) || im_mod()) {

	$tpl->assignInclude('module-head', CORE_PATH . '/modules/' . $category->module . '/head.tpl');
	$tpl->prepare();
	$skinid = ($auth->skin == '1') ? 'dark' : 'light';
	$tpl->assign('skinid', $skinid);

	$tpl_options = 'no-right';
	$tpl->newBlock('rsmod');

	if (!isset($_GET['var1']) || $_GET['var1'] != 'mod') {
		$tpl->newBlock('rsmod-menu');
	}	
	

// mod meklētājs
if (isset($_GET['var1']) && $_GET['var1'] == 'mod') {

	//$search_fields = array('username','email','skype');

	$tpl->newBlock('mod-cpanel');
	$tpl_options = '';

	//$fields = array('nick','mail','skype');
	if (isset($_POST['submit'])) {
	
		// meklēšana pēc lietotāja nika
		if (isset($_POST['nick']) && strlen($_POST['nick']) > 2) {
			$field 		= 'nick';
			$criteria 	= '`nick` LIKE \'%'.sanitize($_POST['nick']).'%\'';
			
		// meklēšana pēc e-pasta
		} else if (isset($_POST['mail']) && strlen($_POST['mail']) > 4) {
			$field 		= 'mail';
			$criteria 	= '`mail` LIKE \'%'.sanitize($_POST['mail']).'%\'';
			
		// meklēšana pēc pēdējās lietotājs IP adreses
		} else if (isset($_POST['ip']) && strlen($_POST['ip']) > 6) {
			$field 		= 'ip';
			$criteria 	= '`lastip` LIKE \'%'.sanitize($_POST['ip']).'%\'';
			
		// kļūdu gadījumā
		} else {
			$criteria 	= '1';
			$field 		= '';
		}

		$results = $db->get_results("SELECT `id`,`nick`,`mail`,`lastip`,`karma`,`date` FROM `users` WHERE ".$criteria." ORDER BY ABS(`level`) DESC, `nick` ASC LIMIT 0,30");
		if ($results) {
			$tpl->newBlock('search-results');
			foreach ($results as $res) {
				
				$res->date = ceil((time() - strtotime($res->date)) / 60 / 60 / 24);
				
				// iekrāso formā ievadītos burtus
				if ($field == 'nick') {
					$res->nick = str_replace('<strong>'.$_POST['nick'].'</strong>',$_POST['nick'],$res->nick);
				}				
				if ($field == 'mail') {
					$res->mail = str_replace($_POST['mail'],'<strong>'.$_POST['mail'].'</strong>',$res->mail);
				}
				if (isset($_POST['ip']) && !empty($_POST['ip'])) {
					$res->lastip = str_replace($_POST['ip'],'<strong>'.$_POST['ip'].'</strong>',$res->lastip);
				}
				$tpl->newBlock('search-result');
				$tpl->assignAll($res);
			}
		}
	}
	
}	




if (isset($_GET['var1']) && $_GET['var1'] == 'dev' && $auth->id == 115) {

	$count_pages 	= $db->get_var("SELECT count(*) FROM `pages` WHERE `category` = '100' ");
	$count_rshelp 	= $db->get_var("SELECT count(*) FROM `rs_help` WHERE `cat` = '100' ");
	
	echo $count_pages.' : '.$count_rshelp.'<br />';

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
			
			$get = $db->get_row("SELECT `id` FROM `rs_help` WHERE `page_id` = '".$quest->id."' ");
			if (!$get) {
		
				echo '<strong>'.$counter.'.</strong> <a href="/read/'.$quest->strid.'">'.$quest->title.'</a><br />';
				$counter++;
			}
		}
		echo '<br /><br />';
	}

}
else if (isset($_GET['var1'])) {

	if ($_GET['var1'] == 'ready' && $auth->id == 115) {
	
		$q = $db->query("UPDATE `rs_help` SET `ready` = '0' ");
		
	} else if ($_GET['var1'] == 'questlist' && $auth->id == 115) {
	
		$pages = $db->get_results("SELECT `id`,`title`,`strid`,`author` FROM `pages` WHERE `category` in ('99','100') ORDER BY `title` ASC");
		foreach ($pages as $page) {
			echo $page->title . '<br />';
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
	
/* ----------------------------------------------------- */
//	 Kvestu sēriju numerācija
/* ---------------------------------------------------- */ 
else if ($_GET['var1'] == 'st-order') {

	// sēriju numerācija un nosaukumi tiek atjaunoti
	if (isset($_GET['var2']) && $_GET['var2'] == 'update') {
		$get_cats = $db->get_results("SELECT `id` FROM `rs_classes` WHERE `cat` = 'series' ");
		if ($get_cats) {
			foreach ($get_cats as $cat => $data) {
				if (isset($_POST['order_' . $data->id]) && isset($_POST['title_' . $data->id])) {
					$order = (int) $_POST['order_' . $data->id];
					$title = sanitize($_POST['title_' . $data->id]);
					$update = $db->query("UPDATE `rs_classes` SET `order` = '$order', `title` = '$title' WHERE `id` = '$data->id' LIMIT 1");
				}
			}
		}
		header("Location: /" . $_GET['viewcat'] . "/st-order");
	}
	
	// izvada visas kvestu sērijas ar to numerāciju
	else {
	
		$all_cats = $db->get_results("SELECT `id`,`title`,`order` FROM `rs_classes` WHERE `cat` = 'series' ORDER BY `order` ASC ");
		if ($all_cats) {
			$skaits = 0;
			$tpl->newBlock('rsmod-series');
			$tpl->newBlock('rsmod-series-col');
			foreach ($all_cats as $cat => $data) {
				$tpl->newBlock('series-single');
				$tpl->assignAll($data);

				$tpl->newBlock('single-ordering');
				$tpl->assign('id', $data->id);

				for ($a = 0; $a < sizeof($all_cats); $a++) {
					$selected = (($a + 1) == $data->order) ? ' selected="selected"' : '';
					$tpl->newBlock('single-order');
					$tpl->assign(array(
						'order' => ($a + 1),
						'selected' => $selected
					));
				}
				$skaits++;
				if ($skaits == 10) {
					$tpl->newBlock('rsmod-series-col');
				}
			}
		}
	}
}


/* ----------------------------------------------------- */
//	 sērijās esošo kvestu secīga sakārtošana
/* ---------------------------------------------------- */ 
else if ($_GET['var1'] == 'order') {

	// numerācija tiek atjaunota
	if (isset($_GET['var2'])) {
		$id = (int) $_GET['var2'];
		if ($story = $db->get_row("SELECT `id` FROM `rs_classes` WHERE `id` = '" . $id . "' LIMIT 1")) {
			$quests = $db->get_results("SELECT `id` FROM `rs_help` WHERE `storyline` = '" . $story->id . "' ");
			if ($quests) {
				foreach ($quests as $quest) {
					if (isset($_POST[$quest->id . '_order'])) {
						$db->query("UPDATE `rs_help` SET `order` = '" . (int) $_POST[$quest->id . '_order'] . "' WHERE `id` = '" . $quest->id . "' ");
					}
				}
			}
		}
		header("Location: /" . $_GET['viewcat'] . "/order");
	}
	// izvada sarakstu ar visām sērijām un tajos esošo questu numerāciju
	else {
		$all_series = $db->get_results("SELECT * FROM `rs_classes` WHERE `cat` = 'series' ORDER BY `order` ASC");
		if ($all_series) {
			$sk = 0;
			foreach ($all_series as $single) {
				$get_quests = $db->get_results("SELECT * FROM `rs_help` WHERE `storyline` = '" . $single->id . "' ORDER BY `order` ASC");
				if ($get_quests && count($get_quests) > 1) {
					$tpl->newBlock('rsmod-quests-order');
					$tpl->assign(array(
						'title' => $single->title,
						'story' => $single->id
					));
					if ($sk % 4 == 0) {
						$tpl->assign('clearleft', 'clear:left');
					}
					foreach ($get_quests as $quest) {
						$title = $db->get_row("SELECT `title`,`strid` FROM `pages` WHERE `id` = '" . $quest->page_id . "' AND `category` IN ('99','100') LIMIT 1");
						if ($title) {
							$tpl->newBlock('order-quest');
							$tpl->assign(array(
								'quest-title' => $title->title,
								'strid' => $title->strid,
								'qid' => $quest->id
							));
							for ($a = 0; $a < count($get_quests); $a++) {
								$selected = ($a + 1 == $quest->order) ? ' selected="selected"' : '';
								$tpl->newBlock('order-nr');
								$tpl->assign(array(
									'nr' => $a + 1,
									'selected' => $selected
								));
							}
						}
					}
					$sk++;
				}
			}
		}
	}
}

/* ----------------------------------------------------- */
//	 prasmju līmeņu rediģēšana questiem
/* ---------------------------------------------------- */ 
else if ($_GET['var1'] == 'qskills') {

	$tpl->newBlock('rsmod-quests-skills');
	if (isset($_POST['submit'])) {
		$get = $db->get_results("SELECT `id` FROM `rs_qskills`");
		foreach ($get as $data) {
			if (isset($_POST[$data->id . '_level']) && isset($_POST[$data->id . '_quest'])) {
				$db->query("UPDATE `rs_qskills` SET `level` = '" . (int) $_POST[$data->id . '_level'] . "', `quest` = '" . sanitize($_POST[$data->id . '_quest']) . "' WHERE `id` = '" . $data->id . "' ");
			}
		}
	}
	$skills = $db->get_results("SELECT * FROM `rs_qskills` ORDER BY `skill` ASC");
	if ($skills) {
		$tpl->newBlock('skills-col');
		$skaits = 0;
		foreach ($skills as $data) {
			$tpl->newBlock('level');
			$tpl->assignAll($data);
			$skaits++;
			if ($skaits == 13) {
				$tpl->newBlock('skills-col');
			}
		}
	}
}

/* ----------------------------------------------------- */
//	 placeholderi neuzrakstītajām pamācībām
/* ---------------------------------------------------- */ 
else if ($_GET['var1'] == 'ph') {

		$cat_ids = array(99, 100, 160, 193, 792, 80, 95, 96, 97, 101);
		$cats = array(
			array(99, 'F2P kvesti'),
			array(100, 'P2P kvesti'),
			array(193, 'Minikvesti'),
			array(160, 'Minispēles'),
			array(792, 'Distractions & Diversions'),
			array(80, 'Ceļveži: Citas vietas'),
			array(95, 'Ceļveži: Wilderness'),
			array(96, 'Ceļveži: Pilsētas'),
			array(97, 'Ceļveži: Salas'),
			array(101, 'Ceļveži: Pazemes')
		);

	// ievieto datubāzē jaunu placeholder
	if (isset($_POST['submit'])) {
		if (title2db($_POST['title']) != '' && in_array((int) $_POST['cat'], $cat_ids)) {
			$db->query("INSERT INTO `rs_placeholders` (cat,title,url,url2) VALUES (
			  '" . (int) $_POST['cat'] . "',
			  '" . title2db($_POST['title']) . "',
			  '" . sanitize($_POST['url']) . "',
			  '" . sanitize($_POST['url2']) . "'
			)");
		}
	}
	// izdzēš no datubāzes jau esošu placeholder
	else if (isset($_GET['delete'])) {
		$id = (int) $_GET['delete'];
		$db->query("DELETE FROM `rs_placeholders` WHERE `id` = '$id' LIMIT 1");
		header("Location: /" . $_GET['viewcat'] . "/ph");
	}
	/* izvada visus pievienotos rakstus */
	$tpl->newBlock('rsmod-placeholders');
	$tpl->newBlock('rsmod-ph-addnew');

	foreach ($cats as $cat) {
		$get_ph = $db->get_results("SELECT * FROM `rs_placeholders` WHERE `cat` = '" . $cat[0] . "' ORDER BY `cat` ASC, `title` ASC");
		if ($get_ph) {
			$tpl->newBlock('rsmod-phtable');
			$tpl->assign('cat-title', $cat[1]);
			foreach ($get_ph as $ph) {
				$tpl->newBlock('rsmod-ph-listitem');
				$tpl->assignAll($ph);
				$link1 = ($ph->url == '') ? 'delete' : 'tick';
				$link2 = ($ph->url2 == '') ? 'delete' : 'tick';
				$tpl->assign(array(
					'link1' => $link1,
					'link2' => $link2
				));
			}
		}
	}
}

/* ----------------------------------------------------------------------- */
//	 Saraksts ar rakstiem, kuriem iekš `pages` ir spec. pamācību sadaļa
/* ---------------------------------------------------------------------- */ 
else if ($_GET['var1'] == 'inpages' && $auth->id == 115) {
	$all_items = $db->get_results("SELECT `strid`,`title`,`author`,`category`,`rsclass` FROM `pages` WHERE `rsclass` != '0' ORDER BY `rsclass` ASC, `title` ASC");
	if ($all_items) {
		$cat = '';
		$tpl->newBlock('rsmod-pagelist');
		foreach ($all_items as $item => $data) {
			if ($cat != $data->rsclass) {
				$tpl->assign('border', ' style="border-bottom:2px solid #bbb;"');
			}
			$cat = $data->rsclass;
			if ($user = get_user($data->author)) {
				$data->author = '<a style="font-size:11px;" href="' . mkurl('user', $user->id, $user->nick) . '">' . usercolor($user->nick, $user->level) . '</a>';
			}
			$tpl->newBlock('pagelist-listitem');
			$tpl->assignAll($data);
		}
	}
}

/* ----------------------------------------------------- */
//	 Saraksts ceļvežiem
/* ---------------------------------------------------- */

// sadaļas rediģēšanas lapa
else if ($_GET['var1'] == 'places' && isset($_GET['edit'])) {
	$page_id = (int) $_GET['edit'];
	$page = $db->get_row("SELECT `id`,`title`,`strid`,`rsclass` FROM `pages` WHERE `id` = '$page_id' AND `category` = '195' LIMIT 1");
	if ($page) {
		$tpl->newBlock('rsmod-cities-edit');
		$tpl->assignAll($page);
		// izvēlne ar kategorijām
		$cats = $db->get_results("SELECT `id`,`title` FROM `rs_classes` WHERE `cat` = 'areas' ORDER BY `order` ASC");
		if ($cats) {
			foreach ($cats as $cat) {
				$tpl->newBlock('rsmod-cities-cat');
				$tpl->assign(array(
					'nr' => $cat->id,
					'cat' => $cat->title
				));
				if ($cat->id == $page->quest_chapter) {
					$tpl->assign('selected', ' selected="selected"');
				}
			}
		}
	}
	
} else if ($_GET['var1'] == 'places' && isset($_GET['delete']) && $auth->id == 115) {
	$page_id = (int) $_GET['delete'];
	//$page = $db->get_var("SELECT count(*) FROM `rs_help` WHERE `page_id` = '$page_id'");
	$del = $db->query("DELETE FROM `rs_help` WHERE `page_id` = '$page_id' ");
	header("Location: /" . $_GET['viewcat'] . "/places");
}


// updeito sadaļu
else if ($_GET['var1'] == 'places' && isset($_GET['var2'])) {
	$page_id = (int) $_GET['var2'];
	$check = $db->get_var("SELECT count(*) FROM `pages` WHERE `id` = '" . $page_id . "' AND `category` = '195' ");
	if ($check == 1 && isset($_POST['cat'])) {
		$cat = (int) $_POST['cat'];
		$update = $db->query("UPDATE `pages` SET `rsclass` = '" . $cat . "' WHERE `id` = '" . $page_id . "' LIMIT 1");
		$up2 = $db->query("UPDATE `rs_help` SET `ready` = '1' WHERE `page_id` = '$page_id' ");
		// pārbaude, vai iekš rs_help tāds ir?
	}
	header("Location: /" . $_GET['viewcat'] . "/places");
}


// saraksts ar ceļvežiem pa sadaļām
else if ($_GET['var1'] == 'places') {
	$tpl->newBlock('rsmod-cities');

	// ceļvežu rediģējamais saraksts
	$pages = $db->get_results("SELECT `id`,`strid`,`title`,`author`,`rsclass` FROM `pages` WHERE `category` = '195' ORDER BY `title` ASC ");
	if ($pages) {
		foreach ($pages as $page) {
		
			if ($user = get_user($page->author)) {
				$page->author = '<a style="font-size:11px;" href="' . mkurl('user', $user->id, $user->nick) . '">' . usercolor($user->nick, $user->level) . '</a>';
			}
			$tpl->newBlock('city');
			if ($c_title = $db->get_row("SELECT `title` FROM `rs_classes` WHERE `cat` = 'areas' AND `id` = '" . $page->rsclass . "' LIMIT 1")) {
				$tpl->assign('c-title', $c_title->title);
			}

			$tpl->assignAll($page);
			if ($auth->id == '115') {
				$tpl->newBlock('city-delete');
				$tpl->assign('id', $page->id);
			}
		}
	}
}

/* ----------------------------------------------------- */
//	 questlist & rediģēšana
/* ---------------------------------------------------- */

// update
else if ($_GET['var1'] == 'quest' && isset($_GET['var2'])) {
	$id = (int) $_GET['var2'];
	if ($db->get_var("SELECT count(*) FROM `rs_help` WHERE `page_id` = '$id' ") == 1) {
		$short_date = substr(sanitize($_POST['date']), -2, 2);
		$db->query("UPDATE `rs_help` SET
		  `location` = '" . sanitize($_POST['location']) . "',
		  `skills` = '" . sanitize($_POST['skills']) . "',
		  `quests` = '" . sanitize($_POST['quests']) . "',
		  `extra` = '" . sanitize($_POST['extra']) . "',
		  `date` = '" . sanitize($_POST['date']) . "',
		  `year` = '$short_date',
		  `difficulty` = '" . (int) $_POST['difficulty'] . "',
		  `length` = '" . (int) $_POST['length'] . "',
		  `storyline` = '" . (int) $_POST['storyline'] . "',
		  `ready` = '1',
		  `edit_user` = '" . $auth->id . "',
		  `description` = '" . sanitize($_POST['description']) . "',
		  `old` = '" . (int) $_POST['old'] . "'
		WHERE `page_id` = '$id'");
	}
	header("Location: /" . $_GET['viewcat'] . "/qedit");
} 

else if ($_GET['var1'] == 'qedit') {

	// dzēšana
	if (isset($_GET['delete']) && $auth->id == 115) {
		$id = (int) $_GET['delete'];
		$del = $db->query("DELETE FROM `rs_help` WHERE `page_id` = '$id' LIMIT 1");
		header("Location: /" . $_GET['viewcat'] . "/qedit/show");
	}


	// konkrēta kvesta/minikvesta rediģēšana
	if (isset($_GET['var2']) && $_GET['var2'] != 'update' && $_GET['var2'] != 'show') {
		$levels = array(array(1, 'Viegls'), array(2, 'Vidējs'), array(3, 'Grūts'), array(4, 'Master'), array(5, 'Grandmaster'), array(6, 'Special'));
		$length = array(array(1, 'Īss'), array(2, 'Vidējs'), array(3, 'Garš'), array(4, 'Ļoti garš'), array(5, 'Ļoti, ļoti garš'));
		$old = array(array(1, 'Need HD'), array(2, 'Need New'));

		$guide_id = (int) $_GET['var2'];
		if ($guide = $db->get_row("SELECT `id`,`strid`,`title` FROM `pages` WHERE `id` = '$guide_id' LIMIT 1")) {
			// AND `category` IN ('99','100','193')
			$info = $db->get_row("SELECT * FROM `rs_help` WHERE `page_id` = '$guide_id' ORDER BY `id` DESC");
			if ($info) {
				$tpl->newBlock('rsmod-questedit');
				$tpl->assignAll($info); // nemainīt vietām! svarīgi, kuru ID assigno pēdējo
				$tpl->assignAll($guide);
				// izvēlne ar Questu sērijām
				$storylines = $db->get_results("SELECT `id`,`title` FROM `rs_classes` WHERE `cat` = 'series' ORDER BY `title` ASC ");
				foreach ($storylines as $storyline => $data) {
					$tpl->newBlock('rsmod-guide-storyline');
					$tpl->assign(array(
						'nr' => $data->id,
						'storyline' => $data->title
					));
					if ($data->id == $info->storyline) {
						$tpl->assign('selected', ' selected="selected"');
					}
				}
				// izvēlne ar sarežģītības līmeņiem
				foreach ($levels as $level) {
					$tpl->newBlock('rsmod-guide-difficulty');
					$tpl->assign(array(
						'nr' => $level[0],
						'level' => $level[1]
					));
					if ($level[0] == $info->difficulty) {
						$tpl->assign('selected', ' selected="selected"');
					}
				}
				// izvēlne ar kvesta ilgumu
				foreach ($length as $single) {
					$tpl->newBlock('rsmod-guide-length');
					$tpl->assign(array(
						'nr' => $single[0],
						'length' => $single[1]
					));
					if ($single[0] == $info->length) {
						$tpl->assign('selected', ' selected="selected"');
					}
				}
				// kvests novecojis, need HD pics or sth
				foreach ($old as $older) {
					$tpl->newBlock('rsmod-guide-age');
					$tpl->assign(array(
						'nr' => $older[0],
						'old' => $older[1]
					));
					if ($older[0] == $info->old) {
						$tpl->assign('selected', ' selected="selected"');
					}
				}
				/* -- */
			} else {
				header("Location: /" . $_GET['viewcat'] . "/qedit");
			}
		} else {
			header("Location: /" . $_GET['viewcat'] . "/qedit");
		}
	}

	/* saraksts */ 
	else {
		$cats = array(array(100, 'members quests'), array(99, 'free quests'), array(193, 'miniquests'));
		$levels = array(1 => 'easy', 2 => 'medium', 3 => 'hard',
			4 => '<span style="color:#2777aa;text-transform:uppercase;">Master</span>',
			5 => '<span style="color:#e93546;text-transform:uppercase;">Grandmaster</span>',
			6 => '<span style="color:#e453e2;text-transform:uppercase;">Special</span>'
		);
		$diffs = array(1, 2, 3, 4, 5, 6);

		foreach ($cats as $cat) {
			$all_quests = $db->get_results("SELECT * FROM `rs_help` WHERE `cat` = '" . $cat[0] . "' ORDER BY `title` ASC ");
			if ($all_quests) {
				$tpl->newBlock('rsmod-questlist');
				$tpl->assign('cat-title', $cat[1]);
				foreach ($all_quests as $quest) {
					//$quest->ready = ($quest->ready == '1') ? '<img src="/bildes/rs/tick.png" />' : '<img src="/bildes/rs/cross.png" />';
					if ($user = get_user($quest->auth)) {
						$quest->auth = '<a style="font-size:11px;" href="' . mkurl('user', $user->id, $user->nick) . '">' . usercolor($user->nick, $user->level) . '</a>';
					}
					$tpl->newBlock('questlist-quest');
					$tpl->assignAll($quest);
					if (in_array($quest->difficulty, $diffs)) {
						$tpl->assign('level', $levels[$quest->difficulty]);
					}
					if (isset($_GET['var2']) && $_GET['var2'] == 'show' && $auth->id == '115') {
						$check = $db->get_var("SELECT count(*) FROM `pages` WHERE `id` = '$quest->page_id' AND `category` IN ('99','100','193') ");
						if ($check != 1) {
							$tpl->newBlock('quest-delete');
							$tpl->assign('page_id', $quest->page_id);
						}
					}
				}
			}
		}
	}
}


/* ----------------------------------------------------- */
//	 activities list
/* ---------------------------------------------------- */ 
else if ($_GET['var1'] == 'aedit' && $auth->id == 115) {

	// update
	if (isset($_GET['update'])) {
		$id = (int) $_GET['update'];
		if ($db->get_var("SELECT count(*) FROM `rs_help` WHERE `page_id` = '$id' ") == 1) {
			$db->query("UPDATE `rs_help` SET
				`location` = '" . sanitize($_POST['location']) . "',
				`p2p` = '" . (int) $_POST['members'] . "',
				`ready` = '1',
				`edit_user` = '$auth->id',
				`description` = '" . sanitize($_POST['description']) . "',
				`old` = '" . (int) $_POST['old'] . "'
			  WHERE `page_id` = '$id'");
		}
		header("Location: /" . $_GET['viewcat'] . "/aedit");
	}
	// dzēšana
	if (isset($_GET['delete']) && $auth->id == '115') {
		$id = (int) $_GET['delete'];
		$del = $db->query("DELETE FROM `rs_help` WHERE `page_id` = '$id' LIMIT 1");
		header("Location: /" . $_GET['viewcat'] . "/aedit/show");
	}
	// rediģēšanas forma
	if (isset($_GET['var2']) && $_GET['var2'] != 'update' && $_GET['var2'] != 'show') {
		$old = array(array(1, 'Need HD'), array(2, 'Need New'));
		$guide_id = (int) $_GET['var2'];
		if ($guide = $db->get_row("SELECT `id`,`strid`,`title` FROM `pages` WHERE `id` = '$guide_id' LIMIT 1")) {
			$info = $db->get_row("SELECT * FROM `rs_help` WHERE `page_id` = '$guide_id' ");
			if ($info) {
				$tpl->newBlock('rsmod-activities-edit');
				$tpl->assignAll($info); // nemainīt vietām! svarīgi, kuru ID assigno pēdējo
				$tpl->assignAll($guide);
				if ($info->p2p == '1') {
					$tpl->assign('selected-members', ' selected="selected"');
				}
				foreach ($old as $older) {
					$tpl->newBlock('activity-age');
					$tpl->assign(array(
						'nr' => $older[0],
						'old' => $older[1]
					));
					if ($older[0] == $info->old) {
						$tpl->assign('selected', ' selected="selected"');
					}
				}
				/* -- */
			} else {
				header("Location: /" . $_GET['viewcat'] . "/aedit");
			}
		} else {
			header("Location: /" . $_GET['viewcat'] . "/aedit");
		}
	}
	// saraksts
	else {
		$cats = array(array(792, 'Distractions & Diversions'), array(160, 'minigames'));
		foreach ($cats as $cat) {
			$activities = $db->get_results("SELECT * FROM `rs_help` WHERE `cat` = '" . $cat[0] . "' ORDER BY `title` ASC ");
			if ($activities) {
				$tpl->newBlock('rsmod-activities');
				$tpl->assign('cat-title', $cat[1]);
				foreach ($activities as $activity) {
					if ($user = get_user($activity->auth)) {
						$activity->auth = '<a style="font-size:11px;" href="' . mkurl('user', $user->id, $user->nick) . '">' . usercolor($user->nick, $user->level) . '</a>';
					}
					$tpl->newBlock('activity');
					$tpl->assignAll($activity);
					if (isset($_GET['var2']) && $_GET['var2'] == 'show' && $auth->id == '115') {
						$check = $db->get_var("SELECT count(*) FROM `pages` WHERE `id` = '$activity->page_id' AND `category` IN ('160','792') ");
						if ($check == 1) {
							$tpl->newBlock('activity-delete');
							$tpl->assign('page_id', $activity->page_id);
						}
					}
				}
			}
		}
	}
}
	
	
/* ----------------------------------------------------- */
//	 update
/* ---------------------------------------------------- */ 
else if ($_GET['var1'] == 'update' && in_array($auth->id,array(115,140))) {
	$pages = $db->get_results("SELECT `page_id`,`strid`,`title`,`auth` FROM `rs_help` ");
	if ($pages) {
		foreach ($pages as $page) {
			$check = $db->get_row("SELECT `id`,`strid`,`title`,`author` FROM `pages` WHERE `id` = '$page->page_id' LIMIT 1");
			if ($check) {
				$string = array();
				if ($check->title != $page->title) {
					$string[] = "`title` = '" . sanitize($check->title) . "'";
				}
				if ($check->strid != $page->strid) {
					$string[] = "`strid` = '" . sanitize($check->strid) . "'";
				}
				if ($check->author != $page->auth) {
					$string[] = "`auth` = '" . (int) $check->author . "'";
				}
				if (!empty($string)) {
					$upd = $db->query("UPDATE `rs_help` SET " . implode(',', $string) . " WHERE `page_id` = '$page->page_id' ");
				}
			}
		}
	}
}
/* ------------------------------------------------- */
if ($_GET['var1'] == 'warns' && $auth->id == 115) {
	$warns = $db->get_results("SELECT `user_id`,`created_by`,`reason` FROM `warns` ORDER BY `id` DESC LIMIT 0,100");
	if ($warns) {
		$tpl->newBlock('rsmod-warns');
		foreach ($warns as $warn) {
			$tpl->newBlock('rsmod-warn');
			if ($user = get_user($warn->user_id)) {
				$warn->user_id = '<a style="font-size:11px;" href="' . mkurl('user', $user->id, $user->nick) . '">' . usercolor($user->nick, $user->level) . '</a>';
			}
			if ($user = get_user($warn->created_by)) {
				$warn->created_by = '<a style="font-size:11px;" href="' . mkurl('user', $user->id, $user->nick) . '">' . usercolor($user->nick, $user->level) . '</a>';
			}
			$tpl->assignAll($warn);
		}
	}
}


	
/* ------------------------------------------------- */
} // end of if(isset($_GET['var1']))
} // end of - lietotāju piekļuves pārbaude
else {
	redirect();
}
?>