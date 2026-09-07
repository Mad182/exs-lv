<?php

/**
 * EXS.LV Spēļu katalogs (/speles)
 */

$tpl->assignInclude('module-head', 'modules/' . $category->module . '/head.tpl');
$tpl->prepare();

// List of available games ordered by votes
$games_list = $db->get_results("
	SELECT * FROM `games` 
	WHERE `status` = 'active' 
	ORDER BY `vote_value` DESC, `id` ASC
");

// Determine the 3 latest games to display "Jaunums" badge
$latest_game_ids = $db->get_results("SELECT id FROM `games` WHERE `status` = 'active' ORDER BY `id` DESC LIMIT 3");
$latest_ids = !empty($latest_game_ids) ? array_map(function($g) { return (int)$g->id; }, $latest_game_ids) : [];

if (!empty($games_list)) {
	foreach ($games_list as $game) {
		$tpl->newBlock('game-card');

		$top_player_info = '';
		if (!empty($game->game_code)) {
			$is_asc = in_array($game->game_code, ['wordle', 'minu-mekletajs', 'sudoku']);
			$order = $is_asc ? 'ASC' : 'DESC';
			$where_extra = $is_asc ? " AND score > 0" : "";
			$top_score = $db->get_row("SELECT * FROM gamescore WHERE game = '" . $game->game_code . "' $where_extra ORDER BY score $order LIMIT 1");
			if ($top_score) {
				$u = $db->get_row("SELECT id, nick, level FROM users WHERE id = '$top_score->user_id'");
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

		$tpl->assign([
			'game-id' => $game->slug,
			'game-title' => $game->title,
			'game-url' => $game->url,
			'game-icon' => $game->icon,
			'game-badge' => $badge_html,
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
	'rezonanse' => ['title' => 'Rezonanse', 'url' => '/rezonanse', 'is_time' => false],
];

$recent_scores = $db->get_results("SELECT * FROM gamescore WHERE score > 0 ORDER BY time DESC LIMIT 8");
if ($recent_scores) {
	$tpl->newBlock('recent-scores-block');
	foreach ($recent_scores as $sc) {
		$u = $db->get_row("SELECT id, nick, level FROM users WHERE id = '$sc->user_id'");
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
