<?php

if (PHP_SAPI !== 'cli') {
	echo 'CLI only!';
	exit;
}

echo 'shell.php started' . "\n";

chdir(__DIR__);
ini_set('memory_limit', '256M');
error_reporting(0);
ini_set('display_errors', 'Off');
require('configdb.php');
require(CORE_PATH . '/includes/class.mdb.php');
require(CORE_PATH . '/includes/functions.core.php');

$debug = true;

$lang = 1;

//mysql konekcija
$db = new mdb($username, $password, $database, $hostname);

function reverse_htmlentities($mixed) {
	$htmltable = get_html_translation_table(HTML_ENTITIES);
	foreach ($htmltable as $key => $value) {
		$mixed = ereg_replace(addslashes($value), $key, $mixed);
	}
	return $mixed;
}

//memcached konekcija
$m = new Memcache;
$m->connect($mc_host, $mc_port);


/*$users = $db->get_results("SELECT `id` FROM `users` ORDER BY `lastseen` DESC");
echo "start update_karma()\n";
$i = 0;
$tot = 0;
foreach ($users as $val) {
	update_karma($val->id, true);
	$i++;
	$tot++;
	if ($i > 99) {
		echo $tot . " updated...\n";
		$i = 0;
		//sleep(1);
	}
}
echo "end update_karma()\n";
*/



if ( $auth->id == 115 && isset($_GET['update-groups'])) {

    $rsclans = $db->get_results("SELECT `id`,`strid` FROM `clans` WHERE `category_id` = 4");
    if ($rsclans) {
        foreach ($rsclans as $clan) {
            $upd = $db->query("UPDATE `clans` SET `lang` = 9 WHERE `id` = '".(int)$clan->id."' ");
            $upd = $db->query("UPDATE `cat` SET `lang` = 9 WHERE `textid` = '".sanitize($clan->strid)."' ");
            $upd = $db->query("UPDATE `miniblog` SET `lang` = 9 WHERE `groupid` = '".(int)$clan->id."' ");
        }
        echo $clan->strid . " done...\n";
    }
}


echo "\ndone!\n";
