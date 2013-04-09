<?php

if ($auth->ok) {
	if (isset($_GET['q']) && !empty($_GET['q'])) {

		$nick = sanitize(substr(trim($_GET['q']), 0, 32));

		if (strlen($nick) < 3) {
			echo 'Too short';
			exit;
		}

		if ($results = $db->get_results("SELECT id,nick FROM users WHERE nick LIKE '%" . $nick . "%'")) {
			if ($results) {
				$users = array();
				foreach ($results as $result) {
					$users[$result->id] = htmlspecialchars($result->nick);
				}
				header("Content-type: application/json");
				echo json_encode($users);
				exit;
			} else {
				echo 'Err';
				exit;
			}
		} else {
			echo '<span style="color: green;">OK</span>';
			exit;
		}
	} else {
		echo 'Err';
		exit;
	}
} else {
	echo 'Jāielogojas';
	exit;
}
