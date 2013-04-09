<?php
error_reporting(0);


require_once 'modules/monitor/GameQ.php';


// Define your servers,
// see list.php for all supported games and identifiers.
$servers = array(
    's0' => array('cs', '95.68.57.214')
);


// Call the class, and add your servers.
$gq = new GameQ();
$gq->addServers($servers);

    
// You can optionally specify some settings
$gq->setOption('timeout', 1200);


// You can optionally specify some output filters,
// these will be applied to the results obtained.
$gq->setFilter('normalise');
$gq->setFilter('sortplayers', 'gq_ping');

// Send requests, and parse the data
$results = $gq->requestData();

echo '<pre>';
print_r($results);
echo '</pre>';

$mapfile = "bildes/ut/" . strtolower($results['s0']['mapname']) . ".jpg";
if (file_exists($mapfile)) {
	$mapimg = '<img width="128" height="79" src="/bildes/ut/' . strtolower($results['s0']['mapname']) . '.jpg" alt="' . $results['s0']['mapname'] . '" />';
} else {
	$mapimg = '<img width="128" height="79" src="/bildes/ut/none.png" alt="none" />';
}

$out = $mapimg . '<br />Serveris: <a href="http://unreal.exs.lv/">unreal.exs.lv:7777</a><br />Karte: ' . $results['s0']['mapname'] . '<br />Spēle: ' . $results['s0']['gametype'] . '<br />Spēlētāji: ' . $results['s0']['playercount'] . '/' . $results['s0']['playercount'];

if($results['s0']['mapname']) {
	//$db->query("UPDATE variables SET value='".sanitize($out)."' WHERE title = 'ut_status_04'");
	echo 'ok';
}

exit;

?>