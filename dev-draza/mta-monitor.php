<?php

function alternator() {
	static $i;
	if (func_num_args() === 0) {
		$i = 0;
		return '';
	}
	$args = func_get_args();
	return $args[($i++ % count($args))];
}

require_once('./libs/GameQ/GameQ.php');

$servers = array(
	array(
		'id' => 'mta',
		'type' => 'mta',
		'host' => 'mta.exs.lv:22126',
	)
);

$gq = new GameQ();
$gq->addServers($servers);
$gq->setOption('timeout', 5);
$gq->setFilter('normalise');

$results = $gq->requestData();

$out = '<div id="mta-monitor">';
$out .= '<p><strong>IP:</strong> mta.exs.lv:22003</p>';
$out .= '<p><strong>Online:</strong> '.$results['mta']['num_players'] . '/' . $results['mta']['max_players'] . '</p>';
$out .= '<div id="chart_div" style="width: 208px; height: 140px;"></div>';
$out .= '<p class="playerlist"><strong>Spēlētāji:</strong>';

foreach($results['mta']['players'] as $player) {
	$out .= '<span class="'.alternator('odd', 'even').'">' . $player['name'] . '</span>';
} 

$out .= '<p>';
$out .= '</div>';

echo $out;

