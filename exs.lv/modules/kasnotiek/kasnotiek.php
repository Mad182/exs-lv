<?php

$actions = $db->get_results("SELECT
		`userlogs`.`action`,
		`userlogs`.`time`,
		`userlogs`.`avatar` AS `action_avatar`,
		`userlogs`.`user`,
		`users`.`avatar`,
		`users`.`av_alt`,
		`users`.`nick`
	FROM
		`userlogs`,
		`users`
	WHERE
		`users`.`id` = `userlogs`.`user` AND
		`userlogs`.`lang` = '$lang'
	ORDER BY
		`userlogs`.`time` DESC
	LIMIT 40");

if ($actions) {
	$tpl->newBlock('user-actions');
	foreach ($actions as $action) {

		if (empty($action->action_avatar)) {
			$action->action_avatar = get_avatar($action, 's');
		}

		$tpl->newBlock('user-actions-node');
		$tpl->assign(array(
			'action' => $action->action,
			'usrnick' => $action->nick,
			'action-date' => time_ago($action->time),
			'action-avatar' => $action->action_avatar
		));
	}
}
