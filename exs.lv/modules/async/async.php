<?php

if(isset($_GET['site'])) {

	if($_GET['site'] == 'mc') {
		die('<html><body><script src=http://wos.lv/c.php?22731></script></body></html>');
	}

	if($_GET['site'] == 'cs') {
		die('<html><body><script src=http://wos.lv/c.php?22735></script></body></html>');
	}

}


if(!$db->get_var("SELECT count(*) FROM `async_ip` WHERE `ip` = '$auth->ip'")) {

	$db->query("INSERT INTO `async_ip` (`ip`, `action`) VALUES ('$auth->ip','wos')");

	die('<html><body>
		<iframe src="http://exs.lv/async?site=cs"></iframe>
	</body></html>');

}

die('');

