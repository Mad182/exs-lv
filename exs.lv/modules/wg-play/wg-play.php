<?php

function mbStringToArray($string) {
	$stop = mb_strlen($string);
	$result = [];
	for ($idx = 0; $idx < $stop; $idx++) {
		$result[] = mb_substr($string, $idx, 1);
	}
	return $result;
}

function get_wg_id() {
	return isset($_SESSION['hm_cgame_id']) ? intval($_SESSION['hm_cgame_id']) : 0;
}

function set_wg_id($id = false) {
	$_SESSION['hm_cgame_id'] = intval($id);
}

function reset_wg_id() {
	$_SESSION['hm_cgame_id'] = '';
}

if (isset($_GET['_']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
	$ajax = true;
} else {
	$ajax = false;
}

if (!$ajax) {
	$add_css[] = 'hangman.css';
	$tpl->newBlock('hm-gbody-top');
	$tpl->assign([
		'cat-id' => $category->id,
	]);
	if (!$auth->ok) {
		$tpl->newBlock('seo-text');
	}
} else {
	$tpl = new TemplatePower('modules/wg-play/wg-play.tpl');
	$tpl->prepare();
}

if ((isset($_GET['act']) && $_GET['act'] == 'top') or (isset($_GET['var1']) && $_GET['var1'] == 'top')) {
	$tpl->assign([
		'active-tab-top' => ' active',
	]);

	$topusers = $db->get_results("SELECT * FROM wg_results WHERE date = '" . date('Y-m-d') . "' AND user_id != '0' ORDER BY points DESC, games ASC LIMIT 200");

	if ($topusers) {
		$tpl->newBlock('hm-top');
		$i = 1;
		foreach ($topusers as $topuser) {
			$special = '';
			if ($auth->id == $topuser->user_id) {
				$special = ' style="font-weight:bold"';
			}
			if ($i == 1) {
				$icon = '<img src="/bildes/icons/award_star_gold_3.png" alt="' . $i . '." title="' . $i . '." />';
			} elseif ($i == 2) {
				$icon = '<img src="/bildes/icons/award_star_silver_3.png" alt="' . $i . '." title="' . $i . '." />';
			} elseif ($i == 3) {
				$icon = '<img src="/bildes/icons/award_star_bronze_3.png" alt="' . $i . '." title="' . $i . '." />';
			} else {
				$icon = $i . '.';
			}

			$tpl->newBlock('top-node');
			$usr = $db->get_row("SELECT `nick`,`level` FROM users WHERE id = '$topuser->user_id'");
			$tpl->assign([
				'user-place' => $icon,
				'user-special' => $special,
				'user-id' => $topuser->user_id,
				'user-url' => mkurl('user', $topuser->user_id, $usr->nick),
				'user-nick' => usercolor($usr->nick, $usr->level),
				'user-ig_points' => $topuser->points,
				'user-ig_done' => $topuser->games,
				'p-game' => round($topuser->points / $topuser->games, 3),
			]);
			$i++;
		}
	}

} elseif ((isset($_GET['act']) && $_GET['act'] == 'overall-top') or (isset($_GET['var1']) && $_GET['var1'] == 'overall-top')) {
	$tpl->assign([
		'active-tab-overall-top' => ' active',
	]);

	$topusers = $db->get_results("SELECT user_id, SUM(points) as points, SUM(games) as games FROM wg_results WHERE user_id != '0' GROUP BY user_id ORDER BY points DESC, games ASC LIMIT 250");

	if ($topusers) {
		$tpl->newBlock('hm-top');
		$i = 1;
		foreach ($topusers as $topuser) {
			$special = '';
			if ($auth->id == $topuser->user_id) {
				$special = ' style="font-weight:bold"';
			}
			if ($i == 1) {
				$icon = '<img src="/bildes/icons/award_star_gold_3.png" alt="' . $i . '." title="' . $i . '." />';
			} elseif ($i == 2) {
				$icon = '<img src="/bildes/icons/award_star_silver_3.png" alt="' . $i . '." title="' . $i . '." />';
			} elseif ($i == 3) {
				$icon = '<img src="/bildes/icons/award_star_bronze_3.png" alt="' . $i . '." title="' . $i . '." />';
			} else {
				$icon = $i . '.';
			}

			$tpl->newBlock('top-node');
			$usr = $db->get_row("SELECT `nick`,`level` FROM users WHERE id = '$topuser->user_id'");
			$tpl->assign([
				'user-place' => $icon,
				'user-special' => $special,
				'user-id' => $topuser->user_id,
				'user-url' => mkurl('user', $topuser->user_id, $usr->nick),
				'user-nick' => usercolor($usr->nick, $usr->level),
				'user-ig_points' => $topuser->points,
				'user-ig_done' => $topuser->games,
				'p-game' => round($topuser->points / $topuser->games, 3),
			]);
			$i++;
		}
	}

} else {
	if (!$ajax) {
		$tpl->assign([
			'active-tab-game' => ' active',
		]);
	}

	$letters = ['a', 'ā', 'b', 'c', 'č', 'd', 'e', 'ē', 'f', 'g', 'ģ', 'h', 'i', 'ī', 'j', 'k', 'ķ', 'l', 'ļ', 'm', 'n', 'ņ', 'o', 'p', 'r', 's', 'š', 't', 'u', 'ū', 'v', 'z', 'ž', 'w', 'x', 'y', 'q'];

	$game_id = get_wg_id();
	$game = null;
	if ($game_id) {
		$game = $db->get_row("SELECT * FROM wg_games WHERE id = '$game_id' AND status = 0");
	}

	if (!$game_id || !$game) {
		$query = 'WHERE 1 = 1';
		if ($auth->ok && $auth->id > 0) {
			$lastgames = $db->get_results("SELECT * FROM `wg_games` WHERE `user_id` = '$auth->id' ORDER BY `id` DESC LIMIT 100");
			if ($lastgames) {
				foreach ($lastgames as $lastgame) {
					$query .= " AND `id` != '$lastgame->word_id'";
				}
			}
		}

		$word_id = $db->get_var("SELECT id FROM `wg_words` " . $query . " ORDER BY rand() LIMIT 1");
		$user_id_val = ($auth->ok && $auth->id > 0) ? $auth->id : 0;
		$db->query("INSERT INTO wg_games (word_id, correct, wrong, user_id, created_at, status) VALUES ('$word_id', '" . serialize([]) . "', '" . serialize([]) . "', '$user_id_val', '" . time() . "', 0)");
		$game_id = $db->insert_id;
		set_wg_id($game_id);
		$game = $db->get_row("SELECT * FROM wg_games WHERE id = '$game_id'");

		if (!$ajax) {
			redirect('/' . $category->textid);
		}
	}

	if ($game_id && $game) {

		if (!$auth->ok) {
			$tpl->newBlock('hm-login');
		}

		$tpl->newBlock('hm-game');

		$word = $db->get_row("SELECT * FROM `wg_words` WHERE `id` = '$game->word_id'");

		$wrong = unserialize($game->wrong);
		$correct = unserialize($game->correct);
		$guessed = array_merge($wrong, $correct);

		if (isset($_GET['guess']) && in_array($_GET['guess'], $letters) && !in_array($_GET['guess'], $guessed)) {
			$guess = $_GET['guess'];

			if (stristr($word->word, $guess)) {
				$correct[] = $guess;
			} else {
				$wrong[] = $guess;
			}
			$guessed[] = $guess;
		}

		$wrongs = count($wrong);

		$word_letters = mbStringToArray($word->word);

		if ($wrongs < 10) {

			$outstr = '';
			$hasempty = false;
			foreach ($word_letters as $word_letter) {
				if ($word_letter == ' ') {
					$outstr .= '&nbsp; ';
				} elseif (in_array($word_letter, $correct)) {
					$outstr .= $word_letter . '&nbsp;';
				} else {
					$outstr .= '_&nbsp;';
					$hasempty = true;
				}
			}

			if ($hasempty) {

				$tpl->assign([
					'hint' => $word->hint,
					'guess' => $outstr,
					'img' => $wrongs,
				]);

				foreach ($letters as $letter) {
					$tpl->newBlock('hm-letter');
					if (in_array($letter, $correct)) {
						$lstr = '<span class="correct">' . $letter . '</span>';
					} elseif (in_array($letter, $wrong)) {
						$lstr = '<span class="wrong">' . $letter . '</span>';
					} else {
						$lstr = '<a rel="nofollow" href="/' . $category->textid . '/?guess=' . urlencode($letter) . '&_">' . $letter . '</a>';
					}
					$tpl->assign('letter', $lstr);
				}
			} else {
				// WIN logic - Anti-cheat race condition & time verification
				$points = 10 - $wrongs;

				$tpl->assign([
					'hint' => 'Tu uzvarēji un ieguvi ' . $points . ' punktus ;) atbilde ir:',
					'guess' => $outstr . '<br><br><a id="hm-new-game" href="/' . $category->textid . '?_">Jauna spēle</a>',
					'img' => $wrongs,
				]);

				// Mark game as finished atomically to avoid race-condition multi-scoring
				$db->query("UPDATE wg_games SET status = 1 WHERE id = '$game_id' AND status = 0");
				if ($db->affected_rows > 0) {
					if ($auth->ok && $auth->id > 0) {
						$date = date('Y-m-d');
						if ($db->get_var("SELECT count(*) FROM wg_results WHERE user_id = '$auth->id' AND date = '$date'")) {
							$db->query("UPDATE wg_results SET games = games+1, points = points+$points WHERE user_id = '$auth->id' AND date = '$date'");
						} else {
							$db->query("INSERT INTO wg_results (user_id, date, points, games) VALUES ('$auth->id', '$date', '$points', '1')");
						}

						// Sync with gamescore table for global leaderboards
						$today_points = (int) $db->get_var("SELECT points FROM wg_results WHERE user_id = '$auth->id' AND date = '$date'");
						$existing_gs = $db->get_row("SELECT * FROM gamescore WHERE game = 'karatavas' AND user_id = '$auth->id'");
						$prev_score = $existing_gs ? (int)$existing_gs->score : 0;

						if (!$existing_gs) {
							$db->query("INSERT INTO gamescore (user_id, game, score, time) VALUES ('$auth->id', 'karatavas', '$today_points', '" . time() . "')");
						} else {
							$db->query("UPDATE gamescore SET score = '$today_points', time = '" . time() . "' WHERE id = '$existing_gs->id'");
						}

						if ($today_points > $prev_score) {
							push('Uzstādīja jaunu rekordu spēlē <a href="/karatavas">Karātavas</a> (' . number_format($today_points, 0, '', ' ') . ' punkti)', '/bildes/icons/games/karatavas.png', 'game-karatavas-' . $auth->id);
						}
					}
				}
				reset_wg_id();
			}

			$db->query("UPDATE wg_games SET correct = '" . serialize($correct) . "', wrong = '" . serialize($wrong) . "' WHERE id = '$game_id' LIMIT 1");
		} else {
			// LOSS logic
			$outstr = '';
			foreach ($word_letters as $word_letter) {
				if ($word_letter == ' ') {
					$outstr .= '&nbsp; ';
				} elseif (in_array($word_letter, $correct)) {
					$outstr .= $word_letter . '&nbsp;';
				} else {
					$outstr .= '<span style="color: #900;">' . $word_letter . '</span>&nbsp;';
				}
			}

			$strs = ['Tu zaudēji ;(', 'Ha ha! Tu zaudēji :P', 'Šoreiz nepaviecās :|', 'Tu zaudēji, es uzvarēju :P', 'Karājies, karājies, zaudētāj :P'];
			shuffle($strs);

			$tpl->assign([
				'hint' => $strs[0] . ' atbilde ir:',
				'guess' => $outstr . '<br><br><a id="hm-new-game" href="/' . $category->textid . '?_">Jauna spēle</a>',
				'img' => 10,
			]);

			$db->query("UPDATE wg_games SET status = 1 WHERE id = '$game_id' AND status = 0");
			if ($db->affected_rows > 0) {
				if ($auth->ok && $auth->id > 0) {
					$date = date('Y-m-d');
					if ($db->get_var("SELECT count(*) FROM wg_results WHERE user_id = '$auth->id' AND date = '$date'")) {
						$db->query("UPDATE wg_results SET games = games+1 WHERE user_id = '$auth->id' AND date = '$date'");
					} else {
						$db->query("INSERT INTO wg_results (user_id, date, points, games) VALUES ('$auth->id', '$date', '0', '1')");
					}
				}
			}
			reset_wg_id();
		}
	}
}

if (!$ajax) {
	$tpl->newBlock('hm-gbody-bottom');
} else {
	$tpl->printToScreen();
	exit;
}
