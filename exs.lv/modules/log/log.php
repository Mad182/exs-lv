<?php

if (!im_mod()) {
	redirect();
}

$end = 100;
$skip = 0;
if (isset($_GET['skip'])) {
	$skip = (int) $_GET['skip'];
}

$logs = $db->get_results("SELECT * FROM `logs` ORDER BY `created` DESC LIMIT $skip, $end");
if ($logs) {
	foreach ($logs as $log) {
		$tpl->newBlock('logs-list-node');
		if ($log->user_id) {
			$who = get_user($log->user_id);
			$log->user_id = '<a href="/user/' . $who->id . '">' . usercolor($who->nick, $who->level, false, $who->id) . '</a>';
		}

		$place = '';

		if ($log->foreign_table == 'pages') {
			$page = $db->get_row("SELECT `title`, `strid` FROM `pages` WHERE `id` = '$log->foreign_key'");
			$place = '<a href="/read/' . $page->strid . '">' . $log->foreign_table . '-' . $log->foreign_key . '</a>';
		} elseif ($log->foreign_table == 'users') {
			$user = get_user($log->foreign_key);
			$place = '<a href="/user/' . $user->id . '">' . $log->foreign_table . ': ' . $user->nick . '</a>';
		} else {
			$place = $log->foreign_table . '-' . $log->foreign_key;
		}

		$tpl->assign(array(
			'log-id' => $log->id,
			'log-ip' => $log->ip,
			'log-who' => $log->user_id,
			'log-place' => $place,
			'log-action' => $log->action,
			'log-time' => $log->created,
		));
	}
}

$pager = pager($db->get_var("SELECT count(*) FROM `logs`"), $skip, $end, '/' . $category->textid . '/?skip=');
$tpl->assignGlobal(array(
	'pager-next' => $pager['next'],
	'pager-prev' => $pager['prev'],
	'pager-numeric' => $pager['pages']
));

