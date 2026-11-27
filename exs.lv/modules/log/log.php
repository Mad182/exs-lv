<?php

if (!im_mod()) {
	redirect();
}

$end = 100;
$skip = 0;
if (isset($_GET['skip'])) {
	$skip = max(0, (int) $_GET['skip']);
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

	$tpl->assign([
		'selected-' . (int) $_POST['criteria'] => ' selected="selected"',
		'field-value' => $value
	]);
}

$logs = $db->get_results("SELECT * FROM `logs` $query_where ORDER BY `created` DESC LIMIT $skip, $end");
if ($logs) {
	// Batch-prefetch referenced pages, wallpapers, and clans to avoid N+1 queries
	$page_ids = [];
	$wp_ids = [];
	$clan_ids = [];
	foreach ($logs as $l) {
		$fk = (int) $l->foreign_key;
		if ($fk > 0) {
			if ($l->foreign_table == 'pages') {
				$page_ids[$fk] = $fk;
			} elseif ($l->foreign_table == 'wallpapers') {
				$wp_ids[$fk] = $fk;
			} elseif ($l->foreign_table == 'clans') {
				$clan_ids[$fk] = $fk;
			}
		}
	}

	$pages_map = [];
	if ($page_ids) {
		$rows = $db->get_results("SELECT `id`, `title`, `strid` FROM `pages` WHERE `id` IN (" . implode(',', $page_ids) . ")");
		if ($rows) {
			foreach ($rows as $r) {
				$pages_map[$r->id] = $r;
			}
		}
	}

	$wps_map = [];
	if ($wp_ids) {
		$rows = $db->get_results("SELECT `id`, `image`, `date` FROM `wallpapers` WHERE `id` IN (" . implode(',', $wp_ids) . ")");
		if ($rows) {
			foreach ($rows as $r) {
				$wps_map[$r->id] = $r;
			}
		}
	}

	$clans_map = [];
	if ($clan_ids) {
		$rows = $db->get_results("SELECT `id`, `title`, `strid` FROM `clans` WHERE `id` IN (" . implode(',', $clan_ids) . ")");
		if ($rows) {
			foreach ($rows as $r) {
				$clans_map[$r->id] = $r;
			}
		}
	}

	foreach ($logs as $log) {
		$tpl->newBlock('logs-list-node');
		if ($log->user_id) {
			$who = get_user($log->user_id);
			if (is_valid_user($who)) {
				$log->user_id = '<a href="/user/' . $who->id . '">' . usercolor($who->nick, $who->level, false, $who->id) . '</a>';
			} else {
				$u_nick = !empty($who->nick) ? mb_strtolower(trim($who->nick), 'UTF-8') : '';
				if ($u_nick === 'nezināms' || $u_nick === 'nezinams') {
					$log->user_id = '<em>nezināms</em>';
				} else {
					$log->user_id = '<em>dzēsts</em>';
				}
			}
		} else {
			$log->user_id = '';
		}

		$place = '';
		$fk = (int) $log->foreign_key;

		if ($log->foreign_table == 'pages' && isset($pages_map[$fk])) {
			$page = $pages_map[$fk];
			$place = '<a href="/read/' . $page->strid . '">' . $log->foreign_table . '-' . $log->foreign_key . '</a>';
		} elseif ($log->foreign_table == 'users' && ($user = get_user($log->foreign_key))) {
			if (is_valid_user($user)) {
				$place = '<a href="/user/' . $user->id . '">' . $log->foreign_table . ': ' . $user->nick . '</a>';
			} else {
				$place = $log->foreign_table . ': ' . ($user->nick ?? 'dzēsts');
			}
		} elseif ($log->foreign_table == 'wallpapers' && isset($wps_map[$fk])) {
			$wp = $wps_map[$fk];
			$place = '<a class="lightbox" href="//img.exs.lv/dati/wallpapers/' . $wp->image . '"><img src="//img.exs.lv/dati/wallpapers/thb/' . $wp->image . '" alt="' . $wp->image . '" style="width:100px" /></a>';
			$log->action .= ' (' . $wp->date . ')';
		} elseif ($log->foreign_table == 'clans' && isset($clans_map[$fk])) {
			$group = $clans_map[$fk];
			if (!empty($group->strid)) {
				$link = '/' . $group->strid;
			} else {
				$link = '/group/' . $log->foreign_key;
			}
			$place = '<a href="' . $link . '">group: ' . $group->title . '</a>';
		} elseif(!empty($log->foreign_table) && !empty($log->foreign_key)) {
			$place = $log->foreign_table . '-' . $log->foreign_key;
		}

		$tpl->assign([
			'log-id' => $log->id,
			'log-ip' => $log->ip,
			'log-who' => $log->user_id,
			'log-place' => $place,
			'log-action' => textlimit($log->action, 255),
			'log-time' => str_replace('-', '.', $log->created),
		]);
	}
}

if (empty($_POST)) {
	$pager = pager($db->get_var("SELECT count(*) FROM `logs`"), $skip, $end, '/' . $category->textid . '/?skip=');
	$tpl->assignGlobal([
		'pager-next' => $pager['next'],
		'pager-prev' => $pager['prev'],
		'pager-numeric' => $pager['pages']
	]);
}

