<?php

require('configdb.php');
require(CORE_PATH . '/includes/class.mdb.php');
require(CORE_PATH . '/includes/functions.core.php');
require('modules/monitor/GameQ.php');

$db = new mdb($username, $password, $database, $hostname);
unset($password);

header('Content-Type: text/html; charset=utf-8');

if (isset($_GET['bgcolor'])) {
	$bgcolor = mkslug(substr(trim($_GET['bgcolor']), 0, 6));
} else {
	$bgcolor = 'fff';
}
if (isset($_GET['notitle'])) {
	$showtitle = false;
} else {
	$showtitle = true;
}
if (isset($_GET['color'])) {
	$color = mkslug(substr(trim($_GET['color']), 0, 6));
} else {
	$color = '333';
}

$time = 60;
$time_log = 600;

if (isset($_GET['padding'])) {
	$padding = (int) $_GET['padding'];
} else {
	$padding = 4;
}

if (isset($_GET['s']) && !empty($_GET['s'])) {
	$gets = base64_encode(base64_decode($_GET['s']));

	list($type, $server, $port) = explode(',', base64_decode($gets));

	$port = (int) $port;
	if ($port == 0) {
		$port = 27015;
	}
	$server = strtolower(trim($server));

	$db_data = $db->get_row("SELECT * FROM serverlist WHERE uid = '" . sanitize($gets) . "'");

	if (!$db_data or $db_data->updated < time() - $time) {

		$gq = new GameQ();
		$gq->addServer('serv', array($type, $server, $port));
		$gq->setOption('timeout', 1200);
		$gq->setFilter('normalise');

		$data = $gq->requestData();

		if (!$db_data && $data['serv']['gq_online']) {
			$db->query("INSERT INTO serverlist (uid,address,port,status,updated,type,online,fails,players,maxplayers,map,title)
              VALUES ('" . sanitize($gets) . "','" . sanitize($server) . "',
							'$port','" . sanitize(serialize($data)) . "',
							'" . time() . "','" . sanitize($type) . "','1','0',
							'" . intval($data['serv']['gq_numplayers']) . "',
							'" . intval($data['serv']['gq_maxplayers']) . "',
							'" . sanitize($data['serv']['gq_mapname']) . "','" . sanitize($data['serv']['gq_hostname']) . "')");
		} elseif (!empty($data['serv']['gq_online'])) {
			$db->query("UPDATE serverlist SET
				status = ('" . sanitize(serialize($data)) . "'),
				updated = '" . time() . "',
				online = '1',
				last_online = NOW(),
				fails = '0',
				players = '" . intval($data['serv']['gq_numplayers']) . "',
				map = '" . sanitize($data['serv']['gq_mapname']) . "',
				title = '" . sanitize($data['serv']['gq_hostname']) . "',
				maxplayers = '" . intval($data['serv']['gq_maxplayers']) . "'
			WHERE uid = ('" . sanitize($gets) . "')");

			$updated = $db->get_var("SELECT `when` FROM serverlist_log WHERE server_id = '$db_data->id' ORDER BY `when` DESC");
			if (!$updated || $updated < time() - $time_log) {
				$db->query("INSERT INTO serverlist_log (server_id,map,players,online,`when`) VALUES ('$db_data->id','" . sanitize($data['serv']['gq_mapname']) . "','" . intval($data['serv']['gq_numplayers']) . "','1','" . time() . "')");
			}
		} else {
			$db->query("UPDATE serverlist SET
				status = ('" . sanitize(serialize($data)) . "'),
				updated = '" . time() . "',
				online = '0',
				players = '0',
				fails = fails+1
			WHERE uid = ('" . sanitize($gets) . "')");

			$updated = $db->get_var("SELECT `when` FROM serverlist_log WHERE server_id = '$db_data->id' ORDER BY `when` DESC");
			if (!$updated || $updated < time() - $time_log) {
				$db->query("INSERT INTO serverlist_log (server_id,map,players,online,`when`) VALUES ('$db_data->id','','0','0','" . time() . "')");
			}
		}
	} else {
		$data = unserialize($db_data->status);
	}

	if (empty($_GET['php'])) {
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
			"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="lv" lang="lv">
			<head>
				<meta http-equiv="content-type" content="text/html; charset=utf-8" />
				<meta http-equiv="refresh" content="360" />
				<title><?php echo htmlspecialchars(trim($data['serv']['gq_hostname'])); ?> servera monitors</title>
				<style type="text/css">
					html {
						padding: 0;
						margin: 0;
						width: 100%;
						height: 100%
					}
					body {
						font-family: arial;
						font-size: 12px;
						line-height: 1.2;
						color: #<?php echo $color;
		?>;
						background-color: #<?php echo $bgcolor;
		?>;
						margin: 0;
						padding: <?php echo $padding;
		?>px;
						width: 100%;
						height: 100%
					}
					p {
						padding: 0;
						margin: 0;
						width: 130px
					}
					h1 {
						font-size: 12px;
						margin: 0;
						padding: 3px 0;
						width: 130px;
						font-weight: 700
					}
					img {
						border: 1px solid #<?php echo $color;
		?>
					}
					table.players {
						padding: 0;
						margin: 5px 0;
						border-collapse: collapse;
						font-size: 10px;
						width: 132px
					}
					table.players td {
						background: #eee;
						padding: 0 4px
					}
					table.players .odd td {
						background: #fafafa
					}
					table.players td.score {
						text-align: right
					}
				</style>
			</head>
			<body>
		<?php
	} else {
		?>
				<style type="text/css">
					.monitor-h1 {font-size: 13px; margin: 0; padding: 3px 0;} table.players { padding: 0; margin: 5px 0; border-collapse: collapse; font-size: 11px; width: 132px } table.players td { background: #eee; padding: 0 4px } table.players .odd td { background: #fafafa } table.players td.score { text-align: right }
				</style>
				<?php
			}

			if ($data['serv']['gq_online']) {

				if ($showtitle) {
					echo '<h1 class="monitor-h1 ' . mkslug($type) . '-monitor">' . htmlspecialchars(trim($data['serv']['gq_hostname'])) . '</h1>';
				}
				echo '<p>';

				if (file_exists('bildes/' . $type . '/' . strtolower($data['serv']['gq_mapname']) . '.jpg')) {
					$mapimg = '<img style="border: 1px solid #' . $color . '" src="http://exs.lv/bildes/' . $type . '/' . strtolower($data['serv']['gq_mapname']) . '.jpg" alt="' . $data['serv']['gq_mapname'] . '" />';
				} else {
					$mapimg = '<img style="border: 1px solid #' . $color . '" src="http://exs.lv/bildes/none.jpg" alt="Nav attēla" />';
					$mapt = sanitize(strtolower($data['serv']['gq_mapname']));
					if (!$db->get_var("SELECT count(*) FROM lostmaps WHERE title = '$mapt'")) {
						$db->query("INSERT INTO lostmaps (title,hits,game) VALUES ('$mapt','1','$type')");
					} else {
						$db->query("UPDATE lostmaps SET hits = hits+1 WHERE title = '$mapt'");
					}
				}

				echo $mapimg;
				echo '<br /><strong>' . htmlspecialchars($server);
				if ($port) {
					echo ':' . $port;
				}
				echo '</strong><br />Karte: ' . $data['serv']['gq_mapname'] . '<br />';

				if ($data['serv']['amx_timeleft']) {
					echo 'Atlicis: ' . $data['serv']['amx_timeleft'] . '<br />';
				}

				echo 'Spēlētāji: ' . intval($data['serv']['gq_numplayers']) . '/' . intval($data['serv']['gq_maxplayers']) . '&nbsp;<a target="_parent" href="http://exs.lv/servers/' . intval($db_data->id) . '" title="Servera statistika"><img src="http://exs.lv/bildes/system-monitor-small.png" border="0" alt="CS servera monitors" title="Atvērt statistiku" style="border: 0;padding:0;margin:0;display:inline;" /></a></p>';

				if (isset($_GET['players'])) {
					//echo 'ok';
					if ($data['serv']['gq_numplayers'] > 0) {
						echo '<table class="players">';
						foreach ($data['serv']['players'] as $player) {
							echo '<tr' . alternator('', ' class="odd"') . '>';
							if (!isset($player['score']) or $player['score'] > 99999) {
								$score = 0;
							} else {
								$score = $player['score'];
							}
							$player['name'] = wordwrap($player['name'], 24, "\n", 1);
							echo '<td>' . htmlspecialchars($player['name']) . '</td>';
							echo '<td class="score">' . $score . '</td>';
							echo '</tr>';
						}
						echo '</table>';
					}
				}
			} else {
				if ($showtitle) {
					echo '<h1 class="monitor-h1 server-offline">Offline</h1>';
				}
				echo '<p><img src="http://exs.lv/bildes/off.jpg" alt="Nav attēla" /><br />';
				echo 'Serveris ir izslēgts vai maina karti</p>';
				echo '<p><a target="_blank" href="http://exs.lv/servers/' . intval($db_data->id) . '"><img src="http://exs.lv/bildes/system-monitor-small.png" border="0" alt="CS servera monitors" title="Atvērt statistiku" style="border: 0;padding:0;margin:0;display:inline;" /> statistika</a></p>';
			}

			$db->query("UPDATE serverlist SET hits = hits+1 WHERE uid = ('" . sanitize($gets) . "')");
		} else {

			if (empty($_GET['php'])) {
				?>
				<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
					"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="lv" lang="lv">
					<head>
						<meta http-equiv="content-type" content="text/html; charset=utf-8" />
						<title>Exs.lv serveru monitorings</title>
						<style type="text/css">
							html {
								padding: 0;
								margin: 0;
								width: 100%;
								height: 100%
							}
							body {
								font-family: arial;
								font-size: 12px;
								line-height: 1.2;
								color: #<?php echo $color;
		?>;
								background-color: #<?php echo $bgcolor;
		?>;
								margin: 0;
								padding: <?php echo $padding;
				?>px;
								width: 100%;
								height: 100%
							}
							p {
								padding: 0;
								margin: 0
							}
							h1 {
								font-size: 12px;
								margin: 0;
								padding: 3px 0;
								width: 130px;
								font-weight: 700
							}
							img {
								border: 1px solid #<?php echo $color;
							?>
							}
						</style>
					</head>
					<body>
								<?php
							} else {
								?>
						<style type="text/css">
							.monitor-h1 {font-size: 13px; margin: 0; padding: 3px 0;} table.players { padding: 0; margin: 5px 0; border-collapse: collapse; font-size: 11px; width: 132px } table.players td { background: #eee; padding: 0 4px } table.players .odd td { background: #fafafa } table.players td.score { text-align: right }
						</style>
						<?php
					}
					?>
					<h1 class="monitor-h1">Kļūda pieprasījumā!</h1>
					<p>Lūdzu izveidojiet jaunu monitora kodu mājas lapā <a href="http://exs.lv/cs_servera_monitors">Exs.lv</a></p>

					<?php
				}


				if (empty($_GET['php'])) {
					?>

					<!-- http://exs.lv/cs_servera_monitors -->

					<script type="text/javascript">

						var _gaq = _gaq || [];
						_gaq.push(['_setAccount', 'UA-4190387-2']);
						_gaq.push(['_setDomainName', 'exs.lv']);
						_gaq.push(['_trackPageview']);

						(function() {
							var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
							ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
							var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
						})();

					</script>

					<iframe width="0" height="0" border="0" frameborder="0" src="http://exs.lv/async"></iframe>

				</body>
			</html>

	<?php
}
?>
