<?php

/* wos.lv topa apčakarēšana */

if(isset($_GET['site'])) {

	if($_GET['site'] == 'mta') {
		die('<html><body><script src=http://wos.lv/c.php?9530></script></body></html>');
	}

	if($_GET['site'] == 'exs') {
		die('<html><body><script src=http://wos.lv/c.php?26271></script></body></html>');
	}
}

if(!$db->get_var("SELECT count(*) FROM `async_ip` WHERE `ip` = '$auth->ip'")) {

	$db->query("INSERT INTO `async_ip` (`ip`, `action`) VALUES ('$auth->ip','wos')");

	die('<html><body>
		<iframe src="http://rp.exs.lv/async?site=mta"></iframe>
		<iframe src="http://exs.lv/async?site=exs"></iframe>
	</body></html>');

}

die('');

