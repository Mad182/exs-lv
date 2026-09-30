<?php

$priv = '';
if (!$auth->ok) {
	$priv = ' AND `userlogs`.`private` = 0 ';
}

$actions = $db->get_results("SELECT
		`userlogs`.`action`,
		`userlogs`.`time`,
		`userlogs`.`avatar` AS `action_avatar`,
		`userlogs`.`user`,
		`users`.`avatar`,
		`users`.`av_alt`,
		`users`.`nick`,
		`users`.`level`,
		`users`.`deleted`
	FROM
		`userlogs`,
		`users`
	WHERE
		`users`.`id` = `userlogs`.`user` AND
		`userlogs`.`lang` = '$lang'
		$priv
	ORDER BY
		`userlogs`.`time` DESC
	LIMIT 50");

if ($actions) {
	$tpl->newBlock('user-actions');
	foreach ($actions as $action) {

		if (empty($action->action_avatar)) {
			$action->action_avatar = get_avatar($action, 'm');
		} else {
			$action->action_avatar = str_replace(
				['/userpic/small/', '/u_small/'],
				['/userpic/medium/', '/useravatar/'],
				$action->action_avatar
			);
		}

		$action->action_avatar = str_replace('http://', '//', $action->action_avatar);

		if (is_valid_user($action)) {
			$usrnick = '<a href="/user/' . $action->user . '">' . usercolor($action->nick, $action->level, false, $action->user) . '</a>';
			$av_html = '<a href="/user/' . $action->user . '"><img class="av" src="' . $action->action_avatar . '" width="64" height="64" alt="" /></a>';
		} else {
			$u_nick = !empty($action->nick) ? mb_strtolower(trim($action->nick), 'UTF-8') : '';
			$usrnick = ($u_nick === 'nezināms' || $u_nick === 'nezinams') ? '<em>nezināms</em>' : '<em>dzēsts</em>';
			$av_html = '<img class="av" src="' . $action->action_avatar . '" width="64" height="64" alt="" />';
		}

		$tpl->newBlock('user-actions-node');
		$tpl->assign([
			'action' => $action->action,
			'usrnick' => $usrnick,
			'action-date' => time_ago($action->time),
			'action-avatar' => $action->action_avatar,
			'av-html' => $av_html
		]);
	}
}
