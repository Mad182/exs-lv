<?php

if (!im_mod()) {
	redirect();
}

$end = 100;
$skip = 0;
if (isset($_GET['skip'])) {
	$skip = (int) $_GET['skip'];
}

// meklētājs
$query_where = '';
if (isset($_POST['criteria']) && isset($_POST['value'])) {

	$value = h(strip_tags(trim($_POST['value'])));

	switch ((int) $_POST['criteria']) {
		// vieta
		case 1:
			$query_where = " WHERE `foreign_key` = '" . (int) $value . "' ";
			break;
		// darbība
		case 2:
			$query_where = " WHERE `action` LIKE '%" . sanitize($value) . "%' ";
			break;
		// IP adrese; laukā drīkst rakstīt % zīmi
		case 3:
			if (substr($value, 0, 1) == '%') {
				$value = substr($value, 1);
			}
			$query_where = " WHERE `ip` LIKE '" . sanitize($value) . "' ";
			break;
		// lietotāja ID
		default:
			$query_where = " WHERE `user_id` = '" . (int) $value . "' ";
			break;
	};
	$skip = 0;
	$limit = 200;

	$tpl->assign(array(
		'selected-' . (int) $_POST['criteria'] => ' selected="selected"',
		'field-value' => $value
	));
}

$logs = $db->get_results("SELECT * FROM `logs` $query_where ORDER BY `created` DESC LIMIT $skip, $end");
if ($logs) {
	foreach ($logs as $log) {
		$tpl->newBlock('logs-list-node');
		if ($log->user_id) {
			$who = get_user($log->user_id);
			$log->user_id = '<a href="/user/' . $who->id . '">' . usercolor($who->nick, $who->level, false, $who->id) . '</a>';
		}

		$place = '';

		// pieprasījumi nepieciešami if'ā, lai arī lokāli vienmēr strādātu,
		// nevis vienmēr jāatjauno gan rakstu un lietotāju, gan logu tabula
		if ($log->foreign_table == 'pages' && ($page = $db->get_row("SELECT `title`, `strid` FROM `pages` WHERE `id` = '$log->foreign_key'"))) {
			$place = '<a href="/read/' . $page->strid . '">' . $log->foreign_table . '-' . $log->foreign_key . '</a>';
		} elseif ($log->foreign_table == 'users' && ($user = get_user($log->foreign_key))) {
			$place = '<a href="/user/' . $user->id . '">' . $log->foreign_table . ': ' . $user->nick . '</a>';
		} elseif ($log->foreign_table == 'wallpapers' && $wp = $db->get_row("SELECT * FROM `wallpapers` WHERE `id` = $log->foreign_key")) {
			$place = '<a class="lightbox" href="//img.exs.lv/dati/wallpapers/' . $wp->image . '"><img src="//img.exs.lv/dati/wallpapers/thb/' . $wp->image . '" alt="' . $wp->image . '" style="width:100px" /></a>';
			$log->action .= ' (' . $wp->date . ')';
		} elseif ($log->foreign_table == 'clans' && $group = $db->get_row("SELECT `title`, `strid` FROM `clans` WHERE `id` = $log->foreign_key")) {
			if (!empty($group->strid)) {
				$link = '/' . $group->strid;
			} else {
				$link = '/group/' . $log->foreign_key;
			}
			$place = '<a href="' . $link . '">group: ' . $group->title . '</a>';
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

if (empty($_POST)) {
	$pager = pager($db->get_var("SELECT count(*) FROM `logs`"), $skip, $end, '/' . $category->textid . '/?skip=');
	$tpl->assignGlobal(array(
		'pager-next' => $pager['next'],
		'pager-prev' => $pager['prev'],
		'pager-numeric' => $pager['pages']
	));
}

