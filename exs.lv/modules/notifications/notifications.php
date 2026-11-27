<?php

$texts = [
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
	16 => 'tevi pieminēja galerijā',
	17 => 'pārspēts spēles rekords'
];

if (isset($_GET['var1']) && $_GET['var1'] == 'rss') {

	if (isset($_GET['var2'])) {
		$userid = (int) $_GET['var2'];
		$user = get_user($userid);

		if ($user) {

			$user_id = intval($user->id);
			$out = '';
			if (!empty($user_id)) {



				if ($notifications = $db->get_results("SELECT * FROM `notify` WHERE `user_id` = '$user_id' ORDER BY `bump` DESC LIMIT 0,20")) {

					header('Content-type: text/xml; charset=utf-8');
					echo '<?xml version="1.0" encoding="UTF-8"?', ">\n";
					echo '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">', "\n";
					echo '	<channel>', "\n";

					echo '		<title>' . h($user->nick . ' notifikācijas') . '</title>', "\n";
					echo '		<link>https://exs.lv/user/' . $user->id . '</link>', "\n";
					echo '		<description>Jaunākais ' . h($user->nick) . ' profilā</description>', "\n";
					echo '		<language>lv</language>', "\n";

					$hide_group_map = [];
					if (!$auth->ok || $auth->id != $user_id) {
						$mb_fkeys = [];
						foreach ($notifications as $n_item) {
							if ($n_item->type == 8 && !empty($n_item->foreign_key)) {
								$mb_fkeys[] = (int) $n_item->foreign_key;
							}
						}
						if (!empty($mb_fkeys)) {
							$fk_in = implode(',', array_unique($mb_fkeys));
							$hide_rows = $db->get_results("SELECT m.id, c.hide_intro, c.title FROM `miniblog` m JOIN `clans` c ON c.id = m.groupid WHERE m.id IN ($fk_in)");
							if (!empty($hide_rows)) {
								foreach ($hide_rows as $hr) {
									$hide_group_map[(int) $hr->id] = $hr;
								}
							}
						}
					}

					foreach ($notifications as $notify) {

						$dom = '';
						$domain_host = $config_domains[$notify->lang]['domain'] ?? 'exs.lv';
						$domain = 'https://' . $domain_host;
						if ($notify->lang != $lang) {
							$dom = ' (' . $domain_host . ')';
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
						if ($notify->type == 8) {
							//hide text for unauthorized users if the group has hide_intro = 1
							if ((!$auth->ok || $auth->id != $user_id) && !empty($hide_group_map[(int) $notify->foreign_key]->hide_intro)) {
								$notify->info = $hide_group_map[(int) $notify->foreign_key]->title;
							}
						}

						$type_text = $texts[$notify->type] ?? '';
						echo '		<item>', "\n";
						echo '			<title>', h(mb_ucfirst($type_text)), '</title>', "\n";
						echo '			<link>' . h($domain . $notify->url) . '</link>' . "\n";
						echo '			<guid>' . h($domain . $notify->url . '?' . strtotime($notify->bump)) . '</guid>' . "\n";
						echo '			<description>', h(mb_ucfirst($type_text) . $dom), '</description>', "\n";
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

				$data = [];

				if ($notifications = $db->get_results("SELECT * FROM `notify` WHERE `user_id` = '$user_id' ORDER BY `bump` DESC LIMIT 0,20")) {

					$hide_group_map = [];
					if (!$auth->ok || $auth->id != $user_id) {
						$mb_fkeys = [];
						foreach ($notifications as $n_item) {
							if ($n_item->type == 8 && !empty($n_item->foreign_key)) {
								$mb_fkeys[] = (int) $n_item->foreign_key;
							}
						}
						if (!empty($mb_fkeys)) {
							$fk_in = implode(',', array_unique($mb_fkeys));
							$hide_rows = $db->get_results("SELECT m.id, c.hide_intro, c.title FROM `miniblog` m JOIN `clans` c ON c.id = m.groupid WHERE m.id IN ($fk_in)");
							if (!empty($hide_rows)) {
								foreach ($hide_rows as $hr) {
									$hide_group_map[(int) $hr->id] = $hr;
								}
							}
						}
					}

					foreach ($notifications as $notify) {

						$domain = 'https://' . ($config_domains[$notify->lang]['domain'] ?? 'exs.lv');

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
						if ($notify->type == 8) {
							//hide text for unauthorized users if the group has hide_intro = 1
							if ((!$auth->ok || $auth->id != $user_id) && !empty($hide_group_map[(int) $notify->foreign_key]->hide_intro)) {
								$notify->info = $hide_group_map[(int) $notify->foreign_key]->title;
							}
						}

						$data[] = [
							'url' => $domain . $notify->url,
							'title' => h(mb_ucfirst($texts[$notify->type] ?? '')),
							'date' => $notify->bump,
							'info' => $notify->info,
							'type' => $notify->type
						];
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

