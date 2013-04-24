<?php

require_once(LIB_PATH . '/GameQ/GameQ.php');

$servers = array(
	array(
		'id' => 'cs_pub',
		'type' => 'cs16',
		'host' => 'cs.exs.lv:27015',
	),
	array(
		'id' => 'cs_dm',
		'type' => 'cs16',
		'host' => 'csdm.exs.lv:27015',
	),
	/*array(
		'id' => 'cs_go',
		'type' => 'csgo',
		'host' => '87.110.140.172:27016',
	)*/
);

$gq = new GameQ();
$gq->addServers($servers);
$gq->setOption('timeout', 5);
$gq->setFilter('normalise');

$results = $gq->requestData();




############## PUB
$mapimg = 'http://exs.lv/bildes/none.jpg';
if (file_exists("bildes/cs/" . strtolower($results['cs_pub']['map']) . ".jpg")) {
	$mapimg = 'http://exs.lv/bildes/cs/' . strtolower($results['cs_pub']['map']) . '.jpg';
}

if ($results['cs_pub']['map']) {

	$out = '<div class="game-monitor">

	<div class="server-img" style="background: url(\''.$mapimg.'\') no-repeat 0 0">
		<span class="server-addr">cs.exs.lv
			<span class="server-count">
				<span class="server-online">'.$results['cs_pub']['num_players'] . '</span>/<span class="server-total">' . $results['cs_pub']['max_players'].'</span>
			</span>
		</span>
		<a class="server-link link1" href="http://exs.lv/group/150">Grupa</a>
		<a class="server-link link2" href="http://cs.exs.lv/" rel="nofollow">Stati</a>
		<a class="server-link link3" href="http://cs.exs.lv/bans/" rel="nofollow">Bani</a>
	</div>Karte: '.$results['cs_pub']['map'].'<br />';

	if ($results['cs_pub']['num_players'] > 0) {
		$out .= '<table class="players">';
		foreach ($results['cs_pub']['players'] as $player) {
			$out .= '<tr' . alternator('', ' class="odd"') . '>';
			/*if (!isset($player['score']) or $player['score'] > 99999) {
				$score = 0;
			} else {
				$score = $player['score'];
			}*/
			$user = $db->get_row("SELECT id,nick,level FROM users WHERE nick = '" . sanitize($player['name']) . "'");
			if ($user) {
				$out .= '<td><a href="http://exs.lv/user/' . $user->id . '">' . usercolor($user->nick, $user->level, false, $user->id) . '</a></td>';
			} else {
				$player['name'] = wordwrap($player['name'], 24, "\n", 1);
				$out .= '<td>' . htmlspecialchars($player['name']) . '</td>';
			}
			//$out .= '<td class="score">' . $score . '</td>';
			$out .= '</tr>';
		}
		$out .= '</table>';
	}
} else {


	$out = '<div class="game-monitor">

	<div class="server-img" style="background: url(\''.$mapimg.'\') no-repeat 0 0">
		<span class="server-addr">cs.exs.lv
			<span class="server-count">
				<span class="server-offline">Off</span>
			</span>
		</span>
		<a class="server-link link1" href="http://exs.lv/group/150">Grupa</a>
		<a class="server-link link2" href="http://cs.exs.lv/" rel="nofollow">Stati</a>
		<a class="server-link link3" href="http://cs.exs.lv/bans/" rel="nofollow">Bani</a>
	</div>';


}

$out .= '</div>';

$fh = fopen('cache/cs_monitor.html', 'w');
fwrite($fh, $out);
fclose($fh);










######### CSDM
$mapimg = 'http://exs.lv/bildes/none.jpg';
if (file_exists("bildes/cs/" . strtolower($results['cs_dm']['map']) . ".jpg")) {
	$mapimg = 'http://exs.lv/bildes/cs/' . strtolower($results['cs_dm']['map']) . '.jpg';
}

if ($results['cs_dm']['map']) {

	$out = '<div class="game-monitor">

	<div class="server-img" style="background: url(\''.$mapimg.'\') no-repeat 0 0">
		<span class="server-addr">csdm.exs.lv
			<span class="server-count">
				<span class="server-online">'.$results['cs_dm']['num_players'] . '</span>/<span class="server-total">' . $results['cs_dm']['max_players'].'</span>
			</span>
		</span>
		<a class="server-link link1" href="http://exs.lv/group/150">Grupa</a>
		<a class="server-link link2" href="http://csdm.exs.lv/" rel="nofollow">Stati</a>
		<a class="server-link link3" href="http://cs.exs.lv/bans/" rel="nofollow">Bani</a>
	</div>Karte: '.$results['cs_dm']['map'].'<br />';

	if ($results['cs_dm']['num_players'] > 0) {
		$out .= '<table class="players">';
		foreach ($results['cs_dm']['players'] as $player) {
			$out .= '<tr' . alternator('', ' class="odd"') . '>';
			/*if (!isset($player['score']) or $player['score'] > 99999) {
				$score = 0;
			} else {
				$score = $player['score'];
			}*/
			$user = $db->get_row("SELECT id,nick,level FROM users WHERE nick = '" . sanitize($player['name']) . "'");
			if ($user) {
				$out .= '<td><a href="http://exs.lv/user/' . $user->id . '">' . usercolor($user->nick, $user->level, false, $user->id) . '</a></td>';
			} else {
				$player['name'] = wordwrap($player['name'], 24, "\n", 1);
				$out .= '<td>' . htmlspecialchars($player['name']) . '</td>';
			}
			//$out .= '<td class="score">' . $score . '</td>';
			$out .= '</tr>';
		}
		$out .= '</table>';
	}
} else {


	$out = '<div class="game-monitor">

	<div class="server-img" style="background: url(\''.$mapimg.'\') no-repeat 0 0">
		<span class="server-addr">csdm.exs.lv
			<span class="server-count">
				<span class="server-offline">Off</span>
			</span>
		</span>
		<a class="server-link link1" href="http://exs.lv/group/150">Grupa</a>
		<a class="server-link link2" href="http://csdm.exs.lv/" rel="nofollow">Stati</a>
		<a class="server-link link3" href="http://cs.exs.lv/bans/" rel="nofollow">Bani</a>
	</div>';


}

$out .= '</div>';

$fh = fopen('cache/csdm_monitor.html', 'w');
fwrite($fh, $out);
fclose($fh);















$url = 'http://exs.lv/modules/ut-stats/mc/server.php';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$resp = curl_exec($ch);
curl_close($ch);

if ($resp)
	$data = json_decode($resp);



if ($resp && $data->online) {



	$out = '<div class="game-monitor">

	<div class="server-img" style="background: url(\'http://exs.lv/bildes/MC.jpg\') no-repeat 0 0">
			<span class="server-addr">mc.exs.lv
				<span class="server-count">
					<span class="server-online">'.$data->curPlayers . '</span>/<span class="server-total">' . $data->maxPlayers.'</span>
				</span>
			</span>
		<a class="server-link link1" href="http://exs.lv/mc">Forums</a>
		<a class="server-link link2" href="http://exs.lv/group/306">Grupa</a>
	</div>';

	if (!empty($data->players)) {
		$out .= '<table class="players">';
		$altRow = 0;
		foreach ($data->players as $name => $player) {
			//echo $name;

			$out .= '<tr' . ($altRow % 2 ? '' : ' class="odd"') . '>';
			$out .= '<td>';

			$user = $db->get_row("SELECT id,nick,level FROM users WHERE nick = '" . sanitize($name) . "'");
			if ($user) {
				$out .= '<a href="http://exs.lv/user/' . $user->id . '">' . usercolor($user->nick, $user->level, false, $user->id) . '</a>';
			} else {
				$out .= htmlspecialchars(wordwrap($name, 24, "\n", 1));
			}

			$out .= '</td>';

			/*$out .= '<td class="score" title="veselība, iekavās līmenis">';
			$out .= htmlspecialchars($player->health) . '&nbsp;(' . htmlspecialchars($player->level) . ')';
			$out .= '</td>';*/
			$out .= '</tr>';
			$altRow++;
		}
		$out .= '</table>';
	}
} else {
	// handle error or offline
	$out = '<div class="game-monitor">

	<div class="server-img" style="background: url(\'http://exs.lv/bildes/MC.jpg\') no-repeat 0 0">
		<span class="server-addr">mc.exs.lv
			<span class="server-count">
				<span class="server-offline">Off</span>
			</span>
		</span>
		<a class="server-link link1" href="http://exs.lv/mc">Forums</a>
		<a class="server-link link2" href="http://exs.lv/group/306">Grupa</a>
	</div>';
}

$out .= '</div>';

$fh = fopen('cache/mc_monitor.html', 'w');
fwrite($fh, $out);
fclose($fh);










class san_andreas 
{ var $timeout=10; // packet TTL in seconds 

  // 
  // MAIN FUNCTION OF THE CLASS (CONSTRUCTOR) 
  // 

  function san_andreas($ip,$port) 
  { $player_count=0; 

    # (begin) socket code 
    $socket=socket_create(AF_INET,SOCK_DGRAM,SOL_UDP); 
    socket_set_option($socket,SOL_SOCKET,SO_RCVTIMEO,array('sec'=>$this->timeout,'usec'=>0)); 
    if (!$socket) return; $packet=null; 
    if (!socket_sendto($socket,chr(115),chr(49),0x100,$ip,$port+123)) return; 
    if (!socket_recvfrom($socket,$raw,4096,0,$client['ip'],$client['port'])) return; 
    socket_close($socket); 
    # (end) socket code 

    # (begin) packet parsing code 
    $server=substr($raw,0,strlen($raw)-strlen(strchr($raw,chr(63)))); 
    $players=explode(chr(63),strchr($raw,chr(63))); 
    foreach (range(1,22) as $code) 
    { $server=str_replace(chr($code),chr(0),$server); 
      $players=str_replace(chr($code),chr(0),$players); 
    } 
    for ($i=0; $i<(count($server)); $i++) $chunk=explode(chr(0),$server); 
    # (end) packet parsing code 

    # (begin) fill server array with results 
    $this->results['server']['hostname']=explode(chr(7),str_replace($port.$chunk[2][5],"",$chunk[2])); 
    $this->results['server']['hostname']=$this->results['server']['hostname'][0]; 
    $this->results['server']['gametype']=$chunk[3]; 
    $this->results['server']['gamemod']=$chunk[4]; 
    $this->results['server']['version']=$chunk[5]; 
    $this->results['server']['max_players']=$chunk[8]; 
    $this->results['server']['players']=0; 
    # (end) fill server array with results 

    # (begin) fill players array with results 
    for ($i=1; $i<count($players); $i++) 
    { $player_info=explode(chr(0),$players[$i]); 
      $this->results['players'][$player_count]['player']=$player_info[1]; 
      $this->results['players'][$player_count]['status']='playing'; 
      $this->results['players'][$player_count]['ping']=$player_info[5]; 
      $player_count++; 
    } 
    if (isset($this->results['players']) && count($this->results['players'])>0) $this->results['server']['players']=count($this->results['players']); 
    # (end) fill players array with results 
    unset($this->timeout); 
  } 
} 






$mta = new san_andreas('94.100.6.70','22003');


if(!empty($mta->results['server']['hostname'])) {

	$out = '<div class="game-monitor">
	<div class="server-img" style="background: url(\'http://exs.lv/bildes/mta.jpg\') no-repeat 0 0">

		<span class="server-addr">mta.exs.lv
			<span class="server-count">
				<span class="server-online">'.$mta->results['server']['players'] . '</span>/<span class="server-total">' . $mta->results['server']['max_players'].'</span>
			</span>
		</span>

		<a class="server-link link1" href="http://rp.exs.lv/">Forums</a>
		<a class="server-link link2" href="http://mta.exs.lv/ucp/login/">UCP</a>
	</div> Ports: 22003<br />';

	



	if ($mta->results['server']['players'] > 0) {
		$out .= '<table class="players">';
		foreach ($mta->results['players'] as $player) {
			$out .= '<tr' . alternator('', ' class="odd"') . '>';

			if($player['player'] == '') {
				$player['player'] = 'Nezināms';
			}

			$user = $db->get_row("SELECT id,nick,level FROM users WHERE nick = '" . sanitize($player['player']) . "'");
			if ($user) {
				$out .= '<td><a href="http://rp.exs.lv/user/' . $user->id . '">' . usercolor($user->nick, $user->level, false, $user->id) . '</a></td>';
			} else {
				$player['player'] = wordwrap($player['player'], 24, "\n", 1);
				$out .= '<td>' . htmlspecialchars($player['player']) . '</td>';
			}
			$out .= '</tr>';
		}
		$out .= '</table>';
	} 

} else {
	// handle error or offline

	$out = '<div class="game-monitor">
	<div class="server-img" style="background: url(\'http://exs.lv/bildes/mta.jpg\') no-repeat 0 0">

		<span class="server-addr">mta.exs.lv
			<span class="server-count">
				<span class="server-offline">Off</span>
			</span>
		</span>

		<a class="server-link link1" href="http://rp.exs.lv/">Forums</a>
		<a class="server-link link2" href="http://mta.exs.lv/ucp/login/">UCP</a>
	</div>';


}

$out .= '</div>';

$fh = fopen('cache/mta_monitor.html', 'w');
fwrite($fh, $out);
fclose($fh);











foreach ($db->get_results("SELECT uid FROM serverlist WHERE fails = '0' ORDER BY updated ASC LIMIT 3") as $server) {
	file_get_contents('http://exs.lv/server.php?s=' . $server->uid);
}

foreach ($db->get_results("SELECT uid FROM serverlist WHERE fails < '3' ORDER BY updated ASC LIMIT 1") as $server) {
	file_get_contents('http://exs.lv/server.php?s=' . $server->uid);
	sleep(1);
}

foreach ($db->get_results("SELECT uid FROM serverlist ORDER BY updated ASC LIMIT 1") as $server) {
	file_get_contents('http://exs.lv/server.php?s=' . $server->uid);
}

die(0);
