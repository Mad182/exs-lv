<?php

/**
 * Neliels antiddos skripts,
 * bloķē ip adreses no kurām nāk pārāk daudz konekciju
 */
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
require(CORE_PATH . '/includes/class.templatepower.php');
require(CORE_PATH . '/includes/functions.core.php');

$debug = true;

$list = `netstat -atun | awk '{print $5}' | cut -d: -f1 | sed -e '/^$/d' |sort | uniq -c | sort -n`;

$lines = explode("\n", $list);

$whitelist = array('127.0.0.1', '127.0.0.2', '0.0.0.0', '92.240.69.183');
$blocked = array();

foreach ($lines as $line) {

	preg_match('#([0-9]+) ([0-9]+.[0-9]+.[0-9]+.[0-9]+)#i', $line, $matches);

	if (($matches[1] > 500 && !in_array($matches[2], $whitelist))) {
		$com = "ufw insert 1 deny from " . $matches[2];
		$block = `$com`;
		echo $matches[2] . ': ' . $block . "\n";
		$blocked[] = array('ip' => $matches[2], 'conn' => $matches[1]);
	}
}

if (!empty($blocked)) {

	$text = '<h3>Bloķētas adreses:</h3><p>';
	foreach ($blocked as $addr) {
		$text .= $addr['ip'] . ' (' . $addr['conn'] . ')<br />';
	}
	$text .= '</p><p>Laiks: ' . date('Y-m-d H:i:s') . '</p>';

	send_email('mad182@gmail.com', 'Bloķētas ip adreses', $text);
}
