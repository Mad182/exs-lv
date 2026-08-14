<?php

/**
 * EXS.LV Spēļu katalogs (/speles)
 */

$tpl->assignInclude('module-head', 'modules/' . $category->module . '/head.tpl');
$tpl->prepare();

// List of available games
$games_list = [
	[
		'id' => 'tetris',
		'title' => 'Tetris',
		'url' => '/tetris',
		'icon' => '🧩',
		'badge' => 'Populāra',
		'badge_class' => 'label-important',
		'desc' => 'Klasiskais Tetris ar SRS griešanos, līmeņiem un tiešsaistes topos.',
		'game_code' => 'tetris'
	],
	[
		'id' => 'snake',
		'title' => 'Čūska',
		'url' => '/snake',
		'icon' => '🐍',
		'badge' => 'Klasika',
		'badge_class' => 'label-info',
		'desc' => 'Izsalkusī čūska ar 6 sarežģītības līmeņiem un šķēršļu sienām.',
		'game_code' => 'snake'
	],
	[
		'id' => 'karatavas',
		'title' => 'Karātavas',
		'url' => '/karatavas',
		'icon' => '🔤',
		'badge' => 'Vārdu spēle',
		'badge_class' => 'label-warning',
		'desc' => 'Interaktīva vārdu minēšanas spēle ar tūkstošiem latviešu valodas vārdu.',
		'game_code' => 'karatavas'
	],
	[
		'id' => 'memory',
		'title' => 'Atmiņas spēle',
		'url' => '/memory',
		'icon' => '🧠',
		'badge' => 'Jaunums',
		'badge_class' => 'label-success',
		'desc' => 'Atrodi vienādos EXS.LV lietotāju avatarus! Maināmi 4x4, 6x4 un 6x6 tīkli.',
		'game_code' => 'memory'
	],
	[
		'id' => '2048',
		'title' => '2048',
		'url' => '/2048-spele',
		'icon' => '🔢',
		'badge' => 'Jaunums',
		'badge_class' => 'label-success',
		'desc' => 'Bīdi un apvieno vienādos skaitļu lauciņus, lai sasniegtu 2048 flīzi un uzstādītu rekordu!',
		'game_code' => '2048'
	],
	[
		'id' => 'desas',
		'title' => 'Desas',
		'url' => '/desas',
		'icon' => '❌⭕',
		'badge' => 'Diviem',
		'badge_class' => 'label-inverse',
		'desc' => 'Klasiskā desu (Tic-Tac-Toe) spēle.',
		'game_code' => 'desas'
	]
];

foreach ($games_list as $game) {
	$tpl->newBlock('game-card');

	$top_player_info = '';
	if (!empty($game['game_code'])) {
		$top_score = $db->get_row("SELECT * FROM gamescore WHERE game = '" . $game['game_code'] . "' ORDER BY score DESC LIMIT 1");
		if ($top_score) {
			$u = $db->get_row("SELECT id, nick, level FROM users WHERE id = '$top_score->user_id'");
			if ($u) {
				$top_player_info = usercolor($u->nick, $u->level) . ' (' . number_format($top_score->score, 0, '', ' ') . ' pīk)';
			}
		}
	}

	$tpl->assign([
		'game-id' => $game['id'],
		'game-title' => $game['title'],
		'game-url' => $game['url'],
		'game-icon' => $game['icon'],
		'game-badge' => $game['badge'],
		'game-badge-class' => $game['badge_class'],
		'game-desc' => $game['desc'],
		'top-player' => $top_player_info ? 'Līderis: ' . $top_player_info : ''
	]);
}

// Recent high scores banner across all games
$recent_scores = $db->get_results("SELECT * FROM gamescore ORDER BY time DESC LIMIT 8");
if ($recent_scores) {
	$tpl->newBlock('recent-scores-block');
	foreach ($recent_scores as $sc) {
		$u = $db->get_row("SELECT id, nick, level FROM users WHERE id = '$sc->user_id'");
		if ($u) {
			$tpl->newBlock('recent-score-node');
			$tpl->assign([
				'user-url' => mkurl('user', $u->id, $u->nick),
				'user-nick' => usercolor($u->nick, $u->level),
				'game-name' => ucfirst($sc->game),
				'game-url' => '/' . $sc->game,
				'score' => number_format($sc->score, 0, '', ' '),
				'time-ago' => time_ago($sc->time)
			]);
		}
	}
}
