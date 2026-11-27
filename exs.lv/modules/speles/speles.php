<?php

/**
 * EXS.LV Spēļu katalogs (/speles)
 */

$page_title = 'Bezmaksas tiešsaistes spēles pārlūkā latviešu valodā';
$meta_description = 'Spēlē bezmaksas tiešsaistes spēles pārlūkā bez lejupielādes telefonā un datorā latviešu valodā! Arkādes, loģikas, vārdu spēles un spēles 2 spēlētājiem EXS.LV.';
$opengraph_meta['title'] = $page_title . ' - exs.lv';
$opengraph_meta['description'] = $meta_description;

$tpl->assignInclude('module-head', 'modules/' . $category->module . '/head.tpl');
$tpl->prepare();

if (!$auth->ok) {
	$tpl->newBlock('seo-intro');
}

// Category mapping per game slug
$game_categories = [
	'tetris' => ['keys' => 'logikas arkades', 'label' => 'Loģikas'],
	'snake' => ['keys' => 'arkades', 'label' => 'Arkādes'],
	'karatavas' => ['keys' => 'vardu logikas', 'label' => 'Vārdu spēle'],
	'memory' => ['keys' => 'logikas', 'label' => 'Loģikas'],
	'2048' => ['keys' => 'logikas', 'label' => 'Loģikas'],
	'minu-mekletajs' => ['keys' => 'logikas', 'label' => 'Loģikas'],
	'sudoku' => ['keys' => 'logikas', 'label' => 'Loģikas'],
	'wordle' => ['keys' => 'vardu logikas', 'label' => 'Vārdu spēle'],
	'rulete' => ['keys' => 'arkades', 'label' => 'Klasika'],
	'desas' => ['keys' => 'diviem logikas', 'label' => '2 spēlētājiem'],
	'flappy' => ['keys' => 'arkades', 'label' => 'Arkādes'],
	'invaders' => ['keys' => 'arkades', 'label' => 'Arkādes'],
	'augsup' => ['keys' => 'arkades', 'label' => 'Arkādes'],
	'vardes' => ['keys' => 'arkades', 'label' => 'Arkādes'],
	'runner' => ['keys' => 'arkades', 'label' => 'Arkādes'],
	'tornis' => ['keys' => 'arkades', 'label' => 'Arkādes'],
	'arkanoid' => ['keys' => 'arkades', 'label' => 'Arkādes'],
	'rezonanse' => ['keys' => 'diviem arkades', 'label' => '2 spēlētājiem / Ritma'],
	'tanki' => ['keys' => 'arkades', 'label' => 'Arkādes'],
	'pacman' => ['keys' => 'arkades', 'label' => 'Arkādes'],
	'ut99' => ['keys' => 'arkades', 'label' => '3D Šūteris'],
];

// List of available games ordered by votes
$games_list = $db->get_results("
	SELECT * FROM `games` 
	WHERE `status` = 'active' 
	ORDER BY `vote_value` DESC, `id` ASC
");

// Determine the 3 latest games to display "Jaunums" badge from already-fetched list
$latest_ids = [];
if (!empty($games_list)) {
	$all_ids = array_map(function($g) { return (int)$g->id; }, $games_list);
	rsort($all_ids);
	$latest_ids = array_slice($all_ids, 0, 3);
}

// Batch-fetch top score per game (cached 60s in Memcached to avoid ~21 N+1 queries)
$top_scores_map = $m->get('speles_top_scores_v1');
if (!is_array($top_scores_map)) {
	$top_scores_map = [];
	$asc_codes = ['wordle', 'minu-mekletajs', 'sudoku'];
	$desc_codes = [];
	if (!empty($games_list)) {
		foreach ($games_list as $g) {
			if (!empty($g->game_code) && !in_array($g->game_code, $asc_codes, true)) {
				$desc_codes[] = "'" . sanitize($g->game_code) . "'";
			}
		}
	}
	if (!empty($desc_codes)) {
		$in_desc = implode(',', array_unique($desc_codes));
		$desc_rows = $db->get_results("
			SELECT g.game, g.user_id, g.score
			FROM gamescore g
			INNER JOIN (
				SELECT game, MAX(score) AS best_score
				FROM gamescore
				WHERE game IN ($in_desc)
				GROUP BY game
			) b ON g.game = b.game AND g.score = b.best_score
		");
		if (!empty($desc_rows)) {
			foreach ($desc_rows as $r) {
				if (!isset($top_scores_map[$r->game])) {
					$top_scores_map[$r->game] = $r;
				}
			}
		}
	}
	$in_asc = "'" . implode("','", $asc_codes) . "'";
	$asc_rows = $db->get_results("
		SELECT g.game, g.user_id, g.score
		FROM gamescore g
		INNER JOIN (
			SELECT game, MIN(score) AS best_score
			FROM gamescore
			WHERE game IN ($in_asc) AND score > 0
			GROUP BY game
		) b ON g.game = b.game AND g.score = b.best_score
	");
	if (!empty($asc_rows)) {
		foreach ($asc_rows as $r) {
			if (!isset($top_scores_map[$r->game])) {
				$top_scores_map[$r->game] = $r;
			}
		}
	}
	$m->set('speles_top_scores_v1', $top_scores_map, 60);
}

if (!empty($games_list)) {
	foreach ($games_list as $game) {
		$tpl->newBlock('game-card');

		$top_player_info = '';
		if (!empty($game->game_code) && isset($top_scores_map[$game->game_code])) {
			$top_score = $top_scores_map[$game->game_code];
			if ($top_score) {
				$u = get_user($top_score->user_id);
				if ($u) {
					if ($game->game_code == 'wordle') {
						$g_cnt = floor($top_score->score / 1000);
						$sec = $top_score->score % 1000;
						$mins = floor($sec / 60);
						$s = $sec % 60;
						$top_player_info = usercolor($u->nick, $u->level) . ' (' . $g_cnt . '/6, ' . sprintf('%02d:%02d', $mins, $s) . ')';
					} elseif (in_array($game->game_code, ['minu-mekletajs', 'sudoku'])) {
						$mins = floor($top_score->score / 60);
						$s = $top_score->score % 60;
						$top_player_info = usercolor($u->nick, $u->level) . ' (' . sprintf('%02d:%02d', $mins, $s) . ')';
					} else {
						$top_player_info = usercolor($u->nick, $u->level) . ' (' . number_format($top_score->score, 0, '', ' ') . ' pīk)';
					}
				}
			}
		}

		$badge_html = in_array((int)$game->id, $latest_ids) ? '<span class="label label-success pull-right">Jaunums</span>' : '';
		$cat_meta = isset($game_categories[$game->slug]) ? $game_categories[$game->slug] : ['keys' => 'arkades', 'label' => 'Arkādes'];

		$tpl->assign([
			'game-id' => $game->slug,
			'game-title' => $game->title,
			'game-url' => $game->url,
			'game-icon' => $game->icon,
			'game-badge' => $badge_html,
			'game-cat-keys' => $cat_meta['keys'],
			'game-cat-label' => $cat_meta['label'],
			'game-desc' => $game->desc,
			'game-rate' => get_game_rate_html($game),
			'top-player' => $top_player_info ? 'Līderis: ' . $top_player_info : ''
		]);
	}
}

// Recent high scores banner across all games
$game_meta_map = [
	'ut99' => ['title' => 'Unreal Tournament 99', 'url' => '/ut99', 'is_time' => false],
	'tetris' => ['title' => 'Tetris', 'url' => '/tetris', 'is_time' => false],
	'snake' => ['title' => 'Čūska', 'url' => '/snake', 'is_time' => false],
	'karatavas' => ['title' => 'Karātavas', 'url' => '/karatavas', 'is_time' => false],
	'memory' => ['title' => 'Atmiņas spēle', 'url' => '/memory', 'is_time' => false],
	'2048' => ['title' => '2048', 'url' => '/2048-spele', 'is_time' => false],
	'minu-mekletajs' => ['title' => 'Mīnu Meklētājs', 'url' => '/minu-mekletajs', 'is_time' => true],
	'minu-mekletajs-easy' => ['title' => 'Mīnu Meklētājs (Iesācējs)', 'url' => '/minu-mekletajs', 'is_time' => true],
	'minu-mekletajs-medium' => ['title' => 'Mīnu Meklētājs (Vidējs)', 'url' => '/minu-mekletajs', 'is_time' => true],
	'minu-mekletajs-hard' => ['title' => 'Mīnu Meklētājs (Eksperts)', 'url' => '/minu-mekletajs', 'is_time' => true],
	'sudoku' => ['title' => 'Sudoku', 'url' => '/sudoku', 'is_time' => true],
	'wordle' => ['title' => 'Wordle', 'url' => '/wordle', 'is_time' => false],
	'rulete' => ['title' => 'Rulete', 'url' => '/rulete', 'is_time' => false],
	'flappy' => ['title' => 'Lidojošais Eksis', 'url' => '/flappy', 'is_time' => false],
	'invaders' => ['title' => 'Space Invaders', 'url' => '/invaders', 'is_time' => false],
	'augsup' => ['title' => 'Augšup', 'url' => '/augsup', 'is_time' => false],
	'vardes' => ['title' => 'Vardes', 'url' => '/vardes', 'is_time' => false],
	'runner' => ['title' => 'Runner', 'url' => '/runner', 'is_time' => false],
	'tornis' => ['title' => 'Tornis', 'url' => '/tornis', 'is_time' => false],
	'rezonanse' => ['title' => 'Rezonanse', 'url' => '/rezonanse', 'is_time' => false],
	'tanki' => ['title' => 'Tanki 1990', 'url' => '/tanki', 'is_time' => false],
	'arkanoid' => ['title' => 'Arkanoid', 'url' => '/arkanoid', 'is_time' => false],
	'pacman' => ['title' => 'Exs-Man', 'url' => '/pacman', 'is_time' => false],
];

$start_of_today = strtotime('today midnight');
$is_asc_games = ['wordle', 'minu-mekletajs', 'sudoku'];

// Fetch recent scores (cached 60s in Memcached, batched without N+1 queries)
$recent_scores = $m->get('speles_recent_scores_v1');
if (!is_array($recent_scores)) {
	$raw_scores = $db->get_results("SELECT * FROM gamescore WHERE score > 0 ORDER BY time DESC LIMIT 100");
	$recent_scores = [];
	$seen_keys = [];

	if (!empty($raw_scores)) {
		// Pre-fetch all today's scores once so we can resolve best-today in PHP without N+1 queries
		$today_best_map = [];
		$has_today = false;
		foreach ($raw_scores as $sc) {
			if ($sc->time >= $start_of_today) {
				$has_today = true;
				break;
			}
		}
		if ($has_today) {
			$today_rows = $db->get_results("SELECT * FROM gamescore WHERE time >= '$start_of_today' AND score > 0 ORDER BY time DESC");
			if (!empty($today_rows)) {
				foreach ($today_rows as $tr) {
					$tkey = $tr->user_id . '_' . $tr->game;
					$is_asc = in_array($tr->game, $is_asc_games, true);
					if (!isset($today_best_map[$tkey])) {
						$today_best_map[$tkey] = $tr;
					} else {
						$cur = $today_best_map[$tkey];
						if (($is_asc && $tr->score < $cur->score) || (!$is_asc && $tr->score > $cur->score)) {
							$today_best_map[$tkey] = $tr;
						}
					}
				}
			}
		}

		foreach ($raw_scores as $sc) {
			$key = $sc->user_id . '_' . $sc->game;
			if (isset($seen_keys[$key])) {
				continue;
			}
			$seen_keys[$key] = true;

			if ($sc->time >= $start_of_today && isset($today_best_map[$key])) {
				$recent_scores[] = $today_best_map[$key];
			} else {
				$recent_scores[] = $sc;
			}

			if (count($recent_scores) >= 8) {
				break;
			}
		}

		// Sort recent scores by time DESC
		usort($recent_scores, function($a, $b) {
			return $b->time - $a->time;
		});
	}
	$m->set('speles_recent_scores_v1', $recent_scores, 60);
}

if (!empty($recent_scores)) {
	$tpl->newBlock('recent-scores-block');
	foreach ($recent_scores as $sc) {
		$u = get_user($sc->user_id);
		if ($u) {
			$g_key = $sc->game;
			$meta = isset($game_meta_map[$g_key]) ? $game_meta_map[$g_key] : [
				'title' => ucfirst(str_replace('-', ' ', $g_key)),
				'url' => '/' . strtok($g_key, '-'),
				'is_time' => false
			];

			if ($g_key == 'wordle') {
				$g_cnt = floor($sc->score / 1000);
				$sec = $sc->score % 1000;
				$mins = floor($sec / 60);
				$s = $sec % 60;
				$score_str = $g_cnt . '/6 (' . sprintf('%02d:%02d', $mins, $s) . ')';
			} elseif (!empty($meta['is_time'])) {
				$mins = floor($sc->score / 60);
				$secs = $sc->score % 60;
				$score_str = sprintf('%02d:%02d', $mins, $secs);
			} else {
				$score_str = number_format($sc->score, 0, '', ' ');
			}

			$tpl->newBlock('recent-score-node');
			$tpl->assign([
				'user-url' => mkurl('user', $u->id, $u->nick),
				'user-nick' => usercolor($u->nick, $u->level),
				'game-name' => $meta['title'],
				'game-url' => $meta['url'],
				'score' => $score_str,
				'time-ago' => time_ago($sc->time)
			]);
		}
	}
}

if (!$auth->ok) {
	$tpl->newBlock('seo-text');
}
