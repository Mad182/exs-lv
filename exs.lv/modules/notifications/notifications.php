<?php

$texts = array(
	0 => 'atbilde komentāram',
	1 => 'komentārs galerijā',
	2 => 'komentārs rakstam',
	3 => 'atbilde miniblogā',
	4 => 'jauns biedrs tavā grupā',
	5 => 'tevi aicina draudzēties',
	6 => 'tev ir jauns draugs',
	7 => 'tu saņēmi medaļu',
	8 => 'tev atbildēja grupā',
	9 => 'saņemta vēstule',
	10 => 'brīdinājums!',
	11 => 'noņemts brīdinājums',
	12 => 'jaunumi no exs.lv',
	13 => 'tevi pieminēja grupā',
	14 => 'tevi pieminēja mb',
	15 => 'tevi pieminēja',
	16 => 'tevi pieminēja galerijā'
);

if (isset($_GET['var1']) && $_GET['var1'] == 'rss') {

	if (isset($_GET['var2'])) {
		$userid = (int) $_GET['var2'];
		$user = get_user($userid);

		if ($user) {

			$user_id = intval($user->id);
			$out = '';
			if (!empty($user_id)) {



				if ($notify = $db->get_results("SELECT * FROM `notify` WHERE `user_id` = '$user_id' ORDER BY `bump` DESC LIMIT 0,20")) {

					header('Content-type: text/xml; charset=utf-8');
					echo '<?xml version="1.0" encoding="UTF-8"?', ">\n";
					echo '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">', "\n";
					echo '	<channel>', "\n";

					echo '		<title>' . h($user->nick . ' notifikācijas') . '</title>', "\n";
					echo '		<link>https://exs.lv/user/' . $user->id . '</link>', "\n";
					echo '		<description>Jaunākais ' . h($user->nick) . ' profilā</description>', "\n";
					echo '		<language>lv</language>', "\n";

					foreach ($notify as $notify) {

						$dom = '';
						$domain = 'https://' . $config_domains[$notify->lang]['domain'];
						if ($notify->lang != $lang) {
							$dom = ' (' . $config_domains[$notify->lang]['domain'] . ')';
						}

						if ($notify->type == 5 || $notify->type == 6) {
							$notify->url = '/friends/' . $notify->user_id;
						}
						if ($notify->type == 7) {
							$notify->url = '/awards/' . $notify->user_id;
						}
						if ($notify->type == 9) {
							$notify->url = '/pm';
						}
						if ($notify->type == 10 || $notify->type == 11) {
							$notify->url = '/warns/' . $notify->user_id;
						}
						if (empty($notify->url)) {
							$notify->url = 'javascript:void(0);';
							$domain = '';
						}
						$class = $notify->type;
						if ($notify->type == 8) {
							$class = 3;
						}

						echo '		<item>', "\n";
						echo '			<title>', h(mb_ucfirst($texts[$notify->type])), '</title>', "\n";
						echo '			<link>' . h($domain . $notify->url) . '</link>' . "\n";
						echo '			<guid>' . h($domain . $notify->url . '?' . strtotime($notify->bump)) . '</guid>' . "\n";
						echo '			<description>', h(mb_ucfirst($texts[$notify->type]) . $dom), '</description>', "\n";
						echo '			<pubDate>', gmdate('r', strtotime($notify->bump)), '</pubDate>', "\n";
						echo '			<dc:creator>exs.lv</dc:creator>' . "\n";
						echo '		</item>', "\n";
					}
					echo '	</channel>', "\n";
					echo '</rss>';
					exit;
				}
			}
		} else {
			echo 'err: not found';
		}
	}

	echo 'err: no user';
	exit;
} elseif (isset($_GET['var1']) && $_GET['var1'] == 'json') {

	if (isset($_GET['var2'])) {
		$userid = (int) $_GET['var2'];
		$user = get_user($userid);

		if ($user) {

			$user_id = intval($user->id);
			$out = '';
			if (!empty($user_id)) {

				$data = array();

				if ($notify = $db->get_results("SELECT * FROM `notify` WHERE `user_id` = '$user_id' ORDER BY `bump` DESC LIMIT 0,20")) {

					foreach ($notify as $notify) {

						$domain = 'https://' . $config_domains[$notify->lang]['domain'];

						if ($notify->type == 5 || $notify->type == 6) {
							$notify->url = '/friends/' . $notify->user_id;
						}
						if ($notify->type == 7) {
							$notify->url = '/awards/' . $notify->user_id;
						}
						if ($notify->type == 9) {
							$notify->url = '/pm';
						}
						if ($notify->type == 10 || $notify->type == 11) {
							$notify->url = '/warns/' . $notify->user_id;
						}
						if (empty($notify->url)) {
							$notify->url = 'javascript:void(0);';
							$domain = '';
						}
						$class = $notify->type;
						if ($notify->type == 8) {
							$class = 3;
						}

						$data[] = array(
							'url' => $domain . $notify->url,
							'title' => h(mb_ucfirst($texts[$notify->type])),
							'date' => $notify->bump,
							'info' => $notify->info,
							'type' => $notify->type
						);
					}

					echo json_encode($data);
					exit;
				}
			}
		} else {
			echo 'err: not found';
		}
	}

	echo 'err: no user';
	exit;
} elseif (isset($_GET['var1']) && $_GET['var1'] == 'html') {

	if (isset($_GET['var2'])) {
		$userid = (int) $_GET['var2'];
		$user = get_user($userid);

		if ($user) {

			echo get_notify($userid, '/notifications/html/' . $userid . '?events-page=');
			exit;
		} else {
			echo 'err: not found';
		}
	}

	echo 'err: no user';
	exit;
} else {

	echo 'err: no format';
	exit;
}

