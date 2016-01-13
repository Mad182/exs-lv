<?php

if ($auth->level == 1) {
	$tpl->newBlock('smslog');
	$logs = $db->get_results("SELECT * FROM sms ORDER BY id DESC LIMIT 100");
	if ($logs) {
		foreach ($logs as $log) {
			$tpl->newBlock('smslog-node');
			$user = get_user($log->message);
			$tpl->assign(array(
				'id' => $log->id,
				'nick' => $user->nick,
				'message' => $log->message,
				'suspended' => $log->suspended,
				'message_id' => $log->message_id,
				'sender' => $log->sender
			));
		}
	}
} else {
	redirect();
}
