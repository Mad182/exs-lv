<?php

$q_add = '';
/* ja admins nav "globāls", tb norādīts sub-exa konfigurācijā, bans attiecas tikai uz to lapu */
if(in_array($auth->id, $site_mods) || in_array($auth->id, $site_admins)) {
	$q_add = " AND `lang` = '$lang'";
}

$getban = $db->get_results("SELECT * FROM banned WHERE time+length > '" . time() . "' " . $q_add . " ORDER BY time DESC");

$tpl->assignGlobal(array(
	'category-id' => $category->id
));

if (!$auth->ok) {
	$tpl->newBlock('banned-public');
	if (isset($_GET['bid'])) {
		$bid = (int) $_GET['bid'];
		if ($baninfo = $db->get_row("SELECT * FROM banned WHERE id = '$bid' LIMIT 1")) {
			$tpl->assignGlobal('bmsg', '<h2>Pieeja liegta!</h2><div class="form"><p class="error">Šim lietotāja profilam un/vai datora IP adresei ir liegta pieeja mājas lapas exs.lv pilnvērtīgai izmantošanai. Ja uzskati, ka bans uzlikts kļūdas dēļ, vai ir kaut kas cits sakāms, raksti un info@exs.lv</p><p class="error"><strong>IP:</strong> ' . $baninfo->ip . '<br /><strong>Lietotājs:</strong> #' . $baninfo->user_id . '<br /><strong>Iemesls:</strong> ' . $baninfo->reason . '<br /><strong>No:</strong> ' . $db->get_var("SELECT nick FROM users WHERE id = '$baninfo->author'") . '<br /><strong>Datums:</strong> ' . date('Y-m-d H:i', $baninfo->time) . '<br /><strong>Atlikušais laiks:</strong> ' . strTime($baninfo->time + $baninfo->length - time()) . '</p><p style="text-align: center;"><img src="/bildes/banhammer.jpg" alt="" /><br /><br /></p></div>');
		} else {
			set_flash("Bloķēšanas iemesls, kuru vēlējies apskatīt, vairs nav aktīvs!");
			redirect();
		}
	}
} elseif (im_mod()) {

	if (isset($_GET['delete'])) {
		$delete = (int) $_GET['delete'];
		$unbanned = $db->get_var("SELECT user_id FROM banned WHERE id = '$delete' " . $q_add . " LIMIT 1");
		$db->query("DELETE FROM banned WHERE id = '$delete' LIMIT 1");
		$auth->log('Atbloķēja lietotāju', 'users', $unbanned);
		get_banlist(true);
		set_flash('Bans noņemts!', 'success');
		redirect('/' . $category->textid);
	}

	if (isset($_GET['delete_ip'])) {
		$delete = (int) $_GET['delete_ip'];
		$unbanned = $db->get_var("SELECT user_id FROM banned WHERE id = '$delete' " . $q_add . " LIMIT 1");
		$db->query("UPDATE `banned` SET `ip` = '--' WHERE `id` = '$delete' LIMIT 1");
		$auth->log('Atbloķēja IP adresi', 'users', $unbanned);
		get_banlist(true);
		set_flash('IP bans noņemts!', 'success');
		redirect('/' . $category->textid);
	}

	$tpl->newBlock('banned-admin');
	if ($getban) {
		foreach ($getban as $banned) {

			$tpl->newBlock('banned-admin-node');
			$user = get_user($banned->user_id);
			if (!empty($user->nick)) {
				$linkuser = '<a href="/user/' . $user->id . '">' . htmlspecialchars($user->nick) . '</a>';
			} else {
				$linkuser = '---';
			}
			$author = get_user($banned->author);
			$tpl->assign(array(
				'banned-id' => $banned->id,
				'banned-user_id' => $banned->user_id,
				'nick' => $linkuser,
				'banned-ip' => $banned->ip,
				'banned-reason' => wordwrap($banned->reason, 32, "\n", 1),
				'banned-date' => date('Y-m-d H:i', $banned->time),
				'banned-until' => date('Y-m-d H:i', $banned->time + $banned->length),
				'banned-author' => $banned->author,
				'anick' => htmlspecialchars($author->nick)
			));

			if ($banned->reason != 'perm (ban evading)') {
				$tpl->newBlock('rmban');
				$tpl->assign(array(
					'banned-id' => $banned->id,
				));
			}
		}
	}
} else {
	redirect();
}

