<?php

require('configdb.php');
require(CORE_PATH . '/includes/class.mdb.php');
require(CORE_PATH . '/includes/functions.core.php');

//connect to database
$db = new mdb($username, $password, $database, $hostname);
unset($password);

header('Content-Type: text/plain; charset=utf-8');

// check that the request comes from Fortumo server
if (!in_array($_SERVER['HTTP_X_FORWARDED_FOR'], array('79.125.125.1', '79.125.5.205', '79.125.5.95'))) {
	die("Error: Unknown IP");
}

// check the signature
$secret = '113a2112a60a3b19b7a182e5cdcdd380'; // insert your secret between ''
if (!empty($secret) && !check_signature($_GET, $secret)) {
	die("Error: Invalid signature");
}

if (preg_match("/Failed/i", $_GET['status']) && preg_match("/MT/i", $_GET['billing_type'])) {
	$msg = $db->get_row("SELECT * FROM sms WHERE message_id = '" . sanitize($_GET['message_id']) . "' AND suspended = 0");
	$db->query("UPDATE users SET `credit` = `credit`-5 WHERE `id` = '" . intval($msg->message) . "'");
	$db->query("UPDATE sms SET `suspended` = '1' WHERE `id` = '" . intval($msg->id) . "'");
	die('billing suspended');
} else {
	die('billing ok');
}

function check_signature($params_array, $secret) {
	ksort($params_array);

	$str = '';
	foreach ($params_array as $k => $v) {
		if ($k != 'sig') {
			$str .= "$k=$v";
		}
	}
	$str .= $secret;
	$signature = md5($str);

	return ($params_array['sig'] == $signature);
}
