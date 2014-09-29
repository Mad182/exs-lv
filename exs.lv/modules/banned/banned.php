<?php
/**
 *  Bloķēto profilu sadaļa
 *
 *  Neautorizēts/bloķēts lietotājs redzēs lieguma paziņojumu.
 *  Modi/admini redzēs sarakstu ar visiem bloķētajie profiliem.
 *
 *  Bloķētie profili redzami divās tabulās:
 *      - tie, kuri atrodas kādā no profilu grupām;
 *      - tie, kuri nevienā grupā nav.
 *
 *  Ja admins nav "globāls", t.i. norādīts sub-exa konfigurācijā, 
 *  bans attiecas tikai uz to lapu.
 */
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

	if (isset($_GET['delete'])) {
		$delete = (int) $_GET['delete'];
		$unbanned = $db->get_var("SELECT user_id FROM banned WHERE id = '$delete' " . $q_add . " LIMIT 1");
		$db->query("DELETE FROM banned WHERE id = '$delete' " . $q_add . " LIMIT 1");
		$auth->log('Atbloķēja lietotāju', 'users', $unbanned);
		get_banlist(true);
		set_flash('Bans noņemts!', 'success');
		redirect('/' . $category->textid);
	}

	if (isset($_GET['delete_ip'])) {
		$delete = (int) $_GET['delete_ip'];
		$unbanned = $db->get_var("SELECT user_id FROM banned WHERE id = '$delete' " . $q_add . " LIMIT 1");
		$db->query("UPDATE `banned` SET `ip` = '--' WHERE `id` = '$delete' " . $q_add . " LIMIT 1");
		$auth->log('Atbloķēja IP adresi', 'users', $unbanned);
		get_banlist(true);
		set_flash('IP bans noņemts!', 'success');
		redirect('/' . $category->textid);
	}
	
	// pagaidām sadalījumu pa profilu grupām citos projektos nerādīsim
	if ($lang == 1) {

		// bloķētie profili, kas atrodas kādā no profilu grupām; 
		// grupas ietvaros pirmais būs tas profils, kas bloķēts pēdējais,
		// tādējādi, nenospiežot toggle, redzami katras grupas pēdējie liegumi
		$group_profiles = $db->get_results("
			SELECT
				`banned`.*,
				`users`.`nick`,
				`users`.`level`,
				`author`.`nick` AS `author_nick`,
				`author`.`level` AS `author_level`,
				CASE 
					WHEN `users_groups`.`parent_id` = 0 
						THEN `users_groups`.`id`
					ELSE `users_groups`.`parent_id`
				END AS `group_id`
			FROM `users_groups`
				JOIN `banned` ON (
					`banned`.`user_id` = `users_groups`.`user_id` AND
					`banned`.`time` + `banned`.`length` > '" . time() . "'
				)
				JOIN `users` ON (
					`users_groups`.`user_id` = `users`.`id` AND
					`users`.`deleted` = 0
				)
				JOIN `users` AS `author` ON `banned`.`author` = `author`.`id`
			WHERE 
				`users_groups`.`deleted_by` = 0
				" . $q_add . " 
			ORDER BY 
				`group_id` ASC, 
				`banned`.`id` DESC
		");      

		if ($group_profiles) {
		
			$tpl->newBlock('banned-by-group');

			$tmp_main_id = 0;
			
			foreach ($group_profiles as $banned) {
			
				$tpl->newBlock('by-group-outer');
			
				// profilu var parādīt divos veidos: kā grupas main vai child

				// šeit grupa mainās
				if ($tmp_main_id != $banned->group_id) {

					$tpl->newBlock('by-group');
					$tpl->assign('group-id', $banned->group_id);

					$tmp_main_id = $banned->group_id;
					$tmp_rm_block = 'main';
					
				// šeit grupa saglabājas, bet mainās child
				} else {
					$tpl->newBlock('by-group-child');
					$tpl->assign('group-id', $tmp_main_id);

					$tmp_rm_block = 'child';
				}

				$banned->nick = '<a href="/user/' . $banned->user_id . '">' . htmlspecialchars($banned->nick) . '</a>';

				$tpl->assign(array(
					'banned-id' => $banned->id,
					'banned-user_id' => $banned->user_id,
					'nick' => $banned->nick,
					'banned-ip' => $banned->ip,
					'banned-reason' => wordwrap($banned->reason, 32, "\n", 1),
					'banned-date' => date('Y-m-d H:i', $banned->time),
					'banned-until' => date('Y-m-d H:i', $banned->time + $banned->length),
					'banned-author' => $banned->author_nick,
					'anick' => htmlspecialchars($banned->author_nick)
				));

				if ($banned->lang == 0) {
					$tpl->assign('where', 'Globāls');
				} else {
					$tpl->assign('where', $config_domains[$banned->lang]['domain']);
				}

				if ($banned->reason != 'perm (ban evading)') {
					$tpl->newBlock('rmban-'.$tmp_rm_block);
					$tpl->assign('banned-id', $banned->id);
				}
				
				// liegums no IP jau var būt noņemts, 
				// un tad dzēšanas poga nav jārāda
				if ($banned->ip != '--' && !empty($banned->ip)) {
					if ($tmp_rm_block == 'main') {
						$tpl->newBlock('remove-ip-main');
					} else {
						$tpl->newBlock('remove-ip-child');                        
					}
					$tpl->assign(array(
						'banned-id' => $banned->id,
						'banned-ip' => $banned->ip
					));
				}
			}
		}
		
		// bloķētie profili, kas neatrodas nevienā profilu grupā
		$others = $db->get_results("
			SELECT 
				`banned`.*,
				`users`.`nick`,
				`users`.`level`,
				`author`.`nick` AS `author_nick`,
				`author`.`level` AS `author_level`
			FROM `banned` 
				JOIN `users` ON (
					`banned`.`user_id` = `users`.`id` AND
					`users`.`deleted` = 0
				)
				JOIN `users` AS `author` ON `banned`.`author` = `author`.`id`
				LEFT JOIN `users_groups` ON (
					`banned`.`user_id` = `users_groups`.`user_id` AND
					`users_groups`.`deleted_by` = 0
				)
			WHERE 
				`users_groups`.`user_id` IS NULL AND
				`banned`.`time` + `banned`.`length` > '" . time() . "'
				" . $q_add . " 
			ORDER BY
				`banned`.`time` DESC
		");

		if ($others) {
		
			$tpl->newBlock('banned-by-single');
			
			foreach ($others as $banned) {
			
				$tpl->newBlock('by-single');
				
				$banned->nick = '<a href="/user/' . $banned->user_id . '">' . htmlspecialchars($banned->nick) . '</a>';

				$tpl->assign(array(
					'banned-id' => $banned->id,
					'banned-user_id' => $banned->user_id,
					'nick' => $banned->nick,
					'banned-reason' => wordwrap($banned->reason, 32, "\n", 1),
					'banned-date' => date('Y-m-d H:i', $banned->time),
					'banned-until' => date('Y-m-d H:i', $banned->time + $banned->length),
					'banned-author' => $banned->author_nick,
					'anick' => htmlspecialchars($banned->author_nick)
				));

				if ($banned->lang == 0) {
					$tpl->assign('where', 'Globāls');
				} else {
					$tpl->assign('where', $config_domains[$banned->lang]['domain']);
				}

				// @mad manuāli bloķētie nebūs dzēšami
				if ($banned->reason != 'perm (ban evading)') {
					$tpl->newBlock('rmban-single');
					$tpl->assign('banned-id', $banned->id);
				}
				
				// liegums no IP jau var būt noņemts, 
				// un tad dzēšanas poga nav jārāda
				if ($banned->ip != '--') {
					$tpl->newBlock('remove-ip');
					$tpl->assign(array(
						'banned-id' => $banned->id,
						'banned-ip' => $banned->ip
					));
				}
			}
		}
	
	// apakšprojektos pēc noklusējuma rādīs vienu tabulu vecajā stilā
	} else {
	
		$getban = $db->get_results("
			SELECT * FROM `banned` WHERE `time` + `length` > '" . time() . "' " . $q_add . " 
			ORDER BY `time` DESC
		");

		$tpl->newBlock('banned-by-global');

		if ($getban) {

			foreach ($getban as $banned) {
			
				$tpl->newBlock('by-global');

				// dzēstiem lietotājiem lietotājvārds var nebūt
				$user = get_user($banned->user_id);
				if (!empty($user->nick)) {
					$linkuser = '<a href="/user/' . $user->id . '">' . htmlspecialchars($user->nick) . '</a>';
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
					'anick' => htmlspecialchars($author->nick)
				));

				if ($banned->lang == 0) {
					$tpl->assign('where', 'Globāls');
				} else {
					$tpl->assign('where', $config_domains[$banned->lang]['domain']);
				}

				// @mad manuāli bloķētie nebūs dzēšami
				if ($banned->reason != 'perm (ban evading)') {
					$tpl->newBlock('rmban-3');
					$tpl->assign('banned-id', $banned->id);
				}
			}            
		}        
	}

} else {
	redirect();
}
