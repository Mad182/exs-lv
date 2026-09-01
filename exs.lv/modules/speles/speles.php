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
		'icon' => '/bildes/icons/games/tetris.png',
		'badge' => 'Populāra',
		'badge_class' => 'label-important',
		'desc' => 'Klasiskais Tetris ar SRS griešanos, līmeņiem un tiešsaistes topos.',
		'game_code' => 'tetris'
	],
	[
		'id' => 'snake',
		'title' => 'Čūska',
		'url' => '/snake',
		'icon' => '/bildes/icons/games/snake.png',
		'badge' => 'Klasika',
		'badge_class' => 'label-info',
		'desc' => 'Izsalkusī čūska ar 6 sarežģītības līmeņiem un šķēršļu sienām.',
		'game_code' => 'snake'
	],
	[
		'id' => 'karatavas',
		'title' => 'Karātavas',
		'url' => '/karatavas',
		'icon' => '/bildes/icons/games/karatavas.png',
		'badge' => 'Vārdu spēle',
		'badge_class' => 'label-warning',
		'desc' => 'Interaktīva vārdu minēšanas spēle ar tūkstošiem latviešu valodas vārdu.',
		'game_code' => 'karatavas'
	],
	[
		'id' => 'memory',
		'title' => 'Atmiņas spēle',
		'url' => '/memory',
		'icon' => '/bildes/icons/games/memory.png',
		'badge' => 'Jaunums',
		'badge_class' => 'label-success',
		'desc' => 'Atrodi vienādos EXS.LV lietotāju avatarus! Maināmi 4x4, 6x4 un 6x6 tīkli.',
		'game_code' => 'memory'
	],
	[
		'id' => '2048',
		'title' => '2048',
		'url' => '/2048-spele',
		'icon' => '/bildes/icons/games/2048.png',
		'badge' => 'Populāra',
		'badge_class' => 'label-important',
		'desc' => 'Bīdi un apvieno vienādos skaitļu lauciņus, lai sasniegtu 2048 flīzi un uzstādītu rekordu!',
		'game_code' => '2048'
	],
	[
		'id' => 'minu-mekletajs',
		'title' => 'Mīnu Meklētājs',
		'url' => '/minu-mekletajs',
		'icon' => '/bildes/icons/games/minu-mekletajs.png',
		'badge' => 'Populāra',
		'badge_class' => 'label-important',
		'desc' => 'Klasiskā Minesweeper spēle ar 3 grūtības līmeņiem un ātruma rekordu topu.',
		'game_code' => 'minu-mekletajs'
	],
	[
		'id' => 'sudoku',
		'title' => 'Sudoku',
		'url' => '/sudoku',
		'icon' => '/bildes/icons/games/sudoku.png',
		'badge' => 'Populāra',
		'badge_class' => 'label-important',
		'desc' => 'Klasiskā Sudoku mīkla ar 3 sarežģītības līmeņiem, zīmuļa piezīmēm un mājieniem.',
		'game_code' => 'sudoku'
	],
	[
		'id' => 'wordle',
		'title' => 'Wordle',
		'url' => '/wordle',
		'icon' => '/bildes/icons/games/wordle.png',
		'badge' => 'Jaunums',
		'badge_class' => 'label-success',
		'desc' => 'Populārā 5 burtu vārdu minēšanas spēle latviešu valodā ar dienas vārdu un treniņu režīmu.',
		'game_code' => 'wordle'
	],
	[
		'id' => 'rulete',
		'title' => 'Rulete',
		'url' => '/rulete',
		'icon' => '/bildes/icons/games/rulete.png',
		'badge' => 'Kazino',
		'badge_class' => 'label-warning',
		'desc' => 'Klasiskā Eiropas kazino rulete ar 100 zelta sākuma kapitālu un ikdienas bilances atjaunošanu.',
		'game_code' => 'rulete'
	],
	[
		'id' => 'desas',
		'title' => 'Desas',
		'url' => '/desas',
		'icon' => '/bildes/icons/games/desas.png',
		'badge' => 'Diviem',
		'badge_class' => 'label-inverse',
		'desc' => 'Klasiskā desu (Tic-Tac-Toe) spēle.',
		'game_code' => 'desas'
	],
	[
		'id' => 'flappy',
		'title' => 'Lidojošais Eksis',
		'url' => '/flappy',
		'icon' => '/bildes/icons/games/flappy.png',
		'badge' => 'Jaunums',
		'badge_class' => 'label-success',
		'desc' => 'Vadā savu pārlūka avatāru cauri šķēršļiem, vāc punktus un uzstādi jaunu rekordu!',
		'game_code' => 'flappy'
	],
	[
		'id' => 'invaders',
		'title' => 'Space Invaders',
		'url' => '/invaders',
		'icon' => '/bildes/icons/games/invaders.png',
		'badge' => 'Arkāde',
		'badge_class' => 'label-info',
		'desc' => 'Klasiskā kosmosa iebrucēju spēle bezgalīgā režīmā. Aizstāvi Zemi, vāc punktus un uzstādi jaunu rekordu!',
		'game_code' => 'invaders'
	],
	[
		'id' => 'augsup',
		'title' => 'Augšup',
		'url' => '/augsup',
		'icon' => '/bildes/icons/games/augsup.png',
		'badge' => 'Jaunums',
		'badge_class' => 'label-success',
		'desc' => 'Lēkā pa platformām ar savu avatāru, sasniedz mākoņus un uzstādi jaunu augstuma rekordu!',
		'game_code' => 'augsup'
	],
	[
		'id' => 'vardes',
		'title' => 'Vardes',
		'url' => '/vardes',
		'icon' => '/bildes/icons/games/vardes.png',
		'badge' => 'Jaunums',
		'badge_class' => 'label-success',
		'desc' => 'Šķērso bīstamo šoseju un upi ar baļķiem, lai sasniegtu liliju lapas un uzstādītu rekordu!',
		'game_code' => 'vardes'
	],
	[
		'id' => 'runner',
		'title' => 'Runner',
		'url' => '/runner',
		'icon' => '/bildes/icons/games/runner.png',
		'badge' => 'Jaunums',
		'badge_class' => 'label-success',
		'desc' => 'Bēdz no šķēršļiem un citu lietotāju avatariem, vāc zvaigznes un uzstādi jaunu rekordu!',
		'game_code' => 'runner'
	]
];

foreach ($games_list as $game) {
	$tpl->newBlock('game-card');

	$top_player_info = '';
	if (!empty($game['game_code'])) {
		$is_asc = in_array($game['game_code'], ['wordle', 'minu-mekletajs', 'sudoku']);
		$order = $is_asc ? 'ASC' : 'DESC';
		$where_extra = $is_asc ? " AND score > 0" : "";
		$top_score = $db->get_row("SELECT * FROM gamescore WHERE game = '" . $game['game_code'] . "' $where_extra ORDER BY score $order LIMIT 1");
		if ($top_score) {
			$u = $db->get_row("SELECT id, nick, level FROM users WHERE id = '$top_score->user_id'");
			if ($u) {
				if ($game['game_code'] == 'wordle') {
					$g_cnt = floor($top_score->score / 1000);
					$sec = $top_score->score % 1000;
					$mins = floor($sec / 60);
					$s = $sec % 60;
					$top_player_info = usercolor($u->nick, $u->level) . ' (' . $g_cnt . '/6, ' . sprintf('%02d:%02d', $mins, $s) . ')';
				} elseif (in_array($game['game_code'], ['minu-mekletajs', 'sudoku'])) {
					$mins = floor($top_score->score / 60);
					$s = $top_score->score % 60;
					$top_player_info = usercolor($u->nick, $u->level) . ' (' . sprintf('%02d:%02d', $mins, $s) . ')';
				} else {
					$top_player_info = usercolor($u->nick, $u->level) . ' (' . number_format($top_score->score, 0, '', ' ') . ' pīk)';
				}
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
