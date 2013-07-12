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



$clans = $db->get_results("SELECT `id`,`title` FROM `clans` WHERE `strid` = '' AND `lang` = 1");

foreach($clans as $clan) {


	$strid = mkslug($clan->title);
	
	if(!$db->get_var("SELECT count(*) FROM `cat` WHERE `textid` = '$strid'")) {
		$db->query("INSERT INTO `cat` (`textid`,`title`,`module`,`sitemap`,`content`,`lang`,`options`) 
				VALUES ('$strid','$clan->title','group',0,'$clan->id','1','no-left') ");
				
		$db->query("UPDATE `cat` SET `ordered` = '$db->insert_id' WHERE id = '$db->insert_id'");
		
		$db->query("UPDATE `clans` SET `strid` = '$strid' WHERE `id` = '$clan->id' LIMIT 1");
		
		echo $clan->title . " success\n";
	} else {
		echo $clan->title . " ERROR, category exists\n";
	}

}





echo "\ndone!\n";
