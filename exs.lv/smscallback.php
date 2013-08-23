<?php

require('configdb.php');
require(CORE_PATH . '/includes/class.mdb.php');
require(CORE_PATH . '/includes/functions.core.php');

//connect to database
$db = new mdb($username, $password, $database, $hostname);
unset($password);

//memcached konekcija
$m = new Memcache;
$m->connect($mc_host, $mc_port);

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

$sender = $_GET['sender'];
$message = $_GET['message'];
$message_id = $_GET['message_id'];

$intmsg = (int) $_GET['message'];

$user = get_user($intmsg);
if (!empty($user)) {
	$db->query("UPDATE `users` SET `credit` = `credit`+5 WHERE `id` = '$user->id'");
	$db->query("INSERT INTO `sms` (time,message,sender,message_id,data) VALUES ('" . time() . "','" . sanitize($message) . "','" . sanitize($sender) . "','" . sanitize($message_id) . "','" . serialize($_GET) . "')");
	$reply = 'Pasūtīti 5 expunkti lietotājam ' . $user->nick . '. Paldies!';
} else {
	$reply = 'SMS saņemta!';
}

// print out the reply
echo $reply;

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
