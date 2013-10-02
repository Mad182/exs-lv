<?php

/* wos.lv topa apčakarēšana */

if(isset($_GET['site'])) {

	if($_GET['site'] == 'mta') {
		die('
			<html>
				<body>
					<script src=http://wos.lv/c.php?27967></script>
				</body>
			</html>');
	}

}

if(!$db->get_var("SELECT count(*) FROM `async_ip` WHERE `ip` = '$auth->ip'")) {

	$db->query("INSERT INTO `async_ip` (`ip`, `action`) VALUES ('$auth->ip','wos')");

	die('<html><body>
		<iframe src="http://rp.exs.lv/async?site=mta"></iframe>
	</body></html>');

}

die('');
