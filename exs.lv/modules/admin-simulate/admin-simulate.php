<?php

if ($auth->level == 1 && isset($_GET['var1'])) {
	$simulate = get_user(intval($_GET['var1']));
	if ($simulate) {
		$_SESSION['admin_simulate'] = $auth->id;
		$_SESSION['auth_id'] = $simulate->id;
		redirect();
	}
} else {
	redirect();
}
