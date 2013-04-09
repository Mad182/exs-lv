<?php
require('JSONAPI.php'); // get this file at: https://github.com/alecgorge/jsonapi/raw/master/sdk/php/JSONAPI.php

$api = new JSONAPI("87.110.140.172", 20059, "exslvsidebar", "qWoPSkakr1ParM0du!!1", "salsunpipari"); // host, port, user, password, salt
$server = @$api->call("getServer");
$data = array();
if ($server['result'] == 'success') {
	$resp = $server['success'];
	$data['online'] = true;
	$data['serverName'] = $resp['serverName'];
	$data['maxPlayers'] = $resp['maxPlayers'];
	$data['curPlayers'] = count($resp['players']);
	foreach ($resp['players'] as $player) {
		$data['players'][$player['name']]['op'] = ($player['op'] ? 1 : 0);
		$data['players'][$player['name']]['level'] = $player['level'];
		$data['players'][$player['name']]['health'] = $player['health']/2;
	}
} else {
	$data['online'] = false;
}
echo json_encode($data);

