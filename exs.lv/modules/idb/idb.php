<?php

/* -------------------------------------------------------------
 * 		RuneScape priekšmetu datubāze
  / */
if (!$auth->ok) {
	redirect();
	exit;
} /* else if (!in_array($auth->id,array(115,21018,703,140))) {
  set_flash("Priekšmetu datubāze tiek pārtaisīta! Piekāp citreiz!");
  redirect();
  exit;
  } */

$idbusr = $db->get_row("SELECT * FROM `idb_users` WHERE `user` = '" . $auth->id . "' ");
if (!$idbusr || $idbusr->allowed == 0) {
	set_flash('Pieeja RuneScape priekšmetu datubāzei liegta!');
	redirect();
	exit;
} else {


	/* ---------------------------------------------------------
	 * 		galvenie iestatījumi un opšeni, un variables
	  / */

	/*
	  todo:
	  dziru efekti nav mainījušies?
	  caurskatīt pēdējos tulkojumus
	  update vārdnīcu
	 */

	$tpl->assignInclude('module-head', 'modules/' . $category->module . '/header.tpl');
	$tpl->prepare();
	$tpl->assign('skinid', (($auth->skin == 1) ? 1 : 0));

	$translated = $db->get_var("SELECT count(*) FROM `idb` WHERE `tolv` = '1' AND `oldrs` = '0' ");
	$percents = round($translated / 10308 * 100, 2);

	$tpl->newBlock('idb-navig');
	$tpl->assign(array(
		'amount' => $translated,
		'percents' => $percents
	));
	if ($idbusr->mod == 1) {
		$tpl->newBlock('navig-mods');
	}
	if ($idbusr->app == 1) {
		$tpl->newBlock('navig-app');
	}
	if (isset($_GET['var1'])) {
		$tpl->newBlock('idb-content');
	}

	$items_mainlist = 15;  // cik priekšmetu sākumlapas vienā kolonnā


	/* ----------------------------------------
	 * 		jQuery checkbox pārbaude
	  / */
	if (isset($_GET['var1']) && $_GET['var1'] == 'checkbox') {
		if (isset($_SESSION['chbx'])) {
			unset($_SESSION['chbx']);
		} else {
			$_SESSION['chbx'] = true;
		}
		exit;
	}




	/* -----------------------------------------------------------------------------------------------------------
	 * 		atjauno lists db sākumlapā, pārvietojoties pa lapām (jaunākie, pēdējie atjaunotie utt. priekšmetu stabiņi)
	  / */
	if (isset($_GET['var1']) && (in_array($_GET['var1'], array('jqnew', 'jqupd', 'jqapp'))) && isset($_GET['var2'])) {

		$page = (int) $_GET['var2'];
		$type = $_GET['var1'];
		$title = ($type == 'jqnew') ? 'Jaunākie iztulkotie šķirkļi' : ( ($type == 'jqupd') ? 'Nesen atjaunotie šķirkļi' : 'Nesen apstiprinātie šķirkļi' );
		$start_pos = ($page > 0 && $page < 6) ? $items_mainlist * ($page - 1) : 0;

		if ($type == 'jqnew') {
			$get_items = $db->get_results("SELECT `strid`,`item` FROM `idb` WHERE `oldrs` = '0' AND `tolv` = '1' ORDER BY `atime` DESC LIMIT  $start_pos,$items_mainlist");
		} else if ($type == 'jqupd') {
			$get_items = $db->get_results("SELECT `strid`,`item` FROM `idb` WHERE `oldrs` = '0' AND `ecount` > '0' ORDER BY `etime` DESC LIMIT $start_pos,$items_mainlist");
		} else {
			$get_items = $db->get_results("SELECT `strid`,`item` FROM `idb` WHERE `oldrs` = '0' AND `appuser` != '0' ORDER BY `apptime` DESC LIMIT $start_pos,$items_mainlist");
		}

		if ($get_items) {
			$ret = '<li class="list_main_title">' . $title . '</li>';
			$counter = $start_pos + 1;
			foreach ($get_items as $item) {
				if (strlen($item->item) > 33) {
					$item->item = substr($item->item, 0, 30) . '...';
				}
				$ret .= '<li><span class="list_nr">' . $counter . '</span> <a href="/db/' . $item->strid . '">' . $item->item . '</a></li>';
				$counter++;
			}
			$ret .= '<li class="' . $type . ' pagerow">';
			for ($i = 1; $i < 6; $i++) {
				if ($i == 5) {
					$ret .= '<a class="idb-next-last" href="/db/' . $type . '/' . $i . '">' . $i . '</a>&nbsp;';
				} else
					$ret .= '<a class="idb-next" href="/db/' . $type . '/' . $i . '">' . $i . '</a>&nbsp;';
			}
			$ret .= '</li>';
			echo $ret;
		} else {
			echo '<p style="margin:50px 0 0 0;font-weight:bold;color:red;">Error loading data!</p>';
		}
		exit;
	}




	/* --------------------------------
	 * 		jquery meklētājs
	  / */ else if (isset($_GET['var1']) && $_GET['var1'] == 'search') {

		if (isset($_GET['q']) && !empty($_GET['q'])) {

			//$string 	= str_replace(array(',', '.', '+', '-', '_'), ' ', $_GET['q']);
			$string = str_replace(array(',', '.', '_', '%'), ' ', $_GET['q']);
			$string = strip_tags($string);
			$string = str_replace(array('  ', '   '), ' ', $string);
			$tpl->assign('qstr', htmlspecialchars($string));
			$string = explode(' ', $string);

			// masīvs ar meklēšanas nosacījumiem
			$cond = array();
			foreach ($string as $str) {
				$str = strtolower($str);
				$cond[] = "`item` LIKE '%" . sanitize($str) . "%'";
			}
			// vai rādīt tikai neiztulkotos?
			if (isset($_SESSION['chbx'])) {
				$translated = ' AND `tolv` = \'0\'';
			} else
				$translated = '';

			// lappušu skaits
			$results_count = $db->get_var("SELECT count(*) FROM `idb` WHERE " . implode(' AND ', $cond) . " AND `oldrs` = '0'$translated");
			$start_pos = 0;
			if ($results_count) {
				$page_count = ceil($results_count / 50);
			} else
				$page_count = 1;

			if (isset($_GET['var2']) && is_numeric($_GET['var2'])) {
				if ($_GET['var2'] > $page_count || $_GET['var2'] < 1) {
					$start_pos = 0;
				} else {
					$start_pos = 50 * ((int) $_GET['var2'] - 1);
				}
			}

			// pēc kritērijiem atlasītie priekšmeti
			$results = $db->get_results("SELECT `strid`,`item`,`img` FROM `idb` WHERE " . implode(' AND ', $cond) . " AND `oldrs` = '0'$translated ORDER BY `item` ASC LIMIT $start_pos,50");
			if ($results) {

				// rinda ar lapām augšpusē
				$ret = '<div id="list-header">';
				$ret .= '<p id="header-left">Atrastie priekšmeti (' . $results_count . ')</p>';
				if ($page_count > 1) {
					$ret .= '<ul id="search-pages">';
					$ret .= display_pages(($start_pos / 50 + 1), $page_count, '/db/search', '/?q=' . $_GET['q']);
					$ret .= '</ul>';
				}
				$ret .= '</div><ul id="result-list">';
				// rezultāti (priekšmeti)
				foreach ($results as $result) {

					$result->item = strip_tags($result->item);
					foreach ($string as $str) {
						$result->item = str_replace($str, '<strong>' . htmlspecialchars($str) . '</strong>', $result->item);
						$result->item = str_replace(ucfirst($str), '<strong>' . htmlspecialchars(ucfirst($str)) . '</strong>', $result->item);
					}
					//$result->item = ucfirst($result->item);

					$ret .= '<li><img src="/dati/idb/' . $result->img . '" title="" alt="" />';
					$ret .= '<a href="/db/' . $result->strid . '">' . $result->item . '</a>';
					$ret .= '<a style="float:right;"  href="/db/' . $result->strid . '/edit">
						<span class="list-hl">[ labot ]</span></a>';
					$ret .= '</li>';
				}
				$ret .= '</ul>';

				// rinda ar lapām apakšpusē
				if ($page_count > 1) {

					$ret .= '<br /><div id="idb_pages">';
					$counter = 1;
					$criteria = (isset($_GET['q'])) ? "?q=" . $_GET['q'] : "";

					while ($counter <= $page_count) {

						$hl = $counter;
						// ja adresē nav norādīta lappuse, izceļ pirmo lappusi; ja ir, izceļ norādīto
						if (!isset($_GET['var2']) && $counter == 1) {
							$hl = "<span class=\"lpp-hl\"><b>" . $counter . "</b></span>";
						} else if (isset($_GET['var2']) && $counter == $_GET['var2']) {
							$hl = "<span class=\"lpp-hl\"><b>" . $counter . "</b></span>";
						}

						$ret .= '<a class="bottom-page" href="/db/search/' . $counter . '/' . $criteria . '">' . $hl . '</a>&nbsp;&nbsp;';
						if ($counter % 20 == 0) {
							$ret .= '<br />';
						}
						$counter++;
					}
					$ret .= '</div><br />';
				}
				echo json_encode(
						array("state" => 'success', "content" => $ret)
				);
			} else {
				// print error: no results
				echo json_encode(
						array("state" => 'failure1', "content" => '<p id="no-results">Pēc šādiem kritērijiem netika atrasts neviens priekšmets!</p>')
				);
			}
		} else {
			// return to homepage
			echo json_encode(
					array("state" => 'failure2', "content" => 'other')
			);
		}
		exit;
	}





	/* ------------------------------------------------------------------------
	 * 		iztulkotie/neiztulkotie priekšmeti, to saraksti + jquery
	  / */ else if (isset($_GET['var1']) && in_array($_GET['var1'], array('unlisted', 'listed', 'junlisted', 'jlisted'))) {

		// mainīgie
		switch ($_GET['var1']) {
			case 'unlisted':  // neiztulkotie
				$type = 0;
				$tolv = 0;
				$typename = 'junlisted';
				$title = 'Vēl neiztulkotie priekšmetu šķirkļi';
				break;
			case 'junlisted':  // neiztulkotie; pieprasījums ar ajax'u
				$type = 1;
				$tolv = 0;
				$typename = 'junlisted';
				$title = 'Vēl neiztulkotie priekšmetu šķirkļi';
				break;
			case 'jlisted':   // iztulkotie; pieprasījums ar ajax'u
				$type = 1;
				$tolv = 1;
				$typename = 'jlisted';
				$title = 'Jau iztulkotie priekšmetu šķirkļi';
				break;
			default: // iztulkotie
				$type = 0;
				$tolv = 1;
				$typename = 'jlisted';
				$title = 'Jau iztulkotie priekšmetu šķirkļi';
				break;
		};

		// lappuses
		$item_count = $db->get_var("SELECT count(*) FROM `idb` WHERE `oldrs` = '0' AND `tolv` = '" . $tolv . "' ");
		$page_count = ceil($item_count / 50);
		$start_pos = 0;

		if (isset($_GET['var2']) && is_numeric($_GET['var2']) && $_GET['var2'] <= $page_count && $_GET['var2'] > 0) {
			$start_pos = 50 * ((int) $_GET['var2'] - 1);
		}

		// rezultāti
		$items = $db->get_results("SELECT `item`,`strid`,`img`,`asg` FROM `idb` WHERE `oldrs` = '0' AND `tolv` = '" . $tolv . "' ORDER BY `item` ASC LIMIT  $start_pos,50 ");
		if ($items) {

			if ($type == 0) {
				$tpl->newBlock('list');
				$tpl->assign('list-title', $title);

				if ($page_count > 1) {
					$tpl->newBlock('list-pages');
					$pagerow = display_pages(($start_pos / 50 + 1), $page_count, '/db/' . $typename, '', 'list-page');
					$tpl->assign('pagerow', $pagerow);
				}
			} else {

				$ret = '<div class="idb-title idb-red">' . $title;

				// rinda ar lapām augšpusē
				//$ret = '<div id="list-header">';
				if ($page_count > 1) {
					$ret .= '<ul id="search-pages">';
					$ret .= display_pages(($start_pos / 50 + 1), $page_count, '/db/' . $typename, '', 'list-page');
					$ret .= '</ul>';
				}
				$ret .= '</div><ul id="result-list">';
			}

			foreach ($items as $item) {
				if ($type == 0) {
					$tpl->newBlock('single-item');
					$tpl->assignAll($item);
					if (($idbusr->app == 0 || $tolv == 0) && $item->asg == 0) {
						$tpl->newBlock('single-item-modedit');
						$tpl->assign('strid', $item->strid);
					} else if ($item->asg == 1) {
						$tpl->newBlock('single-item-asg');
					}
				} else {
					$ret .= '<li><img src="/dati/idb/' . $item->img . '" title="' . $item->item . '" alt="" />';
					$ret .= '<a href="/db/' . $item->strid . '">' . $item->item . '</a>';
					if (($idbusr->app == 0 || $tolv == 0) && $item->asg == 0) {
						$ret .= '<a style="float:right;" href="/db/' . $item->strid . '/edit"><span class="idb-red">[ labot ]</span></a>';
					} else if ($item->asg == 1) {
						$ret .= '<a style="float:right;" href="#"><span class="idb-blue">[ iesniegts ]</span></a>';
					}
					$ret .= '</li>';
				}
			}
			if ($type == 1) {
				$ret .= '</ul><br />';
			}

			// saraksts ar lappusēm
			if ($page_count > 1) {
				$counter = 1;
				if ($type == 1) {
					$ret .= '<div id="idb_pages">';
				}

				while ($counter <= $page_count) {

					$highlight = $counter;

					// ja adresē nav norādīta lappuse, izceļ pirmo lappusi; ja ir, izceļ norādīto;
					if (!isset($_GET['var2']) && $counter == 1) {
						$highlight = "<b>" . $counter . "</b>";
					} else if (isset($_GET['var2']) && $counter == $_GET['var2']) {
						$highlight = "<b>" . $counter . "</b>";
					}

					if ($type == 0) {
						$tpl->newBlock('item-page');
						$tpl->assign(array(
							'page-link' => $counter,
							'page-nr' => $highlight,
							'type' => $typename
						));
					} else {
						$ret .= '<a class="list-page" href="/db/' . $typename . '/' . $counter . '">' . $highlight . '</a>&nbsp;&nbsp;';
					}
					$counter++;
				}
				if ($type == 1) {
					$ret .= '</div>';
				}
			}

			if ($type == 1) {
				echo $ret;
				exit;
			}
		} else {
			if ($type == 1) {
				echo '<p class="idb-red">Kļūda!</p>';
				exit;
			}
		}
		if ($tolv == 0) {
			$page_title = htmlspecialchars('Neiztulkotie priekšmeti | RuneScape datubāze');
		} else {
			$page_title = htmlspecialchars('Iztulkotie priekšmeti | RuneScape datubāze');
		}
	}



	/* -----------------------------------------
	 * 		mazā meklētāja ajax rezultāti
	  / */ else if (isset($_GET['var1']) && $_GET['var1'] == 'ssearch') {

		if (isset($_GET['q']) && !empty($_GET['q'])) {

			$string = str_replace(array(',', '.', '_', '%'), ' ', $_GET['q']);
			$string = strip_tags($string);
			$string = str_replace(array('  ', '   '), ' ', $string);
			$tpl->assign('qstr', htmlspecialchars($string));
			$string = explode(' ', $string);

			// masīvs ar meklēšanas nosacījumiem
			$cond = array();
			foreach ($string as $str) {
				$str = strtolower($str);
				$cond[] = "`item` LIKE '%" . sanitize($str) . "%'";
			}

			// lappušu skaits
			$results_count = $db->get_var("SELECT count(*) FROM `idb` WHERE " . implode(' AND ', $cond) . " AND `oldrs` = '0'");
			$start_pos = 0;
			if ($results_count) {
				$page_count = ceil($results_count / 15);
			} else
				$page_count = 1;

			if (isset($_GET['var2']) && is_numeric($_GET['var2'])) {
				if ($_GET['var2'] > $page_count || $_GET['var2'] < 1) {
					$start_pos = 0;
				} else {
					$start_pos = 15 * ((int) $_GET['var2'] - 1);
				}
			}
			$current_page = ($start_pos + 15) / 15;

			// pēc kritērijiem atlasītie priekšmeti
			$results = $db->get_results("SELECT `strid`,`item`,`img` FROM `idb` WHERE " . implode(' AND ', $cond) . " AND `oldrs` = '0' ORDER BY `item` ASC LIMIT $start_pos,15");
			if ($results) {

				// rezultāti (priekšmeti)
				foreach ($results as $result) {

					$itemname = $result->item;
					if (strlen($result->item) > 33) {
						$result->item = substr($result->item, 0, 30) . '...';
					}

					$result->item = strip_tags($result->item);
					foreach ($string as $str) {
						$result->item = str_replace($str, '<strong>' . htmlspecialchars($str) . '</strong>', $result->item);
						$result->item = str_replace(ucfirst($str), '<strong>' . htmlspecialchars(ucfirst($str)) . '</strong>', $result->item);
					}
					$ret .= '<img src="/dati/idb/' . $result->img . '" title="" alt="" />';
					$ret .= '<a title="' . $itemname . '" href="/db/' . $result->strid . '">' . $result->item . '</a><br />';
				}

				// nākamā lappuse
				if ($current_page < $page_count) {
					$gonext = '<a class="form-page" href="/db/ssearch/' . ($current_page + 1) . '?q=' . $_GET['q'] . '"><img src="/modules/idb/images/right.png" title="Tālāk" alt="" /></a>';
				} else
					$gonext = '';

				// iepriekšējā lappuse
				if ($current_page > 1) {
					$goback = '<a class="form-page page-back" href="/db/ssearch/' . ($current_page - 1) . '?q=' . $_GET['q'] . '"><img src="/modules/idb/images/left.png" title="Atpakaļ" alt="" /></a>';
				} else
					$goback = '';


				echo json_encode(
						array("state" => 'success', "content" => $ret, "pages" => 'true', "next" => $gonext, "prev" => $goback)
				);
			} else {
				echo json_encode(
						array("state" => 'null', "content" => '<p id="no-results">Pēc šādiem kritērijiem netika atrasts neviens priekšmets!</p>', "pages" => 'false')
				);
			}
		} else {
			echo json_encode(
					array("state" => 'null', "content" => 'Error!', "pages" => 'false')
			);
		}
		exit;
	}



	/* -------------------------------------
	 * 		kind of iekšējā vārdnīca
	  / */ else if (isset($_GET['var1']) && $_GET['var1'] == 'help') {
		$tpl->newBlock('idb-help');
		$page_title = htmlspecialchars('RS vārdnīca | RuneScape datubāze');
	}




	/* -------------------------------------------------------------------------
	 * 		konkrēta lietotāja tulkojumi [tikai konkursa nedēļā, ja norāda ?week=1]
	  / */ else if (isset($_GET['var1']) && $_GET['var1'] == 'stats' && isset($_GET['var2'])) {

		$user = (int) $_GET['var2'];
		$check = $db->get_row("SELECT * FROM `idb_users` WHERE `user` = '" . $user . "' ");
		if (!$check) {
			set_flash("Kļūda! Nepareizi norādīts lietotājs!");
			redirect('/db/stats');
			exit;
		}
		if (isset($_GET['week'])) {
			$week_end = date("Y-m-d 23:59:59", strtotime('this week', time()) + 86400 * 6);
			$sql = " AND `atime` > '" . sanitize($idbusr->thisweek) . "' AND `atime` < '" . sanitize($week_end) . "'";
		} else
			$sql = '';

		// priekšmetu saraksta lappušu skaits
		$item_count = $db->get_var("SELECT count(*) FROM `idb` WHERE `oldrs` = '0' AND `asg` = '0' AND `auser` = '" . $user . "'" . $sql . " ");
		$page_count = ceil($item_count / 100);
		$items_start = 0;
		if (isset($_GET['page']) && is_numeric($_GET['page'])) {
			if ($_GET['page'] > $page_count || $_GET['page'] < 0) {
				$items_start = 0;
			} else {
				$items_start = 100 * ($_GET['page'] - 1);
			}
		}

		// priekšmetu saraksts
		$items = $db->get_results("SELECT `item`,`strid`,`img` FROM `idb` WHERE `oldrs` = '0' AND `asg` = '0' AND `auser` = '" . $user . "'" . $sql . " ORDER BY `item` ASC LIMIT $items_start, 100");
		if ($items) {

			$tpl->newBlock('itemsuser');
			$tpl->assign(array('items_count' => $item_count, 'show_items_count' => ''));

			$type = 0;
			foreach ($items as $item) {

				if ($type == 0) {
					$tpl->newBlock('useritem');
					$type = 1;
				} else
					$type = 0;

				$img = '<img src="/dati/idb/' . $item->img . '" title="' . $item->item . '" alt="" />';
				$tpl->newBlock('useritem-data');
				$tpl->assign(array(
					'name' => $item->item,
					'img' => $img,
					'strid' => $item->strid
				));
			}
			$counter = 1;
			while ($counter <= $page_count) {

				$highlight = $counter;
				// ja adresē nav norādīta lappuse, izceļ pirmo lappusi; ja ir, izceļ norādīto;
				if ((!isset($_GET['page']) && $counter == 1) || (isset($_GET['page']) && $counter == $_GET['page'])) {
					$highlight = "<b>" . $counter . "</b>";
				}

				$pagelink = (isset($_GET['week'])) ? $counter . '&amp;week=1' : $counter;

				$tpl->newBlock('useritem-page');
				$tpl->assign(array(
					'page-link' => $pagelink,
					'page-nr' => $highlight,
					'user' => $user
				));
				$counter++;
			}
		} else {
			$tpl->newBlock('error-no-items');
		}
		$page_title = htmlspecialchars('Lietotāja tulkojumi | RuneScape datubāze');
	}





	/* -------------------------------------
	 * 		statistika
	  / */ else if (isset($_GET['var1']) && $_GET['var1'] == 'stats') {

		$week_start = date("Y-m-d 00:00:00", strtotime('this week', time()));
		$week_end = date("Y-m-d 23:59:59", strtotime('this week', time()) + 86400 * 6);
		$last_week_start = date("Y-m-d 00:00:00", strtotime('last week', time()));
		$last_week_end = date("Y-m-d 23:59:59", strtotime('last week', time()) + 86400 * 6);

		// jāatjauno nedēļas statistikas skaitītājs, ja sākusies jauna nedēļa
		if ($week_start != $idbusr->thisweek || isset($_GET['upd'])) {
			$idbusers = $db->get_results("SELECT `id`,`user` FROM `idb_users` ORDER BY `nick` ASC");
			if ($idbusers) {
				foreach ($idbusers as $idbuser) {
					$icount = $db->get_var("SELECT count(*) FROM `idb` WHERE `auser` = '" . $idbuser->user . "' AND `oldrs` = '0' AND `asg` = '0'");
					$tcount = $db->get_var("SELECT count(*) FROM `idb` WHERE `auser` = '" . $idbuser->user . "' AND `atime` < '" . sanitize($week_end) . "' AND `atime` > '" . sanitize($week_start) . "' AND `oldrs` = '0' AND `asg` = '0' ");
					$lcount = $db->get_var("SELECT count(*) FROM `idb` WHERE `auser` = '" . $idbuser->user . "' AND `atime` < '" . sanitize($last_week_end) . "' AND `atime` > '" . sanitize($last_week_start) . "' AND `oldrs` = '0' AND `asg` = '0' ");
					$upd = $db->query("UPDATE `idb_users` SET `items` = '" . $icount . "', `tcount` = '" . $tcount . "',`lcount` = '" . $lcount . "',`thisweek` = '" . sanitize($week_start) . "',`lastweek` = '" . sanitize($last_week_start) . "' WHERE `id` = '" . $idbuser->id . "' ");
				}
			}
		}

		$twheader = date("d M", strtotime('this week', time())) . ' - ' . date("d M", strtotime('this week', time()) + 86400 * 6);
		$lwheader = date("d M", strtotime('last week', time())) . ' - ' . date("d M", strtotime('last week', time()) + 86400 * 6);
		// headera dati
		$prev_count = $db->get_var("SELECT count(*) FROM `idb` WHERE `atime` > '" . sanitize($last_week_start) . "' AND `atime` < '" . sanitize($last_week_end) . "' AND `oldrs` = '0' AND `asg` = '0'");
		$this_count = $db->get_var("SELECT count(*) FROM `idb` WHERE `atime` > '" . sanitize($week_start) . "' AND `atime` < '" . sanitize($week_end) . "' AND `oldrs` = '0' AND `asg` = '0'");

		$tpl->newBlock('itemcontest');
		$tpl->assign(array(
			'prev_count' => $prev_count,
			'this_count' => $this_count,
			'prev_text' => $lwheader,
			'this_text' => $twheader
		));

		$users = $db->get_results("SELECT `id`,`user`,`nick`,`items`,`tcount`,`lcount` FROM `idb_users` WHERE `items` != '0' ORDER BY ABS(`items`) DESC, `nick` ASC");
		if ($users) {
			foreach ($users as $user => $data) {
				if ($userdata = get_user($data->user)) {

					$data->tcount = ($data->tcount == 0) ? '--' : $data->tcount;
					$data->lcount = ($data->lcount == 0) ? '--' : $data->lcount;

					$data->nick = '<a href="' . mkurl('user', $userdata->id, $userdata->nick) . '">' . usercolor($userdata->nick, $userdata->level) . '</a>';
					$tpl->newBlock('contest-node');
					$tpl->assignAll($data);
				}
			}
		}
		$page_title = htmlspecialchars('Datubāzes statistika');
	}



	/* -----------------------------------------------
	 * 		priekšmeta info resetošana (only me)
	  / */ else if (isset($_GET['var1']) && $_GET['var1'] == 'reset' && isset($_GET['var2']) && $auth->id == 115) {

		$item = $db->get_row("SELECT `id`,`appuser`,`auser`,`strid`,`tolv` FROM `idb` WHERE `strid` = '" . sanitize($_GET['var2']) . "' AND `oldrs` = '0' ");
		if ($item) {

			// ja priekšmets ticis apstiprināts
			if ($item->appuser != 0) {

				$upd = $db->query("UPDATE `idb` SET `tolv` = '0',`asg` = '0',`atime` = '0000-00-00 00:00:00',`auser` = '0',`etime` = '0000-00-00 00:00:00',`euser` = '0',`ecount` = '0',`appuser` = '0',`apptime` = '0000-00-00 00:00:00',`lvlocation` = '',`lvuses` = '',`lvnotes` = '' WHERE `id` = '" . $item->id . "' LIMIT 1");
				$getid = $db->get_row("SELECT `idb_approve`.`id` FROM `idb_approve`,`idb` WHERE `idb`.`strid` = '" . sanitize($_GET['var2']) . "' AND `idb`.`id` = `idb_approve`.`itemid` ORDER BY `idb_approve`.`atime` DESC LIMIT 0,1");
				if ($getid) {
					$upd2 = $db->query("UPDATE `idb_approve` SET `app` = '2',`reset` = '1' WHERE `id` = '" . $getid->id . "' LIMIT 1");
				}

				update_weekly($item->auser);
				set_flash('Priekšmets veiksmīgi resetots! (app - ' . $item->strid . ' - ' . $item->id . ')');
				redirect('/db/' . $item->strid);
				exit;
			}
			// ja priekšmets ticis uzreiz pievienots
			else if ($item->tolv == 1) {
				$upd = $db->query("UPDATE `idb` SET `tolv` = '0',`asg` = '0',`atime` = '0000-00-00 00:00:00',`auser` = '0',`etime` = '0000-00-00 00:00:00',`euser` = '0',`ecount` = '0',`appuser` = '0',`apptime` = '0000-00-00 00:00:00',`lvlocation` = '',`lvuses` = '',`lvnotes` = '' WHERE `id` = '" . $item->id . "' LIMIT 1");

				update_weekly($item->auser);
				set_flash('Priekšmets veiksmīgi resetots! (noapp - ' . $item->strid . ' - ' . $item->id . ')');
				redirect('/db/' . $item->strid);
				exit;
			} else {
				set_flash("Priekšmets nav iztulkots! Nevar resetot!");
				redirect('/db/' . $item->strid);
				exit;
			}
		} else {
			set_flash('Kļūda! Tāds priekšmets netika atrasts!');
			redirect('/db');
			exit;
		}
	}


	/* ----------------------------------------------------------
	 * 		pēdējo priekšmetu rediģēšana vienlaicīgi
	  / */ else if (isset($_GET['var1']) && $_GET['var1'] == 'editor' && $auth->id == 115) {

		$start = 0;
		if (isset($_GET['var2']) && is_numeric($_GET['var2'])) {
			$start = ($_GET['var2'] - 1) * 2;
		}
		if (isset($_GET['user'])) {
			$userid = '`idb`.`auser` = \'' . (int) $_GET['user'] . '\' AND ';
			$usr = '?user=' . (int) $_GET['user'];
		} else {
			$userid = '';
			$usr = '';
		}

		$items = $db->get_results("SELECT `idb`.`item`,`idb`.`lvlocation`,`idb`.`location`,`idb`.`lvuses`,`idb`.`uses`,`idb`.`lvnotes`,`idb`.`notes`,`idb`.`id`,`users`.`nick` AS `auser` FROM `idb`,`users` WHERE `idb`.`oldrs` = '0' AND `idb`.`tolv` = '1' AND " . $userid . "`idb`.`auser` = `users`.`id` ORDER BY `idb`.`atime` DESC LIMIT $start,2");

		if ($items) {

			if (isset($_POST['submit'])) {

				$fields = array('lvlocation', 'lvuses', 'lvnotes');
				$count = 0;

				foreach ($items as $item) {

					$save = array();

					foreach ($fields as $field) {
						if (isset($_POST[$item->id . '-' . $field])) {
							$save[] = '`' . $field . '` = \'' . sanitize(trim($_POST[$item->id . '-' . $field])) . '\'';
						}
					}
					if (isset($_POST[$item->id . '-auser'])) {
						//echo '<strong>'.$item->item.'</strong> ('.$item->id.'-'.$field.'): auth true<br />';
						$save[] = '`auser` = \'115\'';
					}
					/* if (isset($_POST[$item->id.'-reset'])) {
					  echo '<strong>'.$item->item.'</strong> ('.$item->id.'-'.$field.'): reset true<br />';
					  } */

					$res = implode(', ', $save);
					if (!empty($res)) {
						$count++;
						//echo $res.' '.$count.'<br /><br />';
						$upd = $db->query("UPDATE `idb` SET " . $res . " WHERE `id` = '" . $item->id . "' LIMIT 1");
						//var_dump($res);
					}
				}
				set_flash("Izmaiņas veiktas " . $count . " priekšmetu info");
				if (isset($_GET['var2'])) {
					redirect('/db/editor/' . (int) $_GET['var2'] . '/' . $usr);
				} else {
					redirect('/db/editor/' . $usr);
				}
				exit;
			} else {

				$tpl->newBlock('idb-editor');
				if (isset($_GET['var2'])) {
					$tpl->assign('page', (int) $_GET['var2']) . '/';
				}
				if (isset($_GET['user'])) {
					$tpl->assign('user', '?user=' . (int) $_GET['user']);
				}

				foreach ($items as $item) {

					$data['lvlocation-area'] = $item->lvlocation;
					$data['lvuses-area'] = $item->lvuses;
					$data['lvnotes-area'] = $item->lvnotes;

					$item = itemsdb_replace($item, 1);

					$tpl->newBlock('editor-view');
					$tpl->assignAll($item);
					$tpl->assign(array(
						'lvlocation-area' => $data['lvlocation-area'],
						'lvuses-area' => $data['lvuses-area'],
						'lvnotes-area' => $data['lvnotes-area']
					));
				}
			}
		}
	}



	/* -----------------------------------------
	 * 		apstiprināmie priekšmeti
	  / */ else if (isset($_GET['var1']) && $_GET['var1'] == 'queue' && $idbusr->mod == 1) {

		// izvēlēts konkrēts priekšmets (maybe)
		if (isset($_GET['var2'])) {

			$item = $db->get_row("
			SELECT 
				`idb`.`item`,`idb`.`img`,`idb`.`strid`,`idb`.`id`,
				`idb`.`location`,`idb`.`uses`,`idb`.`notes`,`idb`.`examine`,
				`idb`.`members`,`idb`.`stacks`,`idb`.`equips`,`idb`.`quest`,`idb`.`trade`,`idb`.`weight`,
				`idb_approve`.`lvlocation`,`idb_approve`.`lvuses`,`idb_approve`.`lvnotes`, 
				`users`.`nick`,`idb_approve`.`id` AS `appid`,
				`idb_approve`.`auser` AS `userid`,`idb_approve`.`atime` AS `usertime`
			FROM `idb`,`idb_approve`,`users` 
			WHERE 
				`idb`.`strid` = '" . sanitize($_GET['var2']) . "' AND
				`idb`.`id` = `idb_approve`.`itemid` AND
				`idb`.`asg` = '1' AND  
				`idb_approve`.`app` = '0' AND
				`idb_approve`.`auser` = `users`.`id`
		");
			// priekšmeta apskate
			if ($item) {

				// redirekts uzreiz pēc darbības ar konkrētu priekšmetu uz nākamo vēl neskatīto
				if (isset($_GET['viewall'])) {
					$specid = $db->get_row("SELECT `idb`.`strid` FROM `idb`,`idb_approve` WHERE `idb_approve`.`itemid` = `idb`.`id` AND `idb`.`asg` = '1' 
			AND `idb_approve`.`app` = '0' ORDER BY `idb_approve`.`atime` ASC LIMIT 1,1");
					$all = true;
				} else
					$all = false;

				// tulkojuma dzēšana
				if (isset($_GET['var3']) && $_GET['var3'] == 'remove') {
					$db->query("UPDATE `idb_approve` SET `app` = '2',`apptime` = NOW(), `appuser` = '" . $auth->id . "' WHERE `id` = '" . $item->appid . "' LIMIT 1 ");
					$db->query("UPDATE `idb` SET `asg` = '0' WHERE `id` = '" . $item->id . "' LIMIT 1");
					if ($all && $specid) {
						redirect('/db/queue/' . $specid->strid . '?viewall=1');
					} else {
						redirect('/db/queue');
					}
					exit;
				}
				// tulkojuma apstiprināšana (no formas)
				else if (isset($_GET['var3']) && $_GET['var3'] == 'approve' && isset($_POST['submit'])) {
					$save = array();
					$fields = array('lvlocation', 'lvuses', 'lvnotes');
					foreach ($fields as $key) {
						if (isset($_POST[$key])) {
							$save[] = '`' . $key . '` = \'' . sanitize(trim($_POST[$key])) . '\'';
						}
					}
					$fields = array('members', 'equips', 'trade', 'stacks', 'quest');
					foreach ($fields as $key) {
						if (isset($_POST[$key]) && ($_POST[$key] == 1 || $_POST[$key] == 0)) {
							$save[] = '`' . $key . '` = \'' . (int) $_POST[$key] . '\'';
						}
					}
					$save = implode(',', $save);
					if (!empty($save)) {
						$db->query("UPDATE `idb_approve` SET `app` = '1',`apptime` = NOW(), `appuser` = '" . $auth->id . "' WHERE `id` = '" . $item->appid . "' LIMIT 1 ");
						$db->query("UPDATE `idb` SET " . $save . ",`auser` = '" . $item->userid . "',`atime` = '" . sanitize($item->usertime) . "',`apptime` = NOW(),`appuser` = '" . $auth->id . "',`asg` = '0',`tolv` = '1' WHERE `id` = '" . $item->id . "' LIMIT 1");

						update_weekly($item->userid);
					} else {
						set_flash('Kļūda! Formā netika ievadīti dati!');
						if ($all && $specid) {
							redirect('/db/queue/' . $item->strid . '?viewall=1');
						} else {
							redirect('/db/queue/' . $item->strid);
						}
						exit;
					}
					if ($all && $specid) {
						redirect('/db/queue/' . $specid->strid . '?viewall=1');
					} else {
						redirect('/db/queue');
					}
					exit;
				}
				// tulkojuma apstiprināšana (no linka)
				else if (isset($_GET['var3']) && $_GET['var3'] == 'approve') {
					$db->query("UPDATE `idb_approve` SET `app` = '1',`apptime` = NOW(), `appuser` = '" . $auth->id . "' WHERE `id` = '" . $item->appid . "' LIMIT 1 ");
					$db->query("UPDATE `idb` SET `asg` = '0',`tolv` = '1',`lvlocation` = '" . sanitize($item->lvlocation) . "', `lvuses` = '" . sanitize($item->lvuses) . "', `lvnotes` = '" . sanitize($item->lvnotes) . "',`auser` = '" . $item->userid . "',`atime` = '" . sanitize($item->usertime) . "',`apptime` = NOW(),`appuser` = '" . $auth->id . "' WHERE `id` = '" . $item->id . "' LIMIT 1");

					update_weekly($item->userid);

					if ($all && $specid) {
						redirect('/db/queue/' . $specid->strid . '?viewall=1');
					} else {
						redirect('/db/queue');
					}
					exit;
				}
				// priekšmeta forma
				else {
					$tpl->newBlock('queueview');
					if ($all && $specid) {
						$tpl->assign('viewall', '?viewall=1');
					}
					$data['lvlocation'] = $item->lvlocation;
					$data['lvuses'] = $item->lvuses;
					$data['lvnotes'] = $item->lvnotes;

					$item = itemsdb_replace($item, 1);
					$tpl->assignAll($item);

					if (!empty($item->lvnotes)) {
						$tpl->newBlock('queue-notes');
						$tpl->assign('lvnotes', $item->lvnotes);
					}
					$tpl->newBlock('queue-form-lv');
					$tpl->assignAll($item);
					$tpl->assign(array(
						'location-data' => $data['lvlocation'],
						'uses-data' => $data['lvuses'],
						'notes-data' => $data['lvnotes']
					));
					$tpl->newBlock('queue-form-eng');
					$tpl->assignAll($item);
				}
			} else {
				set_flash("Kļūda! Tāds priekšmets netika atrasts!");
				redirect('/db/queue');
				exit;
			}
		}
		// saraksts ar apstiprināmajiem priekšmetiem
		else {
			$items = $db->get_results("SELECT `idb`.`strid`,`idb`.`item`,`idb`.`img`,`users`.`nick` FROM `idb_approve`,`idb`,`users` WHERE `idb_approve`.`app` = '0' AND `idb_approve`.`itemid` = `idb`.`id` AND `idb_approve`.`auser` = `users`.`id` ORDER BY `idb_approve`.`atime` ASC");
			if ($items) {
				$type = 0;
				$specid = $db->get_row("SELECT `idb`.`strid` FROM `idb`,`idb_approve` WHERE `idb_approve`.`itemid` = `idb`.`id` AND `idb`.`asg` = '1' 
			AND `idb_approve`.`app` = '0' ORDER BY `idb_approve`.`atime` ASC ");

				$tpl->newBlock('itemsqueue');
				if ($specid) {
					$tpl->assign('strid-spec', $specid->strid);
				}
				foreach ($items as $item) {
					if ($type == 0) {
						$type = 1;
						$tpl->newBlock('queue-item');
					} else
						$type = 0;

					$tpl->newBlock('queue-item-col');
					$tpl->assignAll($item);
				}
			} else {
				$tpl->newBlock('queue-noitems');
			}
		}
		$page_title = htmlspecialchars('Apstiprināšana | RuneScape datubāze');
	}




	/* --------------------------------------------------
	 * 		priekšmeta labotās info saglabāšana
	  / */ else if (isset($_GET['var2']) && $_GET['var2'] == 'edit' && isset($_POST['submit'])) {

		$item = $db->get_row("SELECT * FROM `idb` WHERE `strid` = '" . sanitize($_GET['var1']) . "'");

		if ($item && $item->asg == 0) {

			// jaunie tulkotāji jau iztulkotos nevar rediģēt
			if ($item->tolv == 1 && $idbusr->app == 1) {
				set_flash('Kļūda! Trūkst tiesību labot iztulkotus priekšmetus!');
				redirect('/db/' . $item->strid);
				exit;
			}

			$save = array();

			// teksta lauki
			if ($idbusr->app == 0) {
				$fields = array('examine', 'lvlocation', 'lvuses', 'lvnotes', 'droppedby');
			} else {
				$fields = array('lvlocation', 'lvuses', 'lvnotes');
			}
			foreach ($fields as $key) {
				if (isset($_POST[$key]) && $_POST[$key] != $item->$key) {
					$save[$key] = sanitize(trim($_POST[$key]));
				}
			}
			// zemākie lauki tikai tad, ja lietotāja tulkojumi nav jāapstiprina. Formā šos nemaz nerāda.
			if ($idbusr->app == 0) {
				// nullīšu un vieninieciņu lauciņi
				$fields = array('members', 'trade', 'equips', 'stacks', 'quest');
				foreach ($fields as $key) {
					if (isset($_POST[$key]) && ($_POST[$key] == 0 || $_POST[$key] == 1)/* && $_POST['key'] != $item->$key */) {
						$save[$key] = (int) $_POST[$key];
					}
				}
				// bonusu lauki ar ierobežotu garumu
				$fields = array('weight', 'bonuses', 'dmg', 'level', 'accuracy', 'armour', 'lifeb', 'prayb', 'cmelee', 'cmage', 'crange');
				foreach ($fields as $key) {
					if (isset($_POST[$key]) && $_POST[$key] != $item->$key) {
						$save[$key] = sanitize(substr(trim($_POST[$key]), 0, 15));
					}
				}
				if (isset($_POST['item']) && $_POST['item'] != $item->item) {
					$save['item'] = sanitize(substr(strip_tags(trim($_POST['item'])), 0, 60));
				}
				// pārbauda visus laukus no select'iem
				$slots = array('--', '2H Ierocis', 'Apavi', 'Apmetnis', 'Aura', 'Bikses', 'Bultas', 'Cimdi', 'Galvassega', 'Gredzens', 'Ierocis', 'Kaklarota', 'Ķermenis', 'Vairogs');
				$types = array('--', 'Ranged', 'Melee', 'Magic', 'Visas', 'Hybrid');
				$styles = array('--', 'Slashing', 'Stabbing', 'Crushing');
				$speed = array('--', 'Ļoti ātrs', 'Vidēji ātrs', 'Ātrs', 'Ļoti lēns', 'Lēns');
				$ammo = array('--', 'Bolt', 'Thrown', 'Arrow');

				$arrs = array('slot' => $slots, 'type' => $types, 'style' => $styles, 'speed' => $speed, 'ammo' => $ammo);
				if ($arrs) {
					foreach ($arrs as $field => $arr) {
						if (isset($_POST[$field]) && $_POST[$field] != $item->$field && in_array($_POST[$field], $arr)) {
							$save[$field] = sanitize(substr(strip_tags(trim($_POST[$field])), 0, 25));
						}
					}
				}
			}
			//$save['name'] = sanitize(trim($_POST['item_name']));
			//$save['strid'] = mkslug_itemsdb($_POST['item_name']);
			//$save['approved'] = '1';
			//salipina visu vienā kverijā
			$updates = array();
			foreach ($save as $key => $val) {
				$updates[] = '`' . $key . '` = ' . "'" . $val . "'";
			}

			// iztulkojis kāds jauniņais
			if ($idbusr->app == 1 && $item->tolv == 0) {
				if (!empty($save)) {
					$db->query("INSERT INTO `idb_approve` (lvlocation,lvuses,lvnotes,auser,atime,itemid) VALUES (
					'" . $save['lvlocation'] . "',
					'" . $save['lvuses'] . "',
					'" . $save['lvnotes'] . "',
					'" . $auth->id . "',
					NOW(),
					'" . $item->id . "'
				)
				");
					$db->query("UPDATE `idb` SET `asg` = '1' WHERE `id` = '" . $item->id . "' LIMIT 1");
					set_flash('Paldies! Priekšmeta tulkojums iesniegts pārbaudei!');
				} else {
					set_flash('Kļūda! Netika ievadīta informācija!');
				}
			}
			// iztulkojis kāds supreme līderis
			else if ($item->tolv == 0) {
				$db->query("UPDATE `idb` SET `tolv` = '1', `auser` = '" . $auth->id . "', `atime` = NOW() WHERE `id` = '" . $item->id . "' LIMIT 1");
				$db->query("UPDATE `idb` SET " . implode(', ', $updates) . " WHERE `id` = '" . $item->id . "' LIMIT 1");
				update_weekly($auth->id);
				// priekšmets tiek tikai atjaunots
			} else {
				$db->query("UPDATE `idb` SET `etime` = NOW(), `ecount` = (`ecount` + 1), `euser` = '" . $auth->id . "' WHERE `id` = '" . $item->id . "' LIMIT 1");
				$db->query("UPDATE `idb` SET " . implode(', ', $updates) . " WHERE `id` = '" . $item->id . "' LIMIT 1");
			}
			redirect('/db/' . $item->strid);
			exit;
		} else {
			set_flash('Kļūda! Šo priekšmetu kāds jau ir iztulkojis, un tas gaida apstiprināšanu!');
			redirect('/db');
			exit;
		}
	}

	/* -----------------------------------------------------------------
	 * 		priekšmeta rediģēšanas forma (jaunajiem tulkotājiem)
	  / */ else if (isset($_GET['var2']) && $_GET['var2'] == 'edit' && $idbusr->app == 1) {

		$item = $db->get_row("SELECT * FROM `idb` WHERE `strid` = '" . sanitize($_GET['var1']) . "'");

		if ($item) {
			if ($item->tolv == 1) {
				set_flash('Kļūda! Trūkst tiesību labot iztulkotus priekšmetus!');
				redirect('/db/' . $item->strid);
				exit;
			} else if ($item->asg == 1) {
				set_flash('Kļūda! Šī priekšmeta tulkojums jau atrodas apstiprināmo sarakstā! Lūdzu, izvēlies citu!');
				redirect('/db/' . $item->strid);
				exit;
			} else {
				$tpl->newBlock('search-form');

				//ja nav attēla, parāda defaulto
				if (!empty($item->img) && file_exists('dati/idb/' . $item->img)) {
					$item->img = '/dati/idb/' . $item->img;
				} else {
					$item->img = '/bildes/none.png';
				}

				// assign'o visas vērtības
				$tpl->newBlock('itemedit-small');

				foreach ($item as $key => $val) {
					$tpl->assign($key, $val);
				}
			}
		} else {
			set_flash('Kļūda! Tāds priekšmets netika atrasts!');
			redirect('/db');
			exit;
		}
		$page_title = htmlspecialchars('Priekšmeta rediģēšana | RuneScape datubāze');
	} else if (isset($_GET['var1']) && $_GET['var1'] == 'strip') {

		$items = $db->get_results("SELECT `item`,`id`,`item` FROM `idb` WHERE `item` LIKE '%-%' AND `oldrs` = '0' ORDER BY `item` LIMIT 0,1000");
		if ($items) {
			$counter = 1;
			foreach ($items as $item) {

				//echo '<span style="color:orange"><strong>'.$counter.'.</strong></span> <span style="color:cyan">'.$item->item.'</span>   ';
				//echo '<br /><br /><br />';
				//echo strip_monsters($item->droppedby);
				$res = strip_item($item->item);
				//echo '<br /><br /><br />';
				echo $res . '<br />';
				//$upd = $db->query("UPDATE `idb` SET `item` = '".sanitize($res)."',`alt` = '1' WHERE `id` = '".$item->id."' ");
				$counter++;
			}
		}
	}



	/* --------------------------------------------------
	 * 		priekšmeta rediģēšanas forma
	  / */ else if (isset($_GET['var2']) && $_GET['var2'] == 'edit') {

		$item = $db->get_row("SELECT * FROM `idb` WHERE `strid` = '" . sanitize($_GET['var1']) . "'");

		if ($item && $item->asg == 0) {

			$tpl->newBlock('search-form');

			//ja nav attēla, parāda defaulto.
			if (!empty($item->img) && file_exists('dati/idb/' . $item->img)) {
				$item->img = '/dati/idb/' . $item->img;
			} else {
				$item->img = '/bildes/none.png';
			}

			// toggle buttons
			$short = array('members', 'stacks', 'equips', 'quest', 'trade');
			foreach ($short as $field) {
				//$item->$field = ($item->$field == 1)? '_on' : '';
			}

			// assign'o visas vērtības
			$tpl->newBlock('itemedit');

			foreach ($item as $key => $val) {
				$tpl->assign($key, $val);
			}

			// assign'o visus laukus 'select'iem
			$slots = array('--', '2H Ierocis', 'Apavi', 'Apmetnis', 'Aura', 'Bikses', 'Bultas', 'Cimdi', 'Galvassega', 'Gredzens', 'Ierocis', 'Kaklarota', 'Ķermenis', 'Vairogs');
			$types = array('--', 'Ranged', 'Melee', 'Magic', 'Visas', 'Hybrid');
			$styles = array('--', 'Slashing', 'Stabbing', 'Crushing');
			$speed = array('--', 'Ļoti ātrs', 'Vidēji ātrs', 'Ātrs', 'Ļoti lēns', 'Lēns');
			$ammo = array('--', 'Bolt', 'Thrown', 'Arrow');

			$arrs = array('slot' => $slots, 'type' => $types, 'style' => $styles, 'speed' => $speed, 'ammo' => $ammo);
			if ($arrs) {
				foreach ($arrs as $field => $arr) {
					$tpl->newBlock('itemedit-' . $field . 's');
					foreach ($arr as $data) {
						$selected = ($item->$field == $data) ? $selected = ' selected="selected"' : '';
						$tpl->newBlock('itemedit-' . $field);
						$tpl->assign(array(
							$field => $data,
							'selected' => $selected
						));
					}
				}
			}
		} else if ($item && $item->asg == 1) {
			set_flash('Kļūda! Šī priekšmeta tulkojums jau atrodas apstiprināmo sarakstā! Lūdzu, izvēlies citu!');
			redirect('/db/' . $item->strid);
			exit;
		} else {
			redirect('/db');
			exit;
		}
		$page_title = htmlspecialchars('Priekšmeta rediģēšana | RuneScape datubāze');
	}




	/* ---------------------------------------------------------------------------------------------------
	 * 		iztulkoto priekšmetu saraksts konkrētam lietotājam (ja viņa priekšmeti tiek apstiprināti)
	 * 		+ editošana, ja vēl nav mod-approved
	  / */ else if (isset($_GET['var1']) && $_GET['var1'] == 'myitems' && $idbusr->app == 1) {

		// priekšmeta apskate
		if (isset($_GET['var2'])) {

			// veikti labojumi iesniegtā priekšmeta informācijā
			if (isset($_POST['submit'])) {

				$item = $db->get_row("SELECT `idb_approve`.`id`,`idb_approve`.`app`,`idb`.`strid` FROM `idb`,`idb_approve` WHERE `idb_approve`.`id` = '" . (int) $_GET['var2'] . "' AND `idb_approve`.`itemid` = `idb`.`id` AND `idb_approve`.`auser` = '" . $auth->id . "' ORDER BY `idb_approve`.`atime` DESC LIMIT 0,1");
				if ($item) {

					// kļūda - labot var tikai vēl modu nepārbaudītus priekšmetus
					if ($item->app != 0) {
						set_flash('Kļūda! Veikt izmaiņas var tikai vēl nepārbaudītos priekšmetos!');
						redirect('/db/myitems/' . $item->id);
						exit;
					} else {
						$save = array();
						$fields = array('lvlocation', 'lvuses', 'lvnotes');
						foreach ($fields as $field) {
							if (isset($_POST[$field]) && !empty($_POST[$field])) {
								$save[] = '`' . $field . '` = \'' . sanitize(trim($_POST[$field])) . '\'';
							}
						}
						if (!empty($save)) {
							$save = implode(',', $save);
							$upd = $db->query("UPDATE `idb_approve` SET " . $save . ",`etime` = NOW(),`ecount` = (`ecount`+1) WHERE `id` = '" . $item->id . "' LIMIT 1");
							set_flash('Paldies! Tavs labojums iesniegts!');
							redirect('/db/myitems');
							exit;
						} else {
							set_flash('Kļūda! Ievades lauki atstāti tukši!');
							redirect('/db/myitems/' . $item->id);
							exit;
						}
					}
				} else {
					set_flash('Kļūda! Tāds priekšmets netika atrasts!');
					redirect('/db/myitems');
					exit;
				}
				// priekšmeta formas apskate
			} else {

				$item = $db->get_row("SELECT `idb_approve`.`id`,`idb`.`img`,`idb`.`strid`,`idb`.`item`,`idb_approve`.`app`,`idb_approve`.`lvlocation`,`idb_approve`.`lvuses`,`idb_approve`.`lvnotes`, `idb`.`location`,`idb`.`uses`,`idb`.`notes` FROM `idb`,`idb_approve` WHERE `idb_approve`.`id` = '" . (int) $_GET['var2'] . "' AND `idb_approve`.`itemid` = `idb`.`id` AND `idb_approve`.`auser` = '" . $idbusr->user . "' ORDER BY `idb_approve`.`atime` DESC LIMIT 0,1");
				if ($item) {

					$tpl->newBlock('myitem-view');
					$tpl->assignAll($item);
					if ($item->app == 0) {
						$tpl->newBlock('myitem-submit');
					} else
						$tpl->newBlock('myitem-submit-disabled');
				} else {
					set_flash('Kļūdaini norādīts izvēlētais priekšmets vai arī to vairs nevar labot!');
					redirect('/db/myitems');
					exit;
				}
			}

			// priekšmetu saraksts
		} else {
			$items = $db->get_results("SELECT `idb`.`img`,`idb`.`strid`,`idb`.`item`,`idb_approve`.`app`,`idb_approve`.`id` FROM `idb`,`idb_approve` WHERE `idb_approve`.`itemid` = `idb`.`id` AND `idb_approve`.`auser` = '" . $idbusr->user . "' ORDER BY `idb_approve`.`atime` DESC LIMIT 0,50");
			if ($items) {
				$tpl->newBlock('myitems');
				foreach ($items as $item) {

					$color = ($item->app == 2) ? 'red' : ( ($item->app == 1) ? 'green' : 'blue');
					$text = ($item->app == 2) ? 'noraidīts' : ( ($item->app == 1) ? 'apstiprināts' : 'nav skatīts');

					$tpl->newBlock('myitem');
					$tpl->assignAll($item);
					$tpl->assign(array(
						'color' => $color,
						'text' => $text
					));
				}
			} else {
				$tpl->newBlock('myitems-noitems');
			}
		}
		$page_title = htmlspecialchars('Mani priekšmeti | RuneScape datubāze');
	}




	/* -------------------------------------
	 * 		priekšmeta apskate
	  / */ else if (isset($_GET['var1'])) {

		$item = $db->get_row("SELECT * FROM `idb` WHERE `oldrs` = '0' AND `strid` = '" . sanitize($_GET['var1']) . "' ");

		if (!$item) {
			redirect('/db');
			exit;
		} else {
			$db->query("UPDATE `idb` SET `views` = (`views` + 1) WHERE `id` = '" . $item->id . "' LIMIT 1");
		}

		// views parādā tikai man
		if ($auth->id == 115) {
			$item->views = ' (' . ($item->views + 1) . ')';
		} else
			$item->views = '';

		//ja nav attēla, parāda defaulto
		if (!empty($item->img) && file_exists('dati/idb/' . $item->img)) {
			$item->img = '/dati/idb/' . $item->img;
		} else {
			$item->img = '/bildes/none.png';
		}

		// lietotāja niks ar krāsu un linku
		if ($user = get_user($item->auser)) {
			$item->auser = '<a href="' . mkurl('user', $user->id, $user->nick) . '">' . usercolor($user->nick, $user->level) . '</a>';
		} else {
			$item->auser = '<i>nav vēl iztulkots</i>';
		}

		// svars
		$item->weight = trim(str_replace(array('x', '?', ' kg'), '', $item->weight));
		if (!empty($item->weight)) {
			$item->weight .= ' kg';
		}

		// tulkotā versija
		if ($item->tolv == 1) {
			$item->uses = $item->lvuses;
			$item->location = $item->lvlocation;
			$item->notes = $item->lvnotes;
		}
		$item = itemsdb_replace($item);  // aizstāj [[nosaukums]] ar attiecīgajām adresēm uz rakstiem/priekšmetiem
		// toggle buttons
		$short = array('members', 'stacks', 'equips', 'quest', 'trade');
		foreach ($short as $field) {
			$item->$field = ($item->$field == 1) ? '_on' : '_off';
		}

		// tukšie bonusu lauki
		$bonuses = array('armour', 'dmg', 'level', 'lifeb', 'bonuses', 'prayb', 'cmelee', 'cmage', 'crange', 'accuracy');
		foreach ($bonuses as $field) {
			$item->$field = (empty($item->$field) && $item->$field != 0) ? '--' : $item->$field;
		}

		// assign'o vērtības templeitam
		$tpl->newBlock('search-form');
		$tpl->newBlock('itemview');
		foreach ($item as $key => $val) {
			$tpl->assign($key, $val);
		}

		// dinamiskie lauki, kas var būt un var nebūt
		if ($auth->id == 115 && $item->tolv == 1) {
			$tpl->newBlock('reset-item');
			$tpl->assign('strid', $item->strid);
		}
		if (!empty($item->notes)) {
			$tpl->newBlock('itemview-notes');
			$tpl->assign('notes', $item->notes);
		}
		if ($item->droppedby != '--' && $item->droppedby != '') {
			$item->droppedby = strip_tags($item->droppedby);
			$tpl->newBlock('itemview-monsters');
			$tpl->assign('droppedby', $item->droppedby);
		}
		$item->uses = str_replace(array('--', 'Nav', 'Nav.', '---'), '', $item->uses);
		if (!empty($item->uses)) {
			$tpl->newBlock('itemview-uses');
			$tpl->assign('uses', $item->uses);
		}

		// poga uz rediģēšanas formu
		if (($item->asg == 0 && $idbusr->mod == 1) || ($item->tolv == 0 && $item->asg == 0)) {
			$tpl->newBlock('itemview-options');
			$tpl->assign('strid', $item->strid);
		}
		// priekšmetu tulkojis kāds, kuru jāpārbauda; atrod apstiprinātāju
		if ($idbusr->mod == 1 && $item->appuser != 0) {
			$appuser = get_user($item->appuser);
			$appusr = '<a href="' . mkurl('user', $appuser->id, $appuser->nick) . '">' . usercolor($appuser->nick, $appuser->level) . '</a>';
			$tpl->newBlock('itemview-appby');
			$tpl->assign('appuser', $appusr);
		}

		$page_title = htmlspecialchars($item->item . ' | RuneScape datubāze');
		$pagepath = '<a href="/' . $category->textid . '">' . $category->title . '</a> / ' . $item->item;
	}




	/* -------------------------------------
	 * 		datubāzes sākumlapa
	  / */ else if (!isset($_GET['var1'])) {

		$tpl->newBlock('item-search');
		if (isset($_SESSION['chbx'])) {
			$tpl->assign('checked', 'checked="checked"');
		}

		$main_blocks = array(
			"newest" => array(" `atime` ", " AND `apptime` = '0000-00-00 00:00:00'", "Jaunākie iztulkotie šķirkļi", "jqnew"),
			"updated" => array(" `etime` ", " AND `ecount` > '0'", "Nesen atjaunotie šķirkļi", "jqupd"),
			"accepted" => array(" `apptime` ", " AND `apptime` != '0000-00-00 00:00:00'", "Nesen apstiprinātie šķirkļi", "jqapp")
		);

		foreach ($main_blocks as $block) {

			$item_list = $db->get_results("SELECT `auser`,`item`,`strid` FROM `idb` WHERE `oldrs` = '0' AND `tolv` = '1'" . $block[1] . " ORDER BY " . $block[0] . " DESC LIMIT 0,$items_mainlist");
			if ($item_list) {

				$counter = 1;

				$tpl->newBlock('search-list');
				$tpl->assign('main_title', $block[2]);

				foreach ($item_list as $item) {

					if (strlen($item->item) > 33) {
						$item->item = substr($item->item, 0, 30) . '...';
					}

					$tpl->newBlock('search-items');
					$tpl->assignAll($item);
					$tpl->assign('counter', $counter);
					$counter++;
				}

				// izvada 5 nākamās lapas
				$concat = '';
				for ($a = 0; $a < 5; $a++) {
					if ($a == 4) {
						$concat .= '<a class="idb-next-last" href="/db/' . $block[3] . '/' . ($a + 1) . '" title="' . ($a + 1) . '. lapa">' . ($a + 1) . '</a>&nbsp;';
					} else
						$concat .= '<a class="idb-next" href="/db/' . $block[3] . '/' . ($a + 1) . '" title="' . ($a + 1) . '. lapa">' . ($a + 1) . '</a>&nbsp;';
				}
				$tpl->newBlock('search-page-sm');
				$tpl->assign(array(
					'page-nr' => $concat,
					'block_class' => $block[3]
				));
			}
		}
	}

	/* -- */
}
