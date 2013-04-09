<?php

if (isset($_GET['user']) && !empty($_GET['user'])) {

	$nick = sanitize(substr(trim($_GET['user']), 0, 32));

	if (strlen($nick) < 3) {
		echo '<span style="color: red;">Niks ir pārāk īss!</span>';
		exit;
	}

	if ($db->get_row("SELECT id FROM users WHERE nick = '$nick'")) {
		echo '<span style="color: red;">Šāds niks ir aizņemts!</span>';
		exit;
	} else {
		echo '<span style="color: green;">OK</span>';
		exit;
	}
} else {
	echo '<span style="color: red;">Nav norādīts niks!</span>';
	exit;
}
