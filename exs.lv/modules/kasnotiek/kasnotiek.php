<?php

$actions = $db->get_results("SELECT
		`userlogs`.`action`,
		`userlogs`.`time`,
		`userlogs`.`avatar`,
		`userlogs`.`user`,
		`users`.`avatar` AS `uavatar`,
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
		if (!$action->avatar) {
			if ($action->av_alt) {
				$action->avatar = '/dati/bildes/u_small/' . $action->uavatar;
			} elseif ($action->uavatar) {
				$action->avatar = '/dati/bildes/useravatar/' . $action->uavatar;
			} else {
				$action->avatar = '/dati/bildes/u_small/none.png';
			}
		}
		$tpl->newBlock('user-actions-node');
		$tpl->assign(array(
			'action' => $action->action,
			'usrnick' => $action->nick,
			'action-date' => time_ago($action->time),
			'action-avatar' => $action->avatar,
		));
	}
}

$tpl->newBlock('meta-description');
$tpl->assign('description', 'Pēdējās lietotāju aktivitātes exs.lv mājas lapā');
