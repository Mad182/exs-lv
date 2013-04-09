<?php

if ($auth->ok && (isset($_GET['action']) && $_GET['action'] == 'push') && isset($_GET['score']) && ($_SERVER['HTTP_REFERER'] == 'http://exs.lv/Snake' OR $_SERVER['HTTP_REFERER'] == 'http://exs.lv/Snake/' OR $_SERVER['HTTP_REFERER'] == 'http://exs.lv/?c=355')) {
	$newscore = intval($_GET['score']);
	$current = $db->get_row("SELECT * FROM gamescore WHERE game = 'snake' AND user_id = '$auth->id'");

	if (!$current) {
		$db->query("INSERT INTO gamescore (user_id,game,score,time) VALUES ('$auth->id','snake','$newscore','" . time() . "')");
	} elseif ($newscore > $current->score) {
		$db->query("UPDATE gamescore SET score = '" . $newscore . "', time='" . time() . "' WHERE id = '$current->id' AND user_id = '$auth->id'");
	}
	exit;
} elseif (isset($_GET['action'])) {
	exit;
}

$tpl->assignInclude('module-head', 'modules/' . $category->module . '/head.tpl');
$tpl->prepare();

$scores = $db->get_results("SELECT * FROM gamescore WHERE game = 'snake' ORDER BY score DESC LIMIT 50");

if ($scores) {
	$i = 1;
	foreach ($scores as $score) {
		$user = $db->get_row("SELECT id,nick,level FROM users WHERE id = '$score->user_id'");
		$tpl->newBlock('score-tr');
		$tpl->assign(array(
			'score' => $score->score,
			'user' => usercolor($user->nick, $user->level, false, $user->id),
			'user-url' => mkurl('user', $user->id, $user->nick),
			'date' => date('Y-m-d H:i', $score->time),
			'place' => $i++
		));
	}
}
