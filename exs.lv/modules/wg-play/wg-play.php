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
	return intval($_SESSION['hm_cgame_id']);
}

function set_wg_id($id = false) {
	$_SESSION['hm_cgame_id'] = intval($id);
}

function reset_wg_id() {
	$_SESSION['hm_cgame_id'] = '';
}

if (isset($_GET['_'])) {
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
} else {
	$tpl = new TemplatePower('modules/wg-play/wg-play.tpl');
	$tpl->prepare();
}

if ((isset($_GET['act']) && $_GET['act'] == 'top') or (isset($_GET['var1']) && $_GET['var1'] == 'top')) {
	$tpl->assign([
		'active-tab-top' => ' activeTab',
	]);

	$topusers = $db->get_results("SELECT * FROM wg_results WHERE date = '" . date('Y-m-d') . "' AND user_id != '0' ORDER BY points DESC, games ASC LIMIT 200");

	if ($topusers) {
		$tpl->newBlock('hm-top');
		$i = 1;
		foreach ($topusers as $topuser) {
			$special = '';
			if ($auth->id == $topuser->user_id) {
				$special = ' style="background: #ffffaa;font-weight: bold;"';
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
			'active-tab-game' => ' activeTab',
		]);
	}

	$letters = ['a', 'ā', 'b', 'c', 'č', 'd', 'e', 'ē', 'f', 'g', 'ģ', 'h', 'i', 'ī', 'j', 'k', 'ķ', 'l', 'ļ', 'm', 'n', 'ņ', 'o', 'p', 'r', 's', 'š', 't', 'u', 'ū', 'v', 'z', 'ž', 'w', 'x', 'y', 'q'];

	if (!get_wg_id()) {

		$query = 'WHERE 1 = 1';
		$lastgames = $db->get_results("SELECT * FROM `wg_games` WHERE `user_id` = '$auth->id' ORDER BY `id` DESC LIMIT 100");
		if ($lastgames) {
			foreach ($lastgames as $lastgame) {
				$query .= " AND `id` != '$lastgame->word_id'";
			}
		}

		$word_id = $db->get_var("SELECT id FROM `wg_words` " . $query . " ORDER BY rand() LIMIT 1");
		$db->query("INSERT INTO wg_games (word_id,correct,wrong,user_id) VALUES ('$word_id','" . serialize([]) . "','" . serialize([]) . "','$auth->id')");
		set_wg_id($db->insert_id);
		redirect('/' . $category->textid);
	} else {

		if (!$auth->ok) {
			$tpl->newBlock('hm-login');
		}

		$game_id = get_wg_id();
		$game = $db->get_row("SELECT * FROM wg_games WHERE id = '$game_id'");

		if ($game) {
			$tpl->newBlock('hm-game');

			$word = $db->get_row("SELECT * FROM `wg_words` WHERE `id` = '$game->word_id'");

			$wrong = unserialize($game->wrong);
			$correct = unserialize($game->correct);
			$guessed = $wrong + $correct;

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
							$lstr = '<a rel="nofollow" href="/' . $category->textid . '/?guess=' . urlencode($letter) . '">' . $letter . '</a>';
						}
						$tpl->assign('letter', $lstr);
					}
				} else {

					$points = 10 - $wrongs;

					$tpl->assign([
						'hint' => 'Tu uzvarēji un ieguvi ' . $points . ' punktus ;) atbilde ir:',
						'guess' => $outstr . '<br><br><a id="hm-new-game" href="/' . $category->textid . '">Jauna spēle</a>',
						'img' => $wrongs,
					]);

					$date = date('Y-m-d');
					if ($db->get_var("SELECT count(*) FROM wg_results WHERE user_id = '$auth->id' AND date = '$date'")) {
						$db->query("UPDATE wg_results SET games = games+1, points = points+$points WHERE user_id = '$auth->id' AND date = '$date'");
					} else {
						$db->query("INSERT INTO wg_results (user_id,date,points,games) VALUES ('$auth->id','$date','$points','1')");
					}
					reset_wg_id();
				}

				$db->query("UPDATE wg_games SET correct = '" . serialize($correct) . "', wrong = '" . serialize($wrong) . "' WHERE id = '$game_id' LIMIT 1");
			} else {

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
					'guess' => $outstr . '<br><br><a id="hm-new-game" href="/' . $category->textid . '">Jauna spēle</a>',
					'img' => 10,
				]);

				$db->query("UPDATE wg_games SET correct = '" . serialize($correct) . "', wrong = '" . serialize($wrong) . "' WHERE id = '$game_id' LIMIT 1");

				$date = date('Y-m-d');

				if ($db->get_var("SELECT count(*) FROM wg_results WHERE user_id = '$auth->id' AND date = '$date'")) {
					$db->query("UPDATE wg_results SET games = games+1 WHERE user_id = '$auth->id' AND date = '$date'");
				} else {
					$db->query("INSERT INTO wg_results (user_id,date,points,games) VALUES ('$auth->id','$date','0','1')");
				}
				reset_wg_id();
			}
		}
	}
}

if (!$ajax) {
	$tpl->newBlock('hm-gbody-bottom');
} else {
	$tpl->printToScreen();
	exit;
}

