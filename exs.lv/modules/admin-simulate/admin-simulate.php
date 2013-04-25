<?php

if ($auth->id == 1 && isset($_GET['var1']) && $debug) {
	$simulate = get_user(intval($_GET['var1']));
	if ($simulate) {
		$_SESSION['admin_simulate'] = $auth->id;
		$_SESSION['auth_id'] = $simulate->id;
		redirect();
	}
} else {
	redirect();
}
