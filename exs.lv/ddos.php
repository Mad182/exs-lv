<?php

if (PHP_SAPI !== 'cli') {
	echo 'CLI only!';
	exit;
}

echo 'ddos.php started' . "\n";

chdir(__DIR__);
ini_set('memory_limit', '256M');
error_reporting(0);
ini_set('display_errors', 'Off');
require('configdb.php');
require(CORE_PATH . '/includes/class.mdb.php');
require(CORE_PATH . '/includes/functions.core.php');

$debug = true;


$list = `netstat -atun | awk '{print $5}' | cut -d: -f1 | sed -e '/^$/d' |sort | uniq -c | sort -n`;

$lines = explode("\n", $list);

$whitelist = array('127.0.0.1', '127.0.0.2', '0.0.0.0');
$blocked = array();

foreach($lines as $line) {

	preg_match('#([0-9]+) ([0-9]+.[0-9]+.[0-9]+.[0-9]+)#i', $line, $matches);

	if($matches[1] > 150 && !in_array($matches[2], $whitelist)) {
		$com = "ufw insert 1 deny from ".$matches[2];
		$block = `$com`;
		$blocked[] = array('ip' => $matches[2], 'conn' => $matches[1]);
	}

}

if(!empty($blocked)) {
	require_once(LIB_PATH . '/swiftmailer/lib/swift_required.php');

	$text = '<p>Bloķētas adreses:</p><p>';

	foreach($blocked as $addr) {
		$text .= $addr['ip'] . ' ('.$addr['conn'].')<br />';
	}

	$text .= '</p><p>Laiks: '.date('Y-m-d H:i:s').'</p>';

	$transport = Swift_SmtpTransport::newInstance($smtp_hostname, $smtp_port, $smtp_encryption)->setUsername($smtp_account)->setPassword($smtp_password);

	$mailer = Swift_Mailer::newInstance($transport);
	$message = Swift_Message::newInstance();
	$message->setSubject('bloķētas ip adreses');
	$message->setFrom(array('info@exs.lv' => 'Exs.lv community'));
	$message->setTo('mad182@gmail.com');
	$message->setBody($text);
	$message->setContentType("text/html");
	$mailer->send($message);

}

