<?php

$page_title = 'Flash spēles - exs.lv spēļu portāls';

$add_css[] = 'ajax-comments.css';
$add_css[] = 'flash-games.css';

if (isset($_GET['var1'])) {

	$cats = $db->get_col("SELECT DISTINCT(category_slug) FROM flash_games");

	if (in_array($_GET['var1'], $cats)) {
		$gcat = $db->get_row("SELECT category,category_slug FROM flash_games WHERE category_slug = '" . sanitize($_GET['var1']) . "' LIMIT 1");
		$tpl->assignInclude('module-head', 'modules/' . $category->module . '/head.tpl');
		$tpl->prepare();
		$end = 42;
		if (isset($_GET['page'])) {
			$skip = (int) $_GET['page'] * $end;
		} else {
			$skip = 0;
		}
		if (!file_exists('cache/' . $category->textid . '/' . $skip . '-' . $gcat->category_slug . '.html')) {

			$tpl_cachable = new TemplatePower('modules/flash-games/list.tpl');
			$tpl_cachable->prepare();

			$tpl_cachable->assignGlobal(array(
				'category-url' => $category->textid,
			));

			$games = $db->get_results("SELECT slug,title,thb_local,rating,category,category_slug,rating_count FROM flash_games WHERE category_slug = '$gcat->category_slug' ORDER BY rating/rating_count DESC LIMIT $skip,$end");
			if ($games) {
				$tpl_cachable->newBlock('games-list');
				$tpl_cachable->assign('main-title', $gcat->category . ' flash spēles');

				$cats = $db->get_results("SELECT DISTINCT(category), category_slug FROM flash_games ORDER BY category ASC");
				if ($cats) {
					foreach ($cats as $cat) {
						$tpl_cachable->newBlock('games-catlist');
						$tpl_cachable->assign(array(
							'title' => $cat->category,
							'slug' => $cat->category_slug
						));
					}
				}

				foreach ($games as $game) {
					$tpl_cachable->newBlock('games-node');

					if (!empty($game->thb_local) && file_exists('upload/flash-games/thb/' . $game->thb_local)) {
						$thb = '/upload/flash-games/thb/' . $game->thb_local;
					} else {
						$thb = '/dati/bildes/useravatar/none.png';
					}

					$tpl_cachable->assign(array(
						'slug' => $game->slug,
						'title' => $game->title,
						'alt' => h($game->title),
						'thumbnail' => $thb,
						'rating' => round($game->rating / $game->rating_count, 2),
						'category' => $game->category
					));
				}
				$total = $db->get_var("SELECT count(*) FROM flash_games WHERE category_slug = '$gcat->category_slug'");
				if ($total > $end) {
					if ($skip > 0) {
						if ($skip > $end) {
							$iepriekseja = $skip - $end;
						} else {
							$iepriekseja = 0;
						}
						$pager_next = '<a class="pager-next" title="Iepriekšējā lapa" href="/' . $category->textid . '/' . $gcat->category_slug . '/?page=' . $iepriekseja / $end . '">&laquo;</a>';
					} else {
						$pager_next = '';
					}
					$pager_prev = '';
					if ($total > $skip + $end) {
						$pager_prev = '<span>-</span> <a class="pager-prev" title="Nākamā lapa" href="/' . $category->textid . '/' . $game->category_slug . '/?page=' . ($skip + $end) / $end . '">&raquo;</a>';
					}
					$startnext = 0;
					$page_number = 0;
					$pager_numeric = '';
					while ($total - $startnext > 0) {
						$page_number++;
						$class = '';
						if ($skip == $startnext) {
							$class = ' class="selected"';
						}
						$pager_numeric .= '<span>-</span> <a href="/' . $category->textid . '/' . $gcat->category_slug . '/?page=' . $startnext / $end . '"' . $class . '>' . $page_number . '</a> ';
						$startnext = $startnext + $end;
					}
					$tpl_cachable->assignGlobal(array(
						'pager-next' => $pager_next,
						'pager-prev' => $pager_prev,
						'pager-numeric' => $pager_numeric
					));
				}

				$cache_handle = fopen('cache/' . $category->textid . '/' . $skip . '-' . $gcat->category_slug . '.html', 'wb');
				fwrite($cache_handle, $tpl_cachable->getOutputContent());
				fclose($cache_handle);
			} else {
				redirect();
			}
		}
		$tpl->assignGlobal('clist', file_get_contents('cache/' . $category->textid . '/' . $skip . '-' . $gcat->category_slug . '.html'));
		$pagepath = '<a href="/' . $category->textid . '">' . $category->title . '</a> / ' . $gcat->category;
		$page_title = $gcat->category . ' flash spēles';
		if (!empty($_GET['page'])) {
			$page_title = $page_title . ' | Lapa ' . (intval($_GET['page']) + 1);
		}
	} else {

		$game = $db->get_row("SELECT * FROM flash_games WHERE slug = ('" . sanitize($_GET['var1']) . "') LIMIT 1");
		if ($game) {

			require_once('includes/ajax_comments.php');
			if (isset($_GET['ajax']) || isset($_POST['ajax'])) {
				echo comments_block('fg-' . $game->id, $_GET['ajax']);
				exit;
			}

			$rating_users = unserialize($game->rating_users);

			if (isset($_GET['rate'])) {

				if (isset($_POST['vote']) && $auth->ok) {
					$vote = (int) $_POST['vote'];

					//parbauda vai deriga vote vertiba
					if ($vote < 0 || $vote > 5) {
						die('Kļūdaina vērtība');
					}

					//parbauda vai lietotajs jau nav balsojis
					if (in_array($auth->id, $rating_users)) {
						die('Tu jau nobalsoji');
					}

					//pievieno balsojumu
					$rating_users[] = $auth->id;

					$rating = ($game->rating + $vote);

					$db->query("UPDATE flash_games SET
                        rating = $rating,
                        rating_count = rating_count+1,
                        rating_users = '" . serialize($rating_users) . "'
                      WHERE id = '$game->id'
                      ");

					destroy_cdir(CORE_PATH . '/cache/' . $category->textid . '/');

					if ($vote > 3) {
						push('Patīk flash spēle <a href="/' . $category->textid . '/' . $game->slug . '">' . $game->title . '</a>, vērtējums: ' . $vote, '/upload/flash-games/thb/' . $game->thb_local);
					} else {
						push('Novērtēja flash spēli <a href="/' . $category->textid . '/' . $game->slug . '">' . $game->title . '</a>, vērtējums: ' . $vote, '/upload/flash-games/thb/' . $game->thb_local);
					}

					die('Spēlētāju vērtējums: ' . round($rating / ($game->rating_count + 1), 2) . ' (' . ($game->rating_count + 1) . ' balsis)');
				}
				die('Tukšs pieprasījums');
			}

			$db->query("UPDATE flash_games SET gameplays = gameplays+1 WHERE id = $game->id");

			$tpl->assignInclude('module-head', 'modules/' . $category->module . '/head.tpl');
			$tpl->prepare();
			if ($auth->ok) {

				if (!$lastid = (int) $db->get_var("SELECT id FROM ajax_comments WHERE parent = 'fg-" . $game->id . "' ORDER BY id DESC LIMIT 1")) {
					$lastid = 1;
				}

				$tpl->newBlock('games-head');
				$tpl->assign(array(
					'slug' => $game->slug,
					'lastid' => $lastid
				));
			}
			$tpl->newBlock('games-single');
			$tpl->assign(array(
				'slug' => $game->slug,
				'title' => $game->title,
				'title-encoded' => urlencode($game->title),
				'rating' => round($game->rating / $game->rating_count, 2),
				'rating_count' => $game->rating_count,
				'category' => $game->category,
				'width' => $game->width,
				'height' => $game->height,
				'flash_file' => $game->flash_file,
				'instructions' => $game->instructions,
				'description' => $game->description,
				'comments' => comments_block('fg-' . $game->id)
			));

			if ($auth->ok && !in_array($auth->id, $rating_users)) {
				$tpl->newBlock('game-rate');
			}

			$pagepath = '<a href="/' . $category->textid . '">' . $category->title . '</a> / ' . $game->title;
			$page_title = $game->title . ' | ' . $game->category . ' flash spēles';
			if (isset($_GET['edit']) && $auth->id == 1) {
				if (isset($_POST['description']) || isset($_POST['instructions'])) {
					$description = htmlpost2db($_POST['description']);
					$instructions = htmlpost2db($_POST['instructions']);
					$db->query("UPDATE flash_games SET description = '$description', instructions = '$instructions' WHERE id = '$game->id'");
					redirect('/' . $category->textid . '/' . $game->slug . '/?edit');
				}
				$tpl->newBlock('games-edit');
				$tpl->assign(array(
					'width' => $game->width,
					'height' => $game->height,
					'flash_file' => $game->flash_file,
					'instructions' => $game->instructions,
					'description' => $game->description
				));
			}

			$not = array($game->id);
			//citas speles saja kategorija
			$games = $db->get_results("SELECT id,slug,title,thb_local,rating,category,category_slug,rating_count FROM flash_games WHERE category_slug = '$game->category_slug' AND `id` != '$game->id' ORDER BY rating/rating_count DESC LIMIT 3");
			if ($games) {
				$tpl->newBlock('games-list-other');
				$tpl->assign(array(
					'category' => $game->category,
					'category_slug' => $game->category_slug,
				));
				//popularakas
				foreach ($games as $game) {
					$tpl->newBlock('games-node-other');
					if (!empty($game->thb_local) && file_exists('upload/flash-games/thb/' . $game->thb_local)) {
						$thb = '/upload/flash-games/thb/' . $game->thb_local;
					} else {
						$thb = '/dati/bildes/useravatar/none.png';
					}
					$tpl->assign(array(
						'slug' => $game->slug,
						'title' => $game->title,
						'alt' => h($game->title),
						'thumbnail' => $thb,
						'rating' => round($game->rating / $game->rating_count, 2),
						'category' => $game->category
					));
					$not[] = $game->id;
				}
				$games = $db->get_results("SELECT slug,title,thb_local,rating,category,category_slug,rating_count FROM flash_games WHERE category_slug = '$game->category_slug' AND id NOT IN('" . implode("','", $not) . "') ORDER BY rand() LIMIT 3");
				//random
				foreach ($games as $game) {
					$tpl->newBlock('games-node-other');
					if (!empty($game->thb_local) && file_exists('upload/flash-games/thb/' . $game->thb_local)) {
						$thb = '/upload/flash-games/thb/' . $game->thb_local;
					} else {
						$thb = '/dati/bildes/useravatar/none.png';
					}
					$tpl->assign(array(
						'slug' => $game->slug,
						'title' => $game->title,
						'alt' => h($game->title),
						'thumbnail' => $thb,
						'rating' => round($game->rating / $game->rating_count, 2),
						'category' => $game->category
					));
				}
			}
		} else {
			redirect('/' . $category->textid);
		}
	}
} else {

	$tpl->assignInclude('module-head', 'modules/' . $category->module . '/head.tpl');
	$tpl->prepare();
	$end = 42;
	if (isset($_GET['page'])) {
		$skip = (int) $_GET['page'] * $end;
	} else {
		$skip = 0;
	}
	if (!file_exists('cache/' . $category->textid . '/' . $skip . '.html')) {

		$tpl_cachable = new TemplatePower('modules/flash-games/list.tpl');
		$tpl_cachable->prepare();

		$tpl_cachable->assignGlobal(array(
			'category-url' => $category->textid,
		));

		$games = $db->get_results("SELECT slug,title,thb_local,rating,category,rating_count FROM flash_games ORDER BY rating/rating_count DESC LIMIT $skip,$end");
		if ($games) {
			$tpl_cachable->newBlock('games-list');
			$tpl_cachable->assign('main-title', 'Flash spēles');

			$cats = $db->get_results("SELECT DISTINCT(category), category_slug FROM flash_games ORDER BY category ASC");
			if ($cats) {
				foreach ($cats as $cat) {
					$tpl_cachable->newBlock('games-catlist');
					$tpl_cachable->assign(array(
						'title' => $cat->category,
						'slug' => $cat->category_slug
					));
				}
			}

			foreach ($games as $game) {

				if (!empty($game->thb_local) && file_exists('upload/flash-games/thb/' . $game->thb_local)) {
					$thb = '/upload/flash-games/thb/' . $game->thb_local;
				} else {
					$thb = '/dati/bildes/useravatar/none.png';
				}

				$tpl_cachable->newBlock('games-node');
				$tpl_cachable->assign(array(
					'slug' => $game->slug,
					'title' => $game->title,
					'alt' => h($game->title),
					'thumbnail' => $thb,
					'thb' => $thb,
					'rating' => round($game->rating / $game->rating_count, 2),
					'category' => $game->category
				));
			}
			$total = $db->get_var("SELECT count(*) FROM flash_games");
			if ($skip > 0) {
				if ($skip > $end) {
					$iepriekseja = $skip - $end;
				} else {
					$iepriekseja = 0;
				}
				$pager_next = '<a class="pager-next" title="Iepriekšējā lapa" href="/' . $category->textid . '/?page=' . $iepriekseja / $end . '">&laquo;</a>';
			} else {
				$pager_next = '';
			}
			$pager_prev = '';
			if ($total > $skip + $end) {
				$pager_prev = '<span>-</span> <a class="pager-prev" title="Nākamā lapa" href="/' . $category->textid . '/?page=' . ($skip + $end) / $end . '">&raquo;</a>';
			}
			$startnext = 0;
			$page_number = 0;
			$pager_numeric = '';
			while ($total - $startnext > 0) {
				$page_number++;
				$class = '';
				if ($skip == $startnext) {
					$class = ' class="selected"';
				}
				$pager_numeric .= '<span>-</span> <a href="/' . $category->textid . '/?page=' . $startnext / $end . '"' . $class . '>' . $page_number . '</a> ';
				$startnext = $startnext + $end;
			}
			$tpl_cachable->assignGlobal(array(
				'pager-next' => $pager_next,
				'pager-prev' => $pager_prev,
				'pager-numeric' => $pager_numeric
			));
			$cache_handle = fopen('cache/' . $category->textid . '/' . $skip . '.html', 'wb');
			fwrite($cache_handle, $tpl_cachable->getOutputContent());
			fclose($cache_handle);
		} else {
			redirect();
		}
	}
	$tpl->assignGlobal('clist', file_get_contents('cache/' . $category->textid . '/' . $skip . '.html'));
	$pagepath = 'Flash spēles';


	$tpl->newBlock('meta-description');
	$tpl->assign('description', 'Spēles - exs.lv bezmaksas online flash spēles. Asa sižeta, Auto, Daudzspēlētāju, Piedzīvojumu, Prāta, Šaušanas, Sporta flash spēles. Smieklīgie flashi.');


	if (!empty($_GET['page'])) {
		$page_title = $page_title . ' | Lapa ' . (intval($_GET['page']) + 1);
	}
}

