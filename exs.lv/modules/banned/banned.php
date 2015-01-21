<?php
/**
 *  Bloķēto profilu sadaļa
 *
 *  Neautorizēts/bloķēts lietotājs redzēs lieguma paziņojumu.
 *  Modi/admini redzēs sarakstu ar visiem bloķētajiem profiliem.
 *
 *  Ja admins nav "globāls", t.i. norādīts sub-exa konfigurācijā, 
 *  bans attiecas tikai uz to lapu.
 */
$robotstag[] = 'noindex';
 
$q_add = '';
if (in_array($auth->id, $site_access[1]) || in_array($auth->id, $site_access[2])) {
	$q_add = " AND `banned`.`lang` = '$lang'";
}

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

	if (isset($_GET['delete']) && check_token('remban', $_GET['token'])) {
		$delete = (int) $_GET['delete'];
		$unbanned = $db->get_var("SELECT user_id FROM banned WHERE id = '$delete' " . $q_add . " LIMIT 1");
		$db->query("DELETE FROM banned WHERE id = '$delete' " . $q_add . " LIMIT 1");
		$auth->log('Atbloķēja lietotāju', 'users', $unbanned);
		get_banlist(true);
		set_flash('Bans noņemts!', 'success');
		redirect('/' . $category->textid);
	}

	if (isset($_GET['delete_ip']) && check_token('remban', $_GET['token'])) {
		$delete = (int) $_GET['delete_ip'];
		$unbanned = $db->get_var("SELECT user_id FROM banned WHERE id = '$delete' " . $q_add . " LIMIT 1");
		$db->query("UPDATE `banned` SET `ip` = '--' WHERE `id` = '$delete' " . $q_add . " LIMIT 1");
		$auth->log('Atbloķēja IP adresi', 'users', $unbanned);
		get_banlist(true);
		set_flash('IP bans noņemts!', 'success');
		redirect('/' . $category->textid);
	}
	
	// tabula ar visiem liegumiem    
	$getban = $db->get_results("
		SELECT * FROM `banned` WHERE `time` + `length` > '" . time() . "' " . $q_add . " 
		ORDER BY `time` DESC
	");
	
	$tpl->newBlock('table-of-banned');

	if ($getban) {

		foreach ($getban as $banned) {
		
			$tpl->newBlock('banned-row');

			// dzēstiem lietotājiem lietotājvārds var nebūt
			$user = get_user($banned->user_id);
			if (!empty($user->nick)) {
				$linkuser = '<a href="/user/' . $user->id . '">' . h($user->nick) . '</a>';
			} else {
				$linkuser = '--';
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
				'token' => make_token('remban'),
				'anick' => h($author->nick)
			));

			if ($banned->lang == 0) {
				$tpl->assign('where', 'Globāls');
			} else {
				$tpl->assign('where', $config_domains[$banned->lang]['domain']);
			}

			// @mad manuāli bloķētie nebūs dzēšami
			if ($banned->reason != 'perm (ban evading)') {
				$tpl->newBlock('remove-ban');
				$tpl->assign(array(
					'banned-id' => $banned->id,
					'token' => make_token('remban')
				));
			}
		}            
	}

} else {
	redirect();
}

